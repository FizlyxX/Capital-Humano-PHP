<?php
global $link;
session_start();

require_once '../config.php';
require_once 'funciones.php'; // Incluye las funciones del módulo de colaboradores
require_once '../classes/Footer.php';
require_once '../includes/navbar.php';
require_once 'Vacaciones.php';

// Verificar si el usuario ha iniciado sesión y tiene permisos de Administrador o RRHH
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (!esAdministrador() && !esRRHH())) {
    header("location: ../index.php"); // Redirigir al login si no tiene permisos
    exit;
}

// Definir la página actual para que el navbar la resalte
$current_page = 'colaboradores';

// Obtener colaboradores. Se puede pasar true a getColaboradores() para mostrar inactivos.
// El $_GET['mostrar_inactivos'] se usa para un toggle en la interfaz.
$mostrar_inactivos_param = isset($_GET['mostrar_inactivos']) && $_GET['mostrar_inactivos'] == 'true';
$colaboradores = getColaboradores($link, $mostrar_inactivos_param);

// Mostrar mensaje de éxito/error después de una operación
$mensaje_confirmacion = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'creado') {
        $mensaje_confirmacion = '<div class="alert alert-success" role="alert">Colaborador creado exitosamente.</div>';
    } elseif ($_GET['msg'] == 'actualizado') {
        $mensaje_confirmacion = '<div class="alert alert-success" role="alert">Colaborador actualizado exitosamente.</div>';
    } elseif ($_GET['msg'] == 'desactivado') {
        $mensaje_confirmacion = '<div class="alert alert-warning" role="alert">Colaborador desactivado exitosamente.</div>';
    } elseif ($_GET['msg'] == 'activado') {
        $mensaje_confirmacion = '<div class="alert alert-success" role="alert">Colaborador activado exitosamente.</div>';
    } elseif ($_GET['msg'] == 'error_identificacion') {
        $mensaje_confirmacion = '<div class="alert alert-danger" role="alert">Error: La identificación (cédula) ingresada ya existe.</div>';
    } elseif (isset($_GET['error_upload'])) {
        $mensaje_confirmacion = '<div class="alert alert-danger" role="alert">Error al subir archivo: ' . htmlspecialchars($_GET['error_upload']) . '</div>';
    } elseif ($_GET['msg'] == 'error') {
        $mensaje_confirmacion = '<div class="alert alert-danger" role="alert">Ocurrió un error en la operación.</div>';
    }
}

