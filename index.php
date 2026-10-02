<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
require_once 'conexao.php';

$busca = trim($_GET['busca'] ?? '');
if ($busca !== '') {
    $termo = '%' . $busca . '%';
    $stmt = mysqli_prepare($conexao, 'SELECT * FROM livros WHERE titulo LIKE ? OR autor LIKE ? ORDER BY titulo');
    mysqli_stmt_bind_param($stmt, 'ss', $termo, $termo);
    mysqli_stmt_execute($stmt);
    $livros = mysqli_stmt_get_result($stmt);
} else {
    $livros = mysqli_query($conexao, 'SELECT * FROM livros ORDER BY titulo');
}
$stmt = mysqli_prepare($conexao, 'SELECT e.id, l.titulo, l.autor, e.data_emprestimo, e.data_devolucao, e.status FROM emprestimos e JOIN livros l ON l.id = e.livro_id WHERE e.usuario_id = ? ORDER BY e.data_emprestimo DESC');
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['usuario_id']);
mysqli_stmt_execute($stmt);
$emprestimos = mysqli_stmt_get_result($stmt);
$mensagem = $_GET['mensagem'] ?? '';
?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Acervo | Biblioteca Comunitária</title><link rel="stylesheet" href="style.css"><script src="script.js" defer></script></head>
<body>
<header class="topbar"><a class="logo" href="index.php"><span>BC</span> Biblioteca Comunitária</a><nav>Olá, <?= htmlspecialchars($_SESSION['usuario']) ?> <a class="button secondary" href="sair.php">Sair</a></nav></header>
<main class="container">
<?php if ($mensagem): ?><div class="alert success"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
<section class="heading"><div><p class="eyebrow">ACERVO</p><h1>Livros da biblioteca</h1><p>Consulte, administre e solicite empréstimos.</p></div><a href="cadastrar.php" class="button primary">+ Cadastrar livro</a></section>
<form class="search" method="get"><input type="search" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por título ou autor"><button class="button secondary">Buscar</button><?php if ($busca !== ''): ?><a href="index.php">Limpar</a><?php endif; ?></form>
<section class="table-card"><table><thead><tr><th>Livro</th><th>Autor</th><th>Ano</th><th>Disponíveis</th><th>Ações</th></tr></thead><tbody>
<?php while ($livro = mysqli_fetch_assoc($livros)): ?><tr><td><strong><?= htmlspecialchars($livro['titulo']) ?></strong></td><td><?= htmlspecialchars($livro['autor']) ?></td><td><?= (int) $livro['ano_publicacao'] ?></td><td><span class="stock <?= $livro['quantidade'] > 0 ? 'available' : 'empty' ?>"><?= (int) $livro['quantidade'] ?></span></td><td class="actions">
<?php if ($livro['quantidade'] > 0): ?><form method="post" action="emprestar.php"><input type="hidden" name="livro_id" value="<?= $livro['id'] ?>"><button class="link-button">Emprestar</button></form><?php endif; ?><a href="editar.php?id=<?= $livro['id'] ?>">Editar</a><form method="post" action="excluir.php" data-confirm="Remover este livro do acervo?"><input type="hidden" name="id" value="<?= $livro['id'] ?>"><button class="link-button danger">Excluir</button></form></td></tr><?php endwhile; ?>
</tbody></table></section>
<section class="loans"><div class="section-title"><div><p class="eyebrow">MEUS EMPRÉSTIMOS</p><h2>Histórico de empréstimos</h2></div></div>
<?php if (mysqli_num_rows($emprestimos) === 0): ?><p class="empty-state">Você ainda não possui empréstimos registrados.</p><?php else: ?><div class="loan-list"><?php while ($emprestimo = mysqli_fetch_assoc($emprestimos)): ?><article><div><strong><?= htmlspecialchars($emprestimo['titulo']) ?></strong><p><?= htmlspecialchars($emprestimo['autor']) ?> · Emprestado em <?= date('d/m/Y', strtotime($emprestimo['data_emprestimo'])) ?><?= $emprestimo['data_devolucao'] ? ' · Devolvido em ' . date('d/m/Y', strtotime($emprestimo['data_devolucao'])) : '' ?></p></div><?php if ($emprestimo['status'] === 'Ativo'): ?><form method="post" action="devolver.php"><input type="hidden" name="emprestimo_id" value="<?= $emprestimo['id'] ?>"><button class="button secondary">Devolver</button></form><?php else: ?><span class="stock available">Devolvido</span><?php endif; ?></article><?php endwhile; ?></div><?php endif; ?></section>
</main></body></html>
