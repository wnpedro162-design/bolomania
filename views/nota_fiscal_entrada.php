<?php
// Variáveis vindas do EntradaController::verNota(): $nota e $entrada (com $entrada['itens'])
$nf = [
    'titulo'              => 'Nota Fiscal de Entrada',
    'numero'              => $nota['numero'],
    'serie'               => $nota['serie'],
    'modelo'              => $nota['modelo'] . ' (NF-e)',
    'data'                => date('d/m/Y', strtotime($nota['data_emissao'])),
    'hora'                => '',
    'referencia'          => 'Entrada #' . $entrada['id'],
    'voltar'              => 'index.php?controller=entrada&action=index',
    'destinatario_titulo' => 'Fornecedor (emitente da nota)',
    'destinatario'        => array_filter([
        $entrada['fornecedor_nome'] . ' — CNPJ ' . $entrada['fornecedor_cnpj'],
        $entrada['fornecedor_endereco'] ?? '',
    ]),
    'itens'               => array_map(fn($i) => [
        'sku'        => $i['sku'],
        'descricao'  => $i['produto_nome'] . ' (' . $i['tamanho'] . ' / ' . $i['cor'] . ')',
        'quantidade' => $i['quantidade'],
        'unitario'   => $i['custo_unitario'],
    ], $entrada['itens']),
    'total'               => $nota['valor_total'],
    'chave'               => $nota['chave_acesso'],
    'rodape'              => 'Registro da nota recebida do fornecedor — controle interno do Bolo Mania.',
];
require __DIR__ . '/_nota_fiscal.php';
