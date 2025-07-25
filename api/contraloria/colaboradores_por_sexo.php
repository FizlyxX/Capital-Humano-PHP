<?php

require_once __DIR__ . '/../util/jwt_utils.php';
require_once __DIR__ . '/../util/api_utils.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../reportes/colaboradores_por_sexo.php';

// Solo permite método GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}
requireAuth();

$colaboradores = colaboradorXSexo();

jsonResponse($colaboradores);
