<?php     //muestran las citas filtradas por usuario solo si es paciente   hola
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';
require_once '../models/Cita.php';
require_once __DIR__ . '/../app/controllers/CitaController.php';

$database = new Database();
$db = $database->getConnection();

$cita = new Cita($db);

$controller = new CitaController();

$mensaje = '';

// Procesar eliminación de cita
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    if($cita->eliminar($id)) {
        $mensaje = '<div class="alert alert-success">Cita eliminada correctamente</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al eliminar la cita</div>';
    }
}

// Obtener lista de citas
$citas = $cita->obtenerTodas();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'eliminar':
            $controller->eliminar();
            break;
        default:
            echo $controller->index();
    }
} else {
    echo $controller->index();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas - PMISalud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Gestión de Citas</h2>
        <?php echo $mensaje; ?>
        
        <div class="mb-3">
            <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Doctor</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $citas->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_paciente'] . ' ' . $row['apellido_paciente']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_doctor'] . ' ' . $row['apellido_doctor']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                        <td><?php echo date('H:i', strtotime($row['hora'])); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $row['estado'] === 'confirmada' ? 'success' : ($row['estado'] === 'pendiente' ? 'warning' : 'danger'); ?>">
                                <?php echo ucfirst($row['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar esta cita?');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 