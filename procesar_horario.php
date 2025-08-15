<?php
require_once 'functions.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$usuario = getCurrentUser();
if ($usuario['tipo_usuario'] !== 'doctor') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $conn = getConnection();
    
    if ($_POST['action'] === 'crear') {
        try {
            // Obtener el ID del doctor
            $stmt = $conn->prepare("SELECT id FROM doctores WHERE usuario_id = ?");
            $stmt->execute([$usuario['id']]);
            $doctor = $stmt->fetch();
            
            if (!$doctor) {
                throw new Exception("No se encontró el registro del doctor");
            }
            
            // Crear el horario
            $stmt = $conn->prepare("
                INSERT INTO horarios_disponibles (doctor_id, fecha, hora, cupos_disponibles) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $doctor['id'],
                $_POST['fecha'],
                $_POST['hora'],
                $_POST['cupos_disponibles']
            ]);
            
            $_SESSION['success'] = "Horario creado correctamente";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al crear el horario: " . $e->getMessage();
        }
    }
}

header("Location: dashboard.php");
exit();
?> 