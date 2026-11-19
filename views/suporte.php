<?php
require __DIR__ . '/_icones.php';
require __DIR__ . '/_suporte.php';
$h = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Suporte - Bolo Mania</title>
<link rel="icon" type="image/png" href="/bolomania/public/imagem/logo.png?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/style.css?v=3">
<link rel="stylesheet" href="/bolomania/public/assets/css/painel.css?v=7">
</head>
<body class="bm">

<?php $menuPainel = true; require __DIR__ . '/_topo_painel.php'; ?>

<main class="bm-main">

    <section class="bm-hero bm-hero--compacta" id="hero">
        <div>
            <span class="bm-hero__data">✦ Central de atendimento</span>
            <h1>Suporte <span>Bolo Mania</span></h1>
            <p>Precisa de ajuda com o sistema ou com a loja? Fale com o suporte pelo <b>WhatsApp</b> ou por <b>e-mail</b>. Atendimento feito por <b><?= $h($suporte['responsavel']) ?></b>.</p>
        </div>
        <a class="bm-botao bm-botao--amarelo bm-botao--grande" href="/bolomania/index.php?controller=loja&action=contato" target="_blank"><?= $icone('olho') ?> Ver suporte na loja</a>
    </section>

    <div class="bm-suporte">
        <article class="bm-suporte__cartao bm-suporte__cartao--zap">
            <span class="bm-suporte__icone"><?= $suporteIcone['whatsapp'] ?></span>
            <h2>WhatsApp</h2>
            <p class="bm-suporte__valor"><?= $h($suporte['whatsapp']) ?></p>
            <p class="bm-suporte__dica">Resposta mais rápida</p>
            <a class="bm-suporte__botao" href="<?= $h($suporte['zapLink']) ?>" target="_blank" rel="noopener">Chamar no WhatsApp</a>
        </article>

        <article class="bm-suporte__cartao bm-suporte__cartao--email">
            <span class="bm-suporte__icone"><?= $suporteIcone['email'] ?></span>
            <h2>E-mail</h2>
            <p class="bm-suporte__valor bm-suporte__valor--email"><?= $h($suporte['email']) ?></p>
            <p class="bm-suporte__dica">Resposta em até 1 dia útil</p>
            <a class="bm-suporte__botao" href="mailto:<?= $h($suporte['email']) ?>?subject=<?= rawurlencode('Suporte Bolo Mania') ?>">Enviar e-mail</a>
        </article>

        <article class="bm-suporte__cartao bm-suporte__cartao--horario">
            <span class="bm-suporte__icone"><?= $suporteIcone['horario'] ?></span>
            <h2>Horário</h2>
            <p class="bm-suporte__valor"><?= $h($suporte['horario']) ?></p>
            <p class="bm-suporte__dica">Fora do horário, a resposta vem no dia seguinte</p>
        </article>
    </div>

    <p class="bm-rodape">Bolo Mania · Sistema de gestão da confeitaria</p>
</main>

<script src="/bolomania/public/assets/js/painel.js?v=1"></script>
</body>
</html>
