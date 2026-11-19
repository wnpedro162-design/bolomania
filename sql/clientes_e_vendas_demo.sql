-- =====================================================================
-- Bolo Mania — clientes que já compraram (dados de demonstração)
--   * 8 clientes do balcão + 16 vendas finalizadas (a maioria com nota fiscal)
--   * 3 clientes da loja virtual + 5 pedidos pagos/entregues
-- Tudo baixa o estoque e fica no histórico de movimentação.
-- ANTES: rode o cadastrar_bolos_lukkas.sql (os pesos precisam existir).
-- Rode UMA única vez no phpMyAdmin (banco bolomania > Importar).
-- Senha dos clientes da loja virtual: bolo1234
-- =====================================================================

START TRANSACTION;

-- quem fez as vendas: o vendedor Lukkas (rode antes o vendedor_lukkas.sql;
-- se ele não existir, usa o primeiro administrador)
SET @adm  = COALESCE(
    (SELECT id FROM usuario WHERE nome = 'Lukkas' OR email = 'lukkas@bolomania.com.br' ORDER BY id LIMIT 1),
    (SELECT id FROM usuario WHERE perfil = 'admin' AND ativo = 1 ORDER BY id LIMIT 1));
SET @vend = @adm;

-- ---------------------------------------------------------------------
-- 1) Clientes do balcão
-- ---------------------------------------------------------------------
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'Ana Paula Ribeiro', '787.789.328-07', '(21) 98765-1023', 'anapaula.ribeiro@email.com', 'Rua Marechal Floriano, 215 - Centro - Nova Iguaçu/RJ', '26210-000' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '787.789.328-07');
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'Carlos Eduardo Santos', '792.174.218-12', '(21) 99812-4410', 'carlos.esantos@email.com', 'Av. Governador Amaral Peixoto, 480 - Centro - Nova Iguaçu/RJ', '26210-060' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '792.174.218-12');
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'Fernanda Oliveira Lima', '096.792.908-32', '(21) 97654-2288', 'fe.oliveiralima@email.com', 'Rua Bernardino de Melo, 77 - Centro - Nova Iguaçu/RJ', '26255-140' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '096.792.908-32');
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'João Pedro Almeida', '100.339.075-79', '(21) 98123-5567', 'jp.almeida@email.com', 'Rua Dr. Barros Júnior, 130 - Centro - Nova Iguaçu/RJ', '26215-071' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '100.339.075-79');
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'Mariana Costa Souza', '793.834.701-94', '(21) 99345-7781', 'mari.costasouza@email.com', 'Rua Ataíde Pimenta de Moraes, 54 - Centro - Nova Iguaçu/RJ', '26210-190' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '793.834.701-94');
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'Rafael Moreira Dias', '746.814.538-54', '(21) 98877-3302', 'rafael.mdias@email.com', 'Estrada de Madureira, 1200 - Austin - Nova Iguaçu/RJ', '26087-000' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '746.814.538-54');
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'Juliana Ferreira Gomes', '401.916.146-10', '(21) 97411-9056', 'ju.ferreiragomes@email.com', 'Rua Iracema Soares, 18 - Comendador Soares - Nova Iguaçu/RJ', '26280-000' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '401.916.146-10');
INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco, cep) SELECT 'Buffet Doce Festa Ltda', '45.723.174/0001-10', '(21) 3767-4400', 'contato@docefesta.com.br', 'Av. Abílio Augusto Távora, 2100 - Cabuçu - Nova Iguaçu/RJ', '26291-000' WHERE NOT EXISTS (SELECT 1 FROM cliente WHERE cpf_cnpj = '45.723.174/0001-10');

-- ---------------------------------------------------------------------
-- 2) Vendas no balcão (data = hoje menos X dias)
-- ---------------------------------------------------------------------

-- venda 1
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '787.789.328-07' LIMIT 1), CURDATE() - INTERVAL 17 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Brigadeiro' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 2
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@adm, (SELECT id FROM cliente WHERE cpf_cnpj = '792.174.218-12' LIMIT 1), CURDATE() - INTERVAL 16 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'ChocoDream' AND v.tamanho = '2kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 3
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '096.792.908-32' LIMIT 1), CURDATE() - INTERVAL 15 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 2, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Cenoura' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Beijinho' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 4
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@adm, (SELECT id FROM cliente WHERE cpf_cnpj = '45.723.174/0001-10' LIMIT 1), CURDATE() - INTERVAL 14 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 2, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Nutella com Morango' AND v.tamanho = '3kg' ORDER BY v.id LIMIT 1);
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Brigadeiro' AND v.tamanho = '2kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 5
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '100.339.075-79' LIMIT 1), CURDATE() - INTERVAL 12 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Beijinho' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 6
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, NULL, CURDATE() - INTERVAL 11 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Cenoura' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;

