/* ==========================================================
   Bolo Mania — busca de endereço pelo CEP (manual 24.6)
   Uso: <input id="campo-cep"> + campos #end-logradouro, #end-bairro,
        #end-cidade, #end-uf, #end-numero + <small id="status-cep">
   O navegador fala só com o NOSSO back-end (CepController),
   que por sua vez consulta o ViaCEP.
   ========================================================== */
(function () {
    var campoCep = document.getElementById('campo-cep');
    if (!campoCep) return;

    var status = document.getElementById('status-cep');
    var campo = function (id) { return document.getElementById('end-' + id); };

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
                    status.textContent = 'Erro: ' + dados.erro + ' Preencha o endereço manualmente.';
                    return;
                }
                if (dados.logradouro) campo('logradouro').value = dados.logradouro;
                if (dados.bairro) campo('bairro').value = dados.bairro;
                campo('cidade').value = dados.cidade || '';
                campo('uf').value = dados.estado || '';
                status.textContent = 'Endereço preenchido automaticamente. Complete o número.';
                campo('numero').focus();
            })
            .catch(function () {
                status.textContent = 'Não foi possível consultar o CEP agora. Preencha manualmente.';
            });
    });
})();
