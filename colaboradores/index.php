<?php
session_start();

require_once '../config.php';
require_once 'funciones.php'; // Incluye las funciones del módulo de colaboradores
require_once '../classes/Footer.php'; 
require_once '../includes/navbar.php'; 

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

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Colaboradores - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
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
        <h2>Gestión de Colaboradores</h2>
        <p>Administra la información de los colaboradores de la empresa.</p>

        <?php echo $mensaje_confirmacion; ?>

        <div class="d-flex justify-content-between mb-3">
            <a href="crear.php" class="btn btn-success">Registrar Nuevo Colaborador</a>
            <?php if ($mostrar_inactivos_param): ?>
                <a href="index.php" class="btn btn-info">Mostrar Solo Activos</a>
            <?php else: ?>
                <a href="index.php?mostrar_inactivos=true" class="btn btn-info">Mostrar Todos (Activos/Inactivos)</a>
            <?php endif; ?>
        </div>

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
                                <td>
                                    <a href="ver.php?id=<?php echo $colaborador['id_colaborador']; ?>" class="btn btn-info btn-sm">Ver</a>
                                    <a href="editar.php?id=<?php echo $colaborador['id_colaborador']; ?>" class="btn btn-info btn-sm">Editar</a>
                                    <?php if ($colaborador['activo'] == 1): ?>
                                        <a href="eliminar.php?id=<?php echo $colaborador['id_colaborador']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('¿Está seguro de DESACTIVAR a este colaborador?');">Desactivar</a>
                                    <?php else: ?>
                                        <a href="activar.php?id=<?php echo $colaborador['id_colaborador']; ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Está seguro de ACTIVAR a este colaborador?');">Activar</a>
                                    <?php endif; ?>
                                    <?php if (!empty($colaborador['ruta_historial_academico_pdf'])): ?>
                                        <a href="<?php echo htmlspecialchars(URL_BASE_PDFS . basename($colaborador['ruta_historial_academico_pdf'])); ?>" target="_blank" class="btn btn-secondary btn-sm mt-1">Ver PDF</a>
                                    <?php endif; ?>
                                </td>
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
</html>