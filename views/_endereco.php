<?php
/**
 * Campos de endereço separados (loja). Antes de incluir, defina:
 *   $endOld   = valores atuais (cep, logradouro, numero, complemento, bairro, cidade, uf)
 *   $endErros = erros por campo
 * A busca pelo CEP preenche logradouro, bairro, cidade e UF.
 */
$ev = static fn(string $c): string => e((string) ($endOld[$c] ?? ''));
$ee = static fn(string $c): string => isset($endErros[$c]) ? '<span class="campo__erro" id="erro-' . $c . '">' . e($endErros[$c]) . '</span>' : '';
$ed = static fn(string $c): string => isset($endErros[$c]) ? ' aria-invalid="true" aria-describedby="erro-' . $c . '"' : '';
?>
<fieldset class="endereco-campos">
  <legend>Endereço</legend>
  <div class="campo">
    <label for="cep">CEP</label>
    <input id="cep" type="text" name="cep" value="<?= $ev('cep') ?>" inputmode="numeric" maxlength="9" placeholder="00000-000" autocomplete="postal-code" required<?= $ed('cep') ?>>
    <span class="campo__ajuda" id="cep-status">Digite o CEP: rua, bairro, cidade e UF são preenchidos sozinhos.</span>
    <?= $ee('cep') ?>
  </div>
  <div class="linha-campos">
    <div class="campo linha-campos__grande">
      <label for="logradouro">Rua / avenida</label>
      <input id="logradouro" type="text" name="logradouro" value="<?= $ev('logradouro') ?>" maxlength="150" autocomplete="address-line1" required<?= $ed('logradouro') ?>>
      <?= $ee('logradouro') ?>
    </div>
    <div class="campo">
      <label for="numero">Número</label>
      <input id="numero" type="text" name="numero" value="<?= $ev('numero') ?>" maxlength="10" placeholder="ou s/n" required<?= $ed('numero') ?>>
      <?= $ee('numero') ?>
    </div>
  </div>
  <div class="linha-campos">
    <div class="campo">
      <label for="complemento">Complemento <span class="campo__opcional">(opcional)</span></label>
      <input id="complemento" type="text" name="complemento" value="<?= $ev('complemento') ?>" maxlength="60" placeholder="apto, bloco…" autocomplete="address-line2"<?= $ed('complemento') ?>>
      <?= $ee('complemento') ?>
    </div>
    <div class="campo">
      <label for="bairro">Bairro</label>
      <input id="bairro" type="text" name="bairro" value="<?= $ev('bairro') ?>" maxlength="80" required<?= $ed('bairro') ?>>
      <?= $ee('bairro') ?>
    </div>
  </div>
  <div class="linha-campos">
    <div class="campo linha-campos__grande">
      <label for="cidade">Cidade</label>
      <input id="cidade" type="text" name="cidade" value="<?= $ev('cidade') ?>" maxlength="80" autocomplete="address-level2" required<?= $ed('cidade') ?>>
      <?= $ee('cidade') ?>
    </div>
    <div class="campo">
      <label for="uf">UF</label>
      <input id="uf" type="text" name="uf" value="<?= $ev('uf') ?>" maxlength="2" placeholder="RJ" style="text-transform:uppercase" autocomplete="address-level1" required<?= $ed('uf') ?>>
      <?= $ee('uf') ?>
    </div>
  </div>
</fieldset>
<script>
(function () {
  var cep = document.getElementById('cep'), status = document.getElementById('cep-status');
  if (!cep || cep.dataset.ligado) return; cep.dataset.ligado = '1';
  var f = function (id) { return document.getElementById(id); };
  cep.addEventListener('input', function () {
    cep.value = cep.value.replace(/\D/g, '').slice(0, 8).replace(/^(\d{5})(\d)/, '$1-$2');
  });
  cep.addEventListener('blur', function () {
    var d = cep.value.replace(/\D/g, '');
    if (d.length !== 8) return;
    status.textContent = 'Buscando endereço...';
    fetch('index.php?controller=cep&action=buscar&cep=' + d)
      .then(function (r) { return r.json(); })
      .then(function (x) {
        if (x.erro) { status.textContent = x.erro + ' Preencha manualmente.'; return; }
        if (x.logradouro) f('logradouro').value = x.logradouro;
        if (x.bairro) f('bairro').value = x.bairro;
        f('cidade').value = x.cidade || ''; f('uf').value = x.estado || '';
        status.textContent = 'Endereço preenchido. Complete o número.';
        f('numero').focus();
      })
      .catch(function () { status.textContent = 'Não foi possível consultar o CEP agora. Preencha manualmente.'; });
  });
})();
</script>
