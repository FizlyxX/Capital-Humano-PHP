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

if (isset($_GET["id_rol"]) && !empty(trim($_GET["id_rol"]))) {
    $id_rol = trim($_GET["id_rol"]);

    // Intentar eliminar el rol. La función ya tiene la lógica para verificar usuarios asignados.
    if (eliminarRol($link, $id_rol)) {
        $redirect_url = "index.php?msg=eliminado";
    } else {
        // Error al eliminar, posiblemente por usuarios asignados
        $redirect_url = "index.php?msg=error_eliminar_usuarios"; // Mensaje más específico
    }
} else {
    // Si no se proporcionó un ID, redirigir a index.php sin mensaje específico de error de operación
    $redirect_url = "index.php";
}

mysqli_close($link);

header("location: " . $redirect_url);
exit();
?>