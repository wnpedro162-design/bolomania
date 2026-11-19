<?php
require_once __DIR__ . '/../config/db.php';

class Venda
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function centavos(string $valor): int
    {
        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $valor)) {
            throw new RuntimeException('Preço inválido no cadastro do bolo.');
        }
        $partes = explode('.', $valor);
        return (int)$partes[0] * 100 + (int)str_pad($partes[1] ?? '', 2, '0');
    }

    public function catalogo(): array
    {
        return $this->db->query("SELECT v.id, p.nome, v.tamanho, v.cor, v.sku,
            v.preco, COALESCE(e.quantidade, 0) AS estoque
            FROM variacao v JOIN produto p ON p.id = v.produto_id
            JOIN categoria c ON c.id = p.categoria_id
            LEFT JOIN estoque e ON e.variacao_id = v.id
            WHERE p.ativo = 1 AND c.ativo = 1
            ORDER BY p.nome, v.tamanho, v.id")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clientes(): array
    {
        return $this->db->query('SELECT id, nome FROM cliente ORDER BY nome')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function historico(): array
    {
        return $this->db->query("SELECT v.id, v.data, v.status, v.valor_total,
            u.nome AS vendedor, c.nome AS cliente, nf.numero AS nf_numero
            FROM venda v JOIN usuario u ON u.id = v.usuario_id
            LEFT JOIN cliente c ON c.id = v.cliente_id
            LEFT JOIN nota_fiscal_venda nf ON nf.venda_id = v.id
            ORDER BY v.id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detalhes(int $id): array
    {
        $q = $this->db->prepare('SELECT p.nome, r.tamanho, r.sku,
            i.quantidade, i.preco_unitario FROM venda_item i
            JOIN variacao r ON r.id = i.variacao_id
            JOIN produto p ON p.id = r.produto_id
            WHERE i.venda_id = ? ORDER BY i.id');
        $q->execute([$id]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Venda completa (cliente, vendedor e itens) — usada na nota fiscal. */
    public function buscarPorId(int $id): ?array
    {
        $q = $this->db->prepare('SELECT v.*, c.nome AS cliente_nome, c.cpf_cnpj AS cliente_doc,
            u.nome AS vendedor_nome
            FROM venda v
            JOIN usuario u ON u.id = v.usuario_id
            LEFT JOIN cliente c ON c.id = v.cliente_id
            WHERE v.id = ?');
        $q->execute([$id]);
        $venda = $q->fetch(PDO::FETCH_ASSOC);
        if (!$venda) {
            return null;
        }
        $q = $this->db->prepare('SELECT i.quantidade, i.preco_unitario, r.sku, r.tamanho, r.cor,
            p.nome AS produto_nome
            FROM venda_item i
            JOIN variacao r ON r.id = i.variacao_id
            JOIN produto p ON p.id = r.produto_id
            WHERE i.venda_id = ? ORDER BY i.id');
        $q->execute([$id]);
        $venda['itens'] = $q->fetchAll(PDO::FETCH_ASSOC);
        return $venda;
    }

    public function finalizar(int $usuarioId, ?int $clienteId, array $carrinho): int
    {
        if (!$carrinho) {
            throw new RuntimeException('Adicione pelo menos um bolo à venda.');
        }
        ksort($carrinho, SORT_NUMERIC);
        $this->db->beginTransaction();
        try {
            $q = $this->db->prepare('SELECT id FROM usuario WHERE id = ? AND ativo = 1');
            $q->execute([$usuarioId]);
            if (!$q->fetchColumn()) {
                throw new RuntimeException('Usuário inexistente ou inativo. Entre novamente.');
            }
            if ($clienteId !== null) {
                $q = $this->db->prepare('SELECT id FROM cliente WHERE id = ?');
                $q->execute([$clienteId]);
                if (!$q->fetchColumn()) {
                    throw new RuntimeException('Cliente inválido.');
                }
            }
            $itens = []; $total = 0;
            foreach ($carrinho as $id => $quantidade) {
                if (!is_int($quantidade) || $quantidade < 1 || $quantidade > 10000) {
                    throw new RuntimeException('Quantidade inválida.');
                }
                $q = $this->db->prepare("SELECT r.id, r.preco, p.nome
                    FROM variacao r JOIN produto p ON p.id = r.produto_id
                    JOIN categoria c ON c.id = p.categoria_id
                    WHERE r.id = ? AND p.ativo = 1 AND c.ativo = 1
                    FOR UPDATE");
                $q->execute([(int)$id]);
                $bolo = $q->fetch(PDO::FETCH_ASSOC);
                if (!$bolo) {
                    throw new RuntimeException('Um bolo do carrinho não está mais disponível.');
                }
                $q = $this->db->prepare('SELECT quantidade FROM estoque WHERE variacao_id = ? FOR UPDATE');
                $q->execute([(int)$id]);
                $saldo = $q->fetchColumn();
                if ($saldo === false || (int)$saldo < $quantidade) {
                    throw new RuntimeException('Estoque insuficiente para ' . $bolo['nome'] . '.');
                }
                $total += self::centavos((string)$bolo['preco']) * $quantidade;
                if ($total > 9999999999) {
                    throw new RuntimeException('O total ultrapassa o limite do banco de dados.');
                }
                $itens[] = [(int)$id, $quantidade, $bolo['preco']];
            }
            $q = $this->db->prepare("INSERT INTO venda
                (usuario_id, cliente_id, data, status, valor_total)
                VALUES (?, ?, CURDATE(), 'finalizada', ?)");
            $q->execute([$usuarioId, $clienteId, number_format($total / 100, 2, '.', '')]);
            $vendaId = (int)$this->db->lastInsertId();
            foreach ($itens as [$id, $quantidade, $preco]) {
                $q = $this->db->prepare('INSERT INTO venda_item
                    (venda_id, variacao_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)');
                $q->execute([$vendaId, $id, $quantidade, $preco]);
                $q = $this->db->prepare('UPDATE estoque SET quantidade = quantidade - ?
                    WHERE variacao_id = ? AND quantidade >= ?');
                $q->execute([$quantidade, $id, $quantidade]);
                if ($q->rowCount() !== 1) {
                    throw new RuntimeException('Estoque alterado. Atualize a tela e tente novamente.');
                }
                $q = $this->db->prepare("INSERT INTO movimento_estoque
                    (variacao_id, tipo, quantidade, origem, origem_id)
                    VALUES (?, 'saida', ?, 'venda', ?)");
                $q->execute([$id, $quantidade, $vendaId]);
            }
            $this->db->commit();
            return $vendaId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) { $this->db->rollBack(); }
            throw $e;
        }
    }
}
