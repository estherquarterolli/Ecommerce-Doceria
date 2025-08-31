<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

$nome = $email = $telefone = '';

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { echo "Cliente não encontrado."; exit; }
    $nome = $row['nome']; $email = $row['email']; $telefone = $row['telefone'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($editing) {
        // Na edição, a senha só é atualizada se for preenchida
        if ($senha !== '') {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE cliente SET nome=?, email=?, telefone=?, senha=? WHERE id_cliente=?");
            $stmt->execute([$nome, $email, $telefone, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE cliente SET nome=?, email=?, telefone=? WHERE id_cliente=?");
            $stmt->execute([$nome, $email, $telefone, $id]);
        }
    } else {
        // Na criação, a senha é obrigatória
        if (empty($senha)) {
            die("O campo senha é obrigatório para novos clientes.");
        }
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO cliente (nome, senha, email, telefone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $hash, $email, $telefone]);
    }
    // MODIFICADO: Corrigido o redirecionamento após salvar
    header("Location: index.php?page=clientes_list");
    exit;
}
?>
<h1><?=$editing ? 'Editar' : 'Novo'?> Cliente</h1>
<form method="post" class="grid">
    <label>Nome<br><input name="nome" required value="<?=htmlspecialchars($nome)?>"></label>
    <label>Email<br><input name="email" type="email" required value="<?=htmlspecialchars($email)?>"></label>
    <label>Telefone<br><input name="telefone" required value="<?=htmlspecialchars($telefone)?>"></label>
    <label>Senha <?= $editing ? '(deixe em branco para manter)' : '' ?><br><input name="senha" type="password" <?= $editing ? '' : 'required' ?>></label>
    <button class="btn primary" type="submit">Salvar</button>
    <a class="btn" href="index.php?page=clientes_list">Cancelar</a>
</form>