<?php
require('fpdf/fpdf.php');

class PDFCita extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Recordatorio de Cita Médica', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function generarPDF($datos_cita) {
        $this->AddPage();
        
        // Información del Doctor
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Doctor:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $datos_cita['nombre_doctor'] . ' ' . $datos_cita['apellido_doctor'], 0, 1);
        $this->Cell(0, 10, 'Especialidad: ' . $datos_cita['especialidad'], 0, 1);
        $this->Ln(5);

        // Información del Paciente
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Paciente:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $datos_cita['nombre_paciente'] . ' ' . $datos_cita['apellido_paciente'], 0, 1);
        $this->Ln(5);

        // Detalles de la Cita
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Detalles de la Cita:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Fecha: ' . date('d/m/Y', strtotime($datos_cita['fecha'])), 0, 1);
        $this->Cell(0, 10, 'Hora: ' . date('H:i', strtotime($datos_cita['hora'])), 0, 1);
        $this->Cell(0, 10, 'Ubicación: ' . $datos_cita['ubicacion'], 0, 1);
        $this->Ln(10);

        // Instrucciones
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Instrucciones:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, 'Por favor, llegue 15 minutos antes de su cita. Traiga su documento de identidad y cualquier documentación médica relevante.');
    }
}
?> 