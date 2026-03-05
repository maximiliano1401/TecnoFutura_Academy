<?php
/**
 * API para registrar progreso de lecciones
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/auth/middleware.php';
require_once __DIR__ . '/classes/Database.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!Usuario::estaAutenticado()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener parámetros
$id_inscripcion = intval($_POST['id_inscripcion'] ?? 0);
$id_material = intval($_POST['id_material'] ?? 0);
$completado = intval($_POST['completado'] ?? 0);

if (!$id_inscripcion || !$id_material) {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    exit;
}

// Verificar que la inscripción pertenece al alumno autenticado
$id_alumno = $_SESSION['info_adicional']['id_alumno'] ?? 0;

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar que la inscripción es del alumno
    $stmt = $db->prepare("SELECT id_inscripcion FROM inscripciones 
                          WHERE id_inscripcion = :insc AND id_alumno = :alum");
    $stmt->execute([':insc' => $id_inscripcion, ':alum' => $id_alumno]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Inscripción no válida']);
        exit;
    }
    
    // Verificar si ya existe el registro de progreso
    $stmt = $db->prepare("SELECT id_progreso, completado FROM progreso_lecciones 
                          WHERE id_inscripcion = :insc AND id_material = :mat");
    $stmt->execute([':insc' => $id_inscripcion, ':mat' => $id_material]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Actualizar existente
        $stmt = $db->prepare("UPDATE progreso_lecciones 
                             SET completado = :comp, fecha_completado = :fecha
                             WHERE id_progreso = :prog");
        $stmt->execute([
            ':comp' => $completado,
            ':fecha' => $completado ? date('Y-m-d H:i:s') : null,
            ':prog' => $existing['id_progreso']
        ]);
    } else {
        // Insertar nuevo
        $stmt = $db->prepare("INSERT INTO progreso_lecciones 
                             (id_inscripcion, id_material, completado, fecha_completado)
                             VALUES (:insc, :mat, :comp, :fecha)");
        $stmt->execute([
            ':insc' => $id_inscripcion,
            ':mat' => $id_material,
            ':comp' => $completado,
            ':fecha' => $completado ? date('Y-m-d H:i:s') : null
        ]);
    }
    
    // Calcular y actualizar progreso total del curso
    $stmt = $db->prepare("SELECT 
        COUNT(*) as total_lecciones,
        SUM(CASE WHEN pl.completado = 1 THEN 1 ELSE 0 END) as lecciones_completadas
        FROM materiales_curso mc
        INNER JOIN inscripciones i ON mc.id_curso = i.id_curso
        LEFT JOIN progreso_lecciones pl ON mc.id_material = pl.id_material 
            AND pl.id_inscripcion = i.id_inscripcion
        WHERE i.id_inscripcion = :insc
        GROUP BY i.id_inscripcion");
    $stmt->execute([':insc' => $id_inscripcion]);
    $stats = $stmt->fetch();
    
    if ($stats && $stats['total_lecciones'] > 0) {
        $progreso = round(($stats['lecciones_completadas'] / $stats['total_lecciones']) * 100, 2);
        
        // Actualizar progreso en inscripciones
        $stmt = $db->prepare("UPDATE inscripciones SET progreso = :prog WHERE id_inscripcion = :insc");
        $stmt->execute([':prog' => $progreso, ':insc' => $id_inscripcion]);
        
        // Si llega al 100%, actualizar estado
        if ($progreso >= 100) {
            $stmt = $db->prepare("UPDATE inscripciones 
                                 SET estado = 'Completado', fecha_completado = NOW() 
                                 WHERE id_inscripcion = :insc AND estado != 'Completado'");
            $stmt->execute([':insc' => $id_inscripcion]);
        }
        
        echo json_encode([
            'success' => true,
            'progreso' => $progreso,
            'completadas' => $stats['lecciones_completadas'],
            'total' => $stats['total_lecciones']
        ]);
    } else {
        echo json_encode(['success' => true, 'progreso' => 0]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
