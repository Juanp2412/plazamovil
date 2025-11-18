<?php
require_once __DIR__ . '/paths.php';
// Cargar variables de entorno (opcional, si usas un archivo .env)

$host = 'localhost';
$dbname = 'agro_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}