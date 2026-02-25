<?php
/**
 * TecnoFutura Academy - Archivo de Configuración
 * Configuración general de la plataforma
 */

// Configuración del sitio
define('SITE_NAME', 'TecnoFutura Academy');
define('SITE_DESCRIPTION', 'Plataforma de aprendizaje especializada en Arduino, electrónica y programación de bajo nivel');

// Detectar la URL base dinámicamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
define('SITE_URL', $protocol . $host);

// Información de contacto
define('CONTACT_EMAIL', 'contacto@tecnofutura.academy');
define('CONTACT_PHONE', '+52 123 456 7890');
define('FACEBOOK_URL', '#');
define('TWITTER_URL', '#');
define('INSTAGRAM_URL', '#');
define('LINKEDIN_URL', '#');

// Configuración de rutas relativas desde la raíz del sitio
define('BASE_PATH', __DIR__ . '/..');
define('CSS_PATH', '/css');
define('JS_PATH', '/js');
define('IMG_PATH', '/img');
define('LMS_URL', '/lms');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de caracteres
header('Content-Type: text/html; charset=UTF-8');
?>
