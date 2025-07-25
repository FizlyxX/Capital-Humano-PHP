<?php
// Incluye el archivo de configuración de la base de datos (subir dos niveles desde /colaboradores/)
require_once __DIR__ . '/../config.php';
// Incluye funciones del módulo de usuarios para la verificación de roles
require_once __DIR__ . '/../usuarios/funciones.php'; 

// --- Constantes para rutas de subida ---
// Rutas absolutas en el servidor donde se guardarán los archivos
define('UPLOAD_DIR_FOTOS', __DIR__ . '/../uploads/fotos_perfil/');
define('UPLOAD_DIR_PDFS', __DIR__ . '/../uploads/historiales_academicos/');

// Rutas relativas para ser usadas en el HTML (para mostrar las imágenes/PDFs en el navegador)
define('URL_BASE_FOTOS', '../uploads/fotos_perfil/'); 
define('URL_BASE_PDFS', '../uploads/historiales_academicos/'); 

// --- Constantes para redimensionamiento de imágenes ---
define('THUMBNAIL_WIDTH', 100);  // Ancho deseado para la miniatura
define('THUMBNAIL_HEIGHT', 100); // Alto deseado para la miniatura
define('ORIGINAL_PHOTO_WIDTH', 500); // Ancho deseado para la foto "original" (tamaño uniforme)
define('ORIGINAL_PHOTO_HEIGHT', 500); // Alto deseado para la foto "original" (tamaño uniforme)

// --- Funciones para Colaboradores ---

/**
 * Obtiene una lista de colaboradores de la base de datos.
 */
function getColaboradores($link, $mostrar_inactivos = false) {
    $colaboradores = [];
    $sql = "SELECT * FROM colaboradores";
    if (!$mostrar_inactivos) {
        $sql .= " WHERE activo = 1"; // Solo trae colaboradores activos por defecto
    }
    $sql .= " ORDER BY primer_apellido ASC, primer_nombre ASC";

    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $colaboradores[] = $row;
        }
        mysqli_free_result($result);
    }
    return $colaboradores;
}

/**
 * Obtiene los detalles de un colaborador por su ID.
 */
function getColaboradorById($link, $id) {
    $colaborador = null;
    $sql = "SELECT * FROM colaboradores WHERE id_colaborador = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $colaborador = mysqli_fetch_assoc($result);
            }
        }
        mysqli_stmt_close($stmt);
    }
    return $colaborador;
}


/**
 * Obtiene el estatus del colaborador por su ID.
 *
 * @param mysqli $link La conexión a la base de datos.
 * @param int $id El ID del colaborador.
 * @return string|null El estatus como texto, o null si no se encuentra.
 */
function getEstatus($link, $id): ?string
{
    $estatus = null;
    $sql = "SELECT e.estatus FROM colaboradores c INNER JOIN estatus_colaborador e ON c.estatus_id = e.id WHERE c.id_colaborador = ?;";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $estatusValor);
            if (mysqli_stmt_fetch($stmt)) {
                $estatus = $estatusValor;
            }
        }
        mysqli_stmt_close($stmt);
    }
    return $estatus;
}

/**
 * Sube y redimensiona una imagen de perfil, eliminando versiones antiguas si aplica.
 */
