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

// Detectar la URL base dinámicamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
define('SITE_URL', $protocol . $host);

// Configuración de rutas
define('BASE_PATH', dirname(dirname(__DIR__)));
define('BACKEND_PATH', BASE_PATH . '/backend');
define('CSS_PATH', '/css');
define('JS_PATH', '/js');
define('IMG_PATH', '/img');
define('LMS_URL', '/lms');

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 6);
define('SESSION_LIFETIME', 3600); // 1 hora en segundos

// Incluir archivo de base de datos
require_once BACKEND_PATH . '/config/database.php';
