<?php
// filepath: c:\xampp\htdocs\Plaza-M-vil-3.1\controller\editarperfilcontroller.php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];
    $telefono = $_POST['telefono'];
    $foto_perfil = $_FILES['foto_perfil'];

    // Validación básica
    if (empty($id_usuario) || empty($nombre) || empty($correo) || empty($usuario)) {
        $_SESSION['error'] = 'Todos los campos obligatorios deben ser completados.';
        header('Location: ../view/perfil.php');
        exit();
    }

    // Manejo de la foto de perfil
    $foto_nombre = null;
    $foto_anterior = null;
    // Obtener la foto anterior del usuario
    $stmt_foto = $pdo->prepare('SELECT foto FROM usuarios WHERE id_usuario = ?');
    $stmt_foto->execute([$id_usuario]);
    $foto_anterior = $stmt_foto->fetchColumn();

    if (!empty($foto_perfil['name'])) {
        $foto_nombre = uniqid() . '_' . basename($foto_perfil['name']);
        $foto_ruta = '../img/' . $foto_nombre;

        // Mover el archivo subido
        if (!move_uploaded_file($foto_perfil['tmp_name'], $foto_ruta)) {
            $_SESSION['error'] = 'Error al subir la foto de perfil.';
            header('Location: ../view/perfil.php');
            exit();
        }

        // Eliminar la foto anterior si existe y no es la predeterminada
        if (!empty($foto_anterior) && $foto_anterior !== 'default_profile.png') {
            $ruta_anterior = '../img/' . $foto_anterior;
            if (file_exists($ruta_anterior)) {
                @unlink($ruta_anterior);
            }
        }
    }

    // Actualizar los datos en la base de datos
    try {
        $sql = "UPDATE usuarios SET nombre_completo = ?, email = ?, username = ?, telefono = ?";
        $params = [$nombre, $correo, $usuario, $telefono];

        // Si se subió una nueva foto, incluirla en la consulta
        if ($foto_nombre) {
            $sql .= ", foto = ?";
            $params[] = $foto_nombre;
        }

        $sql .= " WHERE id_usuario = ?";
        $params[] = $id_usuario;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = 'Perfil actualizado correctamente.';
        header('Location: ../view/perfil.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al actualizar el perfil: ' . $e->getMessage();
        header('Location: ../view/perfil.php');
        exit();
    }
} else {
    header('Location: ../view/perfil.php');
    exit();
}
?>
