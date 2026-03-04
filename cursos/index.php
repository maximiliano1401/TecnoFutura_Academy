<?php
$page_title = 'Catálogo de Cursos';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/classes/Curso.php';

$filtros = [
    'nivel'   => $_GET['nivel'] ?? '',
    'gratis'  => $_GET['gratis'] ?? '',
    'buscar'  => $_GET['buscar'] ?? '',
];
$curso = new Curso();
$cursos = $curso->todos($filtros);

include_once __DIR__ . '/../includes/header.php';
?>

<div style="background:var(--bg-surface);border-bottom:1px solid var(--border);padding:3rem 0 2.5rem;margin-top:var(--navbar-height)">
  <div class="container">
    <div class="section-eyebrow" style="margin-bottom:.75rem">Plataforma</div>
    <h1 style="font-size:2.2rem;font-weight:800;margin-bottom:.75rem">Catálogo de Cursos</h1>
    <p style="color:var(--text-secondary);max-width:560px">Explora todos nuestros cursos de Arduino, Lenguaje Ensamblador y Sistemas Embebidos. Aprende a tu ritmo con certificados verificables.</p>

    <!-- Search bar -->
    <div style="display:flex;gap:.75rem;margin-top:1.5rem;max-width:520px">
      <form method="GET" style="display:flex;flex:1;gap:.75rem">
        <div class="form-control-icon" style="flex:1">
          <i class="fas fa-search icon"></i>
          <input type="text" name="buscar" class="form-control" placeholder="Buscar curso..." value="<?= htmlspecialchars($filtros['buscar']) ?>">
        </div>
        <?php if ($filtros['nivel']): ?><input type="hidden" name="nivel" value="<?= htmlspecialchars($filtros['nivel']) ?>"><?php endif; ?>
        <button type="submit" class="btn btn-primary">Buscar</button>
      </form>
    </div>
  </div>
</div>

