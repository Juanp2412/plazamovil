<?php
class AgricultorModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Crear agricultor
    public function addAgricultor($id_usuario, $id_zona, $certificaciones, $fotos, $metodo_entrega, $metodos_de_pago) {
        $stmt = $this->pdo->prepare("
            INSERT INTO agricultor (id_usuario, id_zona, certificaciones, fotos, metodo_entrega, metodos_de_pago) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if ($stmt->execute([$id_usuario, $id_zona, $certificaciones, $fotos, $metodo_entrega, $metodos_de_pago
        ])) {
            return $this -> pdo -> lastInsertId();
        } else {
            return false;
        }
    }

    // Obtener agricultor con datos de usuario y zona
    public function getAgricultorById($id_agricultor) {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.nombre_completo, u.email, z.zona
            FROM agricultor a
            INNER JOIN usuarios u ON a.id_usuario = u.id_usuario
            INNER JOIN zona z ON a.id_zona = z.id_zona
            WHERE a.id_agricultor = ?
        ");
        $stmt->execute([$id_agricultor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar agricultor
    public function updateAgricultor($id_agricultor, $id_zona, $certificaciones, $fotos, $metodo_entrega, $metodos_de_pago, $id_calificacion) {
        $stmt = $this->pdo->prepare("
            UPDATE agricultor 
            SET id_zona = ?, certificaciones = ?, fotos = ?, metodo_entrega = ?, metodos_de_pago = ?, id_calificacion = ?
            WHERE id_agricultor = ?
        ");
        return $stmt->execute([$id_zona, $certificaciones, $fotos, $metodo_entrega, $metodos_de_pago, $id_calificacion, $id_agricultor]);
    }

    // Eliminar agricultor
    public function deleteAgricultor($id_agricultor) {
        $stmt = $this->pdo->prepare("DELETE FROM agricultor WHERE id_agricultor = ?");
        return $stmt->execute([$id_agricultor]);
    }

    // Listar todos los agricultores con sus usuarios y zonas
    public function getAllAgricultores() {
        $stmt = $this->pdo->query("
            SELECT a.id_agricultor, u.nombre_completo, u.email, z.nombre_zona, 
                   a.certificaciones, a.fotos, a.metodo_entrega, a.metodos_de_pago, a.id_calificacion
            FROM agricultor a
            INNER JOIN usuarios u ON a.id_usuario = u.id_usuario
            INNER JOIN zona z ON a.id_zona = z.id_zona
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

}
?>
