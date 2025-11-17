<?php
// Verificar si la sesión ya está iniciada antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id_usuario']) || $_SESSION['user_id_rol'] != 3) {
    header('Location: ../view/login.php');
    exit();
}

require_once '../config/conexion.php';
require_once '../model/carga_masiva_model.php';

$id_agricultor = $_SESSION['user_id_usuario'];
$mensaje = '';
$tipoMensaje = '';
$debug_info = '';

// Procesar el archivo Excel cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_excel'])) {
    try {
        // Validar archivo
        if ($_FILES['archivo_excel']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo: ' . $_FILES['archivo_excel']['error']);
        }

        // Validar tipo de archivo
        $extension = strtolower(pathinfo($_FILES['archivo_excel']['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            throw new Exception('Solo se permiten archivos CSV (.csv)');
        }
        if ($_FILES['archivo_excel']['size'] > 10 * 1024 * 1024) {
            throw new Exception('El archivo es demasiado grande. Máximo 10MB permitido.');
        }

        // Mover archivo a directorio temporal
        $archivoTemp = $_FILES['archivo_excel']['tmp_name'];
        if (!file_exists($archivoTemp)) {
            throw new Exception('El archivo temporal no existe');
        }

        // Procesar el archivo según su tipo
        $productos = procesarCSV($archivoTemp, $pdo, $id_agricultor, $debug_info);
        if (count($productos) > 0) {
            // Insertar productos en la base de datos
            
            $resultados = insertarProductos($productos, $pdo, $id_agricultor);
            
            $mensaje = "Carga masiva completada: " . 
                       $resultados['insertados'] . " productos insertados, " . 
                       $resultados['actualizados'] . " actualizados, " . 
                       $resultados['errores'] . " errores";
            $tipoMensaje = $resultados['errores'] > 0 ? 'warning' : 'success';
        } else {
            $mensaje = "No se encontraron productos válidos en el archivo CSV.";
            $tipoMensaje = 'warning';
        }

    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}

// Para la vista
$stmt = $pdo->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>