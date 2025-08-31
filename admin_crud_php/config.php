<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// valores padrão (fallback se não tiver config.json)
$cfg = [
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'sua_base',
    'db_user' => 'root',
    'db_pass' => ''
];

// se existir config.json, sobrescreve os valores
$configFile = __DIR__ . '/config.json';
if (is_file($configFile)) {
    $json = json_decode(file_get_contents($configFile), true);
    if (is_array($json)) {
        $cfg = array_merge($cfg, $json);
    }
}

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $cfg['db_host'], $cfg['db_port'], $cfg['db_name']
);

try {
    $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Falha ao conectar no banco: ' . htmlspecialchars($e->getMessage()));
}

// helper pra proteger páginas
function require_login(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}
