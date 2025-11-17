<?php
// filepath: c:\xampp\htdocs\Plaza_Movil\controller\gestion_categorias.php
require_once '../model/categorias_model.php';
require_once '../config/conexion.php';

class CategoriasController {
    private $model;

    public function __construct($pdo) {
        $this->model = new CategoriasModel($pdo);
    }

    public function index(): array {
        return $this->model->obtenerCategorias();
    }

    public function listaCategorias(): array {
        return $this->model->listaCategorias();
    }

    public function agregarCategoria($nombre, $descripcion): void {
        $this->model->agregarCategoria($nombre, $descripcion);
    }

    public function eliminarCategoria($id_categoria): void {
        try {
            $this->model->eliminarCategoria($id_categoria);
            header("Location: ../view/gestion_categorias.php?delete=ok");
            exit;
        } catch (Exception $e) {
            header("Location: ../view/gestion_categorias.php?error=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function actualizarCategoria($id_categoria, $nombre, $descripcion): void {
        $this->model->actualizarCategoria($id_categoria, $nombre, $descripcion);
    }
}

// Crear instancia del controlador
$controller = new CategoriasController($pdo);

// Manejo de solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $controller->agregarCategoria($nombre, $descripcion);
                break;

            case 'eliminar':
                $id_categoria = $_POST['id_categoria'] ?? null;
                if ($id_categoria) {
                    $controller->eliminarCategoria($id_categoria);
                }
                break;

            case 'actualizar':
                $id_categoria = $_POST['id_categoria'] ?? null;
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                if ($id_categoria) {
                    $controller->actualizarCategoria($id_categoria, $nombre, $descripcion);
                }
                break;
        }
    }

    // Redirigir tras la acción
    header("Location: ../view/gestion_categorias.php");
    exit;
}

// Si es GET → obtener listado de categorías
$categorias = $controller->index();