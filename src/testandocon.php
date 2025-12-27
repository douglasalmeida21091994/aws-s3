<?php
$mysql_host = 'database-s3-mysql.c8j6u0ick6ig.us-east-1.rds.amazonaws.com';
$mysql_user = 'admins3';
$mysql_pass = 'MySQL2025-ProntEletr0nico!';
$mysql_db   = 'database-s3-mysql'; 

// Usando mysqli (o 'i' é de improved)
$conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);

// Verifica se houve erro
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

echo "Conectado com sucesso ao RDS!!!!";

// Define charset
$conn->set_charset("utf8");
?>