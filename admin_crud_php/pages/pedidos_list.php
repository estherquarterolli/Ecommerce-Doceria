<?php
$page_num = max(1, (int)($_GET['p'] ?? 1));
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

$stmt = $pdo->prepare("
    SELECT p.id_pedido, p.valor_total, p.data_hora, p.endereco_entrega, p.status_pedido,
           c.nome AS cliente_nome, c.email AS cliente_email
      FROM pedido p
      JOIN cliente c ON c.id_cliente = p.id_cliente
     ORDER BY p.id_pedido DESC
     LIMIT :per OFFSET :off
");
$stmt->bindValue(':per', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<h1>Pedidos</h1>
<table>
    <thead>
        <tr>
            <th>ID</th><th>Cliente</th><th>Total</th><th>Status</th><th>Quando</th><th>Entrega</th><th>Itens</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?=htmlspecialchars($r['id_pedido'])?></td>
            <td><?=htmlspecialchars($r['cliente_nome'])?><br><small><?=htmlspecialchars($r['cliente_email'])?></small></td>
            <td>R$ <?=number_format((float)$r['valor_total'], 2, ',', '.')?></td>
            <td><?=htmlspecialchars($r['status_pedido'])?></td>
            <td><?=htmlspecialchars($r['data_hora'])?></td>
            <td><?=htmlspecialchars($r['endereco_entrega'])?></td>
            <td><a href="index.php?page=itens_pedido_list&id_pedido=<?=$r['id_pedido']?>">Ver itens</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7">Nenhum pedido.</td></tr><?php endif; ?>
    </tbody>
</table>