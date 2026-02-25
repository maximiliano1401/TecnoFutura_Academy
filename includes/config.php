<?php
/**
 * TecnoFutura Academy - Archivo de Configuración
 * Configuración general de la plataforma
 */

// Configuración del sitio
define('SITE_NAME', 'TecnoFutura Academy');
define('SITE_DESCRIPTION', 'Plataforma de aprendizaje especializada en Arduino, electrónica y programación de bajo nivel');
define('SITE_URL', 'http://localhost/TecnoFutura_Academy');

// Información de contacto
define('CONTACT_EMAIL', 'contacto@tecnofutura.academy');
define('CONTACT_PHONE', '+52 123 456 7890');
define('FACEBOOK_URL', '#');
define('TWITTER_URL', '#');
define('INSTAGRAM_URL', '#');
define('LINKEDIN_URL', '#');

// Configuración de rutas
define('BASE_PATH', __DIR__ . '/..');
define('CSS_PATH', SITE_URL . '/css');
define('JS_PATH', SITE_URL . '/js');
define('IMG_PATH', SITE_URL . '/img');
define('LMS_URL', SITE_URL . '/lms');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de caracteres
header('Content-Type: text/html; charset=UTF-8');
?>
