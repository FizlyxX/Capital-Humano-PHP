<?php
session_start();

require_once '../config.php';
require_once 'funciones.php';
require_once '../classes/Footer.php';
require_once '../classes/Sanitizar.php'; // Incluye la nueva clase

$current_page = 'colaboradores';
require_once '../includes/navbar.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!esAdministrador() && !esRRHH())) {
    header("location: ../index.php");
    exit;
}

// Inicializar variables del formulario y errores
$primer_nombre = $segundo_nombre = $primer_apellido = $segundo_apellido = "";
$sexo = $identificacion = $fecha_nacimiento = $correo_personal = "";
$telefono = $celular = $direccion = "";
$fecha_ingreso = "";
$estatus = "";
$primer_nombre_err = $primer_apellido_err = $sexo_err = $identificacion_err = $fecha_nacimiento_err = "";
$correo_personal_err = $telefono_err = $celular_err = $direccion_err = "";
$fecha_ingreso_err = "";
$estatus_err = "";
$foto_perfil_err = $historial_academico_pdf_err = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanear datos del formulario usando la nueva clase
    $primer_nombre = Sanitizar::sanearString($_POST['primer_nombre']);
    $segundo_nombre = Sanitizar::sanearString($_POST['segundo_nombre']);
    $primer_apellido = Sanitizar::sanearString($_POST['primer_apellido']);
    $segundo_apellido = Sanitizar::sanearString($_POST['segundo_apellido']);
    $sexo = Sanitizar::sanearString($_POST['sexo']);
    $identificacion = Sanitizar::sanearString($_POST['identificacion']);
    $fecha_nacimiento = Sanitizar::sanearString($_POST['fecha_nacimiento']);
    $correo_personal = Sanitizar::sanearEmail($_POST['correo_personal']);
    $telefono = Sanitizar::sanearString($_POST['telefono']);
    $celular = Sanitizar::sanearString($_POST['celular']);
    $direccion = Sanitizar::sanearString($_POST['direccion']);
    $fecha_ingreso = Sanitizar::sanearString($_POST['fecha_ingreso']);
    $estatus = Sanitizar::sanearString($_POST['estatus']);

    // 2. Validar datos usando la nueva clase
    if (!Sanitizar::validarCampoNoVacio($primer_nombre)) { $primer_nombre_err = "Ingrese el primer nombre."; }
    if (!Sanitizar::validarCampoNoVacio($primer_apellido)) { $primer_apellido_err = "Ingrese el primer apellido."; }
    if (!Sanitizar::validarCampoNoVacio($sexo)) { $sexo_err = "Seleccione el sexo."; }
    if (!Sanitizar::validarCampoNoVacio($identificacion)) { $identificacion_err = "Ingrese la identificación."; }
    if (!Sanitizar::validarCampoNoVacio($fecha_nacimiento)) { $fecha_nacimiento_err = "Ingrese la fecha de nacimiento."; }
    if (!Sanitizar::validarCampoNoVacio($fecha_ingreso)) { $fecha_ingreso_err = "Ingrese la fecha de ingreso."; }
    if (!Sanitizar::validarCampoNoVacio($estatus)) { $estatus_err = "Seleccione el estatus."; }

    // Validar unicidad de identificación
    if (empty($identificacion_err)) {
        $sql_check_id = "SELECT id_colaborador FROM colaboradores WHERE identificacion = ?";
        if ($stmt_check_id = mysqli_prepare($link, $sql_check_id)) {
            mysqli_stmt_bind_param($stmt_check_id, "s", $param_identificacion);
            $param_identificacion = $identificacion;
            if (mysqli_stmt_execute($stmt_check_id)) {
                mysqli_stmt_store_result($stmt_check_id);
                if (mysqli_stmt_num_rows($stmt_check_id) == 1) {
                    $identificacion_err = "Esta identificación (cédula) ya está registrada.";
                }
            }
            mysqli_stmt_close($stmt_check_id);
        }
    }
    
    // Si no hay errores de validación de texto, proceder con archivos y BD
    if (empty($primer_nombre_err) && empty($primer_apellido_err) && empty($sexo_err) && empty($identificacion_err) && empty($fecha_nacimiento_err) && empty($fecha_ingreso_err) && empty($estatus_err)) {
        
        $colaborador_data = [
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
            'direccion' => $direccion,
            'fecha_ingreso' => $fecha_ingreso,
            'estatus_id' => $estatus
        ];

        $resultado_creacion = crearColaborador($link, $colaborador_data, 'foto_perfil', 'historial_academico_pdf');

        if (isset($resultado_creacion['success'])) {
            header("location: index.php?msg=creado");
            exit();
        } else {
            $error_message_from_func = isset($resultado_creacion['error']) ? $resultado_creacion['error'] : 'Error desconocido al crear.';
            
            if (strpos($error_message_from_func, 'identificación') !== false) {
                 $identificacion_err = $error_message_from_func;
            } else {
                header("location: index.php?msg=error_upload&error_upload=" . urlencode($error_message_from_func));
                exit();
            }
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Colaborador - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/app_style.css">
    <style>
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
        <h2>Registrar Nuevo Colaborador</h2>
        <p>Complete el formulario para añadir un nuevo colaborador al sistema.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
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
                <div class="col-md-4">
                    <div class="mb-3 <?php echo (!empty($fecha_ingreso_err)) ? 'has-error' : ''; ?>">
                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso en la Organización:</label>
                        <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" value="<?php echo htmlspecialchars($fecha_ingreso); ?>" required>
                        <span class="invalid-feedback text-danger"><?php echo $fecha_ingreso_err; ?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3 <?php echo (!empty($estatus_err)) ? 'has-error' : ''; ?>">
                        <label for="estatus" class="form-label">Estatus:</label>
                        <select name="estatus" id="estatus" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option value="1" <?php echo ($estatus == 1) ? 'selected' : ''; ?>>Vacaciones</option>
                            <option value="2" <?php echo ($estatus == 2) ? 'selected' : ''; ?>>Licencia</option>
                            <option value="3" <?php echo ($estatus == 3) ? 'selected' : ''; ?>>Incapacitado</option>
                            <option value="4" <?php echo ($estatus == 4) ? 'selected' : ''; ?>>Trabajando</option>
                        </select>
                        <span class="invalid-feedback text-danger"><?php echo $estatus_err; ?></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 <?php echo (!empty($foto_perfil_err)) ? 'has-error' : ''; ?>">
                        <label for="foto_perfil" class="form-label">Foto de Perfil:</label>
                        <input type="file" name="foto_perfil" id="foto_perfil" class="form-control" accept="image/*">
                        <span class="invalid-feedback text-danger"><?php echo $foto_perfil_err; ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 <?php echo (!empty($historial_academico_pdf_err)) ? 'has-error' : ''; ?>">
                        <label for="historial_academico_pdf" class="form-label">Historial Académico (PDF Opcional):</label>
                        <input type="file" name="historial_academico_pdf" id="historial_academico_pdf" class="form-control" accept=".pdf">
                        <span class="invalid-feedback text-danger"><?php echo $historial_academico_pdf_err; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-3">
                <input type="submit" class="btn btn-primary" value="Registrar Colaborador">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    <?php
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