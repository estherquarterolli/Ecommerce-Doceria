<?php
$page_num = max(1, (int)($_GET['p'] ?? 1));
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

$busca = trim($_GET['q'] ?? '');
$params = [];
$sqlWhere = '';
if ($busca !== '') {
    $sqlWhere = " WHERE c.nome LIKE :q OR p.nome LIKE :q ";
    $params[':q'] = "%{$busca}%";
}

$total_stmt = $pdo->prepare("
    SELECT COUNT(*) AS c 
    FROM categoria c 
    LEFT JOIN categoria p ON c.id_categoria_pai = p.id_categoria 
    {$sqlWhere}
");
$total_stmt->execute($params);
$total_registros = (int)$total_stmt->fetch()['c'];

$stmt = $pdo->prepare("
    SELECT c.*, p.nome as pai_nome 
    FROM categoria c 
    LEFT JOIN categoria p ON c.id_categoria_pai = p.id_categoria 
    {$sqlWhere} 
    ORDER BY c.id_categoria_pai, c.nome 
    LIMIT :per OFFSET :off
");
$stmt->bindValue(':per', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<h1>Categorias</h1>
<form method="get" style="margin:.5rem 0 1rem">
    <input type="hidden" name="page" value="categorias_list">
    <input type="text" name="q" placeholder="Buscar por nome" value="<?=htmlspecialchars($busca)?>">
    <button class="btn" type="submit">Buscar</button>
    <a class="btn" href="index.php?page=categorias_form">+ Nova Categoria</a>
</form>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Categoria Pai</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?=htmlspecialchars($r['id_categoria'])?></td>
            <td><?=htmlspecialchars($r['nome'])?></td>
            <td><?=htmlspecialchars($r['pai_nome'] ?? '—')?></td>
            <td class="actions">
                <a href="index.php?page=categorias_form&id=<?=$r['id_categoria']?>">Editar</a>
                <a href="index.php?page=categorias_delete&id=<?=$r['id_categoria']?>" onclick="return confirm('Excluir categoria?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="4">Nenhuma categoria.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<p>Total: <?=$total_registros?></p>