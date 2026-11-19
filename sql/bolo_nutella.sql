-- =====================================================================
-- Bolo Mania — o "Bolo da Casa" vira "Bolo de Nutella com Morango"
-- (nome, descrição, categoria, sabor e SKU das variações).
-- Os IDs não mudam, então vendas, pedidos e notas antigas continuam ligados.
-- Pode rodar mais de uma vez.
-- =====================================================================

START TRANSACTION;

SET @bolo = (SELECT id FROM produto
             WHERE nome IN ('Bolo da Casa', 'Bolo de Nutella com Morango')
             ORDER BY id LIMIT 1);

-- 1) nome, descrição e categoria
UPDATE produto
SET nome = 'Bolo de Nutella com Morango',
    descricao = 'Massa fofinha de chocolate com recheio e cobertura cremosa de Nutella, finalizado com morangos frescos. Intenso, molhadinho e irresistível.',
    categoria_id = COALESCE((SELECT id FROM categoria WHERE nome = 'Bolos de Chocolate' LIMIT 1), categoria_id)
WHERE id = @bolo;

-- 2) variações: sabor novo e SKU com NUTELLA no lugar de CASA
UPDATE variacao
SET cor = 'Nutella com morango',
    sku = REPLACE(sku, '-CASA', '-NUTELLA')
WHERE produto_id = @bolo;

COMMIT;

-- conferência
SELECT p.id, p.nome, c.nome AS categoria, v.tamanho AS peso, v.cor AS sabor, v.sku, v.preco
FROM produto p
JOIN categoria c ON c.id = p.categoria_id
LEFT JOIN variacao v ON v.produto_id = p.id
WHERE p.id = @bolo
ORDER BY v.preco;
