<?php
session_start();
require_once '../config/conexion.php';

// Asegurarse de que $id_rol sea un entero para evitar problemas de comparación estricta
$id_rol = isset($_SESSION['user_id_rol']) ? (int) $_SESSION['user_id_rol'] : null;

// Verificar si el usuario tiene el rol de administrador
if ($id_rol !== 1) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/Plaza-M-vil-3.1/css/styles.css">
    
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>

    <!-- Espacio para que el contenido no quede oculto bajo la navbar fija -->
    <div style="height:70px"></div>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Dashboard - Administrador</h1>
        <div class="row">
            <!-- Gestión de Usuarios -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill" style="font-size: 3rem; color: #007bff;"></i>
                        <h5 class="card-title mt-3">Gestión de Usuarios</h5>
                        <p class="card-text">Administra los usuarios registrados en el sistema.</p>
                        <a href="gestion_usuarios.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>

            <!-- Gestión de Productos -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam" style="font-size: 3rem; color: #28a745;"></i>
                        <h5 class="card-title mt-3">Gestión de Productos</h5>
                        <p class="card-text">Administra los productos publicados en el sistema.</p>
                        <a href="gestion_productos.php" class="btn btn-success">Ir</a>
                    </div>
                </div>
            </div>

            <!-- gestión unidades de medida -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-scale" style="font-size: 3rem; color: #dc3545;"></i>
                        <div style="font-size: 1.5rem; color: #dc3545; font-weight: bold;">Kg</div>
                        <h5 class="card-title mt-3">Gestión de medidas</h5>
                        <p class="card-text">Administra las unidades de medida publicadas en el sistema.</p>
                        <a href="gestion_medidas.php" class="btn btn-danger">Ir</a>
                    </div>
                </div>
            </div>

            <!-- Gestión de Categorías -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-tags-fill" style="font-size: 3rem; color: #ffc107;"></i>
                        <h5 class="card-title mt-3">Gestión de Categorías</h5>
                        <p class="card-text">Administra las categorías disponibles en el sistema.</p>
                        <a href="gestion_categorias.php" class="btn btn-warning">Ir</a>
                    </div>
                </div>
            </div>

            <!-- Gestión de PQRS -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-chat-dots-fill" style="font-size: 3rem; color: #17a2b8;"></i>
                        <h5 class="card-title mt-3">Gestión de PQRS</h5>
                        <p class="card-text">Revisa y responde las PQRS enviadas por los usuarios.</p>
                        <a href="admin_pqrs.php" class="btn btn-info">Ir</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <p class="mb-0">&copy; 2025 Plaza Móvil. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>