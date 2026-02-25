<?php
/**
 * Archivo de prueba para verificar rutas
 * Eliminar después de verificar que todo funciona
 */
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Rutas - TecnoFutura Academy</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; font-weight: bold; }
        .info { color: #007bff; }
        .warning { color: #ffc107; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 200px; }
        .test-image { max-width: 100px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>✅ Test de Rutas - TecnoFutura Academy</h1>
    
    <div class="card">
        <h2>📋 Variables del Sistema</h2>
        <table>
            <tr>
                <td>Sistema Operativo:</td>
                <td><span class="info"><?php echo PHP_OS; ?></span></td>
            </tr>
            <tr>
                <td>HTTP_HOST:</td>
                <td><?php echo $_SERVER['HTTP_HOST']; ?></td>
            </tr>
            <tr>
                <td>SCRIPT_NAME:</td>
                <td><?php echo $_SERVER['SCRIPT_NAME']; ?></td>
            </tr>
            <tr>
                <td>DOCUMENT_ROOT:</td>
                <td><?php echo $_SERVER['DOCUMENT_ROOT']; ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>🌐 Rutas Generadas (config.php)</h2>
        <table>
            <tr>
                <td>SITE_URL:</td>
                <td><span class="success"><?php echo SITE_URL; ?></span></td>
            </tr>
            <tr>
                <td>CSS_PATH:</td>
                <td><span class="success"><?php echo CSS_PATH; ?></span></td>
            </tr>
            <tr>
                <td>JS_PATH:</td>
                <td><span class="success"><?php echo JS_PATH; ?></span></td>
            </tr>
            <tr>
                <td>IMG_PATH:</td>
                <td><span class="success"><?php echo IMG_PATH; ?></span></td>
            </tr>
            <tr>
                <td>LMS_URL:</td>
                <td><span class="success"><?php echo LMS_URL; ?></span></td>
            </tr>
            <tr>
                <td>BASE_PATH:</td>
                <td><span class="success"><?php echo BASE_PATH; ?></span></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>🔗 URLs Completas para Recursos</h2>
        <table>
            <tr>
                <td>CSS completo:</td>
                <td><a href="<?php echo CSS_PATH; ?>/styles.css" target="_blank"><?php echo SITE_URL . CSS_PATH; ?>/styles.css</a></td>
            </tr>
            <tr>
                <td>JS completo:</td>
                <td><a href="<?php echo JS_PATH; ?>/main.js" target="_blank"><?php echo SITE_URL . JS_PATH; ?>/main.js</a></td>
            </tr>
            <tr>
                <td>Logo:</td>
                <td><a href="<?php echo IMG_PATH; ?>/TecnoFutura_Academy-logo.png" target="_blank"><?php echo SITE_URL . IMG_PATH; ?>/TecnoFutura_Academy-logo.png</a></td>
            </tr>
            <tr>
                <td>LMS:</td>
                <td><a href="<?php echo LMS_URL; ?>" target="_blank"><?php echo SITE_URL . LMS_URL; ?></a></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>🖼️ Test de Imagen</h2>
        <p>Si ves el logo aquí abajo, las rutas están funcionando correctamente:</p>
        <img src="<?php echo IMG_PATH; ?>/TecnoFutura_Academy-logo.png" alt="Logo Test" class="test-image">
        <p><small>Ruta utilizada: <code><?php echo IMG_PATH; ?>/TecnoFutura_Academy-logo.png</code></small></p>
    </div>

    <div class="card">
        <h2>✅ Verificación de Archivos</h2>
        <table>
            <tr>
                <td>styles.css existe:</td>
                <td><?php echo file_exists(__DIR__ . '/css/styles.css') ? '<span class="success">✓ SÍ</span>' : '<span class="warning">✗ NO</span>'; ?></td>
            </tr>
            <tr>
                <td>main.js existe:</td>
                <td><?php echo file_exists(__DIR__ . '/js/main.js') ? '<span class="success">✓ SÍ</span>' : '<span class="warning">✗ NO</span>'; ?></td>
            </tr>
            <tr>
                <td>Logo existe:</td>
                <td><?php echo file_exists(__DIR__ . '/img/TecnoFutura_Academy-logo.png') ? '<span class="success">✓ SÍ</span>' : '<span class="warning">✗ NO</span>'; ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>🎯 Instrucciones</h2>
        <ul>
            <li>✅ Si ves el logo arriba, las rutas funcionan correctamente</li>
            <li>✅ Si todos los enlaces son clicables, la configuración es correcta</li>
            <li>✅ Este archivo funciona igual en Windows, Mac y Linux</li>
            <li>⚠️ <strong>Elimina este archivo (test_rutas.php) cuando confirmes que todo funciona</strong></li>
        </ul>
        <p><a href="index.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">← Volver al Index</a></p>
    </div>
</body>
</html>
