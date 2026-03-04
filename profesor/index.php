<?php
$page_title = 'Portal del Docente';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereRol(['PROFESOR']);

$db         = Database::getInstance()->getConnection();
$id_docente = $_SESSION['info_adicional']['id_docente'] ?? 0;

// Get docente's courses
$mis_cursos = $db->prepare("SELECT c.*,
    (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso) AS total_alumnos,
    (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso AND i.estado IN ('Finalizado','Certificado')) AS finalizados,
    (SELECT COUNT(*) FROM materiales_curso m WHERE m.id_curso = c.id_curso) AS total_lecciones
    FROM cursos c WHERE c.id_docente = :d ORDER BY c.id_curso DESC");
$mis_cursos->execute([':d' => $id_docente]);
$cursos = $mis_cursos->fetchAll();

// Recent students in my courses
$recent_students = [];
if (!empty($cursos)) {
    $ids = implode(',', array_column($cursos, 'id_curso'));
    $recent_students = $db->query("SELECT u.nombre_completo, c.nombre_curso, i.estado, i.progreso, i.fecha_inscripcion
        FROM inscripciones i
        JOIN cursos c ON c.id_curso = i.id_curso
        JOIN alumnos a ON i.id_alumno = a.id_alumno
        JOIN usuarios u ON a.id_usuario = u.id_usuario
        WHERE i.id_curso IN ($ids)
        ORDER BY i.fecha_inscripcion DESC LIMIT 10")->fetchAll();
}

$total_inscritos = array_sum(array_column($cursos, 'total_alumnos'));

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:220px 1fr;min-height:calc(100vh - var(--navbar-height))">
<!-- Sidebar -->
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-chalkboard-teacher"></i><span>Portal Docente</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/profesor" class="admin-nav-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/profesor/alumnos.php" class="admin-nav-item"><i class="fas fa-users"></i> Mis Alumnos</a>
    <a href="<?= SITE_URL ?>/profesor/materiales.php" class="admin-nav-item"><i class="fas fa-film"></i> Materiales</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<!-- Main -->
<div class="admin-main">
  <div class="admin-header">
    <div>
      <div class="section-eyebrow" style="margin-bottom:.4rem">Portal Docente</div>
      <h1 class="admin-title">Bienvenido, <?= htmlspecialchars(explode(' ', $_SESSION['usuario_nombre'])[0]) ?></h1>
      <p class="admin-subtitle">Aquí está el resumen de tu actividad docente.</p>
    </div>
  </div>

  <!-- Stats -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-bottom:2.5rem">
    <?php foreach([
      ['fas fa-book','icon-blue',count($cursos),'Mis Cursos'],
      ['fas fa-users','icon-purple',$total_inscritos,'Total Alumnos'],
      ['fas fa-check-circle','icon-green',array_sum(array_column($cursos,'finalizados')),'Finalizaron'],
      ['fas fa-star','icon-amber','4.8','Calificación Promedio'],
    ] as [$ico,$cls,$val,$lbl]): ?>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;display:flex;align-items:center;gap:1rem">
      <div class="stat-icon <?= $cls ?>"><i class="<?= $ico ?>"></i></div>
      <div><div style="font-size:1.6rem;font-weight:800"><?= $val ?></div><div style="font-size:.75rem;color:var(--text-muted)"><?= $lbl ?></div></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- My courses -->
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.25rem"><i class="fas fa-book-open" style="color:var(--primary)"></i> Mis Cursos</h2>
  <?php if (empty($cursos)): ?>
  <div style="text-align:center;padding:3rem;background:var(--bg-card);border:1px dashed var(--border);border-radius:var(--radius-xl);margin-bottom:2rem">
    <i class="fas fa-book" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:1rem"></i>
    <p style="color:var(--text-muted)">No tienes cursos asignados aún. Contacta al administrador.</p>
  </div>
  <?php else: ?>
  <div class="grid-3" style="margin-bottom:2.5rem">
    <?php foreach ($cursos as $c):
      $tasa = $c['total_alumnos'] > 0 ? round($c['finalizados']/$c['total_alumnos']*100) : 0;
    ?>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;gap:.5rem">
        <div>
          <span class="badge badge-<?= strtolower(str_replace('á','a',$c['nivel'])) ?>" style="margin-bottom:.4rem;display:inline-block;font-size:.62rem"><?= $c['nivel'] ?></span>
          <h3 style="font-size:.875rem;font-weight:700;line-height:1.3"><?= htmlspecialchars($c['nombre_curso']) ?></h3>
        </div>
        <span class="badge <?= $c['activo'] ? 'badge-success' : 'badge-warning' ?>" style="font-size:.62rem;flex-shrink:0"><?= $c['activo'] ? 'Activo' : 'Inactivo' ?></span>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-bottom:1rem">
        <?php foreach([['fas fa-users',$c['total_alumnos'],'Alumnos'],['fas fa-film',$c['total_lecciones'],'Lecciones']] as [$ico,$v,$l]): ?>
        <div style="background:var(--bg-surface);border-radius:var(--radius);padding:.6rem;text-align:center">
          <div style="font-size:1.1rem;font-weight:700"><?= $v ?></div>
          <div style="font-size:.68rem;color:var(--text-muted)"><?= $l ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:.3rem;display:flex;justify-content:space-between">
        <span>Tasa de finalización</span><span style="font-weight:700;color:var(--success)"><?= $tasa ?>%</span>
      </div>
      <div class="progress progress-sm" style="margin-bottom:1.25rem">
        <div class="progress-bar" style="width:<?= $tasa ?>%"></div>
      </div>
      <div style="display:flex;gap:.5rem">
        <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-eye"></i> Ver</a>
        <a href="<?= SITE_URL ?>/profesor/materiales.php?id=<?= $c['id_curso'] ?>" class="btn btn-primary btn-sm" style="flex:1"><i class="fas fa-film"></i> Materiales</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Recent students -->
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.25rem"><i class="fas fa-users" style="color:var(--primary)"></i> Actividad Reciente de Alumnos</h2>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <table class="data-table">
      <thead><tr><th>Alumno</th><th>Curso</th><th>Estado</th><th>Progreso</th><th>Fecha Inscripción</th></tr></thead>
      <tbody>
        <?php foreach ($recent_students as $s): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:.6rem">
              <div style="width:28px;height:28px;background:var(--grad-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:#fff;flex-shrink:0"><?= strtoupper(substr($s['nombre_completo'],0,1)) ?></div>
              <span style="font-size:.875rem"><?= htmlspecialchars($s['nombre_completo']) ?></span>
            </div>
          </td>
          <td style="font-size:.82rem;color:var(--text-muted);max-width:160px"><?= htmlspecialchars($s['nombre_curso']) ?></td>
          <td>
            <span class="badge badge-<?= in_array($s['estado'],['Finalizado','Certificado']) ? 'success' : (in_array($s['estado'],['En curso','Inscrito']) ? 'info' : 'warning') ?>" style="font-size:.7rem">
              <?= $s['estado'] ?>
            </span>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:.5rem">
              <div class="progress progress-sm" style="width:80px;margin:0">
                <div class="progress-bar" style="width:<?= round(floatval($s['progreso'])) ?>%"></div>
              </div>
              <span style="font-size:.75rem;color:var(--text-muted)"><?= round(floatval($s['progreso'])) ?>%</span>
            </div>
          </td>
          <td style="font-size:.78rem;color:var(--text-muted)"><?= date('d/m/Y', strtotime($s['fecha_inscripcion'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recent_students)): ?>
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">No hay alumnos inscritos aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
