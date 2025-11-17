<?php
session_start();
require_once '../config/conexion.php';
require_once '../model/detalle_carrito_model.php';

if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: ../view/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_detalle = $_POST['id_detalle'];
    $cantidad = max(1, intval($_POST['cantidad']));

    $detalleModel = new DetalleCarritoModel($pdo);
    $detalleModel->actualizarCantidad($id_detalle, $cantidad);
}

header("Location: ../view/carritoview.php");
exit;
