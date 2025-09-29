<?php
class Validar
{

    // Validar email
    public static function email($email)
    {
        if (empty($email)) {
            return "O email é obrigatório.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email inválido.";
        }

        return true;
    }

    // Validar telefone
    public static function telefone($telefone)
    {
        if (empty($telefone)) {
            return "O telefone é obrigatório.";
        }

        // Remover caracteres não numéricos
        $telefone = preg_replace('/[^0-9]/', '', $telefone);

        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            return "Telefone deve ter 10 ou 11 dígitos.";
        }

        return true;
    }

    // Validar nome
    public static function nome($nome)
    {
        if (empty($nome)) {
            return "O nome é obrigatório.";
        }

        if (strlen($nome) < 2) {
            return "Nome muito curto.";
        }

        return true;
    }

    // Validar senha
    public static function senha($senha)
    {
        if (empty($senha)) {
            return "A senha é obrigatória.";
        }

        if (strlen($senha) < 6) {
            return "Senha deve ter pelo menos 6 caracteres.";
        }

        return true;
    }

    // Validar todos os dados de cadastro
    public static function dadosCadastro($dados)
    {
        $erros = [];

        // Validar nome
        $nomeValido = self::nome($dados['nome'] ?? '');
        if ($nomeValido !== true) {
            $erros['nome'] = $nomeValido;
        }

        // Validar email
        $emailValido = self::email($dados['email'] ?? '');
        if ($emailValido !== true) {
            $erros['email'] = $emailValido;
        }

        // Validar telefone
        $telefoneValido = self::telefone($dados['telefone'] ?? '');
        if ($telefoneValido !== true) {
            $erros['telefone'] = $telefoneValido;
        }

        // Validar senha
        $senhaValida = self::senha($dados['senha'] ?? '');
        if ($senhaValida !== true) {
            $erros['senha'] = $senhaValida;
        }

        return $erros;
    }
}
