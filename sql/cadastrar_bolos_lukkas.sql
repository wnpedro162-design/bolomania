-- =====================================================================
-- Bolo Mania — cadastra os pesos/preços de todos os bolos e registra
-- UMA entrada de mercadoria do fornecedor Lukkas (com nota fiscal).
-- Rode UMA única vez no phpMyAdmin (banco bolomania > aba SQL).
-- Rodar de novo cria outra entrada e soma estoque de novo.
-- =====================================================================

START TRANSACTION;

-- 1) Plano: bolo, peso, sabor, preço de venda, custo e quantidade recebida
DROP TEMPORARY TABLE IF EXISTS plano;
CREATE TEMPORARY TABLE plano (
  produto_nome VARCHAR(100),
  peso         VARCHAR(10),
  sabor        VARCHAR(30),
  sku_sabor    VARCHAR(20),
  preco        DECIMAL(10,2),
  custo        DECIMAL(10,2),
  qtd          INT,
  minimo       INT
);

INSERT INTO plano VALUES
-- Bolo de Brigadeiro
('Bolo de Brigadeiro', '500g',  'Brigadeiro', 'BRIGADEIRO',  35.00,  16.00, 12, 3),
('Bolo de Brigadeiro', '1kg',   'Brigadeiro', 'BRIGADEIRO',  65.00,  29.00, 10, 3),
('Bolo de Brigadeiro', '1,5kg', 'Brigadeiro', 'BRIGADEIRO',  95.00,  43.00,  8, 2),
('Bolo de Brigadeiro', '2kg',   'Brigadeiro', 'BRIGADEIRO', 120.00,  54.00,  6, 2),
('Bolo de Brigadeiro', '3kg',   'Brigadeiro', 'BRIGADEIRO', 175.00,  79.00,  4, 1),
-- Bolo de Beijinho
('Bolo de Beijinho',   '500g',  'Beijinho',   'BEIJINHO',    35.00,  16.00, 12, 3),
('Bolo de Beijinho',   '1kg',   'Beijinho',   'BEIJINHO',    65.00,  29.00, 10, 3),
('Bolo de Beijinho',   '1,5kg', 'Beijinho',   'BEIJINHO',    95.00,  43.00,  8, 2),
('Bolo de Beijinho',   '2kg',   'Beijinho',   'BEIJINHO',   120.00,  54.00,  6, 2),
('Bolo de Beijinho',   '3kg',   'Beijinho',   'BEIJINHO',   175.00,  79.00,  4, 1),
-- Bolo de Cenoura
('Bolo de Cenoura',    '500g',  'Cenoura com chocolate', 'CENOURA',  30.00, 13.50, 12, 3),
('Bolo de Cenoura',    '1kg',   'Cenoura com chocolate', 'CENOURA',  55.00, 25.00, 10, 3),
('Bolo de Cenoura',    '1,5kg', 'Cenoura com chocolate', 'CENOURA',  80.00, 36.00,  8, 2),
('Bolo de Cenoura',    '2kg',   'Cenoura com chocolate', 'CENOURA', 105.00, 47.00,  6, 2),
('Bolo de Cenoura',    '3kg',   'Cenoura com chocolate', 'CENOURA', 150.00, 67.50,  4, 1),
-- Bolo de Nutella com Morango
('Bolo de Nutella com Morango', '500g',  'Nutella com morango', 'NUTELLA',       40.00,  18.00, 12, 3),
('Bolo de Nutella com Morango', '1kg',   'Nutella com morango', 'NUTELLA',       75.00,  34.00, 10, 3),
('Bolo de Nutella com Morango', '1,5kg', 'Nutella com morango', 'NUTELLA',      110.00,  49.50,  8, 2),
('Bolo de Nutella com Morango', '2kg',   'Nutella com morango', 'NUTELLA',      140.00,  63.00,  6, 2),
('Bolo de Nutella com Morango', '3kg',   'Nutella com morango', 'NUTELLA',      200.00,  90.00,  4, 1),
-- ChocoDream
('ChocoDream',         '500g',  'Chocolate com frutas', 'CHOCO',  50.00,  22.50, 12, 3),
('ChocoDream',         '1kg',   'Chocolate com frutas', 'CHOCO',  90.00,  40.50, 10, 3),
('ChocoDream',         '1,5kg', 'Chocolate com frutas', 'CHOCO', 130.00,  58.50,  8, 2),
('ChocoDream',         '2kg',   'Chocolate com frutas', 'CHOCO', 170.00,  76.50,  6, 2),
('ChocoDream',         '3kg',   'Chocolate com frutas', 'CHOCO', 245.00, 110.00,  4, 1);

