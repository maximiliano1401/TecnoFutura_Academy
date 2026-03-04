<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Pago.php';
require_once __DIR__ . '/../backend/classes/Curso.php';

header('Content-Type: application/json');
requiereAutenticacion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$id_curso   = intval($_POST['id_curso'] ?? 0);
$monto      = floatval($_POST['monto'] ?? 0);
$metodo     = htmlspecialchars(trim($_POST['metodo_pago'] ?? 'tarjeta'));
$id_alumno  = $_SESSION['usuario_rol_id'] ?? 0;

if (!$id_curso || !$id_alumno) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

// Validate course and price
$obj    = new Curso();
$curso  = $obj->porId($id_curso);
if (!$curso) {
    echo json_encode(['success' => false, 'message' => 'Curso no encontrado.']);
    exit;
}

// Validate monto matches course price
if (abs($monto - floatval($curso['precio'])) > 0.01) {
    echo json_encode(['success' => false, 'message' => 'Monto inválido.']);
    exit;
}

$pago = new Pago();
$resultado = $pago->procesar([
    'id_curso'    => $id_curso,
    'id_alumno'   => $id_alumno,
    'monto'       => $monto,
    'metodo_pago' => $metodo,
]);

echo json_encode($resultado);
