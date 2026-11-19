<?php
require_once __DIR__ . '/../config/db.php';

class Relatorio
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Resumo geral das vendas
    public function resumo(string $inicio, string $fim): array
    {
        $sql = "
            SELECT
                COUNT(*) AS quantidade_vendas,
                COALESCE(SUM(valor_total), 0) AS faturamento,
                COALESCE(AVG(valor_total), 0) AS ticket_medio
            FROM venda
            WHERE status = 'finalizada'
            AND data BETWEEN :inicio AND :fim
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim' => $fim
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Vendas realizadas por dia
    public function vendasPorDia(string $inicio, string $fim): array
    {
        $sql = "
            SELECT
                data,
                COUNT(*) AS quantidade,
                COALESCE(SUM(valor_total), 0) AS faturamento
            FROM venda
            WHERE status = 'finalizada'
            AND data BETWEEN :inicio AND :fim
            GROUP BY data
            ORDER BY data ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim' => $fim
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Produtos mais vendidos
    public function produtosMaisVendidos(string $inicio, string $fim): array
    {
        $sql = "
            SELECT
                p.nome AS produto,
                v.tamanho,
                v.sku,
                SUM(i.quantidade) AS quantidade_vendida,
                SUM(i.quantidade * i.preco_unitario) AS faturamento
            FROM venda_item i
            INNER JOIN venda vd ON vd.id = i.venda_id
            INNER JOIN variacao v ON v.id = i.variacao_id
            INNER JOIN produto p ON p.id = v.produto_id
            WHERE vd.status = 'finalizada'
            AND vd.data BETWEEN :inicio AND :fim
            GROUP BY p.id, p.nome, v.id, v.tamanho, v.sku
            ORDER BY quantidade_vendida DESC, faturamento DESC
            LIMIT 10
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim' => $fim
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Produtos com estoque baixo
    public function estoqueBaixo(): array
    {
        $sql = "
            SELECT
                p.nome AS produto,
                v.tamanho,
                v.sku,
                e.quantidade,
                e.minimo
            FROM estoque e
            INNER JOIN variacao v ON v.id = e.variacao_id
            INNER JOIN produto p ON p.id = v.produto_id
            WHERE p.ativo = 1
            AND e.quantidade <= e.minimo
            ORDER BY e.quantidade ASC, p.nome ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Histórico de vendas
    public function ultimasVendas(string $inicio, string $fim): array
    {
        $sql = "
            SELECT
                vd.id,
                vd.data,
                vd.valor_total,
                u.nome AS vendedor,
                COALESCE(c.nome, 'Cliente não informado') AS cliente
            FROM venda vd
            INNER JOIN usuario u ON u.id = vd.usuario_id
            LEFT JOIN cliente c ON c.id = vd.cliente_id
            WHERE vd.status = 'finalizada'
            AND vd.data BETWEEN :inicio AND :fim
            ORDER BY vd.data DESC, vd.id DESC
            LIMIT 30
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim' => $fim
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ----------------- KPIs do dashboard (manual 21.4) ----------------- */

    public function kpiVendasMes(): int
    {
        return (int) $this->db->query("
            SELECT COUNT(*) FROM venda
            WHERE status = 'finalizada'
            AND data >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
        ")->fetchColumn();
    }

    public function kpiEntradasMes(): int
    {
        return (int) $this->db->query("
            SELECT COUNT(*) FROM entrada_mercadoria
            WHERE data >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
        ")->fetchColumn();
    }

    public function kpiEstoqueBaixo(): int
    {
        return (int) $this->db->query("
            SELECT COUNT(*) FROM estoque e
            INNER JOIN variacao v ON v.id = e.variacao_id
            INNER JOIN produto p ON p.id = v.produto_id
            WHERE p.ativo = 1 AND e.quantidade <= e.minimo
        ")->fetchColumn();
    }

    public function kpiTotalProdutos(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM produto WHERE ativo = 1")->fetchColumn();
    }

    /** Pedidos da loja virtual no mês (0 se a tabela ainda não foi criada). */
    public function kpiPedidosSiteMes(): int
    {
        try {
            return (int) $this->db->query("
                SELECT COUNT(*) FROM pedido
                WHERE status <> 'cancelado'
                AND data >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
            ")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
