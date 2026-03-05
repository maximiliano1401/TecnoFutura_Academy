<?php
$page_title = 'Detalle del Curso';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/classes/Curso.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . SITE_URL . '/cursos'); exit; }

$obj = new Curso();
$curso = $obj->porId($id);
if (!$curso) { header('Location: ' . SITE_URL . '/cursos'); exit; }

$page_title = $curso['nombre_curso'];
$materiales  = $obj->materiales($id);
$modulos     = $obj->mododulosConLecciones($id);
$is_logged   = Usuario::estaAutenticado();
$is_free     = $curso['precio'] == 0;

$insc = null;
if ($is_logged && $_SESSION['usuario_rol'] === 'USUARIO') {
    $id_alumno = $_SESSION['info_adicional']['id_alumno'] ?? 0;
    $insc = $obj->inscripcionAlumno($id, $id_alumno);
}

$precio_original = $is_free ? null : $curso['precio'] * 1.4;
$descuento       = $precio_original ? round((1 - $curso['precio']/$precio_original)*100) : 0;
$rating          = 4.8;
$reviews         = 142;

// Build what_you_learn from description
$aprenderas = [
    'Fundamentos de arquitectura de computadoras',
    'Programación en ensamblador x86',
    'Gestión de registros y memoria',
    'Interfaces con Arduino',
    'Depuración y análisis de código',
    'Proyectos reales aplicables a tu portafolio',
];

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height)">

