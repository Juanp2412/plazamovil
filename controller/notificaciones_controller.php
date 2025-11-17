<?php
//require_once __DIR__ . '/../config/conexion.php';

class NotificacionesController {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function obtenerNotificaciones($userId) {
        $stmt = $this->pdo->prepare("SELECT mensaje FROM notificaciones WHERE usuario_id = ? AND leida = 0");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []; // Devuelve un array vacío si no hay resultados
    }
}

// Inicia la sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
$notificaciones = [];

if ($userId) {
    $notificacionesController = new NotificacionesController();
    $notificaciones = $notificacionesController->obtenerNotificaciones($userId);
}