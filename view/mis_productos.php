<?php
require_once __DIR__ . '/../config/paths.php';
session_start();
require_once '../config/conexion.php';
require_once '../controller/medidas_controller.php';
require_once '../controller/gestion_categorias.php';

// Validar sesión y rol
if (!isset($_SESSION['user_id_usuario']) || $_SESSION['user_id_rol'] != 3) {
    header("Location: ../index.php");
    exit;
}

$user_id_usuario = $_SESSION['user_id_usuario'];
$id_agricultor = $_SESSION['user_id_agricultor'] ?? null;

// Obtener productos del agricultor
$stmt = $pdo->prepare("
    SELECT p.*, c.nombre AS categoria_nombre, u.nombre AS unidad_nombre
    FROM productos p
    INNER JOIN categoria c ON p.id_categoria = c.id_categoria
    LEFT JOIN unidades_de_medida u ON p.id_unidad = u.id_unidad
    WHERE p.id_agricultor = ?
    ORDER BY p.fecha_publicacion DESC
");
$stmt->execute([$id_agricultor]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías
$stmtCategorias = $pdo->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre ASC");
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
   
</head>

<body>
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mis Productos</h2>
            <div>
                <a href="#" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="bi bi-plus-circle"></i> Añadir Producto
                </a>
                <button type="button" id="btnEliminar" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Eliminar Seleccionados
                </button>
            </div>
        </div>

        <!-- Formulario para eliminar productos -->
        <form id="formEliminarProductos" method="POST" action="../controller/eliminarproductos.php">
            <div class="row">
                <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo !empty($producto['foto']) ? '../img/' . htmlspecialchars($producto['foto']) : '../img/default.png'; ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <input type="checkbox" class="producto-checkbox" name="productos_a_eliminar[]"
                                        value="<?php echo $producto['id_producto']; ?>">
                                    <?php echo htmlspecialchars($producto['nombre']); ?>
                                </h5>
                                <p class="card-text">Descripción: <?php echo htmlspecialchars($producto['descripcion']); ?>
                                </p>
                                <p class="card-text"><strong>Precio:</strong>
                                    $<?php echo number_format($producto['precio_unitario']); ?>
                                     / <?php echo htmlspecialchars($producto['unidad_nombre']); ?></p>
                                <p class="card-text"><strong>Categoría:</strong>
                                    <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                            </div>
                            <!-- Botón EDITAR independiente -->
                            <button type="button" class="btn btn-outline-primary w-100 mt-2" onclick="editarProducto(
                                    <?php echo $producto['id_producto']; ?>,
                                    '<?php echo htmlspecialchars(addslashes($producto['nombre'])); ?>',
                                    '<?php echo htmlspecialchars(addslashes($producto['descripcion'])); ?>',
                                    '<?php echo $producto['precio_unitario']; ?>',
                                    '<?php echo $producto['id_categoria']; ?>',
                                    '<?php echo $producto['stock']; ?>'
                                )">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($productos)): ?>
                    <div class="col-12 text-center text-muted">No tienes productos publicados.</div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Modal Editar Producto -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="formEditarProducto" method="POST" action="../controller/editarproducto.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_producto" id="edit_id_producto">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="edit_categoria" name="categoria" required>
                            <option value="" disabled>Selecciona una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id_categoria']; ?>">
                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_unidad" class="form-label">Unidad de Medida</label>
                        <select class="form-select" id="edit_unidad" name="id_unidad" required>
                            <option value="" disabled>Selecciona una unidad</option>
                            <?php foreach ($medidas as $medida): ?>
                                <option value="<?php echo $medida['id_unidad']; ?>">
                                    <?php echo htmlspecialchars($medida['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="edit_precio" name="precio" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stock" class="form-label">Stock Disponible</label>
                        <input type="number" class="form-control" id="edit_stock" name="stock" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Agregar Producto -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content p-4" id="formAgregarProducto" method="POST"
                action="../controller/productcontroller.php" enctype="multipart/form-data">
                <h2 class="text-center mb-4">Añadir Nuevo Producto</h2>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="precio_unitario" class="form-label">Precio Unitario</label>
                    <input type="number" step="0.01" class="form-control" id="precio_unitario" name="precio_unitario"
                        required>
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
                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Añadir Producto</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarProducto(id_producto, nombre, descripcion, precio_unitario, id_categoria, stock, id_unidad) {
            document.getElementById('edit_id_producto').value = id_producto;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_precio').value = precio_unitario;
            document.getElementById('edit_categoria').value = id_categoria;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_unidad').value = id_unidad;

            let modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            modal.show();
        }

        document.getElementById('btnEliminar').addEventListener('click', function () {
            const checkboxes = document.querySelectorAll('input[name="productos_a_eliminar[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Selecciona al menos un producto para eliminar.');
                return;
            }
            if (confirm('¿Estás seguro de que deseas eliminar los productos seleccionados?')) {
                document.getElementById('formEliminarProductos').submit();
            }
        });
    </script>
</body>

</html>