<?php
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$moeda = static fn($v): string => 'R$ ' . number_format((float) $v, 2, ',', '.');
$ehAdmin = ($_SESSION['perfil'] ?? '') === 'admin';
$pid = (int) $produto['id'];

// foto do bolo (mesma regra da tela de Produtos)
$foto = '/bolomania/public/imagem/produto_sem_foto.png';
foreach (['jpg', 'png', 'webp'] as $ext) {
    $arq = __DIR__ . "/../public/uploads/produtos/{$pid}.{$ext}";
    if (is_file($arq)) {
        $foto = "/bolomania/public/uploads/produtos/{$pid}.{$ext}?t=" . filemtime($arq);
        break;
    }
}

// números do banner
$qtdVar = count($variacoes);
$estoqueTotal = array_sum(array_map(fn($v) => (int) $v['estoque'], $variacoes));
$emAlerta = count(array_filter($variacoes, fn($v) => (int) $v['estoque'] <= (int) $v['minimo']));
$precos = array_map(fn($v) => (float) $v['preco'], $variacoes);

$pesos = ['250g', '500g', '750g', '1kg', '1,5kg', '2kg', '2,5kg', '3kg', '4kg', '5kg'];

$ic = [
    'voltar'  => '<path d="M15 6l-6 6 6 6"/>',
    'editar'  => '<path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="m13.5 6.5 4 4"/>',
    'lixo'    => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
    'cadeado' => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
    'info'    => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.5"/>',
    'balanca' => '<path d="M12 4v16M5 20h14M6 8h12M6 8l-3 6a3 3 0 0 0 6 0L6 8zM18 8l-3 6a3 3 0 0 0 6 0l-3-6z"/>',
    'entrada' => '<path d="M12 3v11M7.5 9.5 12 14l4.5-4.5"/><path d="M4 15v4h16v-4"/>',
];
$svg = fn(string $k) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $ic[$k] . '</svg>';
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Variações de <?= $h($produto['nome']) ?> - Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=7">
</head>
<body class="bm">

