<?php
require_once __DIR__ . '/Database.php';

class Actividad {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ==================== ACTIVIDADES ====================
    
    /**
     * Crear una nueva actividad vinculada a un material
     */
    public function crear(array $datos): array {
        try {
            $stmt = $this->db->prepare("INSERT INTO actividades 
                (id_material, titulo, descripcion, puntaje_total, puntaje_minimo_aprobatorio, 
                 duracion_minutos, intentos_permitidos, mostrar_respuestas)
                VALUES (:mat, :tit, :desc, :pt, :pmin, :dur, :int, :mostrar)");
            
            $stmt->execute([
                ':mat' => $datos['id_material'],
                ':tit' => $datos['titulo'],
                ':desc' => $datos['descripcion'] ?? '',
                ':pt' => $datos['puntaje_total'] ?? 100,
                ':pmin' => $datos['puntaje_minimo_aprobatorio'] ?? 60,
                ':dur' => $datos['duracion_minutos'] ?? 0,
                ':int' => $datos['intentos_permitidos'] ?? 1,
                ':mostrar' => $datos['mostrar_respuestas'] ?? 1,
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtener actividad por ID de material
     */
    public function porMaterial(int $id_material): ?array {
        $stmt = $this->db->prepare("SELECT * FROM actividades WHERE id_material = :id");
        $stmt->execute([':id' => $id_material]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Obtener actividad por ID
     */
    public function porId(int $id_actividad): ?array {
        $stmt = $this->db->prepare("SELECT a.*, m.titulo as nombre_material, m.id_curso
            FROM actividades a
            JOIN materiales_curso m ON a.id_material = m.id_material
            WHERE a.id_actividad = :id");
        $stmt->execute([':id' => $id_actividad]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Actualizar configuración de actividad
     */
    public function actualizar(int $id_actividad, array $datos): bool {
        $stmt = $this->db->prepare("UPDATE actividades SET
            titulo = :tit, descripcion = :desc, puntaje_total = :pt,
            puntaje_minimo_aprobatorio = :pmin, duracion_minutos = :dur,
            intentos_permitidos = :int, mostrar_respuestas = :mostrar
            WHERE id_actividad = :id");
        
        return $stmt->execute([
            ':tit' => $datos['titulo'],
            ':desc' => $datos['descripcion'] ?? '',
            ':pt' => $datos['puntaje_total'],
            ':pmin' => $datos['puntaje_minimo_aprobatorio'],
            ':dur' => $datos['duracion_minutos'],
            ':int' => $datos['intentos_permitidos'],
            ':mostrar' => $datos['mostrar_respuestas'],
            ':id' => $id_actividad
        ]);
    }

    // ==================== PREGUNTAS ====================
    
    /**
     * Crear una pregunta
     */
    public function crearPregunta(array $datos): array {
        try {
            $stmt = $this->db->prepare("INSERT INTO preguntas 
                (id_actividad, tipo_pregunta, texto_pregunta, puntaje, orden, explicacion)
                VALUES (:act, :tipo, :texto, :punt, :orden, :expl)");
            
            $stmt->execute([
                ':act' => $datos['id_actividad'],
                ':tipo' => $datos['tipo_pregunta'],
                ':texto' => $datos['texto_pregunta'],
                ':punt' => $datos['puntaje'] ?? 10,
                ':orden' => $datos['orden'] ?? 0,
                ':expl' => $datos['explicacion'] ?? ''
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtener preguntas de una actividad
     */
    public function preguntasDeActividad(int $id_actividad): array {
        $stmt = $this->db->prepare("SELECT * FROM preguntas 
            WHERE id_actividad = :id ORDER BY orden ASC, id_pregunta ASC");
        $stmt->execute([':id' => $id_actividad]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener pregunta por ID con opciones
     */
    public function preguntaPorId(int $id_pregunta): ?array {
        $stmt = $this->db->prepare("SELECT * FROM preguntas WHERE id_pregunta = :id");
        $stmt->execute([':id' => $id_pregunta]);
        $pregunta = $stmt->fetch();
        
        if ($pregunta) {
            $pregunta['opciones'] = $this->opcionesDePregunta($id_pregunta);
        }
        
        return $pregunta ?: null;
    }

    /**
     * Actualizar pregunta
     */
    public function actualizarPregunta(int $id_pregunta, array $datos): bool {
        $stmt = $this->db->prepare("UPDATE preguntas SET
            texto_pregunta = :texto, puntaje = :punt, orden = :orden, explicacion = :expl
            WHERE id_pregunta = :id");
        
        return $stmt->execute([
            ':texto' => $datos['texto_pregunta'],
            ':punt' => $datos['puntaje'],
            ':orden' => $datos['orden'],
            ':expl' => $datos['explicacion'] ?? '',
            ':id' => $id_pregunta
        ]);
    }

    /**
     * Eliminar pregunta
     */
    public function eliminarPregunta(int $id_pregunta): bool {
        $stmt = $this->db->prepare("DELETE FROM preguntas WHERE id_pregunta = :id");
        return $stmt->execute([':id' => $id_pregunta]);
    }

    // ==================== OPCIONES ====================
    
    /**
     * Crear opción de respuesta
     */
    public function crearOpcion(array $datos): array {
        try {
            $stmt = $this->db->prepare("INSERT INTO opciones_respuesta 
                (id_pregunta, texto_opcion, es_correcta, orden)
                VALUES (:preg, :texto, :correcta, :orden)");
            
            $stmt->execute([
                ':preg' => $datos['id_pregunta'],
                ':texto' => $datos['texto_opcion'],
                ':correcta' => $datos['es_correcta'] ?? 0,
                ':orden' => $datos['orden'] ?? 0
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtener opciones de una pregunta
     */
    public function opcionesDePregunta(int $id_pregunta): array {
        $stmt = $this->db->prepare("SELECT * FROM opciones_respuesta 
            WHERE id_pregunta = :id ORDER BY orden ASC");
        $stmt->execute([':id' => $id_pregunta]);
        return $stmt->fetchAll();
    }

    /**
     * Eliminar opción
     */
    public function eliminarOpcion(int $id_opcion): bool {
        $stmt = $this->db->prepare("DELETE FROM opciones_respuesta WHERE id_opcion = :id");
        return $stmt->execute([':id' => $id_opcion]);
    }

    // ==================== INTENTOS Y RESPUESTAS ====================
    
    /**
     * Iniciar un intento de actividad
     */
    public function iniciarIntento(int $id_inscripcion, int $id_actividad): array {
        try {
            // Verificar intentos previos
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM intentos_actividad 
                WHERE id_inscripcion = :insc AND id_actividad = :act");
            $stmt->execute([':insc' => $id_inscripcion, ':act' => $id_actividad]);
            $intentos_previos = $stmt->fetch()['total'];
            
            // Obtener configuración de la actividad
            $actividad = $this->porId($id_actividad);
            if (!$actividad) {
                return ['success' => false, 'message' => 'Actividad no encontrada'];
            }
            
            // Verificar si puede hacer más intentos
            if ($actividad['intentos_permitidos'] > 0 && $intentos_previos >= $actividad['intentos_permitidos']) {
                return ['success' => false, 'message' => 'Has alcanzado el número máximo de intentos'];
            }
            
            $numero_intento = $intentos_previos + 1;
            
            // Crear registro de intento
            $stmt = $this->db->prepare("INSERT INTO intentos_actividad 
                (id_inscripcion, id_actividad, numero_intento, puntaje_total, fecha_inicio)
                VALUES (:insc, :act, :num, :total, NOW())");
            
            $stmt->execute([
                ':insc' => $id_inscripcion,
                ':act' => $id_actividad,
                ':num' => $numero_intento,
                ':total' => $actividad['puntaje_total']
            ]);
            
            return ['success' => true, 'id_intento' => $this->db->lastInsertId(), 'numero_intento' => $numero_intento];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Guardar respuesta de alumno
     */
    public function guardarRespuesta(array $datos): array {
        try {
            // Verificar si la pregunta es de opción múltiple para auto-calificar
            $pregunta = $this->preguntaPorId($datos['id_pregunta']);
            $es_correcta = null;
            $puntaje = 0;
            
            if ($pregunta['tipo_pregunta'] === 'opcion_multiple' || $pregunta['tipo_pregunta'] === 'verdadero_falso') {
                // Verificar si la opción seleccionada es correcta
                if (isset($datos['id_opcion'])) {
                    $stmt = $this->db->prepare("SELECT es_correcta FROM opciones_respuesta WHERE id_opcion = :id");
                    $stmt->execute([':id' => $datos['id_opcion']]);
                    $opcion = $stmt->fetch();
                    $es_correcta = $opcion['es_correcta'] ?? 0;
                    $puntaje = $es_correcta ? $pregunta['puntaje'] : 0;
                }
            }
            
            // Insertar o actualizar respuesta
            $stmt = $this->db->prepare("INSERT INTO respuestas_alumno 
                (id_inscripcion, id_actividad, id_pregunta, id_opcion, texto_respuesta, 
                 es_correcta, puntaje_obtenido, intento)
                VALUES (:insc, :act, :preg, :opc, :texto, :correcta, :punt, :int)
                ON DUPLICATE KEY UPDATE
                id_opcion = :opc, texto_respuesta = :texto, es_correcta = :correcta, 
                puntaje_obtenido = :punt, fecha_respuesta = NOW()");
            
            $stmt->execute([
                ':insc' => $datos['id_inscripcion'],
                ':act' => $datos['id_actividad'],
                ':preg' => $datos['id_pregunta'],
                ':opc' => $datos['id_opcion'] ?? null,
                ':texto' => $datos['texto_respuesta'] ?? null,
                ':correcta' => $es_correcta,
                ':punt' => $puntaje,
                ':int' => $datos['intento'] ?? 1
            ]);
            
            return ['success' => true, 'es_correcta' => $es_correcta, 'puntaje' => $puntaje];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Finalizar intento y calcular puntaje
     */
    public function finalizarIntento(int $id_inscripcion, int $id_actividad, int $numero_intento): array {
        try {
            // Calcular puntaje total obtenido
            $stmt = $this->db->prepare("SELECT SUM(puntaje_obtenido) as total_obtenido
                FROM respuestas_alumno 
                WHERE id_inscripcion = :insc AND id_actividad = :act AND intento = :int");
            
            $stmt->execute([':insc' => $id_inscripcion, ':act' => $id_actividad, ':int' => $numero_intento]);
            $resultado = $stmt->fetch();
            $puntaje_obtenido = $resultado['total_obtenido'] ?? 0;
            
            // Obtener configuración
            $actividad = $this->porId($id_actividad);
            $aprobado = $puntaje_obtenido >= $actividad['puntaje_minimo_aprobatorio'];
            
            // Verificar si hay preguntas pendientes de calificación manual
            $stmt = $this->db->prepare("SELECT COUNT(*) as pendientes FROM respuestas_alumno 
                WHERE id_inscripcion = :insc AND id_actividad = :act AND intento = :int 
                AND es_correcta IS NULL");
            $stmt->execute([':insc' => $id_inscripcion, ':act' => $id_actividad, ':int' => $numero_intento]);
            $pendientes = $stmt->fetch()['pendientes'];
            $calificado = $pendientes == 0 ? 1 : 0;
            
            // Actualizar intento
            $stmt = $this->db->prepare("UPDATE intentos_actividad SET
                puntaje_obtenido = :punt, aprobado = :aprob, fecha_finalizacion = NOW(),
                calificado = :calif
                WHERE id_inscripcion = :insc AND id_actividad = :act AND numero_intento = :int");
            
            $stmt->execute([
                ':punt' => $puntaje_obtenido,
                ':aprob' => $aprobado,
                ':calif' => $calificado,
                ':insc' => $id_inscripcion,
                ':act' => $id_actividad,
                ':int' => $numero_intento
            ]);
            
            // Si está calificado, actualizar tabla calificaciones
            if ($calificado) {
                $this->registrarCalificacion($id_inscripcion, $id_actividad, $puntaje_obtenido);
            }
            
            return [
                'success' => true, 
                'puntaje_obtenido' => $puntaje_obtenido,
                'puntaje_total' => $actividad['puntaje_total'],
                'aprobado' => $aprobado,
                'calificado' => $calificado
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Registrar calificación en tabla general
     */
    private function registrarCalificacion(int $id_inscripcion, int $id_actividad, float $puntaje): bool {
        $actividad = $this->porId($id_actividad);
        
        $stmt = $this->db->prepare("INSERT INTO calificaciones 
            (id_inscripcion, id_material, id_actividad, calificacion)
            VALUES (:insc, :mat, :act, :calif)
            ON DUPLICATE KEY UPDATE calificacion = :calif, fecha_calificacion = NOW()");
        
        return $stmt->execute([
            ':insc' => $id_inscripcion,
            ':mat' => $actividad['id_material'],
            ':act' => $id_actividad,
            ':calif' => $puntaje
        ]);
    }

    /**
     * Obtener respuestas de un intento
     */
    public function respuestasDeIntento(int $id_inscripcion, int $id_actividad, int $numero_intento): array {
        $stmt = $this->db->prepare("SELECT r.*, p.texto_pregunta, p.tipo_pregunta, p.puntaje as puntaje_pregunta,
            o.texto_opcion, o.es_correcta as opcion_correcta
            FROM respuestas_alumno r
            JOIN preguntas p ON r.id_pregunta = p.id_pregunta
            LEFT JOIN opciones_respuesta o ON r.id_opcion = o.id_opcion
            WHERE r.id_inscripcion = :insc AND r.id_actividad = :act AND r.intento = :int
            ORDER BY p.orden ASC");
        
        $stmt->execute([':insc' => $id_inscripcion, ':act' => $id_actividad, ':int' => $numero_intento]);
        return $stmt->fetchAll();
    }

    /**
     * Calificar respuesta de texto manualmente
     */
    public function calificarRespuestaTexto(int $id_respuesta, float $puntaje, string $comentario = ''): bool {
        $stmt = $this->db->prepare("UPDATE respuestas_alumno SET
            puntos_obtenidos = :punt, comentario_profesor = :com, calificado = 1
            WHERE id_respuesta_alumno = :id");
        
        return $stmt->execute([':punt' => $puntaje, ':com' => $comentario, ':id' => $id_respuesta]);
    }

    /**
     * Obtener intentos de un alumno en una actividad
     */
    public function intentosAlumno(int $id_inscripcion, int $id_actividad): array {
        $stmt = $this->db->prepare("SELECT * FROM intentos_actividad 
            WHERE id_inscripcion = :insc AND id_actividad = :act
            ORDER BY numero_intento DESC");
        $stmt->execute([':insc' => $id_inscripcion, ':act' => $id_actividad]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener actividades pendientes de calificación para un profesor
     */
    public function actividadesPendientesProfesor(int $id_docente): array {
        $stmt = $this->db->prepare("SELECT ia.*, a.titulo as titulo_actividad, 
            u.nombre_completo as nombre_alumno, c.nombre_curso
            FROM intentos_actividad ia
            JOIN actividades a ON ia.id_actividad = a.id_actividad
            JOIN materiales_curso m ON a.id_material = m.id_material
            JOIN cursos c ON m.id_curso = c.id_curso
            JOIN inscripciones i ON ia.id_inscripcion = i.id_inscripcion
            JOIN alumnos al ON i.id_alumno = al.id_alumno
            JOIN usuarios u ON al.id_usuario = u.id_usuario
            WHERE c.id_docente = :doc AND ia.calificado = 0
            ORDER BY ia.fecha_finalizacion DESC");
        
        $stmt->execute([':doc' => $id_docente]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener todas las calificaciones de un alumno
     */
    public function calificacionesAlumno(int $id_alumno): array {
        $stmt = $this->db->prepare("SELECT c.*, m.titulo as nombre_material, 
            cur.nombre_curso, ia.numero_intento, ia.fecha_finalizacion
            FROM calificaciones c
            JOIN inscripciones i ON c.id_inscripcion = i.id_inscripcion
            JOIN materiales_curso m ON c.id_material = m.id_material
            JOIN cursos cur ON m.id_curso = cur.id_curso
            LEFT JOIN intentos_actividad ia ON c.id_actividad = ia.id_actividad 
                AND ia.id_inscripcion = i.id_inscripcion
            WHERE i.id_alumno = :al
            ORDER BY c.fecha_calificacion DESC");
        
        $stmt->execute([':al' => $id_alumno]);
        return $stmt->fetchAll();
    }
}
