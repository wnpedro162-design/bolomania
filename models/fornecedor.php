<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Fornecedor (manual 18.3).
 * Regra de negócio: fornecedor não é apagado — só inativado (exclusão lógica).
 */
class Fornecedor
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listarTodos(): array
    {
        return $this->conn->query("
            SELECT id, nome, cnpj, telefone, email, logradouro, numero, complemento, bairro, cidade, uf, cep, ativo
            FROM fornecedor
            ORDER BY nome
        ")->fetchAll();
    }

    /** Usado no select da tela de Entradas: só fornecedores ativos. */
    public function listarAtivos(): array
    {
        return $this->conn->query("
            SELECT id, nome FROM fornecedor WHERE ativo = 1 ORDER BY nome
        ")->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM fornecedor WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function inserir(array $d): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO fornecedor (nome, cnpj, telefone, email, logradouro, numero, complemento, bairro, cidade, uf, cep, ativo)
            VALUES (:nome, :cnpj, :telefone, :email, :logradouro, :numero, :complemento, :bairro, :cidade, :uf, :cep, 1)
        ");
        $stmt->execute($this->parametros($d));
        return (int) $this->conn->lastInsertId();
    }

    public function atualizar(int $id, array $d): void
    {
        $stmt = $this->conn->prepare("
            UPDATE fornecedor
            SET nome = :nome, cnpj = :cnpj, telefone = :telefone,
                email = :email, logradouro = :logradouro, numero = :numero,
                complemento = :complemento, bairro = :bairro, cidade = :cidade, uf = :uf, cep = :cep
            WHERE id = :id
        ");
        $stmt->execute($this->parametros($d) + [':id' => $id]);
    }

    public function setAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->conn->prepare("UPDATE fornecedor SET ativo = :ativo WHERE id = :id");
        $stmt->execute([':id' => $id, ':ativo' => $ativo ? 1 : 0]);
    }

    private function parametros(array $d): array
    {
        return [
            ':nome'     => $d['nome'],
            ':cnpj'     => $d['cnpj'],
            ':telefone' => $d['telefone'],
            ':email'    => $d['email'],
        ] + Endereco::paraBanco($d['endereco']);
    }
}
