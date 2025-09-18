<?php
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // Verificar se a categoria tem subcategorias
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categoria WHERE id_categoria_pai = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        die("Não é possível excluir esta categoria porque ela possui subcategorias.");
    }
    
    // Verificar se a categoria está sendo usada por produtos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produto_categoria WHERE id_categoria = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        die("Não é possível excluir esta categoria porque ela está associada a produtos.");
    }
    
    // Excluir a categoria
    $stmt = $pdo->prepare("DELETE FROM categoria WHERE id_categoria = ?");
    $stmt->execute([$id]);
}
header("Location: index.php?page=categorias_list");
exit;
?>