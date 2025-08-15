<?php
require_once 'config.php';

try {
    $conn = getConnection();
    
    // Verificar tablas
    $tables = ['usuarios', 'doctores', 'horarios', 'citas'];
    $missing_tables = [];
    
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $missing_tables[] = $table;
        }
    }
    
    if (empty($missing_tables)) {
        echo "Todas las tablas existen correctamente.\n";
        
        // Verificar estructura de usuarios
        $stmt = $conn->query("DESCRIBE usuarios");
        echo "\nEstructura de la tabla usuarios:\n";
        while ($row = $stmt->fetch()) {
            echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
        
        // Verificar usuario administrador
        $stmt = $conn->query("SELECT * FROM usuarios WHERE tipo_usuario = 'administrador'");
        if ($stmt->rowCount() > 0) {
            echo "\nUsuario administrador existe.\n";
        } else {
            echo "\nADVERTENCIA: No existe usuario administrador.\n";
        }
        
    } else {
        echo "Faltan las siguientes tablas:\n";
        foreach ($missing_tables as $table) {
            echo "- $table\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
}
?> 