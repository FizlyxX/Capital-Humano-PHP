<?php
require_once __DIR__ . '/../config.php';

// --- Funciones de Verificación de Roles ---
/**
 * Verifica si el usuario actual tiene un rol específico.
 * @param int $rol_requerido El ID del rol que se requiere.
 * @return bool True si el usuario tiene el rol requerido, false en caso contrario.
 */
function tieneRol($rol_requerido) {
    return (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["id_rol"]) && $_SESSION["id_rol"] === $rol_requerido);
}

/**
 * Verifica si el usuario actual es Administrador.
 * Asume que el ID del rol 'Administrador' es 1. 
 * @return bool True si el usuario es administrador, false en caso contrario.
 */
function esAdministrador() {
    return tieneRol(1); // ID del rol de Administrador
}
/**
 * Verifica si el usuario actual tiene el rol de RRHH.
 * Asume que el ID del rol de RRHH es 2. 
 * @return bool True si el usuario es RRHH, false en caso contrario.
 */
function esRRHH() {
    return tieneRol(2); // Asumiendo que 2 es el id_rol para 'RRHH'
}

// --- Funciones CRUD para Usuarios ---

// Obtener todos los usuarios (filtrando por activos/inactivos)
function getUsuarios($link, $mostrar_todos = false) {
    $usuarios = [];
    // u.activo se añade para mostrar el estado
    $sql = "SELECT u.id, u.nombre_usuario, r.nombre_rol, u.activo FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol";

    if (!$mostrar_todos) {
        $sql .= " WHERE u.activo = 1"; // Por defecto, solo usuarios activos
    }

    $sql .= " ORDER BY u.nombre_usuario ASC";

    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $usuarios[] = $row;
        }
        mysqli_free_result($result);
    }
    return $usuarios;
}

// Obtener un usuario por su ID
function getUsuarioById($link, $id) {
    $usuario = null;
    $sql = "SELECT id, nombre_usuario, contrasena, id_rol, activo FROM usuarios WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $usuario = mysqli_fetch_assoc($result);
            }
        }
        mysqli_stmt_close($stmt);
    }
    return $usuario;
}

// Obtener todos los roles
function getRoles($link) {
    $roles = [];
    $sql = "SELECT id_rol, nombre_rol FROM roles ORDER BY nombre_rol ASC";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }
        mysqli_free_result($result);
    }
    return $roles;
}

// Crear un nuevo usuario
function crearUsuario($link, $nombre_usuario, $contrasena, $id_rol) {
    $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT); // Hashear la contraseña

    $sql = "INSERT INTO usuarios (nombre_usuario, contrasena, id_rol, activo) VALUES (?, ?, ?, 1)"; // Por defecto, activo
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $nombre_usuario, $hashed_password, $id_rol);
        if (mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Actualizar un usuario existente
function actualizarUsuario($link, $id, $nombre_usuario, $contrasena, $id_rol) {
    if (!empty($contrasena)) { // Si se proporciona una nueva contraseña
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre_usuario = ?, contrasena = ?, id_rol = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssii", $nombre_usuario, $hashed_password, $id_rol, $id);
        } else { return false; }
    } else { // Si no se proporciona nueva contraseña, no actualizarla
        $sql = "UPDATE usuarios SET nombre_usuario = ?, id_rol = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sii", $nombre_usuario, $id_rol, $id);
        } else { return false; }
    }

    if (mysqli_stmt_execute($stmt)) { return true; }
    mysqli_stmt_close($stmt);
    return false;
}

// Desactivar un usuario (NO ELIMINAR)
function desactivarUsuario($link, $id) {
    $sql = "UPDATE usuarios SET activo = 0 WHERE id = ?"; // Cambia 'activo' a 0
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Activar un usuario (¡NUEVA FUNCIÓN!)
function activarUsuario($link, $id) {
    $sql = "UPDATE usuarios SET activo = 1 WHERE id = ?"; // Cambia 'activo' a 1
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}
?>