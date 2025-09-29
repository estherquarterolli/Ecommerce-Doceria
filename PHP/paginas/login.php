<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Zabeth's Gourmet</title>
    <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>
    <main class="container-principal">
        <section class="coluna-formulario">
            <div class="conteudo-formulario">
                <img src="../assets/images/logo-zabeths.png" alt="Logo Zabeth's Gourmet" class="logo-pequeno">
                
                <!-- Área para mensagens do PHP -->
                <div id="mensagem-feedback" style="display: none;">
                    <div class="mensagem" id="mensagem-texto"></div>
                </div>

                <nav class="abas">
                    <button class="aba ativa" id="aba-entrar">ENTRAR</button>
                    <button class="aba" id="aba-cadastrar">CRIAR CONTA</button>
                </nav>

                <form method="post" action="processar_login.php" class="formulario ativo" id="form-entrar">
                    <input type="hidden" name="acao" value="login">
                    
                    <div class="grupo-input">
                        <label for="email">EMAIL</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="grupo-input">
                        <label for="senha">SENHA</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                    
                    <a href="recuperar_senha.html" class="link-esqueci-senha">ESQUECI MINHA SENHA</a>

                    <button type="submit" class="btn-principal">ENTRAR</button>

                    <div class="divisor">
                        <span>ou</span>
                    </div>

                    <a href="#" class="btn-social google">
                        <img src="https://img.icons8.com/color/16/000000/google-logo.png" alt="Google">
                        ENTRAR COM GOOGLE
                    </a>
                    <a href="#" class="btn-social whatsapp">
                        <img src="https://img.icons8.com/color/16/000000/whatsapp--v1.png" alt="WhatsApp">
                        ENTRAR COM WHATSAPP
                    </a>
                </form>

                <p style="text-align: center; margin-top: 20px;">
                    Não tem conta? <a href="cadastrar.html" style="color: var(--cor-principal);">Cadastrar</a>
                </p>
            </div>
        </section>
    </main>

    <script>
        // Mostrar mensagens do PHP (se houver)
        const urlParams = new URLSearchParams(window.location.search);
        const mensagem = urlParams.get('mensagem');
        const tipo = urlParams.get('tipo');
        
        if (mensagem) {
            const mensagemDiv = document.getElementById('mensagem-feedback');
            const mensagemTexto = document.getElementById('mensagem-texto');
            
            mensagemTexto.textContent = decodeURIComponent(mensagem);
            mensagemTexto.className = 'mensagem ' + (tipo === 'erro' ? 'erro' : 'sucesso');
            mensagemDiv.style.display = 'block';
        }

        // Alternar entre abas
        document.getElementById('aba-cadastrar').addEventListener('click', function() {
            window.location.href = 'cadastrar.html';
        });
    </script>
</body>
</html>