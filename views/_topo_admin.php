<?php
/**
 * Topo padrão das telas administrativas (mesma faixa marrom da tela de Produtos).
 * Antes de incluir, defina $tituloPagina (ex.: 'Fornecedores').
 * O menu respeita o perfil: Categorias, Fornecedores e Clientes só aparecem para admin (manual 22.3).
 */
$tituloPagina = $tituloPagina ?? '';
$ehAdmin = ($_SESSION['perfil'] ?? '') === 'admin';
$atual = $_GET['controller'] ?? '';
$menu = [
    'produto'    => 'Produtos',
    'categoria'  => 'Categorias',
    'fornecedor' => 'Fornecedores',
    'cliente'    => 'Clientes',
    'entrada'    => 'Entradas',
    'venda'      => 'Vendas',
    'relatorio'  => 'Relatórios',
];
$soAdmin = ['categoria', 'fornecedor', 'cliente'];
?>
<div class="header">
    <div class="container header-inner">
        <div>
            <a href="index.php?controller=auth&action=dashboard" style="text-decoration:none;"><strong>Bolo Mania</strong></a>
            <?php if ($tituloPagina !== ''): ?>
                <span class="badge"><?= htmlspecialchars($tituloPagina) ?></span>
            <?php endif; ?>
        </div>

        <nav class="admin-menu" aria-label="Módulos do sistema">
            <?php foreach ($menu as $ctrl => $rotulo): ?>
                <?php if (in_array($ctrl, $soAdmin, true) && !$ehAdmin) continue; ?>
                <a href="index.php?controller=<?= $ctrl ?>&action=index"
                   class="<?= $atual === $ctrl ? 'ativo' : '' ?>"><?= $rotulo ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="user">
            Olá, <strong><?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?></strong>
            <a class="btn btn-ghost" href="index.php?controller=auth&action=dashboard">Painel</a>
            <a class="btn btn-ghost" href="index.php?controller=auth&action=logout">Sair</a>
        </div>
    </div>
</div>
