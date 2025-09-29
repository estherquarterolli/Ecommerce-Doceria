<?php
class ConexaoBanco
{
    public static function conectar()
    {
        $servidor = 'localhost';
        $banco = 'zabeths_gourmet_db';
        $usuario = 'root';
        $senha = '';

        try {
            $pdo = new PDO("mysql:host=$servidor;dbname=$banco;charset=utf8", $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            echo "Erro ao conectar: " . $e->getMessage();
            exit;
        }
    }
}
