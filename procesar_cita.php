<?php
require_once 'functions.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$usuario = getCurrentUser();
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'agendar':
                if ($usuario['tipo_usuario'] !== 'paciente') {
                    throw new Exception("Solo los pacientes pueden agendar citas");
                }

                // Verificar disponibilidad
                $stmt = $conn->prepare("
                    SELECT cupos_disponibles 
                    FROM horarios_disponibles 
                    WHERE id = ? AND cupos_disponibles > 0
                ");
                $stmt->execute([$_POST['horario_id']]);
                $horario = $stmt->fetch();

                if (!$horario) {
                    throw new Exception("El horario seleccionado no está disponible");
                }

                // Verificar si ya tiene una cita en ese horario
                $stmt = $conn->prepare("
                    SELECT COUNT(*) 
                    FROM citas c 
                    JOIN horarios_disponibles h ON c.horario_id = h.id 
                    WHERE c.paciente_id = ? AND h.fecha = (SELECT fecha FROM horarios_disponibles WHERE id = ?)
                ");
                $stmt->execute([$usuario['id'], $_POST['horario_id']]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("Ya tienes una cita agendada para esta fecha");
                }

                // Crear la cita
                $stmt = $conn->prepare("
                    INSERT INTO citas (paciente_id, horario_id) 
                    VALUES (?, ?)
                ");
                $stmt->execute([$usuario['id'], $_POST['horario_id']]);

                // Actualizar cupos disponibles
                $stmt = $conn->prepare("
                    UPDATE horarios_disponibles 
                    SET cupos_disponibles = cupos_disponibles - 1 
                    WHERE id = ?
                ");
                $stmt->execute([$_POST['horario_id']]);

                $_SESSION['success'] = "Cita agendada correctamente";
                break;

            case 'cancelar':
                if ($usuario['tipo_usuario'] !== 'paciente') {
                    throw new Exception("Solo los pacientes pueden cancelar sus citas");
                }

                // Verificar que la cita pertenece al paciente
                $stmt = $conn->prepare("
                    SELECT c.*, h.id as horario_id 
                    FROM citas c 
                    JOIN horarios_disponibles h ON c.horario_id = h.id 
                    WHERE c.id = ? AND c.paciente_id = ? AND c.estado = 'pendiente'
                ");
                $stmt->execute([$_POST['cita_id'], $usuario['id']]);
                $cita = $stmt->fetch();

                if (!$cita) {
                    throw new Exception("No se encontró la cita o no se puede cancelar");
                }

                // Actualizar estado de la cita
                $stmt = $conn->prepare("UPDATE citas SET estado = 'cancelada' WHERE id = ?");
                $stmt->execute([$_POST['cita_id']]);

                // Devolver el cupo
                $stmt = $conn->prepare("
                    UPDATE horarios_disponibles 
                    SET cupos_disponibles = cupos_disponibles + 1 
                    WHERE id = ?
                ");
                $stmt->execute([$cita['horario_id']]);

                $_SESSION['success'] = "Cita cancelada correctamente";
                break;

            case 'actualizar_estado':
                if ($usuario['tipo_usuario'] !== 'doctor') {
                    throw new Exception("Solo los doctores pueden actualizar el estado de las citas");
                }

                // Verificar que la cita pertenece al doctor
                $stmt = $conn->prepare("
                    SELECT c.* 
                    FROM citas c 
                    JOIN horarios_disponibles h ON c.horario_id = h.id 
                    JOIN doctores d ON h.doctor_id = d.id 
                    WHERE c.id = ? AND d.usuario_id = ?
                ");
                $stmt->execute([$_POST['cita_id'], $usuario['id']]);
                $cita = $stmt->fetch();

                if (!$cita) {
                    throw new Exception("No se encontró la cita");
                }

                // Actualizar estado
                $stmt = $conn->prepare("UPDATE citas SET estado = ? WHERE id = ?");
                $stmt->execute([$_POST['estado'], $_POST['cita_id']]);

                $_SESSION['success'] = "Estado de la cita actualizado correctamente";
                break;

            default:
                throw new Exception("Acción no válida");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header("Location: dashboard.php");
exit();
?> 