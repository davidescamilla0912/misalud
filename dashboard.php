<?php
require_once 'functions.php';
require_once 'citas_functions.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$usuario = getCurrentUser();
$conn = getConnection();

// Mostrar mensajes de éxito o error
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Citas Médicas - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fa;
        }
        .navbar {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: #f8f9fa;
            border-bottom: none;
            border-radius: 1rem 1rem 0 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .btn-primary, .btn-info {
            border-radius: 2rem;
            font-weight: 500;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover, .btn-info:hover {
            background: #0056b3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .btn-danger {
            border-radius: 2rem;
            font-weight: 500;
        }
        .alert {
            border-radius: 1rem;
            font-size: 1rem;
            margin-top: 1rem;
        }
        .form-label {
            font-weight: 500;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .card-subtitle {
            font-size: 1rem;
        }
        .navbar-text {
            font-size: 1.1rem;
        }
        .modal-content {
            border-radius: 1rem;
        }
        .modal-title {
            font-weight: 600;
        }
        @media (max-width: 767px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            .card-title {
                font-size: 1rem;
            }
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: #fff !important;
            }
        }
        .print-only {
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary no-print">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema de Citas Médicas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                </ul>
                <div class="navbar-text me-3">
                    Bienvenido, <?php echo isset($usuario['nombre']) && isset($usuario['apellido']) ? htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) : 'Usuario'; ?>
                </div>
                <a href="logout.php" class="btn btn-light">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Nueva sección de Citas del Día -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Citas proximas </h5>
                    </div>
                    <div class="card-body">
                        <?php echo mostrarCitasDelDia(); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($usuario['tipo_usuario'] === 'doctor'): ?>
            <!-- Panel de Doctor -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Gestionar Horarios</h5>
                        </div>
                        <div class="card-body">
                            <form action="procesar_horario.php" method="POST">
                                <input type="hidden" name="action" value="crear">
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="hora" class="form-label">Hora</label>
                                    <input type="time" class="form-control" id="hora" name="hora" required>
                                </div>
                                <div class="mb-3">
                                    <label for="cupos" class="form-label">Cupos Disponibles</label>
                                    <input type="number" class="form-control" id="cupos" name="cupos_disponibles" min="1" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Crear Horario</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Citas Agendadas</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $conn->prepare("
                                SELECT c.*, u.nombre, u.apellido, h.fecha, h.hora 
                                FROM citas c 
                                JOIN usuarios u ON c.paciente_id = u.id 
                                JOIN horarios_disponibles h ON c.horario_id = h.id 
                                WHERE h.doctor_id = (SELECT id FROM doctores WHERE usuario_id = ?)
                                ORDER BY h.fecha, h.hora
                            ");
                            $stmt->execute([$usuario['id']]);
                            $citas = $stmt->fetchAll();

                            if ($citas && count($citas) > 0):
                                foreach ($citas as $cita):
                            ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <?php echo date('d/m/Y', strtotime($cita['fecha'])); ?> 
                                            <?php echo date('H:i', strtotime($cita['hora'])); ?>
                                        </h6>
                                        <p class="card-text">
                                            Paciente: <?php echo htmlspecialchars($cita['nombre'] . ' ' . $cita['apellido']); ?><br>
                                            Estado: <?php echo ucfirst($cita['estado']); ?>
                                        </p>
                                        <form action="procesar_cita.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="actualizar_estado">
                                            <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                                            <select name="estado" class="form-select form-select-sm d-inline-block w-auto">
                                                <option value="pendiente" <?php echo $cita['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                <option value="confirmada" <?php echo $cita['estado'] === 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                                                <option value="cancelada" <?php echo $cita['estado'] === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                                <option value="completada" <?php echo $cita['estado'] === 'completada' ? 'selected' : ''; ?>>Completada</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                                        </form>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                                <p class="text-muted">No hay citas agendadas.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($usuario['tipo_usuario'] === 'paciente'): ?>
            <!-- Panel de Paciente -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Horarios Disponibles</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $conn->query("
                                SELECT h.*, d.especialidad, u.nombre, u.apellido 
                                FROM horarios_disponibles h 
                                JOIN doctores d ON h.doctor_id = d.id 
                                JOIN usuarios u ON d.usuario_id = u.id 
                                WHERE h.cupos_disponibles > 0 
                                AND h.fecha >= CURDATE() 
                                ORDER BY h.fecha, h.hora
                            ");
                            $horarios = $stmt->fetchAll();

                            if ($horarios && count($horarios) > 0):
                                foreach ($horarios as $horario):
                            ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <?php echo date('d/m/Y', strtotime($horario['fecha'])); ?> 
                                            <?php echo date('H:i', strtotime($horario['hora'])); ?>
                                        </h6>
                                        <p class="card-text">
                                            Doctor: <?php echo htmlspecialchars($horario['nombre'] . ' ' . $horario['apellido']); ?><br>
                                            Especialidad: <?php echo htmlspecialchars($horario['especialidad']); ?><br>
                                            Cupos disponibles: <?php echo $horario['cupos_disponibles']; ?>
                                        </p>
                                        <form action="procesar_cita.php" method="POST">
                                            <input type="hidden" name="action" value="agendar">
                                            <input type="hidden" name="horario_id" value="<?php echo $horario['id']; ?>">
                                            <button type="submit" class="btn btn-primary">Agendar Cita</button>
                                        </form>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                                <p class="text-muted">No hay horarios disponibles.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Mis Citas</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $conn->prepare("
                                SELECT c.*, h.fecha, h.hora, d.especialidad, u.nombre, u.apellido 
                                FROM citas c 
                                JOIN horarios_disponibles h ON c.horario_id = h.id 
                                JOIN doctores d ON h.doctor_id = d.id 
                                JOIN usuarios u ON d.usuario_id = u.id 
                                WHERE c.paciente_id = ? 
                                ORDER BY h.fecha, h.hora
                            ");
                            $stmt->execute([$usuario['id']]);
                            $citas = $stmt->fetchAll();

                            if ($citas && count($citas) > 0):
                                foreach ($citas as $cita):
                            ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <?php echo date('d/m/Y', strtotime($cita['fecha'])); ?> 
                                            <?php echo date('H:i', strtotime($cita['hora'])); ?>
                                        </h6>
                                        <p class="card-text">
                                            Doctor: <?php echo htmlspecialchars($cita['nombre'] . ' ' . $cita['apellido']); ?><br>
                                            Especialidad: <?php echo htmlspecialchars($cita['especialidad']); ?><br>
                                            Estado: <?php echo ucfirst($cita['estado']); ?>
                                        </p>
                                        <?php if ($cita['estado'] === 'pendiente'): ?>
                                            <form action="procesar_cita.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="cancelar">
                                                <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                                                <button type="submit" class="btn btn-danger">Cancelar Cita</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($cita['estado'] === 'confirmada'): ?>
                                            <button onclick="imprimirRecordatorio(<?php echo htmlspecialchars(json_encode([
                                                'fecha' => $cita['fecha'],
                                                'hora' => $cita['hora'],
                                                'doctor' => $cita['nombre'] . ' ' . $cita['apellido'],
                                                'especialidad' => $cita['especialidad'],
                                                'paciente' => $usuario['nombre'] . ' ' . $usuario['apellido']
                                            ])); ?>)" class="btn btn-info">
                                                <i class="bi bi-printer"></i> Imprimir Recordatorio
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                                <p class="text-muted">No tienes citas agendadas.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para el recordatorio -->
    <div class="modal fade" id="recordatorioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recordatorio de Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="recordatorioContenido">
                    <!-- El contenido se llenará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function imprimirRecordatorio(datos) {
            const contenido = `
                <div class="print-only">
                    <h3 class="text-center mb-4">Recordatorio de Cita Médica</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Detalles de la Cita</h5>
                            <p><strong>Paciente:</strong> ${datos.paciente}</p>
                            <p><strong>Doctor:</strong> ${datos.doctor}</p>
                            <p><strong>Especialidad:</strong> ${datos.especialidad}</p>
                            <p><strong>Fecha:</strong> ${new Date(datos.fecha).toLocaleDateString()}</p>
                            <p><strong>Hora:</strong> ${datos.hora}</p>
                            <p class="mt-4"><em>Por favor, llegue 15 minutos antes de su cita.</em></p>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('recordatorioContenido').innerHTML = contenido;
            new bootstrap.Modal(document.getElementById('recordatorioModal')).show();
        }

        function cancelarCita(citaId) {
            if (confirm('¿Está seguro de que desea cancelar esta cita?')) {
                fetch('procesar_cancelacion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'cita_id=' + citaId
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
            }
        }
    </script>
</body>
</html> 