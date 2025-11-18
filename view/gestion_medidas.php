<?php
require_once __DIR__ . '/../config/paths.php';
// filepath: c:\xampp\htdocs\Plaza_Movil\view\gestion_medidas.php
include '../navbar.php';
require_once '../controller/medidas_controller.php';

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar rol administrador (asumiendo rol 1 = admin)
$id_rol = isset($_SESSION['user_id_rol']) ? (int) $_SESSION['user_id_rol'] : null;
if ($id_rol !== 1) {
    header("Location: ../index.php");
    exit;
}

ob_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Unidades de Medida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    
</head>

<body>
    <!-- Espacio para la navbar -->
    <div style="height:70px"></div>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestión de Unidades de Medida</h1>

        <!-- Botón para abrir modal de creación -->
        <div class="text-end mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearMedidaModal">
                <i class="bi bi-plus-circle"></i> Crear Unidad
            </button>
        </div>

        <!-- Tabla de medidas -->
        <table class="table table-bordered table-hover">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medidas as $medida): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($medida['id_unidad']); ?></td>
                        <td><?php echo htmlspecialchars($medida['nombre']); ?></td>
                        <td>
                            <!-- Botón para abrir modal de edición -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editarMedidaModal<?php echo $medida['id_unidad']; ?>">
                                <i class="bi bi-pencil"></i> Editar
                            </button>

                            <!-- Modal de edición -->
                            <div class="modal fade" id="editarMedidaModal<?php echo $medida['id_unidad']; ?>" tabindex="-1"
                                aria-labelledby="editarMedidaModalLabel<?php echo $medida['id_unidad']; ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="../controller/medidas_controller.php" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="editarMedidaModalLabel<?php echo $medida['id_unidad']; ?>">
                                                    Editar Unidad
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="accion" value="editar">
                                                <input type="hidden" name="id_unidad"
                                                    value="<?php echo $medida['id_unidad']; ?>">
                                                <div class="mb-3">
                                                    <label for="nombre" class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" name="nombre"
                                                        value="<?php echo htmlspecialchars($medida['nombre']); ?>" required>
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

                            <!-- Botón eliminar -->
                            <form action="../controller/medidas_controller.php" method="POST" class="d-inline">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_unidad" value="<?php echo $medida['id_unidad']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar esta medida?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal crear medida -->
    <div class="modal fade" id="crearMedidaModal" tabindex="-1" aria-labelledby="crearMedidaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../controller/medidas_controller.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearMedidaModalLabel">Crear Nueva Unidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>