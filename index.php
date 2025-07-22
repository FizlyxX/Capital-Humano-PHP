<?php
session_start();

$error_message = null;
if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') {
    $error_message = "Usuario o contraseña incorrectos.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Capital Humano</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos CSS incrustados para el diseño del login con carrusel */
        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box; /* Asegura que padding y border se incluyan en el ancho/alto */
        }

        body {
            display: flex; /* Habilita Flexbox para los dos paneles */
            height: 100vh; /* Asegura que ocupe toda la altura del viewport */
            margin: 0;
            background-color: #f0f2f5; /* Color de fondo general si no hay carrusel */
            overflow: hidden; /* Importante para el carrusel de altura completa */
        }

        /* Contenedor del Carrusel (Panel Izquierdo) */
        .carousel-container {
            flex: 1; /* Ocupa la mitad del ancho (o más, si right-panel es fijo) */
            overflow: hidden; /* Asegura que el contenido del carrusel no se desborde */
            position: relative; /* Para posibles elementos superpuestos */
        }

        .carousel-item img {
            object-fit: cover; /* Las imágenes cubren el área sin distorsionarse */
            height: 100vh; /* Las imágenes del carrusel ocupan toda la altura */
            width: 100%; /* Las imágenes del carrusel ocupan todo el ancho de su contenedor */
        }

        /* Panel Derecho (Formulario de Login) */
        .right-panel {
            flex: 1; /* Ocupa la otra mitad del ancho (o el espacio restante) */
            display: flex;
            align-items: center; /* Centra verticalmente el contenido del login */
            justify-content: center; /* Centra horizontalmente el contenido del login */
            background-color: #ffffff; /* Fondo blanco para el panel de login */
            padding: 40px; /* Relleno interno */
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.05); /* Sombra sutil a la izquierda */
        }

        .login-content {
            width: 100%;
            max-width: 420px; /* Ancho máximo para el contenido del formulario */
            /* Si quieres un "card" dentro del right-panel, vuelve a poner background-color, padding, etc. */
            background-color: #fff; /* Asegura el fondo blanco del card */
            padding: 40px 30px; /* Padding del card */
            border-radius: 16px; /* Bordes redondeados del card */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); /* Sombra del card */
        }

        .logo {
            display: block;
            max-width: 70px; /* Tamaño del logo */
            margin: 0 auto 20px; /* Centra el logo y añade margen inferior */
        }

        h2 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333; /* Color de texto para el título */
        }

        p {
            color: #6c757d; /* Color de texto para el subtítulo */
            margin-bottom: 30px; /* Espacio debajo del subtítulo */
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            font-size: 16px;
            border: 1px solid #ced4da; /* Borde estándar de Bootstrap */
        }
        .form-control:focus {
            border-color: #80bdff; /* Borde azul al enfocar */
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25); /* Sombra de enfoque */
        }

        .btn-primary {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            background-color: #0069d9; /* Azul de Bootstrap */
            border: none;
            transition: background-color 0.2s ease; /* Transición suave al pasar el ratón */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
        }

        .error-message {
            color: #dc3545; /* Rojo de error */
            margin-top: 15px;
            text-align: center;
            font-weight: 500;
        }

        /* Media Queries para responsividad */
        @media (max-width: 768px) {
            body {
                flex-direction: column; /* Apilar en pantallas pequeñas */
            }
            .carousel-container {
                flex: none; /* Desactivar flex-grow */
                height: 250px; /* Altura fija para el carrusel en móvil */
                width: 100%;
            }
            .right-panel {
                flex: none; /* Desactivar flex-grow */
                width: 100%;
                padding: 30px 20px;
            }
            .login-content {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="carousel-container">
    <div id="carouselLogin" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/login_image1.jpg" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="images/login_image2.jpg" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="images/login_image3.jpg" class="d-block w-100" alt="Slide 3">
            </div>
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="login-content">
        <img src="images/Logo.png" alt="Logo de la Empresa" class="logo">
        <h2 class="text-center">Bienvenido</h2>
        <p class="text-center mb-4">Sistema de Capital Humano</p>

        <form action="login.php" method="post"> <div class="form-group mb-3">
                <input type="text" id="username" name="username" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="form-group mb-4">
                <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="form-group d-grid mb-2">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
            <?php if ($error_message): ?>
                <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const carousel = document.querySelector('#carouselLogin');
    if (carousel) { // Asegurarse de que el carrusel existe
        new bootstrap.Carousel(carousel, {
            interval: 3500, // Tiempo entre slides
            ride: 'carousel', // Iniciar automáticamente
            pause: false, // No pausar al pasar el ratón
            wrap: true // Volver al inicio al llegar al final
        });
    }
</script>
</body>
</html>