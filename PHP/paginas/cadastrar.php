<?php
// 1. Inclui os arquivos necessários
require_once '../modelos/Usuario.php';
require_once '../funcoes/seguranca.php';
require_once '../funcoes/validar.php'; // Incluído para validar os dados

// Se o usuário já estiver logado, redireciona para a home
Seguranca::requerLogout();

$erros = [];
$dados_submetidos = [];

// 2. Bloco de processamento do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda os dados submetidos para preencher o formulário em caso de erro
    $dados_submetidos = $_POST;

    // 3. Valida os dados usando a sua classe Validar
    $erros = Validar::dadosCadastro($dados_submetidos);

    // 4. Se não houver erros de validação...
    if (empty($erros)) {
        $usuarioModel = new Usuario();
        
        // Tenta criar o usuário no banco
        $resultado = $usuarioModel->criar(
            $dados_submetidos['nome'],
            $dados_submetidos['email'],
            $dados_submetidos['telefone'],
            $dados_submetidos['senha']
        );

        // Se a criação foi bem-sucedida...
        if (isset($resultado['sucesso'])) {
            // Faz o login automático
            $login = $usuarioModel->login($dados_submetidos['email'], $dados_submetidos['senha']);
            if (isset($login['sucesso'])) {
                Seguranca::login($login['usuario']);
                // Redireciona para a página de teste para confirmar que tudo funcionou
                header("Location: teste_login.php");
                exit;
            }
        } else {
            // Se o usuário já existe, adiciona o erro
            $erros['geral'] = $resultado['erro'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta | Zabeth's Gourmet</title>
    <link rel="stylesheet" href="../../css/global.css">
</head>
<body>
    <main class="container-principal">
        <aside class="coluna-ilustracao">
            <div class="conteudo-ilustracao">
                <img src="../../assets/logo-zabeths.png" alt="Logo Zabeth's Gourmet" class="logo-grande">
                <h1>Crie sua conta</h1>
                <p>E tenha acesso aos doces mais incríveis da região.</p>
            </div>
        </aside>

        <section class="coluna-formulario">
            <div class="conteudo-formulario">
                <img src="../../assets/logo-zabeths.png" alt="Logo Zabeth's Gourmet" class="logo-pequeno">

                <div id="mensagem-feedback">
                    <?php if (!empty($erros)): ?>
                        <div class="mensagem erro">
                            <?php 
                                // Exibe o primeiro erro encontrado
                                echo htmlspecialchars(array_values($erros)[0]); 
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <nav class="abas">
                    <button class="aba" onclick="location.href='login.php'">ENTRAR</button>
                    <button class="aba ativa">CRIAR CONTA</button>
                </nav>

                <form method="post" action="cadastrar.php" class="formulario ativo">
                    <div class="grupo-input">
                        <label for="nome">NOME COMPLETO</label>
                        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($dados_submetidos['nome'] ?? '') ?>">
                    </div>

                    <div class="grupo-input">
                        <label for="telefone">TELEFONE</label>
                        <input type="tel" id="telefone" name="telefone" required value="<?= htmlspecialchars($dados_submetidos['telefone'] ?? '') ?>">
                    </div>

                    <div class="grupo-input">
                        <label for="email">EMAIL</label>
                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($dados_submetidos['email'] ?? '') ?>">
                    </div>

                    <div class="grupo-input">
                        <label for="senha">SENHA</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>

                    <div class="grupo-termos">
                        <input type="checkbox" id="termos" name="termos" required>
                        <label for="termos">Aceito os Termos e Condições</label>
                    </div>

                    <button type="submit" class="btn-principal">CRIAR CONTA</button>
                </form>

                <p style="text-align: center; margin-top: 20px;">
                    Já tem conta? <a href="login.php" style="color: var(--cor-principal);">Entrar</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>