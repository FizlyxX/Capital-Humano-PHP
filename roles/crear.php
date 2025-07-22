<?php
session_start();

require_once '../config.php';
require_once 'funciones.php';
require_once '../classes/Footer.php';

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !esAdministrador()) {
    header("location: ../index.php");
    exit;
}

// Definir la página actual para que el navbar la resalte
$current_page = 'roles';

// Incluir la barra de navegación reusable
require_once '../includes/navbar.php'; 

$nombre_rol = $descripcion = "";
$nombre_rol_err = $descripcion_err = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar nombre del rol
    if (empty(trim($_POST["nombre_rol"]))) {
        $nombre_rol_err = "Por favor ingrese un nombre para el rol.";
    } else {
        $sql = "SELECT id_rol FROM roles WHERE nombre_rol = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_nombre_rol);
            $param_nombre_rol = trim($_POST["nombre_rol"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $nombre_rol_err = "Este nombre de rol ya existe.";
                } else {
                    $nombre_rol = trim($_POST["nombre_rol"]);
                }
            } else {
                echo "¡Ups! Algo salió mal al verificar el rol.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validar descripción (opcional, puede ser vacía)
    $descripcion = trim($_POST["descripcion"]);

    // Si no hay errores, intentar crear el rol
    if (empty($nombre_rol_err) && empty($descripcion_err)) {
        if (crearRol($link, $nombre_rol, $descripcion)) {
            header("location: index.php?msg=creado");
            exit();
        } else {
            echo '<div class="alert alert-danger">Error al crear el rol. Por favor, inténtelo de nuevo.</div>';
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Rol - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .content-wrapper { flex: 1; padding-bottom: 50px; }
        .form-group { margin-bottom: 1rem; }
        .invalid-feedback { display: block; }
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
        <h2>Crear Nuevo Rol</h2>
        <p>Complete el formulario para añadir un nuevo rol al sistema.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3 <?php echo (!empty($nombre_rol_err)) ? 'has-error' : ''; ?>">
                <label for="nombre_rol" class="form-label">Nombre del Rol:</label>
                <input type="text" name="nombre_rol" id="nombre_rol" class="form-control" value="<?php echo htmlspecialchars($nombre_rol); ?>">
                <span class="invalid-feedback text-danger"><?php echo $nombre_rol_err; ?></span>
            </div>
            <div class="mb-3 <?php echo (!empty($descripcion_err)) ? 'has-error' : ''; ?>">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($descripcion); ?></textarea>
                <span class="invalid-feedback text-danger"><?php echo $descripcion_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Crear Rol">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
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