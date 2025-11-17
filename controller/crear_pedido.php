<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: ../view/login.php");
    exit;
}

$id_usuario = $_SESSION['user_id_usuario'];
$id_carrito = $_POST['id_carrito'] ?? null;

if (!$id_carrito) {
    header("Location: ../view/carrito.php?error=no_pedido");
    exit;
}

// 1. Crear el pedido
$stmt = $pdo->prepare("INSERT INTO pedidos (id_usuario, fecha, estado) VALUES (?, NOW(), 'pendiente')");
$stmt->execute([$id_usuario]);
$id_pedido = $pdo->lastInsertId();

// 2. Pasar los productos del carrito al pedido
$stmtDetalles = $pdo->prepare("
    SELECT cd.id_producto, cd.cantidad, p.precio_unitario, p.id_unidad
    FROM carrito_detalle cd
    INNER JOIN productos p ON cd.id_producto = p.id_producto
    WHERE cd.id_carrito = ?
");
$stmtDetalles->execute([$id_carrito]);
$detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

if ($detalles) {
    $stmtInsert = $pdo->prepare("
        INSERT INTO pedido_detalle (id_pedido, id_producto, cantidad, precio_unitario, id_unidad)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($detalles as $d) {
        $stmtInsert->execute([
            $id_pedido,
            $d['id_producto'],
            $d['cantidad'],
            $d['precio_unitario'],
            $d['id_unidad']
        ]);
    }
}

// 3. Redirigir a pago.php con el ID del pedido
header("Location: ../view/pago.php?id_pedido=$id_pedido");
exit;
?>
