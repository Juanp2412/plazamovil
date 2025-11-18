<?php
require_once '../config/conexion.php';
require_once '../vendor/autoload.php';

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

// Configurar MercadoPago
MercadoPagoConfig::setAccessToken("APP_USR-2180958071478070-092210-ac4ee3a8d1cff42421efa9d6ddd087f1-2702024581"); // token real de pruebas/producción

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$callbackBase = $scheme . $host . BASE_URL . '/controller/confirmar_pago.php';

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
        "success" => $callbackBase . "?status=success&payment_id={payment.id}&preference_id={preference.id}",
        "failure" => $callbackBase . "?status=failure&payment_id={payment.id}&preference_id={preference.id}",
        "pending" => $callbackBase . "?status=pending&payment_id={payment.id}&preference_id={preference.id}"
    ],
    "auto_return" => "approved",
    ]);
} catch (\MercadoPago\Exceptions\MPApiException $e) {
    echo "<pre>";
    print_r($e->getApiResponse()->getContent()); 
    echo "</pre>";
    exit;
}

// Guardar el registro inicial en la tabla pagos con estado "pendiente"
// Usamos preference_id temporalmente, luego en confirmar_pago.php lo actualizamos con payment_id real
$stmt = $pdo->prepare("INSERT INTO pagos (id_venta, proveedor, transaccion_id, monto, moneda, estado, metodo)
                       VALUES (?, 'MercadoPago', ?, ?, 'COP', 'pendiente', 'checkout')");
$stmt->execute([$id_pedido, $preference->id, $total]);

// Redirigir al checkout de MercadoPago
header("Location: " . $preference->init_point);
exit;
