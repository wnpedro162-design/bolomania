-- =====================================================================
-- Bolo Mania — endereço separado em atributos simples
-- (ajuste pedido pelo orientador: o endereço era um campo composto).
--
-- Tabelas: cliente, fornecedor, cliente_site e pedido.
-- Rode UMA vez no phpMyAdmin (banco bolomania > Importar).
-- 1) cria as colunas novas  2) copia os endereços atuais  3) apaga a coluna antiga
-- =====================================================================
SET NAMES utf8mb4;

-- ---------------------------------------------------------------- 1) colunas novas
ALTER TABLE cliente
  ADD COLUMN logradouro  VARCHAR(150) NULL AFTER email,
  ADD COLUMN numero      VARCHAR(10)  NULL AFTER logradouro,
  ADD COLUMN complemento VARCHAR(60)  NULL AFTER numero,
  ADD COLUMN bairro      VARCHAR(80)  NULL AFTER complemento,
  ADD COLUMN cidade      VARCHAR(80)  NULL AFTER bairro,
  ADD COLUMN uf          CHAR(2)      NULL AFTER cidade;

ALTER TABLE fornecedor
  ADD COLUMN logradouro  VARCHAR(150) NULL AFTER email,
  ADD COLUMN numero      VARCHAR(10)  NULL AFTER logradouro,
  ADD COLUMN complemento VARCHAR(60)  NULL AFTER numero,
  ADD COLUMN bairro      VARCHAR(80)  NULL AFTER complemento,
  ADD COLUMN cidade      VARCHAR(80)  NULL AFTER bairro,
  ADD COLUMN uf          CHAR(2)      NULL AFTER cidade;

ALTER TABLE cliente_site
  ADD COLUMN cep         VARCHAR(9)   NOT NULL DEFAULT '' AFTER senha_hash,
  ADD COLUMN logradouro  VARCHAR(150) NOT NULL DEFAULT '' AFTER cep,
  ADD COLUMN numero      VARCHAR(10)  NOT NULL DEFAULT '' AFTER logradouro,
  ADD COLUMN complemento VARCHAR(60)  NULL AFTER numero,
  ADD COLUMN bairro      VARCHAR(80)  NOT NULL DEFAULT '' AFTER complemento,
  ADD COLUMN cidade      VARCHAR(80)  NOT NULL DEFAULT '' AFTER bairro,
  ADD COLUMN uf          CHAR(2)      NOT NULL DEFAULT '' AFTER cidade;

ALTER TABLE pedido
  ADD COLUMN cep_entrega         VARCHAR(9)   NOT NULL DEFAULT '' AFTER forma_pagamento,
  ADD COLUMN logradouro_entrega  VARCHAR(150) NOT NULL DEFAULT '' AFTER cep_entrega,
  ADD COLUMN numero_entrega      VARCHAR(10)  NOT NULL DEFAULT '' AFTER logradouro_entrega,
  ADD COLUMN complemento_entrega VARCHAR(60)  NULL AFTER numero_entrega,
  ADD COLUMN bairro_entrega      VARCHAR(80)  NOT NULL DEFAULT '' AFTER complemento_entrega,
  ADD COLUMN cidade_entrega      VARCHAR(80)  NOT NULL DEFAULT '' AFTER bairro_entrega,
  ADD COLUMN uf_entrega          CHAR(2)      NOT NULL DEFAULT '' AFTER cidade_entrega;

-- ---------------------------------------------------------------- 2) dados atuais
-- Clientes do balcão
UPDATE cliente SET logradouro='Rua Marechal Floriano', numero='215', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua Marechal Floriano, 215 - Centro - Nova Iguaçu/RJ';
UPDATE cliente SET logradouro='Av. Governador Amaral Peixoto', numero='480', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Av. Governador Amaral Peixoto, 480 - Centro - Nova Iguaçu/RJ';
UPDATE cliente SET logradouro='Rua Bernardino de Melo', numero='77', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua Bernardino de Melo, 77 - Centro - Nova Iguaçu/RJ';
UPDATE cliente SET logradouro='Rua Dr. Barros Júnior', numero='130', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua Dr. Barros Júnior, 130 - Centro - Nova Iguaçu/RJ';
UPDATE cliente SET logradouro='Rua Ataíde Pimenta de Moraes', numero='54', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua Ataíde Pimenta de Moraes, 54 - Centro - Nova Iguaçu/RJ';
UPDATE cliente SET logradouro='Estrada de Madureira', numero='1200', bairro='Austin', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Estrada de Madureira, 1200 - Austin - Nova Iguaçu/RJ';
UPDATE cliente SET logradouro='Rua Iracema Soares', numero='18', bairro='Comendador Soares', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua Iracema Soares, 18 - Comendador Soares - Nova Iguaçu/RJ';
UPDATE cliente SET logradouro='Av. Abílio Augusto Távora', numero='2100', bairro='Cabuçu', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Av. Abílio Augusto Távora, 2100 - Cabuçu - Nova Iguaçu/RJ';

