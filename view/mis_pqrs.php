<?php
require_once __DIR__ . '/../config/paths.php';
session_start();
require_once '../model/pqrs_model.php';

if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: login.php');
    exit();
}

$pqrs = PQRSModel::obtenerPorUsuario($_SESSION['user_id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis PQRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Mis PQRS</h2>
        <?php if (empty($pqrs)): ?>
            <div class="alert alert-info">No has registrado PQRS.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Respuesta</th>
                        <th>Adjunto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pqrs as $item): ?>
                        <tr>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>