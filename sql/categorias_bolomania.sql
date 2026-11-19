-- =====================================================================
-- Bolo Mania — troca as categorias do projeto antigo (Donuts, Cupcakes,
-- Balas) por categorias de bolo e organiza os bolos dentro delas.
-- Rode UMA vez no phpMyAdmin (banco bolomania > Importar ou aba SQL).
-- =====================================================================

START TRANSACTION;

-- 1) Apaga os produtos do projeto antigo (donuts, cupcakes e balas)
--    que nunca tiveram variação, venda ou pedido.
DELETE p FROM produto p
JOIN categoria c ON c.id = p.categoria_id
WHERE c.nome IN ('Donuts', 'Cupcakes', 'Balas')
  AND NOT EXISTS (SELECT 1 FROM variacao v WHERE v.produto_id = p.id);

--    Se algum deles tiver histórico, ele não pode ser apagado: fica inativo.
UPDATE produto p
JOIN categoria c ON c.id = p.categoria_id
SET p.ativo = 0
WHERE c.nome IN ('Donuts', 'Cupcakes', 'Balas');

-- 2) Renomeia as categorias (os IDs continuam os mesmos)
UPDATE categoria SET nome = 'Bolos de Chocolate', ativo = 1 WHERE nome = 'Donuts';
UPDATE categoria SET nome = 'Bolos Tradicionais', ativo = 1 WHERE nome = 'Bolos';
UPDATE categoria SET nome = 'Bolos Recheados',    ativo = 1 WHERE nome = 'Cupcakes';
UPDATE categoria SET nome = 'Bolos de Festa',     ativo = 1 WHERE nome = 'Balas';

-- 3) Coloca cada bolo na sua categoria
UPDATE produto SET categoria_id = (SELECT id FROM categoria WHERE nome = 'Bolos de Chocolate')
WHERE nome IN ('Bolo de Brigadeiro', 'ChocoDream', 'Bolo de Nutella com Morango', 'Bolo da Casa');

UPDATE produto SET categoria_id = (SELECT id FROM categoria WHERE nome = 'Bolos Tradicionais')
WHERE nome IN ('Bolo de Cenoura');

UPDATE produto SET categoria_id = (SELECT id FROM categoria WHERE nome = 'Bolos Recheados')
WHERE nome IN ('Bolo de Beijinho');

COMMIT;

-- 4) Conferência
SELECT c.nome AS categoria, p.nome AS bolo, IF(p.ativo = 1, 'ativo', 'inativo') AS status
FROM categoria c
LEFT JOIN produto p ON p.categoria_id = c.id
ORDER BY c.nome, p.nome;
