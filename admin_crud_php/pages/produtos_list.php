<?php
$page_num = max(1, (int)($_GET['p'] ?? 1));
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

$busca = trim($_GET['q'] ?? '');
$params = [];
$sqlWhere = '';
if ($busca !== '') {
    $sqlWhere = " WHERE p.nome LIKE :q ";
    $params[':q'] = "%{$busca}%";
}

$total_stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM produto p {$sqlWhere}");
$total_stmt->execute($params);
$total_registros = (int)$total_stmt->fetch()['c'];

$stmt = $pdo->prepare("SELECT * FROM produto {$sqlWhere} ORDER BY id_produto DESC LIMIT :per OFFSET :off");
$stmt->bindValue(':per', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->execute();
$rows = $stmt->fetchAll();

// Para cada produto, buscar suas categorias
foreach ($rows as &$row) {
    $stmt = $pdo->prepare("
        SELECT c.nome, c.id_categoria_pai 
        FROM categoria c 
        JOIN produto_categoria pc ON c.id_categoria = pc.id_categoria 
        WHERE pc.id_produto = ?
    ");
    $stmt->execute([$row['id_produto']]);
    $categorias = $stmt->fetchAll();
    
    // Organizar categorias por tipo
    $categoria_principal = '';
    $subcategorias = [];
    
    foreach ($categorias as $cat) {
        if ($cat['id_categoria_pai'] === null) {
            $categoria_principal = $cat['nome'];
        } else {
            $subcategorias[] = $cat['nome'];
        }
    }
    
    $row['categoria_principal'] = $categoria_principal;
    $row['subcategorias'] = $subcategorias;
}
unset($row);
?>
<h1>Produtos</h1>
<form method="get" style="margin:.5rem 0 1rem">
    <input type="hidden" name="page" value="produtos_list">
    <input type="text" name="q" placeholder="Buscar por nome" value="<?=htmlspecialchars($busca)?>">
    <button class="btn" type="submit">Buscar</button>
    <a class="btn" href="index.php?page=produtos_form">+ Novo</a>
</form>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Categorias</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?=htmlspecialchars($r['id_produto'])?></td>
            <td><?=htmlspecialchars($r['nome'])?></td>
            <td>R$ <?=number_format((float)$r['preco'], 2, ',', '.')?></td>
            <td>
                <?php if (!empty($r['categoria_principal'])): ?>
                    <strong><?=htmlspecialchars($r['categoria_principal'])?></strong>
                    <?php if (!empty($r['subcategorias'])): ?>
                        <br><small><?=htmlspecialchars(implode(', ', $r['subcategorias']))?></small>
                    <?php endif; ?>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td class="actions">
                <a href="index.php?page=produtos_form&id=<?=$r['id_produto']?>">Editar</a>
                <a href="index.php?page=produtos_delete&id=<?=$r['id_produto']?>" onclick="return confirm('Excluir produto?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="5">Nenhum registro.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<p>Total: <?=$total_registros?></p>