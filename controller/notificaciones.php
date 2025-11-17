<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/conexion.php';
require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

MercadoPagoConfig::setAccessToken("APP_USR-713795618104962-091821-550c6a5df32dc1fa6381f9a4e99d5a10-2696066349");

// Guardar log para debug
$input = file_get_contents("php://input");
file_put_contents(sys_get_temp_dir() . "/mp_webhook.log", "[".date("Y-m-d H:i:s")."] " . $input . PHP_EOL, FILE_APPEND);

$data = json_decode($input, true);

if (!isset($data["id"]) || !isset($data["type"]) || $data["type"] !== "payment") {
    http_response_code(400);
    exit("Notificación inválida");
}

$payment_id = $data["id"];

// Consultar el pago en Mercado Pago
$client = new PaymentClient();
try {
    $payment = $client->get($payment_id);
} catch (Exception $e) {
    http_response_code(500);
    exit("Error al consultar pago: " . $e->getMessage());
}

// Extraer info importante
$status        = $payment->status;        // approved, pending, rejected
$external_ref  = $payment->external_reference; // PEDIDO_xx
$transactionId = $payment->id;
$monto         = $payment->transaction_amount;

// Actualizar tu tabla pagos
$stmt = $pdo->prepare("UPDATE pagos SET estado = ?, transaccion_id = ? WHERE preference_id = ? OR id_pedido = ?");
$stmt->execute([$status, $transactionId, $payment->order->id ?? null, str_replace("PEDIDO_", "", $external_ref)]);

// Responder a Mercado Pago
http_response_code(200);
echo "OK";
