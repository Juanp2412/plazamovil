<?php
session_start();
if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/conexion.php';

$id_pedido = $_GET['id_pedido'] ?? null;
$estado = $_GET['estado'] ?? null;
$id_agricultor = $_SESSION['user_id_usuario'];

if (!$id_pedido || !$estado) {
    $_SESSION['error'] = 'Parámetros inválidos';
    header('Location: ../view/historial_ventas.php');
    exit();
}

// Verificar que el agricultor tiene productos en este pedido
$stmt = $pdo->prepare('
    SELECT COUNT(*) as tiene_productos 
    FROM pedido_detalle pd
    INNER JOIN productos pr ON pd.id_producto = pr.id_producto
    WHERE pd.id_pedido = ? AND pr.id_agricultor = ?
');
$stmt->execute([$id_pedido, $id_agricultor]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['tiene_productos'] > 0) {
    // Actualizar estado del pedido
    $stmt = $pdo->prepare('UPDATE pedidos SET estado = ? WHERE id_pedido = ?');
    $stmt->execute([$estado, $id_pedido]);
    
    $_SESSION['success'] = 'Estado del pedido actualizado correctamente';
} else {
    $_SESSION['error'] = 'No tienes permisos para modificar este pedido';
}

header('Location: ../view/historial_ventas.php');
exit();
?>