<?php
require_once 'config.php';

try {
    $conn = getConnection();
    echo "Conexión exitosa a la base de datos!";
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?> 