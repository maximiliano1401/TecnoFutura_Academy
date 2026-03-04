<?php
/**
 * Configuración de Base de Datos
 * TecnoFutura Academy
 */

// Configuración de conexión
define('DB_HOST', 'localhost');
define('DB_NAME', 'tecnofutura_academy');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Opciones de PDO
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
