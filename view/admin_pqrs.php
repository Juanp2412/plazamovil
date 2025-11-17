<?php
session_start();
require_once '../model/pqrs_model.php';

if (!isset($_SESSION['user_id_usuario']) || $_SESSION['user_id_rol'] != 1) {
    header('Location: login.php');
    exit();
}

$pqrs = PQRSModel::obtenerTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pqrs'], $_POST['respuesta'])) {
    PQRSModel::responder($_POST['id_pqrs'], $_POST['respuesta']);
    header("Location: admin_pqrs.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión PQRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/Plaza-M-vil-3.1/css/styles.css">
    
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">PQRS Registradas</h2>
    <?php if (empty($pqrs)): ?>
        <div class="alert alert-info">No hay PQRS registradas.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Respuesta</th>
                    <th>Adjunto</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pqrs as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($item['tipo']) ?></td>
                    <td><?= htmlspecialchars($item['asunto']) ?></td>
                    <td><?= htmlspecialchars($item['fecha']) ?></td>
                    <td><?= htmlspecialchars($item['estado']) ?></td>
                    <td><?= htmlspecialchars($item['respuesta']) ?></td>
                    <td>
                        <?php if (!empty($item['adjunto'])): ?>
                            <a href="../adjuntos/<?= htmlspecialchars($item['adjunto']) ?>" target="_blank">Ver archivo</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item['estado'] !== 'respondido'): ?>
                        <form method="POST" class="d-flex">
                            <input type="hidden" name="id_pqrs" value="<?= $item['id_pqrs'] ?>">
                            <input type="text" name="respuesta" class="form-control form-control-sm me-2" placeholder="Respuesta..." required>
                            <button type="submit" class="btn btn-success btn-sm">Responder</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