function subirYRedimensionarFotoPerfil($file_input_name, $existing_photo_url_from_db = null) {
    // Si no se sube un nuevo archivo, mantener el existente
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] == UPLOAD_ERR_NO_FILE) {
        return ['success' => $existing_photo_url_from_db];
    }

    $file = $_FILES[$file_input_name];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($file_extension, $allowed_extensions)) {
        return ['error' => 'Tipo de archivo no permitido para la foto (solo JPG, JPEG, PNG, GIF).'];
    }

    // Generar un nombre BASE único para el archivo (SIN PREFIJOS AÚN)
    $unique_base_name = uniqid('foto_') . '.' . $file_extension; // Ej: foto_66a7b8c9d0e1f.jpg
    $temp_original_path_server = UPLOAD_DIR_FOTOS . $unique_base_name; // Path temporal en el servidor

    // Mover el archivo subido al directorio temporal
    if (!move_uploaded_file($file['tmp_name'], $temp_original_path_server)) {
        return ['error' => 'Error al mover el archivo subido. Verifique permisos de escritura.'];
    }

    // --- Procesar y Redimensionar ---
    // Usar @ para suprimir warnings si getimagesize falla (ej. archivo no es una imagen válida)
    list($width, $height, $type) = @getimagesize($temp_original_path_server); 
    if (!$type || !in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF])) {
        // Eliminar el archivo temporal si no es una imagen válida
        if (file_exists($temp_original_path_server)) {
            @unlink($temp_original_path_server);
        }
        return ['error' => 'El archivo subido no es una imagen válida o soportada.'];
    }

    $source_image = null;
    switch ($type) {
        case IMAGETYPE_JPEG: $source_image = imagecreatefromjpeg($temp_original_path_server); break;
        case IMAGETYPE_PNG:  $source_image = imagecreatefrompng($temp_original_path_server); break;
        case IMAGETYPE_GIF:  $source_image = imagecreatefromgif($temp_original_path_server); break;
    }

    if (!$source_image) {
        // Eliminar el archivo temporal si no se pudo crear la imagen GD
        if (file_exists($temp_original_path_server)) {
            @unlink($temp_original_path_server);
        }
        return ['error' => 'Error al procesar la imagen subida (GD resource).'];
    }

    // Rutas ABSOLUTAS de los ARCHIVOS FINALES en el servidor (con los prefijos)
    $final_original_file_path = UPLOAD_DIR_FOTOS . 'original_' . $unique_base_name;
    // CORRECCIÓN CLAVE AQUÍ: La miniatura DEBE tener el prefijo 'thumb_original_' para coincidir con la BD
    $final_thumbnail_file_path = UPLOAD_DIR_FOTOS . 'thumb_original_' . $unique_base_name; 

    // Redimensionar y guardar el archivo original redimensionado
    redimensionarImagen($source_image, $final_original_file_path, ORIGINAL_PHOTO_WIDTH, ORIGINAL_PHOTO_HEIGHT, $file_extension, $type);

    // Redimensionar y guardar la miniatura
    redimensionarImagen($source_image, $final_thumbnail_file_path, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT, $file_extension, $type);

    imagedestroy($source_image); // Liberar memoria de la imagen fuente GD
    @unlink($temp_original_path_server); // Eliminar el archivo temporal sin redimensionar (siempre se elimina)

    // --- Eliminar archivos antiguos (si existían y se subió uno nuevo con éxito) ---
    if ($existing_photo_url_from_db && !empty($existing_photo_url_from_db)) {
        // Obtiene solo el nombre del archivo de la URL de la BD (ej. 'original_foto_OLD.jpg')
        $old_base_name_from_db_url = basename($existing_photo_url_from_db); 
        
        // Rutas ABSOLUTAS completas de los archivos antiguos en el servidor
        $old_original_path_server = UPLOAD_DIR_FOTOS . $old_base_name_from_db_url;
        // CORRECCIÓN CLAVE AQUÍ: Al eliminar la miniatura antigua, su nombre también sigue el patrón 'thumb_original_'
        $old_thumbnail_path_server = UPLOAD_DIR_FOTOS . 'thumb_original_' . $old_base_name_from_db_url; 

        if (file_exists($old_original_path_server)) {
            @unlink($old_original_path_server);
        }
        if (file_exists($old_thumbnail_path_server)) {
            @unlink($old_thumbnail_path_server);
        }
    }
    
    // Devolver la URL RELATIVA para guardar en la BD (solo con 'original_')
    return ['success' => URL_BASE_FOTOS . 'original_' . $unique_base_name];
}

/**
 * Función auxiliar para redimensionar imágenes.
 */
function redimensionarImagen($source_gd_image, $target_path, $target_width, $target_height, $extension, $type) {
    $width = imagesx($source_gd_image);
    $height = imagesy($source_gd_image);

    // Calcular nuevas dimensiones manteniendo la proporción
    $ratio_orig = $width / $height;
    if ($target_width / $target_height > $ratio_orig) {
        $target_width = $target_height * $ratio_orig;
    } else {
        $target_height = $target_width / $ratio_orig;
    }

    $resized_image = imagecreatetruecolor(round($target_width), round($target_height));

    // Conservar transparencia para PNG y GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($resized_image, false);
        imagesavealpha($resized_image, true);
        $transparent = imagecolorallocatealpha($resized_image, 255, 255, 255, 127);
        imagefilledrectangle($resized_image, 0, 0, round($target_width), round($target_height), $transparent);
    }

    imagecopyresampled($resized_image, $source_gd_image, 0, 0, 0, 0, round($target_width), round($target_height), $width, $height);

    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($resized_image, $target_path, 90); // Calidad 90
            break;
        case 'png':
            imagepng($resized_image, $target_path);
            break;
        case 'gif':
            imagegif($resized_image, $target_path);
            break;
    }
    imagedestroy($resized_image); // Liberar memoria
}

/**
 * Sube un archivo PDF al servidor, eliminando la versión antigua si aplica.
 */
