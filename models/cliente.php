<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Cliente do BALCÃO (tabela `cliente`) — usado na tela de Vendas.
 * Mesmo padrão do Fornecedor, com CEP (manual 24.8).
 * Não confundir com ClienteSite (quem compra pela loja virtual).
 */
class Cliente
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listarTodos(): array
    {
        return $this->conn->query("
            SELECT id, nome, cpf_cnpj, telefone, email, endereco, cep
            FROM cliente
            ORDER BY nome
        ")->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM cliente WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function inserir(array $d): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep)
            VALUES (:nome, :cpf_cnpj, :telefone, :email, :endereco, :cep)
        ");
        $stmt->execute($this->parametros($d));
        return (int) $this->conn->lastInsertId();
    }

    public function atualizar(int $id, array $d): void
    {
        $stmt = $this->conn->prepare("
            UPDATE cliente
            SET nome = :nome, cpf_cnpj = :cpf_cnpj, telefone = :telefone,
                email = :email, endereco = :endereco, cep = :cep
            WHERE id = :id
        ");
        $stmt->execute($this->parametros($d) + [':id' => $id]);
    }

    /** Só exclui cliente sem vendas; com vendas o banco recusa (FK) e mantemos o histórico. */
    public function temVendas(int $id): bool
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM venda WHERE cliente_id = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function deletar(int $id): void
    {
        $stmt = $this->conn->prepare("DELETE FROM cliente WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    private function parametros(array $d): array
    {
        return [
            ':nome'     => $d['nome'],
            ':cpf_cnpj' => $d['cpf_cnpj'],
            ':telefone' => $d['telefone'],
            ':email'    => $d['email'],
            ':endereco' => $d['endereco'],
            ':cep'      => $d['cep'],
        ];
    }
}
