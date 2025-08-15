<?php //controlador de horarios
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'doctor') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/conexion.php';
require_once '../models/Horario.php';

$database = new Conexion();
$db = $database->getConnection();

$horario = new Horario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'crear') {
        $horario->doctor_id = $_SESSION['usuario_id'];
        $horario->fecha = $_POST['fecha'];
        $horario->hora = $_POST['hora'];
        $horario->cupos_disponibles = $_POST['cupos_disponibles'];

        if ($horario->crear()) {
            $_SESSION['success'] = "Horario creado exitosamente";
        } else {
            $_SESSION['error'] = "Error al crear el horario";
        }
    }
}

header("Location: ../dashboard.php");
exit();
?> 