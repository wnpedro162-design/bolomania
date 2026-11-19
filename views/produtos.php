<?php
// helper simples para resolver imagem por ID sem usar banco
function imagemProdutoUrl(int $produtoId): string
{
    $baseFs = __DIR__ . "/../public/uploads/produtos/";
    $baseUrl = "/bolomania/public/uploads/produtos/";

    foreach (['jpg', 'png', 'webp'] as $ext) {
        if (file_exists($baseFs . $produtoId . '.' . $ext)) {
            // ?t= muda quando a foto é trocada, assim o navegador não mostra a antiga
            return $baseUrl . $produtoId . '.' . $ext . '?t=' . filemtime($baseFs . $produtoId . '.' . $ext);
        }
    }

    return "/bolomania/public/imagem/produto_sem_foto.png";
}

$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$moeda = static fn($v): string => 'R$ ' . number_format((float) $v, 2, ',', '.');
$ehAdmin = ($_SESSION['perfil'] ?? '') === 'admin';
$resumoVar = $resumoVar ?? [];

// números do banner
$total = count($produtos);
$ativos = count(array_filter($produtos, fn($p) => (int) $p['ativo'] === 1));
$semVariacao = count(array_filter($produtos, fn($p) => empty($resumoVar[$p['id']])));

// ícones
$ic = [
    'busca'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
    'camadas' => '<path d="m12 3 9 5-9 5-9-5 9-5z"/><path d="m3 13 9 5 9-5"/>',
    'editar'  => '<path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="m13.5 6.5 4 4"/>',
    'pausar'  => '<rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/>',
    'ativar'  => '<path d="M5 12.5 10 17l9-10"/>',
    'lixo'    => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
    'mais'    => '<path d="M12 5v14M5 12h14"/>',
    'cadeado' => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
    'info'    => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.5"/>',
];
$svg = fn(string $k) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $ic[$k] . '</svg>';
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Produtos - Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=4">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <!-- BANNER -->
    <section class="bm-hero bm-hero--compacta" id="hero">
        <div>
            <span class="bm-hero__data">✦ Catálogo da confeitaria</span>
            <h1>Nossos <span>bolos</span></h1>
            <p>Cadastre os bolos, coloque uma foto caprichada e defina tamanhos, sabores e preços em <b>Variações</b>. É isso que aparece na loja virtual.</p>
            <div class="bm-chips">
                <span class="bm-chip"><b data-contar="<?= $total ?>"><?= $total ?></b> cadastrados</span>
                <span class="bm-chip"><b data-contar="<?= $ativos ?>"><?= $ativos ?></b> ativos</span>
                <span class="bm-chip"><b data-contar="<?= $total - $ativos ?>"><?= $total - $ativos ?></b> inativos</span>
                <?php if ($semVariacao > 0): ?>
                    <span class="bm-chip bm-chip--alerta"><b><?= $semVariacao ?></b> sem variação (fora da loja)</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($ehAdmin): ?>
            <a class="bm-botao bm-botao--amarelo bm-botao--grande" href="#form-produto"><?= $svg('mais') ?> Novo bolo</a>
        <?php endif; ?>
    </section>

    <!-- BUSCA E FILTRO (sem recarregar a página) -->
    <div class="bm-ferramentas">
        <label class="bm-busca">
            <?= $svg('busca') ?>
            <input type="search" id="busca" placeholder="Buscar bolo pelo nome..." aria-label="Buscar bolo">
        </label>
        <div class="bm-filtros" role="group" aria-label="Filtrar por status">
            <button type="button" class="ativo" data-filtro="todos">Todos</button>
            <button type="button" data-filtro="1">Ativos</button>
            <button type="button" data-filtro="0">Inativos</button>
        </div>
    </div>

    <!-- LEGENDA: o que cada botão faz -->
    <div class="bm-legenda">
        <span class="bm-legenda__titulo"><?= $svg('info') ?> O que cada botão faz</span>
        <span><i class="bm-ponto bm-ponto--amarelo"></i><b>Variações e preços</b> tamanhos, sabores, valores e estoque</span>
        <span><i class="bm-ponto"></i><b>Editar</b> nome, descrição e foto</span>
        <span><i class="bm-ponto bm-ponto--aviso"></i><b>Inativar</b> tira da loja sem apagar</span>
        <span><i class="bm-ponto bm-ponto--sucesso"></i><b>Ativar</b> volta para a loja</span>
        <span><i class="bm-ponto bm-ponto--perigo"></i><b>Excluir</b> apaga para sempre</span>
    </div>

    <?php if (!$ehAdmin): ?>
        <div class="bm-aviso bm-aviso--info"><?= $svg('cadeado') ?> <span>Você entrou como <b>vendedor</b>: pode consultar os bolos e as variações. Cadastrar, editar, inativar e excluir é com o <b>administrador</b>.</span></div>
    <?php endif; ?>

    <div class="bm-grade <?= $ehAdmin ? 'bm-grade--produtos' : 'bm-grade--cheia' ?>">

        <!-- LISTA DE BOLOS EM CARTÕES -->
        <div class="bm-produtos" id="lista">
            <?php if (!$produtos): ?>
                <div class="bm-vazio"><strong>Nenhum bolo cadastrado ainda</strong>Use o formulário ao lado para cadastrar o primeiro.</div>
            <?php endif; ?>

            <?php foreach ($produtos as $p):
                $id = (int) $p['id'];
                $ativo = (int) $p['ativo'] === 1;
                $r = $resumoVar[$id] ?? null;
                $editando = $editar && (int) $editar['id'] === $id;
            ?>
            <article class="bm-produto <?= $ativo ? '' : 'bm-produto--inativo' ?> <?= $editando ? 'bm-produto--editando' : '' ?>"
                     data-nome="<?= $h(mb_strtolower($p['nome'])) ?>" data-ativo="<?= $ativo ? 1 : 0 ?>">

                <div class="bm-produto__foto">
                    <img src="<?= $h(imagemProdutoUrl($id)) ?>" alt="<?= $h($p['nome']) ?>" loading="lazy">
                    <span class="bm-produto__id">#<?= $id ?></span>
                    <span class="bm-status <?= $ativo ? 'bm-status--ok' : 'bm-status--off' ?>"><?= $ativo ? 'Ativo' : 'Inativo' ?></span>
                    <div class="bm-produto__preco">
                        <?php if ($r): ?>
                            <small>a partir de</small>
                            <strong><?= $moeda($r['preco_min']) ?></strong>
                        <?php else: ?>
                            <small>sem preço definido</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bm-produto__corpo">
                    <span class="bm-produto__cat"><?= $h($p['categoria_nome']) ?></span>
                    <h3 class="bm-produto__nome"><?= $h($p['nome']) ?></h3>
                    <p class="bm-produto__desc"><?= $h($p['descricao'] ?: 'Sem descrição cadastrada.') ?></p>

                    <div class="bm-produto__info">
                        <?php if ($r): ?>
                            <span class="bm-tag"><?= (int) $r['total_variacoes'] ?> variação(ões)</span>
                            <span class="bm-tag <?= (int) $r['em_alerta'] > 0 ? 'bm-tag--alerta' : '' ?>"><?= (int) $r['estoque_total'] ?> em estoque</span>
                        <?php else: ?>
                            <span class="bm-tag bm-tag--alerta">Cadastre as variações</span>
                        <?php endif; ?>
                    </div>

                    <div class="bm-produto__acoes">
                        <a class="bm-acao bm-acao--principal" href="index.php?controller=variacao&action=index&produto_id=<?= $id ?>"
                           title="Tamanhos, sabores, preços e estoque deste bolo"><?= $svg('camadas') ?> Variações e preços</a>

                        <div class="bm-acoes-linha">
                            <?php if ($ehAdmin): ?>
                                <a class="bm-acao" href="index.php?controller=produto&action=index&id=<?= $id ?>#form-produto"
                                   title="Alterar nome, descrição ou foto"><?= $svg('editar') ?> Editar</a>

                                <?php if ($ativo): ?>
                                    <a class="bm-acao bm-acao--aviso" href="index.php?controller=produto&action=toggle&id=<?= $id ?>&ativo=0"
                                       title="Tira o bolo da loja sem apagar"
                                       data-confirmar="<?= $h('Inativar “' . $p['nome'] . '”?' . "\n\n" . 'Ele some da loja virtual e das vendas, mas continua salvo. Você pode ativar de novo quando quiser.') ?>"><?= $svg('pausar') ?> Inativar</a>
                                <?php else: ?>
                                    <a class="bm-acao bm-acao--sucesso" href="index.php?controller=produto&action=toggle&id=<?= $id ?>&ativo=1"
                                       title="Volta a mostrar o bolo na loja"><?= $svg('ativar') ?> Ativar</a>
                                <?php endif; ?>

                                <a class="bm-acao bm-acao--perigo" href="index.php?controller=produto&action=deletar&id=<?= $id ?>"
                                   title="Apaga o bolo para sempre"
                                   data-confirmar="<?= $h('Excluir “' . $p['nome'] . '” para sempre?' . "\n\n" . 'Essa ação não pode ser desfeita. Se quiser só tirar da loja, use Inativar.') ?>"><?= $svg('lixo') ?> Excluir</a>
                            <?php else: ?>
                                <span class="bm-acao bm-acao--bloqueada" title="Somente o administrador pode alterar"><?= $svg('cadeado') ?> Editar</span>
                                <span class="bm-acao bm-acao--bloqueada" title="Somente o administrador pode alterar"><?= $svg('cadeado') ?> <?= $ativo ? 'Inativar' : 'Ativar' ?></span>
                                <span class="bm-acao bm-acao--bloqueada" title="Somente o administrador pode alterar"><?= $svg('cadeado') ?> Excluir</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>

            <div class="bm-vazio" id="nada" hidden><strong>Nenhum bolo encontrado</strong>Tente outro nome ou outro filtro.</div>
        </div>

        <!-- CADASTRAR / EDITAR (só admin) -->
        <?php if ($ehAdmin): ?>
        <aside class="bm-form" id="form-produto">
            <div class="bm-form__topo">
                <small><?= $editar ? 'Editando o bolo #' . (int) $editar['id'] : 'Novo item do cardápio' ?></small>
                <h2><?= $editar ? $h($editar['nome']) : 'Cadastrar bolo' ?></h2>
            </div>

            <form method="post" action="index.php?controller=produto&action=salvar" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">

                <div class="bm-campo">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria_id" required>
                        <?php if (count($categorias) !== 1): ?>
                            <option value="">Selecione...</option>
                        <?php endif; ?>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= (int) $c['id'] ?>"
                                <?= ($editar && (int) $editar['categoria_id'] === (int) $c['id']) || count($categorias) === 1 ? 'selected' : '' ?>>
                                <?= $h($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bm-campo">
                    <label for="nome">Nome do bolo</label>
                    <input id="nome" type="text" name="nome" maxlength="100" required placeholder="Ex.: Bolo de Brigadeiro"
                           value="<?= $editar ? $h($editar['nome']) : '' ?>">
                </div>

                <div class="bm-campo">
                    <label for="descricao">Descrição (opcional)</label>
                    <textarea id="descricao" name="descricao" rows="3" placeholder="Massa, recheio, cobertura..."><?= $editar ? $h($editar['descricao'] ?? '') : '' ?></textarea>
                </div>

                <div class="bm-campo">
                    <label>Foto do bolo (opcional)</label>
                    <div class="bm-foto" id="area-foto">
                        <img id="previa" src="<?= $editar ? $h(imagemProdutoUrl((int) $editar['id'])) : '/bolomania/public/imagem/produto_sem_foto.png' ?>" alt="Pré-visualização da foto">
                        <span id="texto-foto"><b>Clique ou arraste uma foto</b>JPG, PNG ou WEBP de até 2MB</span>
                        <input type="file" name="imagem" id="imagem" accept="image/png, image/jpeg, image/webp">
                    </div>
                </div>

                <div class="bm-form__botoes">
                    <button type="submit"><?= $editar ? 'Salvar alterações' : 'Cadastrar bolo' ?></button>
                    <a href="index.php?controller=produto&action=index">Limpar</a>
                </div>

                <p class="bm-form__dica">✦ Depois de salvar, clique em <b>Variações</b> no cartão do bolo para definir tamanho, sabor e preço.</p>
            </form>
        </aside>
        <?php endif; ?>

    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script>
// confirmação antes de inativar/excluir
document.querySelectorAll('[data-confirmar]').forEach(function (a) {
    a.addEventListener('click', function (ev) {
        if (!confirm(a.dataset.confirmar)) ev.preventDefault();
    });
});

(function () {
    // ---------- busca e filtro dos cartões ----------
    var busca = document.getElementById('busca');
    var botoes = document.querySelectorAll('[data-filtro]');
    var cartoes = document.querySelectorAll('.bm-produto');
    var nada = document.getElementById('nada');
    var filtro = 'todos';

    function aplicar() {
        var termo = busca.value.trim().toLowerCase();
        var visiveis = 0;
        cartoes.forEach(function (c) {
            var ok = c.dataset.nome.indexOf(termo) !== -1 && (filtro === 'todos' || c.dataset.ativo === filtro);
            c.hidden = !ok;
            if (ok) visiveis++;
        });
        nada.hidden = visiveis > 0 || cartoes.length === 0;
    }
    busca.addEventListener('input', aplicar);
    botoes.forEach(function (b) {
        b.addEventListener('click', function () {
            botoes.forEach(function (x) { x.classList.remove('ativo'); });
            b.classList.add('ativo');
            filtro = b.dataset.filtro;
            aplicar();
        });
    });

    // ---------- pré-visualização da foto escolhida ----------
    var input = document.getElementById('imagem');
    if (input) {
        var area = document.getElementById('area-foto');
        input.addEventListener('change', function () {
            var arq = input.files[0];
            if (!arq) return;
            document.getElementById('previa').src = URL.createObjectURL(arq);
            document.getElementById('texto-foto').innerHTML = '<b>' + arq.name.replace(/[<>&]/g, '') + '</b>' + (arq.size / 1024 / 1024).toFixed(2) + ' MB';
        });
        ['dragenter', 'dragover'].forEach(function (ev) { area.addEventListener(ev, function () { area.classList.add('arrastando'); }); });
        ['dragleave', 'drop'].forEach(function (ev) { area.addEventListener(ev, function () { area.classList.remove('arrastando'); }); });
    }

    // ---------- números do banner e estrelinhas ----------
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    document.querySelectorAll('[data-contar]').forEach(function (el) {
        var alvo = parseInt(el.dataset.contar, 10) || 0, inicio = null;
        function passo(t) {
            if (!inicio) inicio = t;
            var p = Math.min((t - inicio) / 800, 1);
            el.textContent = Math.round(alvo * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(passo);
        }
        requestAnimationFrame(passo);
    });
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
