<?php
/**
 * Ícones em SVG usados nas telas do painel (sem biblioteca externa).
 * Uso: <?= $icone('editar') ?>
 */
$listaIcones = [
    'editar'     => '<path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="m13.5 6.5 4 4"/>',
    'pausar'     => '<rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/>',
    'ativar'     => '<path d="M5 12.5 10 17l9-10"/>',
    'lixo'       => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
    'cadeado'    => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
    'info'       => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.5"/>',
    'mais'       => '<path d="M12 5v14M5 12h14"/>',
    'categoria'  => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
    'fornecedor' => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
    'cliente'    => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c.8-3.2 3-5 5.5-5s4.7 1.8 5.5 5"/><circle cx="17" cy="9" r="2.4"/><path d="M15.8 14.2c2.3.2 4 1.8 4.7 4.8"/>',
    'entrada'    => '<path d="M12 3v11M7.5 9.5 12 14l4.5-4.5"/><path d="M4 15v4h16v-4"/>',
    'venda'      => '<path d="M4 5h2l2.2 10h9.6L20 8H7"/><circle cx="9.5" cy="19" r="1.4"/><circle cx="16.5" cy="19" r="1.4"/>',
    'relatorio'  => '<path d="M5 20V10M10 20V4M15 20v-7M20 20v-11"/>',
    'nota'       => '<path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5M9 13h7M9 17h5"/>',
    'mapa'       => '<path d="M12 21s-6-5.3-6-10a6 6 0 0 1 12 0c0 4.7-6 10-6 10z"/><circle cx="12" cy="11" r="2.2"/>',
    'pdf'        => '<path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5M9 14h6M12 11v6"/>',
    'excel'      => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 10h16M4 15h16M10 4v16"/>',
    'calendario' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/>',
    'alerta'     => '<path d="M12 4 3 20h18L12 4zM12 10v4M12 17v.5"/>',
    'trofeu'     => '<path d="M8 4h8v5a4 4 0 0 1-8 0V4zM8 6H5a3 3 0 0 0 3 4M16 6h3a3 3 0 0 1-3 4M12 13v4M8 20h8"/>',
    'dinheiro'   => '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 9v.5M18 14.5v.5"/>',
    'carrinho'   => '<path d="M4 5h2l2.2 10h9.6L20 8H7"/><circle cx="9.5" cy="19" r="1.4"/><circle cx="16.5" cy="19" r="1.4"/>',
    'olho'       => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
];
$icone = static function (string $nome) use ($listaIcones): string {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . ($listaIcones[$nome] ?? '') . '</svg>';
};
