<?php
/**
 * Upload Handler - Manejo de subida de archivos
 * Soporta: PDFs, Videos (MP4, AVI, MOV), Imágenes (JPG, PNG, SVG)
 */

class UploadHandler {
    private $upload_dir;
    private $allowed_types;
    private $max_file_size;
    private $errors = [];

    public function __construct() {
        // Ruta absoluta a la carpeta uploads del proyecto
        $this->upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        
        // Tipos permitidos por categoría
        $this->allowed_types = [
            'documento' => ['pdf', 'doc', 'docx', 'txt'],
            'video' => ['mp4', 'avi', 'mov', 'mkv', 'webm'],
            'imagen' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']
        ];
        
        // Tamaños máximos en bytes
        $this->max_file_size = [
            'documento' => 10 * 1024 * 1024,  // 10 MB
            'video' => 100 * 1024 * 1024,     // 100 MB
            'imagen' => 5 * 1024 * 1024       // 5 MB
        ];
    }

    /**
     * Subir archivo de material de curso
     */
    public function uploadMaterial(array $file, int $id_curso, string $tipo = 'documento'): array {
        // Validación inicial
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'No se recibió ningún archivo'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => $this->getUploadErrorMessage($file['error'])];
        }

        // Validar tipo de archivo
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!$this->validateFileType($extension, $tipo)) {
            $allowed = implode(', ', $this->allowed_types[$tipo]);
            return ['success' => false, 'message' => "Tipo de archivo no permitido. Permitidos: $allowed"];
        }

        // Validar tamaño
        if ($file['size'] > $this->max_file_size[$tipo]) {
            $max_mb = $this->max_file_size[$tipo] / (1024 * 1024);
            return ['success' => false, 'message' => "El archivo excede el tamaño máximo de {$max_mb}MB"];
        }

        // Preparar directorio destino: uploads/cursos/{tipo}/{id_curso}/
        $tipo_folder = $tipo === 'documento' ? 'documentos' : ($tipo === 'video' ? 'videos' : 'imagenes');
        $curso_dir = $this->upload_dir . 'cursos' . DIRECTORY_SEPARATOR . $tipo_folder . DIRECTORY_SEPARATOR . $id_curso . DIRECTORY_SEPARATOR;
        if (!$this->ensureDirectoryExists($curso_dir)) {
            return ['success' => false, 'message' => 'Error al crear directorio de destino: ' . $curso_dir];
        }

        // Generar nombre único
        $filename = $this->generateUniqueFilename($file['name'], $curso_dir);
        $destination = $curso_dir . $filename;

        // Verificar que el archivo temporal existe
        if (!file_exists($file['tmp_name'])) {
            return ['success' => false, 'message' => 'El archivo temporal no existe'];
        }

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Verificar que se creó correctamente
            if (!file_exists($destination)) {
                return ['success' => false, 'message' => 'El archivo no se guardó correctamente'];
            }
            
            // Ruta relativa para guardar en BD
            $relative_path = "uploads/cursos/{$tipo_folder}/{$id_curso}/{$filename}";
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $relative_path,
                'url' => $relative_path,
                'size' => $file['size'],
                'mime' => mime_content_type($destination)
            ];
        }

        return ['success' => false, 'message' => 'Error al mover el archivo. Origen: ' . $file['tmp_name'] . ', Destino: ' . $destination];
    }

    /**
     * Subir imagen de portada para curso
     */
    public function uploadCourseImage(array $file, int $id_curso): array {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'No se recibió ningún archivo'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => $this->getUploadErrorMessage($file['error'])];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!$this->validateFileType($extension, 'imagen')) {
            return ['success' => false, 'message' => 'Solo se permiten imágenes (JPG, PNG, SVG)'];
        }

        if ($file['size'] > $this->max_file_size['imagen']) {
            return ['success' => false, 'message' => 'La imagen excede el tamaño máximo de 5MB'];
        }

        $curso_dir = $this->upload_dir . 'cursos' . DIRECTORY_SEPARATOR . 'imagenes' . DIRECTORY_SEPARATOR;
        if (!$this->ensureDirectoryExists($curso_dir)) {
            return ['success' => false, 'message' => 'Error al crear directorio'];
        }

        $filename = $this->generateUniqueFilename($file['name'], $curso_dir);
        $destination = $curso_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Redimensionar si es muy grande
            $this->resizeImage($destination, 1200, 675); // 16:9 ratio
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => "uploads/cursos/imagenes/{$filename}"
            ];
        }

        return ['success' => false, 'message' => 'Error al subir la imagen'];
    }

    /**
     * Validar tipo de archivo
     */
    private function validateFileType(string $extension, string $type): bool {
        if (!isset($this->allowed_types[$type])) {
            return false;
        }
        return in_array($extension, $this->allowed_types[$type]);
    }

    /**
     * Asegurar que el directorio existe
     */
    private function ensureDirectoryExists(string $dir): bool {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generar nombre único de archivo
     */
    private function generateUniqueFilename(string $original_name, string $directory): string {
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $basename = pathinfo($original_name, PATHINFO_FILENAME);
        
        // Sanitizar nombre
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $basename = substr($basename, 0, 50); // Limitar longitud
        
        // Agregar timestamp
        $filename = $basename . '_' . time() . '.' . $extension;
        
        // Verificar si ya existe (aunque es improbable con timestamp)
        $counter = 1;
        while (file_exists($directory . $filename)) {
            $filename = $basename . '_' . time() . '_' . $counter . '.' . $extension;
            $counter++;
        }
        
        return $filename;
    }

    /**
     * Redimensionar imagen manteniendo aspecto
     */
    private function resizeImage(string $filepath, int $max_width, int $max_height): bool {
        $info = getimagesize($filepath);
        if (!$info) return false;

        list($width, $height, $type) = $info;

        // Si ya es menor, no hacer nada
        if ($width <= $max_width && $height <= $max_height) {
            return true;
        }

        // Calcular nuevas dimensiones
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = (int)($width * $ratio);
        $new_height = (int)($height * $ratio);

        // Crear imagen según tipo
        $image = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($filepath);
                break;
            default:
                return false;
        }

        if (!$image) return false;

        // Redimensionar
        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        // Preservar transparencia para PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
        }

        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Guardar
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($new_image, $filepath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($new_image, $filepath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($new_image, $filepath);
                break;
        }

        imagedestroy($image);
        imagedestroy($new_image);

        return true;
    }

    /**
     * Eliminar archivo
     */
    public function deleteFile(string $relative_path): bool {
        $full_path = dirname(__DIR__, 2) . '/' . $relative_path;
        if (file_exists($full_path)) {
            return unlink($full_path);
        }
        return false;
    }

    /**
     * Obtener mensaje de error de upload
     */
    private function getUploadErrorMessage(int $error_code): string {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo es demasiado grande';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo se subió parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se subió ningún archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Falta el directorio temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Error al escribir el archivo en disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Extensión de PHP detuvo la subida';
            default:
                return 'Error desconocido al subir el archivo';
        }
    }

    /**
     * Obtener tamaño máximo permitido formateado
     */
    public function getMaxSizeFormatted(string $type): string {
        if (!isset($this->max_file_size[$type])) {
            return '0 MB';
        }
        $mb = $this->max_file_size[$type] / (1024 * 1024);
        return number_format($mb, 0) . ' MB';
    }

    /**
     * Obtener extensiones permitidas
     */
    public function getAllowedExtensions(string $type): array {
        return $this->allowed_types[$type] ?? [];
    }
}
