<?php
require_once '../config/conexion.php';

class ResenaModel {
    public static function agregarResena($id_producto, $id_usuario, $estrellas, $comentario) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO producto_resenas (id_producto, id_usuario, estrellas, comentario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_producto, $id_usuario, $estrellas, $comentario]);
    }

    public static function obtenerResenas($id_producto) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT r.*, u.nombre_completo FROM producto_resenas r JOIN usuarios u ON r.id_usuario = u.id_usuario WHERE r.id_producto = ? ORDER BY r.fecha DESC");
        $stmt->execute([$id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function promedioAgricultor($id_agricultor) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT AVG(r.estrellas) as promedio, COUNT(*) as total
            FROM producto_resenas r
            JOIN productos p ON r.id_producto = p.id_producto
            WHERE p.id_agricultor = ?
        ");
        $stmt->execute([$id_agricultor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
