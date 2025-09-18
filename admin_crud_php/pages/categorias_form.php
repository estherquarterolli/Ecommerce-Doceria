<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

$nome = $id_categoria_pai = '';

// Carregar todas as categorias para o dropdown de categoria pai
$stmt = $pdo->query("SELECT id_categoria, nome FROM categoria ORDER BY nome");
$todas_categorias = $stmt->fetchAll();

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM categoria WHERE id_categoria = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { echo "Categoria não encontrada."; exit; }
    $nome = $row['nome'];
    $id_categoria_pai = $row['id_categoria_pai'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $id_categoria_pai = !empty($_POST['id_categoria_pai']) ? (int)$_POST['id_categoria_pai'] : null;

    // Validar para evitar que uma categoria seja pai de si mesma
    if ($id_categoria_pai === $id) {
        die("Uma categoria não pode ser pai de si mesma.");
    }

    if ($editing) {
        $stmt = $pdo->prepare("UPDATE categoria SET nome=?, id_categoria_pai=? WHERE id_categoria=?");
        $stmt->execute([$nome, $id_categoria_pai, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categoria (nome, id_categoria_pai) VALUES (?, ?)");
        $stmt->execute([$nome, $id_categoria_pai]);
    }
    header("Location: index.php?page=categorias_list");
    exit;
}
?>
<h1><?=$editing ? 'Editar' : 'Nova'?> Categoria</h1>
<form method="post" class="grid">
    <label>Nome<br><input name="nome" required value="<?=htmlspecialchars($nome)?>"></label>
    
    <label>Categoria Pai<br>
        <select name="id_categoria_pai">
            <option value="">— Nenhuma (categoria principal) —</option>
            <?php foreach ($todas_categorias as $cat): ?>
                <?php if ($editing && $cat['id_categoria'] == $id) continue; // Não permitir selecionar a si mesma ?>
                <option value="<?=$cat['id_categoria']?>" <?=$id_categoria_pai == $cat['id_categoria'] ? 'selected' : ''?>>
                    <?=htmlspecialchars($cat['nome'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button class="btn primary" type="submit">Salvar</button>
    <a class="btn" href="index.php?page=categorias_list">Cancelar</a>
</form>