<?php
session_start();

require_once 'config.php';
require_once 'classes/Footer.php'; 
require_once 'usuarios/funciones.php'; 
require_once 'colaboradores/funciones.php'; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$current_page = 'home';

$current_user_is_admin = esAdministrador();
$current_user_is_rrhh = esRRHH();

// --- Funciones para obtener datos del Dashboard ---

/**
 * Obtiene el total de colaboradores activos (estatus_id = 4 'Trabajando').
 */
function getTotalColaboradoresActivosDashboard($link) {
    // Asegurarse de que 'estatus_id' exista en la tabla colaboradores
    $sql = "SELECT COUNT(*) AS total FROM colaboradores WHERE activo = 1 AND estatus_id = 4"; 
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

/**
 * Obtiene el total de colaboradores con un estatus_id específico.
 */
function getTotalColaboradoresByEstatusId($link, $estatus_id) {
    // Asegurarse de que 'estatus_id' exista en la tabla colaboradores
    $sql = "SELECT COUNT(*) AS total FROM colaboradores WHERE activo = 1 AND estatus_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $estatus_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row['total'];
    }
    return 0;
}

/**
 * Obtiene el total de nuevas contrataciones en un período (ej. último mes o año).
 */
function getTotalNuevasContratacionesDashboard($link, $periodo = 'month') {
    // Asegurarse de que 'fecha_ingreso' exista en la tabla colaboradores
    $sql = "SELECT COUNT(*) AS total FROM colaboradores WHERE activo = 1";
    if ($periodo == 'month') {
        $sql .= " AND fecha_ingreso >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    } elseif ($periodo == 'year') {
        $sql .= " AND fecha_ingreso >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
    }
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

/**
 * Obtiene los próximos cumpleaños del mes. (Opcional: para notificaciones)
 */
function getProximosCumpleanosMesDashboard($link) {
    // Asegurarse de que 'fecha_nacimiento' exista en la tabla colaboradores
    $cumpleanos = [];
    $sql = "SELECT primer_nombre, primer_apellido, fecha_nacimiento FROM colaboradores 
            WHERE activo = 1 AND MONTH(fecha_nacimiento) = MONTH(CURDATE())
            ORDER BY DAY(fecha_nacimiento) ASC LIMIT 5"; 
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cumpleanos[] = $row;
        }
        mysqli_free_result($result);
    }
    return $cumpleanos;
}


// --- Obtener los datos reales del dashboard ---
$total_colaboradores_activos = getTotalColaboradoresActivosDashboard($link);
$colaboradores_vacaciones = getTotalColaboradoresByEstatusId($link, 1); // ID 1 para Vacaciones
$colaboradores_licencia = getTotalColaboradoresByEstatusId($link, 2); // ID 2 para Licencia
$colaboradores_incapacitados = getTotalColaboradoresByEstatusId($link, 3); // ID 3 para Incapacitado
$nuevas_contrataciones_anio = getTotalNuevasContratacionesDashboard($link, 'year'); // Contrataciones del último año

// La sección de notificaciones y recordatorios se mantiene estática como pediste.

// Cierre de conexión a BD
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    </head>
<body>
    <?php require_once 'includes/navbar.php'; ?>

    <div class="container mt-4 content-wrapper">
        <div class="alert alert-success" role="alert">
            Bienvenido al sistema, **<?php echo htmlspecialchars($_SESSION["username"]); ?>**!
        </div>
        
        <?php if ($current_user_is_admin || $current_user_is_rrhh): // Sección de Resumen Rápido (solo para gestión) ?>
            <h3>Resumen Rápido del Personal</h3>
            <p>Una visión general de los datos clave de tu equipo.</p>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Colaboradores Activos</h5>
                            <p class="card-text fs-2 text-primary"><?php echo htmlspecialchars($total_colaboradores_activos); ?></p> 
                            <p class="card-text text-muted">Empleados que se encuentran actualmente trabajando.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">En Vacaciones / Licencia</h5>
                            <p class="card-text fs-2 text-warning"><?php echo htmlspecialchars($colaboradores_vacaciones + $colaboradores_licencia); ?></p> 
                            <p class="card-text text-muted">De vacaciones: <?php echo htmlspecialchars($colaboradores_vacaciones); ?>, con licencia: <?php echo htmlspecialchars($colaboradores_licencia); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Nuevas Contrataciones (Último Año)</h5>
                            <p class="card-text fs-2 text-success"><?php echo htmlspecialchars($nuevas_contrataciones_anio); ?></p> 
                            <p class="card-text text-muted">Empleados incorporados en los últimos 12 meses.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <h3>Accesos Rápidos</h3>
        <p>Realiza acciones comunes de forma instantánea.</p>
        <div class="d-grid gap-2 d-md-block mb-4">
            <?php if ($current_user_is_admin || $current_user_is_rrhh): // Botones para Admins/RRHH ?>
            <a href="colaboradores/crear.php" class="btn btn-success btn-lg me-md-2">Registrar Nuevo Colaborador</a>
            <a href="cargos/crear.php" class="btn btn-info btn-lg">Asignar Nuevo Cargo</a>
            <?php endif; ?>
            <?php if ($current_user_is_admin || $current_user_is_rrhh): // Reportes y Estadísticas para Admins/RRHH ?>
            <a href="reportes/index.php" class="btn btn-primary btn-lg me-md-2">Ver Reportes</a>
            <a href="estadisticas/estadisticas.php" class="btn btn-secondary btn-lg">Ver Estadísticas</a> <?php endif; ?>
            <?php if ($current_user_is_admin): // Gestión de Usuarios solo para Admin ?>
            <a href="usuarios/index.php" class="btn btn-dark btn-lg">Gestión de Usuarios</a>
            <?php endif; ?>
            <?php // Aquí irían botones para el rol "Empleado" si existieran (ej. "Mi Perfil", "Solicitar Vacaciones") ?>
        </div>

        <?php // La sección de Notificaciones y Recordatorios se mantiene estática como pediste. ?>
        <?php if ($current_user_is_admin || $current_user_is_rrhh): // Muestra esta sección solo a roles de gestión si lo deseas ?>
            <h3>Notificaciones y Recordatorios Recientes</h3>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Solicitud de vacaciones de **Ana R.** pendiente.
                    <span class="badge bg-warning rounded-pill">Nueva</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    **Pedro S.** cumple 5 años en la empresa este mes.
                    <span class="badge bg-info rounded-pill">Recordatorio</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    **Nuevo colaborador: Laura M.** se unió al equipo de Marketing.
                    <span class="badge bg-success rounded-pill">Info</span>
                </li>
            </ul>
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