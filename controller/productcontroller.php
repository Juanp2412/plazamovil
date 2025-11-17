<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../model/productmodel.php';
require_once '../config/conexion.php';

class ProductController {
    private $model;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new ProductModel($pdo);
    }

    private function uploadImage($inputName) {
        if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
            $imagen = uniqid() . "_" . basename($_FILES[$inputName]['name']);
            $ruta_destino = '../img/' . $imagen;
            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $ruta_destino)) {
                return $imagen;
            }
        }
        return null;
    }

    public function addProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_agricultor = $_SESSION['user_id_agricultor'] ?? null;

            if (!$id_agricultor && isset($_SESSION['user_id_usuario'])) {
                // Buscar agricultor desde DB
                $stmt = $this->pdo->prepare("SELECT id_agricultor FROM agricultor WHERE id_usuario = ?");
                $stmt->execute([$_SESSION['user_id_usuario']]);
                $agricultor = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($agricultor) {
                    $id_agricultor = $agricultor['id_agricultor'];
                    $_SESSION['user_id_agricultor'] = $id_agricultor;
                }
            }

            error_log("SESSION: " . json_encode($_SESSION));

            if (!$id_agricultor) {
                echo "<script>alert('Error: No se encontró el agricultor.'); window.location.href='../view/mis_productos.php';</script>";
                return;
            }

            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio = $_POST['precio_unitario'];
            $stock = $_POST['stock'];
            $id_categoria = $_POST['id_categoria'];
            $id_unidad = $_POST['id_unidad'];
            $fecha_publicacion = date("Y-m-d H:i:s");

            $foto = $this->uploadImage('foto');

            if ($this->model->addProduct($nombre, $descripcion, $precio, $foto, $id_agricultor, $stock, $id_categoria, $id_unidad, $fecha_publicacion)) {
                echo "<script>alert('¡Producto añadido con éxito!'); window.location.href='../view/mis_productos.php?add=ok';</script>";
            } else {
                echo "<script>alert('Error al añadir el producto.'); window.location.href='../view/mis_productos.php';</script>";
            }
        }
    }
}

$controller = new ProductController($pdo);
$controller->addProduct();
