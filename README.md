# 🍰 Bolo Mania — Sistema de gestão + Loja virtual

Projeto de informática da turma **RGI61** para o gerenciamento de uma loja de bolos,
feito em **PHP + MySQL (padrão MVC)**.

**Alunos:** Pedro Ribeiro e Lukkas Carvalho
Tem duas partes que usam o mesmo banco e o mesmo estoque:

| Parte | Endereço | Quem usa |
|---|---|---|
| **Sistema de gestão** | `index.php` (abre o login) | administrador e vendedor |
| **Loja virtual** | `index.php?controller=loja&action=index` | clientes |

## Funcionalidades

**Sistema (admin / vendedor)**
- Painel com indicadores do mês (vendas, entradas, pedidos do site, estoque baixo)
- Produtos com foto, categorias e **variações** (peso, sabor, preço e estoque mínimo)
- Fornecedores e clientes com **busca de endereço pelo CEP** (API ViaCEP)
- **Entradas de mercadoria** que abastecem o estoque, com nota fiscal do fornecedor
- **Vendas no balcão** com emissão de **nota fiscal**
- **Relatórios** com gráfico e exportação em **PDF** (Dompdf) e **Excel** (CSV)
- Permissões por perfil: o vendedor não acessa Categorias, Fornecedores e Clientes

**Loja virtual (cliente)**
- Vitrine com busca e filtro por categoria
- Página do bolo com escolha de peso e sabor
- Carrinho, cadastro e login do cliente (CPF validado)
- Checkout com CEP, pagamento simulado (Pix ou cartão) e nota fiscal do pedido
- "Meus pedidos" e "Meus dados"

## Como rodar (XAMPP)

1. Copie a pasta para `C:\xampp\htdocs\bolomania`
2. No phpMyAdmin, crie o banco `bolomania` e importe, nesta ordem, os arquivos da pasta `sql/`:
   1. `atualizacao_parte2.sql`
   2. `categorias_bolomania.sql`
   3. `cadastrar_bolos_lukkas.sql`
   4. `vendedor_lukkas.sql`
   5. `clientes_e_vendas_demo.sql`
   6. `bolo_nutella.sql`
3. Gere a pasta `vendor/` (biblioteca de PDF), que não fica no GitHub:
   no terminal, dentro da pasta do projeto, rode `composer install`
   (sem ela o sistema funciona, só o botão "Exportar PDF" abre a versão para imprimir)
4. Acesse `http://localhost/bolomania/`
5. Confira o banco em `http://localhost/bolomania/check.php` (17 tabelas)

## Logins de teste

| Perfil | E-mail | Senha |
|---|---|---|
| Vendedor | `lukkas@bolomania.com.br` | `lukkas123` |
| Cliente da loja | `bia.carvalho@email.com` | `bolo1234` |

## Estrutura

```
config/        conexão com o banco e funções auxiliares
controllers/   regras de cada tela (MVC)
models/        acesso ao banco de dados
views/         telas do sistema (views/site = loja virtual)
public/        CSS, JavaScript, imagens e fotos dos bolos
sql/           scripts do banco
vendor/        biblioteca Dompdf (gerada com composer install)
```

## Tecnologias

PHP 8 · MySQL/MariaDB · HTML5 · CSS3 · JavaScript · Dompdf · API ViaCEP
