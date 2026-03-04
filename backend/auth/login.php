<?php
/**
 * Procesar Login
 * Backend - Lógica de negocio para autenticación
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Usuario.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    
    if (empty($correo) || empty($contrasena)) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor complete todos los campos'
        ]);
        exit;
    }
    
    $usuario = new Usuario();
    $resultado = $usuario->login($correo, $contrasena);
    
    echo json_encode($resultado);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
