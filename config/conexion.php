<?php
// Cargar variables de entorno (opcional, si usas un archivo .env)
$host = getenv("DB_HOST") ?: "mysql.railway.internal";
$db   = getenv("DB_NAME") ?: "agro_app"; // Cambiado a agro_app
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASSWORD") ?: "GedaXvxilJYSGQCwjPJbVaXKLCgnVluP";
$port = getenv("DB_PORT") ?: "3306";

try {
    $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // errores con excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // fetch asociativo por defecto
    ]);

    // Para verificar que conecta correctamente:
    // echo "✅ Conexión exitosa a la base de datos en Railway";

} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}