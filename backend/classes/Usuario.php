<?php
/**
 * Clase Usuario
 * Maneja todas las operaciones relacionadas con usuarios
 */

require_once __DIR__ . '/Database.php';

class Usuario {
    private $db;
    private $id_usuario;
    private $nombre_completo;
    private $correo_electronico;
    private $fecha_nacimiento;
    private $id_rol;
    private $nombre_rol;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Registrar un nuevo usuario
     */
    public function registrar($datos) {
        try {
            $this->db->beginTransaction();
            
            // Validar datos
            if (!$this->validarDatos($datos)) {
                throw new Exception("Datos incompletos o inválidos");
            }
            
            // Verificar si el correo ya existe
            if ($this->existeCorreo($datos['correo_electronico'])) {
                throw new Exception("El correo electrónico ya está registrado");
            }
            
            // Determinar el rol (por defecto USUARIO)
            $id_rol = isset($datos['id_rol']) ? $datos['id_rol'] : 3; // 3 = USUARIO
            
            // Hashear contraseña
            $contrasena_hash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            
            // Insertar usuario
            $sql = "INSERT INTO usuarios (nombre_completo, correo_electronico, contrasena, fecha_nacimiento, id_rol) 
                    VALUES (:nombre, :correo, :contrasena, :fecha_nacimiento, :id_rol)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre_completo'],
                ':correo' => $datos['correo_electronico'],
                ':contrasena' => $contrasena_hash,
                ':fecha_nacimiento' => $datos['fecha_nacimiento'],
                ':id_rol' => $id_rol
            ]);
            
            $id_usuario = $this->db->lastInsertId();
            
            // Si es alumno, crear registro en tabla alumnos
            if ($id_rol == 3) {
                // Generar matrícula única: TF-YYYY-NNNN
                $anio = date('Y');
                $count = $this->db->query("SELECT COUNT(*) FROM alumnos")->fetchColumn();
                $matricula = 'TF-' . $anio . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
                $sql_alumno = "INSERT INTO alumnos (id_usuario, matricula) VALUES (:id_usuario, :matricula)";
                $stmt_alumno = $this->db->prepare($sql_alumno);
                $stmt_alumno->execute([':id_usuario' => $id_usuario, ':matricula' => $matricula]);
            }
            
            // Si es docente, crear registro en tabla docentes
            if ($id_rol == 2 && isset($datos['cedula_profesional'])) {
                $sql_docente = "INSERT INTO docentes (id_usuario, cedula_profesional, institucion_procedencia, especialidad) 
                               VALUES (:id_usuario, :cedula, :institucion, :especialidad)";
                $stmt_docente = $this->db->prepare($sql_docente);
                $stmt_docente->execute([
                    ':id_usuario'   => $id_usuario,
                    ':cedula'       => $datos['cedula_profesional'],
                    ':institucion'  => $datos['institucion_procedencia'] ?? null,
                    ':especialidad' => $datos['especialidad'] ?? null
                ]);
            }
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Usuario registrado exitosamente', 'id_usuario' => $id_usuario];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Iniciar sesión
     */
    public function login($correo, $contrasena) {
        try {
            $sql = "SELECT u.*, r.nombre_rol 
                    FROM usuarios u 
                    INNER JOIN roles r ON u.id_rol = r.id_rol 
                    WHERE u.correo_electronico = :correo AND u.activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':correo' => $correo]);
            $usuario = $stmt->fetch();
            
            if (!$usuario) {
                return ['success' => false, 'message' => 'Credenciales inválidas'];
            }
            
            if (!password_verify($contrasena, $usuario['contrasena'])) {
                return ['success' => false, 'message' => 'Credenciales inválidas'];
            }
            
            // Actualizar última sesión
            $sql_update = "UPDATE usuarios SET ultima_sesion = CURRENT_TIMESTAMP WHERE id_usuario = :id";
            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->execute([':id' => $usuario['id_usuario']]);
            
            // Obtener información adicional según el rol
            $info_adicional = $this->obtenerInfoAdicional($usuario['id_usuario'], $usuario['id_rol']);
            
            // Guardar datos en sesión
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['usuario_correo'] = $usuario['correo_electronico'];
            $_SESSION['usuario_rol'] = $usuario['nombre_rol'];
            $_SESSION['usuario_rol_id'] = $usuario['id_rol'];
            $_SESSION['login_time'] = time();
            
            // Agregar información adicional a la sesión
            if ($info_adicional) {
                $_SESSION['info_adicional'] = $info_adicional;
            }
            
            return [
                'success' => true, 
                'message' => 'Inicio de sesión exitoso',
                'usuario' => [
                    'id' => $usuario['id_usuario'],
                    'nombre' => $usuario['nombre_completo'],
                    'correo' => $usuario['correo_electronico'],
                    'rol' => $usuario['nombre_rol']
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al iniciar sesión: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener información adicional según el rol
     */
    private function obtenerInfoAdicional($id_usuario, $id_rol) {
        try {
            if ($id_rol == 3) { // Alumno
                $sql = "SELECT * FROM alumnos WHERE id_usuario = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':id' => $id_usuario]);
                return $stmt->fetch();
            } elseif ($id_rol == 2) { // Docente
                $sql = "SELECT * FROM docentes WHERE id_usuario = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':id' => $id_usuario]);
                return $stmt->fetch();
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Cerrar sesión
     */
    public static function logout() {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada exitosamente'];
    }
    
    /**
     * Verificar si hay una sesión activa
     */
    public static function estaAutenticado() {
        return isset($_SESSION['usuario_id']);
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     */
    public static function tieneRol($rol) {
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === $rol;
    }
    
    /**
     * Verificar si el correo ya existe
     */
    private function existeCorreo($correo) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo_electronico = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Validar datos del formulario
     */
    private function validarDatos($datos) {
        $campos_requeridos = ['nombre_completo', 'correo_electronico', 'contrasena', 'fecha_nacimiento'];
        
        foreach ($campos_requeridos as $campo) {
            if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
                return false;
            }
        }
        
        // Validar formato de correo
        if (!filter_var($datos['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato de correo electrónico inválido");
        }
        
        // Validar longitud de contraseña
        if (strlen($datos['contrasena']) < PASSWORD_MIN_LENGTH) {
            throw new Exception("La contraseña debe tener al menos " . PASSWORD_MIN_LENGTH . " caracteres");
        }
        
        return true;
    }
    
    /**
     * Obtener información del usuario actual
     */
    public static function obtenerUsuarioActual() {
        if (!self::estaAutenticado()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'correo' => $_SESSION['usuario_correo'],
            'rol' => $_SESSION['usuario_rol'],
            'rol_id' => $_SESSION['usuario_rol_id'],
            'info_adicional' => $_SESSION['info_adicional'] ?? null
        ];
    }
}
