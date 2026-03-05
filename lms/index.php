<?php
$page_title = 'Mi Dashboard';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';
require_once __DIR__ . '/../backend/classes/Certificado.php';
require_once __DIR__ . '/../backend/classes/Actividad.php';

requiereRol(['USUARIO']);

$id_alumno = $_SESSION['info_adicional']['id_alumno'] ?? 0;
$obj       = new Curso();
$actObj    = new Actividad();
$inscripciones = $obj->misInscripciones($id_alumno);

$cert_obj    = new Certificado();
$certs       = $cert_obj->porAlumno($id_alumno);
$total_certs = count($certs);

// Próximas evaluaciones
$proximas_eval = $actObj->proximasEvaluaciones($id_alumno, 5);

// Stats
$total       = count($inscripciones);
$en_curso    = count(array_filter($inscripciones, fn($i) => in_array($i['estado'], ['Activo','En curso','Inscrito'])));
$finalizados = count(array_filter($inscripciones, fn($i) => $i['estado'] === 'Finalizado' || $i['estado'] === 'Completado'));

// Calcular promedio general
$total_progreso = 0;
foreach ($inscripciones as $i) {
    $total_progreso += floatval($i['progreso'] ?? 0);
}
$promedio_progreso = $total > 0 ? round($total_progreso / $total) : 0;

// Preparar datos de cursos
foreach ($inscripciones as &$insc) {
    $insc['pct'] = min(100, round(floatval($insc['progreso'] ?? 0)));
    $insc['total_l'] = $insc['total_lecciones'] ?: 1;
    $insc['done_l'] = round($insc['pct'] / 100 * $insc['total_l']);
}
unset($insc);

// Cursos para "Continuar Aprendiendo" (últimos 3 en progreso)
$continuar_cursos = array_filter($inscripciones, fn($i) => in_array($i['estado'], ['Activo','En curso','Inscrito']) && $i['pct'] > 0 && $i['pct'] < 100);
usort($continuar_cursos, fn($a, $b) => strtotime($b['fecha_inscripcion']) - strtotime($a['fecha_inscripcion']));
$continuar_cursos = array_slice($continuar_cursos, 0, 3);

// Filtro vista
$vista = $_GET['vista'] ?? 'todos'; // todos, activos, completados

