<?php
require __DIR__ . '/_icones.php';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$moeda = static fn($v): string => 'R$ ' . number_format((float) $v, 2, ',', '.');
$data = static fn($v): string => date('d/m/Y', strtotime($v));

$qs = 'inicio=' . urlencode($inicio) . '&fim=' . urlencode($fim);
$maxDia = max(array_merge([1], array_map(fn($d) => (float) $d['faturamento'], $vendasPorDia)));
$maxQtd = max(array_merge([1], array_map(fn($p) => (int) $p['quantidade_vendida'], $produtosMaisVendidos)));
$totalItens = array_sum(array_map(fn($p) => (int) $p['quantidade_vendida'], $produtosMaisVendidos));

// atalhos de período
$atalhos = [
    'Hoje'           => [date('Y-m-d'), date('Y-m-d')],
    'Últimos 7 dias' => [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')],
    'Este mês'       => [date('Y-m-01'), date('Y-m-d')],
    'Mês passado'    => [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last month'))],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatórios | Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=4">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <section class="bm-hero" id="hero">
        <div>
            <span class="bm-hero__data">✦ <?= $data($inicio) ?> até <?= $data($fim) ?></span>
            <h1>Relatórios da <span>confeitaria</span></h1>
            <p>Acompanhe vendas, bolos campeões e o estoque. Exporte em PDF ou Excel para apresentar.</p>
            <div class="bm-chips">
                <a class="bm-botao bm-botao--amarelo" href="index.php?controller=relatorio&action=exportarPdf&<?= $h($qs) ?>"><?= $icone('pdf') ?> Exportar PDF</a>
                <a class="bm-botao bm-botao--linha" href="index.php?controller=relatorio&action=exportarExcel&<?= $h($qs) ?>"><?= $icone('excel') ?> Exportar Excel</a>
            </div>
        </div>
        <div class="bm-cofre">
            <small>Faturamento no período</small>
            <strong data-dinheiro="<?= number_format((float) $resumo['faturamento'], 2, '.', '') ?>"><?= $moeda($resumo['faturamento']) ?></strong>
            <span><?= (int) $resumo['quantidade_vendas'] ?> venda(s) finalizada(s) no balcão</span>
        </div>
    </section>

    <!-- FILTRO DE DATAS -->
    <section class="bm-painel" style="margin-top:24px;">
        <form class="bm-filtro-datas" method="get" action="index.php">
            <input type="hidden" name="controller" value="relatorio">
            <input type="hidden" name="action" value="index">
            <div class="bm-campo">
                <label for="inicio">Data inicial</label>
                <input type="date" id="inicio" name="inicio" value="<?= $h($inicio) ?>" required>
            </div>
            <div class="bm-campo">
                <label for="fim">Data final</label>
                <input type="date" id="fim" name="fim" value="<?= $h($fim) ?>" required>
            </div>
            <button class="bm-btn" type="submit"><?= $icone('calendario') ?> Filtrar</button>
            <div class="bm-atalhos-data">
                <?php foreach ($atalhos as $rotulo => [$i, $f]): ?>
                    <a href="index.php?controller=relatorio&action=index&inicio=<?= $i ?>&fim=<?= $f ?>"><?= $rotulo ?></a>
                <?php endforeach; ?>
            </div>
        </form>
    </section>

    <!-- INDICADORES -->
    <h2 class="bm-secao"><b>✦</b> Resumo do período</h2>
    <div class="bm-kpis bm-kpis--4">
        <div class="bm-kpi">
            <div class="bm-kpi__icone"><?= $icone('venda') ?></div>
            <small>Vendas finalizadas</small>
            <strong data-contar="<?= (int) $resumo['quantidade_vendas'] ?>"><?= (int) $resumo['quantidade_vendas'] ?></strong>
        </div>
        <div class="bm-kpi">
            <div class="bm-kpi__icone"><?= $icone('dinheiro') ?></div>
            <small>Ticket médio</small>
            <strong style="font-size:28px;"><?= $moeda($resumo['ticket_medio']) ?></strong>
        </div>
        <div class="bm-kpi">
            <div class="bm-kpi__icone"><?= $icone('trofeu') ?></div>
            <small>Bolos vendidos</small>
            <strong data-contar="<?= $totalItens ?>"><?= $totalItens ?></strong>
        </div>
        <div class="bm-kpi <?= $estoqueBaixo ? 'bm-kpi--alerta' : '' ?>">
            <div class="bm-kpi__icone"><?= $icone('alerta') ?></div>
            <small>Com estoque baixo</small>
            <strong data-contar="<?= count($estoqueBaixo) ?>"><?= count($estoqueBaixo) ?></strong>
        </div>
    </div>

    <div class="bm-grade bm-grade--lado" style="margin-top:24px;">

        <!-- GRÁFICO -->
        <section class="bm-painel">
            <div class="bm-painel__titulo"><h2><?= $icone('relatorio') ?> Faturamento por dia</h2><small>altura da barra = valor vendido</small></div>
            <?php if (!$vendasPorDia): ?>
                <div class="bm-vazio" style="padding:40px 20px;"><strong>Nenhuma venda no período</strong>Mude as datas ou faça uma venda no balcão.</div>
            <?php else: ?>
                <div class="bm-grafico">
                    <?php foreach ($vendasPorDia as $d): $alt = max(4, round((float) $d['faturamento'] / $maxDia * 100)); ?>
                        <div class="bm-barra" title="<?= $h($data($d['data']) . ': ' . $d['quantidade'] . ' venda(s), ' . $moeda($d['faturamento'])) ?>">
                            <b><?= $moeda($d['faturamento']) ?></b>
                            <i style="height:<?= $alt ?>%"></i>
                            <span><?= date('d/m', strtotime($d['data'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- RANKING -->
        <section class="bm-painel">
            <div class="bm-painel__titulo"><h2><?= $icone('trofeu') ?> Bolos mais vendidos</h2></div>
            <?php if (!$produtosMaisVendidos): ?>
                <p style="color:#7a5a48;">Nenhuma venda no período.</p>
            <?php else: ?>
                <div class="bm-rank">
                    <?php foreach ($produtosMaisVendidos as $i => $p): ?>
                        <div class="bm-rank__item">
                            <span class="bm-rank__pos"><?= $i + 1 ?>º</span>
                            <span class="bm-rank__nome"><?= $h($p['produto']) ?><small><?= $h($p['tamanho']) ?> · <?= (int) $p['quantidade_vendida'] ?> un.</small></span>
                            <strong style="color:var(--marrom);font-size:14px;"><?= $moeda($p['faturamento']) ?></strong>
                            <div class="bm-rank__barra"><i style="width:<?= round((int) $p['quantidade_vendida'] / $maxQtd * 100) ?>%"></i></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="bm-grade bm-grade--lado" style="margin-top:24px;">

        <!-- ÚLTIMAS VENDAS -->
        <section class="bm-painel">
            <div class="bm-painel__titulo"><h2><?= $icone('nota') ?> Vendas do período</h2></div>
            <div class="bm-tabela-wrap">
                <table class="bm-tabela">
                    <thead><tr><th>Nº</th><th>Data</th><th>Cliente</th><th>Vendedor</th><th class="num">Total</th></tr></thead>
                    <tbody>
                    <?php if (!$ultimasVendas): ?>
                        <tr><td colspan="5" class="bm-tabela-vazia">Nenhuma venda encontrada.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($ultimasVendas as $v): ?>
                        <tr>
                            <td><strong>#<?= (int) $v['id'] ?></strong></td>
                            <td><?= $data($v['data']) ?></td>
                            <td><?= $h($v['cliente']) ?></td>
                            <td><?= $h($v['vendedor']) ?></td>
                            <td class="num"><strong><?= $moeda($v['valor_total']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ESTOQUE BAIXO -->
        <section class="bm-painel">
            <div class="bm-painel__titulo"><h2><?= $icone('alerta') ?> Estoque baixo</h2></div>
            <?php if (!$estoqueBaixo): ?>
                <p class="bm-ok">✔ Tudo em dia! Nenhum bolo abaixo do mínimo.</p>
            <?php else: ?>
                <?php foreach ($estoqueBaixo as $e): ?>
                    <div class="bm-alerta">
                        <div><?= $h($e['produto']) ?><small><?= $h($e['tamanho']) ?> · <?= $h($e['sku']) ?></small></div>
                        <b><?= (int) $e['quantidade'] ?>/<?= (int) $e['minimo'] ?></b>
                    </div>
                <?php endforeach; ?>
                <a class="bm-botao bm-botao--amarelo" style="margin-top:12px;" href="index.php?controller=entrada&action=index"><?= $icone('entrada') ?> Registrar entrada</a>
            <?php endif; ?>
        </section>
    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script src="/bolomania/public/assets/js/painel.js?v=1"></script>
</body>
</html>
