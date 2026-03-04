<?php
$page_title = 'Gestión de Pagos';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Pago.php';
requiereRol(['ADMIN']);

$pago_obj    = new Pago();
$pagos       = $pago_obj->todos();
$pago_stats  = $pago_obj->estadisticas();

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - var(--navbar-height))">
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-shield-alt"></i><span>Admin Panel</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/usuarios.php" class="admin-nav-item"><i class="fas fa-users"></i> Usuarios</a>
    <a href="<?= SITE_URL ?>/admin/cursos.php" class="admin-nav-item"><i class="fas fa-book-open"></i> Cursos</a>
    <a href="<?= SITE_URL ?>/admin/pagos.php" class="admin-nav-item active"><i class="fas fa-credit-card"></i> Pagos</a>
    <a href="<?= SITE_URL ?>/admin/certificados.php" class="admin-nav-item"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="<?= SITE_URL ?>/admin/reportes.php" class="admin-nav-item"><i class="fas fa-chart-bar"></i> Reportes</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<div class="admin-main">
  <div class="admin-header">
    <div><h1 class="admin-title">Pagos</h1><p class="admin-subtitle">Historial de todas las transacciones.</p></div>
  </div>
  <!-- Stats -->
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:2rem">
    <?php foreach([
      ['fas fa-peso-sign','icon-green','$'.number_format($pago_stats['ingresos_totales']??0,2),'Ingresos Totales'],
      ['fas fa-calendar','icon-blue','$'.number_format($pago_stats['ingresos_mes']??0,2),'Ingresos este Mes'],
      ['fas fa-check-circle','icon-amber',$pago_stats['pagos_completados']??0,'Pagos Completados'],
    ] as [$ico,$cls,$val,$lbl]): ?>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;display:flex;align-items:center;gap:1rem">
      <div class="stat-icon <?= $cls ?>"><i class="<?= $ico ?>"></i></div>
      <div><div style="font-size:1.5rem;font-weight:800"><?= $val ?></div><div style="font-size:.75rem;color:var(--text-muted)"><?= $lbl ?></div></div>
    </div>
    <?php endforeach; ?>
  </div>
  <!-- Table -->
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <table class="data-table">
      <thead><tr><th>Alumno</th><th>Curso</th><th>Monto</th><th>Método</th><th>Referencia</th><th>Estado</th><th>Fecha</th></tr></thead>
      <tbody>
        <?php foreach ($pagos as $p): ?>
        <tr>
          <td>
            <div style="font-weight:600;font-size:.85rem"><?= htmlspecialchars($p['nombre_alumno'] ?? $p['nombre_completo'] ?? '') ?></div>
            <div style="font-size:.72rem;color:var(--text-muted)"><?= htmlspecialchars($p['correo_electronico']) ?></div>
          </td>
          <td style="max-width:180px;font-size:.82rem"><?= htmlspecialchars($p['nombre_curso']) ?></td>
          <td style="font-weight:700;color:var(--success)">$<?= number_format($p['monto'],2) ?></td>
          <td><span class="badge"><?= htmlspecialchars($p['metodo_pago']) ?></span></td>
          <td style="font-family:monospace;font-size:.72rem;color:var(--text-muted)"><?= $p['referencia_pago'] ?></td>
          <td><span class="badge badge-<?= $p['estado_pago'] === 'completado' ? 'success' : 'warning' ?>"><?= $p['estado_pago'] ?></span></td>
          <td style="font-size:.78rem;color:var(--text-muted)"><?= date('d/m/Y H:i', strtotime($p['fecha_pago'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($pagos)): ?>
        <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)"><i class="fas fa-inbox" style="display:block;font-size:2rem;margin-bottom:.75rem"></i>No hay pagos registrados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
