<!DOCTYPE html>
<html lang="es">          //estructura de la pagina
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'PMISalud'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">PMISalud</a>
            <?php if (isset($user)): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard.php">Dashboard</a>
                    </li>
                    <?php if ($user['tipo_usuario'] === 'administrador'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/usuarios.php">Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/doctores.php">Doctores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/citas.php">Citas</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="navbar-nav">
                    <span class="nav-item nav-link"><?php echo $user['nombre'] . ' ' . $user['apellido']; ?></span>
                    <a class="nav-link" href="/logout.php">Cerrar Sesión</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container mt-4">
        <?php echo $messages ?? ''; ?>
        <?php echo $content ?? ''; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 