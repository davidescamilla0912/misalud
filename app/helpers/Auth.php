<?php    //control de inicio de sesion verificacion de roles
class Auth {
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requireLogin() {
        self::init();
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /index.php");
            exit();
        }
    }

    public static function requireRole($role) {
        self::requireLogin();
        if ($_SESSION['tipo_usuario'] !== $role) {
            header("Location: /index.php");
            exit();
        }
    }

    public static function isLoggedIn() {
        self::init();
        return isset($_SESSION['usuario_id']);
    }

    public static function getCurrentUser() {
        self::init();
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['nombre'],
            'apellido' => $_SESSION['apellido'],
            'tipo_usuario' => $_SESSION['tipo_usuario']
        ];
    }

    public static function login($usuario) {
        self::init();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
    }

    public static function logout() {
        self::init();
        session_destroy();
        header("Location: /index.php");
        exit();
    }
} 