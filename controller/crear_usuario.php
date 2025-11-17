<?php
// Verificar si la conexión a la base de datos ($pdo) está definida
if (!isset($pdo)) {
    require_once '../config/conexion.php';
}

// Verificar si se recibieron los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Depuración: Verificar los datos recibidos
    error_log("Datos recibidos: " . print_r($_POST, true));

    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    // Validar que todos los campos estén completos
    if (!empty($nombre_completo) && !empty($email) && !empty($password) && !empty($username) && !empty($rol)) {
        try {
            // Insertar el usuario en la base de datos
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, email, password, username, id_rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $nombre_completo,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $username,
                $rol
            ]);

            // Redirigir de vuelta a la página de gestión de usuarios con éxito
            header("Location: ../view/gestion_usuarios.php?success=1");
            exit;
        } catch (PDOException $e) {
            error_log("Error al crear el usuario: " . $e->getMessage());
            echo "Error al crear el usuario. Por favor, inténtelo de nuevo.";
        }
    } else {
        // Depuración: Mostrar qué campos están vacíos
        $campos_faltantes = [];
        if (empty($nombre_completo)) $campos_faltantes[] = 'nombre_completo';
        if (empty($email)) $campos_faltantes[] = 'email';
        if (empty($password)) $campos_faltantes[] = 'password';
        if (empty($username)) $campos_faltantes[] = 'username';
        if (empty($rol)) $campos_faltantes[] = 'rol';

        error_log("Campos faltantes: " . implode(", ", $campos_faltantes));
        echo "Todos los campos son obligatorios. Faltan: " . implode(", ", $campos_faltantes);
    }
} else {
    echo "Método no permitido.";
}