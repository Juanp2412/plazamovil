<?php
require_once __DIR__. '/../config/conexion.php';

class CarritoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Crear un carrito nuevo para un usuario
    public function crearCarrito($id_usuario) {
        $sql = "INSERT INTO carrito (id_usuario, fecha_creacion) VALUES (?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $this->pdo->lastInsertId();
    }

    // Buscar carrito activo de un usuario

    public function obtenerCarritoPorUsuario($id_usuario) {
        // Verificar que el usuario exista antes de crear el carrito
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            // Usuario no existe, opcional: destruir sesión y redirigir
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_unset();
            session_destroy();
            header('Location: /Plaza-M-vil-3.1/view/login.php');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM carrito WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$carrito) {
            // Crear carrito vacío si no existe
            $stmt = $this->pdo->prepare("INSERT INTO carrito (id_usuario, fecha_creacion) VALUES (?, NOW())");
            $stmt->execute([$id_usuario]);
            $id_carrito = $this->pdo->lastInsertId();

            $carrito = [
                'id_carrito' => $id_carrito,
                'id_usuario' => $id_usuario
            ];
        }

        return $carrito;
    }
}
