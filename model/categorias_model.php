<?php
// filepath: c:\xampp\htdocs\Plaza_Movil\model\categorias_model.php
require_once '../config/conexion.php';

class CategoriasModel
{
    private $pdo;

    // Recibe el objeto PDO
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Lista TODAS las categorías
    public function obtenerCategorias(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id_categoria, nombre, descripcion
             FROM categoria
             ORDER BY id_categoria ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lista solo los nombres (para un <select>)
    public function listaCategorias(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id_categoria, nombre
             FROM categoria
             ORDER BY id_categoria ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        // devuelve id + nombre
    }

    public function buscarCategoria(string $nombre, string $descripcion): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_categoria, nombre, descripcion
             FROM categoria
             WHERE nombre = ? AND descripcion = ?
             LIMIT 1"
        );
        $stmt->execute([$nombre, $descripcion]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function agregarCategoria($nombre, $descripcion)
    {
        // Verificar si ya existe
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categoria WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            return false;
        }

        // Insertar
        $stmt = $this->pdo->prepare(
            "INSERT INTO categoria (nombre, descripcion) VALUES (?, ?)"
        );
        return $stmt->execute([$nombre, $descripcion]);
    }

    public function eliminarCategoria($id_categoria)
    {
        // Verificar si existen productos asociados a la categoría
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE id_categoria = ?");
        $stmt->execute([$id_categoria]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            throw new Exception("No se puede eliminar la categoría porque tiene productos asociados.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM categoria WHERE id_categoria = ?");
        return $stmt->execute([$id_categoria]);
    }

    public function actualizarCategoria($id_categoria, $nombre, $descripcion)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categoria SET nombre = ?, descripcion = ? WHERE id_categoria = ?"
        );
        return $stmt->execute([$nombre, $descripcion, $id_categoria]);
    }
}