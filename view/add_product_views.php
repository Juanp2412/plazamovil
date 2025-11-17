<?php
session_start();

if (!isset($_SESSION['user_id_usuario']) || $_SESSION['user_id_rol'] !== 3) {
    header("Location: ../view/login.php");
    exit;
}

require_once '../config/conexion.php';
require_once '../controller/medidas_controller.php';
require_once '../controller/gestion_categorias.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/Plaza-M-vil-3.1/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-lg p-4 rounded-3">

                    <h2 class="text-center mb-4">Añadir Nuevo Producto</h2>
                    <form action="../controller/productcontroller.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio_unitario" class="form-label">Precio Unitario</label>
                            <input type="number" step="0.01" class="form-control" id="precio_unitario"
                                name="precio_unitario" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock Disponible</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_unidad" class="form-label">Unidad de Medida</label>
                            <select class="form-control" id="id_unidad" name="id_unidad" required>
                                <option value="">-- Selecciona una unidad --</option>
                                <?php foreach ($medidas as $medida): ?>
                                    <option value="<?= htmlspecialchars($medida['id_unidad']) ?>">
                                        <?= htmlspecialchars($medida['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_categoria" class="form-label">Categoría</label>
                            <select class="form-control" id="id_categoria" name="id_categoria" required>
                                <option value="">-- Selecciona una categoría --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['id_categoria']) ?>">
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_publicacion" class="form-label">Fecha de publicacion</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Imagen del Producto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Añadir Producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>