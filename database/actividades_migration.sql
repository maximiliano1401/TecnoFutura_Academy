-- ============================================================
-- TecnoFutura Academy - Sistema de Actividades/Evaluaciones
-- Migraci贸n para soporte de preguntas y calificaciones
-- ============================================================

USE tecnofutura_academy;

-- ============================================================
-- TABLA: actividades
-- Vincular materiales tipo "evaluacion" con configuraci贸n
-- ============================================================
CREATE TABLE IF NOT EXISTS actividades (
    id_actividad INT AUTO_INCREMENT PRIMARY KEY,
    id_material INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    puntaje_total INT DEFAULT 100,
    puntaje_minimo_aprobatorio INT DEFAULT 60,
    duracion_minutos INT DEFAULT 0 COMMENT 'Si es 0, sin l铆mite de tiempo',
    intentos_permitidos INT DEFAULT 1 COMMENT 'N煤mero de intentos, 0 = ilimitado',
    mostrar_respuestas TINYINT(1) DEFAULT 1 COMMENT 'Mostrar respuestas correctas despu茅s',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_material) REFERENCES materiales_curso(id_material) ON DELETE CASCADE,
    INDEX idx_material (id_material)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: preguntas
-- Preguntas de una actividad/evaluaci贸n
-- ============================================================
CREATE TABLE IF NOT EXISTS preguntas (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    id_actividad INT NOT NULL,
    tipo_pregunta ENUM('opcion_multiple', 'verdadero_falso', 'respuesta_corta') NOT NULL DEFAULT 'opcion_multiple',
    texto_pregunta TEXT NOT NULL,
    puntaje INT DEFAULT 10,
    orden INT DEFAULT 0,
    explicacion TEXT COMMENT 'Explicaci贸n de la respuesta correcta',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_actividad) REFERENCES actividades(id_actividad) ON DELETE CASCADE,
    INDEX idx_actividad (id_actividad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: opciones_respuesta
-- Opciones para preguntas de opci贸n m煤ltiple
-- ============================================================
CREATE TABLE IF NOT EXISTS opciones_respuesta (
    id_opcion INT AUTO_INCREMENT PRIMARY KEY,
    id_pregunta INT NOT NULL,
    texto_opcion TEXT NOT NULL,
    es_correcta TINYINT(1) DEFAULT 0,
    orden INT DEFAULT 0,
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta) ON DELETE CASCADE,
    INDEX idx_pregunta (id_pregunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: respuestas_alumno
-- Respuestas de alumnos a actividades
-- ============================================================
CREATE TABLE IF NOT EXISTS respuestas_alumno (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INT NOT NULL,
    id_actividad INT NOT NULL,
    id_pregunta INT NOT NULL,
    id_opcion INT NULL COMMENT 'Para preguntas de opci贸n m煤ltiple',
    texto_respuesta TEXT NULL COMMENT 'Para preguntas de respuesta corta',
    es_correcta TINYINT(1) NULL COMMENT 'Calculado autom谩ticamente para opci贸n m煤ltiple, manual para texto',
    puntaje_obtenido DECIMAL(5,2) DEFAULT 0,
    intento INT DEFAULT 1,
    fecha_respuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion) ON DELETE CASCADE,
    FOREIGN KEY (id_actividad) REFERENCES actividades(id_actividad) ON DELETE CASCADE,
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta) ON DELETE CASCADE,
    FOREIGN KEY (id_opcion) REFERENCES opciones_respuesta(id_opcion) ON DELETE SET NULL,
    INDEX idx_inscripcion_actividad (id_inscripcion, id_actividad),
    INDEX idx_pregunta (id_pregunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: intentos_actividad
-- Registro de intentos completos de actividades
-- ============================================================
CREATE TABLE IF NOT EXISTS intentos_actividad (
    id_intento INT AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INT NOT NULL,
    id_actividad INT NOT NULL,
    numero_intento INT NOT NULL,
    puntaje_obtenido DECIMAL(5,2) DEFAULT 0,
    puntaje_total DECIMAL(5,2) NOT NULL,
    aprobado TINYINT(1) DEFAULT 0,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_finalizacion TIMESTAMP NULL,
    tiempo_empleado_minutos INT NULL,
    calificado TINYINT(1) DEFAULT 0 COMMENT 'Si requiere revisi贸n manual',
    comentario_profesor TEXT,
    FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion) ON DELETE CASCADE,
    FOREIGN KEY (id_actividad) REFERENCES actividades(id_actividad) ON DELETE CASCADE,
    UNIQUE KEY unique_intento (id_inscripcion, id_actividad, numero_intento),
    INDEX idx_inscripcion (id_inscripcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Actualizar tabla calificaciones existente
-- A帽adir referencia a actividades
-- ============================================================
ALTER TABLE calificaciones 
ADD COLUMN id_actividad INT NULL AFTER id_material,
ADD FOREIGN KEY (id_actividad) REFERENCES actividades(id_actividad) ON DELETE SET NULL;

SELECT 'Migraci贸n de actividades aplicada exitosamente!' AS status;
