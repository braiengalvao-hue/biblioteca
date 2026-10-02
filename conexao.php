<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // No Docker, o MySQL é alcançado pelo nome do serviço "db".
    // Os valores padrão ainda permitem usar um servidor local fora do Docker.
    $host = getenv('DB_HOST') ?: 'localhost';
    $usuario = getenv('DB_USER') ?: 'root';
    $senha = getenv('DB_PASSWORD') ?: '';
    $banco = getenv('DB_NAME') ?: 'biblioteca_db';
    $porta = (int) (getenv('DB_PORT') ?: 3306);

    $conexao = mysqli_connect($host, $usuario, $senha, $banco, $porta);
    mysqli_set_charset($conexao, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    exit('Não foi possível conectar ao banco de dados. Importe o arquivo schema.sql e confira as credenciais em conexao.php.');
}
