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

// Buscar todos os cupons ativos e suas associações com produtos
$cupons_ativos = [];
$stmt_cupons = $pdo->prepare("
    SELECT 
        c.id_cupom, 
        c.codigo, 
        c.tipo_desconto, 
        c.valor_desconto, 
        pc.id_produto 
    FROM cupom c
    JOIN produto_cupom pc ON c.id_cupom = pc.id_cupom
    WHERE c.ativo = 1 
    AND c.data_inicio <= CURDATE() 
    AND c.data_fim >= CURDATE()
    AND (c.uso_maximo IS NULL OR c.usos < c.uso_maximo)
");
$stmt_cupons->execute();
$cupons_produtos = $stmt_cupons->fetchAll();

// Organizar os cupons por produto
foreach ($cupons_produtos as $cp) {
    if (!isset($cupons_ativos[$cp['id_produto']])) {
        $cupons_ativos[$cp['id_produto']] = [];
    }
    $cupons_ativos[$cp['id_produto']][] = $cp;
}

// Para cada produto, buscar suas categorias e calcular preço com desconto
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
    $row['preco_desconto'] = $row['preco'];
    $row['cupons_aplicaveis'] = [];
    
    if (isset($cupons_ativos[$row['id_produto']])) {
        foreach ($cupons_ativos[$row['id_produto']] as $cupom) {
            if ($cupom['tipo_desconto'] == 'percentual') {
                $desconto = $row['preco'] * ($cupom['valor_desconto'] / 100);
            } else {
                $desconto = $cupom['valor_desconto'];
            }
            
            $preco_com_desconto = $row['preco'] - $desconto;
            
            if ($preco_com_desconto < $row['preco_desconto']) {
                $row['preco_desconto'] = $preco_com_desconto;
            }
            
            $row['cupons_aplicaveis'][] = $cupom;
        }
    }
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
            <th>Preço com Desconto</th>
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
                <?php if ($r['preco_desconto'] < $r['preco']): ?>
                    <span style="color: #e74c3c; font-weight: bold;">
                        R$ <?=number_format((float)$r['preco_desconto'], 2, ',', '.')?>
                    </span>
                    <?php if (!empty($r['cupons_aplicaveis'])): ?>
                        <br><small style="color: #27ae60;">
                            Com desconto: 
                            <?php 
                            $desconto_percentual = (($r['preco'] - $r['preco_desconto']) / $r['preco']) * 100;
                            echo number_format($desconto_percentual, 0) . '% off';
                            ?>
                        </small>
                    <?php endif; ?>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
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
                <?php if (!empty($r['cupons_aplicaveis'])): ?>
                    <br><small style="color: #3498db;">
                        Cupons: <?= count($r['cupons_aplicaveis']) ?>
                    </small>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="6">Nenhum registro.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<p>Total: <?=$total_registros?></p>