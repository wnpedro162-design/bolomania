<?php
require __DIR__ . '/_icones.php';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$comCep = count(array_filter($clientes, fn($c) => !empty($c['cep'])));
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Clientes - Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=4">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <section class="bm-hero bm-hero--compacta" id="hero">
        <div>
            <span class="bm-hero__data">✦ Clientes do balcão</span>
            <h1>Nossos <span>clientes</span></h1>
            <p>Clientes cadastrados aqui aparecem para escolher na tela de <b>Vendas</b> e saem com o nome na nota fiscal.</p>
            <div class="bm-chips">
                <span class="bm-chip"><b data-contar="<?= count($clientes) ?>"><?= count($clientes) ?></b> clientes</span>
                <span class="bm-chip"><b data-contar="<?= $comCep ?>"><?= $comCep ?></b> com endereço por CEP</span>
            </div>
        </div>
        <a class="bm-botao bm-botao--amarelo bm-botao--grande" href="#form"><?= $icone('mais') ?> Novo cliente</a>
    </section>

    <?php if ($aviso): ?>
        <div class="bm-aviso <?= $aviso['erro'] ? 'bm-aviso--erro' : 'bm-aviso--ok' ?>" role="status"><?= $h($aviso['msg']) ?></div>
    <?php endif; ?>

    <div class="bm-legenda" style="margin-top:24px;">
        <span class="bm-legenda__titulo"><?= $icone('info') ?> Como funciona</span>
        <span><b>CPF ou CNPJ</b> obrigatório</span>
        <span><b>CEP</b> preenche o endereço sozinho</span>
        <span><i class="bm-ponto"></i><b>Editar</b> altera os dados</span>
        <span><i class="bm-ponto bm-ponto--perigo"></i><b>Excluir</b> só para quem ainda não comprou</span>
    </div>

    <div class="bm-grade bm-grade--lado">

        <section class="bm-painel">
            <div class="bm-painel__titulo"><h2><?= $icone('cliente') ?> Lista de clientes</h2></div>
            <div class="bm-tabela-wrap">
                <table class="bm-tabela">
                    <thead><tr><th>Cliente</th><th>Documento / Contato</th><th>Endereço</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php if (!$clientes): ?>
                        <tr><td colspan="4" class="bm-tabela-vazia">Nenhum cliente cadastrado ainda.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($clientes as $c): $id = (int) $c['id']; ?>
                        <tr class="<?= $editar && (int) $editar['id'] === $id ? 'bm-linha--destaque' : '' ?>">
                            <td><strong><?= $h($c['nome']) ?></strong><small>#<?= $id ?><?= $c['email'] ? ' · ' . $h($c['email']) : '' ?></small></td>
                            <td style="white-space:nowrap;"><?= $h($c['cpf_cnpj']) ?><small><?= $h($c['telefone'] ?? '') ?></small></td>
                            <td><?= $h($c['endereco'] ?? '-') ?><?php if (!empty($c['cep'])): ?><small>CEP <?= $h($c['cep']) ?></small><?php endif; ?></td>
                            <td>
                                <div class="bm-acoes-inline">
                                    <a class="bm-acao" href="index.php?controller=cliente&action=index&id=<?= $id ?>#form"><?= $icone('editar') ?> Editar</a>
                                    <a class="bm-acao bm-acao--perigo" href="index.php?controller=cliente&action=deletar&id=<?= $id ?>"
                                       data-confirmar="<?= $h('Excluir o cliente “' . $c['nome'] . '”?' . "\n\n" . 'Só é possível se ele ainda não tiver compras registradas.') ?>"><?= $icone('lixo') ?> Excluir</a>
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
                <small><?= $editar ? 'Editando o cliente #' . (int) $editar['id'] : 'Cadastro rápido' ?></small>
                <h2><?= $editar ? $h($editar['nome']) : 'Cadastrar cliente' ?></h2>
            </div>
            <form method="post" action="index.php?controller=cliente&action=salvar">
                <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">

                <div class="bm-campo">
                    <label for="nome">Nome</label>
                    <input id="nome" type="text" name="nome" maxlength="100" required value="<?= $editar ? $h($editar['nome']) : '' ?>">
                </div>
                <div class="form-row">
                    <div class="bm-campo">
                        <label for="doc">CPF ou CNPJ</label>
                        <input id="doc" type="text" name="cpf_cnpj" maxlength="18" required value="<?= $editar ? $h($editar['cpf_cnpj']) : '' ?>">
                    </div>
                    <div class="bm-campo">
                        <label for="telefone">Telefone</label>
                        <input id="telefone" type="text" name="telefone" maxlength="20" placeholder="(21) 99999-9999" value="<?= $editar ? $h($editar['telefone'] ?? '') : '' ?>">
                    </div>
                </div>
                <div class="bm-campo">
                    <label for="email">E-mail</label>
                    <input id="email" type="email" name="email" maxlength="100" value="<?= $editar ? $h($editar['email'] ?? '') : '' ?>">
                </div>
                <div class="bm-campo">
                    <label for="campo-cep">CEP</label>
                    <input type="text" id="campo-cep" name="cep" maxlength="9" placeholder="00000-000" value="<?= $editar ? $h($editar['cep'] ?? '') : '' ?>">
                    <small class="bm-campo__ajuda" id="status-cep">Digite o CEP e o endereço é preenchido automaticamente.</small>
                </div>
                <div class="bm-campo">
                    <label for="endereco">Endereço</label>
                    <textarea id="endereco" name="endereco" rows="3" maxlength="255"><?= $editar ? $h($editar['endereco'] ?? '') : '' ?></textarea>
                </div>
                <div class="bm-form__botoes">
                    <button type="submit"><?= $editar ? 'Salvar alterações' : 'Cadastrar cliente' ?></button>
                    <a href="index.php?controller=cliente&action=index">Limpar</a>
                </div>
            </form>
        </aside>
    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script src="/bolomania/public/assets/js/cep.js?v=2"></script>
<script src="/bolomania/public/assets/js/painel.js?v=1"></script>
</body>
</html>
