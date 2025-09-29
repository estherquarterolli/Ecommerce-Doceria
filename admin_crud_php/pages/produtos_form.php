<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

// Inicializar TODAS as variáveis
$nome = $descricao = $preco = $foto = '';
$categoria_id = $subcategoria_id = '';
$em_promocao = false;
$preco_original = $data_inicio_promocao = $data_fim_promocao = '';

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
    if (!$row) { 
        echo "Produto não encontrado."; 
        exit; 
    }
    
    // Preencher todas as variáveis com dados do banco
    $nome = $row['nome']; 
    $descricao = $row['descricao']; 
    $preco = $row['preco']; 
    $foto = $row['foto'];
    
    // Inicializar variáveis de promoção (verificar se existem no banco)
    $em_promocao = isset($row['em_promocao']) ? (bool)$row['em_promocao'] : false;
    $preco_original = $row['preco_original'] ?? '';
    $data_inicio_promocao = $row['data_inicio_promocao'] ?? '';
    $data_fim_promocao = $row['data_fim_promocao'] ?? '';

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
    
    // Capturar dados de promoção
    $em_promocao = isset($_POST['em_promocao']);
    $preco_original = !empty($_POST['preco_original']) ? (float)$_POST['preco_original'] : null;
    $data_inicio_promocao = !empty($_POST['data_inicio_promocao']) ? $_POST['data_inicio_promocao'] : null;
    $data_fim_promocao = !empty($_POST['data_fim_promocao']) ? $_POST['data_fim_promocao'] : null;

    // Validar categoria
    if ($categoria_id <= 0) {
        die("Por favor, selecione uma categoria.");
    }

    if ($editing) {
        // Verificar se as colunas de promoção existem na tabela
        $stmt = $pdo->prepare("
            UPDATE produto 
            SET nome=?, descricao=?, preco=?, foto=?, 
                em_promocao=?, preco_original=?, data_inicio_promocao=?, data_fim_promocao=?
            WHERE id_produto=?
        ");
        $stmt->execute([
            $nome, $descricao, $preco, $foto,
            $em_promocao, $preco_original, $data_inicio_promocao, $data_fim_promocao,
            $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO produto (nome, descricao, preco, foto, em_promocao, preco_original, data_inicio_promocao, data_fim_promocao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $nome, $descricao, $preco, $foto,
            $em_promocao, $preco_original, $data_inicio_promocao, $data_fim_promocao
        ]);
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
    <label>Nome<br><input name="nome" required value="<?=htmlspecialchars($nome)?>"></label>
    <label>Preço<br><input name="preco" type="number" step="0.01" min="0" required value="<?=htmlspecialchars($preco)?>"></label>
    <label>Foto (URL)<br><input name="foto" value="<?=htmlspecialchars($foto)?>"></label>

    <label>Em Promoção<br>
        <input type="checkbox" name="em_promocao" id="em_promocao" <?= $em_promocao ? 'checked' : '' ?> onchange="toggleCamposPromocao()">
        <label for="em_promocao">Produto em promoção</label>
    </label>

    <div id="campos_promocao" style="<?= $em_promocao ? '' : 'display: none;'?>">
        <label>Preço Original (R$)<br>
            <input name="preco_original" type="number" step="0.01" min="0" value="<?= htmlspecialchars($preco_original) ?>">
        </label>
        <label>Data de Início da Promoção<br>
            <input name="data_inicio_promocao" type="date" value="<?= htmlspecialchars($data_inicio_promocao) ?>">
        </label>
        <label>Data de Fim da Promoção<br>
            <input name="data_fim_promocao" type="date" value="<?= htmlspecialchars($data_fim_promocao) ?>">
        </label>
    </div>

    <label>Categoria Principal<br>
        <select name="categoria_id" id="categoria_id" required onchange="carregarSubcategorias()">
            <option value="">— Selecione —</option>
            <?php foreach ($categorias_principais as $cat): ?>
                <option value="<?=$cat['id_categoria']?>" <?=$categoria_id == $cat['id_categoria'] ? 'selected' : ''?>>
                    <?=htmlspecialchars($cat['nome'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Subcategoria<br>
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
        </select>
    </label>

    <label style="grid-column:1/-1">Descrição<br>
        <textarea name="descricao" rows="5"><?=htmlspecialchars($descricao)?></textarea>
    </label>
    
    <button class="btn primary" type="submit">Salvar</button>
    <a class="btn" href="index.php?page=produtos_list">Cancelar</a>
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

function toggleCamposPromocao() {
    const emPromocao = document.getElementById('em_promocao').checked;
    document.getElementById('campos_promocao').style.display = emPromocao ? 'block' : 'none';
}

// Disparar o evento ao carregar a página
window.onload = function() {
    carregarSubcategorias();
    toggleCamposPromocao(); // Garantir que os campos de promoção estejam no estado correto
};
</script>