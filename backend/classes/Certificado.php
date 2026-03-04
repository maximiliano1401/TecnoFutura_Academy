<?php
require_once __DIR__ . '/Database.php';

class Certificado {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function porAlumno(int $id_alumno): array {
        $stmt = $this->db->prepare("SELECT cert.*, c.nombre_curso, u.nombre_completo,
            COALESCE(ud.nombre_completo, 'Equipo TecnoFutura') AS nombre_docente,
            c.nivel
            FROM certificados cert
            JOIN inscripciones i ON cert.id_inscripcion = i.id_inscripcion
            JOIN cursos c ON i.id_curso = c.id_curso
            JOIN alumnos a ON i.id_alumno = a.id_alumno
            JOIN usuarios u ON a.id_usuario = u.id_usuario
            LEFT JOIN docentes d ON c.id_docente = d.id_docente
            LEFT JOIN usuarios ud ON d.id_usuario = ud.id_usuario
            WHERE i.id_alumno = :a
            ORDER BY cert.fecha_emision DESC");
        $stmt->execute([':a' => $id_alumno]);
        return $stmt->fetchAll();
    }

    public function porCodigo(string $codigo): ?array {
        $stmt = $this->db->prepare("SELECT cert.*, c.nombre_curso, u.nombre_completo,
            c.duracion_horas, c.nivel,
            COALESCE(ud.nombre_completo, 'Equipo TecnoFutura') AS nombre_docente
            FROM certificados cert
            JOIN inscripciones i ON cert.id_inscripcion = i.id_inscripcion
            JOIN cursos c ON i.id_curso = c.id_curso
            JOIN alumnos a ON i.id_alumno = a.id_alumno
            JOIN usuarios u ON a.id_usuario = u.id_usuario
            LEFT JOIN docentes d ON c.id_docente = d.id_docente
            LEFT JOIN usuarios ud ON d.id_usuario = ud.id_usuario
            WHERE cert.codigo_certificado = :codigo");
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetch() ?: null;
    }

    public function emitir(int $id_inscripcion): array {
        // Check eligibility
        $stmt = $this->db->prepare("SELECT i.*, c.nombre_curso FROM inscripciones i 
            JOIN cursos c ON c.id_curso = i.id_curso
            WHERE i.id_inscripcion = :id");
        $stmt->execute([':id' => $id_inscripcion]);
        $insc = $stmt->fetch();
        if (!$insc) return ['success' => false, 'message' => 'Inscripción no encontrada.'];
        if ($insc['estado'] !== 'Finalizado' && floatval($insc['progreso']) < 100) {
            return ['success' => false, 'message' => 'El alumno aún no ha completado el curso (progreso: ' . $insc['progreso'] . '%).'];
        }

        // Check if already issued
        $existing = $this->db->prepare("SELECT id_certificado FROM certificados WHERE id_inscripcion = :i");
        $existing->execute([':i' => $id_inscripcion]);
        if ($existing->fetch()) return ['success' => false, 'message' => 'El certificado ya fue emitido.'];

        // Generate unique code
        $codigo = 'TF' . strtoupper(substr(md5($id_inscripcion . time()), 0, 10));

        $ins = $this->db->prepare("INSERT INTO certificados (id_inscripcion, codigo_certificado, fecha_emision)
            VALUES (:i, :c, NOW())");
        $ok = $ins->execute([':i' => $id_inscripcion, ':c' => $codigo]);

        if ($ok) {
            // Update enrollment state
            $this->db->prepare("UPDATE inscripciones SET estado = 'Certificado' WHERE id_inscripcion = :i")
                ->execute([':i' => $id_inscripcion]);
            return ['success' => true, 'codigo' => $codigo, 'id_certificado' => $this->db->lastInsertId()];
        }
        return ['success' => false, 'message' => 'Error al emitir certificado.'];
    }

    public function todos(): array {
        $stmt = $this->db->query("SELECT cert.*, c.nombre_curso, u.nombre_completo,
            u.correo_electronico
            FROM certificados cert
            JOIN inscripciones i ON cert.id_inscripcion = i.id_inscripcion
            JOIN cursos c ON i.id_curso = c.id_curso
            JOIN alumnos a ON i.id_alumno = a.id_alumno
            JOIN usuarios u ON a.id_usuario = u.id_usuario
            ORDER BY cert.fecha_emision DESC");
        return $stmt->fetchAll();
    }

    public function totalCertificados(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM certificados")->fetchColumn();
    }
}
