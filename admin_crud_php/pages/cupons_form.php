<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

$codigo = $descricao = $tipo_desconto = $valor_desconto = $data_inicio = $data_fim = '';
$uso_maximo = $ativo = $aplicacao_automatica = '';

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM cupom WHERE id_cupom = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { echo "Cupom não encontrado."; exit; }
    
    $codigo = $row['codigo'];
    $descricao = $row['descricao'];
    $tipo_desconto = $row['tipo_desconto'];
    $valor_desconto = $row['valor_desconto'];
    $data_inicio = $row['data_inicio'];
    $data_fim = $row['data_fim'];
    $uso_maximo = $row['uso_maximo'];
    $ativo = $row['ativo'];
    $aplicacao_automatica = $row['aplicacao_automatica'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo_desconto = $_POST['tipo_desconto'] ?? '';
    $valor_desconto = (float)($_POST['valor_desconto'] ?? 0);
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $uso_maximo = !empty($_POST['uso_maximo']) ? (int)$_POST['uso_maximo'] : NULL;
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $aplicacao_automatica = isset($_POST['aplicacao_automatica']) ? 1 : 0;

    // Validações
    if (empty($codigo)) {
        die("O código do cupom é obrigatório.");
    }
    
    if ($tipo_desconto == 'percentual' && ($valor_desconto <= 0 || $valor_desconto > 100)) {
        die("O desconto percentual deve estar entre 0 e 100.");
    }
    
    if (strtotime($data_inicio) > strtotime($data_fim)) {
        die("A data de início deve ser anterior à data de fim.");
    }

    if ($editing) {
        $stmt = $pdo->prepare("UPDATE cupom SET codigo=?, descricao=?, tipo_desconto=?, valor_desconto=?, data_inicio=?, data_fim=?, uso_maximo=?, ativo=?, aplicacao_automatica=? WHERE id_cupom=?");
        $stmt->execute([$codigo, $descricao, $tipo_desconto, $valor_desconto, $data_inicio, $data_fim, $uso_maximo, $ativo, $aplicacao_automatica, $id]);
    } else {
        // Verificar se o código já existe
        $stmt = $pdo->prepare("SELECT id_cupom FROM cupom WHERE codigo = ?");
        $stmt->execute([$codigo]);
        if ($stmt->fetch()) {
            die("Já existe um cupom com este código.");
        }
        
        $stmt = $pdo->prepare("INSERT INTO cupom (codigo, descricao, tipo_desconto, valor_desconto, data_inicio, data_fim, uso_maximo, ativo, aplicacao_automatica) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$codigo, $descricao, $tipo_desconto, $valor_desconto, $data_inicio, $data_fim, $uso_maximo, $ativo, $aplicacao_automatica]);
    }
    
    header("Location: index.php?page=cupons_list");
    exit;
}
?>
<h1><?=$editing ? 'Editar' : 'Novo'?> Cupom</h1>
<form method="post" class="grid">
    <label>Código do Cupom<br><input name="codigo" required value="<?=htmlspecialchars($codigo)?>"></label>
    <label>Descrição<br><textarea name="descricao"><?=htmlspecialchars($descricao)?></textarea></label>
    
    <label>Tipo de Desconto<br>
        <select name="tipo_desconto" required onchange="toggleTipoDesconto()">
            <option value="percentual" <?=$tipo_desconto == 'percentual' ? 'selected' : ''?>>Percentual (%)</option>
            <option value="valor_fixo" <?=$tipo_desconto == 'valor_fixo' ? 'selected' : ''?>>Valor Fixo (R$)</option>
        </select>
    </label>
    
    <label>Valor do Desconto<br>
        <input name="valor_desconto" type="number" step="0.01" min="0" required value="<?=htmlspecialchars($valor_desconto)?>">
        <span id="tipo_desconto_simbolo"><?=$tipo_desconto == 'percentual' ? '%' : 'R$'?></span>
    </label>
    
    <label>Data de Início<br><input name="data_inicio" type="date" required value="<?=htmlspecialchars($data_inicio)?>"></label>
    <label>Data de Fim<br><input name="data_fim" type="date" required value="<?=htmlspecialchars($data_fim)?>"></label>
    
    <label>Uso Máximo (deixe em branco para ilimitado)<br><input name="uso_maximo" type="number" min="1" value="<?=htmlspecialchars($uso_maximo)?>"></label>
    
    <label><input type="checkbox" name="ativo" <?=$ativo ? 'checked' : ''?>> Ativo</label>
    <label><input type="checkbox" name="aplicacao_automatica" <?=$aplicacao_automatica ? 'checked' : ''?>> Aplicação Automática</label>

    <button class="btn primary" type="submit">Salvar</button>
    <a class="btn" href="index.php?page=cupons_list">Cancelar</a>
</form>

<script>
function toggleTipoDesconto() {
    const tipo = document.querySelector('select[name="tipo_desconto"]').value;
    document.getElementById('tipo_desconto_simbolo').textContent = tipo === 'percentual' ? '%' : 'R$';
}
</script>