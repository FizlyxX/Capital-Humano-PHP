<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../usuarios/funciones.php'; // Para usar esAdministrador() y tieneRol()

// --- Funciones CRUD para Roles ---

// Función para obtener todos los roles
function getTodosLosRoles($link) {
    $roles = [];
    $sql = "SELECT id_rol, nombre_rol, descripcion FROM roles ORDER BY nombre_rol ASC";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }
        mysqli_free_result($result);
    }
    return $roles;
}

// Función para obtener un rol por su ID
function getRolById($link, $id_rol) {
    $rol = null;
    $sql = "SELECT id_rol, nombre_rol, descripcion FROM roles WHERE id_rol = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_rol);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $rol = mysqli_fetch_assoc($result);
            }
        }
        mysqli_stmt_close($stmt);
    }
    return $rol;
}

// Función para crear un nuevo rol
function crearRol($link, $nombre_rol, $descripcion) {
    $sql = "INSERT INTO roles (nombre_rol, descripcion) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $nombre_rol, $descripcion);
        if (mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Función para actualizar un rol existente
function actualizarRol($link, $id_rol, $nombre_rol, $descripcion) {
    $sql = "UPDATE roles SET nombre_rol = ?, descripcion = ? WHERE id_rol = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $nombre_rol, $descripcion, $id_rol);
        if (mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Función para eliminar un rol
function eliminarRol($link, $id_rol) {
    // Primero, verificar si hay usuarios asignados a este rol para mantener la integridad referencial
    $sql_check = "SELECT COUNT(*) AS num_users FROM usuarios WHERE id_rol = ?";
    if ($stmt_check = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "i", $id_rol);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        mysqli_stmt_close($stmt_check);

        if ($row_check['num_users'] > 0) {
            // No se puede eliminar si hay usuarios asignados
            return false; // Indicando que falló por usuarios asociados
        }
    } else {
        return false; // Error en la verificación
    }

    // Si no hay usuarios asignados, proceder con la eliminación
    $sql_delete = "DELETE FROM roles WHERE id_rol = ?";
    if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt_delete, "i", $id_rol);
        if (mysqli_stmt_execute($stmt_delete)) {
            return true;
        }
        mysqli_stmt_close($stmt_delete);
    }
    return false;
}
?>