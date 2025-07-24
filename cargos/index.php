<?php
session_start();

require_once '../config.php';
require_once 'funciones.php';
require_once '../classes/Footer.php';

$current_page = 'cargos';
require_once '../includes/navbar.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!esAdministrador() && !esRRHH())) {
    header("location: ../index.php");
    exit;
}

// Obtener cargos. Por defecto, mostrar solo los cargos actuales de colaboradores activos.
// Si 'mostrar_inactivos' es 'true', mostrará los cargos actuales de colaboradores inactivos.
$mostrar_solo_inactivos_colaboradores = isset($_GET['mostrar_inactivos']) && $_GET['mostrar_inactivos'] == 'true';
$cargos = getCargos($link, $mostrar_solo_inactivos_colaboradores);

// Mostrar mensaje de éxito/error
$mensaje_confirmacion = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'creado') {
        $mensaje_confirmacion = '<div class="alert alert-success" role="alert">Cargo creado exitosamente. El cargo anterior del colaborador ha sido desactivado.</div>';
    } elseif ($_GET['msg'] == 'actualizado') {
        $mensaje_confirmacion = '<div class="alert alert-success" role="alert">Cargo actualizado exitosamente.</div>';
    } elseif ($_GET['msg'] == 'eliminado') {
        $mensaje_confirmacion = '<div class="alert alert-warning" role="alert">Cargo eliminado exitosamente.</div>';
    } elseif ($_GET['msg'] == 'error_eliminar') {
        $mensaje_confirmacion = '<div class="alert alert-danger" role="alert">Error al eliminar cargo: ' . htmlspecialchars($_GET['detail'] ?? 'Detalles no especificados.') . '</div>';
    } elseif ($_GET['msg'] == 'error') {
        $mensaje_confirmacion = '<div class="alert alert-danger" role="alert">Ocurrió un error en la operación.</div>';
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Cargos - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container mt-4 content-wrapper">
        <h2>Gestión de Cargos <?php echo $mostrar_solo_inactivos_colaboradores ? '(Colaboradores Inactivos)' : '(Colaboradores Activos)'; ?></h2>
        <p>Administra los cargos de los colaboradores.</p>

        <?php echo $mensaje_confirmacion; ?>

        <div class="d-flex justify-content-between mb-3">
            <?php if (!$mostrar_solo_inactivos_colaboradores): // Solo mostrar el botón "Asignar Nuevo Cargo" si no estamos en la vista de inactivos ?>
                <a href="crear.php" class="btn btn-success">Asignar Nuevo Cargo</a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($mostrar_solo_inactivos_colaboradores): // Si estamos mostrando inactivos, ofrecer volver a activos ?>
                <a href="index.php" class="btn btn-info">Mostrar Solo Colaboradores Activos</a>
            <?php else: // Si estamos mostrando activos, ofrecer ver inactivos ?>
                <a href="index.php?mostrar_inactivos=true" class="btn btn-warning">Mostrar Solo Colaboradores Inactivos</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($cargos)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Departamento</th>
                            <th>Ocupación</th>
                            <th>Sueldo</th>
                            <th>F. Contratación</th>
                            <th>Tipo Colaborador</th>
                            <th>Estado del Cargo</th>
                            <th>Integridad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cargos as $cargo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cargo['nombre_colaborador'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cargo['nombre_departamento'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cargo['nombre_ocupacion'] ?? ''); ?></td> <td>$<?php echo number_format($cargo['sueldo'] ?? 0, 2); ?></td>
                                <td><?php echo htmlspecialchars($cargo['fecha_contratacion'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cargo['tipo_colaborador'] ?? ''); ?></td>
                                <td>
                                    <?php
                                    // Si estamos en la vista de colaboradores inactivos, o si el cargo no está activo, mostrar "Histórico"
                                    if ($mostrar_solo_inactivos_colaboradores || ($cargo['activo_en_cargo'] ?? 0) == 0) {
                                        echo '<span class="badge bg-secondary">Histórico</span>';
                                    } else { // Si estamos en la vista de activos y el cargo está activo
                                        echo '<span class="badge bg-success">Actual</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        // Preparar los datos tal como fueron firmados para verificar
                                        $data_for_verification = [
                                            'id_colaborador' => $cargo['id_colaborador'],
                                            'id_departamento' => $cargo['id_departamento'],
                                            'id_ocupacion' => $cargo['id_ocupacion'],
                                            'sueldo' => (float)($cargo['sueldo'] ?? 0),
                                            'fecha_contratacion' => $cargo['fecha_contratacion'],
                                            'tipo_colaborador' => $cargo['tipo_colaborador'],
                                            'timestamp' => $cargo['fecha_firma']
                                        ];

                                        // Verificar si hay firma y si es válida
                                        if (!empty($cargo['firma_datos']) && verificarFirmaCargo($data_for_verification, $cargo['firma_datos'])) {
                                            echo '<span class="badge bg-success">Firmado OK</span>';
                                        } elseif (!empty($cargo['firma_datos'])) {
                                            echo '<span class="badge bg-danger">Firma INV.</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Sin Firma</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <a href="ver_historial.php?id_colaborador=<?php echo $cargo['id_colaborador']; ?>" class="btn btn-info btn-sm">Ver Historial</a>
                                    <?php if (($cargo['activo_en_cargo'] ?? 0) == 1): ?>
                                        <a href="editar.php?id_cargo=<?php echo $cargo['id_cargo']; ?>" class="btn btn-primary btn-sm">Editar Cargo</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No hay cargos registrados en el sistema para esta vista.</div>
        <?php endif; ?>
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