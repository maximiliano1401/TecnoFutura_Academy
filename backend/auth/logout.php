<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Usuario::logout();
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}
header('Location: ' . SITE_URL . '/');
exit;
