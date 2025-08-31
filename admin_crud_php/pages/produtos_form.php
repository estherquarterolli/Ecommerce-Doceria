<?php
// Lembre-se que este arquivo é incluído pelo index.php, então $pdo já existe.
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

$nome = $descricao = $preco = $foto = $categoria1 = $categoria2 = $categoria3 = '';

// Opções para as categorias (você pode carregar do banco de dados no futuro)
$categorias1_opts = ["sem_categoria", "Doces", "Salgados", "Bebidas"];
$categorias2_opts = ["(nenhuma)", "Tortas", "Cupcakes", "Refrigerantes", "Sucos Naturais"];
$categorias3_opts = ["(nenhuma)", "Zero Açúcar", "Festa", "Premium", "Infantil"];

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { echo "Produto não encontrado."; exit; }
    // Preenche as variáveis com os dados do banco
    $nome = $row['nome']; $descricao = $row['descricao']; $preco = $row['preco']; $foto = $row['foto'];
    $categoria1 = $row['categoria1']; $categoria2 = $row['categoria2']; $categoria3 = $row['categoria3'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = (float)($_POST['preco'] ?? 0);
    $foto = trim($_POST['foto'] ?? '');
    $categoria1 = trim($_POST['categoria1'] ?? 'sem_categoria');
    $categoria2 = trim($_POST['categoria2'] ?? '');
    $categoria3 = trim($_POST['categoria3'] ?? '');

    if ($editing) {
        $stmt = $pdo->prepare(
            "UPDATE produto SET nome=?, descricao=?, preco=?, foto=?, categoria1=?, categoria2=?, categoria3=? WHERE id_produto=?"
        );
        $stmt->execute([$nome, $descricao, $preco, $foto, $categoria1, $categoria2, $categoria3, $id]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO produto (nome, descricao, preco, foto, categoria1, categoria2, categoria3) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nome, $descricao, $preco, $foto, $categoria1, $categoria2, $categoria3]);
    }
    header("Location: index.php?page=produtos_list");
    exit;
}
?>
<h1><?=$editing ? 'Editar' : 'Novo'?> Produto</h1>
<form method="post" class="grid">
    <label>Nome<br><input name="nome" required value="<?=htmlspecialchars($nome)?>"></label>
    <label>Preço<br><input name="preco" type="number" step="0.01" min="0" required value="<?=htmlspecialchars($preco)?>"></label>
    <label>Foto (URL)<br><input name="foto" value="<?=htmlspecialchars($foto)?>"></label>

    <label>Categoria 1<br>
        <select name="categoria1" required>
            <?php foreach ($categorias1_opts as $c): ?>
                <option value="<?=$c?>" <?=$categoria1 === $c ? 'selected' : ''?>><?=htmlspecialchars($c)?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Categoria 2<br>
        <select name="categoria2">
            <?php foreach ($categorias2_opts as $c): ?>
                <option value="<?=$c?>" <?=$categoria2 === $c ? 'selected' : ''?>><?=htmlspecialchars($c)?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Categoria 3<br>
        <select name="categoria3">
            <?php foreach ($categorias3_opts as $c): ?>
                <option value="<?=$c?>" <?=$categoria3 === $c ? 'selected' : ''?>><?=htmlspecialchars($c)?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label style="grid-column:1/-1">Descrição<br>
        <textarea name="descricao" rows="5"><?=htmlspecialchars($descricao)?></textarea>
    </label>
    
    <button class="btn primary" type="submit">Salvar</button>
    <a class="btn" href="index.php?page=produtos_list">Cancelar</a>
</form>