-- 2) Fornecedor Lukkas (cria só se ainda não existir) e garante que está ativo
INSERT INTO fornecedor (nome, cnpj, telefone, email, endereco, cep, ativo)
SELECT 'Lukkas Confeitaria Ltda', '12.345.678/0001-95', '(21) 99999-0000',
       'contato@lukkas.com.br', 'Rua das Confeitarias, 100 - Centro', '26210-000', 1
WHERE NOT EXISTS (SELECT 1 FROM fornecedor WHERE nome LIKE 'Lukkas%');

SET @forn = (SELECT id FROM fornecedor WHERE nome LIKE 'Lukkas%' ORDER BY id LIMIT 1);
UPDATE fornecedor SET ativo = 1 WHERE id = @forn;

-- 3) Variações (SKU = BOLO-<id>-<PESO>-<SABOR>, mesmo padrão da tela de Variações)
INSERT IGNORE INTO variacao (produto_id, tamanho, cor, sku, preco)
SELECT p.id, pl.peso, pl.sabor,
       CONCAT('BOLO-', p.id, '-', UPPER(REPLACE(pl.peso, ',', '')), '-', pl.sku_sabor),
       pl.preco
FROM plano pl
JOIN produto p ON p.nome = pl.produto_nome;

-- se a variação já existia, atualiza o preço para o da tabela acima
UPDATE variacao v
JOIN produto p ON p.id = v.produto_id
JOIN plano pl ON pl.produto_nome = p.nome
   AND v.sku = CONCAT('BOLO-', p.id, '-', UPPER(REPLACE(pl.peso, ',', '')), '-', pl.sku_sabor)
SET v.preco = pl.preco;

-- 4) Toda variação com sua linha de estoque (e o mínimo do plano)
INSERT IGNORE INTO estoque (variacao_id, quantidade, minimo)
SELECT v.id, 0, pl.minimo
FROM plano pl
JOIN produto p ON p.nome = pl.produto_nome
JOIN variacao v ON v.sku = CONCAT('BOLO-', p.id, '-', UPPER(REPLACE(pl.peso, ',', '')), '-', pl.sku_sabor);

-- 5) Entrada de mercadoria do Lukkas
INSERT INTO entrada_mercadoria (fornecedor_id, data, valor_total)
VALUES (@forn, CURDATE(), 0);
SET @ent = LAST_INSERT_ID();

INSERT INTO entrada_item (entrada_id, variacao_id, quantidade, custo_unitario)
SELECT @ent, v.id, pl.qtd, pl.custo
FROM plano pl
JOIN produto p ON p.nome = pl.produto_nome
JOIN variacao v ON v.sku = CONCAT('BOLO-', p.id, '-', UPPER(REPLACE(pl.peso, ',', '')), '-', pl.sku_sabor);

UPDATE entrada_mercadoria
SET valor_total = (SELECT SUM(quantidade * custo_unitario) FROM entrada_item WHERE entrada_id = @ent)
WHERE id = @ent;

-- 6) Estoque sobe + histórico de movimentação (igual à tela de Entradas)
UPDATE estoque e
JOIN entrada_item i ON i.variacao_id = e.variacao_id AND i.entrada_id = @ent
SET e.quantidade = e.quantidade + i.quantidade;

INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id)
SELECT variacao_id, 'entrada', quantidade, 'entrada', @ent
FROM entrada_item WHERE entrada_id = @ent;

-- 7) Nota fiscal do fornecedor (chave de acesso de 44 números, única por entrada)
INSERT INTO nota_fiscal_entrada (entrada_id, modelo, serie, numero, chave_acesso, data_emissao, valor_total)
SELECT @ent, '55', '1', CONCAT('LK', LPAD(@ent, 6, '0')),
       CONCAT('3326', DATE_FORMAT(CURDATE(), '%y%m'), LPAD(@ent, 36, '0')),
       CURDATE(), valor_total
FROM entrada_mercadoria WHERE id = @ent;

COMMIT;

-- 8) Conferência: o que foi cadastrado
SELECT p.nome AS bolo, v.tamanho AS peso, v.cor AS sabor, v.sku, v.preco,
       e.quantidade AS estoque, e.minimo
FROM variacao v
JOIN produto p ON p.id = v.produto_id
JOIN estoque e ON e.variacao_id = v.id
ORDER BY p.nome, v.preco;
