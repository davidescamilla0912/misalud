<?php //controlador de autenticacion
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Doctor.php';

if (!class_exists('Usuario')) {
    die('La clase Usuario no se cargó correctamente');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $usuario = new Usuario($db);
    $doctor = new Doctor($db);

    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $usuario->email = $_POST['email'];
        $usuario->password = $_POST['password'];

        if ($usuario->login()) {
            $_SESSION['usuario_id'] = $usuario->id;
            $_SESSION['nombre'] = $usuario->nombre;
            $_SESSION['apellido'] = $usuario->apellido;
            $_SESSION['tipo_usuario'] = $usuario->tipo_usuario;

            // Redirigir según el tipo de usuario
            switch ($usuario->tipo_usuario) {
                case 'administrador':
                    header("Location: ../admin/dashboard.php");
                    break;
                case 'doctor':
                    header("Location: ../doctor/dashboard.php");
                    break;
                case 'paciente':
                    header("Location: ../paciente/dashboard.php");
                    break;
                default:
                    header("Location: ../index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Credenciales inválidas";
            header("Location: ../index.php");
            exit();
        }
    } elseif ($action === 'register') {
        $usuario->nombre = $_POST['nombre'];
        $usuario->apellido = $_POST['apellido'];
        $usuario->email = $_POST['email'];
        $usuario->password = $_POST['password'];
        $usuario->tipo_usuario = $_POST['tipo_usuario'];

        if ($usuario->emailExiste()) {
            $_SESSION['error'] = "El correo electrónico ya está registrado";
            header("Location: ../index.php");
            exit();
        }

        if ($usuario->registrar()) {
            if ($usuario->tipo_usuario === 'doctor') {
                $doctor->usuario_id = $usuario->id;
                $doctor->especialidad = $_POST['especialidad'];
                $doctor->ubicacion = $_POST['ubicacion'];
                $doctor->registrar();
            }

            $_SESSION['success'] = "Registro exitoso. Por favor, inicie sesión.";
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['error'] = "Error al registrar el usuario";
            header("Location: ../index.php");
            exit();
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}
?> 