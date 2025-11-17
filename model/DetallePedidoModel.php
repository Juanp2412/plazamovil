<?php
require_once __DIR__ . '/../config/conexion.php';

class DetallePedidoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function agregarDetalle($id_pedido, $id_producto, $cantidad, $precio_unitario, $id_unidad) {
        $sql = "INSERT INTO pedido_detalle 
                (id_pedido, id_producto, cantidad, precio_unitario, id_unidad) 
                VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario, :id_unidad)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':precio_unitario', $precio_unitario);
        $stmt->bindParam(':id_unidad', $id_unidad, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerDetallesPorPedido($id_pedido) {
        $sql = "SELECT dp.*, p.nombre AS nombre_producto 
                FROM pedido_detalle dp
                INNER JOIN productos p ON dp.id_producto = p.id_producto
                WHERE dp.id_pedido = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function vaciarCarrito($id_carrito) {
        $sql = "DELETE FROM detalle_carrito WHERE id_carrito = :id_carrito";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
