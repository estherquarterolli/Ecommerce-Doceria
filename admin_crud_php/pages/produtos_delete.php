<?php

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM produto WHERE id_produto = ?");
    $stmt->execute([$id]);
}
header("Location: index.php?page=produtos_list");
exit;
?>