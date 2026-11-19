<?php
require_once __DIR__ . '/../models/cliente.php';

/**
 * CRUD de Clientes do balcão (manual 24.8) — somente admin.
 * index.php?controller=cliente&action=index|salvar|deletar
 */
class ClienteController
{
    public function index(): void
    {
        $this->check();
        $this->onlyAdmin();

        $model = new Cliente();
        $clientes = $model->listarTodos();

        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $model->buscarPorId((int) $_GET['id']);
        }

        $aviso = $_SESSION['aviso_admin'] ?? null;
        unset($_SESSION['aviso_admin']);

        require_once __DIR__ . '/../views/clientes.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_POST['id'] ?? 0);
        $vazioParaNull = fn($v) => ($v = trim((string) $v)) === '' ? null : $v;

        $dados = [
            'nome'     => trim($_POST['nome'] ?? ''),
            'cpf_cnpj' => trim($_POST['cpf_cnpj'] ?? ''),
            'telefone' => $vazioParaNull($_POST['telefone'] ?? ''),
            'email'    => $vazioParaNull($_POST['email'] ?? ''),
            'endereco' => $vazioParaNull($_POST['endereco'] ?? ''),
            'cep'      => $vazioParaNull($_POST['cep'] ?? ''),
        ];

        $doc = preg_replace('/\D/', '', $dados['cpf_cnpj']);
        if ($dados['nome'] === '' || !in_array(strlen($doc), [11, 14], true)) {
            $this->voltar('Informe o nome e um CPF (11 dígitos) ou CNPJ (14 dígitos).', true, $id);
        }
        if ($dados['email'] !== null && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $this->voltar('E-mail inválido.', true, $id);
        }

        $model = new Cliente();
        if ($id > 0) {
            $model->atualizar($id, $dados);
            $this->voltar('Cliente atualizado.');
        }
        $model->inserir($dados);
        $this->voltar('Cliente cadastrado. Ele já aparece na tela de Vendas.');
    }

    public function deletar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            die("ID inválido.");
        }

        $model = new Cliente();
        if ($model->temVendas($id)) {
            $this->voltar('Este cliente já tem vendas registradas e não pode ser excluído.', true);
        }
        $model->deletar($id);
        $this->voltar('Cliente excluído.');
    }

    private function voltar(string $msg, bool $erro = false, int $id = 0): void
    {
        $_SESSION['aviso_admin'] = ['msg' => $msg, 'erro' => $erro];
        $extra = $id > 0 ? '&id=' . $id : '';
        header("Location: index.php?controller=cliente&action=index" . $extra);
        exit;
    }

    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

    private function onlyAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            die("Acesso negado.");
        }
    }
}
