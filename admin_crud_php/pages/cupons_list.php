<?php
$page_num = max(1, (int)($_GET['p'] ?? 1));
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

$busca = trim($_GET['q'] ?? '');
$params = [];
$sqlWhere = '';
if ($busca !== '') {
    $sqlWhere = " WHERE codigo LIKE :q OR descricao LIKE :q ";
    $params[':q'] = "%{$busca}%";
}

$total_stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM cupom {$sqlWhere}");
$total_stmt->execute($params);
$total_registros = (int)$total_stmt->fetch()['c'];

$stmt = $pdo->prepare("SELECT * FROM cupom {$sqlWhere} ORDER BY data_criacao DESC LIMIT :per OFFSET :off");
$stmt->bindValue(':per', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<h1>Cupons de Desconto</h1>
<form method="get" style="margin:.5rem 0 1rem">
    <input type="hidden" name="page" value="cupons_list">
    <input type="text" name="q" placeholder="Buscar por código ou descrição" value="<?=htmlspecialchars($busca)?>">
    <button class="btn" type="submit">Buscar</button>
    <a class="btn" href="index.php?page=cupons_form">+ Novo Cupom</a>
</form>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Válido</th>
            <th>Usos</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): 
            $valido = (strtotime($r['data_inicio']) <= time() && strtotime($r['data_fim']) >= time());
            $status = $r['ativo'] ? ($valido ? 'Ativo' : 'Fora do período') : 'Inativo';
        ?>
        <tr>
            <td><?=htmlspecialchars($r['id_cupom'])?></td>
            <td><strong><?=htmlspecialchars($r['codigo'])?></strong></td>
            <td><?=htmlspecialchars($r['tipo_desconto'] == 'percentual' ? '%' : 'R$')?></td>
            <td><?=htmlspecialchars($r['valor_desconto'])?></td>
            <td><?=date('d/m/Y', strtotime($r['data_inicio']))?> a <?=date('d/m/Y', strtotime($r['data_fim']))?></td>
            <td><?=$r['usos']?> / <?=$r['uso_maximo'] ? $r['uso_maximo'] : '∞'?></td>
            <td><?=$status?></td>
            <td class="actions">
                <a href="index.php?page=cupons_form&id=<?=$r['id_cupom']?>">Editar</a>
                <a href="index.php?page=cupons_delete&id=<?=$r['id_cupom']?>" onclick="return confirm('Excluir cupom?')">Excluir</a>
                <a href="index.php?page=cupons_produtos&id=<?=$r['id_cupom']?>">Produtos</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="8">Nenhum cupom cadastrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<p>Total: <?=$total_registros?></p>