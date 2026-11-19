<?php
require __DIR__ . '/_icones.php';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$ativas = count(array_filter($categorias, fn($c) => (int) $c['ativo'] === 1));
$totalBolos = array_sum(array_map(fn($c) => (int) $c['total_produtos'], $categorias));
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Categorias - Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=4">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <section class="bm-hero bm-hero--compacta" id="hero">
        <div>
            <span class="bm-hero__data">✦ Organização do cardápio</span>
            <h1>Categorias de <span>bolos</span></h1>
            <p>As categorias agrupam os bolos na loja virtual (o cliente filtra por elas no topo da vitrine).</p>
            <div class="bm-chips">
                <span class="bm-chip"><b data-contar="<?= count($categorias) ?>"><?= count($categorias) ?></b> categorias</span>
                <span class="bm-chip"><b data-contar="<?= $ativas ?>"><?= $ativas ?></b> ativas</span>
                <span class="bm-chip"><b data-contar="<?= $totalBolos ?>"><?= $totalBolos ?></b> bolos distribuídos</span>
            </div>
        </div>
        <a class="bm-botao bm-botao--amarelo bm-botao--grande" href="#form"><?= $icone('mais') ?> Nova categoria</a>
    </section>

    <?php if ($aviso): ?>
        <div class="bm-aviso <?= $aviso['erro'] ? 'bm-aviso--erro' : 'bm-aviso--ok' ?>" role="status"><?= $h($aviso['msg']) ?></div>
    <?php endif; ?>

    <div class="bm-legenda" style="margin-top:24px;">
        <span class="bm-legenda__titulo"><?= $icone('info') ?> Regras</span>
        <span><i class="bm-ponto"></i><b>Editar</b> muda o nome</span>
        <span><i class="bm-ponto bm-ponto--aviso"></i><b>Inativar</b> some da loja, mas não apaga</span>
        <span><i class="bm-ponto bm-ponto--sucesso"></i><b>Ativar</b> volta para a loja</span>
        <span>Categoria nunca é apagada: os bolos dependem dela.</span>
    </div>

    <div class="bm-grade bm-grade--lado">

        <section class="bm-painel">
            <div class="bm-painel__titulo">
                <h2><?= $icone('categoria') ?> Lista de categorias</h2>
            </div>
            <div class="bm-tabela-wrap">
                <table class="bm-tabela">
                    <thead><tr><th>ID</th><th>Categoria</th><th>Bolos</th><th>Status</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php if (!$categorias): ?>
                        <tr><td colspan="5" class="bm-tabela-vazia">Nenhuma categoria cadastrada.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($categorias as $c): $ativa = (int) $c['ativo'] === 1; $id = (int) $c['id']; ?>
                        <tr class="<?= $editar && (int) $editar['id'] === $id ? 'bm-linha--destaque' : '' ?>">
                            <td>#<?= $id ?></td>
                            <td><strong><?= $h($c['nome']) ?></strong></td>
                            <td><span class="bm-st bm-st--info"><?= (int) $c['total_produtos'] ?> bolo(s)</span></td>
                            <td><span class="bm-st <?= $ativa ? 'bm-st--ok' : 'bm-st--off' ?>"><?= $ativa ? 'Ativa' : 'Inativa' ?></span></td>
                            <td>
                                <div class="bm-acoes-inline">
                                    <a class="bm-acao" href="index.php?controller=categoria&action=index&id=<?= $id ?>#form"><?= $icone('editar') ?> Editar</a>
                                    <?php if ($ativa): ?>
                                        <a class="bm-acao bm-acao--aviso" href="index.php?controller=categoria&action=toggle&id=<?= $id ?>&ativo=0"
                                           data-confirmar="<?= $h('Inativar a categoria “' . $c['nome'] . '”?' . "\n\n" . 'Ela e os bolos dela somem da loja virtual, mas nada é apagado.') ?>"><?= $icone('pausar') ?> Inativar</a>
                                    <?php else: ?>
                                        <a class="bm-acao bm-acao--sucesso" href="index.php?controller=categoria&action=toggle&id=<?= $id ?>&ativo=1"><?= $icone('ativar') ?> Ativar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="bm-form" id="form">
            <div class="bm-form__topo">
                <small><?= $editar ? 'Editando a categoria #' . (int) $editar['id'] : 'Organize o cardápio' ?></small>
                <h2><?= $editar ? $h($editar['nome']) : 'Nova categoria' ?></h2>
            </div>
            <form method="post" action="index.php?controller=categoria&action=salvar">
                <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">
                <div class="bm-campo">
                    <label for="nome">Nome da categoria</label>
                    <input id="nome" type="text" name="nome" maxlength="100" required placeholder="Ex.: Bolos de Festa"
                           value="<?= $editar ? $h($editar['nome']) : '' ?>">
                </div>
                <div class="bm-form__botoes">
                    <button type="submit"><?= $editar ? 'Salvar alterações' : 'Cadastrar categoria' ?></button>
                    <a href="index.php?controller=categoria&action=index">Limpar</a>
                </div>
                <p class="bm-form__dica">✦ Para colocar um bolo numa categoria, vá em <a href="index.php?controller=produto&action=index">Produtos</a> e edite o bolo.</p>
            </form>
        </aside>
    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script src="/bolomania/public/assets/js/painel.js?v=1"></script>
</body>
</html>
