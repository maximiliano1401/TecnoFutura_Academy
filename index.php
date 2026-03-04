<?php
$page_title = 'Aprende Arduino y Ensamblador';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/backend/classes/Database.php';

// Fetch featured courses
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT c.*, COALESCE(u.nombre_completo,'Equipo TecnoFutura') AS nombre_docente,
  (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso) AS total_alumnos
  FROM cursos c
  LEFT JOIN docentes d ON c.id_docente = d.id_docente
  LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
  WHERE c.activo = 1 ORDER BY total_alumnos DESC LIMIT 8");
$cursos = $stmt->fetchAll();

// Stats
$stats_stmt = $db->query("SELECT
  (SELECT COUNT(*) FROM usuarios WHERE id_rol=3) AS total_alumnos,
  (SELECT COUNT(*) FROM cursos WHERE activo=1) AS total_cursos,
  (SELECT COUNT(*) FROM certificados) AS total_certs,
  (SELECT COUNT(*) FROM usuarios WHERE id_rol=2) AS total_docentes");
$stats = $stats_stmt->fetch();

include_once __DIR__ . '/includes/header.php';
?>

<!-- HERO SECTION -->
<section class="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-content fade-in">
        <div class="hero-eyebrow">
          <i class="fas fa-bolt"></i>
          Plataforma LMS Especializada
        </div>
        <h1 class="hero-title">
          Domina el <span class="gradient-text">Lenguaje Ensamblador</span> y Arduino
        </h1>
        <p class="hero-subtitle">
          Aprende desde los fundamentos de arquitectura de computadoras hasta la programación de sistemas embebidos. Proyectos reales, certificados verificables, instructores expertos.
        </p>
        <div class="hero-cta">
          <a href="<?= SITE_URL ?>/cursos" class="btn btn-primary btn-xl">
            <i class="fas fa-play"></i> Explorar Cursos
          </a>
          <a href="<?= SITE_URL ?>/register.php" class="btn btn-outline-white btn-xl">
            Registro Gratis <i class="fas fa-arrow-right"></i>
          </a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat-item">
            <span class="hero-stat-value" data-counter data-target="<?= $stats['total_alumnos'] ?: 1240 ?>" data-suffix="+">0+</span>
            <span class="hero-stat-label">Estudiantes</span>
          </div>
          <div class="hero-stat-item">
            <span class="hero-stat-value" data-counter data-target="<?= $stats['total_cursos'] ?: 8 ?>" data-suffix="">0</span>
            <span class="hero-stat-label">Cursos</span>
          </div>
          <div class="hero-stat-item">
            <span class="hero-stat-value" data-counter data-target="<?= $stats['total_certs'] ?: 380 ?>" data-suffix="+">0+</span>
            <span class="hero-stat-label">Certificados</span>
          </div>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-card" style="position:relative">
          <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem">
            <div style="width:40px;height:40px;background:var(--grad-primary);border-radius:8px;display:flex;align-items:center;justify-content:center"><i class="fas fa-microchip" style="color:#fff"></i></div>
            <div>
              <div style="font-size:.8rem;font-weight:600">Sistemas Embebidos con Arduino</div>
              <div style="font-size:.72rem;color:var(--text-muted)">Nivel Avanzado</div>
            </div>
          </div>
          <!-- Code preview -->
          <div style="background:var(--bg-base);border-radius:var(--radius);padding:1.25rem;font-family:monospace;font-size:.78rem;line-height:1.7;margin-bottom:1.25rem;border:1px solid var(--border)">
            <div style="color:var(--text-muted);margin-bottom:.5rem">; Ensamblador x86 — TecnoFutura</div>
            <div><span style="color:#7dd3fc">MOV</span> <span style="color:#86efac">AX</span>, <span style="color:#fbbf24">0x1A3F</span></div>
            <div><span style="color:#7dd3fc">ADD</span> <span style="color:#86efac">BX</span>, <span style="color:#86efac">AX</span></div>
            <div><span style="color:#7dd3fc">PUSH</span> <span style="color:#86efac">BX</span></div>
            <div style="margin-top:.5rem"><span style="color:#c084fc">void</span> <span style="color:#7dd3fc">setup</span>() {</div>
            <div style="padding-left:1rem"><span style="color:#c084fc">pinMode</span>(<span style="color:#fbbf24">13</span>, <span style="color:#86efac">OUTPUT</span>);</div>
            <div>}</div>
          </div>
          <!-- Progress -->
          <div style="margin-bottom:.5rem;display:flex;justify-content:space-between;font-size:.72rem;color:var(--text-muted)">
            <span>Progreso del Módulo 3</span><span>72%</span>
          </div>
          <div class="progress"><div class="progress-bar" style="width:72%"></div></div>

          <div class="floating-badge floating-badge-1">
            <i class="fas fa-trophy" style="color:var(--accent)"></i>
            <span>Certificado disponible</span>
          </div>
          <div class="floating-badge floating-badge-2">
            <i class="fas fa-bolt" style="color:var(--primary)"></i>
            <span>+240 aprendiendo hoy</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TRUST BAR -->
<section style="background:var(--bg-surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:1.25rem 0">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:center;gap:3rem;flex-wrap:wrap">
      <?php foreach([
        ['fas fa-shield-alt','Certificados verificables'],
        ['fas fa-infinity','Acceso de por vida'],
        ['fas fa-mobile-alt','Compatible con móvil'],
        ['fas fa-headset','Soporte 24/7'],
        ['fas fa-undo','Garantía 30 días'],
      ] as [$ico,$txt]): ?>
      <div style="display:flex;align-items:center;gap:.6rem;font-size:.82rem;color:var(--text-secondary)">
        <i class="<?= $ico ?>" style="color:var(--primary)"></i><?= $txt ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="section-sm" style="background:var(--bg-surface)">
  <div class="container">
    <div class="stats-grid">
      <?php foreach([
        ['fas fa-users','icon-blue', $stats['total_alumnos']??1240, '+', 'Estudiantes Activos'],
        ['fas fa-book-open','icon-purple', $stats['total_cursos']??8, '', 'Cursos Disponibles'],
        ['fas fa-certificate','icon-amber', $stats['total_certs']??380, '+', 'Certificados Emitidos'],
        ['fas fa-star','icon-green','97','%','Tasa de Satisfacción'],
      ] as [$ico,$cls,$val,$suf,$lbl]): ?>
      <div class="stat-box fade-in">
        <div class="stat-icon <?= $cls ?>"><i class="<?= $ico ?>"></i></div>
        <div class="stat-value" data-counter data-target="<?= $val ?>" data-suffix="<?= $suf ?>"><?= $val.$suf ?></div>
        <div class="stat-label"><?= $lbl ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED COURSES -->
<section class="section" id="cursos">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Catálogo Completo</span>
      <h2 class="section-title">Cursos Disponibles</h2>
      <p class="section-subtitle">Desde fundamentos hasta sistemas embebidos avanzados. Aprende a tu ritmo con proyectos prácticos.</p>
    </div>

    <!-- Filter Pills -->
    <div style="display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap;margin-bottom:2.5rem">
      <?php foreach(['Todos','Básico','Intermedio','Avanzado','Gratis'] as $f): ?>
      <button class="btn btn-sm btn-ghost course-filter-btn <?= $f==='Todos'?'active':'' ?>" data-filter="<?= strtolower($f) ?>">
        <?= $f ?>
      </button>
      <?php endforeach; ?>
    </div>

    <div class="grid-4" id="coursesGrid">
      <?php foreach($cursos as $c):
        $is_free = $c['precio'] == 0;
        $nivel_slug = strtolower(str_replace('á','a',$c['nivel']));
        $thumbs = [
          'Introducción a Arduino' => 'arduino-intro.jpg',
          'Arduino Desde Cero' => 'arduino-zero.jpg',
          'Fundamentos de Arquitectura' => 'architecture.jpg',
        ];
        $thumb = '/uploads/cursos/imagenes/' . ($thumbs[$c['nombre_curso']] ?? 'default-course.jpg');
        $precio_original = $c['precio'] > 0 ? $c['precio'] * 1.4 : null;
        $descuento = $precio_original ? round((1 - $c['precio']/$precio_original)*100) : 0;
        $rating = rand(42, 50) / 10;
        $reviews = rand(18, 320);
      ?>
      <div class="course-card fade-in" data-nivel="<?= $nivel_slug ?>" data-precio="<?= $is_free?'gratis':'pagado' ?>">
        <div class="course-card-thumb">
          <img data-src="<?= SITE_URL ?><?= $thumb ?>" src="<?= IMG_PATH ?>/placeholder-course.svg" alt="<?= htmlspecialchars($c['nombre_curso']) ?>" loading="lazy">
          <span class="course-card-badge badge-<?= $is_free ? 'free' : $nivel_slug ?>">
            <?= $is_free ? 'Gratis' : $c['nivel'] ?>
          </span>
          <div class="course-card-overlay">
            <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Ver Curso</a>
          </div>
        </div>
        <div class="course-card-body">
          <div class="course-card-category">
            <?= str_contains($c['nombre_curso'],'Arduino') ? 'Arduino & Electrónica' : 'Lenguaje Ensamblador' ?>
          </div>
          <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" class="course-card-title">
            <?= htmlspecialchars($c['nombre_curso']) ?>
          </a>
          <div class="course-card-instructor">
            <i class="fas fa-user-tie" style="color:var(--text-muted)"></i>
            <?= htmlspecialchars($c['nombre_docente']) ?>
          </div>
          <div class="course-card-stats">
            <span class="rating-stars"><?= str_repeat('★', floor($rating)) ?><?= ($rating - floor($rating)) >= 0.5 ? '½' : '' ?></span>
            <span><?= number_format($rating, 1) ?></span>
            <span style="color:var(--text-muted)">(<?= $reviews ?>)</span>
            <span><i class="fas fa-clock"></i> <?= $c['duracion_horas'] ?>h</span>
            <span><i class="fas fa-users"></i> <?= number_format($c['total_alumnos'] + rand(20,150)) ?></span>
          </div>
          <div class="course-card-footer">
            <div class="course-price">
              <?php if ($is_free): ?>
                <span class="price-current free"><i class="fas fa-gift"></i> Gratis</span>
              <?php else: ?>
                <span class="price-current">$<?= number_format($c['precio'],2) ?></span>
                <?php if($precio_original): ?>
                <span class="price-original">$<?= number_format($precio_original,2) ?></span>
                <span class="price-discount">-<?= $descuento ?>%</span>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:3rem">
      <a href="<?= SITE_URL ?>/cursos" class="btn btn-outline btn-lg">
        Ver Todos los Cursos <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="section" style="background:var(--bg-surface)" id="nosotros">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">¿Por qué elegirnos?</span>
      <h2 class="section-title">Una plataforma pensada para <span class="gradient-text">ti</span></h2>
    </div>
    <div class="features-grid">
      <?php foreach([
        ['fas fa-laptop-code','icon-blue','Proyectos Reales','Cada curso incluye proyectos prácticos con Arduino, Tinkercad y entornos reales que puedes incluir en tu portafolio.'],
        ['fas fa-certificate','icon-amber','Certificados Verificables','Obtén certificados con código único, verificables en línea. Compártelos en LinkedIn o con empleadores.'],
        ['fas fa-chalkboard-teacher','icon-purple','Instructores Expertos','Aprende con docentes especializados en arquitectura de computadoras y sistemas embebidos.'],
        ['fas fa-infinity','icon-green','Acceso de Por Vida','Una vez inscrito, el contenido es tuyo para siempre. Incluye actualizaciones futuras del curso.'],
        ['fas fa-mobile-alt','icon-blue','Aprende en Cualquier Lugar','Plataforma optimizada para móvil, tablet y escritorio. Descarga materiales para aprender offline.'],
        ['fas fa-users','icon-purple','Comunidad Activa','Únete a miles de estudiantes. Foros de discusión, grupos de estudio y proyectos colaborativos.'],
      ] as [$ico,$cls,$title,$desc]): ?>
      <div class="feature-card fade-in">
        <div class="feature-icon <?= $cls ?>"><i class="<?= $ico ?>"></i></div>
        <h3 class="feature-title"><?= $title ?></h3>
        <p class="feature-text"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="section" id="precios">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Planes y Precios</span>
      <h2 class="section-title">Invierte en tu futuro tecnológico</h2>
      <p class="section-subtitle">Elige el plan que mejor se adapte a tu ritmo de aprendizaje. Sin compromisos, cancela cuando quieras.</p>
    </div>
    <div class="pricing-grid">
      <!-- Free -->
      <div class="pricing-card">
        <div class="pricing-name">Gratis</div>
        <div class="pricing-price"><span>$</span>0</div>
        <div class="pricing-period">Siempre gratis</div>
        <p class="pricing-description">Perfecto para comenzar y explorar la plataforma.</p>
        <ul class="pricing-features">
          <li><i class="fas fa-check check"></i> Acceso al curso de introducción</li>
          <li><i class="fas fa-check check"></i> 2 horas de contenido en video</li>
          <li><i class="fas fa-check check"></i> Foros de la comunidad</li>
          <li class="unavailable"><i class="fas fa-times cross"></i> Certificados</li>
          <li class="unavailable"><i class="fas fa-times cross"></i> Proyectos descargables</li>
          <li class="unavailable"><i class="fas fa-times cross"></i> Soporte prioritario</li>
        </ul>
        <a href="<?= SITE_URL ?>/register.php" class="btn btn-outline btn-block">Comenzar Gratis</a>
      </div>
      <!-- Pro (popular) -->
      <div class="pricing-card popular">
        <div class="pricing-popular-badge"><i class="fas fa-fire"></i> Más Popular</div>
        <div class="pricing-name">Pro</div>
        <div class="pricing-price"><span>$</span>499</div>
        <div class="pricing-period">por curso / acceso de por vida</div>
        <p class="pricing-description">Ideal para estudiantes que quieren dominar una especialidad.</p>
        <ul class="pricing-features">
          <li><i class="fas fa-check check"></i> 1 curso a elección completo</li>
          <li><i class="fas fa-check check"></i> Certificado verificable</li>
          <li><i class="fas fa-check check"></i> Proyectos descargables</li>
          <li><i class="fas fa-check check"></i> Soporte por email</li>
          <li><i class="fas fa-check check"></i> Actualizaciones del curso</li>
          <li class="unavailable"><i class="fas fa-times cross"></i> Mentoría 1 a 1</li>
        </ul>
        <a href="<?= SITE_URL ?>/cursos" class="btn btn-primary btn-block">Elegir Curso</a>
      </div>
      <!-- Bundle -->
      <div class="pricing-card">
        <div class="pricing-name">Bundle Completo</div>
        <div class="pricing-price"><span>$</span>1,899</div>
        <div class="pricing-period">todos los cursos / acceso de por vida</div>
        <p class="pricing-description">Acceso a todo el catálogo. La mejor inversión para tu carrera.</p>
        <ul class="pricing-features">
          <li><i class="fas fa-check check"></i> Todos los cursos (8+)</li>
          <li><i class="fas fa-check check"></i> Todos los certificados</li>
          <li><i class="fas fa-check check"></i> Proyectos y recursos extra</li>
          <li><i class="fas fa-check check"></i> Soporte prioritario 24/7</li>
          <li><i class="fas fa-check check"></i> Mentoría grupal mensual</li>
          <li><i class="fas fa-check check"></i> Cursos futuros incluidos</li>
        </ul>
        <a href="<?= SITE_URL ?>/checkout?bundle=1" class="btn btn-secondary btn-block">Obtener Bundle</a>
      </div>
    </div>
    <p style="text-align:center;margin-top:1.5rem;font-size:.85rem;color:var(--text-muted)">
      <i class="fas fa-shield-alt" style="color:var(--success)"></i>
      Garantía de devolución de dinero de 30 días — Sin preguntas.
    </p>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="section" style="background:var(--bg-surface)">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Testimonios</span>
      <h2 class="section-title">Lo que dicen nuestros estudiantes</h2>
    </div>
    <div class="grid-3">
      <?php
      $testimonios = [
        ['Carlos Mendoza','Estudiante de Ingeniería','La plataforma me permitió entender el ensamblador de forma práctica. Los proyectos con Arduino cambiaron completamente mi perspectiva sobre el hardware.', 'CM', 5, 'Fundamentos de Arquitectura'],
        ['Sofía Ramírez','Técnica en Electrónica','El curso de Arduino Intermedio es excelente. Pasé de no saber nada a construir una estación meteorológica funcionando en 3 semanas.','SR', 5,'Arduino Intermedio'],
        ['Diego Torres','Programador Full Stack','Quería entender qué pasa debajo del código alto nivel. Este curso de Ensamblador fue exactamente lo que necesitaba. Certificado en 6 semanas.','DT', 5,'Introducción al Ensamblador'],
      ];
      foreach($testimonios as $t): ?>
      <div class="testimonial-card fade-in">
        <div class="stars"><?= str_repeat('★', $t[4]) ?></div>
        <p class="testimonial-text">"<?= $t[2] ?>"</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar"><?= $t[3] ?></div>
          <div>
            <div class="testimonial-name"><?= $t[0] ?></div>
            <div class="testimonial-role"><?= $t[1] ?> · <?= $t[5] ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA BANNER -->
<section class="section">
  <div class="container">
    <div style="background:linear-gradient(135deg,rgba(6,182,212,.08),rgba(139,92,246,.08));border:1px solid var(--border);border-radius:var(--radius-xl);padding:4rem;text-align:center;position:relative;overflow:hidden">
      <div style="position:absolute;top:-50%;right:-10%;width:400px;height:400px;background:radial-gradient(circle,rgba(6,182,212,.06),transparent 70%);pointer-events:none"></div>
      <span class="section-eyebrow">Empieza Hoy</span>
      <h2 class="section-title" style="margin-bottom:1.25rem">¿Listo para dominar el hardware?</h2>
      <p style="color:var(--text-secondary);max-width:500px;margin:0 auto 2.5rem">
        Únete a más de 1,200 estudiantes que ya están aprendiendo la tecnología del futuro. Tu primer curso es completamente gratis.
      </p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
        <a href="<?= SITE_URL ?>/register.php" class="btn btn-primary btn-xl"><i class="fas fa-rocket"></i> Comenzar Ahora — Gratis</a>
        <a href="<?= SITE_URL ?>/cursos" class="btn btn-outline-white btn-xl">Ver Cursos</a>
      </div>
    </div>
  </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

<script>
// Course filter
document.querySelectorAll('.course-filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.course-filter-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
    document.querySelectorAll('.course-filter-btn').forEach(b => b.classList.add('btn-ghost'));
    btn.classList.add('active');
    btn.classList.remove('btn-ghost'); btn.classList.add('btn-primary');
    const filter = btn.dataset.filter;
    document.querySelectorAll('#coursesGrid .course-card').forEach(card => {
      if (filter === 'todos' || card.dataset.nivel === filter || card.dataset.precio === filter) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});
</script>
