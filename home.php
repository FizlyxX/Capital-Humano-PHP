<?php
session_start();

require_once 'config.php'; 
require_once 'classes/Footer.php'; 
require_once 'usuarios/funciones.php'; 

// Verificar si el usuario ha iniciado sesión, si no, redirigirlo a la página de login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Definir la página actual para que el navbar la resalte
$current_page = 'home';

// Incluir la barra de navegación reusable
require_once 'includes/navbar.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            padding-bottom: 50px;
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
    <div class="container mt-4 content">
        <div class="alert alert-success" role="alert">
            Bienvenido al sistema, **<?php echo htmlspecialchars($_SESSION["username"]); ?>**!
        </div>
        <h3>Módulos Disponibles</h3>
        <p>Selecciona un módulo del menú de navegación para empezar a trabajar.</p>

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