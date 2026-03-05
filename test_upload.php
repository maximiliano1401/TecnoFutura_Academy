<?php
require_once __DIR__ . '/backend/upload_handler.php';

echo "<h2>Test de Configuración de Upload</h2>";

$uploader = new UploadHandler();

// Usar reflexión para acceder a propiedades privadas
$reflection = new ReflectionClass($uploader);
$upload_dir_property = $reflection->getProperty('upload_dir');
$upload_dir_property->setAccessible(true);
$upload_dir = $upload_dir_property->getValue($uploader);

echo "<h3>Directorio Base</h3>";
echo "<p><strong>Ruta configurada:</strong> <code>" . htmlspecialchars($upload_dir) . "</code></p>";
echo "<p><strong>¿Existe?</strong> " . (is_dir($upload_dir) ? "✅ SÍ" : "❌ NO") . "</p>";
echo "<p><strong>¿Es escribible?</strong> " . (is_writable($upload_dir) ? "✅ SÍ" : "❌ NO") . "</p>";

// Probar creación de directorios
$test_curso_id = 999;
$test_types = ['documentos', 'videos', 'imagenes'];

echo "<h3>Prueba de Creación de Directorios</h3>";
foreach ($test_types as $tipo) {
    $test_dir = $upload_dir . 'cursos' . DIRECTORY_SEPARATOR . $tipo . DIRECTORY_SEPARATOR . $test_curso_id . DIRECTORY_SEPARATOR;
    echo "<p><strong>$tipo:</strong> <code>" . htmlspecialchars($test_dir) . "</code></p>";
    
    // Intentar crear
    if (!is_dir($test_dir)) {
        $created = mkdir($test_dir, 0755, true);
        echo "<p>Resultado: " . ($created ? "✅ Creado" : "❌ Error al crear") . "</p>";
        
        if ($created) {
            // Verificar permisos
            echo "<p>¿Es escribible? " . (is_writable($test_dir) ? "✅ SÍ" : "❌ NO") . "</p>";
            
            // Limpiar directorio de prueba
            rmdir($test_dir);
            $parent = dirname($test_dir);
            if (is_dir($parent) && count(scandir($parent)) == 2) { // solo . y ..
                rmdir($parent);
            }
        }
    } else {
        echo "<p>✅ Ya existe</p>";
    }
    echo "<hr>";
}

echo "<h3>Información del Sistema</h3>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Upload Max Filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Post Max Size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>Temp Dir:</strong> " . sys_get_temp_dir() . "</p>";
echo "<p><strong>DIRECTORY_SEPARATOR:</strong> <code>" . DIRECTORY_SEPARATOR . "</code></p>";
