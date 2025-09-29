<?php
class ConexaoBanco
{
    public static function conectar()
    {
        // 1. Define valores padrão como fallback
        $config = [
            'db_host' => 'localhost',
            'db_port' => '3306',
            'db_name' => 'zabeths_gourmet_db',
            'db_user' => 'root',
            'db_pass' => ''
        ];

        // 2. Define o caminho para o arquivo de configuração
        // Ele vai procurar por 'config.json' na pasta 'Ecommerce-Doceria/PHP/'
        $configFile = __DIR__ . '/../config.json';

        // 3. Se o config.json existir, lê e substitui os valores padrão
        if (file_exists($configFile)) {
            $jsonConfig = json_decode(file_get_contents($configFile), true);
            if (is_array($jsonConfig)) {
                $config = array_merge($config, $jsonConfig);
            }
        }

        // 4. Monta a string de conexão (DSN) com os dados da configuração
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['db_host'],
            $config['db_port'],
            $config['db_name']
        );

        try {
            // 5. Cria a conexão PDO
            $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            // Em caso de erro, exibe uma mensagem genérica por segurança
            error_log("Erro de conexão com o banco: " . $e->getMessage());
            die("Erro ao conectar com o servidor. Tente novamente mais tarde.");
        }
    }
}