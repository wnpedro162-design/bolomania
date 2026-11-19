<?php
/**
 * Tela "Suporte" do painel: mostra os canais de atendimento (WhatsApp e e-mail).
 * index.php?controller=suporte&action=index
 */
class SuporteController
{
    public function index(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=form');
            exit;
        }
        require __DIR__ . '/../views/suporte.php';
    }
}
