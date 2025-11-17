<?php
require_once '../model/PerfilModel.php';
require_once '../config/conexion.php';

session_start();
if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: login.php');
    exit();
}

$perfilModel = new PerfilModel($pdo);
$user = $perfilModel->obtenerUsuarioPorId($_SESSION['user_id_usuario']);

if (!$user) {
    $_SESSION['error'] = 'Usuario no encontrado.';
    header('Location: error.php');
    exit();
}

// Pasar datos al View
include '../view/perfil.php';
