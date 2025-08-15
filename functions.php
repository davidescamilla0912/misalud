<?php
session_start();
require_once 'config.php';

function login($email, $password) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
        return true;
    }
    return false;
}

function register($nombre, $apellido, $email, $password, $tipo_usuario, $especialidad = null, $ubicacion = null) {
    $conn = getConnection();
    
    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return false;
    }

    // Registrar usuario
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password, tipo_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $email, $hashed_password, $tipo_usuario]);
    $usuario_id = $conn->lastInsertId();

    // Si es doctor, registrar información adicional
    if ($tipo_usuario === 'doctor' && $especialidad && $ubicacion) {
        $stmt = $conn->prepare("INSERT INTO doctores (usuario_id, especialidad, ubicacion) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $especialidad, $ubicacion]);
    }

    return true;
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?> 