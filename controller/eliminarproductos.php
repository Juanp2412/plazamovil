<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productos_a_eliminar = $_POST['productos_a_eliminar'] ?? [];

    if (!empty($productos_a_eliminar)) {
        try {
            // Convertir el array en una lista para la consulta SQL
            $placeholders = implode(',', array_fill(0, count($productos_a_eliminar), '?'));
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto IN ($placeholders)");
            $stmt->execute($productos_a_eliminar);

            // Redirigir de vuelta a la página de mis productos con un mensaje de éxito
            header("Location: ../view/mis_productos.php?delete=ok");
            exit;
        } catch (PDOException $e) {
            error_log("Error al eliminar productos: " . $e->getMessage());
            echo "Error al eliminar productos.";
        }
    } else {
        echo "No se seleccionaron productos para eliminar.";
    }
} else {
    echo "Método no permitido.";
}