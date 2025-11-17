<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="login-container bg-white p-4 rounded shadow">
                    <h2 class="text-center mb-4">Recuperar Contraseña</h2>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Si el correo existe, se enviaron instrucciones.</div>
                    <?php endif; ?>
                    <form action="../controller/forgotpasswordcontroller.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar instrucciones</button>
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
