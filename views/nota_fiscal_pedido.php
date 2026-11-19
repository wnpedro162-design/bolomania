<?php
// Variáveis vindas do PedidoController::verNota(): $pedido (com itens) e $cliente
$nf = [
    'titulo'              => 'Nota Fiscal do Pedido',
    'numero'              => $pedido['id'],
    'serie'               => '2',
    'modelo'              => '55 (NF-e)',
    'data'                => date('d/m/Y', strtotime($pedido['data'])),
    'hora'                => date('H:i', strtotime($pedido['data'])),
    'referencia'          => 'Pedido #' . $pedido['id'] . ' (loja virtual)',
    'voltar'              => 'index.php?controller=pedido&action=meusPedidos',
    'destinatario_titulo' => 'Destinatário',
    'destinatario'        => [
        $cliente['nome'] . ' — CPF ' . formatarCpf($cliente['cpf']),
        'Entrega: ' . $pedido['endereco_entrega'],
        'Pagamento: ' . ($pedido['forma_pagamento'] === 'cartao' ? 'Cartão de crédito' : 'Pix'),
    ],
    'itens'               => array_map(fn($i) => [
        'sku'        => $i['sku'],
        'descricao'  => $i['produto_nome'] . ' (' . $i['tamanho'] . ' / ' . $i['cor'] . ')',
        'quantidade' => $i['quantidade'],
        'unitario'   => $i['preco_unitario'],
    ], $pedido['itens']),
    'total'               => $pedido['valor_total'],
    'chave'               => '',
    'rodape'              => 'Documento de demonstração gerado pela loja virtual Bolo Mania — sem valor fiscal.',
];
require __DIR__ . '/_nota_fiscal.php';
