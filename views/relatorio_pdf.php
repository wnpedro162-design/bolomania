<?php
// Este arquivo NÃO é aberto pelo navegador: ele só monta o HTML que a Dompdf transforma em PDF.
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$r = static fn($v): string => 'R$ ' . number_format((float) $v, 2, ',', '.');
$d = static fn($v): string => date('d/m/Y', strtotime($v));
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 28px 32px; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #2b140b; }
    .topo { background: #3b1a0f; color: #fff; padding: 14px 18px; border-bottom: 5px solid #f5a100; }
    .topo img { width: 46px; height: 46px; border-radius: 23px; vertical-align: middle; margin-right: 10px; }
    .topo h1 { display: inline-block; vertical-align: middle; font-size: 20px; margin: 0; }
    .topo p { margin: 6px 0 0; color: #e6d3c2; font-size: 11px; }
    h2 { font-size: 13px; color: #3b1a0f; border-left: 4px solid #f5a100; padding-left: 8px; margin: 22px 0 8px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #3b1a0f; color: #f5a100; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; }
    td { padding: 6px 8px; border-bottom: 1px solid #e8ddd2; }
    tr:nth-child(even) td { background: #faf6f1; }
    .num { text-align: right; }
    .kpis td { width: 33%; background: #fff3d6; border: 1px solid #e8ddd2; padding: 10px; }
    .kpis span { display: block; font-size: 9px; text-transform: uppercase; color: #7a5a48; }
    .kpis strong { font-size: 16px; }
    .vazio { color: #7a5a48; font-style: italic; }
    .rodape { margin-top: 26px; font-size: 9px; color: #7a5a48; text-align: center; }
</style>
</head>
<body>

<div class="topo">
    <?php if ($logoBase64): ?><img src="<?= $logoBase64 ?>" alt=""><?php endif; ?>
    <h1>Relatório Gerencial — Bolo Mania</h1>
    <p>Período: <?= $d($inicio) ?> a <?= $d($fim) ?> · Gerado em <?= date('d/m/Y H:i') ?> por <?= $e($_SESSION['nome'] ?? '') ?></p>
</div>

<h2>Resumo do período</h2>
<table class="kpis">
    <tr>
        <td><span>Vendas finalizadas</span><strong><?= (int) $resumo['quantidade_vendas'] ?></strong></td>
        <td><span>Faturamento</span><strong><?= $r($resumo['faturamento']) ?></strong></td>
        <td><span>Ticket médio</span><strong><?= $r($resumo['ticket_medio']) ?></strong></td>
    </tr>
</table>

<h2>Bolos mais vendidos</h2>
<table>
    <thead><tr><th>Bolo</th><th>Tamanho</th><th>SKU</th><th class="num">Qtd.</th><th class="num">Faturamento</th></tr></thead>
    <tbody>
    <?php if (!$produtosMaisVendidos): ?>
        <tr><td colspan="5" class="vazio">Nenhuma venda no período.</td></tr>
    <?php endif; ?>
    <?php foreach ($produtosMaisVendidos as $p): ?>
        <tr>
            <td><?= $e($p['produto']) ?></td>
            <td><?= $e($p['tamanho']) ?></td>
            <td><?= $e($p['sku']) ?></td>
            <td class="num"><?= (int) $p['quantidade_vendida'] ?></td>
            <td class="num"><?= $r($p['faturamento']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Vendas por dia</h2>
<table>
    <thead><tr><th>Data</th><th class="num">Vendas</th><th class="num">Faturamento</th></tr></thead>
    <tbody>
    <?php if (!$vendasPorDia): ?>
        <tr><td colspan="3" class="vazio">Nenhuma venda finalizada no período.</td></tr>
    <?php endif; ?>
    <?php foreach ($vendasPorDia as $v): ?>
        <tr>
            <td><?= $d($v['data']) ?></td>
            <td class="num"><?= (int) $v['quantidade'] ?></td>
            <td class="num"><?= $r($v['faturamento']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Estoque baixo (agora)</h2>
<table>
    <thead><tr><th>Bolo</th><th>Tamanho</th><th>SKU</th><th class="num">Estoque</th><th class="num">Mínimo</th></tr></thead>
    <tbody>
    <?php if (!$estoqueBaixo): ?>
        <tr><td colspan="5" class="vazio">Nenhum bolo com estoque baixo.</td></tr>
    <?php endif; ?>
    <?php foreach ($estoqueBaixo as $item): ?>
        <tr>
            <td><?= $e($item['produto']) ?></td>
            <td><?= $e($item['tamanho']) ?></td>
            <td><?= $e($item['sku']) ?></td>
            <td class="num"><?= (int) $item['quantidade'] ?></td>
            <td class="num"><?= (int) $item['minimo'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p class="rodape">Bolo Mania — relatório gerado automaticamente pelo sistema.</p>
</body>
</html>
