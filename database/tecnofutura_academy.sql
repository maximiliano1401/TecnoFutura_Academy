-- Base de Datos: TecnoFutura Academy
-- Fecha de creación: 26 de febrero de 2026
-- Descripción: Sistema de gestión de cursos en línea con roles diferenciados

CREATE DATABASE IF NOT EXISTS tecnofutura_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tecnofutura_academy;

-- ==============================================
-- TABLA: roles
-- Descripción: Define los roles del sistema
-- ==============================================
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar roles predefinidos
INSERT INTO roles (nombre_rol, descripcion) VALUES
('ADMIN', 'Administrador del sistema con acceso completo'),
('PROFESOR', 'Docente con permisos para gestionar cursos y calificaciones'),
('USUARIO', 'Alumno con acceso a cursos y visualización de calificaciones');

-- ==============================================
-- TABLA: usuarios
-- Descripción: Almacena todos los usuarios del sistema
-- ==============================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    correo_electronico VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    id_rol INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_sesion TIMESTAMP NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para mejorar rendimiento
CREATE INDEX idx_correo ON usuarios(correo_electronico);
CREATE INDEX idx_rol ON usuarios(id_rol);

-- ==============================================
-- TABLA: alumnos
-- Descripción: Información específica de alumnos
-- ==============================================
CREATE TABLE alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    semestre INT DEFAULT 1,
    promedio_general DECIMAL(4,2) DEFAULT 0.00,
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trigger para generar matrícula automáticamente
DELIMITER $$
CREATE TRIGGER generar_matricula BEFORE INSERT ON alumnos
FOR EACH ROW
BEGIN
    DECLARE nueva_matricula VARCHAR(20);
    DECLARE anio_actual VARCHAR(4);
    DECLARE numero_consecutivo INT;
    
    SET anio_actual = YEAR(CURRENT_DATE);
    SET numero_consecutivo = (SELECT COALESCE(MAX(CAST(SUBSTRING(matricula, 5) AS UNSIGNED)), 0) + 1 
                              FROM alumnos 
                              WHERE matricula LIKE CONCAT(anio_actual, '%'));
    
    SET nueva_matricula = CONCAT(anio_actual, LPAD(numero_consecutivo, 6, '0'));
    SET NEW.matricula = nueva_matricula;
END$$
DELIMITER ;