-- Fornecedor
UPDATE fornecedor SET logradouro='Rua sem Nome', numero='s/n', bairro='Palhada', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua sem Nome, Palhada - Nova Iguaçu/RJ';

-- Clientes da loja virtual
UPDATE cliente_site SET cep='26210-030', logradouro='Av. Nilo Peçanha', numero='1500', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Av. Nilo Peçanha, 1500 - Centro, Nova Iguaçu/RJ - CEP 26210-030';
UPDATE cliente_site SET cep='26210-120', logradouro='Rua Santos Dumont', numero='300', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua Santos Dumont, 300 - Centro, Nova Iguaçu/RJ - CEP 26210-120';
UPDATE cliente_site SET cep='26215-310', logradouro='Rua Paulo de Frontin', numero='95', bairro='Centro', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='Rua Paulo de Frontin, 95 - Centro, Nova Iguaçu/RJ - CEP 26215-310';
UPDATE cliente_site SET cep='26290-045', logradouro='Rua Joaquim', numero='34', bairro='Cabuçu', cidade='Nova Iguaçu', uf='RJ' WHERE endereco='rua joaquin 34 cabuçu 26290045';

-- Endereços de entrega dos pedidos
UPDATE pedido SET cep_entrega='26210-030', logradouro_entrega='Av. Nilo Peçanha', numero_entrega='1500', bairro_entrega='Centro', cidade_entrega='Nova Iguaçu', uf_entrega='RJ' WHERE endereco_entrega='Av. Nilo Peçanha, 1500 - Centro, Nova Iguaçu/RJ - CEP 26210-030';
UPDATE pedido SET cep_entrega='26210-120', logradouro_entrega='Rua Santos Dumont', numero_entrega='300', bairro_entrega='Centro', cidade_entrega='Nova Iguaçu', uf_entrega='RJ' WHERE endereco_entrega='Rua Santos Dumont, 300 - Centro, Nova Iguaçu/RJ - CEP 26210-120';
UPDATE pedido SET cep_entrega='26215-310', logradouro_entrega='Rua Paulo de Frontin', numero_entrega='95', bairro_entrega='Centro', cidade_entrega='Nova Iguaçu', uf_entrega='RJ' WHERE endereco_entrega='Rua Paulo de Frontin, 95 - Centro, Nova Iguaçu/RJ - CEP 26215-310';
UPDATE pedido SET cep_entrega='26290-045', logradouro_entrega='Rua Joaquim', numero_entrega='34', bairro_entrega='Cabuçu', cidade_entrega='Nova Iguaçu', uf_entrega='RJ' WHERE endereco_entrega='rua joaquin 34 cabuçu 26290045';

-- Qualquer endereço que não foi reconhecido acima não se perde:
-- o texto inteiro vai para o logradouro (dá para corrigir depois pela tela).
UPDATE cliente      SET logradouro = LEFT(endereco, 150) WHERE logradouro IS NULL AND endereco IS NOT NULL AND endereco <> '';
UPDATE fornecedor   SET logradouro = LEFT(endereco, 150) WHERE logradouro IS NULL AND endereco IS NOT NULL AND endereco <> '';
UPDATE cliente_site SET logradouro = LEFT(endereco, 150) WHERE logradouro = '';
UPDATE pedido       SET logradouro_entrega = LEFT(endereco_entrega, 150) WHERE logradouro_entrega = '';

-- ---------------------------------------------------------------- 3) remove a coluna composta
ALTER TABLE cliente      DROP COLUMN endereco;
ALTER TABLE fornecedor   DROP COLUMN endereco;
ALTER TABLE cliente_site DROP COLUMN endereco;
ALTER TABLE pedido       DROP COLUMN endereco_entrega;

ALTER TABLE cliente_site
  ALTER COLUMN cep DROP DEFAULT, ALTER COLUMN logradouro DROP DEFAULT, ALTER COLUMN numero DROP DEFAULT,
  ALTER COLUMN bairro DROP DEFAULT, ALTER COLUMN cidade DROP DEFAULT, ALTER COLUMN uf DROP DEFAULT;
ALTER TABLE pedido
  ALTER COLUMN cep_entrega DROP DEFAULT, ALTER COLUMN logradouro_entrega DROP DEFAULT, ALTER COLUMN numero_entrega DROP DEFAULT,
  ALTER COLUMN bairro_entrega DROP DEFAULT, ALTER COLUMN cidade_entrega DROP DEFAULT, ALTER COLUMN uf_entrega DROP DEFAULT;
