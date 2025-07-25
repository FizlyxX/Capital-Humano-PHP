<?php
require_once '../config.php';

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
echo "\xEF\xBB\xBF"; // BOM para UTF-8
header("Content-Disposition: attachment; filename=reportes_sueldos.xls");

$condiciones = [];
$tipos = '';
$params = [];

if (!empty($_GET['nombre'])) {
    $condiciones[] = "(c.primer_nombre LIKE ? OR c.segundo_nombre LIKE ?)";
    $tipos .= "ss";
    $params[] = "%" . $_GET['nombre'] . "%";
    $params[] = "%" . $_GET['nombre'] . "%";
}

if (!empty($_GET['apellido'])) {
    $condiciones[] = "(c.primer_apellido LIKE ? OR c.segundo_apellido LIKE ?)";
    $tipos .= "ss";
    $params[] = "%" . $_GET['apellido'] . "%";
    $params[] = "%" . $_GET['apellido'] . "%";
}

if (!empty($_GET['sexo'])) {
    $condiciones[] = "c.sexo = ?";
    $tipos .= "s";
    $params[] = $_GET['sexo'];
}
if (!empty($_GET['edad'])) {
    $condiciones[] = "TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) = ?";
    $tipos .= "i";
    $params[] = intval($_GET['edad']);
}
if (!empty($_GET['salario'])) {
    $condiciones[] = "ca.sueldo >= ?";
    $tipos .= "d";
    $params[] = floatval($_GET['salario']);
}

$where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$sql = "SELECT c.primer_nombre, c.segundo_nombre, c.primer_apellido, c.segundo_apellido, c.sexo, TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) as edad,
                ca.sueldo, d.nombre_departamento AS departamento, o.nombre_ocupacion as ocupacion
        FROM colaboradores c
        JOIN cargos ca ON c.id_colaborador = ca.id_colaborador
        LEFT JOIN departamentos d ON ca.id_departamento = d.id_departamento
        LEFT JOIN ocupaciones o ON ca.id_ocupacion = o.id_ocupacion
        $where";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $tipos, ...$params);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        echo "<table border='1'>";
        echo "<tr><th>Nombre</th><th>Apellido</th><th>Sexo</th><th>Edad</th><th>Departamento</th><th>Ocupación</th><th>Salario</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['primer_nombre'] . ' ' . $row['segundo_nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['primer_apellido'] . ' ' . $row['segundo_apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($row['sexo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['edad']) . "</td>";
            echo "<td>" . htmlspecialchars($row['departamento']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ocupacion']) . "</td>";
            echo "<td>" . number_format($row['sueldo'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error al ejecutar la consulta: " . mysqli_error($link);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error en la preparación de la consulta: " . mysqli_error($link);
}

mysqli_close($link);
?>