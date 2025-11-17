<?php
require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

// Configura el token (usa el de prueba mientras desarrollas)
MercadoPagoConfig::setAccessToken("APP_USR-8452269694356919-092417-c15711717e9df463834bed7ebb2225dc-2702024581");

// Crear una preferencia de prueba
$client = new PreferenceClient();

$preference = $client->create([
    "items" => [
        [
            "title" => "Producto de prueba",
            "quantity" => 1,
            "currency_id" => "COP",
            "unit_price" => 1000
        ]
    ]
]);

echo "Link de pago: " . $preference->init_point;
