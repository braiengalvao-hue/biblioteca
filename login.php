<?php
session_start();
require_once 'conexao.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt = mysqli_prepare($conexao, 'SELECT id, nome, senha FROM usuarios WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario'] = $usuario['nome'];
        header('Location: index.php');
        exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Entrar | Biblioteca Comunitária</title><link rel="stylesheet" href="style.css"></head>
<body class="login-page">
<main class="login-box">
    <span class="brand-mark">BC</span><h1>Biblioteca Comunitária</h1><p>Entre para consultar e gerenciar o acervo.</p>
    <?php if ($erro): ?><div class="alert error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <form method="post" class="form-card">
        <label>E-mail<input type="email" name="email" required autofocus></label>
        <label>Senha<input type="password" name="senha" required></label>
        <button type="submit" class="button primary full">Entrar</button>
    </form>
</main>
</body></html>
