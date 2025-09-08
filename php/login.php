<?php
session_start();
require 'conexao.php';
<!--Fazer sanitização dos dados !-->
$email = $_POST['email-ou-telefone'];
$senha = $_POST['senha-login'];

if (empty($email) || empty($senha)) {
    header("Location: ../php/home.php?erro=login");
    exit();
}

$sql = "SELECT id_cliente, nome, senha FROM cliente WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    if (password_verify($senha, $usuario['senha'])) {
        // SUCESSO: Guarda na sessão e redireciona para a home
        $_SESSION['id_cliente'] = $usuario['id_cliente'];
        $_SESSION['nome_cliente'] = $usuario['nome'];
        $_SESSION['logado'] = true;
        header("Location: ../php/home.php"); //alterei o home pra .php
        exit();
    }
}

// ERRO: Se chegou até aqui, o login falhou. Redireciona de volta com erro.
header("Location: ../php/home.php?erro=login");

$stmt->close();
$conn->close();
exit();
?>
