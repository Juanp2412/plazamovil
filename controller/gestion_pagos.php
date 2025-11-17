<?php
require_once '../config/conexion.php';
require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

session_start();

// Verificar usuario
if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: ../view/login.php");
    exit;
}

$id_usuario = $_SESSION['user_id_usuario'];
$id_pedido = $_POST['id_pedido'] ?? null;

if (!$id_pedido) {
    die("No se recibió un pedido válido.");
}

// Traer productos del pedido
$stmt = $pdo->prepare("
    SELECT pd.*, p.nombre, p.precio_unitario 
    FROM pedido_detalle pd
    JOIN productos p ON pd.id_producto = p.id_producto
    WHERE pd.id_pedido = ?
");
$stmt->execute([$id_pedido]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$productos) {
    die("No se encontraron productos en el pedido.");
}

// Configurar MercadoPago (cambia MercadoPagoConfig por SDK)
MercadoPagoConfig::setAccessToken("APP_USR-8452269694356919-092417-c15711717e9df463834bed7ebb2225dc-2702024581"); // token real de pruebas/producción APP_USR-2180958071478070-092210-ac4ee3a8d1cff42421efa9d6ddd087f1-2702024581

$items = [];
$total = 0;
foreach ($productos as $prod) {
    $items[] = [
        "title" => $prod['nombre'],
        "quantity" => (int) $prod['cantidad'],
        "currency_id" => "COP",
        "unit_price" => (float) $prod['precio_unitario']
    ];
    $total += $prod['cantidad'] * $prod['precio_unitario'];
}

$client = new PreferenceClient();
try {
    $preference = $client->create([
        "items" => $items,
        "back_urls" => [
            "failure" => "https://4181799cb229.ngrok-free.app/Plaza-M-vil-3.1/controller/confirmar_pago.php?status=success&payment_id={payment.id}",
            "success" => "https://4181799cb229.ngrok-free.app/Plaza-M-vil-3.1/controller/confirmar_pago.php?status=failure&payment_id={payment.id}",
            "pending" => "https://4181799cb229.ngrok-free.app/Plaza-M-vil-3.1/controller/confirmar_pago.php?status=pending&payment_id={payment.id}"
        ],
        "auto_return" => "approved",
        "payment_methods" => [
            "excluded_payment_methods" => [],   // no excluyas nada
            "excluded_payment_types" => []      // permite tarjeta, débito, PSE, billeteras, etc.
        ]
        
    ]);
} catch (\MercadoPago\Exceptions\MPApiException $e) {
    echo "<pre>";
    print_r($e->getApiResponse()->getContent()); 
    echo "</pre>";
    exit;
}


// Verifica si las columnas preference_id, proveedor y monto existen en la tabla pagos
$columns = $pdo->query("SHOW COLUMNS FROM pagos")->fetchAll(PDO::FETCH_COLUMN);
$hasPreferenceId = in_array('preference_id', $columns);
$hasProveedor = in_array('proveedor', $columns);
$hasMonto = in_array('monto', $columns);

// Guardar el registro inicial en la tabla pagos con estado "pendiente"
if ($hasPreferenceId && $hasProveedor && $hasMonto) {
    $stmt = $pdo->prepare("INSERT INTO pagos (id_pedido, preference_id, proveedor, transaccion_id, monto, moneda, estado, metodo)
                           VALUES (?, ?, 'MercadoPago', NULL, ?, 'COP', 'pendiente', 'checkout')");
    $stmt->execute([$id_pedido, $preference->id, $total]);
} elseif ($hasPreferenceId && $hasMonto) {
    $stmt = $pdo->prepare("INSERT INTO pagos (id_pedido, preference_id, transaccion_id, monto, moneda, estado, metodo)
                           VALUES (?, ?, NULL, ?, 'COP', 'pendiente', 'checkout')");
    $stmt->execute([$id_pedido, $preference->id, $total]);
} elseif ($hasProveedor && $hasMonto) {
    $stmt = $pdo->prepare("INSERT INTO pagos (id_pedido, proveedor, transaccion_id, monto, moneda, estado, metodo)
                           VALUES (?, 'MercadoPago', NULL, ?, 'COP', 'pendiente', 'checkout')");
    $stmt->execute([$id_pedido, $total]);
} elseif ($hasMonto) {
    $stmt = $pdo->prepare("INSERT INTO pagos (id_pedido, transaccion_id, monto, moneda, estado, metodo)
                           VALUES (?, NULL, ?, 'COP', 'pendiente', 'checkout')");
    $stmt->execute([$id_pedido, $total]);
} else {
    $stmt = $pdo->prepare("INSERT INTO pagos (id_pedido, transaccion_id, moneda, estado, metodo)
                           VALUES (?, NULL, 'COP', 'pendiente', 'checkout')");
    $stmt->execute([$id_pedido]);
}

// Redirigir al checkout de MercadoPago
header("Location: " . $preference->init_point);
exit;

