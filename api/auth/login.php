<?php

use Firebase\JWT\JWT;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../util/api_utils.php';
require_once __DIR__ . '/../util/jwt_utils.php';
require_once __DIR__ . '/../../config.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Método no permitido.', 405);
}

$input = json_decode(file_get_contents("php://input"), true);

$username = trim($input['nombre_usuario'] ?? '');
$password = trim($input['password'] ?? '');

if (!$username || !$password) {
    jsonError('Usuario y contraseña son obligatorios.');
}

$conn = $link ?? null;
if (!$conn) {
    jsonError('No se pudo conectar a la base de datos.', 500);
}

// Consulta real a la tabla usuarios
$sql = "SELECT id, nombre_usuario, contrasena, activo FROM contraloria WHERE nombre_usuario = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) !== 1) {
    jsonError('Credenciales inválidas.', 401);
}

mysqli_stmt_bind_result($stmt, $id, $nombre_usuario, $password_hash, $activo);
mysqli_stmt_fetch($stmt);

if (!$activo) {
    jsonError('Usuario inactivo.', 403);
}

if (!password_verify($password, $password_hash)) {
    jsonError('Credenciales inválidas.', 401);
}

// 2. Generar JWT
$payload = [
    'sub' => JWT_ISSUER,
    'uid' => $id,
    'name' => $nombre_usuario,
    'email' => $nombre_usuario, // No hay campo de correo, usamos el nombre de usuario
    'iat' => time(),
    'exp' => time() + 3600,
    'aud' => JWT_VALID_AUDIENCE
];

$token = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');

session_start();
$_SESSION['token'] = $token;
$_SESSION['loggedin'] = true;

jsonResponse([
    'token' => $token,
    'expires_in' => 3600,
    'user' => [
        'id' => $id,
        'name' => $nombre_usuario,
        'email' => $nombre_usuario,
    ]
]);