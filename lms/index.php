<?php
$page_title = 'Mis Cursos';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';
require_once __DIR__ . '/../backend/classes/Certificado.php';

requiereRol(['USUARIO']);

$id_alumno = $_SESSION['usuario_rol_id'] ?? 0;
$obj       = new Curso();
$inscripciones = $obj->misInscripciones($id_alumno);

$cert_obj    = new Certificado();
$certs       = $cert_obj->porAlumno($id_alumno);
$total_certs = count($certs);

// Stats
$total       = count($inscripciones);
$en_curso    = count(array_filter($inscripciones, fn($i) => in_array($i['estado'], ['En curso','Inscrito'])));
$finalizados = count(array_filter($inscripciones, fn($i) => $i['estado'] === 'Finalizado' || $i['estado'] === 'Certificado'));

// Check if any course is 100% → can get cert
foreach ($inscripciones as &$insc) {
    $insc['pct'] = min(100, round(floatval($insc['progreso'] ?? 0)));
    $insc['total_l'] = $insc['total_lecciones'] ?: 1;
    $insc['done_l'] = round($insc['pct'] / 100 * $insc['total_l']);
}
unset($insc);

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height);min-height:calc(100vh - var(--navbar-height))">
  <!-- Header -->
  <div style="background:var(--bg-surface);border-bottom:1px solid var(--border);padding:2.5rem 0">
    <div class="container">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
        <div>
          <div class="section-eyebrow" style="margin-bottom:.5rem">Portal LMS</div>
          <h1 style="font-size:2rem;font-weight:800;margin-bottom:.25rem">
            Hola, <?= htmlspecialchars(explode(' ', $_SESSION['usuario_nombre'])[0]) ?> 👋
          </h1>
          <p style="color:var(--text-secondary)">Continúa donde lo dejaste. Tu próximo logro te espera.</p>
        </div>
        <a href="<?= SITE_URL ?>/cursos" class="btn btn-primary">
          <i class="fas fa-plus"></i> Explorar más cursos
        </a>
      </div>
    </div>
  </div>

  <div class="container" style="padding-top:2.5rem;padding-bottom:4rem">
    <!-- Stats row -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-bottom:3rem">
      <?php foreach([
        ['fas fa-book-open','icon-blue',$total,'Cursos Inscritos'],
        ['fas fa-spinner','icon-purple',$en_curso,'En Progreso'],
        ['fas fa-check-circle','icon-green',$finalizados,'Completados'],
        ['fas fa-certificate','icon-amber',$total_certs,'Certificados'],
      ] as [$ico,$cls,$val,$lbl]): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;display:flex;align-items:center;gap:1rem">
        <div class="stat-icon <?= $cls ?>"><i class="<?= $ico ?>"></i></div>
        <div>
          <div style="font-size:1.6rem;font-weight:800"><?= $val ?></div>
          <div style="font-size:.78rem;color:var(--text-muted)"><?= $lbl ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($inscripciones)): ?>
    <!-- Empty state -->
    <div style="text-align:center;padding:5rem;background:var(--bg-card);border:1px dashed var(--border);border-radius:var(--radius-xl)">
      <i class="fas fa-graduation-cap" style="font-size:4rem;color:var(--text-muted);margin-bottom:1.5rem;display:block"></i>
      <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:.75rem">Aún no estás inscrito en ningún curso</h2>
      <p style="color:var(--text-secondary);margin-bottom:2rem;max-width:400px;margin-left:auto;margin-right:auto">
        Explora nuestro catálogo de cursos de Arduino y Ensamblador. ¡El primero es completamente gratis!
      </p>
      <a href="<?= SITE_URL ?>/cursos" class="btn btn-primary btn-lg"><i class="fas fa-search"></i> Explorar Cursos</a>
    </div>
    <?php else: ?>

    <!-- Current Courses -->
    <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1.5rem">
      <i class="fas fa-play-circle" style="color:var(--primary)"></i> Mis Cursos
    </h2>
    <div class="grid-3" style="margin-bottom:3rem">
      <?php foreach($inscripciones as $insc):
        $estado_badge = [
          'Inscrito'          => 'badge-success',
          'En curso'          => 'badge-info',
          'Pendiente de inicio'=> 'badge-warning',
          'Finalizado'        => 'badge-success',
          'Certificado'       => 'badge-amber',
          'Tarea pendiente'   => 'badge-warning',
        ][$insc['estado']] ?? 'badge';
        $can_cert = $insc['pct'] >= 100 && $insc['estado'] !== 'Certificado';
        $nivel_slug = strtolower(str_replace('á','a',$insc['nivel']));
      ?>
      <div class="lms-course-card fade-in">
        <div style="height:8px;background:var(--bg-surface);border-radius:4px;overflow:hidden;margin-bottom:1.25rem">
          <div style="height:100%;width:<?= $insc['pct'] ?>%;background:var(--grad-primary);border-radius:4px;transition:width .6s ease"></div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem;margin-bottom:.75rem">
          <div>
            <span class="badge badge-<?= $nivel_slug ?>" style="font-size:.62rem;margin-bottom:.4rem;display:inline-block"><?= $insc['nivel'] ?></span>
            <h3 style="font-size:.95rem;font-weight:700;line-height:1.3"><?= htmlspecialchars($insc['nombre_curso']) ?></h3>
          </div>
          <span class="badge <?= $estado_badge ?>" style="font-size:.62rem;white-space:nowrap;flex-shrink:0"><?= $insc['estado'] ?></span>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;font-size:.75rem;color:var(--text-muted);margin-bottom:1rem">
          <i class="fas fa-user-tie"></i><span><?= htmlspecialchars($insc['nombre_docente']) ?></span>
          <span>·</span>
          <i class="fas fa-clock"></i><span><?= $insc['duracion_horas'] ?>h</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.75rem;color:var(--text-muted);margin-bottom:.4rem">
          <span><?= $insc['done_l'] ?>/<?= $insc['total_l'] ?> lecciones</span>
          <span style="font-weight:700;color:<?= $insc['pct'] >= 100 ? 'var(--success)' : 'var(--primary)' ?>"><?= $insc['pct'] ?>%</span>
        </div>
        <div class="progress progress-sm" style="margin-bottom:1.25rem">
          <div class="progress-bar" style="width:<?= $insc['pct'] ?>%"></div>
        </div>
        <div style="display:flex;gap:.6rem">
          <a href="<?= SITE_URL ?>/lms/curso.php?id=<?= $insc['id_curso'] ?>" class="btn btn-primary btn-sm" style="flex:1">
            <i class="fas fa-<?= $insc['pct'] > 0 ? 'play' : 'rocket' ?>"></i>
            <?= $insc['pct'] > 0 ? 'Continuar' : 'Comenzar' ?>
          </a>
          <?php if ($can_cert): ?>
          <a href="<?= SITE_URL ?>/lms/ajax/generar_cert.php?id=<?= $insc['id_inscripcion'] ?>" class="btn btn-success btn-sm" title="Obtener Certificado">
            <i class="fas fa-certificate"></i>
          </a>
          <?php endif; ?>
        </div>
        <?php if ($insc['estado'] === 'Certificado'): ?>
        <a href="<?= SITE_URL ?>/lms/certificados.php" class="btn btn-ghost btn-sm btn-block" style="margin-top:.5rem;font-size:.75rem">
          <i class="fas fa-download"></i> Ver certificado
        </a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Certificates -->
    <?php if ($total_certs > 0): ?>
    <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1.5rem">
      <i class="fas fa-certificate" style="color:var(--warning)"></i> Mis Certificados
    </h2>
    <div class="grid-3">
      <?php foreach($certs as $cert): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
          <div style="width:44px;height:44px;background:linear-gradient(135deg,var(--warning),#f59e0b);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-certificate" style="color:#fff"></i>
          </div>
          <div>
            <div style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($cert['nombre_curso']) ?></div>
            <div style="font-size:.72rem;color:var(--text-muted)"><?= date('d/m/Y', strtotime($cert['fecha_emision'])) ?></div>
          </div>
        </div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:1rem;font-family:monospace;background:var(--bg-surface);padding:.4rem .6rem;border-radius:4px">
          <?= $cert['codigo_certificado'] ?>
        </div>
        <div style="display:flex;gap:.5rem">
          <a href="<?= SITE_URL ?>/certificados/ver.php?codigo=<?= $cert['codigo_certificado'] ?>" class="btn btn-primary btn-sm" style="flex:1" target="_blank">
            <i class="fas fa-eye"></i> Ver
          </a>
          <a href="<?= SITE_URL ?>/certificados/ver.php?codigo=<?= $cert['codigo_certificado'] ?>&print=1" class="btn btn-ghost btn-sm" title="Descargar/Imprimir">
            <i class="fas fa-download"></i>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
