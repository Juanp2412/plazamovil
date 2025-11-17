<?php
class PerfilModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerUsuarioPorId($id_usuario) {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id_usuario = ?');
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
