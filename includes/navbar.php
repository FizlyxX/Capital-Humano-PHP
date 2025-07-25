<?php

require_once __DIR__ . '/../usuarios/funciones.php'; 

$current_user_is_admin = esAdministrador();
$current_user_is_rrhh = esRRHH(); 

$base_url = '/Capital-Humano-PHP/'; 

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
                    <a class="nav-link <?php echo ($current_page == 'usuarios') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>usuarios/index.php">Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'roles') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>roles/index.php">Roles</a>
                </li>
                <?php endif; ?>

                <?php if ($current_user_is_admin || $current_user_is_rrhh): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'colaboradores') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>colaboradores/index.php">Colaboradores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'cargos') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>cargos/index.php">Cargos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'reportes') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>reportes/colaboradores_sueldos.php">Reportes</a>
                </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_page == 'estadisticas' || $current_page == 'colaboradores_sexo') ? 'active' : ''; ?>" href="#" id="estadisticasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Estadísticas
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'estadisticas') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>estadisticas/estadisticas.php">Generales</a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'colaboradores_sexo') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>estadisticas/colaboradores_por_sexoView.php">Colaboradores por Sexo</a>
                            </li>
                        </ul>
                    </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'vacaciones') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>vacaciones/vacacionesView.php">Vacaciones</a>
                </li>
                <?php endif; ?>

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