function subirPDF($file_input_name, $existing_pdf_url = null) {
    // Si no se sube un nuevo archivo, mantener el existente
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] == UPLOAD_ERR_NO_FILE) {
        return ['success' => $existing_pdf_url];
    }

    $file = $_FILES[$file_input_name];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file_extension != 'pdf') {
        return ['error' => 'Solo se permiten archivos PDF.'];
    }

    $file_name = uniqid('pdf_') . '.' . $file_extension;
    $target_path = UPLOAD_DIR_PDFS . $file_name; // Ruta absoluta en el servidor

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['error' => 'Error al subir el archivo PDF. Verifique permisos de escritura.'];
    }

    // Si ya existía un PDF, borrar el archivo antiguo
    if ($existing_pdf_url && !empty($existing_pdf_url)) {
        $old_pdf_file_name = basename($existing_pdf_url);
        if (file_exists(UPLOAD_DIR_PDFS . $old_pdf_file_name)) {
            @unlink(UPLOAD_DIR_PDFS . $old_pdf_file_name);
        }
    }
    // Retorna la URL relativa para guardar en la BD
    return ['success' => URL_BASE_PDFS . $file_name];
}

/**
 * Crea un nuevo colaborador en la base de datos y gestiona la subida de archivos.
 */
function crearColaborador($link, $data, $foto_file_input_name, $pdf_file_input_name) {
    // Manejo de la subida de foto
    $foto_result = subirYRedimensionarFotoPerfil($foto_file_input_name);
    if (isset($foto_result['error'])) {
        return ['error' => $foto_result['error']];
    }
    $ruta_foto_perfil = $foto_result['success'];

    // Manejo de la subida de PDF
    $pdf_result = subirPDF($pdf_file_input_name);
    if (isset($pdf_result['error'])) {
        // Opcional: borrar la foto si el PDF falla y la foto se subió (solo si es una nueva subida)
        if (!empty($ruta_foto_perfil) && strpos($ruta_foto_perfil, 'original_') !== false) { 
            $uploaded_photo_name = basename($ruta_foto_perfil);
            if (file_exists(UPLOAD_DIR_FOTOS . 'original_' . $uploaded_photo_name)) {
                @unlink(UPLOAD_DIR_FOTOS . 'original_' . $uploaded_photo_name);
            }
            if (file_exists(UPLOAD_DIR_FOTOS . 'thumb_original_' . $uploaded_photo_name)) { 
                @unlink(UPLOAD_DIR_FOTOS . 'thumb_original_' . $uploaded_photo_name);
            }
        }
        return ['error' => $pdf_result['error']];
    }
    $ruta_historial_academico_pdf = $pdf_result['success'];

    $sql = "INSERT INTO colaboradores (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, sexo, identificacion, fecha_nacimiento, correo_personal, telefono, celular, direccion, ruta_foto_perfil, ruta_historial_academico_pdf, fecha_ingreso, estatus_id, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssssssssssssi", // Son 13 's' para 13 variables
            $data['primer_nombre'], $data['segundo_nombre'], $data['primer_apellido'],
            $data['segundo_apellido'], $data['sexo'], $data['identificacion'],
            $data['fecha_nacimiento'], $data['correo_personal'], $data['telefono'],
            $data['celular'], $data['direccion'], $ruta_foto_perfil,
            $ruta_historial_academico_pdf, $data['fecha_ingreso'], $data['estatus_id']
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            // Si la inserción en la BD falla, intenta borrar los archivos subidos (si es una nueva subida)
            if (!empty($ruta_foto_perfil) && strpos($ruta_foto_perfil, 'original_') !== false) {
                $uploaded_photo_name = basename($ruta_foto_perfil);
                if (file_exists(UPLOAD_DIR_FOTOS . 'original_' . $uploaded_photo_name)) {
                    @unlink(UPLOAD_DIR_FOTOS . 'original_' . $uploaded_photo_name);
                }
                if (file_exists(UPLOAD_DIR_FOTOS . 'thumb_original_' . $uploaded_photo_name)) { 
                    @unlink(UPLOAD_DIR_FOTOS . 'thumb_original_' . $uploaded_photo_name);
                }
            }
            if (!empty($ruta_historial_academico_pdf) && strpos($ruta_historial_academico_pdf, 'pdf_') !== false) {
                if (file_exists(UPLOAD_DIR_PDFS . basename($ruta_historial_academico_pdf))) {
                    @unlink(UPLOAD_DIR_PDFS . basename($ruta_historial_academico_pdf));
                }
            }
            if (mysqli_errno($link) == 1062) { // Error de UNIQUE constraint violation
                mysqli_stmt_close($stmt);
                return ['error' => 'La identificación (cédula) ya existe para otro colaborador.'];
            }
            mysqli_stmt_close($stmt);
            return ['error' => 'Error al guardar el colaborador en la base de datos: ' . mysqli_error($link)]; // Añadir error de MySQL para depuración
        }
    }
    return ['error' => 'Error en la preparación de la consulta SQL para crear.'];
}

