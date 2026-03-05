<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/backend/auth/middleware.php';

// Verificar que sea un profesor
requiereRol(['PROFESOR']);

header('Content-Type: application/json');

echo json_encode([
    'session_data' => [
        'usuario_id' => $_SESSION['usuario_id'] ?? 'NO EXISTE',
        'usuario_nombre' => $_SESSION['usuario_nombre'] ?? 'NO EXISTE',
        'usuario_rol' => $_SESSION['usuario_rol'] ?? 'NO EXISTE',
        'usuario_rol_id' => $_SESSION['usuario_rol_id'] ?? 'NO EXISTE',
        'info_adicional' => $_SESSION['info_adicional'] ?? 'NO EXISTE'
    ],
    'id_docente' => $_SESSION['info_adicional']['id_docente'] ?? 'NO EXISTE EN info_adicional'
], JSON_PRETTY_PRINT);
