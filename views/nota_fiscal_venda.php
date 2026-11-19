<?php
// Variáveis vindas do VendaController::verNota(): $nota e $venda (com $venda['itens'])
$nf = [
    'titulo'              => 'Nota Fiscal de Venda',
    'numero'              => $nota['numero'],
    'serie'               => $nota['serie'],
    'modelo'              => $nota['modelo'] . ' (NFC-e)',
    'data'                => date('d/m/Y', strtotime($nota['data_emissao'])),
    'hora'                => '',
    'referencia'          => 'Venda #' . $venda['id'] . ' de ' . date('d/m/Y', strtotime($venda['data'])),
    'voltar'              => 'index.php?controller=venda&action=index',
    'destinatario_titulo' => 'Destinatário / Vendedor',
    'destinatario'        => [
        'Cliente: ' . ($venda['cliente_nome'] ?? 'Consumidor não identificado')
            . (!empty($venda['cliente_doc']) ? ' — CPF/CNPJ ' . $venda['cliente_doc'] : ''),
        'Atendido por: ' . $venda['vendedor_nome'],
    ],
    'itens'               => array_map(fn($i) => [
        'sku'        => $i['sku'],
        'descricao'  => $i['produto_nome'] . ' (' . $i['tamanho'] . ' / ' . $i['cor'] . ')',
        'quantidade' => $i['quantidade'],
        'unitario'   => $i['preco_unitario'],
    ], $venda['itens']),
    'total'               => $nota['valor_total'],
    'chave'               => '',
    'rodape'              => 'Documento de demonstração gerado pelo sistema Bolo Mania — sem valor fiscal.',
];
require __DIR__ . '/_nota_fiscal.php';
