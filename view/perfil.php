<?php
require_once __DIR__ . '/../config/paths.php';
session_start();
if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: login.php');
    exit();
}
require_once '../config/conexion.php';
$user_id_usuario = $_SESSION['user_id_usuario'];
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id_usuario = ?');
$stmt->execute([$user_id_usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo '<div class="alert alert-danger">Usuario no encontrado.</div>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <style>
        body {
            background: linear-gradient(180deg, #e8f5e9 0%, #ffffff 100%);
            min-height: 100vh;
        }

        .card {
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.09);
            border-radius: 18px;
            border: none;
        }

        .profile-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 12px rgba(44, 62, 80, 0.10);
            border: 3px solid #a5d6a7;
        }

        .list-group-item {
            border: none;
            background: #f9fbe7;
            border-radius: 8px;
            margin-bottom: 6px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #388e3c;
            margin-bottom: 1rem;
            letter-spacing: 0.5px;
        }

        .section-divider {
            border: none;
            border-top: 2px solid #c8e6c9;
            margin: 2rem 0 1.5rem 0;
        }

        .historial-card,
        .pqrs-card {
            background: #f1f8e9;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.07);
            padding: 1rem 1.2rem;
            margin-bottom: 1rem;
        }

        .historial-icon,
        .pqrs-icon {
            font-size: 1.5rem;
            color: #43a047;
            margin-right: 8px;
        }

        .btn-group-perfil .btn {
            min-width: 140px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>

    <div class="container mt-5">
        <div class="card p-4">
            <div class="text-center">
                <img src="<?php echo !empty($user['Foto']) ? '../img/' . htmlspecialchars($user['Foto']) : '../img/default_profile.png'; ?>"
                    alt="Foto de perfil" class="profile-img mb-3">
                <h2><?php echo htmlspecialchars($user['nombre_completo']); ?></h2>
                <p class="text-muted">Usuario: <?php echo htmlspecialchars($user['username']); ?></p>
                <p class="text-muted">Rol: <?php echo htmlspecialchars($user['id_rol']); ?></p>
            </div>

            <div class="mt-4">
                <div class="section-title"><i class="bi bi-person-lines-fill me-2"></i>Información Personal</div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                    <li class="list-group-item"><strong>Teléfono:</strong> <?php echo htmlspecialchars($user['telefono'] ?? 'No disponible'); ?></li>
                    <li class="list-group-item"><strong>Dirección:</strong> <?php echo htmlspecialchars($user['direccion'] ?? 'No disponible'); ?></li>
                </ul>
            </div>

            <div class="mt-4 btn-group-perfil text-center d-flex flex-wrap justify-content-center gap-2">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                    <i class="bi bi-pencil"></i> Editar Perfil
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarPerfil">
                    <i class="bi bi-trash"></i> Eliminar Perfil
                </button>
            </div>

            <hr class="section-divider">

            <!-- Historial de pedidos -->
            <div class="mt-4">
                <div class="section-title"><i class="bi bi-clock-history historial-icon"></i>Historial de Pedidos</div>
                <?php
                // Consulta de historial de pedidos
                $stmtPedidos = $pdo->prepare("SELECT id_pedido, fecha, estado FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC LIMIT 5");
                $stmtPedidos->execute([$user_id_usuario]);
                $pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);

                if ($pedidos && count($pedidos) > 0) {
                    foreach ($pedidos as $pedido) {
                        ?>
                        <div class="historial-card mb-4">
                            <div class="mb-2">
                                <strong>ID Pedido:</strong> <?php echo htmlspecialchars($pedido['id_pedido']); ?> |
                                <strong>Fecha:</strong> <?php echo htmlspecialchars($pedido['fecha']); ?> |
                                <strong>Estado:</strong> <?php echo htmlspecialchars($pedido['estado']); ?>
                            </div>
                            <?php
                            // Consulta productos del pedido
                            $stmtProd = $pdo->prepare('
                                SELECT p.nombre, dp.cantidad, dp.precio_unitario
                                FROM pedido_detalle dp
                                INNER JOIN productos p ON dp.id_producto = p.id_producto
                                WHERE dp.id_pedido = ?
                            ');
                            $stmtProd->execute([$pedido['id_pedido']]);
                            $productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

                            // Consulta pagos relacionados
                            $stmtPagos = $pdo->prepare('SELECT id_pago, monto, metodo, estado, fecha_pago FROM pagos WHERE id_pedido = ? ORDER BY fecha_pago DESC');
                            $stmtPagos->execute([$pedido['id_pedido']]);
                            $pagos = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <?php if (count($productos) === 0): ?>
                                <div class="alert alert-warning">No hay productos en este pedido.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-2">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productos as $prod): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                                    <td><?php echo htmlspecialchars($prod['cantidad']); ?></td>
                                                    <td>$<?php echo number_format($prod['precio_unitario'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Pagos realizados -->
                            <?php if ($pagos && count($pagos) > 0): ?>
                                <div class="table-responsive mb-2">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-success">
                                            <tr>
                                                <th>ID Pago</th>
                                                <th>Monto</th>
                                                <th>Método</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                                <th>Comprobante</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pagos as $pago): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($pago['id_pago']); ?></td>
                                                    <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                                                    <td><?php echo htmlspecialchars($pago['metodo']); ?></td>
                                                    <td><?php echo htmlspecialchars($pago['estado']); ?></td>
                                                    <td><?php echo htmlspecialchars($pago['fecha_pago']); ?></td>
                                                    <td>
                                                        <a href="../controller/generar_factura.php?id_pago=<?php echo $pago['id_pago']; ?>"
                                                            class="btn btn-primary btn-sm" target="_blank">
                                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">No hay pagos registrados para este pedido.</div>
                            <?php endif; ?>

                            <div class="mt-2 d-flex gap-2">
                                 <!--<a href="../view/editar_pedido.php?id_pedido=<?php echo $pedido['id_pedido']; ?>"
                                    class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>-->
                                <form action="../controller/eliminar_pedido.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que deseas eliminar este pedido?');">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="historial-card text-muted text-center"><i class="bi bi-info-circle"></i> No tienes pedidos recientes.</div>';
                }
                ?>
            </div>

            <hr class="section-divider">

            <!-- Mis PQRS -->
            <div class="mt-4">
                <div class="section-title"><i class="bi bi-chat-dots pqrs-icon"></i>Mis PQRS</div>
                <?php
                // Consulta de PQRS (incluye respuesta)
                $stmtPQRS = $pdo->prepare("SELECT asunto, estado, fecha, respuesta FROM pqrs WHERE id_usuario = ? ORDER BY fecha DESC LIMIT 5");
                $stmtPQRS->execute([$user_id_usuario]);
                $pqrs = $stmtPQRS->fetchAll(PDO::FETCH_ASSOC);

                if ($pqrs && count($pqrs) > 0) {
                    foreach ($pqrs as $pq) {
                        ?>
                        <div class="pqrs-card">
                            <div><strong><?php echo htmlspecialchars($pq['asunto']); ?></strong></div>
                            <div class="text-muted small">Fecha: <?php echo htmlspecialchars($pq['fecha']); ?> | Estado: <?php echo htmlspecialchars($pq['estado']); ?></div>
                            <?php if (!empty($pq['respuesta'])): ?>
                                <div class="mt-2">
                                    <span class="fw-bold text-success"><i class="bi bi-arrow-return-right"></i> Respuesta:</span>
                                    <div class="bg-white rounded p-2 border mt-1"><?php echo htmlspecialchars($pq['respuesta']); ?></div>
                                </div>
                            <?php else: ?>
                                <div class="mt-2 text-muted"><i class="bi bi-hourglass-split"></i> Sin respuesta aún.</div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="pqrs-card text-muted text-center"><i class="bi bi-info-circle"></i> No tienes PQRS recientes.</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Modal para editar perfil -->
    <div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="formEditarPerfil" method="POST"
                action="../controller/editarperfilcontroller.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPerfilLabel">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($user['id_usuario']); ?>">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?php echo htmlspecialchars($user['nombre_completo']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo"
                            value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario"
                            value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono"
                            value="<?php echo htmlspecialchars($user['telefono']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="foto_perfil" class="form-label">Foto de Perfil</label>
                        <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para eliminar perfil -->
    <div class="modal fade" id="modalEliminarPerfil" tabindex="-1" aria-labelledby="modalEliminarPerfilLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="../controller/eliminarperfilcontroller.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminarPerfilLabel">Confirmar Eliminación de Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($user['id_usuario']); ?>">
                    <p>¿Estás seguro de que deseas eliminar tu perfil? Esta acción no se puede deshacer.</p>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Ingresa tu contraseña para confirmar:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Diagnóstico: muestra errores de JS y verifica el botón de la navbar
        document.addEventListener('DOMContentLoaded', function() {
            var toggler = document.querySelector('.navbar-toggler');
            if (!toggler) {
                console.error('No se encontró el botón navbar-toggler');
            } else {
                toggler.addEventListener('click', function() {
                    console.log('Botón de menú clickeado');
                });
            }
        });
    </script>
</body>
</html>