<?php
$page_title = 'Curso';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';

requiereRol(['USUARIO']);

$id_curso  = intval($_GET['id'] ?? 0);
$id_alumno = $_SESSION['usuario_rol_id'] ?? 0;
$obj       = new Curso();
$curso     = $obj->porId($id_curso);

if (!$curso) { header('Location: ' . SITE_URL . '/lms'); exit; }

$insc = $obj->inscripcionAlumno($id_curso, $id_alumno);
if (!$insc) {
    header('Location: ' . SITE_URL . '/cursos/detalle.php?id=' . $id_curso);
    exit;
}

$id_inscripcion = $insc['id_inscripcion'];
$page_title     = $curso['nombre_curso'];
$materiales     = $obj->materiales($id_curso);
$modulos        = $obj->mododulosConLecciones($id_curso);
$progreso       = floatval($insc['progreso'] ?? 0);

// Current lesson
$id_material = intval($_GET['leccion'] ?? 0);
$current_lesson = null;
foreach ($materiales as $m) {
    if ($m['id_material'] == $id_material) { $current_lesson = $m; break; }
}
if (!$current_lesson && !empty($materiales)) $current_lesson = $materiales[0];

// Progress per lesson
$completadas = [];
foreach ($materiales as $m) {
    $p = $obj->progresoLeccion($id_inscripcion, $m['id_material']);
    if ($p && $p['completado']) $completadas[] = $m['id_material'];
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:1fr 320px;min-height:calc(100vh - var(--navbar-height))">

  <!-- Main content area -->
  <div style="display:flex;flex-direction:column">
    <!-- Video/Content Area -->
    <div style="background:#000;min-height:400px;display:flex;align-items:center;justify-content:center;position:relative">
      <?php if ($current_lesson): ?>
        <?php if ($current_lesson['tipo_material'] === 'video' && $current_lesson['url_material']): ?>
        <div class="video-container" style="width:100%;aspect-ratio:16/9;position:relative">
          <?php if (str_contains($current_lesson['url_material'], 'youtube') || str_contains($current_lesson['url_material'], 'youtu.be')): ?>
          <iframe src="<?= htmlspecialchars($current_lesson['url_material']) ?>" style="width:100%;height:100%;border:none" allowfullscreen></iframe>
          <?php else: ?>
          <video controls style="width:100%;height:100%;max-height:500px" poster="">
            <source src="<?= SITE_URL ?>/<?= htmlspecialchars($current_lesson['url_material']) ?>" type="video/mp4">
            Tu navegador no soporta reproducción de video.
          </video>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Non-video lesson -->
        <div style="color:#fff;text-align:center;padding:3rem">
          <i class="fas fa-<?= $current_lesson['tipo_material'] === 'documento' ? 'file-pdf' : 'newspaper' ?>"
             style="font-size:4rem;color:var(--primary);margin-bottom:1.5rem;display:block"></i>
          <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:.75rem"><?= htmlspecialchars($current_lesson['titulo']) ?></h2>
          <?php if ($current_lesson['url_material']): ?>
          <a href="<?= SITE_URL ?>/<?= htmlspecialchars($current_lesson['url_material']) ?>" target="_blank" class="btn btn-primary btn-lg">
            <i class="fas fa-eye"></i> Abrir Recurso
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      <?php else: ?>
      <div style="color:#fff;text-align:center;padding:3rem">
        <i class="fas fa-film" style="font-size:4rem;color:var(--text-muted);margin-bottom:1.5rem;display:block"></i>
        <p style="color:var(--text-muted)">El contenido de esta lección estará disponible pronto.</p>
      </div>
      <?php endif; ?>
    </div>

    <!-- Lesson info -->
    <div style="padding:1.75rem 2rem;border-bottom:1px solid var(--border);background:var(--bg-card)">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1.5rem;flex-wrap:wrap">
        <div style="flex:1">
          <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.5rem">
            <a href="<?= SITE_URL ?>/lms" style="color:var(--text-muted);text-decoration:none">Mis Cursos</a>
            <i class="fas fa-chevron-right" style="font-size:.5rem;margin:0 .4rem"></i>
            <span style="color:var(--text-primary)"><?= htmlspecialchars($curso['nombre_curso']) ?></span>
          </div>
          <h1 style="font-size:1.3rem;font-weight:700;margin-bottom:.5rem">
            <?= htmlspecialchars($current_lesson ? $current_lesson['titulo'] : 'Selecciona una lección') ?>
          </h1>
          <?php if ($current_lesson): ?>
          <div style="display:flex;align-items:center;gap:1rem;font-size:.8rem;color:var(--text-muted)">
            <span><i class="fas fa-<?= $current_lesson['tipo_material'] === 'video' ? 'play-circle' : 'file-alt' ?>"></i> <?= ucfirst($current_lesson['tipo_material']) ?></span>
            <?php if ($current_lesson['duracion_minutos']): ?>
            <span><i class="fas fa-clock"></i> <?= $current_lesson['duracion_minutos'] ?> min</span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php if ($current_lesson && !in_array($current_lesson['id_material'], $completadas)): ?>
        <button class="btn btn-primary btn-sm mark-complete-btn"
                data-lesson="<?= $current_lesson['id_material'] ?>"
                data-inscripcion="<?= $id_inscripcion ?>">
          <i class="fas fa-check"></i> Marcar como completada
        </button>
        <?php elseif ($current_lesson): ?>
        <span class="btn btn-success btn-sm" style="pointer-events:none;opacity:.85"><i class="fas fa-check"></i> Completada</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Progress bar -->
    <div style="padding:1.25rem 2rem;background:var(--bg-surface);border-bottom:1px solid var(--border)">
      <div style="display:flex;align-items:center;gap:1rem">
        <div style="flex:1">
          <div style="display:flex;justify-content:space-between;font-size:.75rem;color:var(--text-muted);margin-bottom:.4rem">
            <span>Progreso del curso</span>
            <span id="progressText" style="font-weight:700;color:var(--primary)"><?= round($progreso) ?>%</span>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar" id="courseProgress" style="width:<?= $progreso ?>%"></div>
          </div>
        </div>
        <div style="text-align:center;min-width:80px">
          <div style="font-size:.78rem;color:var(--text-muted)"><?= count($completadas) ?>/<?= count($materiales) ?></div>
          <div style="font-size:.68rem;color:var(--text-muted)">lecciones</div>
        </div>
      </div>
    </div>

    <!-- Description -->
    <?php if ($current_lesson && $current_lesson['descripcion']): ?>
    <div style="padding:1.75rem 2rem">
      <h3 style="font-size:1rem;font-weight:600;margin-bottom:.75rem">Acerca de esta lección</h3>
      <p style="color:var(--text-secondary);line-height:1.7"><?= nl2br(htmlspecialchars($current_lesson['descripcion'])) ?></p>
    </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar -->
  <aside style="background:var(--bg-card);border-left:1px solid var(--border);overflow-y:auto;max-height:calc(100vh - var(--navbar-height));position:sticky;top:var(--navbar-height)">
    <div style="padding:1.25rem 1.25rem .75rem;border-bottom:1px solid var(--border)">
      <h2 style="font-size:.95rem;font-weight:700;margin-bottom:.25rem;line-height:1.3"><?= htmlspecialchars($curso['nombre_curso']) ?></h2>
      <div style="font-size:.75rem;color:var(--text-muted)"><?= count($completadas) ?>/<?= count($materiales) ?> lecciones · <?= round($progreso) ?>% completado</div>
      <div class="progress progress-sm" style="margin-top:.6rem">
        <div class="progress-bar" style="width:<?= $progreso ?>%"></div>
      </div>
    </div>

    <!-- Curriculum in sidebar -->
    <div style="padding:.5rem 0">
      <?php $mod_n = 0; foreach ($modulos as $modulo_titulo => $sessiones): $mod_n++; ?>
      <div>
        <div style="padding:.75rem 1.25rem;cursor:pointer;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);background:var(--bg-surface)"
             onclick="this.nextElementSibling.classList.toggle('d-none')">
          <div>
            <div style="font-size:.78rem;font-weight:600"><?= htmlspecialchars($modulo_titulo) ?></div>
            <div style="font-size:.68rem;color:var(--text-muted)"><?= count($sessiones) ?> lecciones</div>
          </div>
          <i class="fas fa-chevron-down" style="font-size:.65rem;color:var(--text-muted)"></i>
        </div>
        <div class="lesson-list">
          <?php foreach ($sessiones as $l):
            $is_done   = in_array($l['id_material'], $completadas);
            $is_active = $current_lesson && $l['id_material'] == $current_lesson['id_material'];
          ?>
          <a href="?id=<?= $id_curso ?>&leccion=<?= $l['id_material'] ?>"
             class="lesson-item <?= $is_done ? 'completed' : '' ?> <?= $is_active ? 'active' : '' ?>"
             data-lesson="<?= $l['id_material'] ?>">
            <div style="display:flex;align-items:center;gap:.65rem;flex:1">
              <?php if ($is_done): ?>
              <i class="fas fa-check-circle" style="color:var(--success);flex-shrink:0;font-size:.85rem"></i>
              <?php elseif ($is_active): ?>
              <i class="fas fa-play-circle" style="color:var(--primary);flex-shrink:0;font-size:.85rem"></i>
              <?php else: ?>
              <i class="fas fa-circle" style="color:var(--text-muted);flex-shrink:0;font-size:.5rem;margin:.175rem 0"></i>
              <?php endif; ?>
              <div>
                <div style="font-size:.8rem;line-height:1.35"><?= htmlspecialchars($l['titulo']) ?></div>
                <?php if ($l['duracion_minutos']): ?>
                <div style="font-size:.68rem;color:var(--text-muted)"><?= $l['duracion_minutos'] ?> min</div>
                <?php endif; ?>
              </div>
            </div>
            <?php if ($is_active): ?>
            <span style="width:3px;height:100%;position:absolute;left:0;top:0;background:var(--primary);border-radius:0 2px 2px 0"></span>
            <?php endif; ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </aside>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
