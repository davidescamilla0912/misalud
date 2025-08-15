<?php   //vista de usuarios
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/conexion.php';
require_once '../models/Usuario.php';

$database = new Conexion();
$db = $database->getConnection();

$usuario = new Usuario($db);

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'eliminar' && isset($_POST['usuario_id'])) {
        if ($usuario->eliminar($_POST['usuario_id'])) {
            $_SESSION['success'] = "Usuario eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el usuario";
        }
    }
}

// Obtener lista de usuarios
$usuarios = $usuario->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Citas Médicas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <h1>Gestión de Usuarios</h1>
            <nav>
                <a href="../dashboard.php" class="btn-secondary">Volver al Dashboard</a>
                <a href="../controllers/logout.php" class="btn-logout">Cerrar Sesión</a>
            </nav>
        </header>

        <main class="dashboard-content">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <section class="dashboard-section">
                <h2>Lista de Usuarios</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['apellido']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['tipo_usuario']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_registro'])); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="eliminar">
                                            <input type="hidden" name="usuario_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn-danger" onclick="return confirm('¿Está seguro de eliminar este usuario?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html> 