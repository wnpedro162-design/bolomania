<?php
/**
 * Topo das telas no estilo do painel (marrom com detalhe dourado).
 * Opcional: defina $menuPainel = true antes de incluir para mostrar
 * as abas dos módulos logo abaixo (usado nas telas internas, ex.: Produtos).
 */
$tpNome    = $_SESSION['nome'] ?? 'Usuário';
$tpPerfil  = $_SESSION['perfil'] ?? 'vendedor';
$tpAdmin   = $tpPerfil === 'admin';
$tpInicial = mb_strtoupper(mb_substr(trim($tpNome), 0, 1));
$tpAtual   = $abaAtiva ?? ($_GET['controller'] ?? 'auth'); // $abaAtiva força a aba marcada
$tpMenu    = [
    'auth'       => ['Painel', false],
    'produto'    => ['Produtos', false],
    'categoria'  => ['Categorias', true],
    'fornecedor' => ['Fornecedores', true],
    'cliente'    => ['Clientes', true],
    'entrada'    => ['Entradas', false],
    'venda'      => ['Vendas', false],
    'relatorio'  => ['Relatórios', false],
    'suporte'    => ['Suporte', false],
];

$tpE = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<header class="bm-topo">
    <div class="bm-topo__in">
        <a class="bm-marca" href="/bolomania/index.php?controller=auth&action=dashboard">
            <img src="/bolomania/public/imagem/logo.png?v=3" alt="Logo Bolo Mania">
            <span><strong>Bolo Mania</strong><small>Painel de gestão</small></span>
        </a>

        <div class="bm-usuario">
            <div class="bm-avatar"><?= $tpE($tpInicial) ?></div>
            <div class="bm-usuario__texto">
                <b><?= $tpE($tpNome) ?></b>
                <span class="bm-pill"><?= $tpE($tpPerfil) ?></span>
            </div>
            <a class="bm-botao bm-botao--linha" href="/bolomania/index.php?controller=loja&action=index" target="_blank">Ver loja</a>
            <a class="bm-botao bm-botao--amarelo" href="/bolomania/index.php?controller=auth&action=logout">Sair</a>
        </div>
    </div>

    <?php if (!empty($menuPainel)): ?>
    <nav class="bm-abas" aria-label="Módulos do sistema">
        <div class="bm-abas__in">
            <?php foreach ($tpMenu as $ctrl => [$rotulo, $soAdmin]): ?>
                <?php if ($soAdmin && !$tpAdmin) continue; ?>
                <a href="/bolomania/index.php?controller=<?= $ctrl ?>&action=<?= $ctrl === 'auth' ? 'dashboard' : 'index' ?>"
                   class="<?= $tpAtual === $ctrl ? 'ativa' : '' ?>"><?= $rotulo ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php endif; ?>
</header>
