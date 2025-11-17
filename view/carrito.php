<?php
require_once '../config/conexion.php';
session_start();

// Verificar si hay usuario logueado
if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['user_id_usuario'];

// 1. Buscar el último pedido pendiente del usuario
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_usuario = ? AND estado = 'pendiente' ORDER BY fecha DESC LIMIT 1");
$stmt->execute([$id_usuario]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

$id_pedido = $_GET['id_pedido'] ?? null;
if (!$id_pedido) {
    die("No se recibió un pedido válido.");
}


$id_pedido = $pedido['id_pedido'];

// 2. Traer los productos del pedido
$stmt = $pdo->prepare("
    SELECT pd.*, p.nombre, p.precio_unitario 
    FROM pedido_detalle pd
    JOIN productos p ON pd.id_producto = p.id_producto
    WHERE pd.id_pedido = ?
");
$stmt->execute([$id_pedido]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Calcular total
$total = 0;
foreach ($productos as $prod) {
    $total += $prod['cantidad'] * $prod['precio_unitario'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Checkout - Plaza Móvil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/Plaza-M-vil-3.1/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            if (params.get('pago') === 'exitoso') {
                alert('¡Pago realizado exitosamente!');
            } else if (params.get('pago') === 'fallido') {
                alert('El pago no se pudo efectuar. Intenta nuevamente.');
            }
        });
    </script>
</head>

<body>
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <h2 class="text-center mb-4 text-success">Confirmar Pedido</h2>

                    <h5>Resumen del Pedido (ID: <?= $id_pedido ?>)</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $prod): ?>
                                <tr>
                                    <td><?= htmlspecialchars($prod['nombre']) ?></td>
                                    <td><?= $prod['cantidad'] ?></td>
                                    <td>$<?= number_format($prod['precio_unitario'], 2) ?></td>
                                    <td>$<?= number_format($prod['cantidad'] * $prod['precio_unitario'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h4 class="text-end text-success">Total: $<?= number_format($total, 2) ?></h4>

                    <form action="../controller/procesar_pago.php" method="POST">
                        <!-- Enviar solo id_pedido (oculto) -->
                        <input type="hidden" name="id_pedido" value="<?= $id_pedido ?>">

                        <div class="mb-3">
                            <label class="form-label">Método de Pago</label>
                            <div class="d-flex justify-content-center">
                                <label class="me-3">
                                    <input type="radio" name="metodo" value="Tarjeta" required> Tarjeta
                                </label>
                                <label class="me-3">
                                    <input type="radio" name="metodo" value="PSE" required> PSE
                                </label>
                                <label>
                                    <input type="radio" name="metodo" value="Nequi" required> Nequi
                                </label>
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-credit-card"></i> Pagar Ahora
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        <p>&copy; <?= date('Y') ?> Plaza Móvil. Todos los derechos reservados.</p>
    </footer>
</body>

</html>