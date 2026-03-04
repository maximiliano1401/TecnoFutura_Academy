<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../backend/auth/middleware.php';
require_once __DIR__ . '/../../backend/classes/Certificado.php';
require_once __DIR__ . '/../../backend/classes/Curso.php';

requiereRol(['USUARIO']);

$id_inscripcion = intval($_GET['id'] ?? 0);
$id_alumno      = $_SESSION['usuario_rol_id'] ?? 0;

// Verify ownership
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT id_alumno, progreso FROM inscripciones WHERE id_inscripcion = :i");
$stmt->execute([':i' => $id_inscripcion]);
$insc = $stmt->fetch();

if (!$insc || $insc['id_alumno'] != $id_alumno) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'No autorizado.'];
    header('Location: ' . SITE_URL . '/lms');
    exit;
}

if (floatval($insc['progreso']) < 100) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Debes completar el 100% del curso para obtener tu certificado.'];
    header('Location: ' . SITE_URL . '/lms');
    exit;
}

$cert_obj  = new Certificado();
$resultado = $cert_obj->emitir($id_inscripcion);

if ($resultado['success']) {
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => '¡Certificado emitido! Código: ' . $resultado['codigo']];
    header('Location: ' . SITE_URL . '/certificados/ver.php?codigo=' . $resultado['codigo']);
} else {
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => $resultado['message']];
    header('Location: ' . SITE_URL . '/lms');
}
exit;
