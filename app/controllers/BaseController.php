<?php
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../config/database.php';

class BaseController {
    protected $db;          //conexion a la base de datos validacion de roles
    protected $user;
    protected $messages = [];

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = Auth::getCurrentUser();
    }

    protected function requireRole($role) {
        Auth::requireRole($role);
    }

    protected function setSuccess($message) {
        $_SESSION['success'] = $message;
    }

    protected function setError($message) {
        $_SESSION['error'] = $message;
    }

    protected function getMessages() {
        $messages = '';
        if (isset($_SESSION['success'])) {
            $messages .= '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            $messages .= '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        return $messages;
    }

    protected function redirect($url) {
        header("Location: $url");
        exit();
    }

    protected function render($view, $data = []) {
        extract($data);
        ob_start();
        include __DIR__ . "/../views/{$view}.php";
        return ob_get_clean();
    }
} 