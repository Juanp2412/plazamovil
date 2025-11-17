<?php
require_once __DIR__ . '/../config/conexion.php';

class PedidoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function crearPedido($id_usuario, $estado = 'pendiente') {
        $sql = "INSERT INTO pedidos (id_usuario, fecha, estado) 
                VALUES (:id_usuario, NOW(), :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId(); // ID del pedido creado
    }

    public function obtenerPedidoPorId($id_pedido) {
        $sql = "SELECT * FROM pedidos WHERE id_pedido = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