?>

    <!DOCTYPE html>
    <html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Vacaciones - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <!-- Bootstrap Icons (para los íconos en los campos) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .content-wrapper { flex: 1; padding-bottom: 50px; }
        .table-responsive { margin-top: 20px; }
        .photo-thumbnail {
            width: 50px; /* Tamaño de la miniatura en la tabla */
            height: 50px;
            object-fit: cover;
            border-radius: 50%; /* Para hacerla circular */
            border: 1px solid #ddd;
        }
        /* Badges para el estado Activo/Inactivo */
        .status-badge {
            padding: .3em .6em;
            border-radius: .25rem;
            font-size: 0.85em;
            font-weight: bold;
        }
        .status-badge.active {
            background-color: #28a745;
            color: white;
        }
        .status-badge.inactive {
            background-color: #dc3545;
            color: white;
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
<div class="container mt-4 content-wrapper">
    <h2>Gestión de Vacaciones</h2>
    <p>Administra las vacaciones de los colaboradores de la empresa.</p>

    <?php echo $mensaje_confirmacion; ?>


    <?php if (!empty($colaboradores)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Identificación</th>
                    <th>Sexo</th>
                    <th>F. Nacimiento</th>
                    <th>Estado</th> <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($colaboradores as $colaborador): ?>
                    <tr>
                        <td>
                            <?php if (!empty($colaborador['ruta_foto_perfil'])): ?>
                                <?php
                                // Generar la ruta de la miniatura (asumiendo que se guardó con prefijo 'thumb_')
                                $base_foto_name = basename($colaborador['ruta_foto_perfil']);
                                $thumbnail_url = URL_BASE_FOTOS . 'thumb_' . $base_foto_name;
                                $original_url = URL_BASE_FOTOS . 'original_' . $base_foto_name;
                                ?>
                                <a href="<?php echo htmlspecialchars($original_url); ?>" target="_blank" title="Ver foto original">
                                    <img src="<?php echo htmlspecialchars($thumbnail_url); ?>" alt="Foto de Perfil" class="photo-thumbnail">
                                </a>
                            <?php else: ?>
                                <img src="https://via.placeholder.com/50?text=No+Foto" alt="Sin Foto" class="photo-thumbnail">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['segundo_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($colaborador['primer_apellido'] . ' ' . $colaborador['segundo_apellido']); ?></td>
                        <td><?php echo htmlspecialchars($colaborador['identificacion']); ?></td>
                        <td><?php echo htmlspecialchars($colaborador['sexo']); ?></td>
                        <td><?php echo htmlspecialchars($colaborador['fecha_nacimiento']); ?></td>
                        <td>
                            <?php if ($colaborador['activo'] == 1): ?>
                                <span class="status-badge active">Activo</span>
                            <?php else: ?>
                                <span class="status-badge inactive">Inactivo</span>
                            <?php endif; ?>
                        </td>

<!--                        Apartado de vacaciones-->

                        <td>
                            <a href="../colaboradores/ver.php?id=<?php echo $colaborador['id_colaborador']; ?>" class="btn btn-info btn-sm">Ver</a>
                            <?php
                            $colaborador_id = $colaborador['id_colaborador'];
                            $vacaciones = new Vacaciones($link);
                            $dias_disponibles = $vacaciones->obtenerDiasDisponibles($colaborador_id);
                            ?>
                            <!-- Botón para abrir el modal -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalVacaciones<?= $colaborador_id ?>">
                                Solicitar Vacaciones
                            </button>


                            <!-- Modal estilizado con Bootstrap -->
                            <div class="modal fade" id="modalVacaciones<?= $colaborador_id ?>" tabindex="-1" aria-labelledby="modalLabel<?= $colaborador_id ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content shadow-lg rounded-4 border-0">
                                        <div class="modal-header bg-primary text-white rounded-top">
                                            <h5 class="modal-title" id="modalLabel<?= $colaborador_id ?>"><i class="bi bi-sun"></i> Solicitar Vacaciones</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body bg-light">
                                            <form id="formVacaciones<?= $colaborador_id ?>" method="POST" action="registrarVacaciones.php" class="needs-validation">
                                                <input type="hidden" name="colaborador_id" value="<?= $colaborador_id ?>">

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Fecha de Inicio</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                                        <input type="date" name="fecha_inicio" id="inicio<?= $colaborador_id ?>" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Días a Tomar</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                                        <input type="number" name="dias" id="dias<?= $colaborador_id ?>" class="form-control" min="7" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Fecha Fin (calculada)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-calendar-range"></i></span>
                                                        <input type="text" name="fecha_fin" id="fin<?= $colaborador_id ?>" class="form-control" readonly>
                                                    </div>
                                                </div>

                                                <p class="text-muted">Días disponibles: <strong><?= $dias_disponibles ?></strong></p>

                                                <div class="d-flex justify-content-between mt-4">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle"></i> Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-send-check"></i> Enviar Solicitud
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                            <script>
                                const form<?= $colaborador_id ?> = document.getElementById("formVacaciones<?= $colaborador_id ?>");
                                const inicio<?= $colaborador_id ?> = document.getElementById("inicio<?= $colaborador_id ?>");
                                const dias<?= $colaborador_id ?> = document.getElementById("dias<?= $colaborador_id ?>");
                                const fin<?= $colaborador_id ?> = document.getElementById("fin<?= $colaborador_id ?>");
                                const disponibles<?= $colaborador_id ?> = <?= $dias_disponibles ?>;

                                function calcularFechaFin() {
                                    const inicio = new Date(inicio<?= $colaborador_id ?>.value);
                                    const dias = parseInt(dias<?= $colaborador_id ?>.value);

                                    if (!isNaN(inicio.getTime()) && dias >= 1) {
                                        const fin = new Date(inicio);
                                        fin.setDate(fin.getDate() + dias - 1);
                                        fin<?= $colaborador_id ?>.value = fin.toISOString().split('T')[0];
                                    } else {
                                        fin<?= $colaborador_id ?>.value = '';
                                    }
                                }

                                inicio<?= $colaborador_id ?>.addEventListener("change", calcularFechaFin);
                                dias<?= $colaborador_id ?>.addEventListener("input", calcularFechaFin);

                                form<?= $colaborador_id ?>.addEventListener("submit", function (e) {
                                    const hoy = new Date().toISOString().split('T')[0];
                                    const fechaInicio = inicio<?= $colaborador_id ?>.value;
                                    const diasTomar = parseInt(dias<?= $colaborador_id ?>.value);

                                    if (fechaInicio <= hoy) {
                                        e.preventDefault();
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Fecha inválida',
                                            text: 'La fecha de inicio debe ser posterior a hoy',
                                            showConfirmButton: true,
                                            confirmButtonColor: '#d33'
                                        });
                                    } else if (isNaN(diasTomar) || diasTomar < 7) {
                                        e.preventDefault();
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Días insuficientes',
                                            text: 'Debes solicitar al menos 7 días',
                                            showConfirmButton: true,
                                            confirmButtonColor: '#f0ad4e'
                                        });
                                    } else if (diasTomar > disponibles<?= $colaborador_id ?>) {
                                        e.preventDefault();
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Exceso de días',
                                            text: 'No tienes suficientes días disponibles',
                                            showConfirmButton: true,
                                            confirmButtonColor: '#d33'
                                        });
                                    }else {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Registro exitoso',
                                            text: 'Registro exitoso de vacaciones.',
                                            showConfirmButton: true,
                                            confirmButtonColor: '#d33'
                                        });
                                        setTimeout(() => {
                                            form<?= $colaborador_id ?>.reset();
                                            fin<?= $colaborador_id ?>.value = '';
                                            const modal = bootstrap.Modal.getInstance(document.getElementById("modalVacaciones<?= $colaborador_id ?>"))
                                            // Cierra y limpia backdrop si quedó colgado
                                            modal.hide();
                                            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                                            document.body.classList.remove('modal-open');
                                            document.body.style = '';
                                        }, 300);

                                    }
                                });
                            </script>
                        </td>


                        <?php if (!empty($colaborador['ruta_historial_academico_pdf'])): ?>
                                <a href="<?php echo htmlspecialchars(URL_BASE_PDFS . basename($colaborador['ruta_historial_academico_pdf'])); ?>" target="_blank" class="btn btn-secondary btn-sm mt-1">Ver PDF</a>
                            <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay colaboradores registrados en el sistema.</div>
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
    </html><?php
