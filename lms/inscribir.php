<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';

requiereRol(['USUARIO']);

$id_curso  = intval($_GET['id'] ?? 0);
$id_alumno = $_SESSION['info_adicional']['id_alumno'] ?? 0;

$obj   = new Curso();
$curso = $obj->porId($id_curso);

if (!$curso || $curso['precio'] != 0) {
    header('Location: ' . SITE_URL . '/cursos');
    exit;
}

$resultado = $obj->inscribir($id_curso, $id_alumno);

if ($resultado['success']) {
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => '¡Inscripción exitosa! Bienvenido al curso.'];
    header('Location: ' . SITE_URL . '/lms/curso.php?id=' . $id_curso);
} else {
    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => $resultado['message']];
    header('Location: ' . SITE_URL . '/cursos/detalle.php?id=' . $id_curso);
}
exit;
