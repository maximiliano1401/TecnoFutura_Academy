<?php
$page_title = 'Gestión de Cursos';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';
requiereRol(['ADMIN']);

$obj    = new Curso();
$cursos = $obj->todos_admin();
$msg    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? '';
    $id_curso  = intval($_POST['id'] ?? 0);
    $db = Database::getInstance()->getConnection();
    if ($action === 'toggle_activo' && $id_curso) {
        $db->prepare("UPDATE cursos SET activo = NOT activo WHERE id_curso = :id")->execute([':id'=>$id_curso]);
        $msg = 'Curso actualizado.';
        $cursos = $obj->todos_admin();
    }
}

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - var(--navbar-height))">
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-shield-alt"></i><span>Admin Panel</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/usuarios.php" class="admin-nav-item"><i class="fas fa-users"></i> Usuarios</a>
    <a href="<?= SITE_URL ?>/admin/cursos.php" class="admin-nav-item active"><i class="fas fa-book-open"></i> Cursos</a>
    <a href="<?= SITE_URL ?>/admin/pagos.php" class="admin-nav-item"><i class="fas fa-credit-card"></i> Pagos</a>
    <a href="<?= SITE_URL ?>/admin/certificados.php" class="admin-nav-item"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="<?= SITE_URL ?>/admin/reportes.php" class="admin-nav-item"><i class="fas fa-chart-bar"></i> Reportes</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<div class="admin-main">
  <div class="admin-header">
    <div><h1 class="admin-title">Cursos</h1><p class="admin-subtitle">Gestiona el catálogo de cursos.</p></div>
    <a href="#" class="btn btn-primary" data-open-modal="addCursoModal"><i class="fas fa-plus"></i> Nuevo Curso</a>
  </div>
  <?php if ($msg): ?>
  <div class="alert alert-success"><i class="fas fa-check-circle alert-icon"></i><?= $msg ?></div>
  <?php endif; ?>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <table class="data-table">
      <thead><tr><th>Curso</th><th>Nivel</th><th>Precio</th><th>Alumnos</th><th>Lecciones</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($cursos as $c): ?>
        <tr>
          <td>
            <div style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($c['nombre_curso']) ?></div>
            <div style="font-size:.72rem;color:var(--text-muted)"><?= htmlspecialchars($c['nombre_docente']) ?></div>
          </td>
          <td><span class="badge badge-<?= strtolower(str_replace('á','a',$c['nivel'])) ?>"><?= $c['nivel'] ?></span></td>
          <td><?= $c['precio'] > 0 ? '<strong>$'.number_format($c['precio'],2).'</strong>' : '<span style="color:var(--success)">Gratis</span>' ?></td>
          <td><?= $c['total_alumnos'] ?></td>
          <td><?= $c['total_lecciones'] ?></td>
          <td><span class="badge <?= $c['activo'] ? 'badge-success' : 'badge-warning' ?>"><?= $c['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
          <td>
            <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" class="btn btn-ghost btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="toggle_activo">
              <input type="hidden" name="id" value="<?= $c['id_curso'] ?>">
              <button type="submit" class="btn btn-ghost btn-sm" title="<?= $c['activo'] ? 'Desactivar' : 'Activar' ?>">
                <i class="fas fa-<?= $c['activo'] ? 'pause' : 'play' ?>"></i>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
