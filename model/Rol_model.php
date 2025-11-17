<?php
class Rol_Model {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todos los roles
    public function ConsultarRoles() {
        $stmt = $this->pdo->query("SELECT * FROM rol");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un rol por ID
    public function ConsultarRol($id_rol) {
        $stmt = $this->pdo->prepare("SELECT * FROM rol WHERE id_rol = ?");
        $stmt->execute([$id_rol]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo rol
    public function CrearRol($nombre) {
        $stmt = $this->pdo->prepare("INSERT INTO rol (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    // Actualizar un rol
    public function EditarRol($id_rol, $nombre) {
        $stmt = $this->pdo->prepare("UPDATE rol SET nombre = ? WHERE id_rol = ?");
        return $stmt->execute([$nombre, $id_rol]);
    }

    // Eliminar un rol
    public function EliminarRol($id_rol) {
        $stmt = $this->pdo->prepare("DELETE FROM rol WHERE id_rol = ?");
        return $stmt->execute([$id_rol]);
    }
}
?>