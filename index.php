<?php
require_once 'functions.php';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            if (login($_POST['email'], $_POST['password'])) {
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Credenciales inválidas";
            }
        } elseif ($_POST['action'] === 'register') {
            if (register(
                $_POST['nombre'],
                $_POST['apellido'],
                $_POST['email'],
                $_POST['password'],
                $_POST['tipo_usuario'],
                $_POST['especialidad'] ?? null,
                $_POST['ubicacion'] ?? null
            )) {
                $success = "Registro exitoso. Por favor, inicie sesión.";
            } else {
                $error = "Error al registrar el usuario";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMI Salud - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .logo-title {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .logo-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #007bff;
            letter-spacing: 1px;
        }
        .logo-title span {
            font-size: 1.15rem;
            color: #555;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 4px 32px rgba(0,0,0,0.13);
        }
        .card-header {
            background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
            color: #fff;
            border-radius: 1.5rem 1.5rem 0 0;
            font-weight: 600;
            font-size: 1.4rem;
            text-align: center;
            letter-spacing: 1px;
            padding: 1.5rem 1rem 1rem 1rem;
        }
        .nav-tabs {
            border-bottom: none;
            justify-content: center;
            margin-bottom: -0.5rem;
        }
        .nav-tabs .nav-link {
            border: none;
            border-radius: 2rem 2rem 0 0;
            color: #fff;
            background: transparent;
            font-weight: 500;
            font-size: 1.15rem;
            margin: 0 0.7rem;
            padding: 0.7rem 2.2rem;
            transition: background 0.2s, color 0.2s;
        }
        .nav-tabs .nav-link.active {
            background: #fff;
            color: #007bff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .card-body {
            padding: 2.2rem 2.2rem 2rem 2.2rem;
        }
        .form-label {
            font-weight: 500;
            font-size: 1.08rem;
        }
        .form-control {
            border-radius: 2rem;
            padding: 1rem 1.3rem;
            font-size: 1.13rem;
        }
        .btn-primary {
            border-radius: 2rem;
            font-weight: 600;
            width: 100%;
            margin-top: 0.7rem;
            transition: background 0.2s, box-shadow 0.2s;
            font-size: 1.18rem;
            padding: 0.8rem 0;
        }
        .btn-primary:hover {
            background: #0056b3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .toggle-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
            font-size: 1.08rem;
        }
        .alert {
            border-radius: 1rem;
            font-size: 1.08rem;
            margin-top: 1rem;
        }
        #doctor-fields {
            background: #f8f9fa;
            border-radius: 1rem;
            padding: 1.2rem 1rem 0.7rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e3e6ee;
        }
        @media (max-width: 767px) {
            .container {
                max-width: 98vw;
                padding: 0 0.5rem;
            }
            .card-header {
                font-size: 1.1rem;
                padding: 1.1rem 0.5rem 0.7rem 0.5rem;
            }
            .logo-title h1 {
                font-size: 1.3rem;
            }
            .card-body {
                padding: 1.2rem 0.7rem 1rem 0.7rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="main-wrapper">
        <div class="logo-title">
            <h1><i class="bi bi-calendar2-heart"></i> Sistema de Citas Médicas</h1>
            <span>Accede o regístrate para gestionar tus citas</span>
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#login">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#register">Registro</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Login Form -->
                                <div class="tab-pane fade show active" id="login">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="login">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                    </form>
                                </div>

                                <!-- Register Form -->
                                <div class="tab-pane fade" id="register">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="register">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="apellido" class="form-label">Apellido</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                                            <select class="form-control" id="tipo_usuario" name="tipo_usuario" required>
                                                <option value="paciente">Paciente</option>
                                                <option value="doctor">Doctor</option>
                                            </select>
                                        </div>
                                        <div id="doctor-fields" style="display: none;">
                                            <div class="mb-3">
                                                <label for="especialidad" class="form-label">Especialidad</label>
                                                <input type="text" class="form-control" id="especialidad" name="especialidad">
                                            </div>
                                            <div class="mb-3">
                                                <label for="ubicacion" class="form-label">Ubicación</label>
                                                <input type="text" class="form-control" id="ubicacion" name="ubicacion">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Registrarse</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('tipo_usuario').addEventListener('change', function() {
            const doctorFields = document.getElementById('doctor-fields');
            doctorFields.style.display = this.value === 'doctor' ? 'block' : 'none';
        });
    </script>
</body>
</html> 