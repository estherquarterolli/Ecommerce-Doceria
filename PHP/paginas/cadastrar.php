<?php
require_once '../funcoes/seguranca.php';
require_once '../modelos/Usuario.php';
require_once '../funcoes/validar.php';

// Se já estiver logado, vai para a página inicial
Seguranca::requerLogout();

$usuarioModel = new Usuario();
$mensagem = '';
$tipoMensagem = '';

// Processar formulário
if ($_POST) {
    $dados = Seguranca::limpar($_POST);

    // Validar dados
    $erros = Validar::dadosCadastro($dados);

    if (empty($erros)) {
        // Tentar criar usuário
        $resultado = $usuarioModel->criar(
            $dados['nome'],
            $dados['email'],
            $dados['telefone'],
            $dados['senha']
        );

        if (isset($resultado['sucesso'])) {
            // Login automático após cadastro
            $login = $usuarioModel->login($dados['email'], $dados['senha']);
            if (isset($login['sucesso'])) {
                Seguranca::login($login['usuario']);
                header("Location: inicio.php");
                exit;
            }
        } else {
            $mensagem = $resultado['erro'];
            $tipoMensagem = 'erro';
        }
    } else {
        $mensagem = implode(' ', $erros);
        $tipoMensagem = 'erro';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar | Zabeth's Gourmet</title>
    <link rel="stylesheet" href="../../css/global.css">
</head>

<body>
    <main class="container-principal">
        <section class="coluna-formulario">
            <div class="conteudo-formulario">
                <img src="../../assets/logo-zabeths.png" alt="Logo Zabeth's Gourmet" class="logo-pequeno">

                <?php if ($mensagem): ?>
                    <div id="mensagem-feedback">
                        <div class="mensagem <?= $tipoMensagem === 'erro' ? 'erro' : 'sucesso' ?>">
                            <?= $mensagem ?>
                        </div>
                    </div>
                <?php endif; ?>

                <nav class="abas">
                    <button class="aba" onclick="location.href='entrar.php'">ENTRAR</button>
                    <button class="aba ativa">CRIAR CONTA</button>
                </nav>

                <form method="post" class="formulario ativo">
                    <div class="grupo-input">
                        <label for="nome">NOME COMPLETO</label>
                        <input type="text" id="nome" name="nome" required
                            value="<?= $_POST['nome'] ?? '' ?>">
                    </div>

                    <div class="grupo-input">
                        <label for="telefone">TELEFONE</label>
                        <input type="tel" id="telefone" name="telefone" required
                            value="<?= $_POST['telefone'] ?? '' ?>">
                    </div>

                    <div class="grupo-input">
                        <label for="email">EMAIL</label>
                        <input type="email" id="email" name="email" required
                            value="<?= $_POST['email'] ?? '' ?>">
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
                    Já tem conta? <a href="entrar.php" style="color: var(--cor-principal);">Entrar</a>
                </p>
            </div>
        </section>
    </main>
</body>

</html>