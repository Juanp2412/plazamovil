<?php
session_start();
if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: login.php');
    exit();
}
require_once '../config/conexion.php';
$user_id_usuario = $_SESSION['user_id_usuario'];

// Consulta pedidos
$stmt = $pdo->prepare('SELECT id_pedido, fecha, estado FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC');
$stmt->execute([$user_id_usuario]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/Plaza-M-vil-3.1/css/styles.css">
    
</head>

<body>
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Historial de Pedidos</h2>
        <?php if (count($pedidos) === 0): ?>
            <div class="alert alert-info text-center">No tienes pedidos registrados.</div>
        <?php else: ?>
            <?php foreach ($pedidos as $pedido): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>ID Pedido:</strong> <?php echo htmlspecialchars($pedido['id_pedido']); ?> |
                        <strong>Fecha:</strong> <?php echo htmlspecialchars($pedido['fecha']); ?> |
                        <strong>Estado:</strong> <?php echo htmlspecialchars($pedido['estado']); ?>
                    </div>
                    <div class="card-body">
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

                        // Consulta pago relacionado (mejorada: busca el pago más reciente del pedido)
                        $stmtPago = $pdo->prepare('SELECT id_pago FROM pagos WHERE id_pedido = ? ORDER BY fecha_pago DESC LIMIT 1');
                        $stmtPago->execute([$pedido['id_pedido']]);
                        $pago = $stmtPago->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (count($productos) === 0): ?>
                            <div class="alert alert-warning">No hay productos en este pedido.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
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
                        <div class="mt-3 d-flex gap-2">
                            <?php if ($pago && !empty($pago['id_pago'])): ?>
                                <a href="../controller/generar_factura.php?id_pago=<?php echo $pago['id_pago']; ?>"
                                    class="btn btn-primary btn-sm" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Comprobante PDF
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="bi bi-file-earmark-pdf"></i> Sin comprobante
                                </button>
                            <?php endif; ?>
                            <!-- CRUD: Editar y Eliminar 
                            <a href="../view/editar_pedido.php?id_pedido=<?php echo $pedido['id_pedido']; ?>"
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
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="mt-3 text-center">
            <a href="perfil.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver al Perfil</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>