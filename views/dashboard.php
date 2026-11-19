<?php
$nome    = $_SESSION['nome'] ?? 'Usuário';
$perfil  = $_SESSION['perfil'] ?? 'vendedor';
$ehAdmin = $perfil === 'admin';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

// saudação de acordo com a hora do dia
date_default_timezone_set('America/Sao_Paulo');
$hora = (int) date('H');
$saudacao = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');

$meses = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
$dias  = ['domingo','segunda-feira','terça-feira','quarta-feira','quinta-feira','sexta-feira','sábado'];
$hoje  = ucfirst($dias[(int) date('w')]) . ', ' . date('d') . ' de ' . $meses[(int) date('n') - 1] . ' de ' . date('Y');

$faturamentoMes = (float) ($resumoMes['faturamento'] ?? 0);
$primeiroNome   = explode(' ', trim($nome))[0];

// ícones (SVG simples, sem biblioteca externa)
$icone = [
    'produto'    => '<path d="M4 18h16M6 18v-6h12v6M8 12V8h8v4M12 8V5"/><circle cx="12" cy="4" r="1"/>',
    'categoria'  => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
    'fornecedor' => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
    'cliente'    => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c.8-3.2 3-5 5.5-5s4.7 1.8 5.5 5"/><circle cx="17" cy="9" r="2.4"/><path d="M15.8 14.2c2.3.2 4 1.8 4.7 4.8"/>',
    'entrada'    => '<path d="M12 3v11M7.5 9.5 12 14l4.5-4.5"/><path d="M4 15v4h16v-4"/>',
    'venda'      => '<path d="M4 5h2l2.2 10h9.6L20 8H7"/><circle cx="9.5" cy="19" r="1.4"/><circle cx="16.5" cy="19" r="1.4"/>',
    'relatorio'  => '<path d="M5 20V10M10 20V4M15 20v-7M20 20v-11"/>',
    'loja'       => '<path d="M4 9l1.5-5h13L20 9M4 9h16v11H4zM4 9c0 1.7 1.3 3 3 3s3-1.3 3-3c0 1.7 1.3 3 3 3s3-1.3 3-3c0 1.7 1.3 3 3 3"/>',
    'venda_kpi'  => '<path d="M4 17l5-5 4 4 7-7M15 9h5v5"/>',
    'alerta'     => '<path d="M12 4 3 20h18L12 4zM12 10v4M12 17v.5"/>',
    'suporte'    => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="2" y="14" width="5" height="6" rx="2"/><rect x="17" y="14" width="5" height="6" rx="2"/>',
    'pedido'     => '<path d="M6 4h12v16l-3-2-3 2-3-2-3 2zM9 9h6M9 13h6"/>',
];
$svg = fn(string $k) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icone[$k] . '</svg>';

$modulos = [
    ['produto',    'Produtos',     'Cardápio, fotos e variações dos bolos', false],
    ['categoria',  'Categorias',   'Organize o catálogo da loja',           true],
    ['fornecedor', 'Fornecedores', 'Parceiros com busca por CEP',           true],
    ['cliente',    'Clientes',     'Cadastro de clientes do balcão',        true],
    ['entrada',    'Entradas',     'Abasteça o estoque e registre NF',      false],
    ['venda',      'Vendas',       'Balcão com emissão de nota fiscal',     false],
    ['relatorio',  'Relatórios',   'Indicadores e exportação PDF/Excel',    false],
    ['suporte',    'Suporte',      'WhatsApp e e-mail de atendimento',      false],
];
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Painel - Bolo Mania</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=7">
</head>

<body class="bm">

