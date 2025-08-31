<?php

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {

    $stmt = $pdo->prepare("DELETE FROM endereco WHERE id_cliente = ?");
    $stmt->execute([$id]);

    $stmt = $pdo->prepare("DELETE FROM cliente WHERE id_cliente = ?");
    $stmt->execute([$id]);
}
header("Location: index.php?page=clientes_list");
exit;
?>