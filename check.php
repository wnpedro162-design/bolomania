<?php

require_once 'config/db.php';

try{

     $db = Database::getConnection();

     $query = $db->query("SHOW TABLES");
     $tabelasEncontradas = $query->fetchAll(PDO::FETCH_COLUMN);

     $total = count($tabelasEncontradas);
     $meta = 17; // 14 do sistema + cliente_site, pedido e pedido_item (loja virtual)

     echo "<h1> Verificação do sistema</h1>";

     if ($total == $meta) {
        echo "<p style='color: green; '>sucesso! O banco de dados está completo com as 17 tabelas.</p>";
     } else {
        $faltando = $meta - $total;
        echo "<p style='color: red; '>Atenção: O banco possui $total tabelas. Estão <strong>faltando $faltando</strong> para chegar às 17. Rode o arquivo sql/atualizacao_parte2.sql no phpMyAdmin.</p>";
     }

     echo "<h3>Tabelas encontradas no banco:</h3>";
     echo "<ul>";
     foreach ($tabelasEncontradas as $tabela) {
        echo "<li>$tabela</li>";
     }
     echo "</ul>";

     } catch (Exception $e) {
        echo"erro ao carregar o teste: ". $e->getMessage();
     }