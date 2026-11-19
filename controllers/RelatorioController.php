<?php
require_once __DIR__ . '/../models/relatorio.php';

class RelatorioController
{
    // Verificar acesso do usuário
    private function verificarAcesso(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }

        if (!in_array($_SESSION['perfil'] ?? '', ['admin', 'vendedor'], true)) {
            http_response_code(403);
            exit('Acesso negado.');
        }
    }

    // Validar datas
    private function validarData(string $data, string $padrao): string
    {
        $obj = DateTime::createFromFormat('!Y-m-d', $data);
        $erros = DateTime::getLastErrors();

        if (
            !$obj ||
            ($erros !== false &&
            ($erros['warning_count'] > 0 || $erros['error_count'] > 0)) ||
            $obj->format('Y-m-d') !== $data
        ) {
            return $padrao;
        }

        return $data;
    }

    /** Lê o período (inicio/fim) da URL — usado na tela e nas exportações. */
    private function periodo(): array
    {
        $hoje = date('Y-m-d');
        $primeiroDiaMes = date('Y-m-01');

        $inicio = $this->validarData((string) ($_GET['inicio'] ?? $primeiroDiaMes), $primeiroDiaMes);
        $fim = $this->validarData((string) ($_GET['fim'] ?? $hoje), $hoje);

        if ($inicio > $fim) {
            [$inicio, $fim] = [$fim, $inicio];
        }

        return [$inicio, $fim];
    }

    // Página principal dos relatórios
    public function index(): void
    {
        $this->verificarAcesso();

        [$inicio, $fim] = $this->periodo();

        $model = new Relatorio();

        $resumo = $model->resumo($inicio, $fim);
        $vendasPorDia = $model->vendasPorDia($inicio, $fim);
        $produtosMaisVendidos = $model->produtosMaisVendidos($inicio, $fim);
        $estoqueBaixo = $model->estoqueBaixo();
        $ultimasVendas = $model->ultimasVendas($inicio, $fim);

        require __DIR__ . '/../views/relatorios.php';
    }

    /**
     * Exporta o relatório do período para Excel (CSV com ; e acentos corretos) — manual 26.2.
     * index.php?controller=relatorio&action=exportarExcel&inicio=...&fim=...
     */
    public function exportarExcel(): void
    {
        $this->verificarAcesso();

        [$inicio, $fim] = $this->periodo();
        $model = new Relatorio();
        $resumo = $model->resumo($inicio, $fim);
        $maisVendidos = $model->produtosMaisVendidos($inicio, $fim);
        $vendasPorDia = $model->vendasPorDia($inicio, $fim);
        $estoqueBaixo = $model->estoqueBaixo();

        $dinheiro = fn($v) => number_format((float) $v, 2, ',', '');
        $data = fn($d) => date('d/m/Y', strtotime($d));

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="relatorio_bolomania_' . $inicio . '_a_' . $fim . '.csv"');

        echo "\xEF\xBB\xBF"; // BOM: o Excel reconhece os acentos

        $saida = fopen('php://output', 'w');

        fputcsv($saida, ['Relatório Bolo Mania', 'Período: ' . $data($inicio) . ' a ' . $data($fim)], ';');
        fputcsv($saida, [], ';');

        fputcsv($saida, ['RESUMO'], ';');
        fputcsv($saida, ['Vendas finalizadas', 'Faturamento (R$)', 'Ticket médio (R$)'], ';');
        fputcsv($saida, [(int) $resumo['quantidade_vendas'], $dinheiro($resumo['faturamento']), $dinheiro($resumo['ticket_medio'])], ';');
        fputcsv($saida, [], ';');

        fputcsv($saida, ['BOLOS MAIS VENDIDOS'], ';');
        fputcsv($saida, ['Bolo', 'Tamanho', 'SKU', 'Quantidade', 'Faturamento (R$)'], ';');
        foreach ($maisVendidos as $p) {
            fputcsv($saida, [$p['produto'], $p['tamanho'], $p['sku'], (int) $p['quantidade_vendida'], $dinheiro($p['faturamento'])], ';');
        }
        fputcsv($saida, [], ';');

        fputcsv($saida, ['VENDAS POR DIA'], ';');
        fputcsv($saida, ['Data', 'Quantidade de vendas', 'Faturamento (R$)'], ';');
        foreach ($vendasPorDia as $d) {
            fputcsv($saida, [$data($d['data']), (int) $d['quantidade'], $dinheiro($d['faturamento'])], ';');
        }
        fputcsv($saida, [], ';');

        fputcsv($saida, ['ESTOQUE BAIXO'], ';');
        fputcsv($saida, ['Bolo', 'Tamanho', 'SKU', 'Estoque', 'Mínimo'], ';');
        foreach ($estoqueBaixo as $e) {
            fputcsv($saida, [$e['produto'], $e['tamanho'], $e['sku'], (int) $e['quantidade'], (int) $e['minimo']], ';');
        }

        fclose($saida);
        exit;
    }

    /**
     * Exporta o relatório do período em PDF usando a biblioteca Dompdf (manual 26.4).
     * Se a pasta vendor/ não existir, mostra a mesma página pronta para "Imprimir > Salvar como PDF".
     */
    public function exportarPdf(): void
    {
        $this->verificarAcesso();

        [$inicio, $fim] = $this->periodo();
        $model = new Relatorio();
        $resumo = $model->resumo($inicio, $fim);
        $produtosMaisVendidos = $model->produtosMaisVendidos($inicio, $fim);
        $vendasPorDia = $model->vendasPorDia($inicio, $fim);
        $estoqueBaixo = $model->estoqueBaixo();

        // Logo embutida no HTML (a Dompdf não precisa acessar a internet)
        $logo = __DIR__ . '/../public/imagem/logo.png';
        $logoBase64 = is_file($logo) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo)) : '';

        ob_start();
        require __DIR__ . '/../views/relatorio_pdf.php';
        $html = ob_get_clean();

        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (!is_file($autoload)) {
            echo $html . '<script>window.print()</script>';
            exit;
        }

        require_once $autoload;

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('relatorio_bolomania_' . $inicio . '_a_' . $fim . '.pdf', ['Attachment' => true]);
        exit;
    }
}
