<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Obtener el hash de la contraseña del usuario
    $stmt = $pdo->prepare('SELECT password FROM usuarios WHERE id_usuario = ?');
    $stmt->execute([$id_usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($confirm_password, $user['password'])) {
        // Eliminar usuario
        $deleteStmt = $pdo->prepare('DELETE FROM usuarios WHERE id_usuario = ?');
        $deleteStmt->execute([$id_usuario]);

        // Destruir sesión
        session_unset();
        session_destroy();

        // Redirigir a login con mensaje de éxito
        header('Location: ../view/login.php?deleted=1');
        exit();
    } else {
        // Contraseña incorrecta, redirigir de vuelta con error
        header('Location: ../view/perfil.php?error=1');
        exit();
    }
}
?>
