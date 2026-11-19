<?php
require_once __DIR__ . '/../models/usuario.php';


class UsuarioController
{
    /**
     * Cadastro de usuários do sistema fechado para o público:
     * só um administrador logado pode criar contas.
     */
    private function somenteAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

    public function create()
    {
        $this->somenteAdmin();
       require_once __DIR__ . '/../views/cadastro.php';
    }
    public function store()
    {
        $this->somenteAdmin();
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha_plana = $_POST['senha'] ?? '';


        if (empty($nome) || empty($email) || empty($senha_plana)) {
            die("Por favor, preencha todos os campos.");
        }

        $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);


        $usuarioModel = new Usuario();
       

        $sucesso = $usuarioModel->cadastrar($nome, $email, $senha_hash, 'vendedor', 1);


        if ($sucesso) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        } else {
            die("Erro ao cadastrar o vendedor no banco de dados.");
        }
    }
}


