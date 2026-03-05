<?php
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereRol(['USUARIO']);

require_once __DIR__ . '/../backend/classes/Curso.php';
require_once __DIR__ . '/../backend/classes/Actividad.php';
require_once __DIR__ . '/../backend/classes/Database.php';

$id_alumno = $_SESSION['usuario_rol_id'];
$cursoObj = new Curso();

// Get student's enrollments (not used but kept for future reference)
$inscripciones = $cursoObj->misInscripciones($id_alumno);

// Get all grades for activities
$db = Database::getInstance()->getConnection();
$sql = "SELECT 
          c.id_curso,
          c.nombre_curso,
          mc.titulo as actividad_titulo,
          a.puntaje_total,
          a.puntaje_minimo_aprobatorio,
          ia.numero_intento,
          ia.puntaje_obtenido,
          ia.fecha_finalizacion,
          ia.calificado,
          CASE 
            WHEN ia.puntaje_obtenido >= a.puntaje_minimo_aprobatorio THEN 1
            ELSE 0
          END as aprobado
        FROM intentos_actividad ia
        INNER JOIN inscripciones i ON ia.id_inscripcion = i.id_inscripcion
        INNER JOIN actividades a ON ia.id_actividad = a.id_actividad
        INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
        INNER JOIN cursos c ON mc.id_curso = c.id_curso
        WHERE i.id_alumno = :id_alumno
          AND ia.fecha_finalizacion IS NOT NULL
          AND ia.calificado = 1
        ORDER BY c.nombre_curso, ia.fecha_finalizacion DESC";

$stmt = $db->prepare($sql);
$stmt->execute([':id_alumno' => $id_alumno]);
$calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by course
$calificacionesPorCurso = [];
$estadisticasPorCurso = [];

foreach ($calificaciones as $cal) {
    $id_curso = $cal['id_curso'];
    
    if (!isset($calificacionesPorCurso[$id_curso])) {
        $calificacionesPorCurso[$id_curso] = [
            'nombre' => $cal['nombre_curso'],
            'calificaciones' => []
        ];
        $estadisticasPorCurso[$id_curso] = [
            'total_actividades' => 0,
            'aprobadas' => 0,
            'puntos_totales' => 0,
            'puntos_obtenidos' => 0
        ];
    }
    
    $calificacionesPorCurso[$id_curso]['calificaciones'][] = $cal;
    $estadisticasPorCurso[$id_curso]['total_actividades']++;
    
    if ($cal['aprobado']) {
        $estadisticasPorCurso[$id_curso]['aprobadas']++;
    }
    
    $estadisticasPorCurso[$id_curso]['puntos_totales'] += $cal['puntaje_total'];
    $estadisticasPorCurso[$id_curso]['puntos_obtenidos'] += $cal['puntaje_obtenido'];
}

