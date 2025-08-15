<?php
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'paciente') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Paciente - PMISalud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Bienvenido, Paciente</h2>
        <p>Este es su panel principal. Aquí podrá agendar y ver sus citas.</p>
        <a href="../controllers/logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</body>
</html> 