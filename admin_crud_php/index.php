<?php
require_once __DIR__ . '/config.php';
require_login();

$allowed_pages = [
    'clientes_list', 'clientes_form', 'clientes_delete',
    'produtos_list', 'produtos_form', 'produtos_delete',
    'pedidos_list', 'itens_pedido_list',
    'categorias_list', 'categorias_form', 'categorias_delete',
    'cupons_list', 'cupons_form', 'cupons_delete', 'cupons_produtos'
];

$page = $_GET['page'] ?? 'clientes_list';
if (!in_array($page, $allowed_pages, true)) $page = 'clientes_list';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Painel</title></head>
<body>
<p>Bem-vindo, <?=htmlspecialchars($_SESSION['admin_email'] ?? '')?> | <a href="logout.php">Sair</a></p>
<nav>
  <a href="index.php?page=clientes_list">Clientes</a> |
  <a href="index.php?page=produtos_list">Produtos</a> |
  <a href="index.php?page=cupons_list">Cupons</a> |
  <a href="index.php?page=categorias_list">Categorias</a> |
  <a href="index.php?page=pedidos_list">Pedidos</a>
</nav>
<hr>
<?php require __DIR__ . "/pages/{$page}.php"; ?>
</body>
</html>
