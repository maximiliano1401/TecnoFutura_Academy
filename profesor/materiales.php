<?php
$page_title = 'Gestión de Materiales';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereRol(['PROFESOR']);

$db         = Database::getInstance()->getConnection();
$id_docente = $_SESSION['info_adicional']['id_docente'] ?? 0;

// Get docente's courses
$cursos_stmt = $db->prepare("SELECT id_curso, nombre_curso FROM cursos WHERE id_docente = :d AND activo = 1 ORDER BY nombre_curso");
$cursos_stmt->execute([':d' => $id_docente]);
$mis_cursos = $cursos_stmt->fetchAll();

$selected_curso = intval($_GET['id'] ?? ($mis_cursos[0]['id_curso'] ?? 0));
$materiales = [];
if ($selected_curso) {
    $m = $db->prepare("SELECT * FROM materiales_curso WHERE id_curso = :c ORDER BY orden ASC");
    $m->execute([':c' => $selected_curso]);
    $materiales = $m->fetchAll();
}

// Handle add
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_material') {
        $stmt = $db->prepare("INSERT INTO materiales_curso (id_curso, titulo, descripcion, tipo_material, url_material, orden, duracion_minutos)
            VALUES (:c,:t,:d,:tipo,:url,:o,:dur)");
        $ok = $stmt->execute([
            ':c'   => intval($_POST['id_curso']),
            ':t'   => htmlspecialchars($_POST['titulo']),
            ':d'   => htmlspecialchars($_POST['descripcion'] ?? ''),
            ':tipo' => htmlspecialchars($_POST['tipo_material']),
            ':url' => htmlspecialchars($_POST['url_material'] ?? ''),
            ':o'   => intval($_POST['orden'] ?? count($materiales)+1),
            ':dur' => intval($_POST['duracion_minutos'] ?? 0),
        ]);
        $msg = $ok ? 'Material agregado exitosamente.' : 'Error al agregar material.';
        if ($ok) {
            $m = $db->prepare("SELECT * FROM materiales_curso WHERE id_curso = :c ORDER BY orden ASC");
            $m->execute([':c' => $selected_curso]);
            $materiales = $m->fetchAll();
        }
    } elseif ($action === 'delete_material') {
        $db->prepare("DELETE FROM materiales_curso WHERE id_material = :i")->execute([':i' => intval($_POST['id_material'])]);
        $msg = 'Material eliminado.';
        $m = $db->prepare("SELECT * FROM materiales_curso WHERE id_curso = :c ORDER BY orden ASC");
        $m->execute([':c' => $selected_curso]);
        $materiales = $m->fetchAll();
    }
}

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:220px 1fr;min-height:calc(100vh - var(--navbar-height))">
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-chalkboard-teacher"></i><span>Portal Docente</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/profesor" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/profesor/alumnos.php" class="admin-nav-item"><i class="fas fa-users"></i> Mis Alumnos</a>
    <a href="<?= SITE_URL ?>/profesor/materiales.php" class="admin-nav-item active"><i class="fas fa-film"></i> Materiales</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<div class="admin-main">
  <div class="admin-header">
    <div><h1 class="admin-title">Materiales del Curso</h1><p class="admin-subtitle">Administra el contenido de tus cursos.</p></div>
    <a href="#" class="btn btn-primary" data-open-modal="addMatModal"><i class="fas fa-plus"></i> Agregar Lección</a>
  </div>
  <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle alert-icon"></i><?= $msg ?></div><?php endif; ?>
  
  <!-- Course selector -->
  <div style="margin-bottom:1.5rem">
    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
      <?php foreach ($mis_cursos as $c): ?>
      <a href="?id=<?= $c['id_curso'] ?>" class="btn btn-sm <?= $selected_curso == $c['id_curso'] ? 'btn-primary' : 'btn-ghost' ?>">
        <?= htmlspecialchars($c['nombre_curso']) ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($selected_curso): ?>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between">
      <span style="font-size:.875rem;color:var(--text-muted)"><?= count($materiales) ?> lecciones</span>
    </div>
    <table class="data-table">
      <thead><tr><th>#</th><th>Título</th><th>Tipo</th><th>Duración</th><th>URL</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($materiales as $m): ?>
        <tr>
          <td style="color:var(--text-muted);font-size:.8rem"><?= $m['orden'] ?></td>
          <td style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($m['titulo']) ?></td>
          <td><span class="badge"><?= ucfirst($m['tipo_material']) ?></span></td>
          <td style="font-size:.8rem;color:var(--text-muted)"><?= $m['duracion_minutos'] ? $m['duracion_minutos'].' min' : '—' ?></td>
          <td style="font-size:.72rem;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted)"><?= $m['url_material'] ?: '—' ?></td>
          <td>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="delete_material">
              <input type="hidden" name="id_material" value="<?= $m['id_material'] ?>">
              <input type="hidden" name="id" value="<?= $selected_curso ?>">
              <button type="submit" class="btn btn-ghost btn-sm text-danger-item" data-confirm="¿Eliminar esta lección?">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($materiales)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Este curso no tiene materiales aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
</div>

<!-- Add Material Modal -->
<div class="modal-backdrop" id="addMatModal">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title">Agregar Lección</h2>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="add_material">
      <input type="hidden" name="id_curso" value="<?= $selected_curso ?>">
      <input type="hidden" name="id" value="<?= $selected_curso ?>">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Título de la Lección</label>
          <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3"></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Tipo</label>
            <select name="tipo_material" class="form-control">
              <option value="video">Video</option>
              <option value="documento">Documento</option>
              <option value="texto">Texto/Artículo</option>
              <option value="ejercicio">Ejercicio</option>
              <option value="evaluacion">Evaluación</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Duración (minutos)</label>
            <input type="number" name="duracion_minutos" class="form-control" min="0" placeholder="ej: 25">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">URL del Material (video/documento)</label>
          <input type="text" name="url_material" class="form-control" placeholder="https://youtube.com/... o ruta al archivo">
        </div>
        <div class="form-group">
          <label class="form-label">Orden</label>
          <input type="number" name="orden" class="form-control" value="<?= count($materiales)+1 ?>" min="1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost modal-close">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Lección</button>
      </div>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
