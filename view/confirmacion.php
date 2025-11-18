<?php
require_once __DIR__ . '/../config/paths.php';
session_start();
require_once '../config/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Confirmación de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>

    <!-- Espacio para que el contenido no quede oculto bajo la navbar fija -->
    <div style="height:70px"></div>

    <div class="container mt-5 text-center">
        <h1 class="text-success">¡Compra realizada con éxito!</h1>
        <p>Gracias por tu compra. Hemos recibido tu pedido y lo estamos procesando.</p>
        <a href="../index.php" class="btn btn-primary mt-3"><i class="bi bi-arrow-left"></i> Volver al inicio</a>
    </div>
</body>

</html>