<?php

session_start();

require_once __DIR__ . '/config/db.php';

// Inicializar conexão com o banco
Database::getConnection();

// Identificar controller e ação
// (sem nada na URL abre o login do sistema; a loja abre em ?controller=loja)
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? (
    $controller === 'auth' ? 'form' : 'index'
);

// Só letras e números (evita nomes estranhos na URL)
if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string) $controller)
    || !preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string) $action)) {
    http_response_code(400);
    exit('Requisição inválida.');
}

// Lista de controllers permitidos
switch ($controller) {

    // ==================== SISTEMA (vendedor / admin) ====================

    case 'auth':
        require_once __DIR__ . '/controllers/AuthController.php';
        $c = new AuthController();
        break;

    case 'categoria':
        require_once __DIR__ . '/controllers/CategoriaController.php';
        $c = new CategoriaController();
        break;

    case 'produto':
        require_once __DIR__ . '/controllers/ProdutoController.php';
        $c = new ProdutoController();
        break;

    case 'variacao':
        require_once __DIR__ . '/controllers/VariacaoController.php';
        $c = new VariacaoController();
        break;

    case 'fornecedor':
        require_once __DIR__ . '/controllers/FornecedorController.php';
        $c = new FornecedorController();
        break;

    case 'cliente':
        require_once __DIR__ . '/controllers/ClienteController.php';
        $c = new ClienteController();
        break;

    case 'entrada':
        require_once __DIR__ . '/controllers/EntradaController.php';
        $c = new EntradaController();
        break;

    case 'venda':
        require_once __DIR__ . '/controllers/VendaController.php';
        $c = new VendaController();
        break;

    case 'relatorio':
        require_once __DIR__ . '/controllers/RelatorioController.php';
        $c = new RelatorioController();
        break;

    case 'usuario':
        require_once __DIR__ . '/controllers/usuariocontroller.php';
        $c = new UsuarioController();
        break;

    case 'suporte':
        require_once __DIR__ . '/controllers/SuporteController.php';
        $c = new SuporteController();
        break;

    case 'cep':
        require_once __DIR__ . '/controllers/CepController.php';
        $c = new CepController();
        break;

    // ==================== LOJA VIRTUAL (cliente) ====================

    case 'loja':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/LojaController.php';
        $c = new LojaController();
        break;

    case 'carrinho':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/CarrinhoController.php';
        $c = new CarrinhoController();
        break;

    case 'conta':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/ContaController.php';
        $c = new ContaController();
        break;

    case 'pedido':
        require_once __DIR__ . '/config/helpers.php';
        require_once __DIR__ . '/controllers/PedidoController.php';
        $c = new PedidoController();
        break;

    default:
        http_response_code(404);
        exit('Controller não encontrado.');
}

// Verificar se a ação existe e pode ser executada
// (métodos que começam com "__", como __construct, não podem ser chamados pela URL)
if (strpos($action, '__') === 0 || !is_callable([$c, $action])) {
    http_response_code(404);
    exit('Ação não encontrada.');
}

// Executar ação solicitada
$c->$action();
