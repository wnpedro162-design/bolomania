<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Pedido da loja virtual (tabelas pedido e pedido_item).
 * Não usa venda/venda_item, que pertencem ao balcão (PDV) do vendedor.
 * O ESTOQUE é o mesmo para os dois canais.
 */
class Pedido
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Cria o pedido, dá baixa no estoque e registra a saída em movimento_estoque,
     * tudo em UMA transação (ou grava tudo, ou nada).
     *
     * @param array $itens itens do carrinho (variacao_id, quantidade)
     * @return int id do pedido criado
     * @throws RegraNegocioException quando falta estoque ou o bolo saiu de linha
     */
    public function criar(string $cpf, string $enderecoEntrega, string $formaPagamento, array $itens): int
    {
        if (!$itens) {
            throw new RegraNegocioException('Seu carrinho está vazio.');
        }
        if (!in_array($formaPagamento, ['pix', 'cartao'], true)) {
            throw new RegraNegocioException('Forma de pagamento inválida.');
        }

        // ordem fixa evita deadlock entre dois pedidos ao mesmo tempo
        usort($itens, fn($a, $b) => $a['variacao_id'] <=> $b['variacao_id']);

        $this->db->beginTransaction();
        try {
            $conferidos = [];
            $total = 0.0;

            foreach ($itens as $item) {
                $variacaoId = (int) $item['variacao_id'];
                $qtd = (int) $item['quantidade'];

                // trava a linha do estoque até o fim da transação
                $stmt = $this->db->prepare("SELECT quantidade FROM estoque WHERE variacao_id = :id FOR UPDATE");
                $stmt->execute([':id' => $variacaoId]);
                $estoque = $stmt->fetchColumn();

                // preço SEMPRE lido do banco (o da sessão pode estar desatualizado)
                $stmt = $this->db->prepare(
                    "SELECT v.preco, p.nome
                     FROM variacao v
                     JOIN produto p ON p.id = v.produto_id
                     JOIN categoria c ON c.id = p.categoria_id
                     WHERE v.id = :id AND p.ativo = 1 AND c.ativo = 1"
                );
                $stmt->execute([':id' => $variacaoId]);
                $variacao = $stmt->fetch();

                if (!$variacao) {
                    throw new RegraNegocioException('Um bolo do carrinho não está mais disponível.');
                }
                if ($qtd < 1 || $estoque === false || (int) $estoque < $qtd) {
                    throw new RegraNegocioException("Estoque insuficiente para “{$variacao['nome']}”.");
                }

                $preco = (float) $variacao['preco'];
                $total += $preco * $qtd;
                $conferidos[] = ['variacao_id' => $variacaoId, 'quantidade' => $qtd, 'preco' => $preco];
            }

            $stmt = $this->db->prepare(
                "INSERT INTO pedido (cliente_cpf, endereco_entrega, forma_pagamento, status, valor_total)
                 VALUES (:cpf, :endereco, :forma, :status, :total)"
            );
            $stmt->execute([
                ':cpf'      => $cpf,
                ':endereco' => $enderecoEntrega,
                ':forma'    => $formaPagamento,
                ':status'   => 'pago', // pagamento simulado: aprovado na hora
                ':total'    => round($total, 2),
            ]);
            $pedidoId = (int) $this->db->lastInsertId();

            $insItem = $this->db->prepare(
                "INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario)
                 VALUES (:pedido, :variacao, :qtd, :preco)"
            );
            $baixa = $this->db->prepare(
                "UPDATE estoque SET quantidade = quantidade - :qtd WHERE variacao_id = :variacao"
            );
            $movimento = $this->db->prepare(
                "INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id)
                 VALUES (:variacao, 'saida', :qtd, 'pedido', :pedido)"
            );

            foreach ($conferidos as $c) {
                $insItem->execute([
                    ':pedido' => $pedidoId, ':variacao' => $c['variacao_id'],
                    ':qtd' => $c['quantidade'], ':preco' => $c['preco'],
                ]);
                $baixa->execute([':qtd' => $c['quantidade'], ':variacao' => $c['variacao_id']]);
                $movimento->execute([
                    ':variacao' => $c['variacao_id'], ':qtd' => $c['quantidade'], ':pedido' => $pedidoId,
                ]);
            }

            $this->db->commit();
            return $pedidoId;
        } catch (Throwable $ex) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $ex;
        }
    }

    /** Pedido do cliente (confere o dono) com os itens; null se não for dele. */
    public function buscarDoCliente(int $pedidoId, string $cpf): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pedido WHERE id = :id AND cliente_cpf = :cpf");
        $stmt->execute([':id' => $pedidoId, ':cpf' => $cpf]);
        $pedido = $stmt->fetch();
        if (!$pedido) {
            return null;
        }
        $pedido['itens'] = $this->itensDosPedidos([$pedidoId])[$pedidoId] ?? [];
        return $pedido;
    }

    /** Histórico do cliente, do mais novo para o mais antigo, já com os itens. */
    public function listarDoCliente(string $cpf): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pedido WHERE cliente_cpf = :cpf ORDER BY data DESC, id DESC");
        $stmt->execute([':cpf' => $cpf]);
        $pedidos = $stmt->fetchAll();

        $itens = $this->itensDosPedidos(array_column($pedidos, 'id'));
        foreach ($pedidos as &$p) {
            $p['itens'] = $itens[$p['id']] ?? [];
        }
        return $pedidos;
    }

    private function itensDosPedidos(array $ids): array
    {
        if (!$ids) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "SELECT pi.pedido_id, pi.quantidade, pi.preco_unitario,
                    v.sku, v.tamanho, v.cor, p.id AS produto_id, p.nome AS produto_nome
             FROM pedido_item pi
             JOIN variacao v ON v.id = pi.variacao_id
             JOIN produto p ON p.id = v.produto_id
             WHERE pi.pedido_id IN ($marcadores)
             ORDER BY pi.id"
        );
        $stmt->execute(array_map('intval', array_values($ids)));

        $agrupado = [];
        foreach ($stmt->fetchAll() as $linha) {
            $agrupado[$linha['pedido_id']][] = $linha;
        }
        return $agrupado;
    }
}
