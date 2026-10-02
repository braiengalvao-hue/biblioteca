<?php
session_start(); if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: login.php'); exit; } require_once 'conexao.php';
$id = (int) ($_POST['id'] ?? 0);
try {
    $stmt = mysqli_prepare($conexao, 'DELETE FROM livros WHERE id = ?'); mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
    $mensagem = 'Livro removido do acervo.';
} catch (mysqli_sql_exception $e) {
    $mensagem = 'Este livro possui empréstimos registrados e não pode ser removido.';
}
header('Location: index.php?mensagem=' . urlencode($mensagem));
