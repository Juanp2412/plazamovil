<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9f7ef;
            /* Fondo verde claro */
        }

        .login-container {
            background-color: #ffffff;
            /* Fondo blanco para el formulario */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .btn-primary {
            background-color: #28a745;
            /* Verde para el botón */
            border-color: #28a745;
        }

        .btn-primary:hover {
            background-color: #218838;
            /* Verde más oscuro al pasar el cursor */
            border-color: #1e7e34;
        }

        .logo {
            display: block;
            margin: 0 auto 20px auto;
            width: 150px;
            /* Ajusta el tamaño del logo (más grande) */
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="login-container">
                    <!-- Logo de la página -->
                    <img src="../img/logoplazamovil.png" alt="Logo Plaza Movil" class="logo">

                    <h2 class="text-center mb-4">Iniciar Sesión</h2>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">Usuario o contraseña incorrectos.</div>
                    <?php endif; ?>

                    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
                        <div class="alert alert-success text-center mt-3">
                            Usuario eliminado correctamente. Puedes crear una nueva cuenta o iniciar sesión con otro usuario.
                        </div>
                    <?php endif; ?>

                    <form action="../controller/logincontroller.php" method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario o Correo Electrónico</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    </form>
                    <div class="text-center mt-2">
                        <a href="forgot_password.php">¿Olvidaste tu contraseña?</a>
                    </div>
                    <p class="text-center mt-3">
                        ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>