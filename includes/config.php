<?php
/**
 * TecnoFutura Academy - Archivo de Configuración
 * Configuración general de la plataforma
 */

// Configuración del sitio
define('SITE_NAME', 'TecnoFutura Academy');
define('SITE_DESCRIPTION', 'Plataforma de aprendizaje especializada en Arduino, electrónica y programación de bajo nivel');

// Detectar la URL base dinámicamente (compatible con Windows, Mac y Linux)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Detectar la carpeta base del proyecto automáticamente
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_folder = ($script_name == '/' || $script_name == '\\') ? '' : $script_name;

// URL completa del sitio incluyendo la carpeta del proyecto
define('SITE_URL', $protocol . $host . $base_folder);

// Información de contacto
define('CONTACT_EMAIL', 'contacto@tecnofutura.academy');
define('CONTACT_PHONE', '+52 123 456 7890');
define('FACEBOOK_URL', '#');
define('TWITTER_URL', '#');
define('INSTAGRAM_URL', '#');
define('LINKEDIN_URL', '#');

// Configuración de rutas (relativas a la carpeta del proyecto)
define('BASE_PATH', __DIR__ . '/..');
define('CSS_PATH', $base_folder . '/css');
define('JS_PATH', $base_folder . '/js');
define('IMG_PATH', $base_folder . '/img');
define('LMS_URL', $base_folder . '/lms');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de caracteres
header('Content-Type: text/html; charset=UTF-8');
?>
