<?php
require_once '../config/conexion.php';

class PQRSModel {
    public static function obtenerPorUsuario($id_usuario) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM pqrs WHERE id_usuario = ? ORDER BY fecha DESC");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodas() {
        global $pdo;
        $stmt = $pdo->query("SELECT pqrs.*, u.nombre_completo FROM pqrs JOIN usuarios u ON pqrs.id_usuario = u.id_usuario ORDER BY fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function responder($id_pqrs, $respuesta) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE pqrs SET respuesta = ?, estado = 'respondido', fecha_respuesta = NOW() WHERE id_pqrs = ?");
        return $stmt->execute([$respuesta, $id_pqrs]);
    }
}
?>
