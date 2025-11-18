<?php
require_once __DIR__ . '/../config/paths.php';
// filepath: c:\xampp\htdocs\Plaza_Movil\view\gestion_categorias.php
session_start();
require_once '../controller/gestion_categorias.php';
require_once '../model/categorias_model.php';

// Asegurarse de que $id_rol sea un entero para evitar problemas de comparación estricta
$id_rol = isset($_SESSION['user_id_rol']) ? (int) $_SESSION['user_id_rol'] : null;

// Verificar si el usuario tiene el rol de administrador
if ($id_rol !== 1) {
    header("Location: ../index.php");
    exit;
}

$model = new CategoriasModel($pdo);
$categorias = $model->obtenerCategorias();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>

    <!-- Espacio para que el contenido no quede oculto bajo la navbar fija -->
    <div style="height:70px"></div>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestión de Categorías</h1>

        <!-- Formulario para agregar una nueva categoría -->
        <div class="mb-4">
            <form action="../controller/gestion_categorias.php" method="POST" class="row g-2">
                <input type="hidden" name="accion" value="agregar">
                <div class="col-md-4">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la categoría"
                        value="<?= htmlspecialchars($_POST['nombre'] ?? '', ENT_QUOTES); ?>" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción"
                        value="<?= htmlspecialchars($_POST['descripcion'] ?? '', ENT_QUOTES); ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Agregar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de categorías -->
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= htmlspecialchars($categoria['id_categoria']); ?></td>
                        <td>
                            <?= !empty($categoria['nombre']) ? htmlspecialchars($categoria['nombre']) : '<span class="text-danger">Sin nombre</span>'; ?>
                        </td>
                        <td>
                            <?= !empty($categoria['descripcion']) ? htmlspecialchars($categoria['descripcion']) : '<span class="text-danger">Sin descripción</span>'; ?>
                        </td>
                        <td class="text-center">
                            <!-- Botón editar (abre modal) -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editarModal<?= $categoria['id_categoria']; ?>">
                                <i class="bi bi-pencil"></i> Editar
                            </button>

                            <!-- Formulario eliminar -->
                            <form action="../controller/gestion_categorias.php" method="POST" class="d-inline">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal editar -->
                    <div class="modal fade" id="editarModal<?= $categoria['id_categoria']; ?>" tabindex="-1"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../controller/gestion_categorias.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Categoría</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="accion" value="actualizar">
                                        <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria']; ?>">

                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control"
                                                value="<?= isset($categoria['nombre']) ? htmlspecialchars($categoria['nombre']) : ''; ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="descripcion" class="form-control"
                                                value="<?= isset($categoria['descripcion']) ? htmlspecialchars($categoria['descripcion']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>