<?php
/**
 * Procesar Registro
 * Backend - Lógica de negocio para registro de usuarios
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Usuario.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_usuario = $_POST['tipo_usuario'] ?? 'alumno';
    
    $datos = [
        'nombre_completo' => $_POST['nombre_completo'] ?? '',
        'correo_electronico' => $_POST['correo_electronico'] ?? '',
        'contrasena' => $_POST['contrasena'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
    ];
    
    // Determinar el rol según el tipo de usuario
    if ($tipo_usuario === 'docente') {
        $datos['id_rol'] = 2; // PROFESOR
        $datos['cedula_profesional'] = $_POST['cedula_profesional'] ?? '';
        $datos['institucion_procedencia'] = $_POST['institucion_procedencia'] ?? '';
        $datos['especialidad'] = $_POST['especialidad'] ?? null;
        
        if (empty($datos['cedula_profesional'])) {
            echo json_encode([
                'success' => false,
                'message' => 'La cédula profesional es requerida para docentes'
            ]);
            exit;
        }
    } else {
        $datos['id_rol'] = 3; // USUARIO (Alumno)
    }
    
    // Validar confirmación de contraseña
    if (isset($_POST['confirmar_contrasena'])) {
        if ($datos['contrasena'] !== $_POST['confirmar_contrasena']) {
            echo json_encode([
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ]);
            exit;
        }
    }
    
    $usuario = new Usuario();
    $resultado = $usuario->registrar($datos);
    
    echo json_encode($resultado);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
