# Sistema de Rutas Multiplataforma

## 🎯 Problema Resuelto

Este proyecto implementa un sistema de **detección automática de rutas** que funciona en **cualquier sistema operativo** (Windows, Mac, Linux) y **cualquier estructura de carpetas** (raíz del servidor o subdirectorio).

## ⚙️ Cómo Funciona

### Detección Automática del Directorio Base

El archivo `backend/config/config.php` detecta automáticamente:
- El protocolo (HTTP/HTTPS)
- El host (localhost, 127.0.0.1, dominio)
- El directorio base del proyecto (TecnoFutura_Academy)

```php
// ✅ CORRECTO - Se detecta automáticamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Busca la carpeta TecnoFutura_Academy en la ruta
$base_folder = '';
foreach ($script_parts as $part) {
    if (stripos($part, 'TecnoFutura') !== false) {
        $base_folder = '/' . $part;
        break;
    }
}

define('SITE_URL', $protocol . $host . $base_folder);
```

### Constantes Globales Disponibles

```php
SITE_URL     → http://localhost/TecnoFutura_Academy (Windows)
             → http://localhost (Mac en raíz)
             
CSS_PATH     → /TecnoFutura_Academy/css (Windows)
             → /css (Mac en raíz)
             
JS_PATH      → /TecnoFutura_Academy/js (Windows)
             → /js (Mac en raíz)
             
IMG_PATH     → /TecnoFutura_Academy/img (Windows)
             → /img (Mac en raíz)
             
LMS_URL      → /TecnoFutura_Academy/lms (Windows)
             → /lms (Mac en raíz)
```

## 📋 Uso Correcto en el Código

### ✅ CORRECTO - Usar las constantes

```php
<!-- CSS -->
<link rel="stylesheet" href="<?= CSS_PATH ?>/styles.css">

<!-- JavaScript -->
<script src="<?= JS_PATH ?>/main.js"></script>

<!-- Imágenes -->
<img src="<?= IMG_PATH ?>/logo.png" alt="Logo">

<!-- Enlaces internos -->
<a href="<?= SITE_URL ?>/cursos">Ver Cursos</a>
<a href="<?= SITE_URL ?>/login.php">Iniciar Sesión</a>
```

### ❌ INCORRECTO - Hardcodear rutas

```php
<!-- ❌ NO HAGAS ESTO -->
<link rel="stylesheet" href="/css/styles.css">
<link rel="stylesheet" href="/TecnoFutura_Academy/css/styles.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/css/styles.css">

<!-- ❌ NO HAGAS ESTO -->
<script src="/js/main.js"></script>
<a href="/cursos">Ver Cursos</a>
```

## 🖥️ Configuraciones de Equipo

### Windows (XAMPP)
```
Ruta del proyecto: C:\xampp\htdocs\TecnoFutura_Academy\
URL: http://localhost/TecnoFutura_Academy/
```

### Mac (XAMPP/MAMP)
```
Ruta del proyecto (raíz): /Applications/XAMPP/htdocs/
URL: http://localhost/

O si está en subdirectorio:
Ruta: /Applications/XAMPP/htdocs/TecnoFutura_Academy/
URL: http://localhost/TecnoFutura_Academy/
```

### Linux
```
Ruta del proyecto: /var/www/html/TecnoFutura_Academy/
URL: http://localhost/TecnoFutura_Academy/
```

## 🔄 Para el Compañero en Mac

**IMPORTANTE:** Si hiciste cambios en las rutas, necesitas revertirlos y hacer pull:

```bash
# 1. Descarta tus cambios locales en las rutas
git checkout backend/config/config.php
git checkout includes/header.php
git checkout includes/footer.php

# 2. Obtén los cambios del repositorio
git pull origin main

# 3. El sistema detectará automáticamente tu configuración
```

### Verificación

Después del pull, verifica que tienes:
1. ✅ `backend/config/config.php` con detección automática
2. ✅ `includes/header.php` usando `CSS_PATH`
3. ✅ `includes/footer.php` usando `JS_PATH`
4. ✅ Todos los archivos usando las constantes en lugar de rutas hardcodeadas

## 🚨 Regla de Oro

**NUNCA modifiques las rutas manualmente**. El sistema las detecta automáticamente.

Si encuentras un archivo que usa rutas hardcodeadas:
1. Abre un issue en GitHub
2. O corrígelo usando las constantes apropiadas
3. Haz commit y push para que todos tengan la versión correcta

## 🧪 Prueba de Compatibilidad

Verifica que el sistema funciona correctamente:

```php
// Agrega esto temporalmente a index.php para verificar
echo '<pre>';
echo 'SITE_URL: ' . SITE_URL . "\n";
echo 'CSS_PATH: ' . CSS_PATH . "\n";
echo 'JS_PATH: ' . JS_PATH . "\n";
echo 'IMG_PATH: ' . IMG_PATH . "\n";
echo 'LMS_URL: ' . LMS_URL . "\n";
echo '</pre>';
```

Deberías ver las rutas correctas para tu sistema operativo.

---

**Última actualización:** 4 de marzo de 2026  
**Versión del sistema de rutas:** 2.0 Multiplataforma
