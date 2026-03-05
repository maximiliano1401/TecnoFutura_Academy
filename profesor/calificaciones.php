<?php
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereRol(['PROFESOR']);

require_once __DIR__ . '/../backend/classes/Curso.php';
require_once __DIR__ . '/../backend/classes/Actividad.php';
require_once __DIR__ . '/../backend/classes/Usuario.php';

$id_docente = $_SESSION['usuario_rol_id'];
$cursoObj = new Curso();
$actObj = new Actividad();
$usuarioObj = new Usuario();

$cursos = $cursoObj->cursosDelProfesor($id_docente);

// Handle grading submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'calificar_respuesta') {
    $id_respuesta = intval($_POST['id_respuesta']);
    $puntos = floatval($_POST['puntos']);
    $comentario = htmlspecialchars($_POST['comentario'] ?? '');
    
    if ($actObj->calificarRespuestaTexto($id_respuesta, $puntos, $comentario)) {
        $msg = 'Respuesta calificada exitosamente.';
        $msg_type = 'success';
    } else {
        $msg = 'Error al calificar la respuesta.';
        $msg_type = 'error';
    }
}

// Handle finalize grading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalizar_calificacion') {
    $id_inscripcion = intval($_POST['id_inscripcion']);
    $id_actividad = intval($_POST['id_actividad']);
    $intento = intval($_POST['intento']);
    
    $resultado = $actObj->finalizarIntento($id_inscripcion, $id_actividad, $intento);
    if ($resultado['success']) {
        $msg = 'Calificación finalizada. Puntaje total: ' . $resultado['puntaje_obtenido'] . '/' . $resultado['puntaje_total'];
        $msg_type = $resultado['aprobado'] ? 'success' : 'warning';
    }
}

// Get selected filters
$filter_curso = isset($_GET['curso']) ? intval($_GET['curso']) : 0;
$filter_actividad = isset($_GET['actividad']) ? intval($_GET['actividad']) : 0;
$filter_estado = $_GET['estado'] ?? 'pendiente'; // pendiente, calificado, todos

// Get activities from selected course
$actividades = [];
if ($filter_curso) {
    $materiales = $cursoObj->materiales($filter_curso);
    foreach ($materiales as $mat) {
        if (in_array($mat['tipo_material'], ['evaluacion', 'ejercicio'])) {
            $act = $actObj->porMaterial($mat['id_material']);
            if ($act) $actividades[] = $act;
        }
    }
}

