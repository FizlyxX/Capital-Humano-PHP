<?php
session_start();

require_once '../config.php';
require_once 'funciones.php'; 

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !esAdministrador()) {
    header("location: ../index.php");
    exit;
}
// Inicializar $redirect_url para controlar la redirección
$redirect_url = "index.php?msg=error"; // Redirección por defecto en caso de fallo

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    // Usar la función activarUsuario (que está en funciones.php)
    if (activarUsuario($link, $id)) {
        $redirect_url = "index.php?msg=activado";
    } else {
        $redirect_url = "index.php?msg=error"; 
    }
} else {
    // Si no se proporcionó un ID, redirigir a index.php sin mensaje específico de error de operación
    $redirect_url = "index.php";
}

mysqli_close($link);

header("location: " . $redirect_url);
exit();
?>