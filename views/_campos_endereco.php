<?php
/**
 * Campos de endereço separados (painel). Antes de incluir, defina:
 *   $endVal = ['cep' => ..., 'logradouro' => ..., ...]  (vazio no cadastro novo)
 * O cep.js preenche logradouro, bairro, cidade e UF pelo CEP.
 */
$ev = static fn(string $c): string => htmlspecialchars((string) ($endVal[$c] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<div class="bm-campo">
    <label for="campo-cep">CEP</label>
    <input type="text" id="campo-cep" name="cep" maxlength="9" placeholder="00000-000" inputmode="numeric" value="<?= $ev('cep') ?>">
    <small class="bm-campo__ajuda" id="status-cep">Digite o CEP: rua, bairro, cidade e UF são preenchidos sozinhos.</small>
</div>
<div class="form-row">
    <div class="bm-campo" style="flex:3">
        <label for="end-logradouro">Logradouro (rua / avenida)</label>
        <input type="text" id="end-logradouro" name="logradouro" maxlength="150" value="<?= $ev('logradouro') ?>">
    </div>
    <div class="bm-campo" style="flex:1">
        <label for="end-numero">Número</label>
        <input type="text" id="end-numero" name="numero" maxlength="10" placeholder="ou s/n" value="<?= $ev('numero') ?>">
    </div>
</div>
<div class="form-row">
    <div class="bm-campo">
        <label for="end-complemento">Complemento</label>
        <input type="text" id="end-complemento" name="complemento" maxlength="60" placeholder="apto, bloco… (opcional)" value="<?= $ev('complemento') ?>">
    </div>
    <div class="bm-campo">
        <label for="end-bairro">Bairro</label>
        <input type="text" id="end-bairro" name="bairro" maxlength="80" value="<?= $ev('bairro') ?>">
    </div>
</div>
<div class="form-row">
    <div class="bm-campo" style="flex:3">
        <label for="end-cidade">Cidade</label>
        <input type="text" id="end-cidade" name="cidade" maxlength="80" value="<?= $ev('cidade') ?>">
    </div>
    <div class="bm-campo" style="flex:1">
        <label for="end-uf">UF</label>
        <input type="text" id="end-uf" name="uf" maxlength="2" placeholder="RJ" style="text-transform:uppercase" value="<?= $ev('uf') ?>">
    </div>
</div>
