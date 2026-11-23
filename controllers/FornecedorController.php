<?php
require_once __DIR__ . '/../models/fornecedor.php';

/**
 * CRUD de Fornecedores (manual 18.4) — somente admin.
 * index.php?controller=fornecedor&action=index|salvar|toggle
 */
class FornecedorController
{
    public function index(): void
    {
        $this->check();
        $this->onlyAdmin();

        $model = new Fornecedor();
        $fornecedores = $model->listarTodos();

        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $model->buscarPorId((int) $_GET['id']);
        }

        $aviso = $_SESSION['aviso_admin'] ?? null;
        unset($_SESSION['aviso_admin']);

        require_once __DIR__ . '/../views/fornecedores.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_POST['id'] ?? 0);
        $vazioParaNull = fn($v) => ($v = trim((string) $v)) === '' ? null : $v;

        $dados = [
            'nome'     => trim($_POST['nome'] ?? ''),
            'cnpj'     => trim($_POST['cnpj'] ?? ''),
            'telefone' => $vazioParaNull($_POST['telefone'] ?? ''),
            'email'    => $vazioParaNull($_POST['email'] ?? ''),
            'endereco' => Endereco::doPost($_POST),
        ];
        $errosEnd = Endereco::validar($dados['endereco'], false);
        if ($errosEnd) {
            $this->voltar('Endereço incompleto: ' . implode(' ', $errosEnd) . ' (ou deixe todos os campos de endereço vazios).', true, $id);
        }

        $cnpjDigitos = preg_replace('/\D/', '', $dados['cnpj']);

        if ($dados['nome'] === '' || strlen($cnpjDigitos) !== 14) {
            $this->voltar('Informe o nome e um CNPJ com 14 dígitos.', true, $id);
        }
        if ($dados['email'] !== null && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $this->voltar('E-mail inválido.', true, $id);
        }

        $model = new Fornecedor();
        if ($id > 0) {
            $model->atualizar($id, $dados);
            $this->voltar('Fornecedor atualizado.');
        }
        $model->inserir($dados);
        $this->voltar('Fornecedor cadastrado.');
    }

    public function toggle(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id    = (int) ($_GET['id'] ?? 0);
        $ativo = (int) ($_GET['ativo'] ?? 1);
        if ($id <= 0) {
            die("ID inválido.");
        }

        (new Fornecedor())->setAtivo($id, $ativo === 1);
        $this->voltar($ativo === 1 ? 'Fornecedor ativado.' : 'Fornecedor inativado.');
    }

    private function voltar(string $msg, bool $erro = false, int $id = 0): void
    {
        $_SESSION['aviso_admin'] = ['msg' => $msg, 'erro' => $erro];
        $extra = $id > 0 ? '&id=' . $id : '';
        header("Location: index.php?controller=fornecedor&action=index" . $extra);
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
