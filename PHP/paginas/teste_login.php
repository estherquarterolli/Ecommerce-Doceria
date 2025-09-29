<?php
// Inclui o sistema de segurança e o modelo de usuário
require_once '../funcoes/seguranca.php';
require_once '../modelos/Usuario.php';

// 1. PROTEÇÃO: Esta é a função mais importante. 
// Ela verifica se o usuário está logado. Se não estiver, redireciona para a página de login.
Seguranca::requerLogin();

// 2. Se passou pela proteção, busca os dados do usuário logado na sessão
$usuarioAtual = Seguranca::usuarioAtual();
$usuarioModel = new Usuario();
$dadosUsuario = $usuarioModel->buscarPorId($usuarioAtual['id']);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste de Login - Sucesso!</title>
    <link rel="stylesheet" href="../../css/global.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; text-align: center; }
        .caixa-teste { padding: 40px; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="caixa-teste">
        <h1>Conexão com o banco e login funcionando!</h1>
        <?php if ($dadosUsuario): ?>
            <h2>Bem-vindo(a), <?= htmlspecialchars($dadosUsuario['nome']) ?>!</h2>
            <p>Seu email é: <?= htmlspecialchars($dadosUsuario['email']) ?></p>
        <?php else: ?>
            <p style="color: red;">Não foi possível buscar os dados completos do usuário.</p>
        <?php endif; ?>
        <br>
        <a href="logout.php">Sair (Logout)</a>
    </div>
</body>
</html>