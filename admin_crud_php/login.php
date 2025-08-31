<?php
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin_usuario WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['admin_id'] = $user['id_admin'];
        $_SESSION['admin_email'] = $user['email'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Email ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Login</title></head>
<body>
<h1>Login do Admin</h1>
<form method="post">
    <label>Email <input type="email" name="email" required></label><br>
    <label>Senha <input type="password" name="senha" required></label><br>
    <?php if ($error): ?><p style="color:red"><?=$error?></p><?php endif; ?>
    <button type="submit">Entrar</button>
</form>
</body>
</html>
