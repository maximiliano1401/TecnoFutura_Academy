<?php
$page_title = 'Certificados';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Certificado.php';
requiereRol(['ADMIN']);

$cert_obj = new Certificado();
$certs    = $cert_obj->todos();

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - var(--navbar-height))">
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-shield-alt"></i><span>Admin Panel</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/usuarios.php" class="admin-nav-item"><i class="fas fa-users"></i> Usuarios</a>
    <a href="<?= SITE_URL ?>/admin/cursos.php" class="admin-nav-item"><i class="fas fa-book-open"></i> Cursos</a>
    <a href="<?= SITE_URL ?>/admin/pagos.php" class="admin-nav-item"><i class="fas fa-credit-card"></i> Pagos</a>
    <a href="<?= SITE_URL ?>/admin/certificados.php" class="admin-nav-item active"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="<?= SITE_URL ?>/admin/reportes.php" class="admin-nav-item"><i class="fas fa-chart-bar"></i> Reportes</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<div class="admin-main">
  <div class="admin-header">
    <div><h1 class="admin-title">Certificados</h1><p class="admin-subtitle">Todos los certificados emitidos.</p></div>
    <div style="background:var(--bg-card);border:1px solid var(--border);padding:.6rem 1rem;border-radius:var(--radius);font-size:.875rem">
      <i class="fas fa-certificate" style="color:var(--warning)"></i>
      <strong><?= count($certs) ?></strong> certificados emitidos
    </div>
  </div>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <table class="data-table">
      <thead><tr><th>Alumno</th><th>Correo</th><th>Curso</th><th>Código</th><th>Fecha Emisión</th><th>Acción</th></tr></thead>
      <tbody>
        <?php foreach ($certs as $c): ?>
        <tr>
          <td style="font-weight:600"><?= htmlspecialchars($c['nombre_completo']) ?></td>
          <td style="font-size:.8rem;color:var(--text-muted)"><?= htmlspecialchars($c['correo_electronico']) ?></td>
          <td style="max-width:200px;font-size:.82rem"><?= htmlspecialchars($c['nombre_curso']) ?></td>
          <td style="font-family:monospace;font-size:.72rem;color:var(--primary)"><?= $c['codigo_certificado'] ?></td>
          <td style="font-size:.8rem;color:var(--text-muted)"><?= date('d/m/Y', strtotime($c['fecha_emision'])) ?></td>
          <td>
            <a href="<?= SITE_URL ?>/certificados/ver.php?codigo=<?= $c['codigo_certificado'] ?>" class="btn btn-ghost btn-sm" target="_blank">
              <i class="fas fa-eye"></i> Ver
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($certs)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No hay certificados emitidos aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