include_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <nav class="sidebar-nav">
      <a href="<?= SITE_URL ?>/lms" class="sidebar-item">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
      </a>
      <a href="<?= SITE_URL ?>/lms/calificaciones.php" class="sidebar-item active">
        <i class="fas fa-graduation-cap"></i>
        <span>Mis Calificaciones</span>
      </a>
      <a href="<?= SITE_URL ?>/lms/certificados.php" class="sidebar-item">
        <i class="fas fa-certificate"></i>
        <span>Certificados</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <div class="content-header">
      <div>
        <h1 class="page-title">Mis Calificaciones</h1>
        <p class="page-subtitle">Revisa tu desempeño en las evaluaciones de tus cursos</p>
      </div>
    </div>

    <?php if (empty($calificaciones)): ?>
    <div class="card">
      <div class="card-body" style="text-align:center;padding:3rem">
        <i class="fas fa-clipboard-list" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem;display:block"></i>
        <p style="color:var(--text-muted);margin-bottom:1.5rem">Aún no tienes evaluaciones calificadas.</p>
        <a href="<?= SITE_URL ?>/lms" class="btn btn-primary">
          <i class="fas fa-book"></i> Ir a Mis Cursos
        </a>
      </div>
    </div>
    <?php else: ?>
    
    <!-- Overall Statistics -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;margin-bottom:2rem">
      <?php
        $total_evaluaciones = count($calificaciones);
        $total_aprobadas = array_sum(array_column($calificaciones, 'aprobado'));
        $total_puntos = array_sum(array_column($calificaciones, 'puntaje_total'));
        $total_obtenidos = array_sum(array_column($calificaciones, 'puntaje_obtenido'));
        $promedio_general = $total_puntos > 0 ? round(($total_obtenidos / $total_puntos) * 100) : 0;
      ?>
      <div class="stat-card stat-primary">
        <div class="stat-value"><?= $total_evaluaciones ?></div>
        <div class="stat-label">Evaluaciones Completadas</div>
      </div>
      <div class="stat-card stat-success">
        <div class="stat-value"><?= $total_aprobadas ?></div>
        <div class="stat-label">Evaluaciones Aprobadas</div>
      </div>
      <div class="stat-card stat-warning">
        <div class="stat-value"><?= $promedio_general ?>%</div>
        <div class="stat-label">Promedio General</div>
      </div>
      <div class="stat-card stat-info">
        <div class="stat-value"><?= $total_obtenidos ?> / <?= $total_puntos ?></div>
        <div class="stat-label">Puntos Totales</div>
      </div>
    </div>

    <!-- Grades by Course -->
    <?php foreach ($calificacionesPorCurso as $id_curso => $cursoDatos): ?>
    <div class="card" style="margin-bottom:2rem">
      <div class="card-header" style="background:var(--bg-surface);padding:1.5rem;border-bottom:2px solid var(--border)">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1.5rem;flex-wrap:wrap">
          <h2 style="font-size:1.3rem;font-weight:700;margin:0"><?= htmlspecialchars($cursoDatos['nombre']) ?></h2>
          <div style="display:flex;gap:1.5rem;font-size:.85rem">
            <?php
              $stats = $estadisticasPorCurso[$id_curso];
              $promedio_curso = $stats['puntos_totales'] > 0 
                ? round(($stats['puntos_obtenidos'] / $stats['puntos_totales']) * 100) 
                : 0;
            ?>
            <span style="color:var(--text-muted)">
              <i class="fas fa-check-circle" style="color:var(--success)"></i>
              <?= $stats['aprobadas'] ?>/<?= $stats['total_actividades'] ?> aprobadas
            </span>
            <span style="color:var(--text-muted)">
              <i class="fas fa-chart-line" style="color:var(--primary)"></i>
              Promedio: <strong style="color:<?= $promedio_curso >= 70 ? 'var(--success)' : 'var(--danger)' ?>"><?= $promedio_curso ?>%</strong>
            </span>
          </div>
        </div>
      </div>
      <div class="card-body" style="padding:0">
        <table class="data-table">
          <thead>
            <tr>
              <th>Actividad</th>
              <th>Intento</th>
              <th>Fecha</th>
              <th>Puntaje</th>
              <th>Porcentaje</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cursoDatos['calificaciones'] as $cal): ?>
            <?php
              $porcentaje = round(($cal['puntaje_obtenido'] / $cal['puntaje_total']) * 100);
              $aprobado = $cal['aprobado'];
            ?>
            <tr>
              <td style="font-weight:600"><?= htmlspecialchars($cal['actividad_titulo']) ?></td>
              <td><span class="badge badge-secondary">Intento #<?= $cal['numero_intento'] ?></span></td>
              <td style="font-size:.85rem"><?= date('d/m/Y H:i', strtotime($cal['fecha_finalizacion'])) ?></td>
              <td>
                <strong style="color:<?= $aprobado ? 'var(--success)' : 'var(--danger)' ?>">
                  <?= $cal['puntaje_obtenido'] ?>/<?= $cal['puntaje_total'] ?>
                </strong>
              </td>
              <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                  <div class="progress" style="flex:1;max-width:100px">
                    <div class="progress-bar" style="width:<?= $porcentaje ?>%;background:<?= $aprobado ? 'var(--success)' : 'var(--danger)' ?>"></div>
                  </div>
                  <span style="font-size:.85rem;font-weight:600;color:<?= $aprobado ? 'var(--success)' : 'var(--danger)' ?>">
                    <?= $porcentaje ?>%
                  </span>
                </div>
              </td>
              <td>
                <?php if ($aprobado): ?>
                <span class="badge badge-success"><i class="fas fa-check"></i> Aprobado</span>
                <?php else: ?>
                <span class="badge badge-danger"><i class="fas fa-times"></i> No Aprobado</span>
                <?php endif; ?>
              </td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="alert('Próximamente: Ver detalles de respuestas')">
                  <i class="fas fa-eye"></i> Ver Detalles
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- Legend -->
    <div class="card">
      <div class="card-body" style="background:var(--bg-secondary)">
        <div style="display:flex;gap:2rem;flex-wrap:wrap;font-size:.85rem">
          <div>
            <strong style="color:var(--text-primary)">Criterios de Aprobación:</strong>
          </div>
          <div>
            <i class="fas fa-check-circle" style="color:var(--success)"></i>
            <span style="color:var(--text-secondary)">Puntaje igual o superior al mínimo requerido</span>
          </div>
          <div>
            <i class="fas fa-times-circle" style="color:var(--danger)"></i>
            <span style="color:var(--text-secondary)">Puntaje inferior al mínimo requerido</span>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>
  </main>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
