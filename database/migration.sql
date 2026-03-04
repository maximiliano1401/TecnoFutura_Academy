-- ============================================================
-- TecnoFutura Academy — Migration v2 (idempotent-safe)
-- Adapta el schema original al código PHP del proyecto
-- Run AFTER tecnofutura_academy.sql, BEFORE seed_data.sql
-- ============================================================

USE tecnofutura_academy;

-- ============================================================
-- 1. usuarios: hacer fecha_nacimiento nullable (ya aplicado)
-- ============================================================
ALTER TABLE usuarios
  MODIFY fecha_nacimiento DATE NULL DEFAULT NULL;

-- ============================================================
-- 2. docentes: agregar biografia, asegurar cedula default
-- ============================================================
ALTER TABLE docentes
  ADD COLUMN biografia TEXT NULL AFTER especialidad,
  MODIFY cedula_profesional VARCHAR(50) NOT NULL DEFAULT '';

-- ============================================================
-- 3. materiales_curso: agregar columnas y corregir ENUM
-- ============================================================
ALTER TABLE materiales_curso
  ADD COLUMN descripcion TEXT NULL AFTER titulo,
  ADD COLUMN url_material VARCHAR(500) NULL AFTER contenido,
  ADD COLUMN duracion_minutos INT NULL DEFAULT 0 AFTER orden,
  MODIFY tipo_material ENUM(
    'video','documento','texto','ejercicio','evaluacion'
  ) NOT NULL DEFAULT 'video';

-- ============================================================
-- 4. inscripciones: expandir ENUM estado
-- ============================================================
ALTER TABLE inscripciones
  MODIFY estado ENUM(
    'Activo','Completado','Suspendido','Cancelado',
    'Inscrito','En curso','Finalizado','Certificado'
  ) NULL DEFAULT 'Inscrito';

-- ============================================================
-- 5. pagos: cambiar ENUMs a minúsculas (que usa el PHP)
-- ============================================================
ALTER TABLE pagos
  MODIFY metodo_pago ENUM(
    'tarjeta','transferencia','paypal','efectivo','oxxo','otro'
  ) NOT NULL DEFAULT 'tarjeta',
  MODIFY estado_pago ENUM(
    'pendiente','completado','reembolsado','cancelado'
  ) NULL DEFAULT 'completado';

-- ============================================================
-- 6. Crear tabla progreso_lecciones
-- ============================================================
CREATE TABLE IF NOT EXISTS progreso_lecciones (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  id_inscripcion  INT NOT NULL,
  id_material     INT NOT NULL,
  completado      TINYINT(1) NOT NULL DEFAULT 0,
  fecha_completado TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY uq_progreso (id_inscripcion, id_material),
  FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Migration applied successfully!' AS status;
