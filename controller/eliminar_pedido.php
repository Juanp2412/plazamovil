<?php
session_start();
if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: ../view/login.php');
    exit();
}
require_once '../model/pedido_model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'])) {
    $id_pedido = $_POST['id_pedido'];
    PedidoModel::eliminarPedido($id_pedido);
    header("Location: ../view/perfil.php");
    exit;
} else {
    echo "Petición inválida.";
}
?>
