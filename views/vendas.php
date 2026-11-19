<?php
require __DIR__ . '/_icones.php';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$reais = static fn(int $centavos): string => 'R$ ' . number_format($centavos / 100, 2, ',', '.');

// números do banner (a partir das últimas vendas)
$hoje = date('Y-m-d');
$vendasHoje = array_filter($vendas, fn($v) => $v['data'] === $hoje && $v['status'] === 'finalizada');
$faturamentoHoje = array_sum(array_map(fn($v) => Venda::centavos((string) $v['valor_total']), $vendasHoje));
$semNota = count(array_filter($vendas, fn($v) => $v['status'] === 'finalizada' && empty($v['nf_numero'])));
$qtdItens = array_sum(array_map(fn($i) => (int) $i['quantidade'], $carrinho));
$disponiveis = count(array_filter($bolos, fn($b) => (int) $b['estoque'] > 0));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Vendas — Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=4">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <section class="bm-hero" id="hero">
        <div>
            <span class="bm-hero__data">✦ Balcão da confeitaria</span>
            <h1>Vendas no <span>balcão</span></h1>
            <p>Monte a venda, finalize e emita a nota fiscal. O estoque é atualizado automaticamente.</p>
            <div class="bm-chips">
                <span class="bm-chip"><b data-contar="<?= count($vendasHoje) ?>"><?= count($vendasHoje) ?></b> vendas hoje</span>
                <span class="bm-chip"><b data-contar="<?= $disponiveis ?>"><?= $disponiveis ?></b> opções com estoque</span>
                <?php if ($semNota > 0): ?>
                    <span class="bm-chip bm-chip--alerta"><b><?= $semNota ?></b> venda(s) sem nota</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="bm-cofre">
            <small>Vendido hoje</small>
            <strong data-dinheiro="<?= number_format($faturamentoHoje / 100, 2, '.', '') ?>"><?= $reais($faturamentoHoje) ?></strong>
            <span>somando as vendas finalizadas de hoje</span>
        </div>
    </section>

    <?php if ($mensagem !== ''): ?>
        <?php $ehErro = preg_match('/não|inválid|insuficiente|ultrapassa|expirad|selecione/i', $mensagem); ?>
        <div class="bm-aviso <?= $ehErro ? 'bm-aviso--erro' : 'bm-aviso--ok' ?>" role="status"><?= $h($mensagem) ?></div>
    <?php endif; ?>

    <div class="bm-legenda" style="margin-top:24px;">
        <span class="bm-legenda__titulo"><?= $icone('info') ?> Passo a passo</span>
        <span><b>1.</b> escolha o bolo e a quantidade e clique em <b>Adicionar</b></span>
        <span><b>2.</b> (opcional) escolha o cliente</span>
        <span><b>3.</b> clique em <b>Finalizar venda</b></span>
        <span><b>4.</b> no histórico, <b>Emitir NF</b> e <b>Ver NF</b></span>
    </div>

    <div class="bm-grade bm-grade--lado">

        <!-- ITENS DA VENDA -->
        <section class="bm-painel">
            <div class="bm-painel__titulo">
                <h2><?= $icone('carrinho') ?> Itens da venda</h2>
                <small><?= $qtdItens ?> item(ns)</small>
            </div>

            <?php if (!$carrinho): ?>
                <div class="bm-vazio" style="padding:34px 20px;"><strong>Nenhum bolo na venda ainda</strong>Escolha um bolo no quadro ao lado para começar.</div>
            <?php else: ?>
                <div class="bm-tabela-wrap">
                    <table class="bm-tabela">
                        <thead><tr><th>Bolo</th><th class="num">Qtd.</th><th class="num">Preço</th><th class="num">Subtotal</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($carrinho as $item): ?>
                            <tr>
                                <td><strong><?= $h($item['nome']) ?></strong><small><?= $h($item['tamanho']) ?> · <?= $h($item['sku']) ?></small></td>
                                <td class="num"><?= (int) $item['quantidade'] ?></td>
                                <td class="num"><?= $reais(Venda::centavos((string) $item['preco'])) ?></td>
                                <td class="num"><strong><?= $reais($item['subtotal']) ?></strong></td>
                                <td>
                                    <form method="post" action="index.php?controller=venda&amp;action=remover">
                                        <input type="hidden" name="token" value="<?= $h($token) ?>">
                                        <input type="hidden" name="variacao_id" value="<?= $h($item['id']) ?>">
                                        <button class="bm-acao bm-acao--perigo" type="submit" title="Remover da venda"><?= $icone('lixo') ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="bm-total-linha"><span>Total da venda</span><strong><?= $reais($total) ?></strong></div>

            <form method="post" action="index.php?controller=venda&amp;action=finalizar">
                <input type="hidden" name="token" value="<?= $h($token) ?>">
                <div class="bm-campo">
                    <label for="cliente">Cliente (opcional)</label>
                    <select name="cliente_id" id="cliente">
                        <option value="">Consumidor não identificado</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $h($cliente['id']) ?>"><?= $h($cliente['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                        <small class="bm-campo__ajuda">Cliente novo? <a href="index.php?controller=cliente&action=index">Cadastre em Clientes</a>.</small>
                    <?php endif; ?>
                </div>
                <button class="bm-btn bm-btn--cheio" type="submit" <?= !$carrinho ? 'disabled' : '' ?>><?= $icone('ativar') ?> Finalizar venda</button>
            </form>

            <?php if ($carrinho): ?>
                <form method="post" action="index.php?controller=venda&amp;action=limpar" style="margin-top:10px;">
                    <input type="hidden" name="token" value="<?= $h($token) ?>">
                    <button class="bm-btn bm-btn--linha bm-btn--cheio" type="submit" style="box-shadow:none;">Limpar venda</button>
                </form>
            <?php endif; ?>
        </section>

        <!-- ADICIONAR BOLO -->
        <aside class="bm-form" id="form">
            <div class="bm-form__topo">
                <small>Monte o pedido do cliente</small>
                <h2>Adicionar bolo</h2>
            </div>
            <?php if (!$bolos): ?>
                <p class="bm-form__dica" style="padding:20px 22px;">Nenhum bolo com variação cadastrada. Em <a href="index.php?controller=produto&action=index">Produtos</a>, clique em <b>Variações</b>.</p>
            <?php else: ?>
            <form method="post" action="index.php?controller=venda&amp;action=adicionar">
                <input type="hidden" name="token" value="<?= $h($token) ?>">
                <div class="bm-campo">
                    <label for="bolo">Bolo / peso</label>
                    <select name="variacao_id" id="bolo" required>
                        <option value="">Selecione um bolo</option>
                        <?php foreach ($bolos as $bolo): ?>
                            <option value="<?= $h($bolo['id']) ?>" data-preco="<?= Venda::centavos((string) $bolo['preco']) ?>" data-estoque="<?= $h($bolo['estoque']) ?>" <?= (int) $bolo['estoque'] < 1 ? 'disabled' : '' ?>>
                                <?= $h($bolo['nome'] . ' — ' . $bolo['tamanho']) ?> · <?= $reais(Venda::centavos((string) $bolo['preco'])) ?><?= (int) $bolo['estoque'] < 1 ? ' (sem estoque)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bm-total-linha" style="margin-top:0;background:#fff3d6;color:var(--marrom);">
                    <span style="color:#7a5a48;" id="estoque-info">Estoque: —</span>
                    <strong style="color:var(--marrom);font-size:20px;" id="preco-info">R$ 0,00</strong>
                </div>

                <div class="bm-campo">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" max="10000" step="1" value="1" required>
                </div>

                <button class="bm-btn bm-btn--cheio" type="submit"><?= $icone('mais') ?> Adicionar à venda</button>
                <p class="bm-form__dica">✦ Opções sem estoque aparecem bloqueadas. Para abastecer, use <a href="index.php?controller=entrada&action=index">Entradas</a>.</p>
            </form>
            <?php endif; ?>
        </aside>
    </div>

    <!-- HISTÓRICO -->
    <section class="bm-painel" style="margin-top:24px;">
        <div class="bm-painel__titulo">
            <h2><?= $icone('nota') ?> Últimas vendas</h2>
            <small>clique em “Ver itens” para detalhar</small>
        </div>
        <div class="bm-tabela-wrap">
            <table class="bm-tabela">
                <thead><tr><th>Venda</th><th>Data</th><th>Cliente</th><th>Vendedor</th><th>Status</th><th class="num">Total</th><th>Ações</th></tr></thead>
                <tbody>
                <?php if (!$vendas): ?>
                    <tr><td colspan="7" class="bm-tabela-vazia">Nenhuma venda registrada ainda.</td></tr>
                <?php endif; ?>
                <?php foreach ($vendas as $venda): $vid = (int) $venda['id']; ?>
                    <tr class="<?= $vendaSelecionada === $vid ? 'bm-linha--destaque' : '' ?>">
                        <td><strong>#<?= $vid ?></strong></td>
                        <td><?= $h(date('d/m/Y', strtotime($venda['data']))) ?></td>
                        <td><?= $h($venda['cliente'] ?? 'Não identificado') ?></td>
                        <td><?= $h($venda['vendedor']) ?></td>
                        <td><span class="bm-st <?= $venda['status'] === 'finalizada' ? 'bm-st--ok' : 'bm-st--info' ?>"><?= $h($venda['status']) ?></span></td>
                        <td class="num"><strong><?= $reais(Venda::centavos((string) $venda['valor_total'])) ?></strong></td>
                        <td>
                            <div class="bm-acoes-inline">
                                <a class="bm-acao" href="index.php?controller=venda&amp;action=index&amp;id=<?= $vid ?>#detalhes"><?= $icone('olho') ?> Ver itens</a>
                                <?php if (!empty($venda['nf_numero'])): ?>
                                    <a class="bm-acao bm-acao--sucesso" href="index.php?controller=venda&amp;action=verNota&amp;venda_id=<?= $vid ?>" target="_blank"><?= $icone('nota') ?> Ver NF <?= $h($venda['nf_numero']) ?></a>
                                <?php elseif ($venda['status'] === 'finalizada'): ?>
                                    <form method="post" action="index.php?controller=venda&amp;action=emitirNota">
                                        <input type="hidden" name="token" value="<?= $h($token) ?>">
                                        <input type="hidden" name="venda_id" value="<?= $vid ?>">
                                        <button class="bm-acao bm-acao--principal" type="submit" style="flex:none;"><?= $icone('nota') ?> Emitir NF</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php if ($vendaSelecionada > 0): ?>
    <section class="bm-painel" id="detalhes" style="margin-top:20px;">
        <div class="bm-painel__titulo"><h2><?= $icone('olho') ?> Itens da venda #<?= (int) $vendaSelecionada ?></h2></div>
        <?php if (!$detalhes): ?>
            <p style="color:#7a5a48;">Nenhum item encontrado para esta venda.</p>
        <?php else: ?>
            <div class="bm-tabela-wrap">
                <table class="bm-tabela">
                    <thead><tr><th>Bolo</th><th>Peso / SKU</th><th class="num">Qtd.</th><th class="num">Preço</th><th class="num">Subtotal</th></tr></thead>
                    <tbody>
                    <?php foreach ($detalhes as $item): ?>
                        <tr>
                            <td><strong><?= $h($item['nome']) ?></strong></td>
                            <td><?= $h($item['tamanho'] . ' / ' . $item['sku']) ?></td>
                            <td class="num"><?= (int) $item['quantidade'] ?></td>
                            <td class="num"><?= $reais(Venda::centavos((string) $item['preco_unitario'])) ?></td>
                            <td class="num"><strong><?= $reais(Venda::centavos((string) $item['preco_unitario']) * (int) $item['quantidade']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script>
(function () {
    var bolo = document.getElementById('bolo');
    if (!bolo) return;
    var fmt = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    bolo.addEventListener('change', function () {
        var op = bolo.options[bolo.selectedIndex];
        if (!bolo.value) {
            document.getElementById('preco-info').textContent = 'R$ 0,00';
            document.getElementById('estoque-info').textContent = 'Estoque: —';
            return;
        }
        document.getElementById('preco-info').textContent = fmt.format(Number(op.dataset.preco) / 100);
        document.getElementById('estoque-info').textContent = 'Estoque: ' + op.dataset.estoque + ' un.';
        document.getElementById('quantidade').max = Math.min(10000, Number(op.dataset.estoque));
    });
})();
</script>
<script src="/bolomania/public/assets/js/painel.js?v=1"></script>
</body>
</html>
