<?php
require_once __DIR__ . '/../models/variacao.php';
require_once __DIR__ . '/../models/Produto.php';

/**
 * Variações (SKUs) de um bolo: tamanho, sabor, preço e estoque mínimo.
 * Sem variação o bolo não pode receber Entrada, nem ser vendido, nem aparecer na loja.
 * index.php?controller=variacao&action=index&produto_id=X
 */
class VariacaoController
{
    public function index(): void
    {
        $this->check();

        $produtoId = (int) ($_GET['produto_id'] ?? 0);
        $produto = $produtoId > 0 ? (new Produto())->buscarPorId($produtoId) : null;
        if (!$produto) {
            header("Location: index.php?controller=produto&action=index");
            exit;
        }

        $model = new Variacao();
        $variacoes = $model->listarPorProduto($produtoId);

        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $model->buscarPorId((int) $_GET['id']);
            if ($editar && (int) $editar['produto_id'] !== $produtoId) {
                $editar = null;
            }
        }

        $aviso = $_SESSION['aviso_admin'] ?? null;
        unset($_SESSION['aviso_admin']);

        require_once __DIR__ . '/../views/variacoes.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id        = (int) ($_POST['id'] ?? 0);
        $produtoId = (int) ($_POST['produto_id'] ?? 0);
        $tamanho   = trim($_POST['tamanho'] ?? '');
        $sabor     = trim($_POST['sabor'] ?? '');
        $sku       = strtoupper(trim($_POST['sku'] ?? ''));
        $minimo    = max(0, (int) ($_POST['minimo'] ?? 2));

        // aceita "45,90", "45.90" e "R$ 1.045,90"
        $precoTexto = str_replace(['R$', ' '], '', $_POST['preco'] ?? '0');
        if (str_contains($precoTexto, ',')) {
            $precoTexto = str_replace(['.', ','], ['', '.'], $precoTexto);
        }
        $preco = round((float) $precoTexto, 2);

        if ($produtoId <= 0 || !(new Produto())->buscarPorId($produtoId)) {
            die("Produto inválido.");
        }
        if ($tamanho === '' || $sabor === '' || $sku === '' || $preco <= 0
            || mb_strlen($tamanho) > 10 || mb_strlen($sabor) > 30 || mb_strlen($sku) > 50) {
            $this->voltar($produtoId, 'Preencha tamanho (até 10 letras), sabor (até 30), SKU e um preço maior que zero.', true, $id);
        }

        $model = new Variacao();
        try {
            if ($id > 0) {
                $model->atualizar($id, $tamanho, $sabor, $sku, $preco, $minimo);
                $this->voltar($produtoId, 'Variação atualizada.');
            }
            $model->inserir($produtoId, $tamanho, $sabor, $sku, $preco, $minimo);
        } catch (PDOException $e) {
            $this->voltar($produtoId, 'Já existe uma variação com o SKU "' . $sku . '". O SKU precisa ser único.', true, $id);
        }

        $this->voltar($produtoId, 'Variação cadastrada com estoque zerado. Registre uma Entrada para abastecer.');
    }

    public function deletar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $produtoId = (int) ($_GET['produto_id'] ?? 0);
        if ($id <= 0 || $produtoId <= 0) {
            die("Parâmetros inválidos.");
        }

        $model = new Variacao();
        if ($model->temMovimento($id)) {
            $this->voltar($produtoId, 'Esta variação já teve entradas ou vendas e não pode ser excluída (o histórico depende dela).', true);
        }

        try {
            $model->deletar($id);
        } catch (PDOException $e) {
            $this->voltar($produtoId, 'Não foi possível excluir: a variação está ligada a outros registros.', true);
        }
        $this->voltar($produtoId, 'Variação excluída.');
    }

    private function voltar(int $produtoId, string $msg, bool $erro = false, int $id = 0): void
    {
        $_SESSION['aviso_admin'] = ['msg' => $msg, 'erro' => $erro];
        $extra = $id > 0 ? '&id=' . $id : '';
        header("Location: index.php?controller=variacao&action=index&produto_id=" . $produtoId . $extra);
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
