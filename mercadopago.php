<?php
require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

// Configura el token (usa el de prueba mientras desarrollas)
MercadoPagoConfig::setAccessToken("APP_USR-713795618104962-091821-550c6a5df32dc1fa6381f9a4e99d5a10-2696066349");

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
