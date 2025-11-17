<?php
class Medidas_model {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todas los medidas
    public function Consultarmedidas(): array {
    $stmt = $this->pdo->query("SELECT id_unidad, nombre FROM unidades_de_medida ORDER BY id_unidad ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una medida por ID
    public function Consultarmedida($id_unidad) {
        $stmt = $this->pdo->prepare("SELECT * FROM unidades_de_medida WHERE id_unidad = ?");
        $stmt->execute([$id_unidad]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una medida 
    public function Crearmedida($nombre) {
        $stmt = $this->pdo->prepare("INSERT INTO unidades_de_medida (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    // Actualizar una medida
    public function Editarmedida($id_unidad, $nombre) {
        $stmt = $this->pdo->prepare("UPDATE unidades_de_medida SET nombre = ? WHERE id_unidad = ?");
        return $stmt->execute([ $nombre, $id_unidad]);
    }

    // Eliminar una medida
    public function Eliminarmedida($id_unidad) {
        $stmt = $this->pdo->prepare("DELETE FROM unidades_de_medida WHERE id_unidad = ?");
        return $stmt->execute([$id_unidad]);
    }
}
?>