// Get submissions/attempts to grade
$intentos_pendientes = [];
if ($filter_curso) {
    $db = Database::getInstance()->getConnection();
    
    $where = ['c.id_docente = :id_docente'];
    $params = [':id_docente' => $id_docente];
    
    if ($filter_curso) {
        $where[] = 'c.id_curso = :id_curso';
        $params[':id_curso'] = $filter_curso;
    }
    if ($filter_actividad) {
        $where[] = 'ia.id_actividad = :id_actividad';
        $params[':id_actividad'] = $filter_actividad;
    }
    if ($filter_estado === 'pendiente') {
        $where[] = 'ia.calificado = 0';
    } elseif ($filter_estado === 'calificado') {
        $where[] = 'ia.calificado = 1';
    }
    
    $sql = "SELECT ia.*, 
                   u.nombre, u.apellido, u.email,
                   i.id_inscripcion,
                   a.puntaje_total, a.puntaje_minimo_aprobatorio,
                   mc.titulo as actividad_titulo,
                   c.nombre_curso
            FROM intentos_actividad ia
            INNER JOIN inscripciones i ON ia.id_inscripcion = i.id_inscripcion
            INNER JOIN usuarios u ON i.id_alumno = u.id_usuario
            INNER JOIN actividades a ON ia.id_actividad = a.id_actividad
            INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
            INNER JOIN cursos c ON mc.id_curso = c.id_curso
            WHERE " . implode(' AND ', $where) . "
              AND ia.fecha_finalizacion IS NOT NULL
            ORDER BY ia.fecha_finalizacion DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $intentos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:220px 1fr;min-height:calc(100vh - var(--navbar-height))">
<?php $active_page = 'calificaciones'; include_once __DIR__ . '/../includes/profesor_sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-header">
    <div>
      <h1 class="admin-title">Calificaciones</h1>
      <p class="admin-subtitle">Revisa y califica las evaluaciones de tus estudiantes</p>
    </div>
  </div>

  <?php if (isset($msg)): ?>
    <div class="alert alert-<?= $msg_type ?>" style="margin-bottom:1.5rem">
      <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card" style="margin-bottom:1.5rem">
      <div class="card-body">
        <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;align-items:end">
          <div>
            <label style="display:block;margin-bottom:.5rem;font-size:.85rem;font-weight:600">Curso</label>
            <select name="curso" class="form-control" required onchange="this.form.submit()">
              <option value="">Selecciona un curso</option>
              <?php foreach ($cursos as $c): ?>
              <option value="<?= $c['id_curso'] ?>" <?= $filter_curso == $c['id_curso'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre_curso']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <?php if ($filter_curso): ?>
          <div>
            <label style="display:block;margin-bottom:.5rem;font-size:.85rem;font-weight:600">Actividad</label>
            <select name="actividad" class="form-control" onchange="this.form.submit()">
              <option value="0">Todas las actividades</option>
              <?php foreach ($actividades as $act): ?>
              <option value="<?= $act['id_actividad'] ?>" <?= $filter_actividad == $act['id_actividad'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($act['titulo']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label style="display:block;margin-bottom:.5rem;font-size:.85rem;font-weight:600">Estado</label>
            <select name="estado" class="form-control" onchange="this.form.submit()">
              <option value="pendiente" <?= $filter_estado === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
              <option value="calificado" <?= $filter_estado === 'calificado' ? 'selected' : '' ?>>Calificados</option>
              <option value="todos" <?= $filter_estado === 'todos' ? 'selected' : '' ?>>Todos</option>
            </select>
          </div>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Submissions List -->
    <?php if (!$filter_curso): ?>
    <div class="card">
      <div class="card-body" style="text-align:center;padding:3rem">
        <i class="fas fa-filter" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem;display:block"></i>
        <p style="color:var(--text-muted)">Selecciona un curso para ver las evaluaciones enviadas por tus estudiantes.</p>
      </div>
    </div>
    <?php elseif (empty($intentos_pendientes)): ?>
    <div class="card">
      <div class="card-body" style="text-align:center;padding:3rem">
        <i class="fas fa-check-circle" style="font-size:3rem;color:var(--success);margin-bottom:1rem;display:block"></i>
        <p style="color:var(--text-muted)">No hay evaluaciones <?= $filter_estado === 'pendiente' ? 'pendientes' : '' ?> en este momento.</p>
      </div>
    </div>
    <?php else: ?>
    <div class="card">
      <div class="card-body" style="padding:0">
        <table class="data-table">
          <thead>
            <tr>
              <th>Estudiante</th>
              <th>Actividad</th>
              <th>Intento</th>
              <th>Fecha Envío</th>
              <th>Estado</th>
              <th>Puntaje</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($intentos_pendientes as $intento): ?>
            <tr>
              <td>
                <div style="font-weight:600"><?= htmlspecialchars($intento['nombre'] . ' ' . $intento['apellido']) ?></div>
                <div style="font-size:.8rem;color:var(--text-muted)"><?= htmlspecialchars($intento['email']) ?></div>
              </td>
              <td><?= htmlspecialchars($intento['actividad_titulo']) ?></td>
              <td><span class="badge badge-secondary">Intento #<?= $intento['numero_intento'] ?></span></td>
              <td style="font-size:.85rem"><?= date('d/m/Y H:i', strtotime($intento['fecha_finalizacion'])) ?></td>
              <td>
                <?php if ($intento['calificado']): ?>
                <span class="badge badge-success"><i class="fas fa-check"></i> Calificado</span>
                <?php else: ?>
                <span class="badge badge-warning"><i class="fas fa-clock"></i> Pendiente</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($intento['calificado']): ?>
                <strong style="color:<?= $intento['puntaje_obtenido'] >= $intento['puntaje_minimo_aprobatorio'] ? 'var(--success)' : 'var(--danger)' ?>">
                  <?= $intento['puntaje_obtenido'] ?>/<?= $intento['puntaje_total'] ?>
                </strong>
                <?php else: ?>
                <span style="color:var(--text-muted)">-</span>
                <?php endif; ?>
              </td>
              <td>
                <button class="btn btn-sm btn-primary" 
                        onclick="verDetalles(<?= $intento['id_inscripcion'] ?>, <?= $intento['id_actividad'] ?>, <?= $intento['numero_intento'] ?>)">
                  <i class="fas fa-eye"></i> Ver Detalles
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
</div>
</div>

<!-- Modal for grading details -->
<div class="modal" id="detallesModal">
  <div class="modal-dialog" style="max-width:900px">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Detalles de la Evaluación</h3>
        <button class="modal-close" onclick="cerrarModal()">&times;</button>
      </div>
      <div class="modal-body" id="detallesContent">
        <div style="text-align:center;padding:2rem">
          <i class="fas fa-spinner fa-spin" style="font-size:2rem;color:var(--primary)"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function verDetalles(id_inscripcion, id_actividad, intento) {
  document.getElementById('detallesModal').classList.add('active');
  document.getElementById('detallesContent').innerHTML = '<div style="text-align:center;padding:2rem"><i class="fas fa-spinner fa-spin" style="font-size:2rem;color:var(--primary)"></i></div>';
  
  fetch('<?= SITE_URL ?>/backend/ajax/obtener_respuestas.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `id_inscripcion=${id_inscripcion}&id_actividad=${id_actividad}&intento=${intento}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      mostrarDetalles(data);
    } else {
      document.getElementById('detallesContent').innerHTML = '<p style="color:var(--danger)">Error al cargar detalles.</p>';
    }
  });
}

function mostrarDetalles(data) {
  const { alumno, actividad, respuestas, intento } = data;
  
  let html = `
    <div style="background:var(--bg-secondary);padding:1.5rem;border-radius:var(--radius);margin-bottom:1.5rem">
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;font-size:.9rem">
        <div><strong>Estudiante:</strong> ${alumno.nombre} ${alumno.apellido}</div>
        <div><strong>Email:</strong> ${alumno.email}</div>
        <div><strong>Actividad:</strong> ${actividad.titulo}</div>
        <div><strong>Intento:</strong> #${intento.numero_intento}</div>
        <div><strong>Fecha Envío:</strong> ${new Date(intento.fecha_finalizacion).toLocaleString('es-MX')}</div>
        <div><strong>Estado:</strong> ${intento.calificado ? '<span class="badge badge-success">Calificado</span>' : '<span class="badge badge-warning">Pendiente</span>'}</div>
      </div>
    </div>
    
    <h4 style="font-size:1.1rem;margin-bottom:1rem">Respuestas del Estudiante</h4>
  `;
  
  respuestas.forEach((resp, idx) => {
    const isTextQuestion = !resp.id_opcion;
    const needsGrading = isTextQuestion && !resp.calificado;
    
    html += `
      <div style="background:var(--bg-secondary);padding:1.5rem;border-radius:var(--radius);margin-bottom:1rem">
        <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:1rem">
          <span style="background:var(--primary);color:#fff;width:2rem;height:2rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0">${idx+1}</span>
          <div style="flex:1">
            <p style="font-weight:600;margin-bottom:.5rem">${resp.pregunta}</p>
            <span style="font-size:.8rem;color:var(--text-muted)">${resp.puntos_pregunta} puntos</span>
          </div>
        </div>
        
        ${isTextQuestion ? `
          <div style="background:var(--bg-primary);padding:1rem;border-radius:var(--radius);margin-bottom:1rem">
            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.5rem">Respuesta del estudiante:</p>
            <p>${resp.texto_respuesta || '<em style="color:var(--text-muted)">Sin respuesta</em>'}</p>
          </div>
          
          ${needsGrading ? `
            <form onsubmit="return calificarRespuesta(event, ${resp.id_respuesta}, ${resp.puntos_pregunta})">
              <div style="display:grid;grid-template-columns:1fr 2fr auto;gap:1rem;align-items:end">
                <div>
                  <label style="display:block;margin-bottom:.5rem;font-size:.85rem;font-weight:600">Puntos Otorgados</label>
                  <input type="number" name="puntos" min="0" max="${resp.puntos_pregunta}" step="0.5" class="form-control" required>
                </div>
                <div>
                  <label style="display:block;margin-bottom:.5rem;font-size:.85rem;font-weight:600">Comentario (opcional)</label>
                  <input type="text" name="comentario" class="form-control" placeholder="Retroalimentación para el estudiante">
                </div>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-check"></i> Calificar
                </button>
              </div>
            </form>
          ` : `
            <div style="background:var(--success);color:#fff;padding:.75rem 1rem;border-radius:var(--radius);display:flex;justify-content:space-between;align-items:center">
              <span><i class="fas fa-check-circle"></i> Calificada: ${resp.puntos_obtenidos}/${resp.puntos_pregunta} puntos</span>
              ${resp.comentario_profesor ? `<span style="font-size:.85rem">Comentario: "${resp.comentario_profesor}"</span>` : ''}
            </div>
          `}
        ` : `
          <div style="margin-bottom:1rem">
            ${resp.opciones.map(opc => `
              <div style="padding:.75rem;border:2px solid ${opc.id_opcion == resp.id_opcion ? (opc.es_correcta ? 'var(--success)' : 'var(--danger)') : 'var(--border)'};border-radius:var(--radius);margin-bottom:.5rem;background:${opc.es_correcta ? 'rgba(16, 185, 129, 0.1)' : 'transparent'}">
                <div style="display:flex;align-items:center;gap:.75rem">
                  ${opc.id_opcion == resp.id_opcion ? '<i class="fas fa-arrow-right" style="color:var(--primary)"></i>' : ''}
                  <span>${opc.texto_opcion}</span>
                  ${opc.es_correcta ? '<i class="fas fa-check-circle" style="color:var(--success);margin-left:auto"></i>' : ''}
                </div>
              </div>
            `).join('')}
          </div>
          
          <div style="background:${resp.es_correcta ? 'var(--success)' : 'var(--danger)'};color:#fff;padding:.75rem 1rem;border-radius:var(--radius)">
            <i class="fas fa-${resp.es_correcta ? 'check' : 'times'}-circle"></i>
            ${resp.es_correcta ? 'Correcta' : 'Incorrecta'}: ${resp.puntos_obtenidos}/${resp.puntos_pregunta} puntos
          </div>
        `}
      </div>
    `;
  });
  
  // Add finalize button if all graded
  const allGraded = respuestas.every(r => r.calificado);
  if (!intento.calificado && allGraded) {
    html += `
      <form method="POST" style="text-align:center;margin-top:2rem" onsubmit="return confirm('¿Finalizar la calificación? El estudiante podrá ver sus resultados.')">
        <input type="hidden" name="action" value="finalizar_calificacion">
        <input type="hidden" name="id_inscripcion" value="${intento.id_inscripcion}">
        <input type="hidden" name="id_actividad" value="${intento.id_actividad}">
        <input type="hidden" name="intento" value="${intento.numero_intento}">
        <button type="submit" class="btn btn-success btn-lg">
          <i class="fas fa-flag-checkered"></i> Finalizar Calificación
        </button>
      </form>
    `;
  }
  
  document.getElementById('detallesContent').innerHTML = html;
}

function calificarRespuesta(e, id_respuesta, puntos_max) {
  e.preventDefault();
  const form = e.target;
  const puntos = parseFloat(form.puntos.value);
  const comentario = form.comentario.value;
  
  if (puntos > puntos_max) {
    alert(`Los puntos no pueden exceder ${puntos_max}`);
    return false;
  }
  
  const formData = new FormData();
  formData.append('action', 'calificar_respuesta');
  formData.append('id_respuesta', id_respuesta);
  formData.append('puntos', puntos);
  formData.append('comentario', comentario);
  
  fetch('', {method: 'POST', body: formData})
    .then(() => location.reload())
    .catch(err => alert('Error al calificar: ' + err));
  
  return false;
}

function cerrarModal() {
  document.getElementById('detallesModal').classList.remove('active');
}

// Close modal on outside click
document.getElementById('detallesModal').addEventListener('click', function(e) {
  if (e.target === this) cerrarModal();
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
