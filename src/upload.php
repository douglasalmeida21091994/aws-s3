<?php
header('Content-Type: application/json');

// 1. Carregar variáveis de ambiente
$path = __DIR__ . '/../.env'; 
if (file_exists($path)) {
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            putenv(trim($parts[0]) . "=" . trim($parts[1]));
        }
    }
}

// ====== CONFIG AWS (Lendo do ambiente) ======
$bucket    = getenv('AWS_BUCKET');
$accessKey = getenv('AWS_ACCESS_KEY');
$secretKey = getenv('AWS_SECRET_KEY');

// ====== CONFIG DB (Lendo do ambiente) ======
$mysql_host = getenv('DB_HOST');
$mysql_user = getenv('DB_USER');
$mysql_pass = getenv('DB_PASS');
$mysql_db   = getenv('DB_NAME');

$conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if ($conn->connect_error) {
    echo json_encode(array('status'=>'erro','message'=>'Falha no Banco: ' . $conn->connect_error));
    exit;
}
$conn->set_charset("utf8");

// ====== DADOS DO FORM ======
$user_code = $_POST['user_code'];
$file      = $_FILES['arquivo'];

if (!$file || !$user_code) {
    echo json_encode(array('status'=>'erro','message'=>'Dados incompletos'));
    exit;
}

// ====== VALIDAÇÃO DE TIPO (APENAS PDF) ======
$allowed_type = 'application/pdf';
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Verifica tanto o MIME type enviado pelo navegador quanto a extensão do arquivo
if ($file['type'] !== $allowed_type || $file_extension !== 'pdf') {
    echo json_encode(array('status'=>'erro','message'=>'Tipo de arquivo não permitido. Apenas PDF é aceito.'));
    exit;
}

$random = substr(md5(uniqid()), 0, 6);
$file_name_clean = str_replace(' ', '_', $file['name']);
$file_key = "contratosprofissinais/" . $user_code . "_" . $random . "_" . $file_name_clean;

$file_data = file_get_contents($file['tmp_name']);
$file_type = $file['type'];
$file_size = $file['size'];

// ====== UPLOAD S3 VIA CURL (AWS SIG V4) ======


$region = 'us-east-1'; 
$service = 's3';
$host = "$bucket.s3.amazonaws.com";
$endpoint = "https://$bucket.s3.amazonaws.com/$file_key";

// Preparação da Assinatura
$alg = 'AWS4-HMAC-SHA256';
$date = gmdate('Ymd');
$stamp = gmdate('Ymd\THis\Z');

$headers = [
    'Host' => $host,
    'x-amz-content-sha256' => hash('sha256', $file_data),
    'x-amz-date' => $stamp,
];

$canonical_headers = "";
$signed_headers = "";
foreach ($headers as $k => $v) {
    $canonical_headers .= strtolower($k) . ":" . trim($v) . "\n";
    $signed_headers .= strtolower($k) . ";";
}
$signed_headers = rtrim($signed_headers, ';');

$canonical_request = "PUT\n/" . $file_key . "\n\n" . $canonical_headers . "\n" . $signed_headers . "\n" . hash('sha256', $file_data);

$scope = "$date/$region/$service/aws4_request";
$string_to_sign = "$alg\n$stamp\n$scope\n" . hash('sha256', $canonical_request);

$k_date = hash_hmac('sha256', $date, "AWS4" . $secretKey, true);
$k_region = hash_hmac('sha256', $region, $k_date, true);
$k_service = hash_hmac('sha256', $service, $k_region, true);
$k_signing = hash_hmac('sha256', "aws4_request", $k_service, true);
$signature = hash_hmac('sha256', $string_to_sign, $k_signing);

$authorization = "$alg Credential=$accessKey/$scope, SignedHeaders=$signed_headers, Signature=$signature";

// Execução do CURL
$curl_headers = [
    "Authorization: $authorization",
    "x-amz-content-sha256: " . hash('sha256', $file_data),
    "x-amz-date: $stamp",
    "Content-Type: $file_type",
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $file_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    echo json_encode(['status' => 'erro', 'message' => "S3 Erro ($http_code): " . $response]);
    exit;
}

// ====== SALVA NO RDS (MySQLi) ======
// real_escape_string para evitar SQL Injection básico nos nomes de arquivos
$safe_file_name = $conn->real_escape_string($file['name']);
$sql = "INSERT INTO pront_contratos_prof 
        (user_code, file_key, file_name, file_type, file_size, created_at)
        VALUES 
        ('$user_code', '$file_key', '$safe_file_name', '$file_type', '$file_size', NOW())";

if($conn->query($sql)) {
    echo json_encode(array('status'=>'ok'));
} else {
    echo json_encode(array('status'=>'erro','message'=>'Erro SQL: '.$conn->error));
}
$conn->close();