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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- Tu CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }

        .carousel-container {
            flex: 1;
            overflow: hidden;
        }

        .carousel-item img {
            object-fit: cover;
            height: 100vh;
            width: 100%;
        }

        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            padding: 40px;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.05);
        }

        .login-content {
            width: 100%;
            max-width: 420px;
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .logo {
            display: block;
            max-width: 70px;
            margin: 0 auto 20px;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        p {
            color: #6c757d;
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            font-size: 16px;
        }

        .btn-primary {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            background-color: #0069d9;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #dc3545;
            margin-top: 15px;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Carrusel de imágenes -->
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

<!-- Panel de login -->
<div class="right-panel">
    <div class="login-content">
        <img src="images/Logo.png" alt="Logo" class="logo">
        <h2 class="text-center">Bienvenido</h2>
        <p class="text-center mb-4">Sistema de Capital Humano</p>

        <form action="login.php" method="post">
            <div class="form-group mb-3">
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Carrusel automático -->
<script>
    const carousel = document.querySelector('#carouselLogin');
    new bootstrap.Carousel(carousel, {
        interval: 3500,
        ride: 'carousel',
        pause: false,
        wrap: true
    });
</script>
</body>
</html>
