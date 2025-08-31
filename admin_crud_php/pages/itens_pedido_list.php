<?php
$id_pedido = (int)($_GET['id_pedido'] ?? 0);
// MODIFICADO: Removida a chamada ao footer.php que não existe
if ($id_pedido <= 0) { echo "Pedido inválido."; exit; }

$pedido = $pdo->prepare("SELECT * FROM pedido WHERE id_pedido = ?");
$pedido->execute([$id_pedido]);
$ped = $pedido->fetch();
// MODIFICADO: Removida a chamada ao footer.php que não existe
if (!$ped) { echo "Pedido não encontrado."; exit; }

$stmt = $pdo->prepare("
    SELECT ip.*, pr.nome
      FROM item_pedido ip
      JOIN produto pr ON pr.id_produto = ip.id_produto
     WHERE ip.id_pedido = ?
");
$stmt->execute([$id_pedido]);
$rows = $stmt->fetchAll();
?>
<h1>Itens do Pedido #<?=htmlspecialchars($id_pedido)?></h1>
<table>
    <thead><tr><th>Produto</th><th>Qtd</th><th>Preço Unit. venda</th><th>Subtotal</th></tr></thead>
    <tbody>
        <?php 
        $total = 0;
        foreach ($rows as $r): 
            $sub = (float)$r['preco_unitario_venda'] * (int)$r['quantidade'];
            $total += $sub;
        ?>
        <tr>
            <td><?=htmlspecialchars($r['nome'])?></td>
            <td><?=htmlspecialchars($r['quantidade'])?></td>
            <td>R$ <?=number_format((float)$r['preco_unitario_venda'], 2, ',', '.')?></td>
            <td>R$ <?=number_format($sub, 2, ',', '.')?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4">Nenhum item.</td></tr><?php endif; ?>
    </tbody>
</table>
<p><strong>Total calculado dos itens:</strong> R$ <?=number_format($total, 2, ',', '.')?></p>