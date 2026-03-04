<?php
$page_title = 'Gestión de Usuarios';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereRol(['ADMIN']);

$db = Database::getInstance()->getConnection();

// Handle actions
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $uid    = intval($_POST['id'] ?? 0);
    if ($action === 'toggle_active' && $uid) {
        $db->prepare("UPDATE usuarios SET activo = NOT activo WHERE id_usuario = :id")->execute([':id'=>$uid]);
        $msg = 'Estado del usuario actualizado.';
    }
}

$rol_filter = $_GET['rol'] ?? '';
$search     = $_GET['q'] ?? '';
$where = ['1=1'];
$params = [];
if ($rol_filter) { $where[] = 'r.nombre_rol = :rol'; $params[':rol'] = $rol_filter; }
if ($search) { $where[] = '(u.nombre_completo LIKE :q OR u.correo_electronico LIKE :q)'; $params[':q'] = '%'.$search.'%'; }

$stmt = $db->prepare("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol 
    WHERE " . implode(' AND ', $where) . " ORDER BY u.fecha_registro DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - var(--navbar-height))">
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-shield-alt"></i><span>Admin Panel</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/usuarios.php" class="admin-nav-item active"><i class="fas fa-users"></i> Usuarios</a>
    <a href="<?= SITE_URL ?>/admin/cursos.php" class="admin-nav-item"><i class="fas fa-book-open"></i> Cursos</a>
    <a href="<?= SITE_URL ?>/admin/pagos.php" class="admin-nav-item"><i class="fas fa-credit-card"></i> Pagos</a>
    <a href="<?= SITE_URL ?>/admin/certificados.php" class="admin-nav-item"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="<?= SITE_URL ?>/admin/reportes.php" class="admin-nav-item"><i class="fas fa-chart-bar"></i> Reportes</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<div class="admin-main">
  <div class="admin-header">
    <div>
      <h1 class="admin-title">Usuarios</h1>
      <p class="admin-subtitle">Gestiona todos los usuarios de la plataforma.</p>
    </div>
  </div>
  <?php if ($msg): ?>
  <div class="alert alert-success"><i class="fas fa-check-circle alert-icon"></i><?= $msg ?></div>
  <?php endif; ?>
  <!-- Filters -->
  <form method="GET" style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <div class="form-control-icon" style="flex:1;min-width:200px">
      <i class="fas fa-search icon"></i>
      <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o correo..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <select name="rol" class="form-control" style="width:160px">
      <option value="">Todos los roles</option>
      <?php foreach(['ADMIN','PROFESOR','USUARIO'] as $r): ?>
      <option value="<?= $r ?>" <?= $rol_filter === $r ? 'selected' : '' ?>><?= $r ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Filtrar</button>
    <a href="?" class="btn btn-ghost">Limpiar</a>
  </form>
  <!-- Table -->
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--border);font-size:.875rem;color:var(--text-muted)">
      <strong style="color:var(--text-primary)"><?= count($users) ?></strong> usuarios encontrados
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Usuario</th><th>Correo</th><th>Rol</th><th>Fecha Registro</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:.65rem">
                <div style="width:32px;height:32px;background:var(--grad-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.75rem;flex-shrink:0">
                  <?= strtoupper(substr($u['nombre_completo'],0,1)) ?>
                </div>
                <span style="font-weight:500"><?= htmlspecialchars($u['nombre_completo']) ?></span>
              </div>
            </td>
            <td><?= htmlspecialchars($u['correo_electronico']) ?></td>
            <td><span class="badge badge-<?= $u['nombre_rol'] === 'ADMIN' ? 'danger' : ($u['nombre_rol'] === 'PROFESOR' ? 'info' : 'success') ?>"><?= $u['nombre_rol'] ?></span></td>
            <td><?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></td>
            <td><span class="badge <?= $u['activo'] ? 'badge-success' : 'badge-warning' ?>"><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
            <td>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="toggle_active">
                <input type="hidden" name="id" value="<?= $u['id_usuario'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm" data-confirm="¿Cambiar estado del usuario <?= htmlspecialchars($u['nombre_completo']) ?>?" title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                  <i class="fas fa-<?= $u['activo'] ? 'ban' : 'check' ?>"></i>
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
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
