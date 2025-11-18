<?php
require_once __DIR__ . '/../config/paths.php';
session_start();
require_once '../config/conexion.php';

// Validar sesión
if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Verificar que venga un id_pedido en la URL
if (!isset($_GET['id_pedido'])) {
    header("Location: carrito.php?error=no_pedido");
    exit;
}

$id_pedido = intval($_GET['id_pedido']);
$id_usuario = $_SESSION['user_id_usuario'];

// Obtener pedido
$stmt = $pdo->prepare("
    SELECT p.id_pedido, p.fecha, p.estado, u.nombre_completo AS comprador
    FROM pedidos p
    INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
    WHERE p.id_pedido = ? AND p.id_usuario = ?
");
$stmt->execute([$id_pedido, $id_usuario]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: carrito.php?error=no_pedido");
    exit;
}

// Obtener detalles del pedido
$stmtDetalles = $pdo->prepare("
    SELECT d.cantidad, d.precio_unitario, pr.nombre, pr.foto, u.nombre_completo AS agricultor
    FROM pedido_detalle d
    INNER JOIN productos pr ON d.id_producto = pr.id_producto
    INNER JOIN agricultor a ON pr.id_agricultor = a.id_agricultor
    INNER JOIN usuarios u ON a.id_usuario = u.id_usuario
    WHERE d.id_pedido = ?
");
$stmtDetalles->execute([$id_pedido]);
$detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

// Calcular total
$total = 0;
foreach ($detalles as $d) {
    $total += $d['precio_unitario'] * $d['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pago del Pedido #<?php echo $id_pedido; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    
</head>

<body>
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>

    <div class="container mt-5">
        <h2 class="mb-4">Pago del Pedido #<?php echo $pedido['id_pedido']; ?></h2>

        <p><strong>Comprador:</strong> <?php echo htmlspecialchars($pedido['comprador']); ?></p>
        <p><strong>Fecha:</strong> <?php echo $pedido['fecha']; ?></p>
        <p><strong>Estado:</strong> <?php echo ucfirst($pedido['estado']); ?></p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Imagen</th>
                    <th>Agricultor</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $d): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d['nombre']); ?></td>
                        <td>
                            <img src="../img/<?php echo htmlspecialchars($d['foto']); ?>" width="60" height="60"
                                style="object-fit:cover;">
                        </td>
                        <td><?php echo htmlspecialchars($d['agricultor']); ?></td>
                        <td><?php echo $d['cantidad']; ?></td>
                        <td>$<?php echo number_format($d['precio_unitario']); ?></td>
                        <td>$<?php echo number_format($d['precio_unitario'] * $d['cantidad']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            <h4>Total: <span class="text-success">$<?php echo number_format($total); ?></span></h4>
        </div>

        <form action="../controller/gestion_pagos.php" method="POST" class="mt-4">
            <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
            <input type="hidden" name="monto" value="<?php echo $total; ?>">
            <button type="submit" class="btn btn-success btn-lg w-100">
                <i class="bi bi-credit-card"></i> Confirmar Pago
            </button>
        </form>

        <a href="carrito.php" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver al carrito
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>