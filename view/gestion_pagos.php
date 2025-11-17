<?php
require_once '../config/conexion.php';
session_start();

$stmt = $pdo->query("
    SELECT 
        p.id_pago, p.fecha_pago, p.monto_total, p.metodo,
        u.nombre_completo AS cliente,
        agri_user.nombre_completo AS agricultor,
        prod.nombre AS producto
    FROM pagos p
    JOIN usuarios u ON u.id_usuario = p.id_usuario
    JOIN productos prod ON prod.id_producto = p.id_producto
    JOIN agricultor agri ON agri.id_agricultor = prod.id_agricultor
    JOIN usuarios agri_user ON agri_user.id_usuario = agri.id_usuario
    ORDER BY p.fecha_pago DESC
");
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pagos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Gestión de Pagos</h2>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Pago registrado correctamente</div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'pago_fallido'): ?>
            <div class="alert alert-danger">El pago ha fallado.</div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'no_pedido'): ?>
            <div class="alert alert-warning">No se recibió un pedido válido.</div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pago</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Agricultor</th>
                    <th>Producto</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Factura</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $pago): ?>
                <tr>
                    <td><?= $pago['id_pago'] ?></td>
                    <td><?= $pago['fecha_pago'] ?></td>
                    <td><?= htmlspecialchars($pago['cliente']) ?></td>
                    <td><?= htmlspecialchars($pago['agricultor']) ?></td>
                    <td><?= htmlspecialchars($pago['producto']) ?></td>
                    <td>$<?= number_format($pago['monto_total'], 2) ?></td>
                    <td><?= htmlspecialchars($pago['metodo']) ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary" 
                           href="../controller/generar_factura.php?id_pago=<?= $pago['id_pago'] ?>" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Comprobante PDF
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>