<div class="container" style="margin-top:2.5rem;margin-bottom:4rem">
  <div style="display:grid;grid-template-columns:240px 1fr;gap:2.5rem;align-items:start">

    <!-- Sidebar Filters -->
    <aside>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;position:sticky;top:calc(var(--navbar-height) + 1rem)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem">
          <h3 style="font-size:.95rem;font-weight:600">Filtros</h3>
          <a href="<?= SITE_URL ?>/cursos" style="font-size:.75rem;color:var(--text-muted)">Limpiar</a>
        </div>

        <div style="margin-bottom:1.5rem">
          <p style="font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem">Nivel</p>
          <?php foreach(['Básico','Intermedio','Avanzado'] as $n): ?>
          <a href="?nivel=<?= urlencode($n) ?><?= $filtros['buscar'] ? '&buscar=' . urlencode($filtros['buscar']) : '' ?>"
             style="display:flex;align-items:center;gap:.6rem;padding:.5rem .75rem;border-radius:var(--radius);font-size:.875rem;color:<?= $filtros['nivel'] === $n ? 'var(--primary)' : 'var(--text-secondary)' ?>;background:<?= $filtros['nivel'] === $n ? 'rgba(6,182,212,.08)' : 'transparent' ?>;border:1px solid <?= $filtros['nivel'] === $n ? 'rgba(6,182,212,.25)' : 'transparent' ?>;margin-bottom:.25rem;text-decoration:none;transition:all .2s">
            <i class="fas fa-circle" style="font-size:.4rem;color:<?= $n === 'Básico' ? 'var(--success)' : ($n === 'Intermedio' ? 'var(--warning)' : 'var(--danger)') ?>"></i>
            <?= $n ?>
          </a>
          <?php endforeach; ?>
        </div>

        <div style="margin-bottom:1.5rem">
          <p style="font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem">Precio</p>
          <a href="?gratis=1" style="display:flex;align-items:center;gap:.6rem;padding:.5rem .75rem;border-radius:var(--radius);font-size:.875rem;color:<?= $filtros['gratis'] ? 'var(--primary)' : 'var(--text-secondary)' ?>;background:<?= $filtros['gratis'] ? 'rgba(6,182,212,.08)' : 'transparent' ?>;border:1px solid <?= $filtros['gratis'] ? 'rgba(6,182,212,.25)' : 'transparent' ?>;text-decoration:none;transition:all .2s">
            <i class="fas fa-gift" style="color:var(--success)"></i> Solo Gratis
          </a>
        </div>

        <!-- Stats -->
        <div style="padding-top:1.25rem;border-top:1px solid var(--border)">
          <p style="font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem">Resultados</p>
          <div style="font-size:.875rem;color:var(--text-secondary)">
            <span style="font-weight:700;color:var(--primary);font-size:1.25rem"><?= count($cursos) ?></span>
            cursos encontrados
          </div>
        </div>
      </div>
    </aside>

    <!-- Course Grid -->
    <div>
      <!-- Active Filters -->
      <?php if ($filtros['nivel'] || $filtros['gratis'] || $filtros['buscar']): ?>
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap">
        <span style="font-size:.8rem;color:var(--text-muted)">Filtrando por:</span>
        <?php if ($filtros['nivel']): ?>
        <span class="badge" style="display:flex;align-items:center;gap:.35rem">
          <?= htmlspecialchars($filtros['nivel']) ?>
          <a href="?<?= http_build_query(array_merge($filtros, ['nivel'=>''])) ?>" style="color:inherit;margin-left:.25rem">&times;</a>
        </span>
        <?php endif; ?>
        <?php if ($filtros['gratis']): ?>
        <span class="badge badge-success" style="display:flex;align-items:center;gap:.35rem">
          Gratis <a href="?" style="color:inherit;margin-left:.25rem">&times;</a>
        </span>
        <?php endif; ?>
        <?php if ($filtros['buscar']): ?>
        <span class="badge" style="display:flex;align-items:center;gap:.35rem">
          "<?= htmlspecialchars($filtros['buscar']) ?>"
          <a href="?<?= http_build_query(array_merge($filtros, ['buscar'=>''])) ?>" style="color:inherit;margin-left:.25rem">&times;</a>
        </span>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Sort + Count -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
        <p style="color:var(--text-muted);font-size:.875rem">
          Mostrando <strong style="color:var(--text-primary)"><?= count($cursos) ?></strong> cursos
        </p>
        <div style="display:flex;gap:.5rem">
          <select class="form-control" style="width:auto;font-size:.8rem;padding:.4rem .75rem" id="sortSelect">
            <option value="">Ordenar por</option>
            <option value="price-asc">Precio: Menor a Mayor</option>
            <option value="price-desc">Precio: Mayor a Menor</option>
            <option value="level">Nivel</option>
          </select>
        </div>
      </div>

      <?php if (empty($cursos)): ?>
      <div style="text-align:center;padding:4rem;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg)">
        <i class="fas fa-search" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem;display:block"></i>
        <h3 style="color:var(--text-secondary);margin-bottom:.75rem">No se encontraron cursos</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem">Intenta con otros filtros o palabras clave.</p>
        <a href="<?= SITE_URL ?>/cursos" class="btn btn-primary">Ver Todos los Cursos</a>
      </div>
      <?php else: ?>
      <div class="grid-3" id="catalogGrid">
        <?php foreach($cursos as $c):
          $is_free = $c['precio'] == 0;
          $nivel_slug = strtolower(str_replace('á','a',$c['nivel']));
          $precio_original = $c['precio'] > 0 ? $c['precio'] * 1.4 : null;
          $descuento = $precio_original ? round((1 - $c['precio']/$precio_original)*100) : 0;
          $rating = rand(42, 50) / 10;
          $reviews = rand(18, 320);
          $enrolled_user = false;
          if (Usuario::estaAutenticado() && $_SESSION['usuario_rol'] === 'USUARIO') {
              // Check enrollment
              $enrolled_user = false; // simplified
          }
        ?>
        <div class="course-card fade-in" data-price="<?= $c['precio'] ?>" data-level="<?= $c['nivel'] ?>">
          <div class="course-card-thumb">
            <img data-src="<?= SITE_URL ?>/uploads/cursos/imagenes/<?= urlencode($c['imagen_portada'] ?? 'default.jpg') ?>"
                 src="<?= SITE_URL ?>/img/placeholder-course.svg"
                 alt="<?= htmlspecialchars($c['nombre_curso']) ?>" loading="lazy">
            <span class="course-card-badge badge-<?= $is_free ? 'free' : $nivel_slug ?>">
              <?= $is_free ? 'Gratis' : $c['nivel'] ?>
            </span>
            <?php if ($descuento > 0): ?>
            <span style="position:absolute;top:.6rem;right:.6rem;background:var(--danger);color:#fff;padding:.2rem .5rem;border-radius:4px;font-size:.7rem;font-weight:700">-<?= $descuento ?>%</span>
            <?php endif; ?>
            <div class="course-card-overlay">
              <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i> Ver Detalles
              </a>
            </div>
          </div>
          <div class="course-card-body">
            <div class="course-card-category">
              <?= str_contains($c['nombre_curso'],'Arduino') ? '<i class="fas fa-microchip"></i> Arduino & Electrónica' : '<i class="fas fa-code"></i> Lenguaje Ensamblador' ?>
            </div>
            <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" class="course-card-title">
              <?= htmlspecialchars($c['nombre_curso']) ?>
            </a>
            <p style="font-size:.78rem;color:var(--text-muted);margin-bottom:.6rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
              <?= htmlspecialchars(substr($c['descripcion'] ?? '', 0, 100)) ?>...
            </p>
            <div class="course-card-instructor">
              <div style="width:22px;height:22px;background:var(--grad-primary);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#fff;flex-shrink:0">
                <?= strtoupper(substr($c['nombre_docente'], 0, 1)) ?>
              </div>
              <?= htmlspecialchars($c['nombre_docente']) ?>
            </div>
            <div class="course-card-stats">
              <span class="rating-stars"><?= str_repeat('★', floor($rating)) ?></span>
              <span><?= number_format($rating, 1) ?></span>
              <span style="color:var(--text-muted)">(<?= $reviews ?>)</span>
              <span><i class="fas fa-clock"></i> <?= $c['duracion_horas'] ?>h</span>
              <span><i class="fas fa-users"></i> <?= number_format(($c['total_alumnos'] ?? 0) + rand(20,150)) ?></span>
            </div>
            <div class="course-card-footer">
              <div class="course-price">
                <?php if ($is_free): ?>
                  <span class="price-current free"><i class="fas fa-gift"></i> Gratis</span>
                <?php else: ?>
                  <span class="price-current">$<?= number_format($c['precio'],2) ?> <small style="font-size:.65rem;color:var(--text-muted)">MXN</small></span>
                  <?php if($precio_original): ?>
                  <span class="price-original">$<?= number_format($precio_original,2) ?></span>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
              <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" class="btn btn-sm btn-outline">
                Ver más <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
<script>
// Sort functionality
document.getElementById('sortSelect')?.addEventListener('change', (e) => {
  const val = e.target.value;
  const grid = document.getElementById('catalogGrid');
  const cards = [...grid.querySelectorAll('.course-card')];
  cards.sort((a, b) => {
    if (val === 'price-asc') return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
    if (val === 'price-desc') return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
    if (val === 'level') {
      const order = {'Básico':1,'Intermedio':2,'Avanzado':3};
      return (order[a.dataset.level]||0) - (order[b.dataset.level]||0);
    }
    return 0;
  });
  cards.forEach(c => grid.appendChild(c));
});
</script>
