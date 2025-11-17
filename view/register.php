<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9f7ef;
            /* Fondo verde claro */
        }

        .register-container {
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
            /* Ajusta el tamaño del logo */
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-container">
                    <!-- Logo de la página -->
                    <img src="../img/logoplazamovil.png" alt="Logo Plaza Movil" class="logo">

                    <h2 class="text-center mb-4">Registro</h2>

                    <!-- Mostrar alertas de éxito o error -->
                    <?php if (isset($_GET['success'])): ?>
                        <script>
                            alert('¡Usuario registrado con éxito!');
                            window.location.href = 'login.php';
                        </script>
                    <?php elseif (isset($_GET['error'])): ?>
                        <script>
                            alert('Error al registrar el usuario. Intente nuevamente.');
                        </script>
                    <?php endif; ?>

                    <form action="../controller/registercontroller.php" method="POST">
                        <div class="mb-3">
                            <label for="nombre_completo" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                            <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                                <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="numero_documento" class="form-label">Número de Documento</label>
                            <input type="text" class="form-control" id="numero_documento" name="numero_documento" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_rol" class="form-label">Tipo de Usuario</label>
                            <select class="form-control" id="id_rol" name="id_rol" required>
                                <option value="2">Comprador</option>
                                <option value="3">Agricultor</option>
                               <!-- <option value="1">Administrador</option> -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>