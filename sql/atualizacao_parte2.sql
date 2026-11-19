-- =====================================================================
-- Bolo Mania — Atualização da Parte 2 (manual seções 18, 24 e loja virtual)
-- Rode UMA vez no phpMyAdmin, com o banco `bolomania` selecionado (aba SQL).
-- Não apaga nada: só acrescenta colunas e tabelas novas.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 18.2 — Fornecedor ganha exclusão lógica (campo ativo)
-- 24.2 — Fornecedor e Cliente ganham o campo CEP
-- ---------------------------------------------------------------------
ALTER TABLE fornecedor
  ADD COLUMN IF NOT EXISTS ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER endereco,
  ADD COLUMN IF NOT EXISTS cep VARCHAR(9) NULL AFTER endereco;

ALTER TABLE cliente
  ADD COLUMN IF NOT EXISTS cep VARCHAR(9) NULL AFTER endereco;

-- ---------------------------------------------------------------------
-- 18.2 — Toda variação precisa ter a sua linha em `estoque`
-- (cria a linha zerada para variações antigas que ainda não têm)
-- ---------------------------------------------------------------------
INSERT INTO estoque (variacao_id, quantidade, minimo)
SELECT v.id, 0, 2
FROM variacao v
LEFT JOIN estoque e ON e.variacao_id = v.id
WHERE e.id IS NULL;

-- ---------------------------------------------------------------------
-- Loja virtual (lado do CLIENTE): cadastro, pedido e itens do pedido.
-- Separado de venda/venda_item, que são do balcão (PDV) do vendedor.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cliente_site (
  cpf        CHAR(11)     NOT NULL PRIMARY KEY,   -- só dígitos
  nome       VARCHAR(100) NOT NULL,
  email      VARCHAR(100) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  endereco   VARCHAR(255) NOT NULL,
  telefone   VARCHAR(20)  NULL,
  criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS pedido (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  cliente_cpf      CHAR(11)      NOT NULL,
  data             DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status           ENUM('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  forma_pagamento  ENUM('pix','cartao') NOT NULL DEFAULT 'pix',
  endereco_entrega VARCHAR(255)  NOT NULL,
  valor_total      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_cpf) REFERENCES cliente_site(cpf) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS pedido_item (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id      INT           NOT NULL,
  variacao_id    INT           NOT NULL,
  quantidade     INT           NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_pedido_item_pedido   FOREIGN KEY (pedido_id)   REFERENCES pedido(id)   ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pedido_item_variacao FOREIGN KEY (variacao_id) REFERENCES variacao(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pedidos do site também baixam o estoque: a movimentação passa a aceitar a origem 'pedido'
ALTER TABLE movimento_estoque
  MODIFY origem ENUM('entrada','venda','pedido') NOT NULL;
