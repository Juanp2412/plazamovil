<?php
require_once '../model/usermodel.php';
require_once '../model/agricultor_model.php';
require_once '../config/conexion.php';

class RegisterController
{
    private $userModel;
    private $agricultorModel;

    public function __construct($pdo)
    {
        $this->userModel = new UserModel($pdo);
        $this->agricultorModel = new AgricultorModel($pdo);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_completo = $_POST['nombre_completo'];
            $tipo_documento = $_POST['tipo_documento'];
            $numero_documento = $_POST['numero_documento'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $id_rol = $_POST['id_rol'];
            $foto = null; // Foto por defecto nula

            // Asignar ID rol según selección
           // switch ($rolSeleccionado) {
               // case "Administrador":
                   // $id_rol = 1;
                    //break;
               // case "Comprador": // 👈 asegúrate que este valor venga del formulario
                 //   $id_rol = 2;
                  //  break;
              //  case "Agricultor":
                //    $id_rol = 3;
              //      break;
               // default:
                //    $id_rol = 3; // por defecto comprador
            //}

            // Validar rol
            if (!in_array($id_rol, [1, 2, 3])) {
                header("Location: ../view/register.php?error=invalid_id_rol");
                exit;
            }

            // Encriptar contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Registrar usuario
            $id_usuario = $this->userModel->addUser(
                $nombre_completo,
                $tipo_documento,
                $numero_documento,
                $telefono,
                $email,
                $fecha_nacimiento,
                $username,
                $hashedPassword,
                $id_rol,
                $foto
            );

            if ($id_usuario) {
                // Si es agricultor, creamos su registro en la tabla agricultor con valores por defecto
                if ($id_rol == 3) {
                    $this->agricultorModel->addAgricultor(
                        $id_usuario,
                        1,          // zona por defecto
                        null,       // certificaciones vacío
                        null,       // fotos vacío
                        null,       // metodo_entrega vacío
                        null,       // metodos_de_pago vacío
                    );
                }

                header("Location: ../view/register.php?success=1");
                exit;
            } else {
                header("Location: ../view/register.php?error=database_error");
                exit;
            }
        }
    }
}

// Ejecutar controlador
$controller = new RegisterController($pdo);
$controller->register();
