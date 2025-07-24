<?php
session_start();
require_once '../config.php';
require_once '../classes/Footer.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../index.php");
    exit;
}

$current_page = 'reportes';

$condiciones = [];
$params = [];

if (!empty($_GET['nombre'])) {
    $condiciones[] = "(c.primer_nombre LIKE ? OR c.segundo_nombre LIKE ?)";
    $params[] = "%" . $_GET['nombre'] . "%";
    $params[] = "%" . $_GET['nombre'] . "%";
}

if (!empty($_GET['apellido'])) {
    $condiciones[] = "(c.primer_apellido LIKE ? OR c.segundo_apellido LIKE ?)";
    $params[] = "%" . $_GET['apellido'] . "%";
    $params[] = "%" . $_GET['apellido'] . "%";
}

if (!empty($_GET['sexo'])) {
    $condiciones[] = "c.sexo = ?";
    $params[] = $_GET['sexo'];
}
if (!empty($_GET['edad'])) {
    $condiciones[] = "TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) = ?";
    $params[] = $_GET['edad'];
}
if (!empty($_GET['salario'])) {
    $condiciones[] = "ca.sueldo >= ?";
    $params[] = $_GET['salario'];
}

$where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Consulta principal
$sql = "SELECT c.id_colaborador, c.primer_nombre, c.segundo_nombre, c.primer_apellido, c.segundo_apellido, c.sexo, TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) as edad,
               ca.sueldo, d.nombre_departamento AS departamento, o.nombre_ocupacion as ocupacion
        FROM colaboradores c
        JOIN cargos ca ON c.id_colaborador = ca.id_colaborador
        LEFT JOIN departamentos d ON ca.id_departamento = d.id_departamento
        LEFT JOIN ocupaciones o ON ca.id_ocupacion = o.id_ocupacion
        $where
        LIMIT $limit OFFSET $offset";

$stmt = mysqli_prepare($link, $sql);
if ($params) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Total de registros para paginación
$sql_count = "SELECT COUNT(*) as total FROM colaboradores c JOIN cargos ca ON c.id_colaborador = ca.id_colaborador 
              LEFT JOIN departamentos d ON ca.id_departamento = d.id_departamento
              LEFT JOIN ocupaciones o ON ca.id_ocupacion = o.id_ocupacion
              $where";
$stmt_count = mysqli_prepare($link, $sql_count);
if ($params) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt_count, $types, ...$params);
}
mysqli_stmt_execute($stmt_count);
$res_count = mysqli_stmt_get_result($stmt_count);
$total_rows = mysqli_fetch_assoc($res_count)['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Sueldos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="container mt-4 content-wrapper">
    <h2 class="mb-4">Reporte de Colaboradores y Sueldos</h2>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-2"><input type="text" name="nombre" placeholder="Nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2"><input type="text" name="apellido" placeholder="Apellido" value="<?= htmlspecialchars($_GET['apellido'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2">
            <select name="sexo" class="form-select">
                <option value="">Sexo</option>
                <option value="M" <?= ($_GET['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= ($_GET['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
            </select>
        </div>
        <div class="col-md-2"><input type="number" name="edad" placeholder="Edad exacta" value="<?= htmlspecialchars($_GET['edad'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2"><input type="number" name="salario" placeholder="Salario >= $" value="<?= htmlspecialchars($_GET['salario'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2 d-grid gap-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="reporte_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-success">Exportar Excel</a>
        </div>
    </form>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Sexo</th>
            <th>Edad</th>
            <th>Departamento</th>
            <th>Ocupación</th>
            <th>Salario ($)</th>
        </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['primer_nombre'] . ' ' . $row['segundo_nombre']) ?></td>
                <td><?= htmlspecialchars($row['primer_apellido'] . ' ' . $row['segundo_apellido']) ?></td>
                <td><?= htmlspecialchars($row['sexo']) ?></td>
                <td><?= $row['edad'] ?></td>
                <td><?= htmlspecialchars($row['departamento']) ?></td>
                <td><?= htmlspecialchars($row['ocupacion']) ?></td>
                <td><?= number_format($row['sueldo'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php
if (class_exists('Footer')) {
    $footer = new Footer();
    $footer->render();
} else {
    echo '<footer class="footer"><div class="container"><p>&copy; ' . date("Y") . ' Proyecto PHP Capital Humano.</p></div></footer>';
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
