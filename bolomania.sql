-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/11/2026 às 22:26
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `bolomania`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome`, `ativo`) VALUES
(1, 'Bolos de Chocolate', 1),
(2, 'Bolos Tradicionais', 1),
(3, 'Bolos Recheados', 1),
(4, 'Bolos de Festa', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf_cnpj` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `cpf_cnpj`, `telefone`, `email`, `endereco`, `cep`) VALUES
(1, 'Ana Paula Ribeiro', '787.789.328-07', '(21) 98765-1023', 'anapaula.ribeiro@email.com', 'Rua Marechal Floriano, 215 - Centro - Nova Iguaçu/RJ', '26210-000'),
(2, 'Carlos Eduardo Santos', '792.174.218-12', '(21) 99812-4410', 'carlos.esantos@email.com', 'Av. Governador Amaral Peixoto, 480 - Centro - Nova Iguaçu/RJ', '26210-060'),
(3, 'Fernanda Oliveira Lima', '096.792.908-32', '(21) 97654-2288', 'fe.oliveiralima@email.com', 'Rua Bernardino de Melo, 77 - Centro - Nova Iguaçu/RJ', '26255-140'),
(4, 'João Pedro Almeida', '100.339.075-79', '(21) 98123-5567', 'jp.almeida@email.com', 'Rua Dr. Barros Júnior, 130 - Centro - Nova Iguaçu/RJ', '26215-071'),
(5, 'Mariana Costa Souza', '793.834.701-94', '(21) 99345-7781', 'mari.costasouza@email.com', 'Rua Ataíde Pimenta de Moraes, 54 - Centro - Nova Iguaçu/RJ', '26210-190'),
(6, 'Rafael Moreira Dias', '746.814.538-54', '(21) 98877-3302', 'rafael.mdias@email.com', 'Estrada de Madureira, 1200 - Austin - Nova Iguaçu/RJ', '26087-000'),
(7, 'Juliana Ferreira Gomes', '401.916.146-10', '(21) 97411-9056', 'ju.ferreiragomes@email.com', 'Rua Iracema Soares, 18 - Comendador Soares - Nova Iguaçu/RJ', '26280-000'),
(8, 'Buffet Doce Festa Ltda', '45.723.174/0001-10', '(21) 3767-4400', 'contato@docefesta.com.br', 'Av. Abílio Augusto Távora, 2100 - Cabuçu - Nova Iguaçu/RJ', '26291-000');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente_site`
--

CREATE TABLE `cliente_site` (
  `cpf` char(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente_site`
--

