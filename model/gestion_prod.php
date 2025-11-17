<?php
// filepath: c:\xampp\htdocs\Plaza_Movil\model\gestion_prod.php
require_once '../config/conexion.php';

class ProductosModel
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }
    public function obtenerProductos()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
            p.id_producto,
            p.nombre,
            p.descripcion,
            p.precio_unitario,
            p.stock,
            p.foto,
            p.fecha_publicacion,
            c.nombre AS categoria,
            u.nombre_completo AS agricultor,
            m.nombre AS unidades_de_medida
        FROM productos p
        LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
        LEFT JOIN unidades_de_medida m ON p.id_unidad = m.id_unidad
        LEFT JOIN agricultor a ON p.id_agricultor = a.id_agricultor
        LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario;
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarProducto($nombre, $descripcion, $precio_unitario, $foto, $id_agricultor, $stock, $id_categoria, $id_unidad, $fecha_publicacion)
    {
        $stmt = $this->pdo->prepare("INSERT INTO productos (nombre, descripcion, precio_unitario, foto, id_agricultor, stock, id_categoria, id_unidad, fecha_publicacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([$nombre, $descripcion, $precio_unitario, $foto, $id_agricultor, $stock, $id_categoria, $id_unidad, $fecha_publicacion]);
    }

    public function actualizarProducto($id_producto, $nombre, $descripcion, $precio_unitario, $foto, $stock, $id_categoria, $id_unidad, $fecha_publicacion)
    {
        $stmt = $this->pdo->prepare("
            UPDATE productos 
            SET nombre = ?, 
                descripcion = ?, 
                precio_unitario = ?, 
                foto = ?, 
                stock = ?, 
                id_categoria = ?, 
                id_unidad = ?, 
                fecha_publicacion = ? 
            WHERE id_producto = ?
        ");

        return $stmt->execute([
            $nombre, 
            $descripcion, 
            $precio_unitario, 
            $foto, 
            $stock, 
            $id_categoria, 
            $id_unidad, 
            $fecha_publicacion, 
            $id_producto
        ]);
    }


    public function eliminarProducto($id)
    {
        // Primero eliminamos los detalles del carrito asociados
        $stmt = $this->pdo->prepare("DELETE FROM carrito_detalle WHERE id_producto = ?");
        $stmt->execute([$id]);

        // Luego eliminamos el producto
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
        return $stmt->execute([$id]);
    }
    
}