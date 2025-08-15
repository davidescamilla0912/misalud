<?php
/**
 * Archivo: public/index.php
 * Descripción: Punto de entrada principal de la aplicación
 * Funciones:
 *   - Verifica si el usuario está autenticado
 *   - Redirige según el tipo de usuario:
 *     * Administrador -> /admin/dashboard.php
 *     * Doctor -> /doctor/dashboard.php
 *     * Paciente -> /paciente/dashboard.php
 *   - Si no está autenticado, redirige a login
 */

require_once __DIR__ . '/../app/helpers/functions.php';

// Redirigir a la página de inicio si no está logueado
if (!isLoggedIn()) {
    header("Location: /login.php");
    exit();
}

// Redirigir según el tipo de usuario
$user = getCurrentUser();
switch ($user['tipo_usuario']) {
    case 'administrador':
        header("Location: /admin/dashboard.php");
        break;
    case 'doctor':
        header("Location: /doctor/dashboard.php");
        break;
    case 'paciente':
        header("Location: /paciente/dashboard.php");
        break;
    default:
        header("Location: /login.php");
}
exit(); 