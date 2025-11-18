<?php
// controller/gestionar_pago.php

require_once '../config/conexion.php';
require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

session_start();

/* ================== Validar sesión ================== */
if (!isset($_SESSION['user_id_usuario'])) {
    header("Location: ../view/login.php");
    exit;
}

$id_usuario = $_SESSION['user_id_usuario'];

/* ================== Validar pedido ================== */
$id_pedido = $_POST['id_pedido'] ?? null;
if (!$id_pedido) {
    die("No se recibió un pedido válido.");
}

/* ================== Traer productos del pedido ================== */
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

/* ================== Configurar Mercado Pago ================== */
// ⚠️ PON AQUÍ TU ACCESS TOKEN REAL (idealmente desde .env)
MercadoPagoConfig::setAccessToken("APP_USR-8452269694356919-092417-c15711717e9df463834bed7ebb2225dc-2702024581"); // token real de pruebas/producción APP_USR-2180958071478070-092210-ac4ee3a8d1cff42421efa9d6ddd087f1-2702024581

/* ================== URLs públicas (ngrok) ================== */
/**
 * Este dominio debe ser accesible desde internet.
 * Ngrok ya lo es, así que usamos directamente tu dominio.
 */
$publicBaseUrl    = 'https://18d61d0853e5.ngrok-free.app';

// Callback cuando el usuario vuelve desde Mercado Pago
$callbackBaseUrl  = rtrim($publicBaseUrl, '/') . '/controller/confirmar_pago.php';

// (Opcional pero recomendado) Webhook para recibir notificaciones de pago
$notificationUrl  = rtrim($publicBaseUrl, '/') . '/webhook/mercadopago.php';

/* ================== Construir items y total ================== */
$items = [];
$total = 0;

foreach ($productos as $prod) {
    $items[] = [
        "title"       => $prod['nombre'],
        "quantity"    => (int) $prod['cantidad'],
        "currency_id" => "COP",
        "unit_price"  => (float) $prod['precio_unitario']
    ];
    $total += $prod['cantidad'] * $prod['precio_unitario'];
}

/* ================== Crear preferencia ================== */
$client = new PreferenceClient();

try {
    $preference = $client->create([
        "items" => $items,

        // URLs limpias (sin {payment.id}, {preference.id}, etc.)
        "back_urls" => [
            "success" => $callbackBaseUrl . "?status=success",
            "failure" => $callbackBaseUrl . "?status=failure",
            "pending" => $callbackBaseUrl . "?status=pending",
        ],

        // Redirección automática cuando el pago sea aprobado
        "auto_return" => "approved",

        "payment_methods" => [
            "excluded_payment_methods" => [],
            "excluded_payment_types"   => []
        ],

        // Para que puedas identificar el pedido en tu sistema
        "external_reference" => (string) $id_pedido,

        // Opcional, pero muy útil si implementas webhook
        "notification_url"   => $notificationUrl,
    ]);
} catch (\MercadoPago\Exceptions\MPApiException $e) {
    echo "<h3>Error al crear la preferencia de pago</h3>";
    echo "<pre>";
    print_r($e->getApiResponse()->getContent());
    echo "</pre>";
    exit;
}

/* ================== Registrar pago en tu BD ================== */
// Verifica si las columnas existen en la tabla pagos
$columns        = $pdo->query("SHOW COLUMNS FROM pagos")->fetchAll(PDO::FETCH_COLUMN);
$hasPreferenceId = in_array('preference_id', $columns);
$hasProveedor    = in_array('proveedor', $columns);
$hasMonto        = in_array('monto', $columns);

if ($hasPreferenceId && $hasProveedor && $hasMonto) {
    $stmt = $pdo->prepare("
        INSERT INTO pagos (id_pedido, preference_id, proveedor, transaccion_id, monto, moneda, estado, metodo)
        VALUES (?, ?, 'MercadoPago', NULL, ?, 'COP', 'pendiente', 'checkout')
    ");
    $stmt->execute([$id_pedido, $preference->id, $total]);

} elseif ($hasPreferenceId && $hasMonto) {
    $stmt = $pdo->prepare("
        INSERT INTO pagos (id_pedido, preference_id, transaccion_id, monto, moneda, estado, metodo)
        VALUES (?, ?, NULL, ?, 'COP', 'pendiente', 'checkout')
    ");
    $stmt->execute([$id_pedido, $preference->id, $total]);

} elseif ($hasProveedor && $hasMonto) {
    $stmt = $pdo->prepare("
        INSERT INTO pagos (id_pedido, proveedor, transaccion_id, monto, moneda, estado, metodo)
        VALUES (?, 'MercadoPago', NULL, ?, 'COP', 'pendiente', 'checkout')
    ");
    $stmt->execute([$id_pedido, $total]);

} elseif ($hasMonto) {
    $stmt = $pdo->prepare("
        INSERT INTO pagos (id_pedido, transaccion_id, monto, moneda, estado, metodo)
        VALUES (?, NULL, ?, 'COP', 'pendiente', 'checkout')
    ");
    $stmt->execute([$id_pedido, $total]);

} else {
    $stmt = $pdo->prepare("
        INSERT INTO pagos (id_pedido, transaccion_id, moneda, estado, metodo)
        VALUES (?, NULL, 'COP', 'pendiente', 'checkout')
    ");
    $stmt->execute([$id_pedido]);
}

/* ================== Redirigir al checkout de Mercado Pago ================== */
header("Location: " . $preference->init_point);
// Si quieres usar modo sandbox explícito, puedes probar con:
// header("Location: " . $preference->sandbox_init_point);
exit;
