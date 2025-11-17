<?php
session_start();
require_once '../config/conexion.php';
require_once '../model/carrito_model.php';
require_once '../model/detalle_carrito_model.php';

$carritoModel = new CarritoModel($pdo);
$detalleModel = new DetalleCarritoModel($pdo);

// Verificar si usuario está logueado
if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: ../view/login.php");
    exit;
}

$id_usuario = $_SESSION['user_id_usuario'];

// Obtener o crear carrito
$carrito = $carritoModel->obtenerCarritoPorUsuario($id_usuario);
if (!$carrito) {
    $id_carrito = $carritoModel->crearCarrito($id_usuario);
} else {
    $id_carrito = $carrito['id_carrito'];
}

// Acción: agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    $id_producto = $_POST['id_producto'];
    $detalleModel->agregarProducto($id_carrito, $id_producto, 1);

    header("Location: ../view/carritoview.php");
    exit;
}
