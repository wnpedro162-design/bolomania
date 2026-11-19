<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Estoque (manual 18.7). Relação 1:1 com variacao (variacao_id é UNIQUE).
 * Regra: toda variação nasce com uma linha de estoque zerada.
 */
class Estoque
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /** Cria a linha de estoque da variação, se ainda não existir. */
    public function garantirRegistro(int $variacaoId, int $minimo = 2): void
    {
        $stmt = $this->conn->prepare("SELECT id FROM estoque WHERE variacao_id = :vid");
        $stmt->execute([':vid' => $variacaoId]);

        if (!$stmt->fetch()) {
            $ins = $this->conn->prepare("
                INSERT INTO estoque (variacao_id, quantidade, minimo)
                VALUES (:vid, 0, :minimo)
            ");
            $ins->execute([':vid' => $variacaoId, ':minimo' => max(0, $minimo)]);
        }
    }

    public function buscarPorVariacao(int $variacaoId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM estoque WHERE variacao_id = :vid");
        $stmt->execute([':vid' => $variacaoId]);
        return $stmt->fetch() ?: null;
    }

    public function atualizarMinimo(int $variacaoId, int $minimo): void
    {
        $this->garantirRegistro($variacaoId, $minimo);
        $stmt = $this->conn->prepare("UPDATE estoque SET minimo = :m WHERE variacao_id = :vid");
        $stmt->execute([':m' => max(0, $minimo), ':vid' => $variacaoId]);
    }

    /**
     * Ajusta a quantidade (positivo = entrada, negativo = saída).
     * Nunca deixa o estoque ficar negativo.
     */
    public function ajustar(int $variacaoId, int $delta): void
    {
        $this->garantirRegistro($variacaoId);

        $stmt = $this->conn->prepare("SELECT quantidade FROM estoque WHERE variacao_id = :vid FOR UPDATE");
        $stmt->execute([':vid' => $variacaoId]);
        $nova = (int) $stmt->fetchColumn() + $delta;

        if ($nova < 0) {
            throw new RuntimeException("Estoque insuficiente para esta operação.");
        }

        $up = $this->conn->prepare("UPDATE estoque SET quantidade = :q WHERE variacao_id = :vid");
        $up->execute([':q' => $nova, ':vid' => $variacaoId]);
    }

    public function contarBaixoEstoque(): int
    {
        return (int) $this->conn->query("
            SELECT COUNT(*) FROM estoque e
            JOIN variacao v ON v.id = e.variacao_id
            JOIN produto p ON p.id = v.produto_id
            WHERE p.ativo = 1 AND e.quantidade <= e.minimo
        ")->fetchColumn();
    }
}
