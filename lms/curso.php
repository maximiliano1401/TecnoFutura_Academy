<?php
$page_title = 'Curso';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';
require_once __DIR__ . '/../backend/classes/Actividad.php';

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

// Check if current lesson is an activity
$actObj = new Actividad();
$actividad = null;
$preguntas = [];
$intento_actual = null;
$resultado = null;
$msg = '';

if ($current_lesson && in_array($current_lesson['tipo_material'], ['evaluacion', 'ejercicio'])) {
    $actividad = $actObj->porMaterial($current_lesson['id_material']);
    
    if ($actividad) {
        $preguntas = $actObj->preguntasDeActividad($actividad['id_actividad']);
        foreach ($preguntas as &$preg) {
            $preg['opciones'] = $actObj->opcionesDePregunta($preg['id_pregunta']);
        }
        unset($preg);
        
        // Get previous attempts
        $intentos = $actObj->intentosAlumno($id_inscripcion, $actividad['id_actividad']);
    }
}

// Handle activity actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $actividad) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'iniciar_intento') {
        $result = $actObj->iniciarIntento($id_inscripcion, $actividad['id_actividad']);
        if ($result['success']) {
            $intento_actual = $result['numero_intento'];
            $_SESSION['intento_actual'] = $intento_actual;
            $msg = 'Intento iniciado. ¡Buena suerte!';
        }
    }
    elseif ($action === 'guardar_respuesta') {
        $intento = intval($_POST['intento'] ?? $_SESSION['intento_actual'] ?? 1);
        $id_pregunta = intval($_POST['id_pregunta']);
        $id_opcion = isset($_POST['id_opcion']) ? intval($_POST['id_opcion']) : null;
        $texto_respuesta = isset($_POST['texto_respuesta']) ? htmlspecialchars($_POST['texto_respuesta']) : null;
        
        $actObj->guardarRespuesta([
            'id_inscripcion' => $id_inscripcion,
            'id_actividad' => $actividad['id_actividad'],
            'id_pregunta' => $id_pregunta,
            'id_opcion' => $id_opcion,
            'texto_respuesta' => $texto_respuesta,
            'intento' => $intento
        ]);
    }
    elseif ($action === 'finalizar_intento') {
        $intento = intval($_POST['intento'] ?? $_SESSION['intento_actual'] ?? 1);
        $resultado = $actObj->finalizarIntento($id_inscripcion, $actividad['id_actividad'], $intento);
        unset($_SESSION['intento_actual']);
        
        if ($resultado['success']) {
            if ($resultado['calificado']) {
                $porcentaje = round(($resultado['puntaje_obtenido'] / $resultado['puntaje_total']) * 100);
                $msg = $resultado['aprobado'] 
                    ? "¡Felicidades! Has aprobado con {$porcentaje}%." 
                    : "Has obtenido {$porcentaje}%. No alcanzaste el puntaje mínimo.";
            } else {
                $msg = 'Intento enviado. Algunas preguntas requieren revisión del profesor.';
            }
        }
    }
}

