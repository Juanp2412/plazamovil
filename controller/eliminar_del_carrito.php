<?php
session_start();
require_once '../config/conexion.php';
require_once '../model/detalle_carrito_model.php';

if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: ../view/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_detalle'])) {
    $id_detalle = $_POST['id_detalle'];

    $detalleModel = new DetalleCarritoModel($pdo);
    $detalleModel->eliminarProducto($id_detalle);
}

header("Location: ../view/carritoview.php");
exit;
