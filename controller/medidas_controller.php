
<?php
require_once '../config/conexion.php';
require_once '../model/medidas_model.php';

class MedidasController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Medidas_model($pdo);
    }

    // Listar todas las medidas
    public function index(): array {
        return $this->model->Consultarmedidas();
    }

    // Consultar una medida por ID
    public function show($id_unidad): ?array {
        return $this->model->Consultarmedida($id_unidad);
    }

    // Crear una nueva medida
    public function store($nombre): bool {
        return $this->model->Crearmedida($nombre);
    }

    // Editar una medida existente
    public function update($id_unidad, $nombre): bool {
        return $this->model->Editarmedida($id_unidad, $nombre);
    }

    // Eliminar una medida
    public function destroy($id_unidad): bool {
        return $this->model->Eliminarmedida($id_unidad);
    }
}

// =======================
// Manejo de solicitudes HTTP
// =======================
$controller = new MedidasController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'crear':
            $nombre = $_POST['nombre'] ?? '';
            $controller->store($nombre);
            break;

        case 'editar':
            $id_unidad = $_POST['id_unidad'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            if ($id_unidad) {
                $controller->update($id_unidad, $nombre);
            }
            break;

        case 'eliminar':
            $id_unidad = $_POST['id_unidad'] ?? null;
            if ($id_unidad) {
                $controller->destroy($id_unidad);
            }
            break;
    }

    // Redirigir después de la acción
    header("Location: ../view/gestion_medidas.php");
    exit;
}

// =======================
// Si es GET → obtener listado para mostrar en la vista
// =======================
$medidas = $controller->index();