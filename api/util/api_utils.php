<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn]
function jsonResponse($data = [], int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $code,
        'data' => $data
    ]);
    exit;
}

#[NoReturn]
function jsonError(string $message, int $code = 400): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $code,
        'error' => $message
    ]);
    exit;
}
