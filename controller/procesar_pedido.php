<?php
session_start();
require_once '../config/conexion.php'; // aquí creas el objeto PDO
require_once '../model/PedidoModel.php';
require_once '../model/DetallePedidoModel.php';
require_once '../model/productModel.php';


if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['user_id_usuario'];

// Instanciar modelos
$pedidoModel = new PedidoModel($conexion);
$detallePedidoModel = new DetallePedidoModel($conexion);
$productoModel = new ProductModel($conexion);

// 1. Crear pedido
$id_pedido = $pedidoModel->crearPedido($id_usuario, 'pendiente');

// 2. Recorrer carrito y agregar detalles
if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $id_producto = $item['id_producto'];
        $cantidad = $item['cantidad'];

        // Obtener precio y unidad del producto
        $producto = $productoModel->obtenerProductoPorId($id_producto);
        $precio_unitario = $producto['precio'];
        $id_unidad = $producto['id_unidad'];

        // Insertar en detalle_pedido
        $detallePedidoModel->agregarDetalle(
            $id_pedido,
            $id_producto,
            $cantidad,
            $precio_unitario,
            $id_unidad
        );
    }

    // Vaciar carrito después de generar pedido
    unset($_SESSION['carrito']);
}

// 3. Redirigir a pago.php
header("Location: ../view/pago.php?id_pedido=" . $id_pedido);
exit();
