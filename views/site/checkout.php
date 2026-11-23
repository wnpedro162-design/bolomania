<h1 class="titulo-pagina">Finalizar compra</h1>

<div class="carrinho">
  <form class="formulario checkout__form" action="<?= e(url('pedido', 'pagamento')) ?>" method="post" novalidate>
    <?= csrfCampo() ?>
    <h2>Endereço de entrega</h2>

    <?php if ($erro): ?><div class="aviso aviso--erro" role="alert"><?= e($erro) ?></div><?php endif; ?>

    <?php $endCad = Endereco::formatar($cliente); if ($endCad !== ''): ?>
    <div class="opcoes-endereco">
      <label class="opcoes-endereco__item"><input type="radio" name="endereco_opcao" value="cadastrado" checked> Usar endereço cadastrado</label>
      <label class="opcoes-endereco__item"><input type="radio" name="endereco_opcao" value="novo"<?= ($old['endereco_opcao'] ?? '') === 'novo' ? ' checked' : '' ?>> Usar outro endereço para este pedido</label>
    </div>
    <p id="endereco-cadastrado-resumo" class="checkout__cliente"><?= e($endCad) ?></p>
    <?php endif; ?>

    <div id="bloco-endereco-novo">
      <p class="formulario__intro">Informe o CEP para preencher o endereço automaticamente (ViaCEP).</p>

      <?php $endOld = $old; $endErros = $erros ?? []; require __DIR__ . '/_endereco.php'; ?>
    </div>

    <p class="checkout__cliente">Pedido em nome de <strong><?= e($cliente['nome']) ?></strong> (CPF <?= e(formatarCpf($cliente['cpf'])) ?>).</p>

    <button type="submit" class="botao botao--primario botao--cheio">Ir para pagamento</button>
  </form>

  <aside class="resumo" aria-label="Itens do pedido">
    <h2>Seu pedido</h2>
    <ul class="resumo__itens">
      <?php foreach ($carrinho->itens() as $item): ?>
        <li>
          <span><?= (int) $item['quantidade'] ?> × <?= e($item['produto_nome']) ?>
            <small><?= e($item['tamanho']) ?>, <?= e($item['cor']) ?></small></span>
          <span><?= e(moeda($carrinho->subtotal($item))) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <dl>
      <div class="resumo__total"><dt>Total</dt><dd><?= e(moeda($carrinho->total())) ?></dd></div>
    </dl>
    <a class="link" href="<?= e(url('carrinho', 'index')) ?>">Voltar ao carrinho</a>
  </aside>
</div>

<script>
(function () {
  var radios = document.querySelectorAll('input[name="endereco_opcao"]');
  var blocoNovo = document.getElementById('bloco-endereco-novo');
  var resumo = document.getElementById('endereco-cadastrado-resumo');

  function atualizar() {
    var opcao = document.querySelector('input[name="endereco_opcao"]:checked');
    var usarNovo = !opcao || opcao.value === 'novo';
    blocoNovo.style.display = usarNovo ? '' : 'none';
    if (resumo) resumo.style.display = usarNovo ? 'none' : '';
  }
  if (radios.length) {
    radios.forEach(function (r) { r.addEventListener('change', atualizar); });
    atualizar();
  }

})();
</script>