<?php $menuPainel = true; $abaAtiva = 'produto'; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <!-- BANNER com a foto do bolo -->
    <section class="bm-hero bm-hero--compacta bm-hero--var" id="hero">
        <div class="bm-hero__bolo">
            <img src="<?= $h($foto) ?>" alt="<?= $h($produto['nome']) ?>">
            <div>
                <a class="bm-hero__data bm-voltar" href="index.php?controller=produto&action=index"><?= $svg('voltar') ?> Voltar aos produtos</a>
                <h1>Variações de <span><?= $h($produto['nome']) ?></span></h1>
                <p>Cada variação é uma opção de compra: <b>peso + sabor + preço</b>, com o seu próprio estoque.</p>
                <div class="bm-chips">
                    <span class="bm-chip"><b><?= $qtdVar ?></b> variação(ões)</span>
                    <span class="bm-chip"><b><?= $estoqueTotal ?></b> un. em estoque</span>
                    <?php if ($precos): ?>
                        <span class="bm-chip"><b><?= $moeda(min($precos)) ?></b><?= max($precos) > min($precos) ? ' até ' . $moeda(max($precos)) : '' ?></span>
                    <?php endif; ?>
                    <?php if ($emAlerta): ?>
                        <span class="bm-chip bm-chip--alerta"><b><?= $emAlerta ?></b> com estoque baixo</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if ($aviso): ?>
        <div class="bm-aviso <?= $aviso['erro'] ? 'bm-aviso--erro' : 'bm-aviso--ok' ?>" role="status"><?= $h($aviso['msg']) ?></div>
    <?php endif; ?>

    <!-- LEGENDA -->
    <div class="bm-legenda" style="margin-top:24px;">
        <span class="bm-legenda__titulo"><?= $svg('info') ?> Como funciona</span>
        <span><b>Peso</b> 500g, 1kg, 2kg...</span>
        <span><b>SKU</b> código único da opção</span>
        <span><i class="bm-ponto bm-ponto--sucesso"></i><b>Estoque ok</b></span>
        <span><i class="bm-ponto bm-ponto--perigo"></i><b>Estoque baixo</b> igual ou abaixo do mínimo</span>
        <span><b>Abastecer</b> pela tela de <a href="index.php?controller=entrada&action=index">Entradas</a></span>
    </div>

    <?php if (!$ehAdmin): ?>
        <div class="bm-aviso bm-aviso--info"><?= $svg('cadeado') ?> <span>Você entrou como <b>vendedor</b>: pode consultar as variações. Cadastrar, editar e excluir é com o <b>administrador</b>.</span></div>
    <?php endif; ?>

    <div class="bm-grade <?= $ehAdmin ? 'bm-grade--produtos' : 'bm-grade--cheia' ?>">

        <!-- VARIAÇÕES EM CARTÕES -->
        <div class="bm-variacoes">
            <?php if (!$variacoes): ?>
                <div class="bm-vazio"><strong>Este bolo ainda não tem variações</strong>Sem variação ele não aparece na loja nem nas vendas. Cadastre a primeira no formulário ao lado.</div>
            <?php endif; ?>

            <?php foreach ($variacoes as $v):
                $est = (int) $v['estoque'];
                $min = (int) $v['minimo'];
                $baixo = $est <= $min;
                // barra: cheia quando o estoque é 3x o mínimo (ou 10 un. se o mínimo for 0)
                $teto = max(1, $min > 0 ? $min * 3 : 10);
                $pct = min(100, round($est / $teto * 100));
                $editando = $editar && (int) $editar['id'] === (int) $v['id'];
            ?>
            <article class="bm-var <?= $editando ? 'bm-var--editando' : '' ?>">
                <div class="bm-var__peso">
                    <?= $svg('balanca') ?>
                    <strong><?= $h($v['tamanho']) ?></strong>
                </div>

                <div class="bm-var__corpo">
                    <div class="bm-var__topo">
                        <div>
                            <h3><?= $h($v['cor']) ?></h3>
                            <span class="bm-var__sku">SKU <?= $h($v['sku']) ?></span>
                        </div>
                        <strong class="bm-var__preco"><?= $moeda($v['preco']) ?></strong>
                    </div>

                    <div class="bm-estoque <?= $baixo ? 'bm-estoque--baixo' : '' ?>">
                        <div class="bm-estoque__texto">
                            <span><b><?= $est ?></b> em estoque</span>
                            <span>mínimo <?= $min ?></span>
                        </div>
                        <div class="bm-estoque__barra"><i style="width:<?= $pct ?>%"></i></div>
                        <?php if ($baixo): ?><small>⚠ Estoque baixo — registre uma entrada</small><?php endif; ?>
                    </div>

                    <div class="bm-acoes-linha bm-acoes-linha--2">
                        <?php if ($ehAdmin): ?>
                            <a class="bm-acao" href="index.php?controller=variacao&action=index&produto_id=<?= $pid ?>&id=<?= (int) $v['id'] ?>#form-variacao"
                               title="Alterar peso, sabor, preço ou mínimo"><?= $svg('editar') ?> Editar</a>
                            <a class="bm-acao bm-acao--perigo" href="index.php?controller=variacao&action=deletar&produto_id=<?= $pid ?>&id=<?= (int) $v['id'] ?>"
                               title="Apaga esta opção"
                               data-confirmar="<?= $h('Excluir a variação ' . $v['tamanho'] . ' / ' . $v['cor'] . '?' . "\n\n" . 'Só é possível excluir se ela ainda não teve entradas nem vendas.') ?>"><?= $svg('lixo') ?> Excluir</a>
                        <?php else: ?>
                            <span class="bm-acao bm-acao--bloqueada" title="Somente o administrador pode alterar"><?= $svg('cadeado') ?> Editar</span>
                            <span class="bm-acao bm-acao--bloqueada" title="Somente o administrador pode alterar"><?= $svg('cadeado') ?> Excluir</span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>

            <a class="bm-var-entrada" href="index.php?controller=entrada&action=index"><?= $svg('entrada') ?> Abastecer o estoque em <b>Entradas</b></a>
        </div>

        <!-- FORMULÁRIO (só admin) -->
        <?php if ($ehAdmin): ?>
        <aside class="bm-form" id="form-variacao">
            <div class="bm-form__topo">
                <small><?= $editar ? 'Editando a variação ' . $h($editar['sku']) : 'Nova opção de compra' ?></small>
                <h2><?= $editar ? $h($editar['tamanho'] . ' · ' . $editar['cor']) : 'Nova variação' ?></h2>
            </div>

            <form method="post" action="index.php?controller=variacao&action=salvar">
                <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">
                <input type="hidden" name="produto_id" value="<?= $pid ?>">

                <div class="bm-campo">
                    <label for="tamanho">Peso (g ou kg)</label>
                    <input id="tamanho" type="text" name="tamanho" maxlength="10" required
                           placeholder="Escolha abaixo ou digite" value="<?= $editar ? $h($editar['tamanho']) : '' ?>">
                    <div class="bm-pesos" role="group" aria-label="Pesos mais usados">
                        <?php foreach ($pesos as $p): ?>
                            <button type="button" data-peso="<?= $p ?>"><?= $p ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bm-campo">
                    <label for="sabor">Sabor / recheio</label>
                    <input id="sabor" type="text" name="sabor" maxlength="30" required
                           placeholder="Ex.: Chocolate" value="<?= $editar ? $h($editar['cor']) : '' ?>">
                </div>

                <div class="bm-campo">
                    <label for="sku">SKU (código único)</label>
                    <input id="sku" type="text" name="sku" maxlength="50" required
                           placeholder="Gerado automaticamente" value="<?= $editar ? $h($editar['sku']) : '' ?>">
                    <small class="bm-campo__ajuda">Preenchido sozinho a partir do peso e sabor. Pode alterar se quiser.</small>
                </div>

                <div class="form-row">
                    <div class="bm-campo">
                        <label for="preco">Preço (R$)</label>
                        <input id="preco" type="text" name="preco" inputmode="decimal" required placeholder="49,90"
                               value="<?= $editar ? number_format((float) $editar['preco'], 2, ',', '') : '' ?>">
                    </div>
                    <div class="bm-campo">
                        <label for="minimo">Estoque mínimo</label>
                        <input id="minimo" type="text" name="minimo" inputmode="numeric"
                               value="<?= $editar ? (int) $editar['minimo'] : 2 ?>">
                    </div>
                </div>

                <div class="bm-form__botoes">
                    <button type="submit"><?= $editar ? 'Salvar alterações' : 'Cadastrar variação' ?></button>
                    <a href="index.php?controller=variacao&action=index&produto_id=<?= $pid ?>">Limpar</a>
                </div>

                <p class="bm-form__dica">✦ A variação nasce com estoque <b>zerado</b>. Para ela aparecer à venda, registre uma <a href="index.php?controller=entrada&action=index">Entrada</a>.</p>
            </form>
        </aside>
        <?php endif; ?>
    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script>
