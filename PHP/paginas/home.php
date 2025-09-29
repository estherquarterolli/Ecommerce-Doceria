<?php
require_once '../funcoes/seguranca.php';
require_once '../modelos/Usuario.php';

// Verificar se está logado
Seguranca::requerLogin();

$usuarioModel = new Usuario();
$usuarioAtual = Seguranca::usuarioAtual();
$dadosUsuario = $usuarioModel->buscarPorId($usuarioAtual['id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início | Zabeth's Gourmet</title>
    <link rel="stylesheet" href="../../css/global.css">
</head>

<body>
    <div class="container-home">
        <header class="header-home">
            <div class="area-usuario">
                <span class="saudacao-usuario">Olá, <?= htmlspecialchars($dadosUsuario['nome']) ?>!</span>
                <a href="logout.php" class="btn-login-header">Sair</a>
            </div>

            <img src="../../assets/logo-zabeths.png" alt="Logo Zabeth's Gourmet" class="logo-home">

            <nav class="nav-home">
                <a href="cardapio.php" class="btn-nav">Cardápio</a>
                <a href="sobre.php" class="btn-nav">Sobre Nós</a>
                <a href="carrinho.php" class="btn-nav">Carrinho</a>
            </nav>
        </header>

        <main>
            <section class="banner-container">
                <div class="banner-slides">
                    <div class="slide">
                        <img src="../../assets/banner1.jpg" alt="Promoção especial">
                    </div>
                </div>
            </section>

            <section class="secao-produtos">
                <h2 class="titulo-secao">Produtos em Destaque</h2>
                <div class="grade-produtos">
                    <div style="text-align: center; padding: 40px;">
                        <p>Em breve: nossos deliciosos produtos!</p>
                        <a href="cardapio.php" class="btn-principal" style="display: inline-block; margin-top: 20px;">
                            Ver Cardápio Completo
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer-fixo">
            <div class="info-loja">
                <span id="status-loja">ABERTO ATÉ ÀS 23:00h</span>
            </div>
            <a href="carrinho.php" class="btn-carrinho">
                🛒
                <span class="contador-carrinho" id="contador-carrinho">0</span>
            </a>
        </footer>
    </div>

    <script src="../../js/home.js"></script>
</body>

</html>