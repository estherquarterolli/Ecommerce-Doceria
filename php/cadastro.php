<?php
require 'conexao.php';

$nome = $_POST['nome-completo'];
$telefone = $_POST['telefone-cadastro'];
$email = $_POST['email-cadastro'];
$senha = $_POST['senha-cadastro'];

if (empty($nome) || empty($telefone) || empty($email) || empty($senha)) {
    // Redireciona de volta para a aba de cadastro com uma mensagem genérica (não implementada no JS, mas poderia ser)
    header("Location: ../html/login.html?aba=cadastro&erro=campos");
    exit();
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO cliente (nome, telefone, email, senha) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $telefone, $email, $senha_hash);

if ($stmt->execute()) {
    // SUCESSO: Redireciona para a aba de login com mensagem de sucesso
    header("Location: ../html/login.html?sucesso=cadastro");
} else {
    // ERRO: Redireciona de volta para a aba de cadastro com mensagem de erro
    if ($conn->errno == 1062) { // Erro de entrada duplicada (email)
        header("Location: ../html/login.html?aba=cadastro&erro=email");
    } else {
        header("Location: ../html/login.html?aba=cadastro&erro=generico");
    }
}

$stmt->close();
$conn->close();
exit(); // Garante que o script pare após o redirecionamento
?>