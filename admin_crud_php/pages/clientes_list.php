<?php
$page_num = max(1, (int)($_GET['p'] ?? 1));
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

$busca = trim($_GET['q'] ?? '');
$params = [];
$sqlWhere = '';
if ($busca !== '') {
    $sqlWhere = " WHERE nome LIKE :q OR email LIKE :q ";
    $params[':q'] = "%{$busca}%";
}

$total_stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM cliente {$sqlWhere}");
$total_stmt->execute($params);
$total_registros = (int)$total_stmt->fetch()['c'];

$stmt = $pdo->prepare("SELECT * FROM cliente {$sqlWhere} ORDER BY id_cliente DESC LIMIT :per OFFSET :off");
$stmt->bindValue(':per', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<h1>Clientes</h1>
<form method="get" style="margin:.5rem 0 1rem">
    <input type="hidden" name="page" value="clientes_list">
    <input type="text" name="q" placeholder="Buscar por nome/email" value="<?=htmlspecialchars($busca)?>">
    <button class="btn" type="submit">Buscar</button>
    <a class="btn" href="index.php?page=clientes_form">+ Novo</a>
</form>
<table>
    <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr></thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?=htmlspecialchars($r['id_cliente'])?></td>
            <td><?=htmlspecialchars($r['nome'])?></td>
            <td><?=htmlspecialchars($r['email'])?></td>
            <td><?=htmlspecialchars($r['telefone'])?></td>
            <td class="actions">
                <a href="index.php?page=clientes_form&id=<?=$r['id_cliente']?>">Editar</a>
                <a href="index.php?page=clientes_delete&id=<?=$r['id_cliente']?>" onclick="return confirm('Excluir cliente?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="5">Nenhum registro.</td></tr><?php endif; ?>
    </tbody>
</table>
<p>Total: <?=$total_registros?></p>