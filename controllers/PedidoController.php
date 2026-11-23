<?php
require_once __DIR__ . '/SiteBaseController.php';
require_once __DIR__ . '/../models/ClienteSite.php';
require_once __DIR__ . '/../models/Pedido.php';

class PedidoController extends SiteBaseController
{
    /** index.php?controller=pedido&action=checkout  (exige login) */
    public function checkout(): void
    {
        $this->exigirLogin();

        $carrinho = new Carrinho();
        foreach ($carrinho->sincronizar() as $aviso) {
            flash('aviso', $aviso);
        }
        if ($carrinho->vazio()) {
            flash('aviso', 'Seu carrinho está vazio.');
            redirecionar(url('carrinho', 'index'));
        }

        $cliente = (new ClienteSite())->buscarPorCpf($this->clienteCpf());

        $this->render('checkout', [
            'carrinho' => $carrinho,
            'cliente'  => $cliente,
            'old'      => [],
            'erro'     => '',
        ], 'Finalizar compra');
    }

    /** POST: recebe o endereço, valida e mostra a escolha de pagamento */
    public function pagamento(): void
    {
        $this->exigirLogin();
        $this->exigirPost(url('pedido', 'checkout'));

        $old = ['endereco_opcao' => $_POST['endereco_opcao'] ?? 'cadastrado'] + Endereco::doPost($_POST);

        $carrinho = new Carrinho();
        $avisos = $carrinho->sincronizar();
        if ($avisos) {
            foreach ($avisos as $a) {
                flash('aviso', $a);
            }
        }
        if ($carrinho->vazio()) {
            redirecionar(url('carrinho', 'index'));
        }

        $cliente = (new ClienteSite())->buscarPorCpf($this->clienteCpf());

        $cadastrado = Endereco::daLinha($cliente ?: []);
        if ($old['endereco_opcao'] === 'cadastrado' && !Endereco::validar($cadastrado, true)) {
            $endereco = $cadastrado;
        } else {
            $endereco = Endereco::daLinha($old);
            $errosEnd = Endereco::validar($endereco, true);
            if ($errosEnd) {
                $this->render('checkout', [
                    'carrinho' => $carrinho,
                    'cliente'  => $cliente,
                    'old'      => $old + ['endereco_opcao' => 'novo'],
                    'erros'    => $errosEnd,
                    'erro'     => 'Confira os campos do endereço de entrega.',
                ], 'Finalizar compra');
                return;
            }
        }

        $_SESSION['checkout_endereco'] = $endereco;

        $this->render('pagamento', [
            'carrinho' => $carrinho,
            'erro'     => '',
        ], 'Forma de pagamento');
    }

    /** POST: confirma a forma de pagamento e cria o pedido */
    public function finalizar(): void
    {
        $this->exigirLogin();
        $this->exigirPost(url('pedido', 'checkout'));

        $endereco = $_SESSION['checkout_endereco'] ?? null;
        $carrinho = new Carrinho();
        if (!is_array($endereco) || Endereco::validar($endereco, true) || $carrinho->vazio()) {
            redirecionar(url('pedido', 'checkout'));
        }

        $metodo = $_POST['metodo'] ?? '';
        if ($metodo === 'cartao') {
            $erroCartao = $this->validarCartao();
            if ($erroCartao !== '') {
                $this->render('pagamento', [
                    'carrinho' => $carrinho,
                    'erro'     => $erroCartao,
                ], 'Forma de pagamento');
                return;
            }
        } elseif ($metodo !== 'pix') {
            redirecionar(url('pedido', 'checkout'));
        }

        try {
            $pedidoId = (new Pedido())->criar($this->clienteCpf(), $endereco, $metodo, $carrinho->itens());
        } catch (RegraNegocioException $ex) {
            flash('erro', $ex->getMessage());
            redirecionar(url('carrinho', 'index'));
        } catch (Throwable $ex) {
            error_log('[bolomania] falha ao criar pedido: ' . $ex->getMessage());
            flash('erro', 'Não foi possível concluir o pedido agora. Seu carrinho foi mantido; tente novamente.');
            redirecionar(url('carrinho', 'index'));
        }

        $carrinho->limpar();
        unset($_SESSION['checkout_endereco']);
        redirecionar(url('pedido', 'confirmacao', ['id' => $pedidoId]));
    }

    /** index.php?controller=pedido&action=confirmacao&id=N */
    public function confirmacao(): void
    {
        $this->exigirLogin();
        $pedido = (new Pedido())->buscarDoCliente((int) ($_GET['id'] ?? 0), $this->clienteCpf());
        if (!$pedido) {
            http_response_code(404);
            $this->render('nao_encontrado', ['mensagem' => 'Pedido não encontrado.'], 'Pedido não encontrado');
            return;
        }
        $this->render('confirmacao', ['pedido' => $pedido], 'Pedido realizado');
    }

    /** index.php?controller=pedido&action=verNota&id=N  (mesma tela da NF-e, com botão Imprimir) */
    public function verNota(): void
    {
        $this->exigirLogin();

        $pedido = (new Pedido())->buscarDoCliente((int) ($_GET['id'] ?? 0), $this->clienteCpf());
        if (!$pedido) {
            http_response_code(404);
            $this->render('nao_encontrado', ['mensagem' => 'Pedido não encontrado.'], 'Pedido não encontrado');
            return;
        }

        $cliente = (new ClienteSite())->buscarPorCpf($this->clienteCpf());

        require __DIR__ . '/../views/nota_fiscal_pedido.php';
    }

    /** index.php?controller=pedido&action=meusPedidos */
    public function meusPedidos(): void
    {
        $this->exigirLogin();
        $pedidos = (new Pedido())->listarDoCliente($this->clienteCpf());
        $this->render('meus_pedidos', ['pedidos' => $pedidos], 'Meus pedidos');
    }

    /**
     * Pagamento SIMULADO (nenhum dado de cartão é guardado no banco).
     * Confere só o formato: número (13 a 19 dígitos + algoritmo de Luhn), validade futura e CVV.
     */
    private function validarCartao(): string
    {
        $numero = somenteDigitos($_POST['numero_cartao'] ?? '');
        $nome = trim((string) ($_POST['nome_cartao'] ?? ''));
        $validade = trim((string) ($_POST['validade_cartao'] ?? ''));
        $cvv = somenteDigitos($_POST['cvv_cartao'] ?? '');

        if ($numero === '' || $nome === '' || $validade === '' || $cvv === '') {
            return 'Preencha todos os dados do cartão.';
        }

        $soma = 0;
        $dobrar = false;
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $d = (int) $numero[$i];
            if ($dobrar) {
                $d *= 2;
                if ($d > 9) {
                    $d -= 9;
                }
            }
            $soma += $d;
            $dobrar = !$dobrar;
        }
        if (strlen($numero) < 13 || strlen($numero) > 19 || $soma % 10 !== 0) {
            return 'Número de cartão inválido.';
        }

        if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $validade, $m)
            || (int) ('20' . $m[2] . $m[1]) < (int) date('Ym')) {
            return 'Validade do cartão inválida ou vencida (use MM/AA).';
        }

        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return 'CVV inválido.';
        }

        return '';
    }
}
