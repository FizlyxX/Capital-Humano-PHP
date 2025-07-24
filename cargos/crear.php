<?php
session_start();

require_once '../config.php';
require_once 'funciones.php';
require_once '../colaboradores/funciones.php';
require_once '../classes/Footer.php';

$current_page = 'cargos';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!esAdministrador() && !esRRHH())) {
    header("location: ../index.php");
    exit;
}

// Inicializar variables del formulario y errores
$id_colaborador = $id_departamento = $id_ocupacion = $sueldo = "";
$fecha_contratacion = $tipo_colaborador = "";
$colaborador_err = $departamento_err = $ocupacion_err = $sueldo_err = "";
$fecha_contratacion_err = $tipo_colaborador_err = "";

// Obtener listas de colaboradores, departamentos y ocupaciones para los selects
$colaboradores_list = getColaboradores($link, true); // Traer todos los colaboradores (activos e inactivos)
$departamentos_list = getDepartamentos($link);
$ocupaciones_list = getOcupaciones($link);

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recopilar y sanear datos
    $id_colaborador = trim($_POST['id_colaborador']);
    $id_departamento = trim($_POST['id_departamento']);
    $id_ocupacion = trim($_POST['id_ocupacion']);
    $sueldo = trim($_POST['sueldo']);
    $fecha_contratacion = trim($_POST['fecha_contratacion']);
    $tipo_colaborador = trim($_POST['tipo_colaborador']);

    // 2. Validar datos
    if (empty($id_colaborador) || !is_numeric($id_colaborador)) { $colaborador_err = "Seleccione un colaborador válido."; }
    if (empty($id_departamento) || !is_numeric($id_departamento)) { $departamento_err = "Seleccione un departamento."; }
    if (empty($id_ocupacion) || !is_numeric($id_ocupacion)) { $ocupacion_err = "Seleccione una ocupación."; }
    if (empty($sueldo) || !is_numeric($sueldo) || $sueldo <= 0) { $sueldo_err = "Ingrese un sueldo válido."; }
    if (empty($fecha_contratacion)) { $fecha_contratacion_err = "Ingrese la fecha de contratación."; }
    if (empty($tipo_colaborador)) { $tipo_colaborador_err = "Seleccione el tipo de colaborador."; }
    
    // Si no hay errores de validación, intentar crear el cargo
    if (empty($colaborador_err) && empty($departamento_err) && empty($ocupacion_err) && empty($sueldo_err) && empty($fecha_contratacion_err) && empty($tipo_colaborador_err)) {
        
        $resultado_creacion = crearCargo($link, $id_colaborador, $id_departamento, $id_ocupacion, $sueldo, $fecha_contratacion, $tipo_colaborador);

        if (isset($resultado_creacion['success'])) {
            header("location: index.php?msg=creado");
            exit();
        } else {
            $error_message_from_func = isset($resultado_creacion['error']) ? $resultado_creacion['error'] : 'Error desconocido al crear el cargo.';
            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($error_message_from_func) . '</div>';
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
    <title>Asignar Cargo - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css"> 
    </head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="container mt-4 content-wrapper">
        <h2>Asignar Nuevo Cargo</h2>
        <p>Complete el formulario para asignar un nuevo cargo a un colaborador.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3 <?php echo (!empty($colaborador_err)) ? 'has-error' : ''; ?>">
                <label for="id_colaborador" class="form-label">Colaborador:</label>
                <select name="id_colaborador" id="id_colaborador" class="form-select" required>
                    <option value="">Seleccione un colaborador</option>
                    <?php foreach ($colaboradores_list as $col): ?>
                        <option value="<?php echo htmlspecialchars($col['id_colaborador']); ?>" <?php echo ($id_colaborador == $col['id_colaborador']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($col['primer_nombre'] . ' ' . ($col['segundo_nombre'] ?? '') . ' ' . $col['primer_apellido'] . ' ' . ($col['segundo_apellido'] ?? '') . ' (' . ($col['activo'] ? 'Activo' : 'Inactivo') . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="invalid-feedback text-danger"><?php echo $colaborador_err; ?></span>
            </div>

            <div class="mb-3 <?php echo (!empty($departamento_err)) ? 'has-error' : ''; ?>">
                <label for="id_departamento" class="form-label">Departamento:</label>
                <select name="id_departamento" id="id_departamento" class="form-select" required>
                    <option value="">Seleccione un departamento</option>
                    <?php foreach ($departamentos_list as $dep): ?>
                        <option value="<?php echo htmlspecialchars($dep['id_departamento']); ?>" <?php echo ($id_departamento == $dep['id_departamento']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep['nombre_departamento']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="invalid-feedback text-danger"><?php echo $departamento_err; ?></span>
            </div>

            <div class="mb-3 <?php echo (!empty($ocupacion_err)) ? 'has-error' : ''; ?>">
                <label for="id_ocupacion" class="form-label">Ocupación:</label>
                <select name="id_ocupacion" id="id_ocupacion" class="form-select" required>
                    <option value="">Seleccione una ocupación</option>
                    <?php foreach ($ocupaciones_list as $ocup): ?>
                        <option value="<?php echo htmlspecialchars($ocup['id_ocupacion']); ?>" <?php echo ($id_ocupacion == $ocup['id_ocupacion']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ocup['nombre_ocupacion']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="invalid-feedback text-danger"><?php echo $ocupacion_err; ?></span>
            </div>

            <div class="mb-3 <?php echo (!empty($sueldo_err)) ? 'has-error' : ''; ?>">
                <label for="sueldo" class="form-label">Sueldo:</label>
                <input type="number" step="0.01" name="sueldo" id="sueldo" class="form-control" value="<?php echo htmlspecialchars($sueldo); ?>" required>
                <span class="invalid-feedback text-danger"><?php echo $sueldo_err; ?></span>
            </div>

            <div class="mb-3 <?php echo (!empty($fecha_contratacion_err)) ? 'has-error' : ''; ?>">
                <label for="fecha_contratacion" class="form-label">Fecha de Contratación (o Inicio de Cargo):</label>
                <input type="date" name="fecha_contratacion" id="fecha_contratacion" class="form-control" value="<?php echo htmlspecialchars($fecha_contratacion); ?>" required>
                <span class="invalid-feedback text-danger"><?php echo $fecha_contratacion_err; ?></span>
            </div>

            <div class="mb-3 <?php echo (!empty($tipo_colaborador_err)) ? 'has-error' : ''; ?>">
                <label for="tipo_colaborador" class="form-label">Tipo de Colaborador:</label>
                <select name="tipo_colaborador" id="tipo_colaborador" class="form-select" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="Permanente" <?php echo ($tipo_colaborador == 'Permanente') ? 'selected' : ''; ?>>Permanente</option>
                    <option value="Eventual" <?php echo ($tipo_colaborador == 'Eventual') ? 'selected' : ''; ?>>Eventual</option>
                    <option value="Interino" <?php echo ($tipo_colaborador == 'Interino') ? 'selected' : ''; ?>>Interino</option>
                </select>
                <span class="invalid-feedback text-danger"><?php echo $tipo_colaborador_err; ?></span>
            </div>
            
            <div class="form-group mt-3">
                <input type="submit" class="btn btn-primary" value="Asignar Cargo">
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