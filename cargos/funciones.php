<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../usuarios/funciones.php';
require_once __DIR__ . '/../colaboradores/funciones.php';

// --- Constantes para OpenSSL ---
define('PRIVATE_KEY_PATH', __DIR__ . '/../keys/private_key.pem');
define('PUBLIC_KEY_PATH', __DIR__ . '/../keys/public_key.pem');

define('PRIVATE_KEY_PASSPHRASE', 'melon17');

// --- Funciones para Departamentos ---
function getDepartamentos($link) {
    $departamentos = [];
    $sql = "SELECT id_departamento, nombre_departamento FROM departamentos ORDER BY nombre_departamento ASC";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $departamentos[] = $row;
        }
        mysqli_free_result($result);
    }
    return $departamentos;
}

// --- Funciones para Ocupaciones ---
function getOcupaciones($link) {
    $ocupaciones = [];
    $sql = "SELECT id_ocupacion, nombre_ocupacion FROM ocupaciones ORDER BY nombre_ocupacion ASC";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ocupaciones[] = $row;
        }
        mysqli_free_result($result);
    }
    return $ocupaciones;
}

// --- Funciones de Firma Digital (OpenSSL) ---

/**
 * Firma un conjunto de datos usando OpenSSL.
 */
function firmarDatosCargo($data_to_sign) {
    if (!extension_loaded('openssl')) {
        error_log("OpenSSL PHP extension not loaded.");
        return false;
    }

    // Cargar la clave privada usando la PASSPHRASE
    $private_key = openssl_pkey_get_private(file_get_contents(PRIVATE_KEY_PATH), PRIVATE_KEY_PASSPHRASE);
    if (!$private_key) {
        error_log("Failed to load private key: " . openssl_error_string());
        return false;
    }

    $serialized_data = json_encode($data_to_sign);

    // Firmar los datos.
    if (!openssl_sign($serialized_data, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
        error_log("Failed to sign data: " . openssl_error_string());
        return false;
    }

    return base64_encode($signature);
}

/**
 * Verifica la integridad de los datos de un cargo usando su firma digital.
 */
function verificarFirmaCargo($data_to_verify, $signature_b64) {
    if (!extension_loaded('openssl')) {
        error_log("OpenSSL PHP extension not loaded.");
        return false;
    }

    $public_key = openssl_pkey_get_public(file_get_contents(PUBLIC_KEY_PATH));
    if (!$public_key) {
        error_log("Failed to load public key: " . openssl_error_string());
        return false;
    }

    $serialized_data = json_encode($data_to_verify);
    $signature = base64_decode($signature_b64);

    $result = openssl_verify($serialized_data, $signature, $public_key, OPENSSL_ALGO_SHA256);

    return ($result === 1);
}


// --- Funciones CRUD para Cargos ---

/**
 * Obtiene cargos.
 *
 * @param mysqli $link La conexión a la base de datos.
 * @param bool $mostrarSoloInactivosColaboradores Si es true, filtra por colaboradores con activo = 0. Si es false, filtra por colaboradores con activo = 1.
 * @return array Una lista de cargos.
 */
function getCargos($link, $mostrarSoloInactivosColaboradores = false) {
    $cargos = [];
    $sql = "SELECT c.id_cargo, c.id_colaborador, c.id_departamento, c.id_ocupacion, CONCAT(col.primer_nombre, ' ', col.segundo_nombre, ' ', col.primer_apellido, ' ', col.segundo_apellido) AS nombre_colaborador,
                   d.nombre_departamento, o.nombre_ocupacion,
                   c.sueldo, c.fecha_contratacion,
                   c.tipo_colaborador, c.activo_en_cargo, c.firma_datos, c.fecha_firma
            FROM cargos c
            JOIN colaboradores col ON c.id_colaborador = col.id_colaborador
            JOIN departamentos d ON c.id_departamento = d.id_departamento
            JOIN ocupaciones o ON c.id_ocupacion = o.id_ocupacion
            WHERE c.activo_en_cargo = TRUE"; // Siempre muestra solo los cargos actuales

    if ($mostrarSoloInactivosColaboradores) {
        $sql .= " AND col.activo = FALSE"; // Agrega la condición para colaboradores inactivos
    } else {
        $sql .= " AND col.activo = TRUE"; // Agrega la condición para colaboradores activos
    }

    $sql .= " ORDER BY nombre_colaborador ASC, c.fecha_contratacion DESC";

    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cargos[] = $row;
        }
        mysqli_free_result($result);
    }
    return $cargos;
}


/**
 * Obtiene el historial completo de cargos para un colaborador específico.
 */
