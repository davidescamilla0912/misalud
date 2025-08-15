<?php   //vista de doctores
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/database.php';
require_once '../models/Usuario.php';
require_once '../models/Doctor.php';

$database = new Database();
$db = $database->getConnection();

$usuario = new Usuario($db);
$doctor = new Doctor($db);

$mensaje = '';

// Procesar eliminación de doctor
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    if($usuario->eliminar($id)) {
        $mensaje = '<div class="alert alert-success">Doctor eliminado correctamente</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al eliminar el doctor</div>';
    }
}

// Obtener lista de doctores
$doctores = $doctor->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Doctores - PMISalud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Gestión de Doctores</h2>
        <?php echo $mensaje; ?>
        
        <div class="mb-3">
            <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Especialidad</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $doctores->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['especialidad']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar este doctor?');">
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