-- venda 7
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@adm, (SELECT id FROM cliente WHERE cpf_cnpj = '793.834.701-94' LIMIT 1), CURDATE() - INTERVAL 10 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'ChocoDream' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 2, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Brigadeiro' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 8
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '746.814.538-54' LIMIT 1), CURDATE() - INTERVAL 8 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Nutella com Morango' AND v.tamanho = '1,5kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 9
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@adm, (SELECT id FROM cliente WHERE cpf_cnpj = '401.916.146-10' LIMIT 1), CURDATE() - INTERVAL 7 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Brigadeiro' AND v.tamanho = '1,5kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 10
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '787.789.328-07' LIMIT 1), CURDATE() - INTERVAL 6 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Beijinho' AND v.tamanho = '2kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 11
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@adm, (SELECT id FROM cliente WHERE cpf_cnpj = '45.723.174/0001-10' LIMIT 1), CURDATE() - INTERVAL 5 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'ChocoDream' AND v.tamanho = '3kg' ORDER BY v.id LIMIT 1);
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Cenoura' AND v.tamanho = '2kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 12
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '096.792.908-32' LIMIT 1), CURDATE() - INTERVAL 4 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 2, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Nutella com Morango' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;

-- venda 13
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@adm, NULL, CURDATE() - INTERVAL 3 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Brigadeiro' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'ChocoDream' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 14
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '792.174.218-12' LIMIT 1), CURDATE() - INTERVAL 2 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Cenoura' AND v.tamanho = '1,5kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 15
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@adm, (SELECT id FROM cliente WHERE cpf_cnpj = '100.339.075-79' LIMIT 1), CURDATE() - INTERVAL 1 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Beijinho' AND v.tamanho = '1,5kg' ORDER BY v.id LIMIT 1);
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Nutella com Morango' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;
INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total) SELECT @v, '65', '1', (SELECT COALESCE(MAX(CAST(n.numero AS UNSIGNED)), 0) + 1 FROM nota_fiscal_venda n), data, valor_total FROM venda WHERE id = @v;

-- venda 16
INSERT INTO venda (usuario_id, cliente_id, data, status, valor_total) VALUES (@vend, (SELECT id FROM cliente WHERE cpf_cnpj = '793.834.701-94' LIMIT 1), CURDATE() - INTERVAL 0 DAY, 'finalizada', 0);
SET @v = LAST_INSERT_ID();
INSERT INTO venda_item (venda_id, variacao_id, quantidade, preco_unitario) SELECT @v, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'ChocoDream' AND v.tamanho = '1,5kg' ORDER BY v.id LIMIT 1);
UPDATE venda SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM venda_item WHERE venda_id = @v) WHERE id = @v;
UPDATE estoque e JOIN venda_item i ON i.variacao_id = e.variacao_id AND i.venda_id = @v SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'venda', @v, (SELECT data FROM venda WHERE id = @v) + INTERVAL 14 HOUR FROM venda_item WHERE venda_id = @v;

-- ---------------------------------------------------------------------
-- 3) Clientes da loja virtual (login: e-mail + senha bolo1234)
-- ---------------------------------------------------------------------
INSERT IGNORE INTO cliente_site (cpf, nome, email, senha_hash, endereco, telefone) VALUES ('10033076634', 'Beatriz Carvalho Nunes', 'bia.carvalho@email.com', '$2y$10$hyKjLPX1dPDBDRaVq5DAKOsHOCM0BVkzvUZizQFDkmsnLc2RMsBB6', 'Rua Santos Dumont, 300 - Centro, Nova Iguaçu/RJ - CEP 26210-120', '21987650011');
INSERT IGNORE INTO cliente_site (cpf, nome, email, senha_hash, endereco, telefone) VALUES ('61934514551', 'Lucas Martins Pereira', 'lucas.mpereira@email.com', '$2y$10$hyKjLPX1dPDBDRaVq5DAKOsHOCM0BVkzvUZizQFDkmsnLc2RMsBB6', 'Rua Paulo de Frontin, 95 - Centro, Nova Iguaçu/RJ - CEP 26215-310', '21991234455');
INSERT IGNORE INTO cliente_site (cpf, nome, email, senha_hash, endereco, telefone) VALUES ('06123100785', 'Camila Rocha Antunes', 'camila.rantunes@email.com', '$2y$10$hyKjLPX1dPDBDRaVq5DAKOsHOCM0BVkzvUZizQFDkmsnLc2RMsBB6', 'Av. Nilo Peçanha, 1500 - Centro, Nova Iguaçu/RJ - CEP 26210-030', '21976543322');

-- ---------------------------------------------------------------------
-- 4) Pedidos feitos pela loja virtual
-- ---------------------------------------------------------------------

