<?php
session_start();

require_once '../config.php';
require_once 'funciones.php';
require_once '../colaboradores/funciones.php';
require_once '../classes/Footer.php';

$current_page = 'cargos'; 
require_once '../includes/navbar.php'; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!esAdministrador() && !esRRHH())) {
    header("location: ../index.php");
    exit;
}

$id_colaborador = null;
$nombre_colaborador_display = "";
$historial_cargos = [];

if (isset($_GET["id_colaborador"]) && !empty(trim($_GET["id_colaborador"]))) {
    $id_colaborador = trim($_GET["id_colaborador"]);

    // Obtener la información del colaborador para mostrar su nombre
    $colaborador_info = getColaboradorById($link, $id_colaborador);
    if ($colaborador_info) {
        $nombre_colaborador_display = htmlspecialchars($colaborador_info['primer_nombre'] . ' ' . ($colaborador_info['segundo_nombre'] ?? '') . ' ' . $colaborador_info['primer_apellido'] . ' ' . ($colaborador_info['segundo_apellido'] ?? ''));
    } else {
        $nombre_colaborador_display = "Colaborador Desconocido";
        // Si el colaborador no existe, deberíamos redirigir o mostrar un error
        mysqli_close($link);
        header("location: index.php?msg=error_colaborador_notfound");
        exit();
    }

    // Obtener el historial de cargos de este colaborador
    $historial_cargos = getHistorialCargosColaborador($link, $id_colaborador);

} else {
    // Si no se proporcionó un ID de colaborador, redirigir
    mysqli_close($link);
    header("location: index.php?msg=error"); 
    exit();
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cargos - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .content-wrapper { flex: 1; padding-bottom: 50px; }
        .table-responsive { margin-top: 20px; }
        /* Badges para el estado del cargo y firmas */
        .badge.bg-success, .badge.bg-secondary, .badge.bg-danger {
            padding: .35em .65em;
            border-radius: .25rem;
            font-size: .75em;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container mt-4 content-wrapper">
        <h2>Historial de Cargos para <?php echo $nombre_colaborador_display; ?></h2>
        <p>Detalle de todos los cargos ocupados por este colaborador.</p>

        <a href="index.php" class="btn btn-secondary mb-3">Volver a Cargos Activos</a>
        <a href="crear.php?id_colaborador=<?php echo htmlspecialchars($id_colaborador); ?>" class="btn btn-success mb-3">Asignar Nuevo Cargo a <?php echo htmlspecialchars($colaborador_info['primer_nombre'] ?? ''); ?></a>

        <?php if (!empty($historial_cargos)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Ocupación</th>
                            <th>Sueldo</th>
                            <th>F. Contratación</th>
                            <th>Tipo Colaborador</th>
                            <th>Estado del Cargo</th>
                            <th>Integridad</th> <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial_cargos as $cargo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cargo['nombre_departamento'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cargo['nombre_ocupacion'] ?? ''); ?></td>
                                <td>$<?php echo number_format($cargo['sueldo'] ?? 0, 2); ?></td>
                                <td><?php echo htmlspecialchars($cargo['fecha_contratacion'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cargo['tipo_colaborador'] ?? ''); ?></td>
                                <td>
                                    <?php if (($cargo['activo_en_cargo'] ?? 0) == 1): ?>
                                        <span class="badge bg-success">Actual</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Histórico</span>
                                    <?php endif; ?>
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
                                    <a href="editar.php?id_cargo=<?php echo $cargo['id_cargo']; ?>" class="btn btn-primary btn-sm">Editar</a>
                                    <a href="eliminar.php?id_cargo=<?php echo $cargo['id_cargo']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este cargo? ¡Esto eliminará el registro de su historial! (No se puede eliminar un cargo activo)');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No hay historial de cargos para este colaborador.</div>
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