// confirmação antes de excluir
document.querySelectorAll('[data-confirmar]').forEach(function (a) {
    a.addEventListener('click', function (ev) {
        if (!confirm(a.dataset.confirmar)) ev.preventDefault();
    });
});

(function () {
    var peso = document.getElementById('tamanho');
    if (!peso) return;
    var sabor = document.getElementById('sabor');
    var sku = document.getElementById('sku');
    var botoes = document.querySelectorAll('[data-peso]');
    var skuManual = sku.value !== '';

    function marcar() {
        botoes.forEach(function (b) { b.classList.toggle('ativo', b.dataset.peso === peso.value); });
    }

    // SKU automático: BOLO-<id>-<PESO>-<SABOR>
    function gerarSku() {
        if (skuManual) return;
        var limpar = function (t) {
            return t.normalize('NFD').replace(/[̀-ͯ]/g, '').toUpperCase().replace(/[^A-Z0-9]+/g, '');
        };
        var partes = ['BOLO', '<?= $pid ?>', limpar(peso.value), limpar(sabor.value).slice(0, 8)].filter(Boolean);
        sku.value = partes.length > 2 ? partes.join('-') : '';
    }

    botoes.forEach(function (b) {
        b.addEventListener('click', function () { peso.value = b.dataset.peso; marcar(); gerarSku(); sabor.focus(); });
    });
    peso.addEventListener('input', function () { marcar(); gerarSku(); });
    sabor.addEventListener('input', gerarSku);
    sku.addEventListener('input', function () { skuManual = sku.value !== ''; });
    marcar();

    // estrelinhas do banner
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var hero = document.getElementById('hero');
    for (var i = 0; i < 10; i++) {
        var s = document.createElement('span');
        s.className = 'bm-estrela';
        s.textContent = '✦';
        s.style.left = (Math.random() * 100) + '%';
        s.style.top = (Math.random() * 100) + '%';
        s.style.fontSize = (8 + Math.random() * 10) + 'px';
        s.style.animationDelay = (Math.random() * 3.6) + 's';
        hero.appendChild(s);
    }
})();
</script>
</body>
</html>
