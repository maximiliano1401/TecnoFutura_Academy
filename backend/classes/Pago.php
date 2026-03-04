<?php
require_once __DIR__ . '/Database.php';

class Pago {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Registra un pago y activa la inscripción al curso.
     */
    public function procesar(array $datos): array {
        try {
            $this->db->beginTransaction();

            // 1. Check if already enrolled
            $curso_obj = null;
            if (class_exists('Curso')) {
                $curso_obj = new Curso();
                $insc_exist = $curso_obj->inscripcionAlumno($datos['id_curso'], $datos['id_alumno']);
                if ($insc_exist && $insc_exist['estado'] !== 'pendiente_pago') {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Ya estás inscrito en este curso.'];
                }
            }

            // 2. Simulate payment gateway processing
            $referencia = 'TF-' . strtoupper(bin2hex(random_bytes(5)));
            $status = 'completado'; // Always succeeds (fictitious)

            // 3. Get or create inscripcion
            $stmt = $this->db->prepare("SELECT id_inscripcion FROM inscripciones 
                WHERE id_curso = :c AND id_alumno = :a");
            $stmt->execute([':c' => $datos['id_curso'], ':a' => $datos['id_alumno']]);
            $insc = $stmt->fetch();

            if ($insc) {
                $id_inscripcion = $insc['id_inscripcion'];
                $this->db->prepare("UPDATE inscripciones SET estado = 'Inscrito' WHERE id_inscripcion = :i")
                    ->execute([':i' => $id_inscripcion]);
            } else {
                $s = $this->db->prepare("INSERT INTO inscripciones (id_curso, id_alumno, fecha_inscripcion, estado)
                    VALUES (:c, :a, NOW(), 'Inscrito')");
                $s->execute([':c' => $datos['id_curso'], ':a' => $datos['id_alumno']]);
                $id_inscripcion = $this->db->lastInsertId();
            }

            // 4. Record payment
            $stmt2 = $this->db->prepare("INSERT INTO pagos 
                (id_inscripcion, monto, metodo_pago, referencia_pago, estado_pago, fecha_pago)
                VALUES (:insc, :monto, :metodo, :ref, :status, NOW())");
            $stmt2->execute([
                ':insc'   => $id_inscripcion,
                ':monto'  => $datos['monto'],
                ':metodo' => $datos['metodo_pago'] ?? 'tarjeta',
                ':ref'    => $referencia,
                ':status' => $status,
            ]);
            $id_pago = $this->db->lastInsertId();

            $this->db->commit();

            return [
                'success'         => true,
                'id_pago'         => $id_pago,
                'id_inscripcion'  => $id_inscripcion,
                'referencia'      => $referencia,
                'message'         => 'Pago procesado exitosamente.',
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al procesar pago: ' . $e->getMessage()];
        }
    }

    public function historialAlumno(int $id_alumno): array {
        $stmt = $this->db->prepare("SELECT p.*, c.nombre_curso, i.id_curso
            FROM pagos p
            JOIN inscripciones i ON p.id_inscripcion = i.id_inscripcion
            JOIN cursos c ON i.id_curso = c.id_curso
            WHERE i.id_alumno = :a
            ORDER BY p.fecha_pago DESC");
        $stmt->execute([':a' => $id_alumno]);
        return $stmt->fetchAll();
    }

    public function todos(): array {
        $stmt = $this->db->query("SELECT p.*, c.nombre_curso, u.nombre_completo AS nombre_alumno,
            u.correo_electronico
            FROM pagos p
            JOIN inscripciones i ON p.id_inscripcion = i.id_inscripcion
            JOIN cursos c ON i.id_curso = c.id_curso
            JOIN alumnos a ON i.id_alumno = a.id_alumno
            JOIN usuarios u ON a.id_usuario = u.id_usuario
            ORDER BY p.fecha_pago DESC");
        return $stmt->fetchAll();
    }

    public function estadisticas(): array {
        $row = $this->db->query("SELECT 
            COUNT(*) AS total_pagos,
            SUM(monto) AS ingresos_totales,
            SUM(CASE WHEN MONTH(fecha_pago) = MONTH(NOW()) THEN monto ELSE 0 END) AS ingresos_mes,
            COUNT(CASE WHEN estado_pago = 'completado' THEN 1 END) AS pagos_completados
            FROM pagos WHERE estado_pago = 'completado'")->fetch();
        return $row;
    }
}