<!-- COURSE HERO -->
<div style="background:var(--bg-surface);border-bottom:1px solid var(--border);padding:3.5rem 0 0">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 380px;gap:3rem;align-items:start">
      <!-- Left -->
      <div>
        <!-- Breadcrumb -->
        <div style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--text-muted);margin-bottom:1.25rem">
          <a href="<?= SITE_URL ?>" style="color:var(--text-muted);text-decoration:none">Inicio</a>
          <i class="fas fa-chevron-right" style="font-size:.6rem"></i>
          <a href="<?= SITE_URL ?>/cursos" style="color:var(--text-muted);text-decoration:none">Cursos</a>
          <i class="fas fa-chevron-right" style="font-size:.6rem"></i>
          <span style="color:var(--primary)"><?= htmlspecialchars($curso['nombre_curso']) ?></span>
        </div>

        <span class="badge badge-<?= strtolower(str_replace('á','a',$curso['nivel'])) ?>" style="margin-bottom:1rem;display:inline-block">
          <?= $curso['nivel'] ?>
        </span>

        <h1 style="font-size:2.1rem;font-weight:800;line-height:1.2;margin-bottom:1.25rem">
          <?= htmlspecialchars($curso['nombre_curso']) ?>
        </h1>

        <p style="color:var(--text-secondary);font-size:1.05rem;line-height:1.7;margin-bottom:1.5rem">
          <?= htmlspecialchars($curso['descripcion'] ?? '') ?>
        </p>

        <!-- Ratings row -->
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;font-size:.875rem;margin-bottom:1.5rem">
          <div style="display:flex;align-items:center;gap:.4rem">
            <span style="color:var(--warning);font-size:1rem"><?= str_repeat('★', floor($rating)) ?></span>
            <strong><?= $rating ?></strong>
            <span style="color:var(--text-muted)">(<?= $reviews ?> reseñas)</span>
          </div>
          <span><i class="fas fa-users" style="color:var(--primary)"></i> <?= number_format(($curso['total_alumnos']??0) + 94) ?> estudiantes</span>
          <span><i class="fas fa-clock" style="color:var(--primary)"></i> <?= $curso['duracion_horas'] ?> horas</span>
          <span><i class="fas fa-book-open" style="color:var(--primary)"></i> <?= count($materiales) ?> lecciones</span>
        </div>

        <!-- Instructor -->
        <div style="display:flex;align-items:center;gap:.75rem;padding:1rem;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:var(--radius);max-width:400px">
          <div style="width:44px;height:44px;background:var(--grad-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:1.1rem;flex-shrink:0">
            <?= strtoupper(substr($curso['nombre_docente']??'T', 0, 1)) ?>
          </div>
          <div>
            <div style="font-size:.8rem;color:var(--text-muted)">Instructor</div>
            <div style="font-weight:600"><?= htmlspecialchars($curso['nombre_docente']??'Equipo TecnoFutura') ?></div>
            <?php if ($curso['especialidad']): ?>
            <div style="font-size:.75rem;color:var(--text-muted)"><?= htmlspecialchars($curso['especialidad']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Sticky purchase card (desktop overlaps) -->
      <div style="padding-bottom:2rem">
        <!-- (empty space for card positioning) -->
      </div>
    </div>
  </div>
</div>

<!-- MAIN CONTENT + STICKY CARD -->
<div class="container" style="margin-top:2.5rem;margin-bottom:4rem">
  <div style="display:grid;grid-template-columns:1fr 380px;gap:3rem;align-items:start">

    <!-- Left: Tabs content -->
    <div>
      <!-- What you'll learn -->
      <div class="detail-section fade-in">
        <h2 class="detail-section-title"><i class="fas fa-graduation-cap"></i> Lo que aprenderás</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
          <?php foreach($aprenderas as $a): ?>
          <div style="display:flex;align-items:flex-start;gap:.6rem;font-size:.875rem;color:var(--text-secondary)">
            <i class="fas fa-check" style="color:var(--success);margin-top:.2rem;flex-shrink:0"></i>
            <?= htmlspecialchars($a) ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Requirements -->
      <div class="detail-section fade-in">
        <h2 class="detail-section-title"><i class="fas fa-list-check"></i> Requisitos</h2>
        <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:.5rem">
          <?php foreach(['Computadora con Windows, macOS o Linux','Conexión a Internet','Ganas de aprender (¡no se requiere experiencia previa!)',
          $curso['nivel'] !== 'Básico' ? 'Haber completado el nivel anterior o conocimientos equivalentes' : null] as $r):
            if (!$r) continue; ?>
          <li style="display:flex;align-items:flex-start;gap:.6rem;font-size:.875rem;color:var(--text-secondary)">
            <i class="fas fa-circle-check" style="color:var(--primary);margin-top:.2rem;flex-shrink:0"></i>
            <?= $r ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Curriculum -->
      <div class="detail-section fade-in">
        <h2 class="detail-section-title"><i class="fas fa-book-open"></i> Contenido del Curso</h2>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;font-size:.82rem;color:var(--text-muted)">
          <span><?= count($modulos) ?> módulos · <?= count($materiales) ?> lecciones · <?= $curso['duracion_horas'] ?> horas en total</span>
        </div>

        <div class="curriculum-accordion">
          <?php $mod_num = 0; foreach($modulos as $mod_title => $lessons): $mod_num++; ?>
          <div class="curriculum-module">
            <div class="curriculum-module-header">
              <div style="display:flex;align-items:center;gap:.75rem">
                <span style="width:28px;height:28px;background:var(--grad-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#fff;flex-shrink:0"><?= $mod_num ?></span>
                <div>
                  <div style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($mod_title) ?></div>
                  <div style="font-size:.72rem;color:var(--text-muted)"><?= count($lessons) ?> lecciones</div>
                </div>
              </div>
              <i class="fas fa-chevron-down toggle-icon" style="color:var(--text-muted);transition:transform .3s"></i>
            </div>
            <div class="curriculum-lessons <?= $mod_num === 1 ? 'show' : '' ?>">
              <?php foreach($lessons as $l): ?>
              <div class="curriculum-lesson">
                <div style="display:flex;align-items:center;gap:.6rem">
                  <i class="<?= $l['tipo_material'] === 'video' ? 'fas fa-play-circle' : 'fas fa-file-alt' ?>" style="color:var(--text-muted);width:14px"></i>
                  <span style="font-size:.85rem"><?= htmlspecialchars($l['titulo']) ?></span>
                  <?php if (!$insc && !$is_free): ?>
                  <i class="fas fa-lock" style="color:var(--text-muted);font-size:.7rem;margin-left:.25rem" title="Requiere inscripción"></i>
                  <?php endif; ?>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;font-size:.75rem;color:var(--text-muted);flex-shrink:0">
                  <?php if ($l['duracion_minutos'] ?? 0): ?>
                  <span><i class="fas fa-clock"></i> <?= $l['duracion_minutos'] ?> min</span>
                  <?php endif; ?>
                  <span class="badge" style="font-size:.65rem"><?= ucfirst($l['tipo_material']) ?></span>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($modulos)): ?>
          <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:.875rem">
            <i class="fas fa-book" style="display:block;font-size:2rem;margin-bottom:.75rem"></i>
            El contenido del curso se publicará próximamente.
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Instructor bio -->
      <div class="detail-section fade-in">
        <h2 class="detail-section-title"><i class="fas fa-user-tie"></i> Tu Instructor</h2>
        <div style="display:flex;gap:1.25rem">
          <div style="width:64px;height:64px;background:var(--grad-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:#fff;flex-shrink:0">
            <?= strtoupper(substr($curso['nombre_docente']??'T', 0, 1)) ?>
          </div>
          <div>
            <div style="font-weight:700;font-size:1.05rem;margin-bottom:.25rem"><?= htmlspecialchars($curso['nombre_docente']??'Equipo TecnoFutura') ?></div>
            <?php if ($curso['especialidad']): ?>
            <div style="font-size:.82rem;color:var(--primary);margin-bottom:.75rem"><?= htmlspecialchars($curso['especialidad']) ?></div>
            <?php endif; ?>
            <p style="font-size:.875rem;color:var(--text-secondary);line-height:1.7">
              Especialista en sistemas embebidos y lenguaje ensamblador con más de 8 años de experiencia docente. Ha formado a estudiantes en instituciones técnicas y universitarias, con enfoque en proyectos industriales reales.
            </p>
            <div style="display:flex;gap:1.5rem;margin-top:.75rem;font-size:.8rem;color:var(--text-muted)">
              <span><i class="fas fa-star" style="color:var(--warning)"></i> 4.9 calificación</span>
              <span><i class="fas fa-users" style="color:var(--primary)"></i> 1,240+ alumnos</span>
              <span><i class="fas fa-book" style="color:var(--secondary)"></i> <?= count($modulos) ?>+ cursos</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Purchase Card (sticky) -->
    <div class="course-sticky-card" style="position:sticky;top:calc(var(--navbar-height) + 1.5rem)">
      <!-- Course preview image -->
      <div style="border-radius:var(--radius) var(--radius) 0 0;overflow:hidden;background:var(--bg-base);height:200px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);border-bottom:none">
        <div style="text-align:center;color:var(--text-muted)">
          <i class="fas fa-play-circle" style="font-size:3.5rem;margin-bottom:.75rem;display:block;color:var(--primary)"></i>
          <div style="font-size:.82rem">Vista previa del curso</div>
        </div>
      </div>

      <div class="purchase-card">
        <!-- Price -->
        <div style="margin-bottom:1.5rem">
          <?php if ($is_free): ?>
          <div style="font-size:2rem;font-weight:800;color:var(--success);margin-bottom:.25rem"><i class="fas fa-gift"></i> Gratis</div>
          <div style="font-size:.8rem;color:var(--text-muted)">Acceso completo y certificado incluido</div>
          <?php else: ?>
          <div style="font-size:2.2rem;font-weight:800;color:var(--text-primary);margin-bottom:.15rem">
            $<?= number_format($curso['precio'],2) ?> <span style="font-size:.9rem;font-weight:400;color:var(--text-muted)">MXN</span>
          </div>
          <?php if ($precio_original): ?>
          <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem">
            <span style="font-size:.9rem;color:var(--text-muted);text-decoration:line-through">$<?= number_format($precio_original,2) ?></span>
            <span style="background:var(--danger);color:#fff;padding:.15rem .4rem;border-radius:4px;font-size:.72rem;font-weight:700">-<?= $descuento ?>%</span>
          </div>
          <?php endif; ?>
          <div style="font-size:.78rem;color:var(--warning)"><i class="fas fa-clock"></i> ¡Oferta por tiempo limitado!</div>
          <?php endif; ?>
        </div>

        <!-- CTA Button -->
        <?php if ($insc): ?>
          <a href="<?= SITE_URL ?>/lms/curso.php?id=<?= $id ?>" class="btn btn-success btn-block btn-lg" style="margin-bottom:1rem">
            <i class="fas fa-play"></i> Continuar Aprendiendo
          </a>
        <?php elseif ($is_free): ?>
          <a href="<?= $is_logged ? SITE_URL.'/lms/inscribir.php?id='.$id : SITE_URL.'/login.php?redirect=/lms/inscribir.php?id='.$id ?>" class="btn btn-primary btn-block btn-lg" style="margin-bottom:1rem">
            <i class="fas fa-graduation-cap"></i> Inscribirme Gratis
          </a>
        <?php else: ?>
          <a href="<?= $is_logged ? SITE_URL.'/checkout?id='.$id : SITE_URL.'/login.php?redirect=/checkout?id='.$id ?>" class="btn btn-primary btn-block btn-lg" style="margin-bottom:1rem">
            <i class="fas fa-shopping-cart"></i> Inscribirme Ahora
          </a>
          <button class="btn btn-outline btn-block" style="margin-bottom:1rem">
            <i class="fas fa-heart"></i> Agregar a lista de deseos
          </button>
        <?php endif; ?>

        <p style="text-align:center;font-size:.72rem;color:var(--text-muted);margin-bottom:1.25rem">
          <i class="fas fa-shield-alt" style="color:var(--success)"></i> Garantía de devolución 30 días
        </p>

        <!-- Course includes -->
        <div style="border-top:1px solid var(--border);padding-top:1.25rem">
          <p style="font-size:.8rem;font-weight:600;margin-bottom:.75rem;color:var(--text-secondary)">Este curso incluye:</p>
          <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:.5rem">
            <?php foreach([
              ['fas fa-video','icon-blue', $curso['duracion_horas'].' horas de video HD'],
              ['fas fa-infinity','icon-purple','Acceso de por vida'],
              ['fas fa-download','icon-green','Materiales descargables'],
              ['fas fa-certificate','icon-amber','Certificado verificable'],
              ['fas fa-mobile-alt','icon-blue','Acceso desde móvil y tablet'],
              ['fas fa-headset','icon-purple','Soporte del instructor'],
            ] as [$ico,$cls,$txt]): ?>
            <li style="display:flex;align-items:center;gap:.6rem;font-size:.82rem;color:var(--text-secondary)">
              <i class="<?= $ico ?>" style="color:var(--<?= str_replace('icon-','',preg_replace('/icon-(blue|purple|green|amber)/','var(--$1)',$cls)) ?>);width:14px"></i>
              <?= $txt ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Share -->
        <div style="border-top:1px solid var(--border);padding-top:1rem;margin-top:1rem;text-align:center">
          <p style="font-size:.75rem;color:var(--text-muted);margin-bottom:.6rem">Compartir:</p>
          <div style="display:flex;justify-content:center;gap:.5rem">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL.'/cursos/detalle.php?id='.$id) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL.'/cursos/detalle.php?id='.$id) ?>&text=<?= urlencode('¡Aprendiendo '.$curso['nombre_curso'].' en TecnoFutura Academy!') ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="fab fa-twitter"></i></a>
            <button class="btn btn-ghost btn-sm" data-copy="<?= SITE_URL.'/cursos/detalle.php?id='.$id ?>"><i class="fas fa-link"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div><!-- end margin-top -->

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
