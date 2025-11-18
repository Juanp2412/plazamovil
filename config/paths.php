<?php
// Define rutas base de la aplicación
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Plaza-M-vil-3.1');
}

if (!defined('BASE_PATH')) {
    $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    if (!empty($documentRoot)) {
        define('BASE_PATH', $documentRoot . BASE_URL);
    } else {
        define('BASE_PATH', realpath(__DIR__ . '/..'));
    }
}
