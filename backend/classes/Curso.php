<?php
require_once __DIR__ . '/Database.php';

class Curso {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function todos(array $filtros = []): array {
        $where = ['c.activo = 1'];
        $params = [];

        if (!empty($filtros['nivel'])) {
            $where[] = 'c.nivel = :nivel';
            $params[':nivel'] = $filtros['nivel'];
        }
        if (isset($filtros['gratis']) && $filtros['gratis'] == 1) {
            $where[] = 'c.precio = 0';
        }
        if (!empty($filtros['buscar'])) {
            $where[] = '(c.nombre_curso LIKE :buscar OR c.descripcion LIKE :buscar)';
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }

        $sql = "SELECT c.*, 
            COALESCE(u.nombre_completo, 'Equipo TecnoFutura') AS nombre_docente,
            (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso) AS total_alumnos
            FROM cursos c
            LEFT JOIN docentes d ON c.id_docente = d.id_docente
            LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
            WHERE " . implode(' AND ', $where) . "
            ORDER BY c.precio ASC, c.nombre_curso ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function porId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT c.*, 
            COALESCE(u.nombre_completo, 'Equipo TecnoFutura') AS nombre_docente,
            d.especialidad,
            d.cedula_profesional,
            (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso) AS total_alumnos,
            (SELECT COUNT(*) FROM materiales_curso m WHERE m.id_curso = c.id_curso) AS total_lecciones
            FROM cursos c
            LEFT JOIN docentes d ON c.id_docente = d.id_docente
            LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
            WHERE c.id_curso = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function materiales(int $id_curso): array {
        $stmt = $this->db->prepare("SELECT * FROM materiales_curso 
            WHERE id_curso = :id ORDER BY orden ASC");
        $stmt->execute([':id' => $id_curso]);
        return $stmt->fetchAll();
    }

    public function inscripcionAlumno(int $id_curso, int $id_alumno): ?array {
        $stmt = $this->db->prepare("SELECT * FROM inscripciones 
            WHERE id_curso = :c AND id_alumno = :a");
        $stmt->execute([':c' => $id_curso, ':a' => $id_alumno]);
        return $stmt->fetch() ?: null;
    }

    public function misInscripciones(int $id_alumno): array {
        $stmt = $this->db->prepare("SELECT c.*, i.id_inscripcion, i.fecha_inscripcion,
            i.estado, i.progreso,
            COALESCE(u.nombre_completo, 'Equipo TecnoFutura') AS nombre_docente,
            (SELECT COUNT(*) FROM materiales_curso m WHERE m.id_curso = c.id_curso) AS total_lecciones,
            (SELECT COUNT(*) FROM progreso_lecciones p 
             WHERE p.id_inscripcion = i.id_inscripcion AND p.completado = 1) AS lecciones_completadas
            FROM inscripciones i
            JOIN cursos c ON c.id_curso = i.id_curso
            LEFT JOIN docentes d ON c.id_docente = d.id_docente
            LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
            WHERE i.id_alumno = :a AND c.activo = 1
            ORDER BY i.fecha_inscripcion DESC");
        $stmt->execute([':a' => $id_alumno]);
        return $stmt->fetchAll();
    }

    public function progresoLeccion(int $id_inscripcion, int $id_material): ?array {
        $stmt = $this->db->prepare("SELECT * FROM progreso_lecciones 
            WHERE id_inscripcion = :i AND id_material = :m");
        $stmt->execute([':i' => $id_inscripcion, ':m' => $id_material]);
        return $stmt->fetch() ?: null;
    }

    public function marcarLeccionCompletada(int $id_inscripcion, int $id_material): bool {
        // Upsert
        $stmt = $this->db->prepare("INSERT INTO progreso_lecciones 
            (id_inscripcion, id_material, completado, fecha_completado)
            VALUES (:i, :m, 1, NOW())
            ON DUPLICATE KEY UPDATE completado = 1, fecha_completado = NOW()");
        $ok = $stmt->execute([':i' => $id_inscripcion, ':m' => $id_material]);
        if ($ok) $this->recalcularProgreso($id_inscripcion);
        return $ok;
    }

    private function recalcularProgreso(int $id_inscripcion): void {
        $stmt = $this->db->prepare("SELECT i.id_curso FROM inscripciones i WHERE i.id_inscripcion = :i");
        $stmt->execute([':i' => $id_inscripcion]);
        $row = $stmt->fetch();
        if (!$row) return;

        $total = $this->db->prepare("SELECT COUNT(*) FROM materiales_curso WHERE id_curso = :c");
        $total->execute([':c' => $row['id_curso']]);
        $totalCount = (int)$total->fetchColumn();

        $done = $this->db->prepare("SELECT COUNT(*) FROM progreso_lecciones 
            WHERE id_inscripcion = :i AND completado = 1");
        $done->execute([':i' => $id_inscripcion]);
        $doneCount = (int)$done->fetchColumn();

        $progreso = $totalCount > 0 ? ($doneCount / $totalCount * 100) : 0;
        $estado = $progreso >= 100 ? 'Finalizado' : ($progreso > 0 ? 'En curso' : 'Pendiente de inicio');

        $upd = $this->db->prepare("UPDATE inscripciones SET progreso = :p, estado = :e WHERE id_inscripcion = :i");
        $upd->execute([':p' => round($progreso, 2), ':e' => $estado, ':i' => $id_inscripcion]);
    }

    public function mododulosConLecciones(int $id_curso): array {
        $rows = $this->materiales($id_curso);
        $modulos = [];
        foreach ($rows as $m) {
            $mod_title = $m['descripcion'] ? explode(':', $m['descripcion'])[0] : ('Módulo 1');
            $modulos[$mod_title][] = $m;
        }
        return $modulos;
    }

    public function inscribir(int $id_curso, int $id_alumno): array {
        // Check if already enrolled
        $exist = $this->inscripcionAlumno($id_curso, $id_alumno);
        if ($exist) return ['success' => false, 'message' => 'Ya estás inscrito en este curso.'];

        try {
            $stmt = $this->db->prepare("INSERT INTO inscripciones 
                (id_curso, id_alumno, fecha_inscripcion, estado)
                VALUES (:c, :a, NOW(), 'Inscrito')");
            $stmt->execute([':c' => $id_curso, ':a' => $id_alumno]);
            $id = $this->db->lastInsertId();
            return ['success' => true, 'id_inscripcion' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al inscribir: ' . $e->getMessage()];
        }
    }

    // Admin methods
    public function todos_admin(): array {
        $stmt = $this->db->query("SELECT c.*, 
            COALESCE(u.nombre_completo,'Equipo TecnoFutura') AS nombre_docente,
            (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso) AS total_alumnos,
            (SELECT COUNT(*) FROM materiales_curso m WHERE m.id_curso = c.id_curso) AS total_lecciones
            FROM cursos c
            LEFT JOIN docentes d ON c.id_docente = d.id_docente
            LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
            ORDER BY c.id_curso ASC");
        return $stmt->fetchAll();
    }

    public function crear(array $datos): array {
        try {
            $stmt = $this->db->prepare("INSERT INTO cursos 
                (nombre_curso, descripcion, nivel, precio, duracion_horas, id_docente, imagen_portada, activo)
                VALUES (:n,:d,:niv,:p,:h,:doc,:img,1)");
            $stmt->execute([
                ':n'   => $datos['nombre_curso'],
                ':d'   => $datos['descripcion'],
                ':niv' => $datos['nivel'],
                ':p'   => $datos['precio'],
                ':h'   => $datos['duracion_horas'],
                ':doc' => $datos['id_docente'] ?? null,
                ':img' => $datos['imagen_portada'] ?? null,
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function actualizar(int $id, array $datos): bool {
        $stmt = $this->db->prepare("UPDATE cursos SET 
            nombre_curso=:n, descripcion=:d, nivel=:niv, precio=:p,
            duracion_horas=:h, activo=:a
            WHERE id_curso=:id");
        return $stmt->execute([':n'=>$datos['nombre_curso'], ':d'=>$datos['descripcion'],
            ':niv'=>$datos['nivel'], ':p'=>$datos['precio'], ':h'=>$datos['duracion_horas'],
            ':a'=>$datos['activo']??1, ':id'=>$id]);
    }
}
