<?php
require_once '../config/conexion.php';

class PagoModel {
    public static function crearPago($id_pedido, $monto, $metodo, $estado, $transaccion_id, $id_cliente, $nombre_cliente) {
        global $pdo;
        // Aquí deberías obtener el id_producto del pedido si lo tienes
        $id_producto = 1; // Simulación, reemplaza por el id real

        $stmt = $pdo->prepare("INSERT INTO pagos (id_pedido, monto_total, metodo, estado, transaccion_id, id_cliente, id_producto, fecha_pago) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id_pedido, $monto, $metodo, $estado, $transaccion_id, $id_cliente, $id_producto]);
    }
}
?>
