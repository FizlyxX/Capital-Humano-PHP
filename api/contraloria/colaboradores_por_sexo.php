<?php

require_once __DIR__ . '/../auth/validate_token.php';
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

// Solo permite método GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Obtener el encabezado Authorization
$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];

$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');

if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(["error" => "Token no proporcionado"]);
    exit;
}

// Extraer token
$token = substr($authHeader, 7);

// Validar token
$userData = validate_token($token);
if (!$userData) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido o expirado"]);
    exit;
}

// Consulta a la base de datos
$query = "SELECT sexo, COUNT(*) as cantidad FROM colaboradores WHERE activo = 1 GROUP BY sexo";
$result = mysqli_query($link, $query);

$masculino = 0;
$femenino = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $sexo = strtolower($row['sexo']);
    if ($sexo === 'masculino') {
        $masculino = (int)$row['cantidad'];
    } elseif ($sexo === 'femenino') {
        $femenino = (int)$row['cantidad'];
    }
}

// Enviar respuesta JSON
echo json_encode([
    "masculino" => $masculino,
    "femenino" => $femenino
]);
