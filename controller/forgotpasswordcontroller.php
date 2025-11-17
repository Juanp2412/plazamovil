<?php
// Controlador para procesar la solicitud de recuperación de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../model/usermodel.php';
    $email = $_POST['email'];
    // Aquí deberías buscar el usuario y generar un token único para recuperación
    // Por ahora, solo redirige con mensaje de éxito
    header('Location: ../view/forgot_password.php?success=1');
    exit();
} else {
    header('Location: ../view/forgot_password.php');
    exit();
}
