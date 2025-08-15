<?php
require_once 'config.php';

try {
    // Conectar al servidor MySQL sin seleccionar base de datos
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Eliminar la base de datos si existe
    $conn->exec("DROP DATABASE IF EXISTS sistema_citas_medicasp");
    echo "Base de datos anterior eliminada (si existía)<br>";

    // Crear la base de datos
    $conn->exec("CREATE DATABASE sistema_citas_medicasp");
    echo "Base de datos creada correctamente<br>";

    // Seleccionar la base de datos
    $conn->exec("USE sistema_citas_medicasp");

    // Crear tabla de usuarios
    $conn->exec("CREATE TABLE usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL,
        apellido VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        tipo_usuario ENUM('administrador', 'doctor', 'paciente') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Tabla usuarios creada correctamente<br>";

    // Crear tabla de doctores
    $conn->exec("CREATE TABLE doctores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        especialidad VARCHAR(100) NOT NULL,
        ubicacion VARCHAR(255),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )");
    echo "Tabla doctores creada correctamente<br>";

    // Crear tabla de horarios disponibles
    $conn->exec("CREATE TABLE horarios_disponibles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INT NOT NULL,
        fecha DATE NOT NULL,
        hora TIME NOT NULL,
        cupos_disponibles INT NOT NULL,
        FOREIGN KEY (doctor_id) REFERENCES doctores(id) ON DELETE CASCADE
    )");
    echo "Tabla horarios_disponibles creada correctamente<br>";

    // Crear tabla de citas
    $conn->exec("CREATE TABLE citas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paciente_id INT NOT NULL,
        horario_id INT NOT NULL,
        estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') DEFAULT 'pendiente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (paciente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (horario_id) REFERENCES horarios_disponibles(id) ON DELETE CASCADE
    )");
    echo "Tabla citas creada correctamente<br>";

    // Insertar usuario administrador por defecto
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password, tipo_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Admin', 'Sistema', 'admin@sistema.com', $password_hash, 'administrador']);
    echo "Usuario administrador creado correctamente<br>";

    echo "<br>¡Base de datos configurada exitosamente!<br>";
    echo "Credenciales de administrador:<br>";
    echo "Email: admin@sistema.com<br>";
    echo "Contraseña: admin123<br>";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 