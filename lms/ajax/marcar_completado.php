<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../backend/auth/middleware.php';
require_once __DIR__ . '/../../backend/classes/Curso.php';

header('Content-Type: application/json');
requiereRol(['USUARIO']);

$body = json_decode(file_get_contents('php://input'), true);
$id_material    = intval($body['id_material'] ?? 0);
$id_inscripcion = intval($body['id_inscripcion'] ?? 0);

if (!$id_material || !$id_inscripcion) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

$db = Database::getInstance()->getConnection();

// Verify the inscripcion belongs to this user
$stmt = $db->prepare("SELECT id_alumno FROM inscripciones WHERE id_inscripcion = :i");
$stmt->execute([':i' => $id_inscripcion]);
$row = $stmt->fetch();
$id_alumno  = $_SESSION['usuario_rol_id'] ?? 0;
if (!$row || $row['id_alumno'] != $id_alumno) {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}

$obj = new Curso();
$ok  = $obj->marcarLeccionCompletada($id_inscripcion, $id_material);

// Get updated progress
$p = $db->prepare("SELECT progreso FROM inscripciones WHERE id_inscripcion = :i");
$p->execute([':i' => $id_inscripcion]);
$progreso = $p->fetch()['progreso'] ?? 0;

echo json_encode(['success' => $ok, 'progreso' => floatval($progreso)]);