// Get current attempt if exists
if ($actividad && !$resultado) {
    $intento_actual = $_SESSION['intento_actual'] ?? null;
    if (!$intento_actual && !empty($intentos)) {
        // Check if there's an ongoing attempt
        foreach ($intentos as $int) {
            if (!$int['fecha_finalizacion']) {
                $intento_actual = $int['numero_intento'];
                $_SESSION['intento_actual'] = $intento_actual;
                break;
            }
        }
    }
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:1fr 320px;min-height:calc(100vh - var(--navbar-height))">

  <!-- Main content area -->
  <div style="display:flex;flex-direction:column">
    <!-- Video/Content Area -->
    <div style="background:<?= $actividad ? 'var(--bg-primary)' : '#000' ?>;min-height:400px;display:flex;align-items:center;justify-content:center;position:relative">
      <?php if ($msg): ?>
      <div style="position:absolute;top:1rem;left:50%;transform:translateX(-50%);background:var(--success);color:#fff;padding:.75rem 1.5rem;border-radius:var(--radius);z-index:10;box-shadow:var(--shadow-lg)">
        <?= htmlspecialchars($msg) ?>
      </div>
      <?php endif; ?>
      
      <?php if ($current_lesson): ?>
        <?php if ($actividad): ?>
        <!-- Activity/Exam Interface -->
        <div style="width:100%;max-width:900px;padding:2rem;background:var(--bg-card);margin:2rem;border-radius:var(--radius);box-shadow:var(--shadow-lg);max-height:600px;overflow-y:auto">
          <div style="text-align:center;margin-bottom:2rem">
            <i class="fas fa-clipboard-check" style="font-size:3rem;color:var(--primary);margin-bottom:1rem;display:block"></i>
            <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:.5rem"><?= htmlspecialchars($actividad['titulo'] ?? $current_lesson['titulo']) ?></h2>
            <div style="display:flex;gap:1.5rem;justify-content:center;font-size:.9rem;color:var(--text-muted);margin-top:1rem">
              <span><i class="fas fa-star"></i> <?= $actividad['puntaje_total'] ?> puntos</span>
              <?php if ($actividad['duracion_minutos']): ?>
              <span><i class="fas fa-clock"></i> <?= $actividad['duracion_minutos'] ?> minutos</span>
              <?php endif; ?>
              <span><i class="fas fa-redo"></i> <?= $actividad['intentos_permitidos'] == 0 ? 'Ilimitados' : $actividad['intentos_permitidos'] ?> intentos</span>
            </div>
          </div>

          <?php if ($resultado): ?>
          <!-- Results View -->
          <div style="background:var(--bg-secondary);padding:1.5rem;border-radius:var(--radius);margin-bottom:1.5rem">
            <h3 style="font-size:1.1rem;margin-bottom:1rem;color:<?= $resultado['aprobado'] ? 'var(--success)' : 'var(--danger)' ?>">
              <i class="fas fa-<?= $resultado['aprobado'] ? 'check-circle' : 'times-circle' ?>"></i>
              <?= $resultado['aprobado'] ? '¡Aprobado!' : 'No Aprobado' ?>
            </h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;font-size:.9rem">
              <div><strong>Puntaje:</strong> <?= $resultado['puntaje_obtenido'] ?> / <?= $resultado['puntaje_total'] ?></div>
              <div><strong>Porcentaje:</strong> <?= round(($resultado['puntaje_obtenido']/$resultado['puntaje_total'])*100) ?>%</div>
              <div><strong>Mínimo requerido:</strong> <?= $actividad['puntaje_minimo_aprobatorio'] ?> puntos</div>
            </div>
            <?php if (!$resultado['calificado']): ?>
            <p style="color:var(--warning);margin-top:1rem;font-size:.9rem">
              <i class="fas fa-exclamation-triangle"></i> Algunas respuestas están pendientes de revisión por el profesor.
            </p>
            <?php endif; ?>
          </div>
          
          <?php if ($actividad['intentos_permitidos'] == 0 || count($intentos) < $actividad['intentos_permitidos']): ?>
          <form method="POST" style="text-align:center">
            <input type="hidden" name="action" value="iniciar_intento">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="fas fa-redo"></i> Intentar de Nuevo
            </button>
          </form>
          <?php else: ?>
          <p style="text-align:center;color:var(--text-muted)">Has agotado todos tus intentos para esta actividad.</p>
          <?php endif; ?>

          <?php elseif ($intento_actual): ?>
          <!-- Exam Taking Interface -->
          <form id="exam-form" method="POST">
            <input type="hidden" name="intento" value="<?= $intento_actual ?>">
            
            <?php foreach ($preguntas as $idx => $preg): ?>
            <div style="background:var(--bg-secondary);padding:1.5rem;border-radius:var(--radius);margin-bottom:1.5rem">
              <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:1rem">
                <span style="background:var(--primary);color:#fff;width:2rem;height:2rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0"><?= $idx+1 ?></span>
                <div style="flex:1">
                  <p style="font-size:1rem;margin-bottom:.5rem"><?= htmlspecialchars($preg['pregunta']) ?></p>
                  <span style="font-size:.8rem;color:var(--text-muted)"><?= $preg['puntos'] ?> puntos</span>
                </div>
              </div>

              <?php if ($preg['tipo_pregunta'] === 'respuesta_corta'): ?>
              <textarea name="respuesta_text_<?= $preg['id_pregunta'] ?>" rows="4" 
                        style="width:100%;padding:.75rem;border:1px solid var(--border);border-radius:var(--radius);background:var(--bg-primary);color:var(--text-primary);font-family:inherit;resize:vertical"
                        placeholder="Escribe tu respuesta aquí..."></textarea>
              
              <?php else: ?>
              <?php foreach ($preg['opciones'] as $opc): ?>
              <label style="display:flex;align-items:center;gap:.75rem;padding:.75rem;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:.5rem;cursor:pointer;transition:all .2s" class="option-label">
                <input type="radio" name="respuesta_<?= $preg['id_pregunta'] ?>" value="<?= $opc['id_opcion'] ?>" 
                       style="width:1.2rem;height:1.2rem;cursor:pointer">
                <span style="flex:1"><?= htmlspecialchars($opc['texto_opcion']) ?></span>
              </label>
              <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <div style="display:flex;justify-content:center;gap:1rem;padding-top:1rem">
              <button type="button" class="btn btn-secondary" onclick="if(confirm('¿Salir sin enviar? Se perderá tu progreso.')) location.reload()">
                <i class="fas fa-times"></i> Cancelar
              </button>
              <button type="submit" class="btn btn-success btn-lg" onclick="return submitExam()">
                <i class="fas fa-paper-plane"></i> Enviar Evaluación
              </button>
            </div>
          </form>

          <?php else: ?>
          <!-- Start Attempt View -->
          <div style="text-align:center">
            <?php if (!empty($intentos)): ?>
            <div style="background:var(--bg-secondary);padding:1rem;border-radius:var(--radius);margin-bottom:1.5rem">
              <h4 style="font-size:.9rem;margin-bottom:.75rem;color:var(--text-muted)">Intentos Anteriores</h4>
              <?php foreach ($intentos as $int): ?>
              <div style="display:flex;justify-content:space-between;font-size:.85rem;padding:.5rem 0;border-top:1px solid var(--border)">
                <span>Intento <?= $int['numero_intento'] ?></span>
                <span><?= $int['fecha_finalizacion'] ? date('d/m/Y H:i', strtotime($int['fecha_finalizacion'])) : 'En progreso' ?></span>
                <?php if ($int['puntaje_obtenido'] !== null): ?>
                <span style="color:<?= $int['puntaje_obtenido'] >= $actividad['puntaje_minimo_aprobatorio'] ? 'var(--success)' : 'var(--danger)' ?>">
                  <?= $int['puntaje_obtenido'] ?>/<?= $actividad['puntaje_total'] ?>
                </span>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($actividad['intentos_permitidos'] == 0 || count($intentos) < $actividad['intentos_permitidos']): ?>
            <form method="POST">
              <input type="hidden" name="action" value="iniciar_intento">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-play"></i> Comenzar Evaluación
              </button>
            </form>
            <?php else: ?>
            <p style="color:var(--text-muted)">Has agotado todos tus intentos para esta actividad.</p>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>

        <?php elseif ($current_lesson['tipo_material'] === 'video' && $current_lesson['url_material']): ?>
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

<script>
// Submit exam with AJAX for each question
function submitExam() {
  const form = document.getElementById('exam-form');
  const formData = new FormData(form);
  
  // Validate all questions answered
  const questions = <?= json_encode(array_map(fn($p) => ['id' => $p['id_pregunta'], 'tipo' => $p['tipo_pregunta']], $preguntas ?? [])) ?>;
  let allAnswered = true;
  
  questions.forEach(q => {
    if (q.tipo === 'respuesta_corta') {
      const textarea = form.querySelector(`[name="respuesta_text_${q.id}"]`);
      if (!textarea || !textarea.value.trim()) allAnswered = false;
    } else {
      const radio = form.querySelector(`[name="respuesta_${q.id}"]:checked`);
      if (!radio) allAnswered = false;
    }
  });
  
  if (!allAnswered) {
    alert('Por favor responde todas las preguntas antes de enviar.');
    return false;
  }
  
  if (!confirm('¿Estás seguro de enviar la evaluación? No podrás modificar tus respuestas.')) {
    return false;
  }
  
  // Change button text
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
  
  // Save each answer via AJAX, then finalize
  saveAllAnswers(formData).then(() => {
    // Submit finalize form
    const finalForm = document.createElement('form');
    finalForm.method = 'POST';
    finalForm.innerHTML = `
      <input type="hidden" name="action" value="finalizar_intento">
      <input type="hidden" name="intento" value="${formData.get('intento')}">
    `;
    document.body.appendChild(finalForm);
    finalForm.submit();
  }).catch(err => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
    alert('Error al enviar respuestas: ' + err);
  });
  
  return false;
}

