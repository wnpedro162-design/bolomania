<?php
require_once __DIR__ . '/../models/venda.php';
require_once __DIR__ . '/../models/NotaFiscal.php';

class VendaController
{
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }
        if (!in_array($_SESSION['perfil'] ?? '', ['admin', 'vendedor'], true)) {
            http_response_code(403);
            exit('Acesso negado.');
        }
        $_SESSION['venda_carrinho'] ??= [];
        $_SESSION['venda_token'] ??= bin2hex(random_bytes(32));
    }

    private function voltar(): void
    {
        header('Location: index.php?controller=venda&action=index');
        exit;
    }

    private function validarPost(): void
    {
        $this->check();
        $token = $_POST['token'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($token)
            || !hash_equals($_SESSION['venda_token'], $token)) {
            http_response_code(400);
            exit('Formulário expirado. Volte à tela de vendas e atualize a página.');
        }
    }

    public function index(): void
    {
        $this->check();
        $model = new Venda();
        $bolos = $model->catalogo();
        $clientes = $model->clientes();
        $vendas = $model->historico();
        $porId = array_column($bolos, null, 'id');
        $carrinho = []; $total = 0;
        foreach ($_SESSION['venda_carrinho'] as $id => $qtd) {
            $bolo = $porId[$id] ?? null;
            if (!$bolo) {
                $bolo = ['id' => $id, 'nome' => 'Bolo indisponível',
                    'tamanho' => '', 'sku' => '', 'preco' => '0.00', 'estoque' => 0];
            }
            $bolo['quantidade'] = $qtd;
            $bolo['subtotal'] = Venda::centavos((string)$bolo['preco']) * $qtd;
            $total += $bolo['subtotal'];
            $carrinho[] = $bolo;
        }
        $detalhes = [];
        $vendaSelecionada = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        if ($vendaSelecionada > 0) { $detalhes = $model->detalhes($vendaSelecionada); }
        $mensagem = $_SESSION['venda_mensagem'] ?? '';
        unset($_SESSION['venda_mensagem']);
        $token = $_SESSION['venda_token'];
        require __DIR__ . '/../views/vendas.php';
    }

    public function adicionar(): void
    {
        $this->validarPost();
        $id = filter_var($_POST['variacao_id'] ?? '', FILTER_VALIDATE_INT);
        $qtd = filter_var($_POST['quantidade'] ?? '', FILTER_VALIDATE_INT);
        $bolos = array_column((new Venda())->catalogo(), null, 'id');
        $novo = ($_SESSION['venda_carrinho'][$id ?: 0] ?? 0) + ($qtd ?: 0);
        if (!$id || !$qtd || $qtd < 1 || $novo > 10000 || !isset($bolos[$id])) {
            $_SESSION['venda_mensagem'] = 'Selecione um bolo e uma quantidade válida.';
        } elseif ($novo > (int)$bolos[$id]['estoque']) {
            $_SESSION['venda_mensagem'] = 'A quantidade solicitada ultrapassa o estoque disponível.';
        } else {
            $_SESSION['venda_carrinho'][$id] = $novo;
        }
        $this->voltar();
    }

    public function remover(): void
    {
        $this->validarPost();
        $id = filter_var($_POST['variacao_id'] ?? '', FILTER_VALIDATE_INT);
        if ($id) { unset($_SESSION['venda_carrinho'][$id]); }
        $this->voltar();
    }

    public function limpar(): void
    {
        $this->validarPost();
        $_SESSION['venda_carrinho'] = [];
        $this->voltar();
    }

    public function finalizar(): void
    {
        $this->validarPost();
        $cliente = $_POST['cliente_id'] ?? '';
        $clienteId = $cliente === '' ? null : filter_var($cliente, FILTER_VALIDATE_INT);
        if ($clienteId === false || ($clienteId !== null && $clienteId <= 0)) {
            $_SESSION['venda_mensagem'] = 'Cliente inválido.';
            $this->voltar();
        }
        try {
            $id = (new Venda())->finalizar((int)$_SESSION['usuario_id'],
                $clienteId, $_SESSION['venda_carrinho']);
            $_SESSION['venda_carrinho'] = [];
            $_SESSION['venda_token'] = bin2hex(random_bytes(32));
            $_SESSION['venda_mensagem'] = 'Venda #' . $id . ' finalizada. Estoque atualizado!';
        } catch (PDOException $e) {
            error_log((string)$e);
            $_SESSION['venda_mensagem'] = 'Não foi possível gravar a venda. Confira as tabelas do banco; nenhum item desta venda foi salvo.';
        } catch (RuntimeException $e) {
            $_SESSION['venda_mensagem'] = $e->getMessage();
        }
        $this->voltar();
    }

    /** Emite a nota fiscal de uma venda finalizada (manual 25.4). POST: venda_id + token */
    public function emitirNota(): void
    {
        $this->validarPost();
        $vendaId = filter_var($_POST['venda_id'] ?? '', FILTER_VALIDATE_INT);
        if (!$vendaId) {
            $_SESSION['venda_mensagem'] = 'Venda inválida.';
            $this->voltar();
        }
        try {
            (new NotaFiscal())->emitirParaVenda($vendaId);
            $nota = (new NotaFiscal())->buscarPorVenda($vendaId);
            $_SESSION['venda_mensagem'] = 'Nota fiscal nº ' . $nota['numero'] . ' emitida para a venda #' . $vendaId . '.';
        } catch (Throwable $e) {
            $_SESSION['venda_mensagem'] = 'Não foi possível emitir a nota: ' . $e->getMessage();
        }
        $this->voltar();
    }

    /** Tela de impressão da NF de venda. GET: venda_id */
    public function verNota(): void
    {
        $this->check();
        $vendaId = filter_var($_GET['venda_id'] ?? '', FILTER_VALIDATE_INT) ?: 0;
        $nota = (new NotaFiscal())->buscarPorVenda($vendaId);
        $venda = (new Venda())->buscarPorId($vendaId);
        if (!$nota || !$venda) {
            $_SESSION['venda_mensagem'] = 'Esta venda ainda não tem nota fiscal. Clique em "Emitir NF" primeiro.';
            $this->voltar();
        }
        require __DIR__ . '/../views/nota_fiscal_venda.php';
    }
}
