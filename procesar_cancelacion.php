<?php
require_once 'citas_functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cita_id'])) {
    $cita_id = intval($_POST['cita_id']);
    $resultado = cancelarCita($cita_id);
    
    echo json_encode([
        'success' => true,
        'message' => $resultado
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida'
    ]);
}
?> 