/**
 * Actualiza un colaborador existente en la base de datos y gestiona la subida de archivos.
 */
function actualizarColaborador($link, $id_colaborador, $data, $foto_file_input_name, $pdf_file_input_name) {
    // Obtener las rutas actuales de foto y PDF del colaborador desde la BD para pasarlas a las funciones de subida
    $colaborador_existente = getColaboradorById($link, $id_colaborador);
    if (!$colaborador_existente) {
        return ['error' => 'Colaborador no encontrado para actualizar.'];
    }
    $old_foto_url = $colaborador_existente['ruta_foto_perfil'] ?? null; 
    $old_pdf_url = $colaborador_existente['ruta_historial_academico_pdf'] ?? null;


    // Manejo de la subida de foto (subirYRedimensionarFotoPerfil gestiona el borrado de la antigua si se sube una nueva)
    $foto_result = subirYRedimensionarFotoPerfil($foto_file_input_name, $old_foto_url);
    if (isset($foto_result['error'])) {
        return ['error' => $foto_result['error']];
    }
    $ruta_foto_perfil = $foto_result['success'];

    // Manejo de la subida de PDF (subirPDF gestiona el borrado de la antigua si se sube uno nuevo)
    $pdf_result = subirPDF($pdf_file_input_name, $old_pdf_url);
    if (isset($pdf_result['error'])) {
        return ['error' => $pdf_result['error']];
    }
    $ruta_historial_academico_pdf = $pdf_result['success'];

    $sql = "UPDATE colaboradores SET
                primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?, segundo_apellido = ?,
                sexo = ?, identificacion = ?, fecha_nacimiento = ?, correo_personal = ?,
                telefono = ?, celular = ?, direccion = ?, ruta_foto_perfil = ?,
                ruta_historial_academico_pdf = ?, fecha_ingreso = ?, estatus_id = ?
            WHERE id_colaborador = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssssssssssssii",
            $data['primer_nombre'], $data['segundo_nombre'], $data['primer_apellido'],
            $data['segundo_apellido'], $data['sexo'], $data['identificacion'],
            $data['fecha_nacimiento'], $data['correo_personal'], $data['telefono'],
            $data['celular'], $data['direccion'], $ruta_foto_perfil,
            $ruta_historial_academico_pdf, $data['fecha_ingreso'], $data['estatus_id'] , $id_colaborador
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } else {
            if (mysqli_errno($link) == 1062) { // Error de UNIQUE constraint violation
                mysqli_stmt_close($stmt);
                return ['error' => 'La identificación (cédula) ya existe para otro colaborador.'];
            }
            mysqli_stmt_close($stmt);
            return ['error' => 'Error al actualizar el colaborador en la base de datos: ' . mysqli_error($link)]; // Añadir error de MySQL para depuración
        }
    }
    return ['error' => 'Error en la preparación de la consulta SQL para actualización.'];
}

/**
 * Desactiva un colaborador en la base de datos (cambia su estado 'activo' a 0).
 */
function desactivarColaborador($link, $id_colaborador) {
    $sql = "UPDATE colaboradores SET activo = 0 WHERE id_colaborador = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_colaborador);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

/**
 * Activa un colaborador en la base de datos (cambia su estado 'activo' a 1).
 */
function activarColaborador($link, $id_colaborador) {
    $sql = "UPDATE colaboradores SET activo = 1 WHERE id_colaborador = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_colaborador);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

/**
 * Elimina físicamente los archivos de foto y PDF de un colaborador del servidor.
 */
function eliminarArchivosColaborador($colaborador_data) {
    if (is_array($colaborador_data)) {
        $foto_url = $colaborador_data['ruta_foto_perfil'];
        $pdf_url = $colaborador_data['ruta_historial_academico_pdf'];

        if (!empty($foto_url)) {
            $base_foto_name = basename($foto_url); // 'original_foto_XYZ.jpg'
            // Eliminar la imagen original redimensionada
            if (file_exists(UPLOAD_DIR_FOTOS . $base_foto_name)) {
                @unlink(UPLOAD_DIR_FOTOS . $base_foto_name);
            }
            // Eliminar la miniatura
            if (file_exists(UPLOAD_DIR_FOTOS . 'thumb_' . $base_foto_name)) { // Esto espera 'thumb_original_foto_XYZ.jpg'
                @unlink(UPLOAD_DIR_FOTOS . 'thumb_' . $base_foto_name);
            }
        }
        if (!empty($pdf_url)) {
            $base_pdf_name = basename($pdf_url);
            if (file_exists(UPLOAD_DIR_PDFS . $base_pdf_name)) {
                @unlink(UPLOAD_DIR_PDFS . $base_pdf_name);
            }
        }
        return true;
    }
    return false;
}
?>