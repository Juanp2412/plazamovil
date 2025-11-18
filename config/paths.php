<?php
// Define rutas base de la aplicación de forma dinámica y con opción de override
if (!defined('BASE_URL')) {
    // Permite ajustar el directorio base via variable de entorno si el proyecto se despliega en otra carpeta
    $baseDir = getenv('PLAZA_BASE_DIR');
    if ($baseDir === false || $baseDir === '') {
        // Usa el nombre real del proyecto para evitar quedar atado a "Plaza-M-vil-3.1" en otras instalaciones
        $projectDir = basename(realpath(__DIR__ . '/..'));
        $baseDir = '/' . trim($projectDir, '/');
    }

    // Construye la URL absoluta con el host detectado
    $isHttps = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off';
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    define('BASE_URL', rtrim(sprintf('%s://%s%s', $scheme, $host, $baseDir), '/'));
}

if (!defined('BASE_PATH')) {
    $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    if (!empty($documentRoot)) {
        define('BASE_PATH', $documentRoot . parse_url(BASE_URL, PHP_URL_PATH));
    } else {
        define('BASE_PATH', realpath(__DIR__ . '/..'));
    }
}
