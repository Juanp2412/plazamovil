<?php
// Asegúrate de que la variable de conexión a la base de datos sea accesible
require_once '../config/conexion.php';

// Si el error de "Undefined variable $pdo" persiste,
// la siguiente línea podría solucionarlo, haciendo la variable globalmente accesible.
// global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;

    if ($id_usuario) {
        try {
            // Inicia una transacción para asegurar la integridad de los datos
            $pdo->beginTransaction();

            // 1. Elimina registros de `carrito_detalle`, ya que dependen de `carrito`.
            $stmtCarritoDetalle = $pdo->prepare("DELETE FROM carrito_detalle WHERE id_carrito IN (SELECT id_carrito FROM carrito WHERE id_usuario = ?)");
            $stmtCarritoDetalle->execute([$id_usuario]);
            
            // 2. Ahora, elimina los registros de la tabla `carrito`.
            $stmtCarrito = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ?");
            $stmtCarrito->execute([$id_usuario]);
            
            // 3. Elimina los registros de la tabla `pedidos`.
            $stmtPedidos = $pdo->prepare("DELETE FROM pedidos WHERE id_usuario = ?");
            $stmtPedidos->execute([$id_usuario]);
            
            // 4. Elimina los registros de la tabla `agricultor`.
            $stmtAgricultor = $pdo->prepare("DELETE FROM agricultor WHERE id_usuario = ?");
            $stmtAgricultor->execute([$id_usuario]);

            // 5. Finalmente, elimina el registro de la tabla `usuarios`.
            $stmtUsuario = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmtUsuario->execute([$id_usuario]);

            // Si todo funciona, confirma los cambios.
            $pdo->commit();

            header("Location: ../view/gestion_usuarios.php?deleted=1");
            exit;

        } catch (PDOException $e) {
            // Si algo falla, revierte la transacción.
            $pdo->rollBack();
            echo "Error al eliminar el usuario y sus datos asociados: " . $e->getMessage();
            exit;
        }
    } else {
        echo "Error: ID de usuario no proporcionado.";
        exit;
    }
} else {
    echo "Error: Método de solicitud no permitido.";
    exit;
}