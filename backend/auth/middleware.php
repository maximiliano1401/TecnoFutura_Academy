<?php
/**
 * Middleware de Autenticación
 * Verifica que el usuario esté autenticado antes de acceder a rutas protegidas
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Usuario.php';

function requiereAutenticacion($redirigir = true) {
    if (!Usuario::estaAutenticado()) {
        if ($redirigir) {
            $base = defined('SITE_URL') ? SITE_URL : '';
            header('Location: ' . $base . '/login.php?error=sesion_expirada');
            exit;
        }
        return false;
    }
    return true;
}

function requiereRol($roles_permitidos, $redirigir = true) {
    if (!Usuario::estaAutenticado()) {
        if ($redirigir) {
            header('Location: /login.php');
            exit;
        }
        return false;
    }
    
    if (!is_array($roles_permitidos)) {
        $roles_permitidos = [$roles_permitidos];
    }
    
    $rol_actual = $_SESSION['usuario_rol'] ?? '';
    
    if (!in_array($rol_actual, $roles_permitidos)) {
        if ($redirigir) {
            $base = defined('SITE_URL') ? SITE_URL : '';
            header('Location: ' . $base . '/login.php?error=acceso_denegado');
            exit;
        }
        return false;
    }
    
    return true;
}
