<?php
$page_title = 'Mis Alumnos';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereRol(['PROFESOR']);

$db = Database::getInstance()->getConnection();
$id_docente = $_SESSION['info_adicional']['id_docente'] ?? 0;

$alumnos = $db->prepare("SELECT u.nombre_completo, u.correo_electronico, c.nombre_curso, c.nivel,
    i.estado, i.progreso, i.fecha_inscripcion, i.id_inscripcion
    FROM inscripciones i
    JOIN cursos c ON c.id_curso = i.id_curso
    JOIN alumnos a ON i.id_alumno = a.id_alumno
    JOIN usuarios u ON a.id_usuario = u.id_usuario
    WHERE c.id_docente = :d
    ORDER BY i.fecha_inscripcion DESC");
$alumnos->execute([':d' => $id_docente]);
$alumnos = $alumnos->fetchAll();

// Handle state update
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_estado'])) {
    $id_insc     = intval($_POST['id_inscripcion']);
    $nuevo_estado = htmlspecialchars($_POST['nuevo_estado']);
    $allowed = ['Inscrito','Pendiente de inicio','En curso','Tarea pendiente','Tarea enviada','Tarea calificada','Finalizado'];
    if (in_array($nuevo_estado, $allowed)) {
        $upd = $db->prepare("UPDATE inscripciones SET estado = :e WHERE id_inscripcion = :i");
        $upd->execute([':e' => $nuevo_estado, ':i' => $id_insc]);
        $msg = 'Estado del alumno actualizado.';
    }
}

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:220px 1fr;min-height:calc(100vh - var(--navbar-height))">
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-chalkboard-teacher"></i><span>Portal Docente</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/profesor" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/profesor/alumnos.php" class="admin-nav-item active"><i class="fas fa-users"></i> Mis Alumnos</a>
    <a href="<?= SITE_URL ?>/profesor/materiales.php" class="admin-nav-item"><i class="fas fa-film"></i> Materiales</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<div class="admin-main">
  <div class="admin-header"><div><h1 class="admin-title">Mis Alumnos</h1><p class="admin-subtitle"><?= count($alumnos) ?> estudiantes inscritos en tus cursos.</p></div></div>
  <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle alert-icon"></i><?= $msg ?></div><?php endif; ?>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <table class="data-table">
      <thead><tr><th>Alumno</th><th>Curso</th><th>Estado</th><th>Progreso</th><th>Actualizar Estado</th><th>Fecha</th></tr></thead>
      <tbody>
        <?php foreach ($alumnos as $a): ?>
        <tr>
          <td>
            <div style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($a['nombre_completo']) ?></div>
            <div style="font-size:.72rem;color:var(--text-muted)"><?= htmlspecialchars($a['correo_electronico']) ?></div>
          </td>
          <td style="font-size:.82rem;max-width:160px"><?= htmlspecialchars($a['nombre_curso']) ?></td>
          <td><span class="badge badge-<?= in_array($a['estado'],['Finalizado','Certificado']) ? 'success' : 'info' ?>" style="font-size:.7rem"><?= $a['estado'] ?></span></td>
          <td>
            <div style="display:flex;align-items:center;gap:.5rem">
              <div class="progress progress-sm" style="width:70px;margin:0">
                <div class="progress-bar" style="width:<?= round(floatval($a['progreso'])) ?>%"></div>
              </div>
              <span style="font-size:.72rem;color:var(--text-muted)"><?= round(floatval($a['progreso'])) ?>%</span>
            </div>
          </td>
          <td>
            <form method="POST" style="display:flex;gap:.4rem">
              <input type="hidden" name="id_inscripcion" value="<?= $a['id_inscripcion'] ?>">
              <select name="nuevo_estado" class="form-control" style="font-size:.72rem;padding:.25rem .5rem">
                <?php foreach(['Inscrito','Pendiente de inicio','En curso','Tarea pendiente','Tarea enviada','Tarea calificada','Finalizado'] as $e): ?>
                <option value="<?= $e ?>" <?= $a['estado'] === $e ? 'selected' : '' ?>><?= $e ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
            </form>
          </td>
          <td style="font-size:.75rem;color:var(--text-muted)"><?= date('d/m/Y', strtotime($a['fecha_inscripcion'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($alumnos)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No tienes alumnos inscritos aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