-- pedido 1
INSERT INTO pedido (cliente_cpf, data, status, forma_pagamento, endereco_entrega, valor_total) SELECT cpf, NOW() - INTERVAL 13 DAY, 'entregue', 'pix', endereco, 0 FROM cliente_site WHERE cpf = '10033076634';
SET @p = LAST_INSERT_ID();
INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario) SELECT @p, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Brigadeiro' AND v.tamanho = '2kg' ORDER BY v.id LIMIT 1);
UPDATE pedido SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM pedido_item WHERE pedido_id = @p) WHERE id = @p;
UPDATE estoque e JOIN pedido_item i ON i.variacao_id = e.variacao_id AND i.pedido_id = @p SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'pedido', @p, (SELECT data FROM pedido WHERE id = @p) FROM pedido_item WHERE pedido_id = @p;

-- pedido 2
INSERT INTO pedido (cliente_cpf, data, status, forma_pagamento, endereco_entrega, valor_total) SELECT cpf, NOW() - INTERVAL 9 DAY, 'entregue', 'cartao', endereco, 0 FROM cliente_site WHERE cpf = '61934514551';
SET @p = LAST_INSERT_ID();
INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario) SELECT @p, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'ChocoDream' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario) SELECT @p, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Beijinho' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
UPDATE pedido SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM pedido_item WHERE pedido_id = @p) WHERE id = @p;
UPDATE estoque e JOIN pedido_item i ON i.variacao_id = e.variacao_id AND i.pedido_id = @p SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'pedido', @p, (SELECT data FROM pedido WHERE id = @p) FROM pedido_item WHERE pedido_id = @p;

-- pedido 3
INSERT INTO pedido (cliente_cpf, data, status, forma_pagamento, endereco_entrega, valor_total) SELECT cpf, NOW() - INTERVAL 6 DAY, 'enviado', 'pix', endereco, 0 FROM cliente_site WHERE cpf = '06123100785';
SET @p = LAST_INSERT_ID();
INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario) SELECT @p, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Nutella com Morango' AND v.tamanho = '2kg' ORDER BY v.id LIMIT 1);
UPDATE pedido SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM pedido_item WHERE pedido_id = @p) WHERE id = @p;
UPDATE estoque e JOIN pedido_item i ON i.variacao_id = e.variacao_id AND i.pedido_id = @p SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'pedido', @p, (SELECT data FROM pedido WHERE id = @p) FROM pedido_item WHERE pedido_id = @p;

-- pedido 4
INSERT INTO pedido (cliente_cpf, data, status, forma_pagamento, endereco_entrega, valor_total) SELECT cpf, NOW() - INTERVAL 2 DAY, 'pago', 'cartao', endereco, 0 FROM cliente_site WHERE cpf = '10033076634';
SET @p = LAST_INSERT_ID();
INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario) SELECT @p, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Cenoura' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario) SELECT @p, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Brigadeiro' AND v.tamanho = '500g' ORDER BY v.id LIMIT 1);
UPDATE pedido SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM pedido_item WHERE pedido_id = @p) WHERE id = @p;
UPDATE estoque e JOIN pedido_item i ON i.variacao_id = e.variacao_id AND i.pedido_id = @p SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'pedido', @p, (SELECT data FROM pedido WHERE id = @p) FROM pedido_item WHERE pedido_id = @p;

-- pedido 5
INSERT INTO pedido (cliente_cpf, data, status, forma_pagamento, endereco_entrega, valor_total) SELECT cpf, NOW() - INTERVAL 1 DAY, 'pago', 'pix', endereco, 0 FROM cliente_site WHERE cpf = '61934514551';
SET @p = LAST_INSERT_ID();
INSERT INTO pedido_item (pedido_id, variacao_id, quantidade, preco_unitario) SELECT @p, id, 1, preco FROM variacao WHERE id = (SELECT v.id FROM variacao v JOIN produto p ON p.id = v.produto_id WHERE p.nome = 'Bolo de Beijinho' AND v.tamanho = '1kg' ORDER BY v.id LIMIT 1);
UPDATE pedido SET valor_total = (SELECT SUM(quantidade * preco_unitario) FROM pedido_item WHERE pedido_id = @p) WHERE id = @p;
UPDATE estoque e JOIN pedido_item i ON i.variacao_id = e.variacao_id AND i.pedido_id = @p SET e.quantidade = e.quantidade - i.quantidade;
INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data) SELECT variacao_id, 'saida', quantidade, 'pedido', @p, (SELECT data FROM pedido WHERE id = @p) FROM pedido_item WHERE pedido_id = @p;

COMMIT;

-- ---------------------------------------------------------------------
-- 5) Conferência
-- ---------------------------------------------------------------------
SELECT c.nome AS cliente, COUNT(v.id) AS compras, SUM(v.valor_total) AS total_gasto
FROM cliente c
JOIN venda v ON v.cliente_id = c.id
GROUP BY c.id, c.nome
ORDER BY total_gasto DESC;
