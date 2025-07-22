<?php
session_start();

require_once '../config.php';
require_once 'funciones.php'; 
require_once '../classes/Footer.php'; 

$current_page = 'colaboradores';
require_once '../includes/navbar.php'; 

// Verificar si el usuario ha iniciado sesión y tiene permisos (ej. RRHH o Administrador)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!esAdministrador() && !esRRHH())) {
    header("location: ../index.php"); // Redirigir al login si no tiene permisos
    exit;
}

// Inicializar variables del formulario y errores
$id_colaborador = $primer_nombre = $segundo_nombre = $primer_apellido = $segundo_apellido = "";
$sexo = $identificacion = $fecha_nacimiento = $correo_personal = "";
$telefono = $celular = $direccion = "";
$primer_nombre_err = $primer_apellido_err = $sexo_err = $identificacion_err = $fecha_nacimiento_err = "";
$correo_personal_err = $telefono_err = $celular_err = $direccion_err = "";
$foto_perfil_err = $historial_academico_pdf_err = "";
$current_foto_path = ''; // Para la URL de la miniatura a mostrar en el HTML
$current_pdf_path = '';  // Para la URL del PDF a mostrar en el HTML

// Procesar el formulario cuando se envía (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_colaborador = $_POST['id_colaborador'];

    // 1. Recopilar y sanear datos de texto
    $primer_nombre = trim($_POST['primer_nombre']);
    $segundo_nombre = trim($_POST['segundo_nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $sexo = trim($_POST['sexo']);
    $identificacion = trim($_POST['identificacion']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $correo_personal = trim($_POST['correo_personal']);
    $telefono = trim($_POST['telefono']);
    $celular = trim($_POST['celular']);
    $direccion = trim($_POST['direccion']);

    // 2. Validar datos de texto
    if (empty($primer_nombre)) { $primer_nombre_err = "Ingrese el primer nombre."; }
    if (empty($primer_apellido)) { $primer_apellido_err = "Ingrese el primer apellido."; }
    if (empty($sexo)) { $sexo_err = "Seleccione el sexo."; }
    if (empty($identificacion)) { $identificacion_err = "Ingrese la identificación."; }
    // Validar unicidad de identificación, excluyendo al colaborador actual
    $sql_check_id = "SELECT id_colaborador FROM colaboradores WHERE identificacion = ? AND id_colaborador != ?";
    if ($stmt_check_id = mysqli_prepare($link, $sql_check_id)) {
        mysqli_stmt_bind_param($stmt_check_id, "si", $param_identificacion, $param_id_colaborador);
        $param_identificacion = $identificacion;
        $param_id_colaborador = $id_colaborador;
        if (mysqli_stmt_execute($stmt_check_id)) {
            mysqli_stmt_store_result($stmt_check_id);
            if (mysqli_stmt_num_rows($stmt_check_id) == 1) {
                $identificacion_err = "Esta identificación (cédula) ya está registrada para otro colaborador.";
            }
        }
        mysqli_stmt_close($stmt_check_id);
    }
    if (empty($fecha_nacimiento)) { $fecha_nacimiento_err = "Ingrese la fecha de nacimiento."; }

    // Si no hay errores de validación de texto, proceder con archivos y BD
    if (empty($primer_nombre_err) && empty($primer_apellido_err) && empty($sexo_err) && empty($identificacion_err) && empty($fecha_nacimiento_err)) {
        
        $colaborador_data_for_update = [ 
            'primer_nombre' => $primer_nombre,
            'segundo_nombre' => $segundo_nombre,
            'primer_apellido' => $primer_apellido,
            'segundo_apellido' => $segundo_apellido,
            'sexo' => $sexo,
            'identificacion' => $identificacion,
            'fecha_nacimiento' => $fecha_nacimiento,
            'correo_personal' => $correo_personal,
            'telefono' => $telefono,
            'celular' => $celular,
            'direccion' => $direccion
        ];

        // Llamar a la función para actualizar el colaborador, que también maneja las subidas de archivos
        // La función actualizarColaborador() internamente obtiene las rutas antiguas de la BD.
        $resultado_actualizacion = actualizarColaborador($link, $id_colaborador, $colaborador_data_for_update, 'foto_perfil', 'historial_academico_pdf');

        if (isset($resultado_actualizacion['success'])) {
            header("location: index.php?msg=actualizado");
            exit();
        } else {
            // Manejar errores específicos devueltos por actualizarColaborador
            $error_message_from_func = isset($resultado_actualizacion['error']) ? $resultado_actualizacion['error'] : 'Error desconocido al actualizar.';
            
            if (strpos($error_message_from_func, 'identificación') !== false) {
                 $identificacion_err = $error_message_from_func;
                 // No redirigir, se mostrará el error en el formulario actual
            } else if (strpos($error_message_from_func, 'foto') !== false || strpos($error_message_from_func, 'PDF') !== false) {
                $foto_perfil_err = $error_message_from_func; // Se mostrará este error
            } else {
                // Para otros errores de DB que no son de archivos/identificación
                echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($error_message_from_func) . '</div>';
            }
            // Si hay errores que se muestran en la página (no redirección), necesitas cerrar $link aquí
            mysqli_close($link); 
        }
    } else { // Si hay errores de validación de texto de formulario
        // Si no se redirige, y hay errores de texto, la conexión debe cerrarse.
        mysqli_close($link);
    }

} else { // Si no es POST, cargar datos del colaborador para el formulario (GET request)
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id_colaborador = trim($_GET["id"]);
        $colaborador_data = getColaboradorById($link, $id_colaborador); // Este es el $colaborador_data original del GET

        if ($colaborador_data) {
            // Asignación de variables, usando coalescencia nula para campos que pueden ser NULL en BD
            $primer_nombre = $colaborador_data['primer_nombre'] ?? '';
            $segundo_nombre = $colaborador_data['segundo_nombre'] ?? '';
            $primer_apellido = $colaborador_data['primer_apellido'] ?? '';
            $segundo_apellido = $colaborador_data['segundo_apellido'] ?? '';
            $sexo = $colaborador_data['sexo'] ?? '';
            $identificacion = $colaborador_data['identificacion'] ?? '';
            $fecha_nacimiento = $colaborador_data['fecha_nacimiento'] ?? '';
            $correo_personal = $colaborador_data['correo_personal'] ?? '';
            $telefono = $colaborador_data['telefono'] ?? '';
            $celular = $colaborador_data['celular'] ?? '';
            $direccion = $colaborador_data['direccion'] ?? '';
            
            // Rutas de archivos obtenidas directamente de la base de datos (con coalesce para NULL)
            $current_foto_path_from_db = $colaborador_data['ruta_foto_perfil'] ?? ''; 
            $current_pdf_path_from_db = $colaborador_data['ruta_historial_academico_pdf'] ?? '';

            // Construir la URL de la miniatura para mostrar en el HTML
            if (!empty($current_foto_path_from_db)) {
                 $base_foto_name = basename($current_foto_path_from_db);
                 // La miniatura debe tener el formato 'thumb_original_foto_XXXX.jpeg'
                 $current_foto_path = URL_BASE_FOTOS . 'thumb_' . $base_foto_name;
            } else {
                $current_foto_path = ''; // No hay foto, la ruta está vacía
            }
            // La ruta del PDF es directa, solo asegúrate de que esté vacía si es NULL
            $current_pdf_path = $current_pdf_path_from_db;
            
        } else {
            // Colaborador no encontrado, redirigir
            mysqli_close($link); // Cierra conexión antes de redirigir
            header("location: index.php?msg=error_notfound");
            exit();
        }
    } else {
        // ID de colaborador no proporcionado en la URL, redirigir
        mysqli_close($link); // Cierra conexión antes de redirigir
        header("location: index.php?msg=error");
        exit();
    }
    mysqli_close($link); // Cierra la conexión si la página se carga por GET y todo fue bien.
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Colaborador - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/app_style.css"> <style>
        .current-file-preview {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
            display: block;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
        }
        .current-pdf-link {
            margin-top: 10px;
            display: inline-block;
        }
         .footer { 
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 0.9rem;
            width: 100%;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="container mt-4 content-wrapper">
        <h2>Editar Colaborador</h2>
        <p>Modifique los datos del colaborador.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_colaborador" value="<?php echo htmlspecialchars($id_colaborador); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 <?php echo (!empty($primer_nombre_err)) ? 'has-error' : ''; ?>">
                        <label for="primer_nombre" class="form-label">Primer Nombre:</label>
                        <input type="text" name="primer_nombre" id="primer_nombre" class="form-control" value="<?php echo htmlspecialchars($primer_nombre); ?>" required>
                        <span class="invalid-feedback text-danger"><?php echo $primer_nombre_err; ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="segundo_nombre" class="form-label">Segundo Nombre:</label>
                        <input type="text" name="segundo_nombre" id="segundo_nombre" class="form-control" value="<?php echo htmlspecialchars($segundo_nombre); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 <?php echo (!empty($primer_apellido_err)) ? 'has-error' : ''; ?>">
                        <label for="primer_apellido" class="form-label">Primer Apellido:</label>
                        <input type="text" name="primer_apellido" id="primer_apellido" class="form-control" value="<?php echo htmlspecialchars($primer_apellido); ?>" required>
                        <span class="invalid-feedback text-danger"><?php echo $primer_apellido_err; ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="segundo_apellido" class="form-label">Segundo Apellido:</label>
                        <input type="text" name="segundo_apellido" id="segundo_apellido" class="form-control" value="<?php echo htmlspecialchars($segundo_apellido); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3 <?php echo (!empty($sexo_err)) ? 'has-error' : ''; ?>">
                        <label for="sexo" class="form-label">Sexo:</label>
                        <select name="sexo" id="sexo" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option value="M" <?php echo ($sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="F" <?php echo ($sexo == 'F') ? 'selected' : ''; ?>>Femenino</option>
                            <option value="Otro" <?php echo ($sexo == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                        </select>
                        <span class="invalid-feedback text-danger"><?php echo $sexo_err; ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3 <?php echo (!empty($identificacion_err)) ? 'has-error' : ''; ?>">
                        <label for="identificacion" class="form-label">Identificación (Cédula):</label>
                        <input type="text" name="identificacion" id="identificacion" class="form-control" value="<?php echo htmlspecialchars($identificacion); ?>" required>
                        <span class="invalid-feedback text-danger"><?php echo $identificacion_err; ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3 <?php echo (!empty($fecha_nacimiento_err)) ? 'has-error' : ''; ?>">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($fecha_nacimiento); ?>" required>
                        <span class="invalid-feedback text-danger"><?php echo $fecha_nacimiento_err; ?></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="correo_personal" class="form-label">Correo Personal:</label>
                        <input type="email" name="correo_personal" id="correo_personal" class="form-control" value="<?php echo htmlspecialchars($correo_personal); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono:</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" value="<?php echo htmlspecialchars($telefono); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular:</label>
                        <input type="text" name="celular" id="celular" class="form-control" value="<?php echo htmlspecialchars($celular); ?>">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección:</label>
                <textarea name="direccion" id="direccion" class="form-control" rows="3"><?php echo htmlspecialchars($direccion); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 <?php echo (!empty($foto_perfil_err)) ? 'has-error' : ''; ?>">
                        <label for="foto_perfil" class="form-label">Foto de Perfil (dejar vacío para no cambiar):</label>
                        <input type="file" name="foto_perfil" id="foto_perfil" class="form-control" accept="image/*">
                        <?php if (!empty($current_foto_path)): ?>
                            <img src="<?php echo htmlspecialchars($current_foto_path); ?>" alt="Foto actual" class="current-file-preview">
                        <?php endif; ?>
                        <span class="invalid-feedback text-danger"><?php echo $foto_perfil_err; ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 <?php echo (!empty($historial_academico_pdf_err)) ? 'has-error' : ''; ?>">
                        <label for="historial_academico_pdf" class="form-label">Historial Académico (PDF Opcional - dejar vacío para no cambiar):</label>
                        <input type="file" name="historial_academico_pdf" id="historial_academico_pdf" class="form-control" accept=".pdf">
                        <?php if (!empty($current_pdf_path)): ?>
                            <a href="<?php echo htmlspecialchars($current_pdf_path); ?>" target="_blank" class="current-pdf-link btn btn-sm btn-secondary">Ver PDF Actual</a>
                        <?php endif; ?>
                        <span class="invalid-feedback text-danger"><?php echo $historial_academico_pdf_err; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-3">
                <input type="submit" class="btn btn-primary" value="Actualizar Colaborador">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div> <?php
    if (class_exists('Footer')) {
        $footer = new Footer();
        $footer->render();
    } else {
        echo '<footer class="footer">';
        echo '  <div class="container">';
        echo '      <p>&copy; ' . date("Y") . ' Proyecto PHP Capital Humano. Todos los derechos reservados.</p>';
        echo '  </div>';
        echo '</footer>';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>