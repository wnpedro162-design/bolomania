<?php
require __DIR__ . '/_icones.php';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$moeda = static fn($v): string => 'R$ ' . number_format((float) $v, 2, ',', '.');

// números do banner
$mesAtual = date('Y-m');
$doMes = array_filter($entradas, fn($e) => substr($e['data'], 0, 7) === $mesAtual);
$valorMes = array_sum(array_map(fn($e) => (float) $e['valor_total'], $doMes));
$comNota = count(array_filter($entradas, fn($e) => !empty($e['nf_numero'])));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Entradas - Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=4">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <section class="bm-hero" id="hero">
        <div>
            <span class="bm-hero__data">✦ Abastecimento do estoque</span>
            <h1>Entradas de <span>mercadoria</span></h1>
            <p>Registre os bolos recebidos dos fornecedores. O estoque de cada variação sobe na hora e tudo fica no histórico.</p>
            <div class="bm-chips">
                <span class="bm-chip"><b data-contar="<?= count($entradas) ?>"><?= count($entradas) ?></b> entradas no total</span>
                <span class="bm-chip"><b data-contar="<?= count($doMes) ?>"><?= count($doMes) ?></b> neste mês</span>
                <span class="bm-chip"><b data-contar="<?= $comNota ?>"><?= $comNota ?></b> com nota fiscal</span>
            </div>
        </div>
        <div class="bm-cofre">
            <small>Comprado neste mês</small>
            <strong data-dinheiro="<?= number_format($valorMes, 2, '.', '') ?>"><?= $moeda($valorMes) ?></strong>
            <span>valor pago aos fornecedores</span>
        </div>
    </section>

    <?php if ($aviso): ?>
        <div class="bm-aviso <?= $aviso['erro'] ? 'bm-aviso--erro' : 'bm-aviso--ok' ?>" role="status"><?= $h($aviso['msg']) ?></div>
    <?php endif; ?>

    <?php if (!$fornecedores || !$variacoes): ?>
        <div class="bm-aviso bm-aviso--erro" style="margin-top:20px;">
            <?php if (!$fornecedores): ?>Nenhum fornecedor ativo. <a href="index.php?controller=fornecedor&action=index">Cadastre um fornecedor</a> primeiro.<br><?php endif; ?>
            <?php if (!$variacoes): ?>Nenhum bolo com variação. Em <a href="index.php?controller=produto&action=index">Produtos</a>, clique em <b>Variações</b> e cadastre peso, sabor e preço.<?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="bm-legenda" style="margin-top:24px;">
        <span class="bm-legenda__titulo"><?= $icone('info') ?> Passo a passo</span>
        <span><b>1.</b> escolha o fornecedor</span>
        <span><b>2.</b> adicione os bolos, a quantidade e o custo</span>
        <span><b>3.</b> (opcional) informe a nota do fornecedor</span>
        <span><b>4.</b> clique em <b>Registrar entrada</b></span>
    </div>

    <div class="bm-grade bm-grade--lado">

        <!-- HISTÓRICO -->
        <section class="bm-painel">
            <div class="bm-painel__titulo">
                <h2><?= $icone('entrada') ?> Histórico de entradas</h2>
                <small>da mais nova para a mais antiga</small>
            </div>
            <div class="bm-tabela-wrap">
                <table class="bm-tabela">
                    <thead><tr><th>Código</th><th>Fornecedor</th><th>Data</th><th class="num">Valor</th><th>Nota fiscal</th></tr></thead>
                    <tbody>
                    <?php if (!$entradas): ?>
                        <tr><td colspan="5" class="bm-tabela-vazia">Nenhuma entrada registrada ainda.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($entradas as $e): ?>
                        <tr>
                            <td><strong>#<?= (int) $e['id'] ?></strong></td>
                            <td><?= $h($e['fornecedor_nome']) ?></td>
                            <td><?= date('d/m/Y', strtotime($e['data'])) ?></td>
                            <td class="num"><strong><?= $moeda($e['valor_total']) ?></strong></td>
                            <td>
                                <?php if (!empty($e['nf_numero'])): ?>
                                    <a class="bm-acao" href="index.php?controller=entrada&action=verNota&id=<?= (int) $e['id'] ?>" target="_blank"><?= $icone('nota') ?> NF <?= $h($e['nf_numero']) ?></a>
                                <?php else: ?>
                                    <span class="bm-st bm-st--info">Sem nota</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- NOVA ENTRADA -->
        <aside class="bm-form" id="form">
            <div class="bm-form__topo">
                <small>Recebimento de mercadoria</small>
                <h2>Nova entrada</h2>
            </div>
            <form method="post" action="index.php?controller=entrada&action=salvar">

                <div class="bm-campo">
                    <label for="fornecedor">Fornecedor</label>
                    <select id="fornecedor" name="fornecedor_id" required>
                        <option value="">Selecione um fornecedor</option>
                        <?php foreach ($fornecedores as $f): ?>
                            <option value="<?= (int) $f['id'] ?>"><?= $h($f['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <p class="bm-rotulo">Bolos recebidos</p>
                <div class="bm-itens" id="itens">
                    <div class="bm-item">
                        <div class="bm-campo">
                            <label>Bolo / variação</label>
                            <select name="variacao_id[]" required>
                                <option value="">Selecione</option>
                                <?php foreach ($variacoes as $v): ?>
                                    <option value="<?= (int) $v['id'] ?>"><?= $h($v['produto_nome'] . ' — ' . $v['tamanho'] . ' / ' . $v['cor']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="bm-campo">
                            <label>Qtd.</label>
                            <input type="number" name="quantidade[]" min="1" required>
                        </div>
                        <div class="bm-campo">
                            <label>Custo (R$)</label>
                            <input type="number" name="custo_unitario[]" min="0" step="0.01" required>
                        </div>
                        <button type="button" class="bm-item__remover" title="Remover este bolo" disabled>×</button>
                    </div>
                </div>
                <button type="button" class="bm-botao-add" id="adicionar">+ Adicionar outro bolo</button>

                <div class="bm-total-linha"><span>Total da entrada</span><strong id="total">R$ 0,00</strong></div>

                <details class="bm-detalhe">
                    <summary>Nota fiscal do fornecedor (opcional)</summary>
                    <div class="form-row">
                        <div class="bm-campo">
                            <label for="nf_numero">Número</label>
                            <input id="nf_numero" type="text" name="nf_numero" maxlength="10">
                        </div>
                        <div class="bm-campo">
                            <label for="nf_serie">Série</label>
                            <input id="nf_serie" type="text" name="nf_serie" maxlength="5" value="1">
                        </div>
                    </div>
                    <div class="bm-campo">
                        <label for="nf_chave">Chave de acesso (44 números)</label>
                        <input id="nf_chave" type="text" name="nf_chave" maxlength="54" inputmode="numeric" placeholder="0000 0000 0000 ...">
                    </div>
                    <div class="bm-campo">
                        <label for="nf_data">Data de emissão</label>
                        <input id="nf_data" type="date" name="nf_data" value="<?= date('Y-m-d') ?>">
                    </div>
                </details>

                <div class="bm-form__botoes" style="margin-top:16px;">
                    <button type="submit">Registrar entrada</button>
                </div>
                <p class="bm-form__dica">✦ Falta algum bolo na lista? Cadastre o peso e o sabor em <a href="index.php?controller=produto&action=index">Produtos → Variações</a>.</p>
            </form>
        </aside>
    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script>
(function () {
    var itens = document.getElementById('itens');
    var fmt = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

    function atualizarTotal() {
        var total = 0;
        itens.querySelectorAll('.bm-item').forEach(function (linha) {
            var q = parseFloat(linha.querySelector('[name="quantidade[]"]').value) || 0;
            var c = parseFloat(linha.querySelector('[name="custo_unitario[]"]').value) || 0;
            total += q * c;
        });
        document.getElementById('total').textContent = fmt.format(total);
        var linhas = itens.querySelectorAll('.bm-item');
        linhas.forEach(function (l) { l.querySelector('.bm-item__remover').disabled = linhas.length === 1; });
    }

    document.getElementById('adicionar').addEventListener('click', function () {
        var novo = itens.querySelector('.bm-item').cloneNode(true);
        novo.querySelectorAll('input').forEach(function (i) { i.value = ''; });
        novo.querySelector('select').selectedIndex = 0;
        itens.appendChild(novo);
        atualizarTotal();
    });

    itens.addEventListener('click', function (ev) {
        if (ev.target.classList.contains('bm-item__remover') && !ev.target.disabled) {
            ev.target.closest('.bm-item').remove();
            atualizarTotal();
        }
    });
    itens.addEventListener('input', atualizarTotal);
    atualizarTotal();
})();
</script>
<script src="/bolomania/public/assets/js/painel.js?v=1"></script>
</body>
</html>