INSERT INTO `cliente_site` (`cpf`, `nome`, `email`, `senha_hash`, `endereco`, `telefone`, `criado_em`) VALUES
('06123100785', 'Camila Rocha Antunes', 'camila.rantunes@email.com', '$2y$10$hyKjLPX1dPDBDRaVq5DAKOsHOCM0BVkzvUZizQFDkmsnLc2RMsBB6', 'Av. Nilo Peçanha, 1500 - Centro, Nova Iguaçu/RJ - CEP 26210-030', '21976543322', '2026-11-19 17:13:06'),
('10033076634', 'Beatriz Carvalho Nunes', 'bia.carvalho@email.com', '$2y$10$hyKjLPX1dPDBDRaVq5DAKOsHOCM0BVkzvUZizQFDkmsnLc2RMsBB6', 'Rua Santos Dumont, 300 - Centro, Nova Iguaçu/RJ - CEP 26210-120', '21987650011', '2026-11-19 17:13:06'),
('61934514551', 'Lucas Martins Pereira', 'lucas.mpereira@email.com', '$2y$10$hyKjLPX1dPDBDRaVq5DAKOsHOCM0BVkzvUZizQFDkmsnLc2RMsBB6', 'Rua Paulo de Frontin, 95 - Centro, Nova Iguaçu/RJ - CEP 26215-310', '21991234455', '2026-11-19 17:13:06'),
('84066180787', 'Lukkas Carvalho', 'lukkasoliveira074@gmail.com', '$2y$10$Hg/T.YGJ3oXj/egxXVXTVuvObwSdLgir1LpRteidZTsLfumoIEEpe', 'rua joaquin 34 cabuçu 26290045', NULL, '2026-11-18 18:28:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada_item`
--

CREATE TABLE `entrada_item` (
  `id` int(11) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `custo_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `entrada_item`
--

INSERT INTO `entrada_item` (`id`, `entrada_id`, `variacao_id`, `quantidade`, `custo_unitario`) VALUES
(1, 1, 6, 4, 100.00),
(2, 2, 7, 12, 16.00),
(3, 2, 8, 10, 29.00),
(4, 2, 9, 8, 43.00),
(5, 2, 10, 6, 54.00),
(6, 2, 11, 4, 79.00),
(7, 2, 12, 12, 16.00),
(8, 2, 6, 10, 29.00),
(9, 2, 13, 8, 43.00),
(10, 2, 14, 6, 54.00),
(11, 2, 15, 4, 79.00),
(12, 2, 16, 12, 13.50),
(13, 2, 17, 10, 25.00),
(14, 2, 18, 8, 36.00),
(15, 2, 19, 6, 47.00),
(16, 2, 20, 4, 67.50),
(17, 2, 21, 12, 18.00),
(18, 2, 22, 10, 34.00),
(19, 2, 23, 8, 49.50),
(20, 2, 24, 6, 63.00),
(21, 2, 25, 4, 90.00),
(22, 2, 26, 12, 22.50),
(23, 2, 27, 10, 40.50),
(24, 2, 28, 8, 58.50),
(25, 2, 29, 6, 76.50),
(26, 2, 30, 4, 110.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada_mercadoria`
--

CREATE TABLE `entrada_mercadoria` (
  `id` int(11) NOT NULL,
  `fornecedor_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `status` enum('rascunho','confirmar') NOT NULL DEFAULT 'rascunho',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `entrada_mercadoria`
--

INSERT INTO `entrada_mercadoria` (`id`, `fornecedor_id`, `data`, `status`, `valor_total`) VALUES
(1, 1, '2026-11-19', 'rascunho', 400.00),
(2, 1, '2026-11-19', 'rascunho', 7916.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `minimo` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `variacao_id`, `quantidade`, `minimo`) VALUES
(1, 1, 10, 2),
(2, 2, 10, 2),
(3, 3, 10, 2),
(4, 4, 8, 2),
(5, 5, 10, 2),
(6, 6, 12, 5),
(7, 7, 8, 3),
(8, 8, 9, 3),
(9, 9, 7, 2),
(10, 10, 4, 2),
(11, 11, 4, 1),
(12, 12, 10, 3),
(13, 13, 7, 2),
(14, 14, 5, 2),
(15, 15, 4, 1),
(16, 16, 10, 3),
(17, 17, 8, 3),
(18, 18, 7, 2),
(19, 19, 5, 2),
(20, 20, 4, 1),
(21, 21, 11, 3),
(22, 22, 8, 3),
(23, 23, 7, 2),
(24, 24, 5, 2),
(25, 25, 2, 1),
(26, 26, 11, 3),
(27, 27, 8, 3),
(28, 28, 7, 2),
(29, 29, 5, 2),
(30, 30, 3, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedor`
--

CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedor`
--

INSERT INTO `fornecedor` (`id`, `nome`, `cnpj`, `telefone`, `email`, `endereco`, `cep`, `ativo`) VALUES
(1, 'Lukkas Carvalho', '20604215789123', '219655356733', 'lukkasoliveira074@gmail.com', 'Rua sem Nome, Palhada - Nova Iguaçu/RJ', '26290-045', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimento_estoque`
--

CREATE TABLE `movimento_estoque` (
  `id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `origem` enum('entrada','venda','pedido') NOT NULL,
  `origem_id` int(11) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movimento_estoque`
--

INSERT INTO `movimento_estoque` (`id`, `variacao_id`, `tipo`, `quantidade`, `origem`, `origem_id`, `data`) VALUES
(1, 4, 'saida', 2, 'pedido', 1, '2026-11-18 18:29:43'),
(2, 6, 'entrada', 4, 'entrada', 1, '2026-11-19 16:41:11'),
(3, 7, 'entrada', 12, 'entrada', 2, '2026-11-19 16:49:59'),
(4, 8, 'entrada', 10, 'entrada', 2, '2026-11-19 16:49:59'),
(5, 9, 'entrada', 8, 'entrada', 2, '2026-11-19 16:49:59'),
(6, 10, 'entrada', 6, 'entrada', 2, '2026-11-19 16:49:59'),
(7, 11, 'entrada', 4, 'entrada', 2, '2026-11-19 16:49:59'),
(8, 12, 'entrada', 12, 'entrada', 2, '2026-11-19 16:49:59'),
(9, 6, 'entrada', 10, 'entrada', 2, '2026-11-19 16:49:59'),
(10, 13, 'entrada', 8, 'entrada', 2, '2026-11-19 16:49:59'),
(11, 14, 'entrada', 6, 'entrada', 2, '2026-11-19 16:49:59'),
(12, 15, 'entrada', 4, 'entrada', 2, '2026-11-19 16:49:59'),
(13, 16, 'entrada', 12, 'entrada', 2, '2026-11-19 16:49:59'),
(14, 17, 'entrada', 10, 'entrada', 2, '2026-11-19 16:49:59'),
(15, 18, 'entrada', 8, 'entrada', 2, '2026-11-19 16:49:59'),
(16, 19, 'entrada', 6, 'entrada', 2, '2026-11-19 16:49:59'),
(17, 20, 'entrada', 4, 'entrada', 2, '2026-11-19 16:49:59'),
(18, 21, 'entrada', 12, 'entrada', 2, '2026-11-19 16:49:59'),
(19, 22, 'entrada', 10, 'entrada', 2, '2026-11-19 16:49:59'),
(20, 23, 'entrada', 8, 'entrada', 2, '2026-11-19 16:49:59'),
(21, 24, 'entrada', 6, 'entrada', 2, '2026-11-19 16:49:59'),
(22, 25, 'entrada', 4, 'entrada', 2, '2026-11-19 16:49:59'),
(23, 26, 'entrada', 12, 'entrada', 2, '2026-11-19 16:49:59'),
(24, 27, 'entrada', 10, 'entrada', 2, '2026-11-19 16:49:59'),
(25, 28, 'entrada', 8, 'entrada', 2, '2026-11-19 16:49:59'),
(26, 29, 'entrada', 6, 'entrada', 2, '2026-11-19 16:49:59'),
(27, 30, 'entrada', 4, 'entrada', 2, '2026-11-19 16:49:59'),
(34, 8, 'saida', 1, 'venda', 1, '2026-11-02 14:00:00'),
(35, 29, 'saida', 1, 'venda', 2, '2026-11-03 14:00:00'),
(36, 16, 'saida', 2, 'venda', 3, '2026-11-04 14:00:00'),
(37, 12, 'saida', 1, 'venda', 3, '2026-11-04 14:00:00'),
(39, 25, 'saida', 2, 'venda', 4, '2026-11-05 14:00:00'),
(40, 10, 'saida', 1, 'venda', 4, '2026-11-05 14:00:00'),
(42, 6, 'saida', 1, 'venda', 5, '2026-11-07 14:00:00'),
(43, 17, 'saida', 1, 'venda', 6, '2026-11-08 14:00:00'),
(44, 27, 'saida', 1, 'venda', 7, '2026-11-09 14:00:00'),
(45, 7, 'saida', 2, 'venda', 7, '2026-11-09 14:00:00'),
(47, 23, 'saida', 1, 'venda', 8, '2026-11-11 14:00:00'),
(48, 9, 'saida', 1, 'venda', 9, '2026-11-12 14:00:00'),
(49, 14, 'saida', 1, 'venda', 10, '2026-11-13 14:00:00'),
(50, 30, 'saida', 1, 'venda', 11, '2026-11-14 14:00:00'),
(51, 19, 'saida', 1, 'venda', 11, '2026-11-14 14:00:00'),
(53, 22, 'saida', 2, 'venda', 12, '2026-11-15 14:00:00'),
(54, 7, 'saida', 1, 'venda', 13, '2026-11-16 14:00:00'),
(55, 26, 'saida', 1, 'venda', 13, '2026-11-16 14:00:00'),
(57, 18, 'saida', 1, 'venda', 14, '2026-11-17 14:00:00'),
(58, 13, 'saida', 1, 'venda', 15, '2026-11-18 14:00:00'),
(59, 21, 'saida', 1, 'venda', 15, '2026-11-18 14:00:00'),
(61, 28, 'saida', 1, 'venda', 16, '2026-11-19 14:00:00'),
(62, 10, 'saida', 1, 'pedido', 2, '2026-11-06 17:13:06'),
(63, 27, 'saida', 1, 'pedido', 3, '2026-11-10 17:13:06'),
(64, 12, 'saida', 1, 'pedido', 3, '2026-11-10 17:13:06'),
(66, 24, 'saida', 1, 'pedido', 4, '2026-11-13 17:13:06'),
(67, 17, 'saida', 1, 'pedido', 5, '2026-11-17 17:13:06'),
(68, 7, 'saida', 1, 'pedido', 5, '2026-11-17 17:13:06'),
(70, 6, 'saida', 1, 'pedido', 6, '2026-11-18 17:13:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal_entrada`
--

CREATE TABLE `nota_fiscal_entrada` (
  `id` int(11) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `modelo` varchar(5) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `chave_acesso` varchar(44) NOT NULL,
  `data_emissao` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `nota_fiscal_entrada`
--

INSERT INTO `nota_fiscal_entrada` (`id`, `entrada_id`, `modelo`, `serie`, `numero`, `chave_acesso`, `data_emissao`, `valor_total`) VALUES
(1, 2, '55', '1', 'LK000002', '33262611000000000000000000000000000000000002', '2026-11-19', 7916.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal_venda`
--

CREATE TABLE `nota_fiscal_venda` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `modelo` varchar(5) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `data_emissao` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `nota_fiscal_venda`
--

INSERT INTO `nota_fiscal_venda` (`id`, `venda_id`, `modelo`, `serie`, `numero`, `data_emissao`, `valor_total`) VALUES
(1, 1, '65', '1', '1', '2026-11-02', 65.00),
(2, 2, '65', '1', '2', '2026-11-03', 170.00),
(3, 3, '65', '1', '3', '2026-11-04', 95.00),
(4, 4, '65', '1', '4', '2026-11-05', 520.00),
(5, 5, '65', '1', '5', '2026-11-07', 65.00),
(6, 7, '65', '1', '6', '2026-11-09', 160.00),
(7, 8, '65', '1', '7', '2026-11-11', 110.00),
(8, 9, '65', '1', '8', '2026-11-12', 95.00),
(9, 10, '65', '1', '9', '2026-11-13', 120.00),
(10, 11, '65', '1', '10', '2026-11-14', 350.00),
(11, 13, '65', '1', '11', '2026-11-16', 85.00),
(12, 14, '65', '1', '12', '2026-11-17', 80.00),
(13, 15, '65', '1', '13', '2026-11-18', 135.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `cliente_cpf` char(11) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  `forma_pagamento` enum('pix','cartao') NOT NULL DEFAULT 'pix',
  `endereco_entrega` varchar(255) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido`
--

INSERT INTO `pedido` (`id`, `cliente_cpf`, `data`, `status`, `forma_pagamento`, `endereco_entrega`, `valor_total`) VALUES
(1, '84066180787', '2026-11-18 18:29:43', 'pago', 'pix', 'rua joaquin 34 cabuçu 26290045', 70.00),
(2, '10033076634', '2026-11-06 17:13:06', 'entregue', 'pix', 'Rua Santos Dumont, 300 - Centro, Nova Iguaçu/RJ - CEP 26210-120', 120.00),
(3, '61934514551', '2026-11-10 17:13:06', 'entregue', 'cartao', 'Rua Paulo de Frontin, 95 - Centro, Nova Iguaçu/RJ - CEP 26215-310', 125.00),
(4, '06123100785', '2026-11-13 17:13:06', 'enviado', 'pix', 'Av. Nilo Peçanha, 1500 - Centro, Nova Iguaçu/RJ - CEP 26210-030', 140.00),
(5, '10033076634', '2026-11-17 17:13:06', 'pago', 'cartao', 'Rua Santos Dumont, 300 - Centro, Nova Iguaçu/RJ - CEP 26210-120', 90.00),
(6, '61934514551', '2026-11-18 17:13:06', 'pago', 'pix', 'Rua Paulo de Frontin, 95 - Centro, Nova Iguaçu/RJ - CEP 26215-310', 65.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_item`
--

CREATE TABLE `pedido_item` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_item`
--

INSERT INTO `pedido_item` (`id`, `pedido_id`, `variacao_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 4, 2, 35.00),
(2, 2, 10, 1, 120.00),
(3, 3, 27, 1, 90.00),
(4, 3, 12, 1, 35.00),
(5, 4, 24, 1, 140.00),
(6, 5, 17, 1, 55.00),
(7, 5, 7, 1, 35.00),
(8, 6, 6, 1, 65.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id`, `categoria_id`, `nome`, `descricao`, `ativo`) VALUES
(6, 1, 'ChocoDream', 'Bolo de chocolate decorado com frutas, com massa fofinha e sabor intenso de cacau.', 1),
(7, 1, 'Bolo de Nutella com Morango', 'Massa fofinha de chocolate com recheio e cobertura cremosa de Nutella, finalizado com morangos frescos. Intenso, molhadinho e irresistível.', 1),
(11, 2, 'Bolo de Cenoura', 'Massa fofinha de cenoura com cobertura de chocolate.', 1),
(12, 3, 'Bolo de Beijinho', 'Massa branca recheada com beijinho e coco ralado.', 1),
(13, 1, 'Bolo de Brigadeiro', 'Massa de chocolate com recheio e cobertura de brigadeiro.', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','vendedor') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `perfil`, `ativo`) VALUES
(1, 'Laura', 'laurinha1@gmail.com', '$2y$10$D2KkYXTKRb8ZNF2YMtXcpukmx5DCzmH/f7G44HgiTBoEf0.PMN3DK', 'vendedor', 1),
(2, 'Amanda', 'amandaramossoares1@gmail.com', '$2y$10$lEGGxijfdNrt8NqafmgOYONZqhHHSvDUM2WNhCC/asTQ9zZ4svy2a', 'admin', 1),
(4, 'Victória', 'trabalhosoficial9@gmail.com', '$2y$10$HZFXFHnjeBOg2.IXtwEmJutyeDu91AU9kHULq1Xk3VEYq6LhphSty', 'admin', 1),
(5, 'pedro', 'wnpedro162@gmail.com', '$2y$10$sVhNczuoNtiIayN6Oy3TE.sRroHF5RpZ9lsMOOanImWpn8nOBGaGG', 'admin', 1),
(6, 'Lukkas', 'lukkas@bolomania.com.br', '$2y$10$k2qdJ2QjpCu5uLR31rbw5uTVd8p8eFT5gsLSelqnRt/k4ydEQA6Yy', 'vendedor', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `variacao`
--

CREATE TABLE `variacao` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `tamanho` varchar(10) NOT NULL,
  `cor` varchar(30) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `variacao`
--

INSERT INTO `variacao` (`id`, `produto_id`, `tamanho`, `cor`, `sku`, `preco`) VALUES
(1, 6, 'Inteiro', 'Tradicional', 'BM-BOLO-5-INT', 50.00),
(2, 7, 'Inteiro', 'Nutella com morango', 'BM-BOLO-1-INT', 45.00),
(3, 11, 'Inteiro', 'Tradicional', 'BM-BOLO-4-INT', 35.00),
(4, 12, 'Inteiro', 'Tradicional', 'BM-BOLO-2-INT', 35.00),
(5, 13, 'Inteiro', 'Tradicional', 'BM-BOLO-3-INT', 35.00),
(6, 12, '1kg', 'beijinho', 'BOLO-12-1KG-BEIJINHO', 65.00),
(7, 13, '500g', 'Brigadeiro', 'BOLO-13-500G-BRIGADEIRO', 35.00),
(8, 13, '1kg', 'Brigadeiro', 'BOLO-13-1KG-BRIGADEIRO', 65.00),
(9, 13, '1,5kg', 'Brigadeiro', 'BOLO-13-15KG-BRIGADEIRO', 95.00),
(10, 13, '2kg', 'Brigadeiro', 'BOLO-13-2KG-BRIGADEIRO', 120.00),
(11, 13, '3kg', 'Brigadeiro', 'BOLO-13-3KG-BRIGADEIRO', 175.00),
(12, 12, '500g', 'Beijinho', 'BOLO-12-500G-BEIJINHO', 35.00),
(13, 12, '1,5kg', 'Beijinho', 'BOLO-12-15KG-BEIJINHO', 95.00),
(14, 12, '2kg', 'Beijinho', 'BOLO-12-2KG-BEIJINHO', 120.00),
(15, 12, '3kg', 'Beijinho', 'BOLO-12-3KG-BEIJINHO', 175.00),
(16, 11, '500g', 'Cenoura com chocolate', 'BOLO-11-500G-CENOURA', 30.00),
(17, 11, '1kg', 'Cenoura com chocolate', 'BOLO-11-1KG-CENOURA', 55.00),
(18, 11, '1,5kg', 'Cenoura com chocolate', 'BOLO-11-15KG-CENOURA', 80.00),
(19, 11, '2kg', 'Cenoura com chocolate', 'BOLO-11-2KG-CENOURA', 105.00),
(20, 11, '3kg', 'Cenoura com chocolate', 'BOLO-11-3KG-CENOURA', 150.00),
(21, 7, '500g', 'Nutella com morango', 'BOLO-7-500G-NUTELLA', 40.00),
(22, 7, '1kg', 'Nutella com morango', 'BOLO-7-1KG-NUTELLA', 75.00),
(23, 7, '1,5kg', 'Nutella com morango', 'BOLO-7-15KG-NUTELLA', 110.00),
(24, 7, '2kg', 'Nutella com morango', 'BOLO-7-2KG-NUTELLA', 140.00),
(25, 7, '3kg', 'Nutella com morango', 'BOLO-7-3KG-NUTELLA', 200.00),
(26, 6, '500g', 'Chocolate com frutas', 'BOLO-6-500G-CHOCO', 50.00),
(27, 6, '1kg', 'Chocolate com frutas', 'BOLO-6-1KG-CHOCO', 90.00),
(28, 6, '1,5kg', 'Chocolate com frutas', 'BOLO-6-15KG-CHOCO', 130.00),
(29, 6, '2kg', 'Chocolate com frutas', 'BOLO-6-2KG-CHOCO', 170.00),
(30, 6, '3kg', 'Chocolate com frutas', 'BOLO-6-3KG-CHOCO', 245.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda`
--

CREATE TABLE `venda` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `status` enum('aberta','finalizada') NOT NULL DEFAULT 'aberta',
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda`
--

INSERT INTO `venda` (`id`, `usuario_id`, `cliente_id`, `data`, `status`, `valor_total`) VALUES
(1, 6, 1, '2026-11-02', 'finalizada', 65.00),
(2, 6, 2, '2026-11-03', 'finalizada', 170.00),
(3, 6, 3, '2026-11-04', 'finalizada', 95.00),
(4, 6, 8, '2026-11-05', 'finalizada', 520.00),
(5, 6, 4, '2026-11-07', 'finalizada', 65.00),
(6, 6, NULL, '2026-11-08', 'finalizada', 55.00),
(7, 6, 5, '2026-11-09', 'finalizada', 160.00),
(8, 6, 6, '2026-11-11', 'finalizada', 110.00),
(9, 6, 7, '2026-11-12', 'finalizada', 95.00),
(10, 6, 1, '2026-11-13', 'finalizada', 120.00),
(11, 6, 8, '2026-11-14', 'finalizada', 350.00),
(12, 6, 3, '2026-11-15', 'finalizada', 150.00),
(13, 6, NULL, '2026-11-16', 'finalizada', 85.00),
(14, 6, 2, '2026-11-17', 'finalizada', 80.00),
(15, 6, 4, '2026-11-18', 'finalizada', 135.00),
(16, 6, 5, '2026-11-19', 'finalizada', 130.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda_item`
--

CREATE TABLE `venda_item` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `variacao_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda_item`
--

INSERT INTO `venda_item` (`id`, `venda_id`, `variacao_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 8, 1, 65.00),
(2, 2, 29, 1, 170.00),
(3, 3, 16, 2, 30.00),
(4, 3, 12, 1, 35.00),
(5, 4, 25, 2, 200.00),
(6, 4, 10, 1, 120.00),
(7, 5, 6, 1, 65.00),
(8, 6, 17, 1, 55.00),
(9, 7, 27, 1, 90.00),
(10, 7, 7, 2, 35.00),
(11, 8, 23, 1, 110.00),
(12, 9, 9, 1, 95.00),
(13, 10, 14, 1, 120.00),
(14, 11, 30, 1, 245.00),
(15, 11, 19, 1, 105.00),
(16, 12, 22, 2, 75.00),
(17, 13, 7, 1, 35.00),
(18, 13, 26, 1, 50.00),
(19, 14, 18, 1, 80.00),
(20, 15, 13, 1, 95.00),
(21, 15, 21, 1, 40.00),
(22, 16, 28, 1, 130.00);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente_site`
--
ALTER TABLE `cliente_site`
  ADD PRIMARY KEY (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `entrada_item`
--
ALTER TABLE `entrada_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entrada_item_entrada` (`entrada_id`),
  ADD KEY `idx_entrada_item_variacao` (`variacao_id`);

--
-- Índices de tabela `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entrada_fornecedor` (`fornecedor_id`),
  ADD KEY `idx_entrada_data` (`data`),
  ADD KEY `idx_entrada_status` (`status`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variacao_id` (`variacao_id`);

--
-- Índices de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mov_variacao` (`variacao_id`),
  ADD KEY `idx_mov_data` (`data`),
  ADD KEY `idx_mov_origem` (`origem`,`origem_id`),
  ADD KEY `idx_mov_tipo` (`tipo`);

--
-- Índices de tabela `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entrada_id` (`entrada_id`),
  ADD UNIQUE KEY `chave_acesso` (`chave_acesso`),
  ADD KEY `idx_nf_entrada_data` (`data_emissao`);

--
-- Índices de tabela `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `venda_id` (`venda_id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `idx_nf_venda_data` (`data_emissao`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_cliente` (`cliente_cpf`);

--
-- Índices de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_item_pedido` (`pedido_id`),
  ADD KEY `fk_pedido_item_variacao` (`variacao_id`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_categoria` (`categoria_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `variacao`
--
ALTER TABLE `variacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_variacao_produto` (`produto_id`);

--
-- Índices de tabela `venda`
--
ALTER TABLE `venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_usuario` (`usuario_id`),
  ADD KEY `idx_venda_cliente` (`cliente_id`),
  ADD KEY `idx_venda_data` (`data`),
  ADD KEY `idx_venda_status` (`status`);

--
-- Índices de tabela `venda_item`
--
ALTER TABLE `venda_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_item_venda` (`venda_id`),
  ADD KEY `idx_venda_item_variacao` (`variacao_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `entrada_item`
--
ALTER TABLE `entrada_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `pedido_item`
--
ALTER TABLE `pedido_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `variacao`
--
ALTER TABLE `variacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `venda`
--
ALTER TABLE `venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `venda_item`
--
ALTER TABLE `venda_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `entrada_item`
--
ALTER TABLE `entrada_item`
  ADD CONSTRAINT `fk_entrada_item_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `entrada_mercadoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entrada_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `entrada_mercadoria`
--
ALTER TABLE `entrada_mercadoria`
  ADD CONSTRAINT `fk_entrada_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedor` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `movimento_estoque`
--
ALTER TABLE `movimento_estoque`
  ADD CONSTRAINT `fk_movimento_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `nota_fiscal_entrada`
--
ALTER TABLE `nota_fiscal_entrada`
  ADD CONSTRAINT `fk_nf_entrada_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `entrada_mercadoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `nota_fiscal_venda`
--
ALTER TABLE `nota_fiscal_venda`
  ADD CONSTRAINT `fk_nf_venda_venda` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_cliente` FOREIGN KEY (`cliente_cpf`) REFERENCES `cliente_site` (`cpf`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido_item`
--
ALTER TABLE `pedido_item`
  ADD CONSTRAINT `fk_pedido_item_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedido_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `produto`
--
ALTER TABLE `produto`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `variacao`
--
ALTER TABLE `variacao`
  ADD CONSTRAINT `fk_variacao_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `venda`
--
ALTER TABLE `venda`
  ADD CONSTRAINT `fk_venda_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venda_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `venda_item`
--
ALTER TABLE `venda_item`
  ADD CONSTRAINT `fk_venda_item_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `variacao` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venda_item_venda` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
