<?php

require_once __DIR__ . '/../usuarios/funciones.php'; 

// Definir el estado de administrador una vez para usarlo en las condiciones
$current_user_is_admin = esAdministrador();
$current_user_is_rrhh = esRRHH(); 

$base_url = '/Proyecto_php/'; // ¡AJUSTA ESTA LÍNEA A LA RUTA BASE DE TU PROYECTO!

// $current_page debe ser definida en el archivo que incluye navbar.php (ej. home.php, usuarios/index.php)
// Si $current_page no está definida, se inicializa para evitar errores
if (!isset($current_page)) {
    $current_page = '';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $base_url; ?>home.php">Capital Humano</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>" aria-current="page" href="<?php echo $base_url; ?>home.php">Home</a>
                </li>

                <?php if ($current_user_is_admin): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'usuarios') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>usuarios/index.php">Módulo de Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'roles') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>roles/index.php">Módulo de Roles</a>
                </li>
                <?php endif; ?>

                <?php if ($current_user_is_admin || $current_user_is_rrhh): // <-- NUEVA CONDICIÓN PARA COLABORADORES ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'colaboradores') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>colaboradores/index.php">Módulo de Colaboradores</a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="#">Módulo de Cargos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Vacaciones</a>
                </li>

            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>