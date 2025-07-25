<?php

require_once __DIR__ . '/../auth/validate_token.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../reportes/colaboradores_por_sexo.php';;

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

$colaboradores = colaboradorXSexo();

echo json_encode($colaboradores);

// Consulta a la base de datos

