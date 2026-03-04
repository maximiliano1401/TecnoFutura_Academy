<?php
/**
 * Configuración General del Sistema
 * TecnoFutura Academy
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (cambiar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración del sitio
define('SITE_NAME', 'TecnoFutura Academy');
define('SITE_DESCRIPTION', 'Plataforma de aprendizaje especializada en Arduino, electrónica y programación de bajo nivel');

// Detectar la URL base dinámicamente (Compatible con cualquier SO y estructura de carpetas)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Detectar el directorio base del proyecto automáticamente
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$script_parts = explode('/', trim($script_name, '/'));

// Buscar la carpeta TecnoFutura_Academy en la ruta
$base_folder = '';
foreach ($script_parts as $part) {
    if (stripos($part, 'TecnoFutura') !== false || $part === 'TecnoFutura_Academy') {
        $base_folder = '/' . $part;
        break;
    }
}

// Si no se encuentra, usar detección genérica
if (empty($base_folder)) {
    // Obtener solo el primer directorio después de la raíz
    $base_folder = !empty($script_parts[0]) ? '/' . $script_parts[0] : '';
}

define('SITE_URL', $protocol . $host . $base_folder);

// Configuración de rutas
define('BASE_PATH', dirname(dirname(__DIR__)));
define('BACKEND_PATH', BASE_PATH . '/backend');
define('CSS_PATH', $base_folder . '/css');
define('JS_PATH', $base_folder . '/js');
define('IMG_PATH', $base_folder . '/img');
define('LMS_URL', $base_folder . '/lms');

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 6);
define('SESSION_LIFETIME', 3600); // 1 hora en segundos

// Incluir archivo de base de datos
require_once BACKEND_PATH . '/config/database.php';
