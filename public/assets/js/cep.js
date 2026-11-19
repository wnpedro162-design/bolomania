/* ==========================================================
   Bolo Mania — busca de endereço pelo CEP (manual 24.6)
   Uso: <input id="campo-cep" ...> + <textarea name="endereco">
        + <small id="status-cep"></small>
   O navegador fala só com o NOSSO back-end (CepController),
   que por sua vez consulta o ViaCEP.
   ========================================================== */
(function () {
    var campoCep = document.getElementById('campo-cep');
    if (!campoCep) return;

    var status = document.getElementById('status-cep');
    var campoEndereco = document.querySelector('textarea[name="endereco"]');

    // máscara 00000-000 enquanto digita
    campoCep.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 8).replace(/^(\d{5})(\d)/, '$1-$2');
    });

    campoCep.addEventListener('blur', function () {
        var cepDigitado = this.value.replace(/\D/g, ''); // remove tudo que não é número

        if (cepDigitado.length !== 8) {
            status.textContent = '';
            return;
        }

        status.textContent = 'Buscando endereço...';

        fetch('index.php?controller=cep&action=buscar&cep=' + cepDigitado)
            .then(function (resposta) { return resposta.json(); })
            .then(function (dados) {
                if (dados.erro) {
                    status.textContent = 'Erro: ' + dados.erro;
                    return;
                }
                campoEndereco.value =
                    dados.logradouro + ', ' +
                    dados.bairro + ' - ' +
                    dados.cidade + '/' + dados.estado;
                status.textContent = 'Endereço preenchido automaticamente. Complete o número.';
                campoEndereco.focus();
            })
            .catch(function () {
                status.textContent = 'Não foi possível consultar o CEP agora.';
            });
    });
})();
