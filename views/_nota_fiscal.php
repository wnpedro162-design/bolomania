<?php
/**
 * Molde único das notas fiscais (venda do balcão, pedido do site e entrada).
 * A view que inclui este arquivo monta o array $nf:
 *   titulo, numero, serie, modelo, data, hora, referencia, voltar,
 *   destinatario_titulo, destinatario (linhas), itens[] (sku, descricao, quantidade, unitario),
 *   total, chave (opcional), rodape
 */
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$r = static fn($v): string => 'R$ ' . number_format((float) $v, 2, ',', '.');
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $e($nf['titulo']) ?> nº <?= $e($nf['numero']) ?> - Bolo Mania</title>
<link rel="stylesheet" href="/bolomania/public/assets/css/nota.css">
</head>
<body class="nf-body">

<div class="nf-container">

    <header class="nf-cabecalho">
        <div class="nf-emitente">
            <img class="nf-logo" src="/bolomania/public/imagem/logo.png?v=3" alt="">
            <div>
                <h1>BOLO MANIA CONFEITARIA LTDA</h1>
                <p>CNPJ: 00.000.000/0001-00 · IE: Isento</p>
                <p>Rua Exemplo, 123 - Centro - Cidade/UF</p>
            </div>
        </div>
        <div class="nf-documento">
            <h2><?= $e(mb_strtoupper($nf['titulo'])) ?></h2>
            <p><strong>Nº</strong> <?= $e(str_pad((string) $nf['numero'], 6, '0', STR_PAD_LEFT)) ?></p>
            <p><strong>Série</strong> <?= $e($nf['serie']) ?> · <strong>Modelo</strong> <?= $e($nf['modelo']) ?></p>
        </div>
    </header>

    <section class="nf-faixa">
        <div><span>Data de emissão</span><strong><?= $e($nf['data']) ?></strong></div>
        <?php if (!empty($nf['hora'])): ?><div><span>Hora</span><strong><?= $e($nf['hora']) ?></strong></div><?php endif; ?>
        <div><span>Referência</span><strong><?= $e($nf['referencia']) ?></strong></div>
    </section>

    <section class="nf-bloco">
        <h3><?= $e($nf['destinatario_titulo']) ?></h3>
        <?php foreach ($nf['destinatario'] as $linha): ?>
            <p><?= $e($linha) ?></p>
        <?php endforeach; ?>
    </section>

    <table class="nf-tabela">
        <thead>
            <tr>
                <th>Código / SKU</th>
                <th>Descrição</th>
                <th class="num">Qtd</th>
                <th class="num">V. unitário</th>
                <th class="num">V. total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($nf['itens'] as $item): ?>
            <tr>
                <td><?= $e($item['sku']) ?></td>
                <td><?= $e($item['descricao']) ?></td>
                <td class="num"><?= (int) $item['quantidade'] ?></td>
                <td class="num"><?= $r($item['unitario']) ?></td>
                <td class="num"><?= $r($item['unitario'] * $item['quantidade']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="nf-total">
        <span>VALOR TOTAL DA NOTA</span>
        <strong><?= $r($nf['total']) ?></strong>
    </div>

    <?php if (!empty($nf['chave'])): ?>
    <section class="nf-bloco">
        <h3>Chave de acesso</h3>
        <p class="nf-chave"><?= $e(trim(chunk_split($nf['chave'], 4, ' '))) ?></p>
    </section>
    <?php endif; ?>

    <p class="nf-rodape"><?= $e($nf['rodape']) ?></p>
</div>

<div class="nf-botoes no-print">
    <button type="button" onclick="window.print()">Imprimir nota</button>
    <a href="<?= $e($nf['voltar']) ?>">Voltar</a>
</div>

</body>
</html>
