<?php
session_start();

require_once '../config.php';
require_once '../classes/Footer.php';

$current_page = 'estadisticas';

if (!isset($_SESSION["loggedin"])) {
    header("location: ../index.php");
    exit;
}

// Consultas
$sexo_query = "SELECT sexo, COUNT(*) as total FROM colaboradores GROUP BY sexo";
$edad_query = "SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad FROM colaboradores";
$direccion_query = "SELECT direccion, COUNT(*) as total FROM colaboradores GROUP BY direccion";

$sexo_data = mysqli_query($link, $sexo_query);
$edad_data = mysqli_query($link, $edad_query);
$direccion_data = mysqli_query($link, $direccion_query);

// Procesar edades
$edad_total = 0;
$contador = 0;
$grupos_etarios = [
    "18-25" => 0,
    "26-35" => 0,
    "36-45" => 0,
    "46-60" => 0,
    "60+" => 0
];

while ($row = mysqli_fetch_assoc($edad_data)) {
    $edad = $row['edad'];
    $edad_total += $edad;
    $contador++;

    if ($edad >= 18 && $edad <= 25) $grupos_etarios["18-25"]++;
    elseif ($edad <= 35) $grupos_etarios["26-35"]++;
    elseif ($edad <= 45) $grupos_etarios["36-45"]++;
    elseif ($edad <= 60) $grupos_etarios["46-60"]++;
    else $grupos_etarios["60+"]++;
}

$promedio_edad = $contador ? round($edad_total / $contador, 2) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas del Sistema</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="container mt-4 content-wrapper">
    <h2 class="mb-4">Estadísticas del Sistema</h2>

    <div class="row mb-5">
        <div class="col-md-6">
            <canvas id="sexoChart"></canvas>
        </div>
        <div class="col-md-6">
            <h4>Promedio de Edad</h4>
            <p><strong><?php echo $promedio_edad; ?> años</strong></p>

            <h4>Grupos Etarios</h4>
            <table class="table table-bordered">
                <thead><tr><th>Grupo</th><th>Cantidad</th></tr></thead>
                <tbody>
                <?php foreach ($grupos_etarios as $grupo => $cantidad): ?>
                    <tr><td><?php echo $grupo; ?></td><td><?php echo $cantidad; ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4>Colaboradores por Dirección</h4>
    <table class="table table-striped">
        <thead><tr><th>Dirección</th><th>Cantidad</th></tr></thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($direccion_data)): ?>
            <tr><td><?php echo htmlspecialchars($row['direccion']); ?></td><td><?php echo $row['total']; ?></td></tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
if (class_exists('Footer')) {
    $footer = new Footer();
    $footer->render();
} else {
    echo '<footer class="footer"><div class="container"><p>&copy; ' . date("Y") . ' Proyecto PHP Capital Humano.</p></div></footer>';
}
?>

<script>
    const ctx = document.getElementById('sexoChart');
    const sexoChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [
                <?php mysqli_data_seek($sexo_data, 0); while($row = mysqli_fetch_assoc($sexo_data)) echo "'" . $row['sexo'] . "',"; ?>
            ],
            datasets: [{
                data: [
                    <?php mysqli_data_seek($sexo_data, 0); while($row = mysqli_fetch_assoc($sexo_data)) echo $row['total'] . ","; ?>
                ],
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