function getHistorialCargosColaborador($link, $id_colaborador) {
    $historial = [];
    // AÑADIDO: c.id_departamento y c.id_ocupacion a la selección explícita
    $sql = "SELECT c.id_cargo, c.id_colaborador, c.id_departamento, c.id_ocupacion, CONCAT(col.primer_nombre, ' ', col.segundo_nombre, ' ', col.primer_apellido, ' ', col.segundo_apellido) AS nombre_colaborador, 
                   d.nombre_departamento, o.nombre_ocupacion, 
                   c.sueldo, c.fecha_contratacion, 
                   c.tipo_colaborador, c.activo_en_cargo, c.firma_datos, c.fecha_firma
            FROM cargos c
            JOIN colaboradores col ON c.id_colaborador = col.id_colaborador
            JOIN departamentos d ON c.id_departamento = d.id_departamento
            JOIN ocupaciones o ON c.id_ocupacion = o.id_ocupacion
            WHERE c.id_colaborador = ?
            ORDER BY c.fecha_contratacion DESC";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_colaborador);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $historial[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    return $historial;
}

/**
 * Obtiene los detalles de un cargo por su ID.
 */
function getCargoById($link, $id_cargo) {
    $cargo = null;
    // Selecciona todos los campos de la tabla 'cargos' directamente
    $sql = "SELECT * FROM cargos WHERE id_cargo = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_cargo);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $cargo = mysqli_fetch_assoc($result);
            }
        }
        mysqli_stmt_close($stmt);
    }
    return $cargo;
}


/**
 * Crea un nuevo cargo para un colaborador, desactivando cualquier cargo activo previo.
 * Los datos del cargo son firmados digitalmente con OpenSSL.
 */
function crearCargo($link, $id_colaborador, $id_departamento, $id_ocupacion, $sueldo, $fecha_contratacion, $tipo_colaborador) {
    // 1. Desactivar cualquier cargo activo previo para este colaborador
    $sql_desactivar_antiguos = "UPDATE cargos SET activo_en_cargo = FALSE WHERE id_colaborador = ? AND activo_en_cargo = TRUE";
    if ($stmt_desactivar = mysqli_prepare($link, $sql_desactivar_antiguos)) {
        mysqli_stmt_bind_param($stmt_desactivar, "i", $id_colaborador);
        mysqli_stmt_execute($stmt_desactivar);
        mysqli_stmt_close($stmt_desactivar);
    } else {
        return ['error' => 'Error al preparar la desactivación de cargos antiguos: ' . mysqli_error($link)];
    }

     // 2. Preparar los datos que serán firmados (deben ser consistentes)
    $data_to_sign = [
        'id_colaborador' => $id_colaborador,
        'id_departamento' => $id_departamento,
        'id_ocupacion' => $id_ocupacion,
        'sueldo' => (float)$sueldo, 
        'fecha_contratacion' => $fecha_contratacion,
        'tipo_colaborador' => $tipo_colaborador,
        'timestamp' => date('Y-m-d H:i:s') 
    ];

    // 3. Firmar los datos del cargo
    $firma_datos = firmarDatosCargo($data_to_sign);
    if ($firma_datos === false) {
        return ['error' => 'Error al firmar los datos del cargo. Verifique configuración OpenSSL y claves.'];
    }
    $fecha_firma = date('Y-m-d H:i:s'); // La fecha y hora en que se firmó el cargo

    // 4. Insertar el nuevo cargo como activo, incluyendo la firma y su fecha
    $sql_insert = "INSERT INTO cargos (id_colaborador, id_departamento, id_ocupacion, sueldo, fecha_contratacion, tipo_colaborador, activo_en_cargo, firma_datos, fecha_firma) VALUES (?, ?, ?, ?, ?, ?, TRUE, ?, ?)";
    if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
        // CORRECCIÓN CLAVE AQUÍ: "iiidssss" para 8 variables vinculadas
        mysqli_stmt_bind_param($stmt_insert, "iiidssss", 
            $id_colaborador, $id_departamento, $id_ocupacion, $sueldo, $fecha_contratacion, $tipo_colaborador, $firma_datos, $fecha_firma
        );
        if (mysqli_stmt_execute($stmt_insert)) {
            $new_cargo_id = mysqli_insert_id($link); // Obtener el ID del nuevo cargo
            mysqli_stmt_close($stmt_insert);
            return ['success' => true, 'new_cargo_id' => $new_cargo_id];
        } else {
            mysqli_stmt_close($stmt_insert);
            return ['error' => 'Error al insertar el nuevo cargo en la BD: ' . mysqli_error($link)];
        }
    }
    return ['error' => 'Error en la preparación de la consulta SQL para insertar cargo: ' . mysqli_error($link)];
}

