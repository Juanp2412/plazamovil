<?php
class Zona_model {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todas los Zonas
    public function ConsultarZonas() {
        $stmt = $this->pdo->query("SELECT * FROM zona");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una zona por ID
    public function ConsultarZona($id_zona) {
        $stmt = $this->pdo->prepare("SELECT * FROM zona WHERE id_zona = ?");
        $stmt->execute([$id_zona]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una zona rol
    public function CrearZona($zona) {
        $stmt = $this->pdo->prepare("INSERT INTO zona (zona) VALUES (?)");
        return $stmt->execute([$zona]);
    }

    // Actualizar una zona
    public function EditarZona($id_zona, $zona) {
        $stmt = $this->pdo->prepare("UPDATE zona SET zona = ? WHERE id_zona = ?");
        return $stmt->execute([$zona, $id_zona]);
    }

    // Eliminar una zona
    public function EliminarZona($id_zona) {
        $stmt = $this->pdo->prepare("DELETE FROM zona WHERE id_zona = ?");
        return $stmt->execute([$id_zona]);
    }
}
?>