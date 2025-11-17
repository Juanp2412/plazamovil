<?php
// Controlador para procesar el cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    if ($password !== $confirm) {
        header('Location: ../view/reset_password.php?token=' . urlencode($token) . '&error=1');
        exit();
    }
    // Aquí deberías validar el token y actualizar la contraseña en la base de datos
    // Por ahora, solo redirige con mensaje de éxito
    header('Location: ../view/reset_password.php?success=1');
    exit();
} else {
    header('Location: ../view/reset_password.php');
    exit();
}
