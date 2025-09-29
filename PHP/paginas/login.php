<?php
// Inclui os arquivos essenciais de uma única vez
require_once '../modelos/Usuario.php';
require_once '../funcoes/seguranca.php';

// --- BLOCO DE PROCESSAMENTO DO FORMULÁRIO ---
// Este código só executa se o formulário for enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Pega o email e a senha enviados
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Tenta fazer o login usando o modelo de Usuário
    $usuarioModel = new Usuario();
    $resultadoLogin = $usuarioModel->login($email, $senha);

    // Verifica se o login teve sucesso
    if (isset($resultadoLogin['sucesso'])) {
        // Se sim, inicia a sessão e redireciona para a página de teste
        Seguranca::login($resultadoLogin['usuario']);
        header("Location: teste_login.php");
        exit;
    } else {
        // Se não, redireciona de volta para esta mesma página com uma mensagem de erro
        header("Location: login.php?erro=login");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Zabeth's Gourmet</title>
    <link rel="stylesheet" href="../../css/global.css">
</head>
<body>

    <main class="container-principal">
        <aside class="coluna-ilustracao">
            <div class="conteudo-ilustracao">
                <img src="../../assets/logo-zabeths.png" alt="Logo Zabeth's Gourmet" class="logo-grande">
                <h1>Bem-vindo(a) de volta!</h1>
                <p>O sabor que você ama, a um clique de distância.</p>
            </div>
        </aside>

        <section class="coluna-formulario">
            <div class="conteudo-formulario">
                <img src="../../assets/logo-zabeths.png" alt="Logo Zabeth's Gourmet" class="logo-pequeno">
                
                <div id="mensagem-feedback"></div>

                <nav class="abas">
                    <button id="aba-login" class="aba ativa" type="button">LOGIN</button>
                    <button id="aba-cadastro" class="aba" type="button" onclick="window.location.href='cadastrar.php'">CRIAR CONTA</button>
                </nav>

                <form id="form-login" class="formulario ativo" action="login.php" method="POST">
                    <div class="grupo-input">
                        <label for="login-email">EMAIL</label>
                        <input type="text" id="login-email" name="email" required>
                    </div>
                    <div class="grupo-input">
                        <label for="login-senha">SENHA</label>
                        <input type="password" id="login-senha" name="senha" required>
                    </div>
                    <a href="#" class="link-esqueci-senha">ESQUECI MINHA SENHA</a>

                    <button type="submit" class="btn-principal">ENTRAR</button>
                    
                    </form>

                </div>
        </section>
    </main>

    <script src="../../js/login.js"></script>
</body>
</html>