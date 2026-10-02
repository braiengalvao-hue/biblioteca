<?php
session_start(); if (!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; } require_once 'conexao.php';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $autor = trim($_POST['autor'] ?? ''); $ano = (int) ($_POST['ano_publicacao'] ?? 0); $quantidade = (int) ($_POST['quantidade'] ?? 0);
    if ($titulo === '' || $autor === '' || $ano < 1 || $quantidade < 0) $erro = 'Preencha todos os campos com valores válidos.';
    else { $stmt = mysqli_prepare($conexao, 'INSERT INTO livros (titulo, autor, ano_publicacao, quantidade) VALUES (?, ?, ?, ?)'); mysqli_stmt_bind_param($stmt, 'ssii', $titulo, $autor, $ano, $quantidade); mysqli_stmt_execute($stmt); header('Location: index.php?mensagem=Livro cadastrado com sucesso.'); exit; }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Cadastrar livro</title><link rel="stylesheet" href="style.css"></head><body><header class="topbar"><a class="logo" href="index.php"><span>BC</span> Biblioteca Comunitária</a></header><main class="form-page"><a href="index.php">← Voltar ao acervo</a><h1>Cadastrar livro</h1><?php if ($erro): ?><div class="alert error"><?= $erro ?></div><?php endif; ?><form method="post" class="form-card"><label>Título<input name="titulo" required></label><label>Autor<input name="autor" required></label><label>Ano de publicação<input type="number" name="ano_publicacao" min="1" max="<?= date('Y') ?>" required></label><label>Quantidade disponível<input type="number" name="quantidade" min="0" value="1" required></label><button class="button primary">Salvar livro</button></form></main></body></html>
