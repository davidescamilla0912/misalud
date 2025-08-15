<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/conexion.php';
require_once 'models/Cita.php';
require_once 'models/PDFCita.php';

$database = new Conexion();
$db = $database->getConnection();

$cita = new Cita($db);

if(isset($_GET['id'])) {
    $cita->id = $_GET['id'];
    $datos_cita = $cita->obtenerDetallesCita();

    if($datos_cita) {
        // Verificar que el usuario sea el paciente de la cita
        if($datos_cita['paciente_id'] == $_SESSION['usuario_id']) {
            $pdf = new PDFCita();
            $pdf->generarPDF($datos_cita);
            $pdf->Output('Cita_Medica.pdf', 'D');
            exit();
        }
    }
}

$_SESSION['error'] = "No se pudo generar el PDF de la cita";
header("Location: dashboard.php");
exit();
?> 