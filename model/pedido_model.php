<?php
require_once '../config/conexion.php';

class PedidoModel {
    public static function obtenerPedido($id_pedido) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM pedidos WHERE id_pedido = ?');
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerProductos($id_pedido) {
        global $pdo;
        $stmt = $pdo->prepare('
            SELECT p.nombre, dp.cantidad, dp.precio_unitario, dp.id_pedido_detalle
            FROM pedido_detalle dp
            INNER JOIN productos p ON dp.id_producto = p.id_producto
            WHERE dp.id_pedido = ?
        ');
        $stmt->execute([$id_pedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actualizarEstado($id_pedido, $estado) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE pedidos SET estado = ? WHERE id_pedido = ?');
        return $stmt->execute([$estado, $id_pedido]);
    }

    public static function eliminarPedido($id_pedido) {
        global $pdo;
        // Elimina detalles primero por integridad referencial
        $stmt = $pdo->prepare('DELETE FROM pedido_detalle WHERE id_pedido = ?');
        $stmt->execute([$id_pedido]);
        // Elimina el pedido
        $stmt2 = $pdo->prepare('DELETE FROM pedidos WHERE id_pedido = ?');
        return $stmt2->execute([$id_pedido]);
    }
}
?>
