<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id_usuario']) || $_SESSION['user_id_rol'] != 3) {
    header('Location: ../view/login.php');
    exit();
}

// Headers para CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="plantilla_productos.csv"');

// Crear contenido CSV
$output = fopen('php://output', 'w');

// Encabezados
fputcsv($output, ['Nombre', 'Descripcion', 'Categoria', 'Precio', 'Stock', 'Unidad']);

// Ejemplos de datos - usando las categorías que tienes
fputcsv($output, ['Manzana Roja', 'Manzana fresca de la region', 'Frutas', '2500', '100', 'Kilo']);
fputcsv($output, ['Zanahoria', 'Zanahoria organica fresca', 'Verduras', '1800', '50', 'Kilo']);
fputcsv($output, ['Papa', 'Papa pastusa premium', 'Tuberculos', '2000', '80', 'Kilo']);

fclose($output);
exit();
?>