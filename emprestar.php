<?php
session_start(); if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: login.php'); exit; } require_once 'conexao.php';
$livroId = (int) ($_POST['livro_id'] ?? 0); $usuarioId = (int) $_SESSION['usuario_id'];
mysqli_begin_transaction($conexao);
try {
    $stmt = mysqli_prepare($conexao, 'UPDATE livros SET quantidade = quantidade - 1 WHERE id = ? AND quantidade > 0'); mysqli_stmt_bind_param($stmt, 'i', $livroId); mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) !== 1) throw new Exception('Livro indisponível.');
    $stmt = mysqli_prepare($conexao, "INSERT INTO emprestimos (usuario_id, livro_id, data_emprestimo, status) VALUES (?, ?, CURDATE(), 'Ativo')"); mysqli_stmt_bind_param($stmt, 'ii', $usuarioId, $livroId); mysqli_stmt_execute($stmt);
    mysqli_commit($conexao); $mensagem = 'Empréstimo registrado com sucesso.';
} catch (Throwable $e) { mysqli_rollback($conexao); $mensagem = 'Não foi possível registrar o empréstimo: ' . $e->getMessage(); }
header('Location: index.php?mensagem=' . urlencode($mensagem));
