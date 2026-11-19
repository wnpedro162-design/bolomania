<?php
require_once __DIR__ . '/../models/categoria.php';

/**
 * CRUD de Categorias — somente admin.
 * index.php?controller=categoria&action=index|salvar|toggle
 */
class CategoriaController
{
    public function index(): void
    {
        $this->check();
        $this->onlyAdmin();

        $model = new Categoria();
        $categorias = $model->listarTodas();

        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $model->buscarPorId((int) $_GET['id']);
        }

        $aviso = $_SESSION['aviso_admin'] ?? null;
        unset($_SESSION['aviso_admin']);

        require_once __DIR__ . '/../views/categorias.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '' || mb_strlen($nome) > 100) {
            $this->voltar('Informe um nome de até 100 caracteres.', true);
        }

        $model = new Categoria();
        if ($id > 0) {
            $model->atualizar($id, $nome);
            $this->voltar('Categoria atualizada.');
        }
        $model->inserir($nome);
        $this->voltar('Categoria cadastrada.');
    }

    public function toggle(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $ativo = (int) ($_GET['ativo'] ?? 1);
        if ($id <= 0) {
            die("ID inválido.");
        }

        (new Categoria())->setAtivo($id, $ativo === 1);
        $this->voltar($ativo === 1 ? 'Categoria ativada.' : 'Categoria inativada (some da loja virtual).');
    }

    private function voltar(string $msg, bool $erro = false): void
    {
        $_SESSION['aviso_admin'] = ['msg' => $msg, 'erro' => $erro];
        header("Location: index.php?controller=categoria&action=index");
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