<?php require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <!-- BANNER DE BOAS-VINDAS -->
    <section class="bm-hero" id="hero">
        <div>
            <span class="bm-hero__data">✦ <?= $h($hoje) ?></span>
            <h1><?= $h($saudacao) ?>, <span><?= $h($primeiroNome) ?></span>!</h1>
            <p>Tudo pronto para mais um dia de bolos fresquinhos. Acompanhe os números da confeitaria e escolha um módulo abaixo para começar.</p>
        </div>

        <div class="bm-cofre">
            <small>Faturamento do mês</small>
            <strong data-dinheiro="<?= number_format($faturamentoMes, 2, '.', '') ?>">R$ <?= number_format($faturamentoMes, 2, ',', '.') ?></strong>
            <span><?= (int) ($resumoMes['quantidade_vendas'] ?? 0) ?> venda(s) no balcão · ticket médio R$ <?= number_format((float) ($resumoMes['ticket_medio'] ?? 0), 2, ',', '.') ?></span>
        </div>
    </section>

    <!-- INDICADORES (manual 21.4) -->
    <h2 class="bm-secao"><b>✦</b> Resumo do mês</h2>
    <div class="bm-kpis">
        <div class="bm-kpi">
            <div class="bm-kpi__icone"><?= $svg('venda_kpi') ?></div>
            <small>Vendas no balcão</small>
            <strong data-contar="<?= (int) $kpiVendasMes ?>"><?= (int) $kpiVendasMes ?></strong>
        </div>
        <div class="bm-kpi">
            <div class="bm-kpi__icone"><?= $svg('pedido') ?></div>
            <small>Pedidos do site</small>
            <strong data-contar="<?= (int) $kpiPedidosSite ?>"><?= (int) $kpiPedidosSite ?></strong>
        </div>
        <div class="bm-kpi">
            <div class="bm-kpi__icone"><?= $svg('entrada') ?></div>
            <small>Entradas</small>
            <strong data-contar="<?= (int) $kpiEntradasMes ?>"><?= (int) $kpiEntradasMes ?></strong>
        </div>
        <div class="bm-kpi <?= $kpiEstoqueBaixo > 0 ? 'bm-kpi--alerta' : '' ?>">
            <div class="bm-kpi__icone"><?= $svg('alerta') ?></div>
            <small>Estoque baixo</small>
            <strong data-contar="<?= (int) $kpiEstoqueBaixo ?>"><?= (int) $kpiEstoqueBaixo ?></strong>
        </div>
        <div class="bm-kpi">
            <div class="bm-kpi__icone"><?= $svg('produto') ?></div>
            <small>Bolos ativos</small>
            <strong data-contar="<?= (int) $kpiTotalProdutos ?>"><?= (int) $kpiTotalProdutos ?></strong>
        </div>
    </div>

    <!-- MÓDULOS (manual 22): admin vê tudo, vendedor só o que pode usar -->
    <h2 class="bm-secao"><b>✦</b> Módulos do sistema</h2>
    <div class="bm-grade">

        <div class="bm-modulos">
            <?php foreach ($modulos as [$ctrl, $titulo, $descricao, $soAdmin]): ?>
                <?php if ($soAdmin && !$ehAdmin) continue; ?>
                <a class="bm-modulo" href="/bolomania/index.php?controller=<?= $ctrl ?>&action=index">
                    <div class="bm-modulo__icone"><?= $svg($ctrl) ?></div>
                    <strong><?= $titulo ?><?php if ($soAdmin): ?><em class="bm-selo">Admin</em><?php endif; ?></strong>
                    <span><?= $descricao ?></span>
                    <i>→</i>
                </a>
            <?php endforeach; ?>
        </div>

        <aside class="bm-lado">
            <div class="bm-caixa">
                <h3><?= $svg('alerta') ?> Atenção ao estoque</h3>
                <?php if (!$alertasEstoque): ?>
                    <p class="bm-ok">✔ Tudo em dia! Nenhum bolo abaixo do estoque mínimo.</p>
                <?php else: ?>
                    <?php foreach ($alertasEstoque as $a): ?>
                        <div class="bm-alerta">
                            <div>
                                <?= $h($a['produto']) ?>
                                <small><?= $h($a['tamanho']) ?> · <?= $h($a['sku']) ?></small>
                            </div>
                            <b><?= (int) $a['quantidade'] ?>/<?= (int) $a['minimo'] ?></b>
                        </div>
                    <?php endforeach; ?>
                    <a class="bm-botao bm-botao--amarelo" style="margin-top:12px;" href="/bolomania/index.php?controller=entrada&action=index">Registrar entrada</a>
                <?php endif; ?>
            </div>

            <div class="bm-caixa bm-caixa--loja">
                <h3><?= $svg('loja') ?> Loja virtual</h3>
                <p>Veja a vitrine como o cliente vê: bolos, carrinho e pagamento por Pix ou cartão.</p>
                <a class="bm-botao" href="/bolomania/index.php?controller=loja&action=index" target="_blank">Abrir a loja →</a>
            </div>
        </aside>

    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script>
// Números sobem de 0 até o valor real (efeito de contagem)
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    document.querySelectorAll('[data-contar]').forEach(function (el) {
        var alvo = parseInt(el.dataset.contar, 10) || 0, inicio = null;
        function passo(t) {
            if (!inicio) inicio = t;
            var p = Math.min((t - inicio) / 900, 1);
            el.textContent = Math.round(alvo * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(passo);
        }
        requestAnimationFrame(passo);
    });

    var din = document.querySelector('[data-dinheiro]');
    if (din) {
        var alvo = parseFloat(din.dataset.dinheiro) || 0, inicio = null;
        var fmt = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
        (function passo(t) {
            if (t === undefined) return requestAnimationFrame(passo);
            if (!inicio) inicio = t;
            var p = Math.min((t - inicio) / 1200, 1);
            din.textContent = fmt.format(alvo * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(passo);
        })();
    }

    // estrelinhas douradas espalhadas pelo banner
    var hero = document.getElementById('hero');
    for (var i = 0; i < 14; i++) {
        var s = document.createElement('span');
        s.className = 'bm-estrela';
        s.textContent = '✦';
        s.style.left = (Math.random() * 100) + '%';
        s.style.top = (Math.random() * 100) + '%';
        s.style.fontSize = (8 + Math.random() * 12) + 'px';
        s.style.animationDelay = (Math.random() * 3.6) + 's';
        hero.appendChild(s);
    }
})();
</script>
</body>
</html>
