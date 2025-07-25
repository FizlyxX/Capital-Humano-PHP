<?php
define('DB_SERVER', 'localhost'); 
define('DB_USERNAME', 'nathan.carrasco');
define('DB_PASSWORD', 'Pyro1721');
define('DB_NAME', 'capital_humano');

if (!defined('JWT_SECRET_KEY')) {
    define('JWT_SECRET_KEY', 'claveProyecto');
}

// Intentar conectar a la base de datos
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if($link === false){
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

if (!defined('JWT_ALGORITHM')) {
    define('JWT_ALGORITHM', 'HS256');
}

if (!defined('JWT_ISSUER')) {
    define('JWT_ISSUER', 'proyecto-php');
}

if (!defined('JWT_VALID_AUDIENCE')) {
    define('JWT_VALID_AUDIENCE', 'usuarios-app');
}

?>