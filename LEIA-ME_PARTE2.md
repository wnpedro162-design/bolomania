# Bolo Mania — Parte 2 (o que foi acrescentado)

Pacote com **somente os arquivos novos ou alterados**. Ele segue a mesma estrutura de pastas do seu projeto (`controllers/`, `models/`, `views/`, `public/`…), então basta copiar por cima.

## Instalação (XAMPP) — 4 passos

1. **Faça um backup** da pasta `C:\xampp\htdocs\bolomania` (copie e cole com outro nome).
2. **Copie** tudo deste pacote para dentro de `C:\xampp\htdocs\bolomania`, aceitando substituir os arquivos.
   O `config/db.php` NÃO vem no pacote: o seu continua igual.
3. No **phpMyAdmin**, selecione o banco `bolomania` → aba **SQL** → cole e execute o conteúdo de
   `sql/atualizacao_parte2.sql`. (Pode rodar mais de uma vez sem problema.)
4. Abra `http://localhost/bolomania/check.php` — deve aparecer **17 tabelas**.

## Primeiro uso (nesta ordem)

1. Entre como **admin** → **Fornecedores** → cadastre um fornecedor (o CEP preenche o endereço sozinho).
2. **Produtos** → no bolo, clique em **Variações** → cadastre tamanho, sabor, SKU e preço.
   *Sem variação o bolo não recebe entrada, não aparece nas Vendas nem na loja.*
3. **Entradas** → registre a compra (pode informar a nota fiscal do fornecedor) → o estoque sobe.
4. **Vendas** → venda no balcão → **Emitir NF** → **Ver NF**.
5. **Relatórios** → **Exportar Excel** / **Exportar PDF**.
6. Loja virtual: `http://localhost/bolomania/index.php?controller=loja&action=index`
   (também tem link no login e no painel). Crie uma conta, coloque um bolo no carrinho e finalize.
   - CPF de teste válido: `529.982.247-25`
   - Cartão de teste: `4111 1111 1111 1111`, validade futura (ex.: `12/30`), CVV `123` — ou escolha Pix.

## O que cada item do manual ganhou

| Manual | O que foi feito | Arquivos principais |
|---|---|---|
| 18 Fornecedores + Estoque | CRUD com inativação; toda variação nasce com estoque zerado | `FornecedorController`, `models/fornecedor.php`, `models/estoque.php`, `views/fornecedores.php` |
| (base p/ 19–20) Variações | Tamanho, sabor, SKU, preço e estoque mínimo por bolo | `VariacaoController`, `models/variacao.php`, `views/variacoes.php` |
| Categorias | O controller estava vazio; agora é CRUD com inativação | `CategoriaController`, `models/categoria.php`, `views/categorias.php` |
| 19 Entradas | Só fornecedores ativos; NF do fornecedor opcional; tela no visual do sistema | `EntradaController`, `models/entrada.php`, `views/entradas.php` |
| 21.4 Dashboard | KPIs reais do banco (vendas, entradas, pedidos do site, estoque baixo, produtos) | `AuthController`, `models/relatorio.php`, `views/dashboard.php` |
| 22 Menu | Todos os módulos com link; Categorias/Fornecedores/Clientes só para admin | `views/dashboard.php`, `views/_topo_admin.php`, `index.php` |
| 24 ViaCEP | Endpoint `controller=cep&action=buscar` + preenchimento automático | `CepController`, `public/assets/js/cep.js` |
| 24.8 Clientes | CRUD de clientes do balcão com CEP (aparecem na tela de Vendas) | `ClienteController`, `models/cliente.php`, `views/clientes.php` |
| 25 Notas fiscais | NF de venda (número sequencial) e NF de entrada (chave de 44 dígitos), telas de impressão | `models/NotaFiscal.php`, `views/nota_fiscal_*.php`, `views/_nota_fiscal.php` |
| 26 Exportação | Excel (CSV) e PDF (Dompdf, pasta `vendor/` incluída) do período filtrado | `RelatorioController`, `views/relatorio_pdf.php` |
| Ficha item 3 — Catálogo/Carrinho/Formulário | Loja virtual: vitrine com busca e categorias, página do bolo, carrinho, conta do cliente, checkout com CEP, pagamento simulado (Pix/cartão), confirmação, meus pedidos e NF do pedido | `LojaController`, `CarrinhoController`, `ContaController`, `PedidoController`, `models/Vitrine.php`, `Carrinho.php`, `ClienteSite.php`, `Pedido.php`, `views/site/*`, `loja.css`, `loja.js` |

## Correções que vieram junto

- Logo e fundo do login/painel apontavam para `/bolomania/imagem/…` (pasta que não existe); agora usam `public/imagem/…`.
- `check.php` usava `$conexao` (variável inexistente).
- Alguns `require` usavam letra maiúscula diferente do nome do arquivo (`Usuario.php` × `usuario.php`). No Windows funciona, mas **na hospedagem (Linux) quebra** — os arquivos alterados já usam o nome exato.
- Arquivos que começavam com uma linha em branco antes do `<?php` (atrapalhava o download do PDF/Excel).

## Para personalizar

- Dados da empresa na nota fiscal (CNPJ, endereço): `views/_nota_fiscal.php`.
- Textos do rodapé da loja: `views/site/_footer.php`.
