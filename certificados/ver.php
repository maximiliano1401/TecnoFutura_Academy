<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/classes/Certificado.php';

$codigo = htmlspecialchars(trim($_GET['codigo'] ?? ''));
$print  = isset($_GET['print']) && $_GET['print'] == '1';

$cert_obj = new Certificado();
$cert     = $codigo ? $cert_obj->porCodigo($codigo) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $cert ? 'Certificado — '.$cert['nombre_curso'] : 'Verificar Certificado' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/css/styles.css">
<?php if ($print): ?>
<style>
  body { background: #fff !important; }
  .navbar, #back-to-top, .btn, .verify-section { display: none !important; }
  .cert-wrapper { padding: 20px; }
  @media print {
    .cert-paper { box-shadow: none; border: 2px solid #ccc; }
  }
</style>
<?php endif; ?>
</head>
<body>
<?php if (!$print): ?>
<nav class="navbar" style="background:var(--bg-surface);border-bottom:1px solid var(--border)">
  <div class="navbar-inner">
    <a class="navbar-brand" href="<?= SITE_URL ?>">
      <div class="brand-icon"><i class="fas fa-microchip"></i></div>
      Tecno<span class="accent">Futura</span>
    </a>
  </div>
</nav>
<?php endif; ?>

<div class="cert-wrapper" style="<?= !$print ? 'margin-top:var(--navbar-height);' : '' ?>min-height:100vh;padding:3rem 1rem;display:flex;flex-direction:column;align-items:center;justify-content:center;background:<?= $print ? '#fff' : 'var(--bg-base)' ?>">

<?php if (!$cert): ?>
<!-- Verification form -->
<div style="max-width:480px;width:100%;text-align:center">
  <div style="width:80px;height:80px;background:var(--bg-card);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
    <i class="fas fa-shield-alt" style="font-size:2rem;color:var(--primary)"></i>
  </div>
  <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:.75rem">Verificar Certificado</h1>
  <p style="color:var(--text-secondary);margin-bottom:2rem">Ingresa el código del certificado para verificar su autenticidad.</p>
  <form method="GET" style="display:flex;gap:.75rem">
    <div class="form-control-icon" style="flex:1">
      <i class="fas fa-hashtag icon"></i>
      <input type="text" name="codigo" class="form-control" placeholder="Código del certificado..." value="<?= htmlspecialchars($codigo) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Verificar</button>
  </form>
  <?php if ($codigo && !$cert): ?>
  <div class="alert alert-danger" style="margin-top:1rem"><i class="fas fa-times-circle alert-icon"></i> Certificado no encontrado. Verifica el código.</div>
  <?php endif; ?>
</div>

<?php else: ?>
<!-- Certificate -->
<div class="cert-paper" style="max-width:900px;width:100%;background:#fff;border-radius:16px;overflow:hidden;<?= !$print ? 'box-shadow:0 25px 60px rgba(0,0,0,.5)' : '' ?>">
  
  <!-- Certificate inner (cream/gold design) -->
  <div style="padding:4rem;text-align:center;position:relative;background:linear-gradient(135deg,#fefefe,#faf8f0);border:1px solid #e2d9c8">
    
    <!-- Top decoration -->
    <div style="position:absolute;top:0;left:0;right:0;height:8px;background:linear-gradient(90deg,#06b6d4,#8b5cf6,#06b6d4)"></div>
    
    <!-- Logo -->
    <div style="margin-bottom:2rem">
      <div style="display:inline-flex;align-items:center;gap:.75rem;padding:1rem 2rem;background:linear-gradient(135deg,#0a0f1e,#1a2035);border-radius:50px">
        <i class="fas fa-microchip" style="color:#06b6d4;font-size:1.25rem"></i>
        <span style="font-family:'Space Grotesk',sans-serif;font-size:1.1rem;font-weight:700;color:#fff">TecnoFutura Academy</span>
      </div>
    </div>

    <!-- Certificate of Completion -->
    <div style="font-size:.8rem;letter-spacing:.25em;text-transform:uppercase;color:#8b7355;margin-bottom:.75rem;font-weight:600">Certificado de Finalización</div>
    <div style="font-family:'Playfair Display', serif;font-size:1rem;color:#4a3f30;margin-bottom:2rem;font-style:italic">This is to certify that</div>

    <!-- Student name -->
    <h1 style="font-family:'Playfair Display',serif;font-size:3rem;font-weight:700;color:#1a1210;margin-bottom:.75rem;letter-spacing:-.01em">
      <?= htmlspecialchars($cert['nombre_completo']) ?>
    </h1>

    <p style="font-family:'Playfair Display',serif;font-size:1rem;color:#4a3f30;font-style:italic;margin-bottom:1.5rem">
      ha completado satisfactoriamente el curso
    </p>

    <!-- Course name -->
    <div style="display:inline-block;padding:1rem 3rem;background:linear-gradient(135deg,#0a0f1e,#1e2a45);border-radius:12px;margin-bottom:2rem">
      <h2 style="font-family:'Space Grotesk',sans-serif;font-size:1.6rem;font-weight:700;color:#fff;margin:0"><?= htmlspecialchars($cert['nombre_curso']) ?></h2>
      <div style="font-size:.8rem;color:#64b5f6;margin-top:.4rem"><?= $cert['nivel'] ?> · <?= $cert['duracion_horas'] ?> horas de formación</div>
    </div>

    <!-- Details row -->
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;margin-bottom:2.5rem;padding:1.5rem;background:rgba(255,255,255,.6);border-radius:12px;border:1px solid #e2d9c8">
      <?php foreach([
        ['fas fa-user-tie','Instructor',$cert['nombre_docente']],
        ['fas fa-calendar','Fecha de Emisión',date('d/m/Y', strtotime($cert['fecha_emision']))],
        ['fas fa-hashtag','Código de Verificación', $cert['codigo_certificado']],
      ] as [$ico,$label,$val]): ?>
      <div>
        <div style="font-size:.72rem;color:#8b7355;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem"><?= $label ?></div>
        <div style="font-weight:600;font-size:.875rem;color:#1a1210;font-family:<?= $label==='Código de Verificación' ? 'monospace' : 'inherit' ?>"><?= htmlspecialchars($val) ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Signature area -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;max-width:500px;margin:0 auto 2rem">
      <?php foreach([
        [$cert['nombre_docente'],'Director Académico'],
        ['TecnoFutura Academy','Plataforma Certificada'],
      ] as [$name,$role]): ?>
      <div style="text-align:center">
        <div style="border-bottom:2px solid #c4b08d;padding-bottom:.5rem;margin-bottom:.5rem;font-family:'Playfair Display',serif;font-style:italic;font-size:1.1rem;color:#4a3f30"><?= htmlspecialchars($name) ?></div>
        <div style="font-size:.72rem;color:#8b7355;text-transform:uppercase;letter-spacing:.05em"><?= $role ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Verification badge -->
    <div style="display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1.25rem;background:linear-gradient(135deg,rgba(16,185,129,.1),rgba(6,182,212,.1));border:1px solid rgba(16,185,129,.3);border-radius:50px">
      <i class="fas fa-check-circle" style="color:#10b981"></i>
      <span style="font-size:.78rem;color:#10b981;font-weight:600">Certificado Verificado — TecnoFutura Academy</span>
    </div>

    <!-- Bottom decoration -->
    <div style="position:absolute;bottom:0;left:0;right:0;height:8px;background:linear-gradient(90deg,#8b5cf6,#06b6d4,#8b5cf6)"></div>
  </div>
</div>

<!-- Verify section -->
<?php if (!$print): ?>
<div class="verify-section" style="margin-top:2rem;display:flex;gap:.75rem;flex-wrap:wrap;justify-content:center">
  <a href="?codigo=<?= $cert['codigo_certificado'] ?>&print=1" class="btn btn-primary" onclick="window.print();return true;">
    <i class="fas fa-print"></i> Imprimir / Descargar PDF
  </a>
  <a href="<?= SITE_URL ?>/certificados/ver.php" class="btn btn-ghost">
    <i class="fas fa-shield-alt"></i> Verificar otro certificado
  </a>
  <button class="btn btn-ghost" data-copy="<?= SITE_URL ?>/certificados/ver.php?codigo=<?= $cert['codigo_certificado'] ?>">
    <i class="fas fa-link"></i> Copiar enlace
  </button>
</div>
<?php endif; ?>

<?php endif; ?>
</div>

<script src="<?= SITE_URL ?>/js/main.js"></script>
<?php if ($print): ?>
<script>window.addEventListener('load', () => window.print());</script>
<?php endif; ?>
</body>
</html>
