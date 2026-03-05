<?php
require_once __DIR__ . '/../../backend/auth/middleware.php';
requiereRol(['PROFESOR']);

require_once __DIR__ . '/../../backend/classes/Actividad.php';
require_once __DIR__ . '/../../backend/classes/Usuario.php';
require_once __DIR__ . '/../../backend/classes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id_inscripcion = intval($_POST['id_inscripcion']);
$id_actividad = intval($_POST['id_actividad']);
$intento = intval($_POST['intento']);

try {
    $db = Database::getInstance()->getConnection();
    
    // Get student info
    $sqlAlumno = "SELECT u.nombre, u.apellido, u.email 
                  FROM usuarios u
                  INNER JOIN inscripciones i ON u.id_usuario = i.id_alumno
                  WHERE i.id_inscripcion = :id_inscripcion";
    $stmt = $db->prepare($sqlAlumno);
    $stmt->execute([':id_inscripcion' => $id_inscripcion]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get activity info
    $sqlActividad = "SELECT a.*, mc.titulo 
                     FROM actividades a
                     INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
                     WHERE a.id_actividad = :id_actividad";
    $stmt = $db->prepare($sqlActividad);
    $stmt->execute([':id_actividad' => $id_actividad]);
    $actividad = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get attempt info
    $sqlIntento = "SELECT * FROM intentos_actividad 
                   WHERE id_inscripcion = :id_inscripcion 
                   AND id_actividad = :id_actividad 
                   AND numero_intento = :intento";
    $stmt = $db->prepare($sqlIntento);
    $stmt->execute([
        ':id_inscripcion' => $id_inscripcion,
        ':id_actividad' => $id_actividad,
        ':intento' => $intento
    ]);
    $intentoData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get questions and answers
    $sqlRespuestas = "SELECT 
                        p.id_pregunta,
                        p.pregunta,
                        p.tipo_pregunta,
                        p.puntos,
                        ra.id_respuesta_alumno,
                        ra.id_opcion,
                        ra.texto_respuesta,
                        ra.puntos_obtenidos,
                        ra.comentario_profesor,
                        ra.calificado,
                        op.texto_opcion,
                        op.es_correcta
                      FROM preguntas p
                      INNER JOIN respuestas_alumno ra ON p.id_pregunta = ra.id_pregunta
                      LEFT JOIN opciones_respuesta op ON ra.id_opcion = op.id_opcion
                      WHERE ra.id_inscripcion = :id_inscripcion
                        AND ra.id_actividad = :id_actividad
                        AND ra.intento = :intento
                      ORDER BY p.orden, p.id_pregunta";
    
    $stmt = $db->prepare($sqlRespuestas);
    $stmt->execute([
        ':id_inscripcion' => $id_inscripcion,
        ':id_actividad' => $id_actividad,
        ':intento' => $intento
    ]);
    $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all options for each question
    $formattedRespuestas = [];
    $preguntasProcessed = [];
    
    foreach ($respuestas as $resp) {
        $id_pregunta = $resp['id_pregunta'];
        
        if (!isset($preguntasProcessed[$id_pregunta])) {
            // Get all options for this question
            $sqlOpciones = "SELECT id_opcion, texto_opcion, es_correcta
                           FROM opciones_respuesta
                           WHERE id_pregunta = :id_pregunta
                           ORDER BY orden, id_opcion";
            $stmtOpc = $db->prepare($sqlOpciones);
            $stmtOpc->execute([':id_pregunta' => $id_pregunta]);
            $opciones = $stmtOpc->fetchAll(PDO::FETCH_ASSOC);
            
            $esCorrecta = false;
            if ($resp['id_opcion']) {
                foreach ($opciones as $opc) {
                    if ($opc['id_opcion'] == $resp['id_opcion'] && $opc['es_correcta']) {
                        $esCorrecta = true;
                        break;
                    }
                }
            }
            
            $formattedRespuestas[] = [
                'id_respuesta' => $resp['id_respuesta_alumno'],
                'pregunta' => $resp['pregunta'],
                'tipo_pregunta' => $resp['tipo_pregunta'],
                'puntos_pregunta' => floatval($resp['puntos']),
                'id_opcion' => $resp['id_opcion'],
                'texto_respuesta' => $resp['texto_respuesta'],
                'puntos_obtenidos' => $resp['puntos_obtenidos'] !== null ? floatval($resp['puntos_obtenidos']) : null,
                'comentario_profesor' => $resp['comentario_profesor'],
                'calificado' => (bool)$resp['calificado'],
                'es_correcta' => $esCorrecta,
                'opciones' => $opciones
            ];
            
            $preguntasProcessed[$id_pregunta] = true;
        }
    }
    
    echo json_encode([
        'success' => true,
        'alumno' => $alumno,
        'actividad' => $actividad,
        'intento' => $intentoData,
        'respuestas' => $formattedRespuestas
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
