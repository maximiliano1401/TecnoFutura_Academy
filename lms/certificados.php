<?php
$page_title = 'Mis Certificados';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Certificado.php';

requiereRol(['USUARIO']);

$cert_obj = new Certificado();
$certs    = $cert_obj->porAlumno($_SESSION['usuario_rol_id'] ?? 0);

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);padding:3rem 0 4rem;min-height:calc(100vh - var(--navbar-height))">
  <div class="container">
    <div class="section-eyebrow" style="margin-bottom:.75rem">LMS</div>
    <h1 style="font-size:2rem;font-weight:800;margin-bottom:2rem">Mis Certificados</h1>

    <?php if (empty($certs)): ?>
    <div style="text-align:center;padding:4rem;background:var(--bg-card);border:1px dashed var(--border);border-radius:var(--radius-xl)">
      <i class="fas fa-certificate" style="font-size:3.5rem;color:var(--text-muted);margin-bottom:1.25rem;display:block"></i>
      <h3 style="margin-bottom:.75rem">Aún no tienes certificados</h3>
      <p style="color:var(--text-muted);margin-bottom:1.5rem">Completa un curso al 100% para obtener tu certificado verificable.</p>
      <a href="<?= SITE_URL ?>/lms" class="btn btn-primary">Ir a Mis Cursos</a>
    </div>
    <?php else: ?>
    <div class="grid-3">
      <?php foreach ($certs as $cert): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.75rem;text-align:center">
        <div style="width:64px;height:64px;background:linear-gradient(135deg,var(--warning),#f59e0b);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
          <i class="fas fa-certificate" style="color:#fff;font-size:1.6rem"></i>
        </div>
        <h3 style="font-size:.95rem;font-weight:700;margin-bottom:.5rem"><?= htmlspecialchars($cert['nombre_curso']) ?></h3>
        <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.5rem"><?= date('d/m/Y', strtotime($cert['fecha_emision'])) ?></div>
        <div style="font-family:monospace;font-size:.7rem;color:var(--text-muted);background:var(--bg-surface);padding:.35rem .6rem;border-radius:4px;margin-bottom:1.25rem"><?= $cert['codigo_certificado'] ?></div>
        <div style="display:flex;gap:.5rem;justify-content:center">
          <a href="<?= SITE_URL ?>/certificados/ver.php?codigo=<?= $cert['codigo_certificado'] ?>" class="btn btn-primary btn-sm" target="_blank"><i class="fas fa-eye"></i> Ver</a>
          <a href="<?= SITE_URL ?>/certificados/ver.php?codigo=<?= $cert['codigo_certificado'] ?>&print=1" class="btn btn-ghost btn-sm"><i class="fas fa-print"></i></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
