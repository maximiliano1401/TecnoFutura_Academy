<?php
ob_start();
session_start();
ob_clean();

require_once __DIR__ . '/includes/config.php';

ob_end_clean();
header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Test estructura de tabla preguntas
    $stmt = $db->query("SHOW COLUMNS FROM preguntas");
    $columns_preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test estructura de tabla opciones_respuesta
    $stmt = $db->query("SHOW COLUMNS FROM opciones_respuesta");
    $columns_opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test query básico
    $stmt = $db->query("SELECT * FROM preguntas LIMIT 1");
    $sample_pregunta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'columns_preguntas' => $columns_preguntas,
        'columns_opciones' => $columns_opciones,
        'sample_pregunta' => $sample_pregunta,
        'session_docente' => $_SESSION['info_adicional']['id_docente'] ?? 'NO EXISTE'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
