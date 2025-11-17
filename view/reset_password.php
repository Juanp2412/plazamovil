<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="login-container bg-white p-4 rounded shadow">
                    <h2 class="text-center mb-4">Cambiar Contraseña</h2>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Contraseña cambiada correctamente.</div>
                    <?php endif; ?>
                    <form action="../controller/resetpasswordcontroller.php" method="POST">
                        <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cambiar Contraseña</button>
                    </form>
                    <p class="text-center mt-3">
                        <a href="login.php">Volver al login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
