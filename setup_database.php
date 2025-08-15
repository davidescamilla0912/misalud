<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Conectar sin seleccionar base de datos
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear la base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS pmisalud");
    $pdo->exec("USE pmisalud");
    
    // Crear tabla usuarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        apellido VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        tipo_usuario ENUM('administrador', 'doctor', 'paciente') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Crear tabla doctores
    $pdo->exec("CREATE TABLE IF NOT EXISTS doctores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        especialidad VARCHAR(100) NOT NULL,
        ubicacion VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )");
    
    // Crear tabla horarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS horarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_id INT NOT NULL,
        fecha DATE NOT NULL,
        hora TIME NOT NULL,
        cupos_disponibles INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (doctor_id) REFERENCES doctores(id) ON DELETE CASCADE
    )");
    
    // Crear tabla citas
    $pdo->exec("CREATE TABLE IF NOT EXISTS citas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paciente_id INT NOT NULL,
        horario_id INT NOT NULL,
        estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') NOT NULL DEFAULT 'pendiente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (paciente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (horario_id) REFERENCES horarios(id) ON DELETE CASCADE
    )");
    
    // Verificar si existe el usuario administrador
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'administrador'");
    if ($stmt->fetchColumn() == 0) {
        // Insertar usuario administrador por defecto
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, tipo_usuario) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            'Admin',
            'Sistema',
            'admin@pmisalud.com',
            password_hash('password', PASSWORD_DEFAULT),
            'administrador'
        ]);
        echo "Usuario administrador creado correctamente.\n";
    }
    
    echo "Base de datos y tablas creadas correctamente.\n";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?> 