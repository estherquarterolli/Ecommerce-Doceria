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
        header("Location: ../../integracao/entrar.php");
        exit;
    }

    public static function requerLogin()
    {
        if (!self::usuarioLogado()) {
            header("Location: ../../integracao/entrar.php");
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
