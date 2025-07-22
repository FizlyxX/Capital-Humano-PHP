<?php
session_start();

require_once '../config.php';
require_once 'funciones.php';
require_once '../includes/navbar.php'; // Incluir para usar esAdministrador()

// Verificar si el usuario ha iniciado sesión y tiene permisos de Administrador o RRHH
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!esAdministrador() && !esRRHH())) {
    header("location: ../index.php"); // Redirigir al login si no tiene permisos
    exit;
}

$redirect_url = "index.php?msg=error";

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    // Usar la función desactivarColaborador
    if (desactivarColaborador($link, $id)) {
        $redirect_url = "index.php?msg=desactivado";
    } else {
        $redirect_url = "index.php?msg=error"; // O un mensaje más específico si falla la desactivación
    }
} else {
    // Si no se proporcionó un ID, redirigir
    $redirect_url = "index.php";
}

mysqli_close($link);
header("location: " . $redirect_url);
exit();
?>