-- ==============================================
-- TABLA: docentes
-- Descripción: Información específica de docentes
-- ==============================================
CREATE TABLE docentes (
    id_docente INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    cedula_profesional VARCHAR(50) NOT NULL UNIQUE,
    institucion_procedencia VARCHAR(200),
    especialidad VARCHAR(150),
    anos_experiencia INT DEFAULT 0,
    fecha_alta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLA: cursos
-- Descripción: Catálogo de cursos disponibles
-- ==============================================
CREATE TABLE cursos (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nombre_curso VARCHAR(200) NOT NULL,
    descripcion TEXT,
    nivel ENUM('Básico', 'Intermedio', 'Avanzado') NOT NULL,
    duracion_horas INT NOT NULL,
    precio DECIMAL(10,2) DEFAULT 0.00,
    id_docente INT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_docente) REFERENCES docentes(id_docente) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLA: inscripciones
-- Descripción: Registro de alumnos inscritos en cursos
-- ==============================================
CREATE TABLE inscripciones (
    id_inscripcion INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_curso INT NOT NULL,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_inicio DATE,
    fecha_finalizacion DATE,
    estado ENUM('Activo', 'Completado', 'Suspendido', 'Cancelado') DEFAULT 'Activo',
    progreso DECIMAL(5,2) DEFAULT 0.00,
    calificacion_final DECIMAL(4,2) DEFAULT NULL,
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso) ON DELETE CASCADE,
    UNIQUE KEY inscripcion_unica (id_alumno, id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLA: materiales_curso
-- Descripción: Materiales y contenido de cada curso
-- ==============================================
CREATE TABLE materiales_curso (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    tipo_material ENUM('Video', 'Documento', 'Ejercicio', 'Examen', 'Recurso') NOT NULL,
    contenido TEXT,
    url_archivo VARCHAR(500),
    orden INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLA: calificaciones
-- Descripción: Registro de calificaciones por material/actividad
-- ==============================================
CREATE TABLE calificaciones (
    id_calificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INT NOT NULL,
    id_material INT NOT NULL,
    calificacion DECIMAL(4,2) NOT NULL,
    intento INT DEFAULT 1,
    comentarios TEXT,
    fecha_calificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES materiales_curso(id_material) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLA: certificados
-- Descripción: Certificados emitidos a alumnos
-- ==============================================
CREATE TABLE certificados (
    id_certificado INT AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INT NOT NULL,
    codigo_certificado VARCHAR(50) NOT NULL UNIQUE,
    fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    url_certificado VARCHAR(500),
    verificado BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLA: pagos
-- Descripción: Registro de pagos realizados
-- ==============================================
CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('Tarjeta', 'Transferencia', 'PayPal', 'Efectivo', 'Otro') NOT NULL,
    estado_pago ENUM('Pendiente', 'Completado', 'Reembolsado', 'Cancelado') DEFAULT 'Pendiente',
    referencia_pago VARCHAR(100),
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLA: permisos_rol
-- Descripción: Define permisos específicos por rol
-- ==============================================
CREATE TABLE permisos_rol (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    modulo VARCHAR(100) NOT NULL,
    puede_leer BOOLEAN DEFAULT FALSE,
    puede_crear BOOLEAN DEFAULT FALSE,
    puede_editar BOOLEAN DEFAULT FALSE,
    puede_eliminar BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE CASCADE,
    UNIQUE KEY permiso_unico (id_rol, modulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar permisos para ADMIN
INSERT INTO permisos_rol (id_rol, modulo, puede_leer, puede_crear, puede_editar, puede_eliminar) VALUES
(1, 'usuarios', TRUE, TRUE, TRUE, TRUE),
(1, 'roles', TRUE, TRUE, TRUE, TRUE),
(1, 'cursos', TRUE, TRUE, TRUE, TRUE),
(1, 'certificados', TRUE, TRUE, TRUE, TRUE),
(1, 'pagos', TRUE, TRUE, TRUE, TRUE),
(1, 'dashboard', TRUE, FALSE, FALSE, FALSE),
(1, 'reportes', TRUE, TRUE, FALSE, FALSE);

-- Insertar permisos para PROFESOR
INSERT INTO permisos_rol (id_rol, modulo, puede_leer, puede_crear, puede_editar, puede_eliminar) VALUES
(2, 'cursos', TRUE, FALSE, TRUE, FALSE),
(2, 'materiales', TRUE, TRUE, TRUE, TRUE),
(2, 'calificaciones', TRUE, TRUE, TRUE, FALSE),
(2, 'alumnos', TRUE, FALSE, FALSE, FALSE);

-- Insertar permisos para USUARIO
INSERT INTO permisos_rol (id_rol, modulo, puede_leer, puede_crear, puede_editar, puede_eliminar) VALUES
(3, 'cursos', TRUE, FALSE, FALSE, FALSE),
(3, 'inscripciones', TRUE, TRUE, FALSE, FALSE),
(3, 'calificaciones', TRUE, FALSE, FALSE, FALSE),
(3, 'certificados', TRUE, FALSE, FALSE, FALSE);

-- ==============================================
-- DATOS DE EJEMPLO
-- ==============================================

-- Insertar usuario administrador por defecto
-- Contraseña: admin123 (debe cambiarse en producción)
INSERT INTO usuarios (nombre_completo, correo_electronico, contrasena, fecha_nacimiento, id_rol) VALUES
('Administrador del Sistema', 'admin@tecnofutura.academy', '$2y$10$e0MYzXyjpJS7Pd2ALwlOEu.hkFCZLWRLfyqJWk1Yq3g8hO4xZqB5e', '1990-01-01', 1);

-- ==============================================
-- VISTAS ÚTILES
-- ==============================================

-- Vista: información completa de alumnos
CREATE VIEW vista_alumnos AS
SELECT 
    a.id_alumno,
    a.matricula,
    u.nombre_completo,
    u.correo_electronico,
    u.fecha_nacimiento,
    a.semestre,
    a.promedio_general,
    u.activo,
    a.fecha_ingreso
FROM alumnos a
INNER JOIN usuarios u ON a.id_usuario = u.id_usuario;

-- Vista: información completa de docentes
CREATE VIEW vista_docentes AS
SELECT 
    d.id_docente,
    d.cedula_profesional,
    u.nombre_completo,
    u.correo_electronico,
    u.fecha_nacimiento,
    d.institucion_procedencia,
    d.especialidad,
    d.anos_experiencia,
    u.activo,
    d.fecha_alta
FROM docentes d
INNER JOIN usuarios u ON d.id_usuario = u.id_usuario;

-- Vista: cursos con información del docente
CREATE VIEW vista_cursos AS
SELECT 
    c.id_curso,
    c.nombre_curso,
    c.descripcion,
    c.nivel,
    c.duracion_horas,
    c.precio,
    c.activo,
    u.nombre_completo AS nombre_docente,
    doc.cedula_profesional,
    c.fecha_creacion
FROM cursos c
LEFT JOIN docentes doc ON c.id_docente = doc.id_docente
LEFT JOIN usuarios u ON doc.id_usuario = u.id_usuario;

-- Vista: inscripciones con detalles
CREATE VIEW vista_inscripciones AS
SELECT 
    i.id_inscripcion,
    a.matricula,
    u.nombre_completo AS nombre_alumno,
    c.nombre_curso,
    c.nivel,
    i.fecha_inscripcion,
    i.estado,
    i.progreso,
    i.calificacion_final
FROM inscripciones i
INNER JOIN alumnos a ON i.id_alumno = a.id_alumno
INNER JOIN usuarios u ON a.id_usuario = u.id_usuario
INNER JOIN cursos c ON i.id_curso = c.id_curso;

-- ==============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ==============================================

-- Procedimiento: Obtener promedio general de un alumno
DELIMITER $$
CREATE PROCEDURE calcular_promedio_alumno(IN p_id_alumno INT)
BEGIN
    DECLARE promedio DECIMAL(4,2);
    
    SELECT AVG(calificacion_final) INTO promedio
    FROM inscripciones
    WHERE id_alumno = p_id_alumno 
      AND calificacion_final IS NOT NULL
      AND estado = 'Completado';
    
    UPDATE alumnos 
    SET promedio_general = COALESCE(promedio, 0.00)
    WHERE id_alumno = p_id_alumno;
    
    SELECT promedio AS promedio_calculado;
END$$
DELIMITER ;

-- ==============================================
-- FIN DEL SCRIPT
-- ==============================================
