<?php //controlador de citas
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'paciente') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/conexion.php';
require_once '../models/Cita.php';
require_once '../models/Horario.php';

$database = new Conexion();
$db = $database->getConnection();

$cita = new Cita($db);
$horario = new Horario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'agendar') {
        $horario->id = $_POST['horario_id'];
        
        // Verificar si hay cupos disponibles
        if ($horario->actualizarCupos()) {
            $cita->paciente_id = $_SESSION['usuario_id'];
            $cita->horario_id = $_POST['horario_id'];

            if ($cita->agendar()) {
                $_SESSION['success'] = "Cita agendada exitosamente";
            } else {
                $_SESSION['error'] = "Error al agendar la cita";
                // Revertir la actualización de cupos
                $horario->revertirCupos();
            }
        } else {
            $_SESSION['error'] = "No hay cupos disponibles para este horario";
        }
    }
}

header("Location: ../dashboard.php");
exit();
?> 