<?php
// ====== CONFIG AWS ======
// ====== CONFIG AWS (Lendo do ambiente) ======
$bucket    = getenv('AWS_BUCKET');
$accessKey = getenv('AWS_ACCESS_KEY');
$secretKey = getenv('AWS_SECRET_KEY');
$region    = getenv('AWS_REGION');

$key = $_GET['key'];
if (!$key) die("Chave do arquivo não informada.");

// ====== CONFIGURAÇÕES DE TEMPO ======
$expires = 60; // URL válida por 1min
$service = 's3';
$host    = "$bucket.s3.amazonaws.com";
$method  = 'GET';

$alg   = 'AWS4-HMAC-SHA256';
$date  = gmdate('Ymd');
$stamp = gmdate('Ymd\THis\Z');

// 1. Parâmetros da Query String (Ordenados alfabeticamente)
$params = [
    'X-Amz-Algorithm'     => $alg,
    'X-Amz-Credential'    => "$accessKey/$date/$region/$service/aws4_request",
    'X-Amz-Date'          => $stamp,
    'X-Amz-Expires'       => $expires,
    'X-Amz-SignedHeaders' => 'host',
];
ksort($params);

$query_string = http_build_query($params);

// 2. Criar a "Canonical Request"
// O S3 exige que o path da chave seja escapado corretamente
$canonical_uri = '/' . str_replace('%2F', '/', rawurlencode($key));
$canonical_request = "$method\n$canonical_uri\n$query_string\nhost:$host\n\nhost\nUNSIGNED-PAYLOAD";

// 3. String to Sign
$scope = "$date/$region/$service/aws4_request";
$string_to_sign = "$alg\n$stamp\n$scope\n" . hash('sha256', $canonical_request);

// 4. Calcular Assinatura (Signing Key)
$k_date    = hash_hmac('sha256', $date, "AWS4" . $secretKey, true);
$k_region  = hash_hmac('sha256', $region, $k_date, true);
$k_service = hash_hmac('sha256', $service, $k_region, true);
$k_signing = hash_hmac('sha256', "aws4_request", $k_service, true);
$signature = hash_hmac('sha256', $string_to_sign, $k_signing);

// 5. Montar a URL Final
$signed_url = "https://$host$canonical_uri?$query_string&X-Amz-Signature=$signature";

// Redireciona para a URL assinada
header("Location: $signed_url");
exit;