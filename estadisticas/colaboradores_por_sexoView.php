<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/Footer.php';
require_once __DIR__ . '/../reportes/colaboradores_por_sexo.php';

$current_page = 'colaboradores_sexo';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php?error=not_authenticated");
    exit;
}

$data = colaboradorXSexo();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Colaboradores por Sexo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="container mt-4 content-wrapper">
    <h2 class="mb-4">Distribución de Colaboradores por Sexo</h2>

    <?php if (!$data || !isset($data['masculino']) || !isset($data['femenino'])): ?>
        <div class="alert alert-danger">No se pudo obtener la información desde la API.</div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Sexo</th>
                        <th>Cantidad</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>Masculino</td><td><?= htmlspecialchars($data['masculino']) ?></td></tr>
                    <tr><td>Femenino</td><td><?= htmlspecialchars($data['femenino']) ?></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
                <canvas id="sexoChart"></canvas>
            </div>
        </div>
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

<?php if ($data && isset($data['masculino'], $data['femenino'])): ?>
    <script>
        const ctx = document.getElementById('sexoChart');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Masculino', 'Femenino'],
                datasets: [{
                    label: 'Colaboradores',
                    data: [<?= $data['masculino'] ?>, <?= $data['femenino'] ?>],
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
