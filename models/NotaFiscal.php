<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Notas fiscais (manual 25.2).
 * Venda/Entrada = a operação comercial. Nota fiscal = o documento fiscal dela.
 * Regras: 1 nota por venda/entrada; número da NF de venda único por série/modelo;
 * chave de acesso da NF de entrada única.
 */
class NotaFiscal
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /* ------------------------- NF de VENDA ------------------------- */

    public function buscarPorVenda(int $vendaId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM nota_fiscal_venda WHERE venda_id = :venda_id");
        $stmt->execute([':venda_id' => $vendaId]);
        return $stmt->fetch() ?: null;
    }

    /** Emite a NF de uma venda finalizada com o próximo número da série. */
    public function emitirParaVenda(int $vendaId, string $modelo = '65', string $serie = '1'): int
    {
        $jaEmitida = $this->buscarPorVenda($vendaId);
        if ($jaEmitida) {
            throw new RuntimeException("Esta venda já possui nota fiscal emitida (nº {$jaEmitida['numero']}).");
        }

        $stmt = $this->conn->prepare("SELECT valor_total, status FROM venda WHERE id = :id");
        $stmt->execute([':id' => $vendaId]);
        $venda = $stmt->fetch();

        if (!$venda) {
            throw new RuntimeException("Venda não encontrada.");
        }
        if ($venda['status'] !== 'finalizada') {
            throw new RuntimeException("Só é possível emitir nota de venda finalizada.");
        }

        $numero = $this->proximoNumero($modelo, $serie);

        $stmt = $this->conn->prepare("
            INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total)
            VALUES (:venda_id, :modelo, :serie, :numero, CURDATE(), :valor_total)
        ");
        $stmt->execute([
            ':venda_id'    => $vendaId,
            ':modelo'      => $modelo,
            ':serie'       => $serie,
            ':numero'      => $numero,
            ':valor_total' => $venda['valor_total'],
        ]);

        return (int) $this->conn->lastInsertId();
    }

    /* ------------------------ NF de ENTRADA ------------------------ */

    public function buscarPorEntrada(int $entradaId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM nota_fiscal_entrada WHERE entrada_id = :entrada_id");
        $stmt->execute([':entrada_id' => $entradaId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Registra a NF que o FORNECEDOR emitiu para a compra.
     * A chave de acesso (44 dígitos) vem impressa no DANFE do fornecedor.
     */
    public function registrarParaEntrada(int $entradaId, string $modelo, string $serie, string $numero, string $chaveAcesso, ?string $dataEmissao = null): int
    {
        if ($this->buscarPorEntrada($entradaId)) {
            throw new RuntimeException("Esta entrada já possui nota fiscal registrada.");
        }
        if (!preg_match('/^\d{44}$/', $chaveAcesso)) {
            throw new RuntimeException("A chave de acesso deve ter exatamente 44 números.");
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM nota_fiscal_entrada WHERE chave_acesso = :c");
        $stmt->execute([':c' => $chaveAcesso]);
        if ((int) $stmt->fetchColumn() > 0) {
            throw new RuntimeException("Essa chave de acesso já foi usada em outra entrada.");
        }

        $stmt = $this->conn->prepare("SELECT valor_total FROM entrada_mercadoria WHERE id = :id");
        $stmt->execute([':id' => $entradaId]);
        $entrada = $stmt->fetch();
        if (!$entrada) {
            throw new RuntimeException("Entrada não encontrada.");
        }

        $stmt = $this->conn->prepare("
            INSERT INTO nota_fiscal_entrada
                (entrada_id, modelo, serie, numero, chave_acesso, data_emissao, valor_total)
            VALUES
                (:entrada_id, :modelo, :serie, :numero, :chave, :data, :valor_total)
        ");
        $stmt->execute([
            ':entrada_id'  => $entradaId,
            ':modelo'      => $modelo,
            ':serie'       => $serie,
            ':numero'      => $numero,
            ':chave'       => $chaveAcesso,
            ':data'        => $dataEmissao ?: date('Y-m-d'),
            ':valor_total' => $entrada['valor_total'],
        ]);

        return (int) $this->conn->lastInsertId();
    }

    /* --------------------------- Auxiliar -------------------------- */

    /** Próximo número sequencial da NF de venda, único por série/modelo. */
    private function proximoNumero(string $modelo, string $serie): string
    {
        $stmt = $this->conn->prepare("
            SELECT MAX(CAST(numero AS UNSIGNED)) AS ultimo
            FROM nota_fiscal_venda
            WHERE modelo = :modelo AND serie = :serie
        ");
        $stmt->execute([':modelo' => $modelo, ':serie' => $serie]);
        $ultimo = (int) ($stmt->fetch()['ultimo'] ?? 0);

        return (string) ($ultimo + 1);
    }
}
