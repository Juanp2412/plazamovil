<?php
session_start();
require_once '../config/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit;
}

// Obtener el ID del usuario
$id_usuario = $_SESSION['user_id'];



// Verificar si se ha enviado un producto específico o si se está procesando el carrito completo
$id_producto = $_POST['id_producto'] ?? null;
$productos = [];

if ($id_producto) {
    // Si se compra un producto específico desde producto_detalle.php
    $stmt = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo "Producto no encontrado.";
        exit;
    }

    $productos[] = $producto;
} else {
    // Si se compra el carrito completo desde carritoview.php
    $carrito = $_SESSION['carrito'] ?? [];
    if (!empty($carrito) && is_array($carrito)) {
        $placeholders = implode(',', array_fill(0, count($carrito), '?'));
        $stmt = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id IN ($placeholders)");
        $stmt->execute($carrito);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Calcular el total del pedido
$total = 0;
foreach ($productos as $producto) {
    $total += $producto['precio'];
}

// Registrar el pedido en la base de datos
try {
    $stmt = $pdo->prepare("INSERT INTO pedidos (id_usuario, productos, total) VALUES (?, ?, ?)");
    $stmt->execute([
        $id_usuario,
        json_encode($productos), // Almacenar los productos en formato JSON
        $total
    ]);

    // Limpiar el carrito si se compró todo
    if (!$id_producto) {
        $_SESSION['carrito'] = [];
    }

    // Redirigir a la página de confirmación
    header("Location: ../view/confirmacion.php");
    exit;
} catch (PDOException $e) {
    echo "Error al procesar la compra: " . $e->getMessage();
    exit;
}
?>