<?php
session_start();

require_once '../config.php';
require_once 'funciones.php'; 
require_once '../includes/navbar.php'; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !esAdministrador()) { 
    header("location: ../index.php"); 
    exit;
}

$redirect_url = "index.php?msg=error"; // Redirección por defecto en caso de fallo

if (isset($_GET["id_cargo"]) && !empty(trim($_GET["id_cargo"]))) {
    $id_cargo = trim($_GET["id_cargo"]);

    // Llamar a la función eliminarCargo, que ahora devuelve un array con 'success' o 'error'
    $resultado = eliminarCargo($link, $id_cargo);

    if (isset($resultado['success'])) {
        $redirect_url = "index.php?msg=eliminado"; // Mensaje de éxito
    } else {
        // En caso de error, redirigir con el mensaje de error específico
        $redirect_url = "index.php?msg=error_eliminar&detail=" . urlencode($resultado['error']);
    }
} else {
    // Si no se proporcionó un ID, redirigir a la lista de cargos
    $redirect_url = "index.php?msg=error";
}

mysqli_close($link);
header("location: " . $redirect_url);
exit();
?>