/**
 * Actualiza un cargo existente. Si se activa, desactiva otros cargos del mismo colaborador.
 * Los datos del cargo son firmados digitalmente de nuevo al actualizarse.
 */
function actualizarCargo($link, $id_cargo, $id_colaborador, $id_departamento, $id_ocupacion, $sueldo, $fecha_contratacion, $tipo_colaborador, $activo_en_cargo) {
    // Si el cargo editado se está marcando como ACTIVO, desactivar los demás cargos del colaborador.
    if ($activo_en_cargo == TRUE) {
        $sql_desactivar_otros = "UPDATE cargos SET activo_en_cargo = FALSE WHERE id_colaborador = ? AND id_cargo != ?";
        if ($stmt_desactivar = mysqli_prepare($link, $sql_desactivar_otros)) {
            mysqli_stmt_bind_param($stmt_desactivar, "ii", $id_colaborador, $id_cargo);
            mysqli_stmt_execute($stmt_desactivar);
            mysqli_stmt_close($stmt_desactivar);
        } else {
            return ['error' => 'Error al preparar la desactivación de otros cargos: ' . mysqli_error($link)];
        }
    }

     // Preparar los datos para la firma (para actualizar la firma del cargo editado)
    $data_to_sign = [
        'id_colaborador' => $id_colaborador,
        'id_departamento' => $id_departamento,
        'id_ocupacion' => $id_ocupacion,
        'sueldo' => (float)$sueldo, 
        'fecha_contratacion' => $fecha_contratacion,
        'tipo_colaborador' => $tipo_colaborador,
        'timestamp' => date('Y-m-d H:i:s') // Nuevo timestamp para la firma de actualización
    ];
    $firma_datos = firmarDatosCargo($data_to_sign);
    if ($firma_datos === false) {
        return ['error' => 'Error al firmar los datos del cargo durante la actualización.'];
    }
    $fecha_firma = date('Y-m-d H:i:s');


    $sql = "UPDATE cargos SET
                id_colaborador = ?, id_departamento = ?, id_ocupacion = ?, sueldo = ?,
                fecha_contratacion = ?, tipo_colaborador = ?, activo_en_cargo = ?,
                firma_datos = ?, fecha_firma = ?
            WHERE id_cargo = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "iiidssissi", // iidss: ints, float, strings; i: bool; s: firma; s: fecha_firma
            $id_colaborador, $id_departamento, $id_ocupacion, $sueldo,
            $fecha_contratacion, $tipo_colaborador, $activo_en_cargo,
            $firma_datos, $fecha_firma, $id_cargo
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'Error al actualizar el cargo en la base de datos: ' . mysqli_error($link)];
        }
    }
    return ['error' => 'Error en la preparación de la consulta SQL para actualizar cargo: ' . mysqli_error($link)];
}

/**
 * Elimina físicamente un cargo de la base de datos.
 */
function eliminarCargo($link, $id_cargo) {
    $cargo_a_eliminar = getCargoById($link, $id_cargo);

    if (!$cargo_a_eliminar) {
        return ['error' => 'Cargo no encontrado.'];
    }

    if (($cargo_a_eliminar['activo_en_cargo'] ?? 0) == 1) {
        $sql_count_cargos = "SELECT COUNT(*) AS total_cargos FROM cargos WHERE id_colaborador = ?";
        $stmt_count = mysqli_prepare($link, $sql_count_cargos);
        mysqli_stmt_bind_param($stmt_count, "i", $cargo_a_eliminar['id_colaborador']);
        mysqli_stmt_execute($stmt_count);
        $result_count = mysqli_stmt_get_result($stmt_count);
        $row_count = mysqli_fetch_assoc($result_count);
        mysqli_stmt_close($stmt_count);

        if (($row_count['total_cargos'] ?? 0) > 1) {
            return ['error' => 'No se puede eliminar el cargo activo. Desactive este cargo o asigne un nuevo cargo al colaborador primero.'];
        } else {
            return ['error' => 'No se puede eliminar el único cargo activo del colaborador. El colaborador debe tener al menos un cargo activo.'];
        }
    }

    $sql = "DELETE FROM cargos WHERE id_cargo = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_cargo);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'Error al eliminar el cargo de la base de datos: ' . mysqli_error($link)];
        }
    }
    return ['error' => 'Error en la preparación de la consulta SQL para eliminar cargo.'];
}
?>