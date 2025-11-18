<?php
require_once __DIR__ . '/../config/paths.php';
session_start();
require_once '../config/conexion.php';

// Asegurarse de que $id_rol sea un entero para evitar problemas de comparación estricta
$id_rol = isset($_SESSION['user_id_rol']) ? (int) $_SESSION['user_id_rol'] : null;

// Verificar si el usuario tiene el rol de administrador
if ($id_rol !== 1) {
    header("Location: ../index.php");
    exit;
}

// Obtener la lista de usuarios
$stmt = $pdo->prepare("SELECT id_usuario, nombre_completo, email, username, id_rol FROM usuarios");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si se debe mostrar el modal de éxito o eliminación
$success = isset($_GET['success']) && $_GET['success'] == 1;
$deleted = isset($_GET['deleted']) && $_GET['deleted'] == 1;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>

    <div style="height:70px"></div> <!-- Espacio para navbar fija -->

    <div class="container mt-5">
        <div class="card p-4">
            <h1 class="text-center mb-4">Gestión de Usuarios</h1>

            <!-- Botón volver -->
            <div class="text-start mb-3">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver al
                    Dashboard</a>
            </div>

            <!-- Botón crear usuario -->
            <div class="text-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#crearUsuarioModal">
                    <i class="bi bi-plus-circle"></i> Crear Usuario
                </button>
            </div>

            <!-- Modal crear usuario -->
            <div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Nuevo Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="../controller/crear_usuario.php" method="POST"
                            onsubmit="return validarFormulario(this);">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control" name="nombre_completo" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rol</label>
                                    <select name="rol" class="form-select" required>
                                        <option value="1">Administrador</option>
                                        <option value="2">Vendedor</option>
                                        <option value="3">Comprador</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal éxito -->
            <?php if ($success): ?>
                <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Usuario Creado</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">El usuario ha sido creado con éxito.</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        new bootstrap.Modal(document.getElementById('successModal')).show();
                    });
                </script>
            <?php endif; ?>

            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['id_usuario']); ?></td>
                                <td><?= htmlspecialchars($usuario['nombre_completo']); ?></td>
                                <td><?= htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <form action="../controller/editar_usuario.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario']; ?>">
                                        <select name="rol" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="1" <?= $usuario['id_rol'] == 1 ? 'selected' : ''; ?>>Administrador
                                            </option>
                                            <option value="2" <?= $usuario['id_rol'] == 2 ? 'selected' : ''; ?>>Vendedor
                                            </option>
                                            <option value="3" <?= $usuario['id_rol'] == 3 ? 'selected' : ''; ?>>Comprador
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <!-- Botón abrir modal edición -->
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editarModal<?= $usuario['id_usuario']; ?>">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>

                                    <!-- Modal edición -->
                                    <div class="modal fade" id="editarModal<?= $usuario['id_usuario']; ?>" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Usuario</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="../controller/editar_usuario.php" method="POST"
                                                    onsubmit="return validarFormularioEdicion(this);">
                                                    <input type="hidden" name="id_usuario"
                                                        value="<?= $usuario['id_usuario']; ?>">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nombre Completo</label>
                                                            <input type="text" class="form-control" name="nombre_completo"
                                                                value="<?= htmlspecialchars($usuario['nombre_completo']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email"
                                                                value="<?= htmlspecialchars($usuario['email']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nombre de Usuario</label>
                                                            <input type="text" class="form-control" name="username"
                                                                value="<?= htmlspecialchars($usuario['username']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Rol</label>
                                                            <select name="rol" class="form-select" required>
                                                                <option value="1" <?= $usuario['id_rol'] == 1 ? 'selected' : ''; ?>>Administrador</option>
                                                                <option value="2" <?= $usuario['id_rol'] == 2 ? 'selected' : ''; ?>>Vendedor</option>
                                                                <option value="3" <?= $usuario['id_rol'] == 3 ? 'selected' : ''; ?>>Comprador</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-warning">Guardar
                                                            Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón eliminar -->
                                    <form action="../controller/eliminar_usuario.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validarFormulario(form) {
            const campos = ['nombre_completo', 'email', 'password', 'username', 'rol'];
            for (const campo of campos) {
                const input = form.querySelector(`[name="${campo}"]`);
                if (!input || !input.value.trim()) {
                    alert(`El campo ${campo} es obligatorio.`);
                    input.focus();
                    return false;
                }
            }
            return true;
        }

        function validarFormularioEdicion(form) {
            const campos = ['nombre_completo', 'username', 'email', 'rol'];
            for (const campo of campos) {
                const input = form.querySelector(`[name="${campo}"]`);
                if (!input || !input.value.trim()) {
                    alert(`El campo ${campo} es obligatorio.`);
                    input.focus();
                    return false;
                }
            }
            return true;
        }

        <?php if (isset($_GET['error']) && $_GET['error'] === 'campos'): ?>
                < div class="alert alert-danger" > Todos los campos son obligatorios.</div >
                <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === '1'): ?>
                < div class="alert alert-danger" > Error al actualizar el usuario.Intenta de nuevo.</div >
                <?php endif; ?>
    </script>
</body>

</html>