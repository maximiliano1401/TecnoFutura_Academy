<?php
// Evitar cualquier output antes del JSON
ob_start();
session_start();

// Limpiar cualquier output previo
ob_clean();

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../classes/Database.php';

// Asegurar que no hay output anterior
ob_end_clean();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Verificar sesión
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'PROFESOR') {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if (!isset($_GET['id_actividad'])) {
    echo json_encode(['error' => 'ID de actividad no especificado']);
    exit;
}

$id_actividad = intval($_GET['id_actividad']);
$id_docente = $_SESSION['info_adicional']['id_docente'] ?? 0;

if (!$id_docente) {
    echo json_encode(['error' => 'ID de docente no encontrado en sesión']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar que la actividad pertenece al docente
    $stmtCheck = $db->prepare("
        SELECT a.id_actividad 
        FROM actividades a
        INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
        INNER JOIN cursos c ON mc.id_curso = c.id_curso
        WHERE a.id_actividad = ? AND c.id_docente = ?
    ");
    $stmtCheck->execute([$id_actividad, $id_docente]);
    
    if (!$stmtCheck->fetch()) {
        echo json_encode(['error' => 'No tienes permisos para ver esta actividad']);
        exit;
    }
    
    // Obtener preguntas
    $stmtPreguntas = $db->prepare("
        SELECT id_pregunta, texto_pregunta, tipo_pregunta, puntaje, orden, explicacion
        FROM preguntas
        WHERE id_actividad = ?
        ORDER BY orden ASC
    ");
    $stmtPreguntas->execute([$id_actividad]);
    $preguntas = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener opciones para cada pregunta
    foreach ($preguntas as &$pregunta) {
        $stmtOpciones = $db->prepare("
            SELECT id_opcion, texto_opcion, es_correcta
            FROM opciones_respuesta
            WHERE id_pregunta = ?
            ORDER BY id_opcion ASC
        ");
        $stmtOpciones->execute([$pregunta['id_pregunta']]);
        $pregunta['opciones'] = $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode(['preguntas' => $preguntas]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al cargar preguntas: ' . $e->getMessage()]);
}
