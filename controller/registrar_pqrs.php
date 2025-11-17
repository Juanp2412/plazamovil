<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: ../view/login.php');
    exit;
}

$id_usuario = $_SESSION['user_id_usuario'];
$tipo = $_POST['tipo'] ?? '';
$asunto = $_POST['asunto'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$fecha = date('Y-m-d');
$adjunto = null;

// Guardar archivo adjunto si existe
if (!empty($_FILES['adjunto']['name'])) {
    $nombreArchivo = uniqid() . '_' . basename($_FILES['adjunto']['name']);
    $rutaDestino = '../adjuntos/' . $nombreArchivo;
    if (move_uploaded_file($_FILES['adjunto']['tmp_name'], $rutaDestino)) {
        $adjunto = $nombreArchivo;
    }
}

$stmt = $pdo->prepare("INSERT INTO pqrs (id_usuario, tipo, asunto, descripcion, fecha, estado, adjunto) VALUES (?, ?, ?, ?, ?, 'pendiente', ?)");
$stmt->execute([$id_usuario, $tipo, $asunto, $descripcion, $fecha, $adjunto]);

echo "<script>
    alert('PQRS registrada con éxito');
    window.history.back();
</script>";
exit;
?>
