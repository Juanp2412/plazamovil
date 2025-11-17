<?php
require_once 'Rol_Model.php';

class UserModel extends Rol_Model
{
    public function __construct($pdo)
    {
        parent::__construct($pdo); // hereda la conexión de RolModel
    }

    // Buscar usuario por username o email
    public function getUserByUsernameOrEmail($usernameOrEmail) {
    $stmt = $this->pdo->prepare("
        SELECT u.*, a.id_agricultor
        FROM usuarios u
        LEFT JOIN agricultor a ON u.id_usuario = a.id_usuario
        WHERE u.username = ? OR u.email = ?
        LIMIT 1
    ");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Agregar usuario
    public function addUser($nombre_completo, $tipo_documento, $numero_documento, $telefono, $email, $fecha_nacimiento, $username, $password, $id_rol, $foto)
    {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios 
            (nombre_completo, tipo_documento, numero_documento, telefono, email, fecha_nacimiento, username, password, id_rol, foto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (
            $stmt->execute([
                $nombre_completo,
                $tipo_documento,
                $numero_documento,
                $telefono,
                $email,
                $fecha_nacimiento,
                $username,
                $password,
                $id_rol,
                $foto
            ])
        ) {
            return $this->pdo->lastInsertId();
        } else {
            return false;
        }
    }
    // Actualizar usuario
    public function updateUser($id_usuario, $nombre_completo, $tipo_documento, $numero_documento, $telefono, $email, $fecha_nacimiento, $username, $password, $id_rol)
    {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET 
            nombre_completo = ?, 
            tipo_documento = ?, 
            numero_documento = ?, 
            telefono = ?, 
            email = ?, 
            fecha_nacimiento = ?, 
            username = ?, 
            password = ?, 
            id_rol = ?
            WHERE id_usuario = ?");

        return $stmt->execute([
            $nombre_completo,
            $tipo_documento,
            $numero_documento,
            $telefono,
            $email,
            $fecha_nacimiento,
            $username,
            $password,
            $id_rol,
            $id_usuario
        ]);
    }

    //Eliminar usuario
    public function deleteUser($id_usuario)
    {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        return $stmt->execute([$id_usuario]);
    }
    // Obtener usuario con su rol
    public function getUserWithRole($id_usuario)
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN rol r ON u.id_rol = r.id_rol
            WHERE u.id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}
?>