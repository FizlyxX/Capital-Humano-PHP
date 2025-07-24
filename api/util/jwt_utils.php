<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/api_utils.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const JWT_SECRET_KEY = 'mi_clave_ultrasecreta';
const JWT_ALGORITHM = 'HS256';
const JWT_ISSUER = 'mi_web_issuer';
const JWT_VALID_AUDIENCE = 'Contraloría General de Panamá';

function requireAuth(): array {
    $authHeader = getAuthorizationHeader();
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        jsonError('Token no proporcionado o mal formado.', 401);
    }

    $token = $matches[1];
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, JWT_ALGORITHM));
        $claims = (array)$decoded;

        // Validar que al menos uno de los claims sea igual al issuer esperado
        if (!in_array(JWT_ISSUER, [$claims['aud'] ?? '', $claims['sub'] ?? '', $claims['iss'] ?? ''])) {
            jsonError('Token inválido: emisor no autorizado.', 403);
        }

        return $claims;
    } catch (Exception $e) {
        jsonError('Token inválido: ' . $e->getMessage(), 401);
    }
}

function getAuthorizationHeader(): ?string
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // para Apache
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function getBearerToken(): ?string
{
    $header = getAuthorizationHeader();
    if (!empty($header) && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
        return $matches[1];
    }
    return null;
}

function validateJWT(string $token): array
{
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
        $payload = (array)$decoded;

        if (
            ($payload['aud'] ?? null) !== JWT_VALID_AUDIENCE &&
            ($payload['sub'] ?? null) !== JWT_VALID_AUDIENCE &&
            ($payload['iss'] ?? null) !== JWT_VALID_AUDIENCE
        ) {
            throw new InvalidArgumentException('El token no tiene los claims válidos.');
        }

        return $payload;
    } catch (Exception $e) {
        jsonError('Token inválido: ' . $e->getMessage(), 401);
    }
}

function isValidContraloriaToken(?string $authHeader): bool {
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return false;
    }

    $token = $matches[1];

    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, JWT_ALGORITHM));
        $claims = (array)$decoded;

        return in_array(JWT_VALID_AUDIENCE, [
            $claims['aud'] ?? '',
            $claims['sub'] ?? '',
            $claims['iss'] ?? ''
        ]);
    } catch (Exception $e) {
        return false;
    }
}