/* Bolo Mania — efeitos do painel (sem bibliotecas) */
(function () {
    // Confirmação antes de ações importantes: <a data-confirmar="texto">
    document.querySelectorAll('[data-confirmar]').forEach(function (el) {
        el.addEventListener('click', function (ev) {
            if (!confirm(el.dataset.confirmar)) ev.preventDefault();
        });
    });

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    // Números que sobem de 0 até o valor: <b data-contar="12">12</b>
    document.querySelectorAll('[data-contar]').forEach(function (el) {
        var alvo = parseInt(el.dataset.contar, 10) || 0, inicio = null;
        function passo(t) {
            if (!inicio) inicio = t;
            var p = Math.min((t - inicio) / 850, 1);
            el.textContent = Math.round(alvo * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(passo);
        }
        requestAnimationFrame(passo);
    });

    // Valores em reais que sobem: <strong data-dinheiro="209.60">
    var fmt = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    document.querySelectorAll('[data-dinheiro]').forEach(function (el) {
        var alvo = parseFloat(el.dataset.dinheiro) || 0, inicio = null;
        function passo(t) {
            if (!inicio) inicio = t;
            var p = Math.min((t - inicio) / 1100, 1);
            el.textContent = fmt.format(alvo * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(passo);
        }
        requestAnimationFrame(passo);
    });

    // Estrelinhas douradas no banner (id="hero")
    var hero = document.getElementById('hero');
    if (hero) {
        for (var i = 0; i < 10; i++) {
            var s = document.createElement('span');
            s.className = 'bm-estrela';
            s.textContent = '✦';
            s.style.left = (Math.random() * 100) + '%';
            s.style.top = (Math.random() * 100) + '%';
            s.style.fontSize = (8 + Math.random() * 10) + 'px';
            s.style.animationDelay = (Math.random() * 3.6) + 's';
            hero.appendChild(s);
        }
    }
})();
