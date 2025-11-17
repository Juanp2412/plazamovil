<?php
session_start();

require_once '../config/conexion.php';

if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Obtener los IDs de productos en el carrito
require_once '../model/carrito_model.php';
require_once '../model/detalle_carrito_model.php';

$carritoModel = new CarritoModel($pdo);
$detalleModel = new DetalleCarritoModel($pdo);

// Verificar usuario logueado
$id_usuario = $_SESSION['user_id_usuario'];

// Obtener carrito del usuario
$carrito = $carritoModel->obtenerCarritoPorUsuario($id_usuario);
$productos = [];

if ($carrito) {
    $productos = $detalleModel->obtenerProductos($carrito['id_carrito']);
}

// Calcular el total del carrito
$total = 0;
foreach ($productos as $producto) {
    $total += $producto['precio_unitario'] * $producto['cantidad'];
}

$pago = isset($_GET['pago']) ? $_GET['pago'] : null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>

    <!-- Espacio para que el contenido no quede oculto bajo la navbar fija -->
    <div style="height:70px"></div>

    <div class="cart-container">
        <?php if ($pago === 'exitoso'): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> ¡Pago realizado con éxito! Tu pedido ha sido registrado.
            </div>
        <?php elseif ($pago === 'fallido'): ?>
            <div class="alert alert-danger">
                <i class="bi bi-x-circle"></i> Hubo un problema con el pago. Intenta nuevamente.
            </div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'no_pedido'): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> No se recibió un pedido válido.
            </div>
        <?php endif; ?>
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-cart3 cart-icon"></i>
            <h2 class="ms-3 cart-title mb-0">Carrito de Compras</h2>
        </div>
        <?php if (empty($productos)): ?>
            <div class="cart-empty">
                <i class="bi bi-emoji-frown cart-empty-icon"></i>
                <p>Tu carrito está vacío.</p>
                <a href="../index.php" class="btn btn-outline-success mt-2"><i class="bi bi-arrow-left"></i> Seguir
                    comprando</a>
            </div>
        <?php else: ?>
            <?php foreach ($productos as $producto): ?>
                <div class="row align-items-center cart-product">
                    <div class="col-2">
                        <img src="../img/<?php echo htmlspecialchars($producto['foto']); ?>" class="cart-img"
                            alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    </div>
                    <div class="col-4">
                        <div class="fw-bold"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                        <div class="text-muted"><?php echo htmlspecialchars($producto['descripcion']); ?></div>
                    </div>
                    <div class="col-2">
                        <!-- Formulario para editar cantidad -->
                        <form action="../controller/editar_cantidad.php" method="POST" class="d-flex">
                            <input type="hidden" name="id_detalle" value="<?php echo $producto['id_detalle']; ?>">
                            <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" min="1"
                                class="form-control form-control-sm text-center me-2" style="width:80px;">
                            <button type="submit" class="btn btn-sm btn-outline-success">Actualizar</button>
                        </form>
                    </div>
                    <div class="col-2 text-end">
                        <span class="text-success fw-semibold">
                            $<?php echo number_format($producto['precio_unitario'] * $producto['cantidad']); ?>
                        </span>
                    </div>
                    <div class="col-2 text-end">
                        <!-- Formulario para eliminar producto -->
                        <form action="../controller/eliminar_del_carrito.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_detalle" value="<?php echo $producto['id_detalle']; ?>">
                            <button type="submit" class="btn btn-link cart-remove" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <form action="../controller/crear_pedido.php" method="POST">
                    <input type="hidden" name="id_carrito" value="<?php echo $carrito['id_carrito']; ?>">
                    <button type="submit" class="btn btn-checkout btn-lg text-white">
                        <i class="bi bi-credit-card"></i> Comprar ahora
                    </button>
                </form>

            </div>
            <a href="../index.php" class="btn btn-outline-success mt-2"><i class="bi bi-arrow-left"></i> Seguir
                comprando</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>