include_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/dashboard.css">

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

    <!-- Continuar Aprendiendo -->
    <?php if (!empty($continuar_cursos)): ?>
    <div style="margin-bottom:3rem">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
        <h2 style="font-size:1.3rem;font-weight:700;margin:0">
          <i class="fas fa-play-circle" style="color:var(--primary)"></i> Continuar Aprendiendo
        </h2>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.5rem">
        <?php foreach($continuar_cursos as $curso): 
          $nivel_slug = strtolower(str_replace('á','a',$curso['nivel']));
        ?>
        <div style="background:linear-gradient(135deg, var(--primary-light), var(--bg-card));border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.75rem;position:relative;overflow:hidden;transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 24px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
          <!-- Badge nivel -->
          <span class="badge badge-<?= $nivel_slug ?>" style="font-size:.65rem;margin-bottom:.75rem;display:inline-block"><?= $curso['nivel'] ?></span>
          
          <!-- Título -->
          <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:.5rem;line-height:1.3">
            <?= htmlspecialchars($curso['nombre_curso']) ?>
          </h3>
          
          <!-- Info -->
          <div style="display:flex;align-items:center;gap:.75rem;font-size:.8rem;color:var(--text-muted);margin-bottom:1.25rem">
            <span><i class="fas fa-user-tie"></i> <?= htmlspecialchars($curso['nombre_docente']) ?></span>
            <span>·</span>
            <span><i class="fas fa-clock"></i> <?= $curso['duracion_horas'] ?>h</span>
          </div>
          
          <!-- Progress -->
          <div style="margin-bottom:1.25rem">
            <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:.5rem">
              <span style="color:var(--text-secondary)">Tu progreso</span>
              <span style="font-weight:700;color:var(--primary)"><?= $curso['pct'] ?>%</span>
            </div>
            <div class="progress" style="height:10px;border-radius:999px;overflow:hidden;background:rgba(0,0,0,0.05)">
              <div class="progress-bar" style="width:<?= $curso['pct'] ?>%;background:var(--grad-primary);transition:width 0.6s ease"></div>
            </div>
          </div>
          
          <!-- CTA Button -->
          <a href="<?= SITE_URL ?>/lms/curso.php?id=<?= $curso['id_curso'] ?>" 
             class="btn btn-primary btn-block" 
             style="font-weight:600;padding:0.875rem 1.5rem;font-size:1rem;box-shadow:0 4px 12px rgba(79, 70, 229, 0.3)">
            <i class="fas fa-play"></i> Continuar Curso
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Próximas Evaluaciones -->
    <?php if (!empty($proximas_eval)): ?>
    <div style="margin-bottom:3rem">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
        <h2 style="font-size:1.3rem;font-weight:700;margin:0">
          <i class="fas fa-clipboard-list" style="color:var(--warning)"></i> Próximas Evaluaciones
        </h2>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden">
        <?php foreach($proximas_eval as $idx => $eval):
          $is_last = ($idx === count($proximas_eval) - 1);
          $intentos_restantes = $eval['intentos_permitidos'] > 0 ? 
            ($eval['intentos_permitidos'] - intval($eval['intentos_realizados'] ?? 0)) : 
            '∞';
        ?>
        <div style="padding:1.25rem 1.5rem;<?= !$is_last ? 'border-bottom:1px solid var(--border);' : '' ?>display:grid;grid-template-columns:auto 1fr auto;gap:1.25rem;align-items:center;transition:background 0.2s" onmouseover="this.style.background='var(--bg-surface)'" onmouseout="this.style.background=''">
          
          <!-- Icon -->
          <div style="width:48px;height:48px;background:linear-gradient(135deg, var(--warning), #f59e0b);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;flex-shrink:0">
            <i class="fas fa-file-alt"></i>
          </div>
          
          <!-- Content -->
          <div>
            <div style="font-size:1rem;font-weight:700;margin-bottom:.3rem">
              <?= htmlspecialchars($eval['titulo']) ?>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;font-size:.8rem;color:var(--text-muted);flex-wrap:wrap">
              <span><i class="fas fa-book"></i> <?= htmlspecialchars($eval['nombre_curso']) ?></span>
              <span><i class="fas fa-question-circle"></i> <?= $eval['total_preguntas'] ?> preguntas</span>
              <?php if ($eval['duracion_minutos'] > 0): ?>
              <span><i class="fas fa-clock"></i> <?= $eval['duracion_minutos'] ?> min</span>
              <?php endif; ?>
              <span class="badge <?= $intentos_restantes === '∞' ? 'badge-success' : 'badge-warning' ?>" style="font-size:.65rem">
                <?= $intentos_restantes ?> intento<?= $intentos_restantes !== 1 ? 's' : '' ?> disponible<?= $intentos_restantes !== 1 ? 's' : '' ?>
              </span>
            </div>
          </div>
          
          <!-- Action -->
          <a href="<?= SITE_URL ?>/lms/curso.php?id=<?= $eval['id_curso'] ?>&actividad=<?= $eval['id_actividad'] ?>" 
             class="btn btn-primary btn-sm" 
             style="white-space:nowrap;padding:0.625rem 1.25rem">
            <i class="fas fa-play"></i> Realizar
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- All Courses -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
      <h2 style="font-size:1.3rem;font-weight:700;margin:0">
        <i class="fas fa-th-large" style="color:var(--info)"></i> Todos Mis Cursos
      </h2>
      <div style="display:flex;gap:.5rem;font-size:.85rem">
        <a href="?vista=todos" class="btn btn-ghost btn-sm <?= $vista === 'todos' ? 'active' : '' ?>" style="<?= $vista === 'todos' ? 'background:var(--primary-light);color:var(--primary)' : '' ?>">
          Todos (<?= $total ?>)
        </a>
        <a href="?vista=activos" class="btn btn-ghost btn-sm <?= $vista === 'activos' ? 'active' : '' ?>" style="<?= $vista === 'activos' ? 'background:var(--success-light);color:var(--success)' : '' ?>">
          Activos (<?= $en_curso ?>)
        </a>
        <a href="?vista=completados" class="btn btn-ghost btn-sm <?= $vista === 'completados' ? 'active' : '' ?>" style="<?= $vista === 'completados' ? 'background:var(--info-light);color:var(--info)' : '' ?>">
          Completados (<?= $finalizados ?>)
        </a>
      </div>
    </div>
    <div class="grid-3" style="margin-bottom:3rem">
      <?php 
      // Filtrar cursos según vista
      $cursos_filtrados = $inscripciones;
      if ($vista === 'activos') {
        $cursos_filtrados = array_filter($inscripciones, fn($i) => in_array($i['estado'], ['Activo','En curso','Inscrito']));
      } elseif ($vista === 'completados') {
        $cursos_filtrados = array_filter($inscripciones, fn($i) => $i['estado'] === 'Finalizado' || $i['estado'] === 'Completado' || $i['estado'] === 'Certificado');
      }
      
      foreach($cursos_filtrados as $insc):
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
      
      <?php if (empty($cursos_filtrados)): ?>
      <div style="grid-column:1/-1;text-align:center;padding:3rem;background:var(--bg-surface);border:1px dashed var(--border);border-radius:var(--radius-lg)">
        <i class="fas fa-inbox" style="font-size:2.5rem;color:var(--text-muted);margin-bottom:1rem;display:block"></i>
        <p style="color:var(--text-secondary);font-size:.95rem">
          No hay cursos en esta categoría
        </p>
      </div>
      <?php endif; ?>
    </div>

    <!-- Certificates -->
    <?php if ($total_certs > 0): ?>
    <div style="margin-bottom:3rem">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
        <h2 style="font-size:1.3rem;font-weight:700;margin:0">
          <i class="fas fa-certificate" style="color:#f39c12"></i> Mis Certificados
        </h2>
        <a href="<?= SITE_URL ?>/lms/certificados.php" class="btn btn-ghost btn-sm">
          Ver todos <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <div class="grid-3">
      <?php foreach($certs as $cert): ?>
      <div class="cert-card" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;transition:all 0.3s ease" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 24px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
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