async function saveAllAnswers(formData) {
  const intento = formData.get('intento');
  const questions = <?= json_encode(array_map(fn($p) => ['id' => $p['id_pregunta'], 'tipo' => $p['tipo_pregunta']], $preguntas ?? [])) ?>;
  
  for (const q of questions) {
    const data = new FormData();
    data.append('action', 'guardar_respuesta');
    data.append('intento', intento);
    data.append('id_pregunta', q.id);
    
    if (q.tipo === 'respuesta_corta') {
      const textarea = document.querySelector(`[name="respuesta_text_${q.id}"]`);
      data.append('texto_respuesta', textarea.value);
    } else {
      const radio = document.querySelector(`[name="respuesta_${q.id}"]:checked`);
      if (radio) data.append('id_opcion', radio.value);
    }
    
    await fetch('', {method: 'POST', body: data});
  }
}

// Mark lesson as complete
document.querySelectorAll('.mark-complete-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const lessonId = this.dataset.lesson;
    const inscripcionId = this.dataset.inscripcion;
    
    fetch('<?= SITE_URL ?>/backend/progreso.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `id_inscripcion=${inscripcionId}&id_material=${lessonId}&completado=1`
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error al marcar como completada.');
      }
    });
  });
});

// Option hover effect
document.querySelectorAll('.option-label').forEach(label => {
  label.addEventListener('mouseenter', function() {
    this.style.background = 'var(--bg-primary)';
    this.style.borderColor = 'var(--primary)';
  });
  label.addEventListener('mouseleave', function() {
    const input = this.querySelector('input');
    if (!input.checked) {
      this.style.background = 'transparent';
      this.style.borderColor = 'var(--border)';
    }
  });
  
  const input = label.querySelector('input');
  input.addEventListener('change', function() {
    // Remove checked style from all options in same group
    const name = this.name;
    document.querySelectorAll(`input[name="${name}"]`).forEach(inp => {
      inp.parentElement.style.background = 'transparent';
      inp.parentElement.style.borderColor = 'var(--border)';
    });
    // Add checked style
    if (this.checked) {
      label.style.background = 'var(--bg-primary)';
      label.style.borderColor = 'var(--primary)';
    }
  });
});

// Auto-hide message after 5 seconds
const msg = document.querySelector('div[style*="position:absolute"][style*="top:1rem"]');
if (msg) {
  setTimeout(() => msg.style.display = 'none', 5000);
}
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
