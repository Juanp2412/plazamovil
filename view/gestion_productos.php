<?php
include '../navbar.php';
require_once '../controller/gestion_productos.php';
require_once '../controller/medidas_controller.php';
require_once '../controller/gestion_categorias.php';

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_rol = isset($_SESSION['user_id_rol']) ? (int) $_SESSION['user_id_rol'] : null;


// Verificar si el usuario tiene el rol de administrador
if ($id_rol !== 1) {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id_agricultor, id_zona FROM agricultor");
$stmt->execute();
$agricultor = $stmt->fetchAll(PDO::FETCH_ASSOC);



ob_start();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/Plaza-M-vil-3.1/css/styles.css">

</head>

<body>
    <div style="height:70px"></div>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestión de Productos</h1>

        <!-- Botón para abrir el modal de creación de producto -->
        <div class="text-end mb-3">
            <a href="#" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="bi bi-plus-circle"></i> Añadir Producto
            </a>
        </div>

        <!-- Tabla de productos -->
        <table class="table table-bordered table-hover">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Foto</th>
                    <th>Fecha Publicación</th>
                    <th>Categoría</th>
                    <th>Agricultor</th>
                    <th>Unidad de Medida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['id_producto']); ?></td>
                        <td><?= htmlspecialchars($producto['nombre']); ?></td>
                        <td><?= htmlspecialchars($producto['descripcion']); ?></td>
                        <td><?= htmlspecialchars($producto['precio_unitario']); ?></td>
                        <td><?= htmlspecialchars($producto['stock']); ?></td>
                        <td>
                            <?php if (!empty($producto['foto'])): ?>
                                <img src="../img/<?= htmlspecialchars($producto['foto']); ?>" alt="Imagen producto" width="60"
                                    height="60">
                            <?php else: ?>
                                <span class="text-muted">Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($producto['fecha_publicacion']); ?></td>
                        <td><?= htmlspecialchars($producto['categoria']); ?></td>
                        <td><?= htmlspecialchars($producto['agricultor']); ?></td>
                        <td><?= htmlspecialchars($producto['unidades_de_medida']); ?></td>
                        <td>
                            <!-- Botón para abrir el modal de edición -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editarProductoModal<?= $producto['id_producto']; ?>">
                                <i class="bi bi-pencil"></i> Editar
                            </button>

                            <!-- Modal de edición -->
                            <div class="modal fade" id="editarProductoModal<?= $producto['id_producto']; ?>" tabindex="-1"
                                aria-labelledby="editarProductoModalLabel<?= $producto['id_producto']; ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="../controller/gestion_productos.php" method="POST"
                                            enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="editarProductoModalLabel<?= $producto['id_producto']; ?>">Editar
                                                    Producto</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="accion" value="actualizar">
                                                <input type="hidden" name="id_producto"
                                                    value="<?= $producto['id_producto']; ?>">
                                                <div class="mb-3">
                                                    <label for="nombre" class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" name="nombre"
                                                        value="<?= htmlspecialchars($producto['nombre']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="descripcion" class="form-label">Descripción</label>
                                                    <textarea class="form-control" name="descripcion"
                                                        required><?= htmlspecialchars($producto['descripcion']); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="precio_unitario" class="form-label">Precio</label>
                                                    <input type="number" class="form-control" name="precio_unitario"
                                                        value="<?= htmlspecialchars($producto['precio_unitario']); ?>"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stock" class="form-label">Stock</label>
                                                    <input type="number" class="form-control" name="stock"
                                                        value="<?= htmlspecialchars($producto['stock']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="foto" class="form-label">Foto</label>
                                                    <input type="file" class="form-control" name="foto">
                                                    <input type="hidden" name="foto_actual"
                                                        value="<?= htmlspecialchars($producto['foto']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="id_categoria" class="form-label">Categoría</label>
                                                    <select class="form-control" id="id_categoria" name="id_categoria"
                                                        required>
                                                        <option value="">-- Selecciona una categoría --</option>
                                                        <?php foreach ($categorias as $cat): ?>
                                                            <option value="<?= htmlspecialchars($cat['id_categoria']) ?>">
                                                                <?= htmlspecialchars($cat['nombre']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
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
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal Agregar Producto -->
                            <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form class="modal-content p-4" id="formAgregarProducto" method="POST"
                                        action="../controller/productcontroller.php" enctype="multipart/form-data">
                                        <h2 class="text-center mb-4">Añadir Nuevo Producto</h2>
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Id Del Agricultor</label>
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
                                            <label for="foto" class="form-label">Imagen del Producto</label>
                                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*"
                                                required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-success">Añadir Producto</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Botón para eliminar -->
                            <form action="../controller/gestion_productos.php" method="POST" class="d-inline">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_producto" value="<?= $producto['id_producto']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>