<?php
$id_cupom = (int)($_GET['id'] ?? 0);
if ($id_cupom <= 0) {
    echo "Cupom inválido.";
    exit;
}

// Buscar informações do cupom
$stmt = $pdo->prepare("SELECT * FROM cupom WHERE id_cupom = ?");
$stmt->execute([$id_cupom]);
$cupom = $stmt->fetch();
if (!$cupom) {
    echo "Cupom não encontrado.";
    exit;
}

// Processar adição/remoção de produtos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_produto'])) {
        $id_produto = (int)($_POST['id_produto'] ?? 0);
        if ($id_produto > 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO produto_cupom (id_produto, id_cupom) VALUES (?, ?)");
                $stmt->execute([$id_produto, $id_cupom]);
            } catch (PDOException $e) {
                // Ignora erro de duplicação
            }
        }
    } elseif (isset($_POST['remover_produto'])) {
        $id_produto = (int)($_POST['id_produto'] ?? 0);
        if ($id_produto > 0) {
            $stmt = $pdo->prepare("DELETE FROM produto_cupom WHERE id_produto = ? AND id_cupom = ?");
            $stmt->execute([$id_produto, $id_cupom]);
        }
    }
}

// Buscar produtos associados ao cupom
$stmt = $pdo->prepare("
    SELECT p.id_produto, p.nome, p.preco 
    FROM produto p 
    JOIN produto_cupom pc ON p.id_produto = pc.id_produto 
    WHERE pc.id_cupom = ?
");
$stmt->execute([$id_cupom]);
$produtos_associados = $stmt->fetchAll();

// Buscar produtos não associados ao cupom
$stmt = $pdo->prepare("
    SELECT p.id_produto, p.nome, p.preco 
    FROM produto p 
    WHERE p.id_produto NOT IN (
        SELECT id_produto FROM produto_cupom WHERE id_cupom = ?
    )
");
$stmt->execute([$id_cupom]);
$produtos_nao_associados = $stmt->fetchAll();
?>
<h1>Produtos do Cupom: <?=htmlspecialchars($cupom['codigo'])?></h1>

<div style="display: flex; gap: 2rem;">
    <div style="flex: 1;">
        <h2>Produtos Associados</h2>
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos_associados as $produto): ?>
                    <tr>
                        <td><?=htmlspecialchars($produto['nome'])?></td>
                        <td>R$ <?=number_format($produto['preco'], 2, ',', '.')?></td>
                        <td>
                            <button type="submit" name="remover_produto" value="<?=$produto['id_produto']?>">Remover</button>
                            <input type="hidden" name="id_produto" value="<?=$produto['id_produto']?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($produtos_associados)): ?>
                    <tr><td colspan="3">Nenhum produto associado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
    
    <div style="flex: 1;">
        <h2>Adicionar Produtos</h2>
        <form method="post">
            <select name="id_produto" required>
                <option value="">Selecione um produto</option>
                <?php foreach ($produtos_nao_associados as $produto): ?>
                <option value="<?=$produto['id_produto']?>"><?=htmlspecialchars($produto['nome'])?> - R$ <?=number_format($produto['preco'], 2, ',', '.')?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="adicionar_produto">Adicionar</button>
        </form>
    </div>
</div>

<a class="btn" href="index.php?page=cupons_list">Voltar para Lista de Cupons</a>