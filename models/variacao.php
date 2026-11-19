<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/estoque.php';

/**
 * Variação (SKU) de um bolo: tamanho + sabor/recheio + preço.
 * No banco o "sabor" fica na coluna `cor`.
 */
class Variacao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /** Variações do produto já com quantidade e mínimo do estoque. */
    public function listarPorProduto(int $produtoId): array
    {
        $stmt = $this->conn->prepare("
            SELECT v.*, COALESCE(e.quantidade, 0) AS estoque, COALESCE(e.minimo, 0) AS minimo
            FROM variacao v
            LEFT JOIN estoque e ON e.variacao_id = v.id
            WHERE v.produto_id = :produto_id
            ORDER BY v.preco, v.tamanho
        ");
        $stmt->execute([':produto_id' => $produtoId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT v.*, COALESCE(e.minimo, 2) AS minimo
            FROM variacao v
            LEFT JOIN estoque e ON e.variacao_id = v.id
            WHERE v.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Cria a variação E a linha de estoque zerada, na mesma transação (manual 18.2). */
    public function inserir(int $produtoId, string $tamanho, string $sabor, string $sku, float $preco, int $minimo): int
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO variacao (produto_id, tamanho, cor, sku, preco)
                VALUES (:produto_id, :tamanho, :cor, :sku, :preco)
            ");
            $stmt->execute([
                ':produto_id' => $produtoId,
                ':tamanho'    => $tamanho,
                ':cor'        => $sabor,
                ':sku'        => $sku,
                ':preco'      => $preco,
            ]);
            $id = (int) $this->conn->lastInsertId();

            (new Estoque())->garantirRegistro($id, $minimo);

            $this->conn->commit();
            return $id;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function atualizar(int $id, string $tamanho, string $sabor, string $sku, float $preco, int $minimo): void
    {
        $stmt = $this->conn->prepare("
            UPDATE variacao
            SET tamanho = :tamanho, cor = :cor, sku = :sku, preco = :preco
            WHERE id = :id
        ");
        $stmt->execute([
            ':id'      => $id,
            ':tamanho' => $tamanho,
            ':cor'     => $sabor,
            ':sku'     => $sku,
            ':preco'   => $preco,
        ]);
        (new Estoque())->atualizarMinimo($id, $minimo);
    }

    /** Variação que já foi comprada/vendida não pode sumir (histórico e notas fiscais). */
    public function temMovimento(int $id): bool
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM movimento_estoque WHERE variacao_id = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function deletar(int $id): void
    {
        $this->conn->beginTransaction();
        try {
            $this->conn->prepare("DELETE FROM estoque WHERE variacao_id = :id")->execute([':id' => $id]);
            $this->conn->prepare("DELETE FROM variacao WHERE id = :id")->execute([':id' => $id]);
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
