<?php
// Iniciar la sesión
session_start();

// Vaciar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión.
session_destroy();

// Redirigir al login
header("location: index.php");
exit;
?>