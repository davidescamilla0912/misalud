<?php     // pagina principal que muestra luego de iniciarse sesion
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';
require_once '../models/Usuario.php';
require_once '../models/Doctor.php';
require_once '../models/Cita.php';

$database = new Database();
$db = $database->getConnection();

$usuario = new Usuario($db);
$doctor = new Doctor($db);
$cita = new Cita($db);

// Obtener estadísticas
$total_pacientes = $usuario->contarPorTipo('paciente');
$total_doctores = $usuario->contarPorTipo('doctor');
$total_citas = $cita->contarTodas();
$citas_pendientes = $cita->contarPorEstado('pendiente');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - PMISalud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Administrador</h2>
            <a href="../logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pacientes</h5>
                        <p class="card-text display-4"><?php echo $total_pacientes; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Doctores</h5>
                        <p class="card-text display-4"><?php echo $total_doctores; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Citas</h5>
                        <p class="card-text display-4"><?php echo $total_citas; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Citas Pendientes</h5>
                        <p class="card-text display-4"><?php echo $citas_pendientes; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enlaces a secciones -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Usuarios</h5>
                        <p class="card-text">Administra todos los usuarios del sistema.</p>
                        <a href="usuarios.php" class="btn btn-primary">Ver Usuarios</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Doctores</h5>
                        <p class="card-text">Administra los doctores y sus especialidades.</p>
                        <a href="doctores.php" class="btn btn-primary">Ver Doctores</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Citas</h5>
                        <p class="card-text">Administra todas las citas del sistema.</p>
                        <a href="citas.php" class="btn btn-primary">Ver Citas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 