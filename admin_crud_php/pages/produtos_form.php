<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

$nome = $descricao = $preco = $foto = '';
$categoria_id = $subcategoria_id = '';

// Buscar todas as categorias principais
$stmt = $pdo->query("SELECT id_categoria, nome FROM categoria WHERE id_categoria_pai IS NULL ORDER BY nome");
$categorias_principais = $stmt->fetchAll();

// Buscar todas as subcategorias
$stmt = $pdo->query("SELECT id_categoria, nome, id_categoria_pai FROM categoria WHERE id_categoria_pai IS NOT NULL ORDER BY nome");
$subcategorias = $stmt->fetchAll();

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { echo "Produto não encontrado."; exit; }
    $nome = $row['nome']; 
    $descricao = $row['descricao']; 
    $preco = $row['preco']; 
    $foto = $row['foto'];

    // Buscar categorias do produto
    $stmt = $pdo->prepare("
        SELECT c.id_categoria, c.id_categoria_pai 
        FROM produto_categoria pc 
        JOIN categoria c ON pc.id_categoria = c.id_categoria 
        WHERE pc.id_produto = ?
    ");
    $stmt->execute([$id]);
    $categorias_produto = $stmt->fetchAll();
    
    // Separar categoria principal e subcategoria
    foreach ($categorias_produto as $cat) {
        if ($cat['id_categoria_pai'] === null) {
            $categoria_id = $cat['id_categoria'];
        } else {
            $subcategoria_id = $cat['id_categoria'];
            // Se temos uma subcategoria, buscar também a categoria principal dela
            if (!$categoria_id) {
                $stmt_cat = $pdo->prepare("SELECT id_categoria_pai FROM categoria WHERE id_categoria = ?");
                $stmt_cat->execute([$subcategoria_id]);
                $categoria_pai = $stmt_cat->fetch();
                $categoria_id = $categoria_pai['id_categoria_pai'];
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = (float)($_POST['preco'] ?? 0);
    $foto = trim($_POST['foto'] ?? '');
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $subcategoria_id = (int)($_POST['subcategoria_id'] ?? 0);

    // Validar categoria
    if ($categoria_id <= 0) {
        die("Por favor, selecione uma categoria.");
    }

    if ($editing) {
        $stmt = $pdo->prepare("UPDATE produto SET nome=?, descricao=?, preco=?, foto=? WHERE id_produto=?");
        $stmt->execute([$nome, $descricao, $preco, $foto, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO produto (nome, descricao, preco, foto) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $preco, $foto]);
        $id = $pdo->lastInsertId();
    }

    // Atualizar categorias do produto
    // Primeiro remover todas as associações existentes
    $stmt = $pdo->prepare("DELETE FROM produto_categoria WHERE id_produto = ?");
    $stmt->execute([$id]);

    // Adicionar a categoria principal
    $stmt = $pdo->prepare("INSERT INTO produto_categoria (id_produto, id_categoria) VALUES (?, ?)");
    $stmt->execute([$id, $categoria_id]);

    // Adicionar a subcategoria se foi selecionada
    if ($subcategoria_id > 0) {
        $stmt->execute([$id, $subcategoria_id]);
    }

    header("Location: index.php?page=produtos_list");
    exit;
}
?>
<h1><?=$editing ? 'Editar' : 'Novo'?> Produto</h1>
<form method="post" class="grid">
    <label for="nome">Nome</label><br>
    <input id="nome" name="nome" required value="<?=htmlspecialchars($nome)?>"><br><br>

    <label for="preco">Preço</label><br>
    <input id="preco" name="preco" type="number" step="0.01" min="0" required value="<?=htmlspecialchars($preco)?>"><br><br>

    <label for="foto">Foto (URL)</label><br>
    <input id="foto" name="foto" value="<?=htmlspecialchars($foto)?>"><br><br>

    <label for="categoria_id">Categoria Principal</label><br>
    <select name="categoria_id" id="categoria_id" required onchange="carregarSubcategorias()">
        <option value="">— Selecione —</option>
        <?php foreach ($categorias_principais as $cat): ?>
            <option value="<?=$cat['id_categoria']?>" <?=$categoria_id == $cat['id_categoria'] ? 'selected' : ''?>>
                <?=htmlspecialchars($cat['nome'])?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="subcategoria_id">Subcategoria</label><br>
    <select name="subcategoria_id" id="subcategoria_id">
        <option value="">— Selecione —</option>
        <?php foreach ($subcategorias as $subcat): ?>
            <option value="<?=$subcat['id_categoria']?>"
                data-pai="<?=$subcat['id_categoria_pai']?>"
                <?=$subcategoria_id == $subcat['id_categoria'] ? 'selected' : ''?>
                style="display: none;">
                <?=htmlspecialchars($subcat['nome'])?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="descricao">Descrição</label><br>
    <textarea id="descricao" name="descricao"><?=htmlspecialchars($descricao)?></textarea><br><br>

    <button type="submit">Salvar</button>
    <a href="produtos_list.php">Cancelar</a>
</form>

<script>
function carregarSubcategorias() {
    var categoriaId = document.getElementById('categoria_id').value;
    var subcategoriaSelect = document.getElementById('subcategoria_id');
    
    // Habilitar/desabilitar o select de subcategoria
    subcategoriaSelect.disabled = !categoriaId;
    
    // Mostrar apenas as subcategorias da categoria selecionada
    for (var i = 0; i < subcategoriaSelect.options.length; i++) {
        var option = subcategoriaSelect.options[i];
        if (option.value === "") {
            option.style.display = ''; // Manter a opção padrão
            continue;
        }
        
        if (option.getAttribute('data-pai') == categoriaId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
            // Desmarcar se estava selecionada
            if (option.selected) {
                option.selected = false;
            }
        }
    }
}

// Disparar o evento ao carregar a página
window.onload = function() {
    carregarSubcategorias();
};
</script>