<?php
$page_title = '¡Pago Exitoso!';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';

requiereAutenticacion();

$referencia = htmlspecialchars($_GET['ref'] ?? '');
$id_curso   = intval($_GET['id'] ?? 0);

$obj   = new Curso();
$curso = $id_curso ? $obj->porId($id_curso) : null;

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height);min-height:calc(100vh - var(--navbar-height));display:flex;align-items:center;justify-content:center;padding:3rem 1rem;background:var(--bg-surface)">
  <div style="max-width:580px;width:100%;text-align:center">
    <!-- Success animation -->
    <div style="width:100px;height:100px;background:linear-gradient(135deg,rgba(16,185,129,.15),rgba(6,182,212,.15));border:2px solid var(--success);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 2rem;animation:pulse 2s infinite">
      <i class="fas fa-check" style="font-size:2.5rem;color:var(--success)"></i>
    </div>

    <div class="section-eyebrow" style="display:inline-block;margin-bottom:1rem">¡Todo listo!</div>
    <h1 style="font-size:2.2rem;font-weight:800;margin-bottom:1rem">¡Pago Confirmado!</h1>
    <p style="color:var(--text-secondary);font-size:1.05rem;line-height:1.7;margin-bottom:2rem">
      Tu inscripción ha sido procesada exitosamente. Ya tienes acceso completo al contenido del curso.
    </p>

    <!-- Details card -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;margin-bottom:2.5rem;text-align:left">
      <h3 style="font-size:1rem;font-weight:600;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--border)">Detalles de la Compra</h3>
      
      <?php if ($referencia): ?>
      <div style="display:flex;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid var(--border);font-size:.875rem">
        <span style="color:var(--text-muted)">Referencia</span>
        <span style="font-family:monospace;font-weight:600;color:var(--primary)"><?= $referencia ?></span>
      </div>
      <?php endif; ?>
      <?php if ($curso): ?>
      <div style="display:flex;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid var(--border);font-size:.875rem">
        <span style="color:var(--text-muted)">Curso</span>
        <span style="font-weight:600;max-width:280px;text-align:right"><?= htmlspecialchars($curso['nombre_curso']) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid var(--border);font-size:.875rem">
        <span style="color:var(--text-muted)">Monto</span>
        <span style="font-weight:600;color:var(--success)">$<?= number_format($curso['precio'],2) ?> MXN</span>
      </div>
      <?php endif; ?>
      <div style="display:flex;justify-content:space-between;padding:.7rem 0;font-size:.875rem">
        <span style="color:var(--text-muted)">Fecha</span>
        <span style="font-weight:600"><?= date('d/m/Y H:i') ?></span>
      </div>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <?php if ($id_curso): ?>
      <a href="<?= SITE_URL ?>/lms/curso.php?id=<?= $id_curso ?>" class="btn btn-primary btn-lg">
        <i class="fas fa-play"></i> Comenzar Ahora
      </a>
      <?php endif; ?>
      <a href="<?= SITE_URL ?>/lms" class="btn btn-outline btn-lg">
        <i class="fas fa-graduation-cap"></i> Mis Cursos
      </a>
    </div>

    <p style="margin-top:2rem;font-size:.8rem;color:var(--text-muted)">
      <i class="fas fa-envelope" style="color:var(--primary)"></i>
      Se enviará un comprobante a <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'tu correo') ?>
    </p>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
