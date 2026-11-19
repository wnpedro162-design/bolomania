-- =====================================================================
-- Bolo Mania — Lukkas passa a ser o vendedor de todas as vendas
-- Rode no phpMyAdmin (banco bolomania > Importar ou aba SQL).
-- Login do Lukkas no sistema:  lukkas@bolomania.com.br  /  senha: lukkas123
-- =====================================================================

START TRANSACTION;

-- 1) Cria o usuário vendedor Lukkas (só se ainda não existir)
INSERT INTO usuario (nome, email, senha, perfil, ativo)
SELECT 'Lukkas', 'lukkas@bolomania.com.br',
       '$2y$10$k2qdJ2QjpCu5uLR31rbw5uTVd8p8eFT5gsLSelqnRt/k4ydEQA6Yy',
       'vendedor', 1
WHERE NOT EXISTS (SELECT 1 FROM usuario WHERE email = 'lukkas@bolomania.com.br');

SET @lukkas = (SELECT id FROM usuario WHERE email = 'lukkas@bolomania.com.br');

-- 2) Todas as vendas do balcão passam a ser do Lukkas
UPDATE venda SET usuario_id = @lukkas;

-- 3) (Opcional) deixar o Lukkas como ÚNICO vendedor com acesso ao sistema.
--    Tire os dois traços do começo da linha abaixo para ativar:
-- UPDATE usuario SET ativo = 0 WHERE perfil = 'vendedor' AND id <> @lukkas;

COMMIT;

-- Conferência
SELECT u.nome AS vendedor, COUNT(v.id) AS vendas, SUM(v.valor_total) AS total
FROM venda v JOIN usuario u ON u.id = v.usuario_id
GROUP BY u.nome;
