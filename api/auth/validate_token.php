<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!defined('JWT_ISSUER')) {
    define('JWT_ISSUER', 'proyecto-php');
}

if (!defined('JWT_VALID_AUDIENCE')) {
    define('JWT_VALID_AUDIENCE', 'usuarios-app');
}

function validate_token($token) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));

        // Validar que el payload incluya el issuer y audience esperados
        if (
            !isset($decoded->sub) || $decoded->sub !== JWT_ISSUER ||
            !isset($decoded->aud) || $decoded->aud !== JWT_VALID_AUDIENCE
        ) {
            return false;
        }

        return $decoded;
    } catch (Exception $e) {
        return false;
    }
}