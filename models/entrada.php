<?php

require_once __DIR__ . '/../config/db.php';

class Entrada
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listar(): array
    {
        $sql = "
            SELECT
                e.id,
                e.data,
                e.status,
                e.valor_total,
                f.nome AS fornecedor_nome,
                nf.numero AS nf_numero
            FROM entrada_mercadoria e
            INNER JOIN fornecedor f
                ON f.id = e.fornecedor_id
            LEFT JOIN nota_fiscal_entrada nf
                ON nf.entrada_id = e.id
            ORDER BY e.id DESC
        ";

        return $this->conn->query($sql)->fetchAll();
    }

    public function listarFornecedores(): array
    {
        return $this->conn->query("
            SELECT id, nome
            FROM fornecedor
            WHERE ativo = 1
            ORDER BY nome ASC
        ")->fetchAll();
    }

    public function listarVariacoes(): array
    {
        return $this->conn->query("
            SELECT
                v.id,
                v.sku,
                v.tamanho,
                v.cor,
                v.preco,
                p.nome AS produto_nome
            FROM variacao v
            INNER JOIN produto p
                ON p.id = v.produto_id
            WHERE p.ativo = 1
            ORDER BY p.nome ASC, v.tamanho ASC
        ")->fetchAll();
    }

    /** Entrada com fornecedor e itens (usada na nota fiscal de entrada). */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT e.*, f.nome AS fornecedor_nome, f.cnpj AS fornecedor_cnpj,
                   f.endereco AS fornecedor_endereco
            FROM entrada_mercadoria e
            INNER JOIN fornecedor f ON f.id = e.fornecedor_id
            WHERE e.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $entrada = $stmt->fetch();
        if (!$entrada) {
            return null;
        }

        $stmt = $this->conn->prepare("
            SELECT i.quantidade, i.custo_unitario, v.sku, v.tamanho, v.cor,
                   p.nome AS produto_nome
            FROM entrada_item i
            INNER JOIN variacao v ON v.id = i.variacao_id
            INNER JOIN produto p ON p.id = v.produto_id
            WHERE i.entrada_id = :id
            ORDER BY i.id
        ");
        $stmt->execute([':id' => $id]);
        $entrada['itens'] = $stmt->fetchAll();

        return $entrada;
    }

    public function registrar(
        int $fornecedorId,
        array $itens
    ): int {

        $this->conn->beginTransaction();

        try {
            $valorTotal = 0;

            foreach ($itens as $item) {
                $valorTotal +=
                    $item['quantidade'] * $item['custo_unitario'];
            }

            // Registrar entrada principal
            $stmt = $this->conn->prepare("
                INSERT INTO entrada_mercadoria
                (fornecedor_id, data, valor_total)
                VALUES (:fornecedor_id, NOW(), :valor_total)
            ");

            $stmt->execute([
                ':fornecedor_id' => $fornecedorId,
                ':valor_total' => $valorTotal
            ]);

            $entradaId = (int) $this->conn->lastInsertId();

            foreach ($itens as $item) {

                $variacaoId = (int) $item['variacao_id'];
                $quantidade = (int) $item['quantidade'];
                $custo = (float) $item['custo_unitario'];

                // Registrar item da entrada
                $stmt = $this->conn->prepare("
                    INSERT INTO entrada_item
                    (entrada_id, variacao_id, quantidade, custo_unitario)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $entradaId,
                    $variacaoId,
                    $quantidade,
                    $custo
                ]);

                // Verificar estoque existente
                $stmt = $this->conn->prepare("
                    SELECT id
                    FROM estoque
                    WHERE variacao_id = ?
                    FOR UPDATE
                ");

                $stmt->execute([$variacaoId]);

                $estoque = $stmt->fetch();

                if ($estoque) {
                    $stmt = $this->conn->prepare("
                        UPDATE estoque
                        SET quantidade = quantidade + ?
                        WHERE variacao_id = ?
                    ");

                    $stmt->execute([
                        $quantidade,
                        $variacaoId
                    ]);
                } else {
                    $stmt = $this->conn->prepare("
                        INSERT INTO estoque
                        (variacao_id, quantidade, minimo)
                        VALUES (?, ?, 2)
                    ");

                    $stmt->execute([
                        $variacaoId,
                        $quantidade
                    ]);
                }

                // Registrar movimentação
                $stmt = $this->conn->prepare("
                    INSERT INTO movimento_estoque
                    (variacao_id, tipo, quantidade, origem, origem_id, data)
                    VALUES (?, 'entrada', ?, 'entrada', ?, NOW())
                ");

                $stmt->execute([
                    $variacaoId,
                    $quantidade,
                    $entradaId
                ]);
            }

            $this->conn->commit();

            return $entradaId;

        } catch (Throwable $e) {

            $this->conn->rollBack();

            throw $e;
        }
    }
}
