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
$current_page = 'usuarios';

// Incluir la barra de navegación reusable
require_once '../includes/navbar.php'; 

$id = $nombre_usuario = $contrasena = $confirm_contrasena = "";
$nombre_usuario_err = $contrasena_err = $confirm_contrasena_err = $rol_err = "";
$id_rol = '';

$roles = getRoles($link);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    if (empty(trim($_POST["nombre_usuario"]))) {
        $nombre_usuario_err = "Por favor ingrese un nombre de usuario.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE nombre_usuario = ? AND id != ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $param_nombre_usuario, $param_id);
            $param_nombre_usuario = trim($_POST["nombre_usuario"]);
            $param_id = $id;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $nombre_usuario_err = "Este nombre de usuario ya está en uso por otro usuario.";
                } else {
                    $nombre_usuario = trim($_POST["nombre_usuario"]);
                }
            } else {
                echo "¡Ups! Algo salió mal al verificar el usuario.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    $contrasena_nueva = trim($_POST["contrasena"]);
    $confirm_contrasena = trim($_POST["confirm_contrasena"]);

    if (!empty($contrasena_nueva)) {
        if (strlen($contrasena_nueva) < 6) {
            $contrasena_err = "La contraseña debe tener al menos 6 caracteres.";
        } else {
            $contrasena = $contrasena_nueva;
        }

        if (empty($confirm_contrasena)) {
            $confirm_contrasena_err = "Por favor confirme la nueva contraseña.";
        } elseif (empty($contrasena_err) && ($contrasena != $confirm_contrasena)) {
            $confirm_contrasena_err = "Las contraseñas no coinciden.";
        }
    }

    if (empty($_POST["id_rol"]) || !is_numeric($_POST["id_rol"])) {
        $rol_err = "Por favor seleccione un rol válido.";
    } else {
        $id_rol = (int)$_POST["id_rol"];
    }

    if (empty($nombre_usuario_err) && empty($contrasena_err) && empty($confirm_contrasena_err) && empty($rol_err)) {
        if (actualizarUsuario($link, $id, $nombre_usuario, $contrasena, $id_rol)) {
            header("location: index.php?msg=actualizado");
            exit();
        } else {
            echo '<div class="alert alert-danger">Error al actualizar el usuario. Puede que el nombre de usuario ya exista.</div>';
        }
    }
    mysqli_close($link);

} else {
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        $usuario_data = getUsuarioById($link, $id);

        if ($usuario_data) {
            $nombre_usuario = $usuario_data['nombre_usuario'];
            $id_rol = $usuario_data['id_rol'];
        } else {
            header("location: index.php");
            exit();
        }
    } else {
        header("location: index.php");
        exit();
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Capital Humano</title>
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
        <h2>Editar Usuario</h2>
        <p>Modifique los datos del usuario.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <div class="mb-3 <?php echo (!empty($nombre_usuario_err)) ? 'has-error' : ''; ?>">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="<?php echo htmlspecialchars($nombre_usuario); ?>">
                <span class="invalid-feedback text-danger"><?php echo $nombre_usuario_err; ?></span>
            </div>
            <div class="mb-3 <?php echo (!empty($contrasena_err)) ? 'has-error' : ''; ?>">
                <label for="contrasena" class="form-label">Nueva Contraseña (Dejar vacío para no cambiar):</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" value="">
                <span class="invalid-feedback text-danger"><?php echo $contrasena_err; ?></span>
            </div>
            <div class="mb-3 <?php echo (!empty($confirm_contrasena_err)) ? 'has-error' : ''; ?>">
                <label for="confirm_contrasena" class="form-label">Confirmar Nueva Contraseña:</label>
                <input type="password" name="confirm_contrasena" id="confirm_contrasena" class="form-control" value="">
                <span class="invalid-feedback text-danger"><?php echo $confirm_contrasena_err; ?></span>
            </div>
            <div class="mb-3 <?php echo (!empty($rol_err)) ? 'has-error' : ''; ?>">
                <label for="id_rol" class="form-label">Rol:</label>
                <select name="id_rol" id="id_rol" class="form-select">
                    <option value="">Seleccione un rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo htmlspecialchars($rol['id_rol']); ?>" <?php echo ($id_rol == $rol['id_rol']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($rol['nombre_rol']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="invalid-feedback text-danger"><?php echo $rol_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Actualizar Usuario">
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