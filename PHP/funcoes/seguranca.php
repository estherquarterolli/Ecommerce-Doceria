<?php
class Seguranca
{

    public static function iniciarSessao()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function usuarioLogado()
    {
        self::iniciarSessao();
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }

    public static function login($usuario)
    {
        self::iniciarSessao();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
    }

    public static function logout()
    {
        self::iniciarSessao();
        session_destroy();
        // CORREÇÃO: O caminho correto para o login a partir da pasta 'funcoes'
        header("Location: ../paginas/login.php");
        exit;
    }

    public static function requerLogin()
    {
        if (!self::usuarioLogado()) {
            // CORREÇÃO: O caminho correto para o login
            header("Location: ../paginas/login.php");
            exit;
        }
    }

    // --- NOVA FUNÇÃO ---
    // Impede que usuários logados acessem páginas como login e cadastro
    public static function requerLogout()
    {
        if (self::usuarioLogado()) {
            // CORREÇÃO: Redireciona para a home se já estiver logado
            header("Location: ../paginas/home.php");
            exit;
        }
    }


    public static function usuarioAtual()
    {
        self::iniciarSessao();
        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nome' => $_SESSION['usuario_nome'] ?? null,
            'email' => $_SESSION['usuario_email'] ?? null
        ];
    }
}