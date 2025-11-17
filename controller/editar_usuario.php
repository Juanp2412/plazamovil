<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['rol'] ?? '';

    if ($id_usuario && !empty($nombre_completo) && !empty($email) && !empty($role)) {
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, id_rol = ? WHERE id_usuario = ?");
            $stmt->execute([$nombre_completo, $email, $role, $id_usuario]);

            // Redirigir con mensaje de éxito
            header("Location: ../view/gestion_usuarios.php?success=1");
            exit;
        } catch (PDOException $e) {
            error_log("Error al actualizar el usuario: " . $e->getMessage());
            header("Location: ../view/gestion_usuarios.php?error=1");
            exit;
        }
    } else {
        // Si faltan campos, redirigir con error
        header("Location: ../view/gestion_usuarios.php?error=campos");
        exit;
    }
} else {
    // Si no es POST, redirigir
    header("Location: ../view/gestion_usuarios.php");
    exit;
}