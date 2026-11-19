<?php require __DIR__ . '/../_suporte.php'; ?>
<section class="contato-topo">
  <div>
    <p class="destaque__selo">Suporte</p>
    <h1>Precisa de <span>ajuda</span>?</h1>
    <p>Nosso suporte está disponível para tirar dúvidas sobre o site, sua conta e seus pedidos. Fale com a gente pelo WhatsApp ou por e-mail.</p>
  </div>
</section>

<div class="suporte">
  <article class="suporte__cartao suporte__cartao--zap">
    <span class="suporte__icone"><?= $suporteIcone['whatsapp'] ?></span>
    <h2>WhatsApp</h2>
    <p class="suporte__valor"><?= e($suporte['whatsapp']) ?></p>
    <p class="suporte__dica">Resposta mais rápida</p>
    <a class="suporte__botao" href="<?= e($suporte['zapLink']) ?>" target="_blank" rel="noopener">Chamar no WhatsApp</a>
  </article>

  <article class="suporte__cartao suporte__cartao--email">
    <span class="suporte__icone"><?= $suporteIcone['email'] ?></span>
    <h2>E-mail</h2>
    <p class="suporte__valor suporte__valor--email"><?= e($suporte['email']) ?></p>
    <p class="suporte__dica">Respondemos em até 1 dia útil</p>
    <a class="suporte__botao" href="mailto:<?= e($suporte['email']) ?>?subject=<?= rawurlencode('Suporte Bolo Mania') ?>">Enviar e-mail</a>
  </article>

  <article class="suporte__cartao suporte__cartao--horario">
    <span class="suporte__icone"><?= $suporteIcone['horario'] ?></span>
    <h2>Horário</h2>
    <p class="suporte__valor"><?= e($suporte['horario']) ?></p>
    <p class="suporte__dica">Mensagens fora do horário são respondidas no dia seguinte</p>
  </article>
</div>
