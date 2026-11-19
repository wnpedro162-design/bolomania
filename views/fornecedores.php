<?php
require __DIR__ . '/_icones.php';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$ativos = count(array_filter($fornecedores, fn($f) => (int) $f['ativo'] === 1));
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fornecedores - Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=4">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <section class="bm-hero bm-hero--compacta" id="hero">
        <div>
            <span class="bm-hero__data">✦ Parceiros da confeitaria</span>
            <h1>Nossos <span>fornecedores</span></h1>
            <p>Quem entrega os bolos para o estoque. Só fornecedores <b>ativos</b> aparecem na tela de Entradas.</p>
            <div class="bm-chips">
                <span class="bm-chip"><b data-contar="<?= count($fornecedores) ?>"><?= count($fornecedores) ?></b> cadastrados</span>
                <span class="bm-chip"><b data-contar="<?= $ativos ?>"><?= $ativos ?></b> ativos</span>
                <span class="bm-chip"><b data-contar="<?= count($fornecedores) - $ativos ?>"><?= count($fornecedores) - $ativos ?></b> inativos</span>
            </div>
        </div>
        <a class="bm-botao bm-botao--amarelo bm-botao--grande" href="#form"><?= $icone('mais') ?> Novo fornecedor</a>
    </section>

    <?php if ($aviso): ?>
        <div class="bm-aviso <?= $aviso['erro'] ? 'bm-aviso--erro' : 'bm-aviso--ok' ?>" role="status"><?= $h($aviso['msg']) ?></div>
    <?php endif; ?>

    <div class="bm-legenda" style="margin-top:24px;">
        <span class="bm-legenda__titulo"><?= $icone('info') ?> Como funciona</span>
        <span><b>CEP</b> preenche o endereço sozinho (API ViaCEP)</span>
        <span><i class="bm-ponto"></i><b>Editar</b> altera os dados</span>
        <span><i class="bm-ponto bm-ponto--aviso"></i><b>Inativar</b> some das Entradas, sem apagar</span>
        <span><i class="bm-ponto bm-ponto--sucesso"></i><b>Ativar</b> volta a aparecer</span>
    </div>

    <div class="bm-grade bm-grade--lado">

        <section class="bm-painel">
            <div class="bm-painel__titulo"><h2><?= $icone('fornecedor') ?> Lista de fornecedores</h2></div>
            <div class="bm-tabela-wrap">
                <table class="bm-tabela">
                    <thead><tr><th>Fornecedor</th><th>CNPJ / Contato</th><th>Endereço</th><th>Status</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php if (!$fornecedores): ?>
                        <tr><td colspan="5" class="bm-tabela-vazia">Nenhum fornecedor cadastrado. Use o formulário ao lado.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($fornecedores as $f): $ativo = (int) $f['ativo'] === 1; $id = (int) $f['id']; ?>
                        <tr class="<?= $editar && (int) $editar['id'] === $id ? 'bm-linha--destaque' : '' ?>">
                            <td><strong><?= $h($f['nome']) ?></strong><small>#<?= $id ?><?= $f['email'] ? ' · ' . $h($f['email']) : '' ?></small></td>
                            <td style="white-space:nowrap;"><?= $h($f['cnpj']) ?><small><?= $h($f['telefone'] ?? '') ?></small></td>
                            <td><?= $h($f['endereco'] ?? '-') ?><?php if (!empty($f['cep'])): ?><small>CEP <?= $h($f['cep']) ?></small><?php endif; ?></td>
                            <td><span class="bm-st <?= $ativo ? 'bm-st--ok' : 'bm-st--off' ?>"><?= $ativo ? 'Ativo' : 'Inativo' ?></span></td>
                            <td>
                                <div class="bm-acoes-inline">
                                    <a class="bm-acao" href="index.php?controller=fornecedor&action=index&id=<?= $id ?>#form"><?= $icone('editar') ?> Editar</a>
                                    <?php if ($ativo): ?>
                                        <a class="bm-acao bm-acao--aviso" href="index.php?controller=fornecedor&action=toggle&id=<?= $id ?>&ativo=0"
                                           data-confirmar="<?= $h('Inativar o fornecedor “' . $f['nome'] . '”?' . "\n\n" . 'Ele some da tela de Entradas, mas o histórico de compras continua.') ?>"><?= $icone('pausar') ?> Inativar</a>
                                    <?php else: ?>
                                        <a class="bm-acao bm-acao--sucesso" href="index.php?controller=fornecedor&action=toggle&id=<?= $id ?>&ativo=1"><?= $icone('ativar') ?> Ativar</a>
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
                <small><?= $editar ? 'Editando o fornecedor #' . (int) $editar['id'] : 'Novo parceiro' ?></small>
                <h2><?= $editar ? $h($editar['nome']) : 'Cadastrar fornecedor' ?></h2>
            </div>
            <form method="post" action="index.php?controller=fornecedor&action=salvar">
                <input type="hidden" name="id" value="<?= $editar ? (int) $editar['id'] : 0 ?>">

                <div class="bm-campo">
                    <label for="nome">Nome / razão social</label>
                    <input id="nome" type="text" name="nome" maxlength="100" required value="<?= $editar ? $h($editar['nome']) : '' ?>">
                </div>
                <div class="form-row">
                    <div class="bm-campo">
                        <label for="cnpj">CNPJ</label>
                        <input id="cnpj" type="text" name="cnpj" maxlength="18" required placeholder="00.000.000/0000-00" value="<?= $editar ? $h($editar['cnpj']) : '' ?>">
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
                    <button type="submit"><?= $editar ? 'Salvar alterações' : 'Cadastrar fornecedor' ?></button>
                    <a href="index.php?controller=fornecedor&action=index">Limpar</a>
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
