<?php
// Lembre-se que este arquivo é incluído pelo index.php, então $pdo já existe.
$page_num = max(1, (int)($_GET['p'] ?? 1));
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

$busca = trim($_GET['q'] ?? '');
$params = [];
$sqlWhere = '';
if ($busca !== '') {
    $sqlWhere = " WHERE nome LIKE :q ";
    $params[':q'] = "%{$busca}%";
}

$total_stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM produto {$sqlWhere}");
$total_stmt->execute($params);
$total_registros = (int)$total_stmt->fetch()['c'];

$stmt = $pdo->prepare("SELECT * FROM produto {$sqlWhere} ORDER BY id_produto DESC LIMIT :per OFFSET :off");
$stmt->bindValue(':per', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->execute();
$rows = $stmt->fetchAll();
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
                <?php
                    $categorias = array_filter([$r['categoria1'], $r['categoria2'], $r['categoria3']]);
                    echo htmlspecialchars(implode(' / ', $categorias));
                ?>
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