<?php

require_once __DIR__ . '/../models/entrada.php';
require_once __DIR__ . '/../models/NotaFiscal.php';

class EntradaController
{
    private function verificarLogin(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

    private function voltar(string $msg, bool $erro = false): void
    {
        $_SESSION['aviso_admin'] = ['msg' => $msg, 'erro' => $erro];
        header("Location: index.php?controller=entrada&action=index");
        exit;
    }

    public function index(): void
    {
        $this->verificarLogin();

        $model = new Entrada();

        $entradas = $model->listar();
        $fornecedores = $model->listarFornecedores();
        $variacoes = $model->listarVariacoes();

        $aviso = $_SESSION['aviso_admin'] ?? null;
        unset($_SESSION['aviso_admin']);

        require_once __DIR__ . '/../views/entradas.php';
    }

    public function salvar(): void
    {
        $this->verificarLogin();

        $fornecedorId = (int) ($_POST['fornecedor_id'] ?? 0);

        $variacoes = $_POST['variacao_id'] ?? [];
        $quantidades = $_POST['quantidade'] ?? [];
        $custos = $_POST['custo_unitario'] ?? [];

        if ($fornecedorId <= 0) {
            $this->voltar("Selecione um fornecedor.", true);
        }

        $itens = [];

        foreach ((array) $variacoes as $i => $variacaoId) {

            $variacaoId = (int) $variacaoId;
            $quantidade = (int) ($quantidades[$i] ?? 0);
            $custo = (float) str_replace(',', '.', $custos[$i] ?? '0');

            if ($variacaoId > 0 && $quantidade > 0 && $custo >= 0) {
                $itens[] = [
                    'variacao_id' => $variacaoId,
                    'quantidade' => $quantidade,
                    'custo_unitario' => $custo
                ];
            }
        }

        if (empty($itens)) {
            $this->voltar("Adicione pelo menos um bolo com quantidade válida.", true);
        }

        // Nota fiscal do fornecedor (opcional) — validada ANTES de gravar a entrada
        $nfNumero = trim($_POST['nf_numero'] ?? '');
        $nfSerie  = trim($_POST['nf_serie'] ?? '') ?: '1';
        $nfChave  = preg_replace('/\D/', '', $_POST['nf_chave'] ?? '');
        $nfData   = trim($_POST['nf_data'] ?? '');
        $temNota  = $nfNumero !== '' || $nfChave !== '';

        if ($temNota && ($nfNumero === '' || strlen($nfChave) !== 44)) {
            $this->voltar("Para registrar a nota do fornecedor, informe o número e a chave de acesso com 44 dígitos.", true);
        }

        $model = new Entrada();

        try {
            $entradaId = $model->registrar($fornecedorId, $itens);
        } catch (Throwable $e) {
            $this->voltar("Erro ao registrar entrada: " . $e->getMessage(), true);
        }

        if ($temNota) {
            try {
                (new NotaFiscal())->registrarParaEntrada($entradaId, '55', $nfSerie, $nfNumero, $nfChave, $nfData ?: null);
            } catch (Throwable $e) {
                $this->voltar("Entrada #{$entradaId} registrada e estoque atualizado, mas a nota não foi salva: " . $e->getMessage(), true);
            }
        }

        $this->voltar("Entrada #{$entradaId} registrada. Estoque atualizado!");
    }

    /** Tela de impressão da NF de entrada (desafio do manual 25.5). */
    public function verNota(): void
    {
        $this->verificarLogin();

        $entradaId = (int) ($_GET['id'] ?? 0);
        $nota = (new NotaFiscal())->buscarPorEntrada($entradaId);
        $entrada = (new Entrada())->buscarPorId($entradaId);

        if (!$nota || !$entrada) {
            $this->voltar("Esta entrada não tem nota fiscal registrada.", true);
        }

        require __DIR__ . '/../views/nota_fiscal_entrada.php';
    }
}
