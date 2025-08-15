<?php
require_once 'config.php';

// Función para obtener las citas del día o próximas 
function obtenerCitasProximas($usuario_id, $tipo_usuario) {
    try {
        $conn = getConnection();
        $sql = "SELECT * FROM vw_citas_dia";
        $params = [];

     

        if ($tipo_usuario === 'paciente') {
            $sql .= " WHERE paciente_id = ?";
            $params[] = $usuario_id;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error al obtener citas próximas: " . $e->getMessage());
        return [];
    }
}

// Función para cancelar una cita
function cancelarCita($cita_id) {
    try {
        $conn = getConnection();
        $stmt = $conn->prepare("CALL sp_cancelar_cita(?, @mensaje)");
        $stmt->execute([$cita_id]);
        
        // Obtener el mensaje de resultado
        $result = $conn->query("SELECT @mensaje as mensaje")->fetch(PDO::FETCH_ASSOC);
        return $result['mensaje'];
    } catch(PDOException $e) {
        error_log("Error al cancelar cita: " . $e->getMessage());
        return "Error al procesar la cancelación";
    }
}

// Función para mostrar las citas en una tabla HTML
function mostrarCitasDelDia() {
    // Necesitamos acceder a la sesión para obtener el usuario actual
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    $tipo_usuario = $_SESSION['tipo_usuario'] ?? null;

    if (!$usuario_id || !$tipo_usuario) {
        return '<div class="alert alert-danger">No se pudo obtener la información del usuario.</div>';
    }

    $citas = obtenerCitasProximas($usuario_id, $tipo_usuario);
    
    if (empty($citas)) {
        return '<div class="alert alert-info">No hay citas programadas para hoy.</div>';
    }
    
    $html = '<div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Doctor</th>
                    <th>Especialidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($citas as $cita) {
        $html .= '<tr>
            <td>' . date('H:i', strtotime($cita['hora'])) . '</td>
            <td>' . htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']) . '</td>
            <td>' . htmlspecialchars($cita['doctor_nombre'] . ' ' . $cita['doctor_apellido']) . '</td>
            <td>' . htmlspecialchars($cita['especialidad']) . '</td>
            <td>' . htmlspecialchars($cita['estado']) . '</td>
            <td>';
        
        if ($cita['estado'] != 'cancelada') {
            $html .= '<button class="btn btn-danger btn-sm" onclick="cancelarCita(' . $cita['cita_id'] . ')">
                Cancelar
            </button>';
        }
        
        $html .= '</td></tr>';
    }
    
    $html .= '</tbody></table></div>';
    
    return $html;
}
?> 