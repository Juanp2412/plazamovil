<?php
require_once __DIR__.'/../config/conexion.php';

class DetalleCarritoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Agregar un producto al carrito
    public function agregarProducto($id_carrito, $id_producto, $cantidad = 1) {
        // Si ya existe, sumamos cantidad
        $sql = "SELECT * FROM carrito_detalle WHERE id_carrito = ? AND id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_carrito, $id_producto]);
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($detalle) {
            $sql = "UPDATE carrito_detalle SET cantidad = cantidad + ? WHERE id_detalle = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cantidad, $detalle['id_detalle']]);
        } else {
            $sql = "INSERT INTO carrito_detalle (id_carrito, id_producto, cantidad) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_carrito, $id_producto, $cantidad]);
        }
    }

    // Obtener productos del carrito
    public function obtenerProductos($id_carrito) {
        $sql = "SELECT dc.*, p.nombre, p.descripcion, p.precio_unitario, p.foto 
                FROM carrito_detalle dc 
                JOIN productos p ON dc.id_producto = p.id_producto 
                WHERE dc.id_carrito = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_carrito]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function actualizarCantidad($id_detalle, $cantidad) {
        $sql = "UPDATE carrito_detalle SET cantidad = ? WHERE id_detalle = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$cantidad, $id_detalle]);
    }

    // Eliminar producto del carrito
    public function eliminarProducto($id_detalle) {
        $sql = "DELETE FROM carrito_detalle WHERE id_detalle = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_detalle]);
    }

    public function contarProductosUnicos($id_carrito) {
        $sql = "SELECT COUNT(*) as total FROM carrito_detalle WHERE id_carrito = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_carrito]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? intval($row['total']) : 0;
    }

    public function vaciarCarrito($id_carrito) {
        $sql = "DELETE FROM carrito_detalle WHERE id_carrito = :id_carrito";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

