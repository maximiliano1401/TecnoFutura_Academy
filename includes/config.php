<?php
if (!isset($config_loaded)) {
    require_once __DIR__ . '/../backend/config/config.php';
    require_once __DIR__ . '/../backend/classes/Database.php';
    require_once __DIR__ . '/../backend/classes/Usuario.php';
    $config_loaded = true;
}
