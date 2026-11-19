<?php

require_once __DIR__ . '/../config/db.php';

/**
 * Categoria do catálogo.
 * Regra: categoria não é apagada, só inativada (produtos dependem dela).
 */
class Categoria
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listarAtivas(): array
    {
        return $this->conn->query("
            SELECT id, nome
            FROM categoria
            WHERE ativo = 1
            ORDER BY nome
        ")->fetchAll();
    }

    /** Todas as categorias + quantos produtos cada uma tem. */
    public function listarTodas(): array
    {
        return $this->conn->query("
            SELECT c.id, c.nome, c.ativo, COUNT(p.id) AS total_produtos
            FROM categoria c
            LEFT JOIN produto p ON p.categoria_id = c.id
            GROUP BY c.id, c.nome, c.ativo
            ORDER BY c.nome
        ")->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM categoria WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function inserir(string $nome): int
    {
        $stmt = $this->conn->prepare("INSERT INTO categoria (nome, ativo) VALUES (:nome, 1)");
        $stmt->execute([':nome' => $nome]);
        return (int) $this->conn->lastInsertId();
    }

    public function atualizar(int $id, string $nome): void
    {
        $stmt = $this->conn->prepare("UPDATE categoria SET nome = :nome WHERE id = :id");
        $stmt->execute([':id' => $id, ':nome' => $nome]);
    }

    public function setAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->conn->prepare("UPDATE categoria SET ativo = :ativo WHERE id = :id");
        $stmt->execute([':id' => $id, ':ativo' => $ativo ? 1 : 0]);
    }
}
