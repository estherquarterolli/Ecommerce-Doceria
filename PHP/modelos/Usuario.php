<?php
require_once '../conexao/banco.php';

class Usuario
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = ConexaoBanco::conectar();
    }

    // Criar novo usuário
    public function criar($nome, $email, $telefone, $senha)
    {
        try {
            // Verificar se email já existe
            $verificar = $this->pdo->prepare("SELECT id_cliente FROM cliente WHERE email = ?");
            $verificar->execute([$email]);

            if ($verificar->fetch()) {
                return ["erro" => "Este email já está cadastrado."];
            }

            // Criptografar senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Inserir no banco
            $inserir = $this->pdo->prepare("
                INSERT INTO cliente (nome, email, telefone, senha) 
                VALUES (?, ?, ?, ?)
            ");
            $inserir->execute([$nome, $email, $telefone, $senhaHash]);

            return ["sucesso" => "Conta criada com sucesso!", "id" => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ["erro" => "Erro ao criar conta: " . $e->getMessage()];
        }
    }

    // Fazer login
    public function login($email, $senha)
    {
        try {
            $buscar = $this->pdo->prepare("
                SELECT id_cliente, nome, email, senha 
                FROM cliente 
                WHERE email = ?
            ");
            $buscar->execute([$email]);
            $usuario = $buscar->fetch();

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                return [
                    "sucesso" => true,
                    "usuario" => [
                        "id" => $usuario['id_cliente'],
                        "nome" => $usuario['nome'],
                        "email" => $usuario['email']
                    ]
                ];
            } else {
                return ["erro" => "Email ou senha incorretos."];
            }
        } catch (PDOException $e) {
            return ["erro" => "Erro ao fazer login: " . $e->getMessage()];
        }
    }

    // Buscar usuário por ID
    public function buscarPorId($id)
    {
        try {
            $buscar = $this->pdo->prepare("
                SELECT id_cliente, nome, email, telefone 
                FROM cliente 
                WHERE id_cliente = ?
            ");
            $buscar->execute([$id]);
            return $buscar->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
}
