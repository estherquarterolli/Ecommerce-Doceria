<?php

$servername = "localhost"; // O servidor do banco de dados
$username = "root";        // Usuário padrão do MySQL no XAMPP
$password = "";            // Senha padrão do MySQL no XAMPP é vazia
$dbname = "zabethsgourmet"; // O nome do seu banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão falhou
if ($conn->connect_error) {
    // Se falhar, mata a execução e exibe o erro
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

//Define o charset para UTF-8 para evitar problemas com acentos
$conn->set_charset("utf8");
?>