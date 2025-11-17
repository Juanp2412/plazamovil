<?php
// Mostrar errores para depuración (quítalo en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Manejo de logout
if (
    (isset($_POST['action']) && $_POST['action'] === 'logout') ||
    (isset($_GET['action']) && $_GET['action'] === 'logout')
) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    header('Location: /Plaza-M-vil-3.1/view/login.php');
    exit;
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../model/usermodel.php';
require_once '../config/conexion.php';

class LoginController {
    private $model;

    public function __construct($pdo) {
        $this->model = new UserModel($pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usernameOrEmail = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validar datos
            if (empty($usernameOrEmail) || empty($password)) {
                header("Location: ../view/login.php?error=Datos%20faltantes");
                exit;
            }

            // Buscar usuario
            $user = $this->model->getUserByUsernameOrEmail($usernameOrEmail);

            if ($user && password_verify($password, $user['password'])) {
                // Guardar datos básicos en sesión
                $_SESSION['user_id_usuario'] = (int)$user['id_usuario'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_id_rol'] = (int)$user['id_rol'];
                if (!empty($user['id_agricultor'])) {
                    $_SESSION['user_id_agricultor'] = (int)$user['id_agricultor'];
                }
                // Redirección única
                header("Location: ../index.php");
                exit;
            } else {
                // Error de login
                header("Location: ../view/login.php?error=Credenciales%20incorrectas");
                exit;
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /Plaza-M-vil-3.1/view/login.php');
        exit;
    }
}

// Ejecutar acción
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $controller = new LoginController($pdo);
    $controller->login();
}