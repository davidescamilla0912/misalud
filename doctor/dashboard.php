<?php //vista de dashboard doctor
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'doctor') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';
require_once '../models/Doctor.php';
require_once '../models/Horario.php';

$database = new Database();
$db = $database->getConnection();

$doctor = new Doctor($db);
$horario = new Horario($db);
$doctor->id = $_SESSION['usuario_id'];

// Mensajes de éxito/error
$mensaje = '';
if(isset($_SESSION['success'])) {
    $mensaje = '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])) {
    $mensaje = '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Doctor - PMISalud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Panel del Doctor</h2>
            <a href="../controllers/logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        <?php echo $mensaje; ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Agregar Horario Disponible</div>
                    <div class="card-body">
                        <form action="../controllers/horario.php" method="POST">
                            <input type="hidden" name="action" value="crear">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                            </div>
                            <div class="mb-3">
                                <label for="hora" class="form-label">Hora</label>
                                <input type="time" class="form-control" id="hora" name="hora" required>
                            </div>
                            <div class="mb-3">
                                <label for="cupos" class="form-label">Cupos Disponibles</label>
                                <input type="number" class="form-control" id="cupos" name="cupos_disponibles" min="1" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar Horario</button>
                        </form>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">Mis Horarios Disponibles</div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Cupos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $horarios = $doctor->obtenerHorarios();
                                while($row = $horarios->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($row['hora'])); ?></td>
                                    <td><?php echo $row['cupos_disponibles'] ?? '-'; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">Citas Agendadas</div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $citas = $doctor->obtenerCitasAgendadas();
                                while($row = $citas->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($row['hora'])); ?></td>
                                    <td><?php echo ucfirst($row['estado']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 