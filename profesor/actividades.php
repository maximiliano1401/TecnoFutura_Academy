<?php
$page_title = 'Actividades y Evaluaciones';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Actividad.php';
require_once __DIR__ . '/../backend/classes/Curso.php';
requiereRol(['PROFESOR']);

$db = Database::getInstance()->getConnection();
$id_docente = $_SESSION['info_adicional']['id_docente'] ?? 0;
$actObj = new Actividad();
$cursoObj = new Curso();

// Get teacher's courses
$cursos = $cursoObj->cursosDelProfesor($id_docente);

// Get all activities from teacher's courses
$stmt = $db->prepare("SELECT a.*, mc.titulo as material_titulo, mc.id_curso, c.nombre_curso,
    COUNT(DISTINCT p.id_pregunta) as total_preguntas
    FROM actividades a
    INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
    INNER JOIN cursos c ON mc.id_curso = c.id_curso
    LEFT JOIN preguntas p ON a.id_actividad = p.id_actividad
    WHERE c.id_docente = :doc
    GROUP BY a.id_actividad
    ORDER BY c.nombre_curso, mc.titulo");
$stmt->execute([':doc' => $id_docente]);
$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle actions
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'crear_cuestionario') {
        $id_curso = intval($_POST['id_curso']);
        $titulo = htmlspecialchars($_POST['titulo']);
        $descripcion = htmlspecialchars($_POST['descripcion'] ?? '');
        $num_preguntas = intval($_POST['num_preguntas'] ?? 5);
        $puntaje_por_pregunta = intval($_POST['puntaje_por_pregunta'] ?? 10);
        $duracion = intval($_POST['duracion_minutos'] ?? 30);
        $intentos = intval($_POST['intentos_permitidos'] ?? 1);
        
        // Verificar que el curso pertenece al profesor
        $stmt = $db->prepare("SELECT id_curso FROM cursos WHERE id_curso = :id AND id_docente = :doc");
        $stmt->execute([':id' => $id_curso, ':doc' => $id_docente]);
        if (!$stmt->fetch()) {
            $error = 'Curso no válido';
        } else {
            try {
                $db->beginTransaction();
                
                // 1. Crear material de tipo evaluacion
                $stmt = $db->prepare("INSERT INTO materiales_curso 
                    (id_curso, titulo, descripcion, tipo_material, orden, duracion_minutos)
                    VALUES (:curso, :titulo, :desc, 'evaluacion', 999, :dur)");
                $stmt->execute([
                    ':curso' => $id_curso,
                    ':titulo' => $titulo,
                    ':desc' => $descripcion,
                    ':dur' => $duracion
                ]);
                $id_material = $db->lastInsertId();
                
                // 2. Crear actividad
                $puntaje_total = $num_preguntas * $puntaje_por_pregunta;
                $puntaje_minimo = ceil($puntaje_total * 0.6); // 60% para aprobar
                
                $stmt = $db->prepare("INSERT INTO actividades 
                    (id_material, puntaje_total, puntaje_minimo_aprobatorio, duracion_minutos, intentos_permitidos, mostrar_respuestas)
                    VALUES (:mat, :total, :min, :dur, :int, 1)");
                $stmt->execute([
                    ':mat' => $id_material,
                    ':total' => $puntaje_total,
                    ':min' => $puntaje_minimo,
                    ':dur' => $duracion,
                    ':int' => $intentos
                ]);
                $id_actividad = $db->lastInsertId();
                
                // 3. Crear preguntas vacías
                for ($i = 1; $i <= $num_preguntas; $i++) {
                    $stmt = $db->prepare("INSERT INTO preguntas 
                        (id_actividad, texto_pregunta, tipo_pregunta, puntaje, orden)
                        VALUES (:act, :preg, 'opcion_multiple', :pts, :orden)");
                    $stmt->execute([
                        ':act' => $id_actividad,
                        ':preg' => "Pregunta $i",
                        ':pts' => $puntaje_por_pregunta,
                        ':orden' => $i
                    ]);
                    
                    $id_pregunta = $db->lastInsertId();
                    
                    // Crear 4 opciones por defecto
                    for ($j = 1; $j <= 4; $j++) {
                        $stmt = $db->prepare("INSERT INTO opciones_respuesta 
                            (id_pregunta, texto_opcion, es_correcta, orden)
                            VALUES (:preg, :texto, :correcta, :orden)");
                        $stmt->execute([
                            ':preg' => $id_pregunta,
                            ':texto' => "Opción $j",
                            ':correcta' => ($j === 1) ? 1 : 0, // Primera opción por defecto
                            ':orden' => $j
                        ]);
                    }
                }
                
                $db->commit();
                $msg = "Cuestionario '$titulo' creado con $num_preguntas preguntas. Ahora edita cada pregunta.";
                
                // Recargar actividades
                $stmt = $db->prepare("SELECT a.*, mc.titulo as material_titulo, mc.id_curso, c.nombre_curso,
                    COUNT(DISTINCT p.id_pregunta) as total_preguntas
                    FROM actividades a
                    INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
                    INNER JOIN cursos c ON mc.id_curso = c.id_curso
                    LEFT JOIN preguntas p ON a.id_actividad = p.id_actividad
                    WHERE c.id_docente = :doc
                    GROUP BY a.id_actividad
                    ORDER BY c.nombre_curso, mc.titulo");
                $stmt->execute([':doc' => $id_docente]);
                $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Error al crear cuestionario: ' . $e->getMessage();
            }
        }
    }
    elseif ($action === 'actualizar_pregunta') {
        $id_pregunta = intval($_POST['id_pregunta']);
        $texto_pregunta = htmlspecialchars($_POST['texto_pregunta']);
        $opciones = $_POST['opciones'] ?? [];
        $id_opciones = $_POST['id_opciones'] ?? [];
        $correcta = intval($_POST['opcion_correcta'] ?? 0);
        
        try {
            $db->beginTransaction();
            
            // Actualizar pregunta
            $stmt = $db->prepare("UPDATE preguntas SET texto_pregunta = :texto WHERE id_pregunta = :id");
            $stmt->execute([':texto' => $texto_pregunta, ':id' => $id_pregunta]);
            
            // Actualizar opciones
            foreach ($opciones as $idx => $texto_opcion) {
                $id_opcion = intval($id_opciones[$idx] ?? 0);
                $es_correcta = ($idx == $correcta) ? 1 : 0;
                
                if ($id_opcion > 0) {
                    $stmt = $db->prepare("UPDATE opciones_respuesta 
                        SET texto_opcion = :texto, es_correcta = :correcta 
                        WHERE id_opcion = :id");
                    $stmt->execute([
                        ':texto' => htmlspecialchars($texto_opcion),
                        ':correcta' => $es_correcta,
                        ':id' => $id_opcion
                    ]);
                }
            }
            
            $db->commit();
            $msg = 'Pregunta actualizada exitosamente';
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Error al actualizar pregunta: ' . $e->getMessage();
        }
    }
    elseif ($action === 'eliminar_actividad') {
        $id_actividad = intval($_POST['id_actividad']);
        
        // Verificar que pertenece al profesor
        $stmt = $db->prepare("SELECT a.id_actividad, a.id_material
            FROM actividades a
            INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
            INNER JOIN cursos c ON mc.id_curso = c.id_curso
            WHERE a.id_actividad = :id AND c.id_docente = :doc");
        $stmt->execute([':id' => $id_actividad, ':doc' => $id_docente]);
        $act = $stmt->fetch();
        
        if ($act) {
            try {
                $db->beginTransaction();
                
                // Eliminar en cascada
                $id_material = $act['id_material'];
                $db->exec("DELETE FROM opciones_respuesta WHERE id_pregunta IN 
                    (SELECT id_pregunta FROM preguntas WHERE id_actividad = $id_actividad)");
                $db->exec("DELETE FROM preguntas WHERE id_actividad = $id_actividad");
                $db->exec("DELETE FROM actividades WHERE id_actividad = $id_actividad");
                $db->exec("DELETE FROM materiales_curso WHERE id_material = $id_material");
                
                $db->commit();
                $msg = 'Cuestionario eliminado';
                
                // Recargar actividades
                $stmt = $db->prepare("SELECT a.*, mc.titulo as material_titulo, mc.id_curso, c.nombre_curso,
                    COUNT(DISTINCT p.id_pregunta) as total_preguntas
                    FROM actividades a
                    INNER JOIN materiales_curso mc ON a.id_material = mc.id_material
                    INNER JOIN cursos c ON mc.id_curso = c.id_curso
                    LEFT JOIN preguntas p ON a.id_actividad = p.id_actividad
                    WHERE c.id_docente = :doc
                    GROUP BY a.id_actividad
                    ORDER BY c.nombre_curso, mc.titulo");
                $stmt->execute([':doc' => $id_docente]);
                $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Error al eliminar: ' . $e->getMessage();
            }
        }
    }
}

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:220px 1fr;min-height:calc(100vh - var(--navbar-height))">
<?php $active_page = 'actividades'; include_once __DIR__ . '/../includes/profesor_sidebar.php'; ?>
<div class="admin-main">
  <div class="admin-header">
    <div>
      <h1 class="admin-title">Cuestionarios y Evaluaciones</h1>
      <p class="admin-subtitle">Crea cuestionarios con opción múltiple de forma rápida y sencilla.</p>
    </div>
    <button class="btn btn-primary" data-open-modal="crearCuestionarioModal"><i class="fas fa-plus"></i> Nuevo Cuestionario</button>
  </div>

  <?php if ($msg): ?>
  <div class="alert alert-success"><i class="fas fa-check-circle alert-icon"></i><?= $msg ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="alert alert-danger"><i class="fas fa-exclamation-circle alert-icon"></i><?= $error ?></div>
  <?php endif; ?>

  <!-- Grid de Cuestionarios -->
  <?php if (empty($actividades)): ?>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:3rem 2rem;text-align:center">
    <i class="fas fa-clipboard-list" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem;display:block"></i>
    <h3 style="font-size:1.25rem;margin-bottom:.5rem">No hay cuestionarios aún</h3>
    <p style="color:var(--text-muted);margin-bottom:1.5rem">Crea tu primer cuestionario para empezar a evaluar a tus alumnos.</p>
    <button class="btn btn-primary" data-open-modal="crearCuestionarioModal"><i class="fas fa-plus"></i> Crear Mi Primer Cuestionario</button>
  </div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:1.25rem">
    <?php foreach ($actividades as $act): ?>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:border .2s,box-shadow .2s">
      <div style="padding:1.25rem;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:start;justify-content:space-between;gap:.75rem;margin-bottom:.75rem">
          <h3 style="font-size:1rem;font-weight:600;line-height:1.4;flex:1"><?= htmlspecialchars($act['material_titulo']) ?></h3>
          <div style="display:flex;gap:.25rem">
            <button class="btn btn-sm btn-ghost" onclick="editarCuestionario(<?= $act['id_actividad'] ?>)" title="Editar preguntas"><i class="fas fa-edit"></i></button>
            <form method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar este cuestionario y todas sus preguntas?')">
              <input type="hidden" name="action" value="eliminar_actividad">
              <input type="hidden" name="id_actividad" value="<?= $act['id_actividad'] ?>">
              <button type="submit" class="btn btn-sm btn-ghost text-danger-item" title="Eliminar"><i class="fas fa-trash"></i></button>
            </form>
          </div>
        </div>
        
        <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.5rem">
          <i class="fas fa-book-open"></i> <?= htmlspecialchars($act['nombre_curso']) ?>
        </div>
        
        <?php if (!empty($act['descripcion'])): ?>
        <p style="font-size:.85rem;color:var(--text-muted);line-height:1.4;margin-bottom:.75rem"><?= htmlspecialchars(mb_substr($act['descripcion'], 0, 100)) ?><?= mb_strlen($act['descripcion']) > 100 ? '...' : '' ?></p>
        <?php endif; ?>
      </div>
      
      <div style="padding:1rem 1.25rem;background:var(--bg-subtle);display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem">
        <div>
          <div style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.25rem">Preguntas</div>
          <div style="font-size:1.1rem;font-weight:700;color:var(--primary)"><?= $act['total_preguntas'] ?? 0 ?></div>
        </div>
        <div>
          <div style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.25rem">Puntaje</div>
          <div style="font-size:1.1rem;font-weight:700"><?= $act['puntaje_total'] ?> pts</div>
        </div>
        <div>
          <div style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.25rem">Duración</div>
          <div style="font-size:.9rem;font-weight:600"><?= $act['duracion_minutos'] > 0 ? $act['duracion_minutos'].' min' : 'Sin límite' ?></div>
        </div>
        <div>
          <div style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.25rem">Intentos</div>
          <div style="font-size:.9rem;font-weight:600"><?= $act['intentos_permitidos'] > 0 ? $act['intentos_permitidos'] : 'Ilimitado' ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</div>

<!-- Modal: Crear Cuestionario -->
<div class="modal-backdrop" id="crearCuestionarioModal">
  <div class="modal" style="max-width:600px">
    <div class="modal-header">
      <h2 class="modal-title">Crear Nuevo Cuestionario</h2>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="crear_cuestionario">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Curso</label>
          <select name="id_curso" class="form-control" required>
            <option value="">Selecciona un curso...</option>
            <?php foreach ($cursos as $curso): ?>
            <option value="<?= $curso['id_curso'] ?>"><?= htmlspecialchars($curso['nombre_curso']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label class="form-label">Título del Cuestionario</label>
          <input type="text" name="titulo" class="form-control" placeholder="Ej: Examen de Arduino - Unidad 1" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Descripción / Instrucciones</label>
          <textarea name="descripcion" class="form-control" rows="3" placeholder="Instrucciones para el alumno..."></textarea>
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Número de Preguntas</label>
            <input type="number" name="num_preguntas" class="form-control" value="5" min="1" max="50" required>
            <small style="color:var(--text-muted);font-size:.75rem">Se crearán con 4 opciones cada una</small>
          </div>
          <div class="form-group">
            <label class="form-label">Puntos por Pregunta</label>
            <input type="number" name="puntaje_por_pregunta" class="form-control" value="10" min="1" required>
          </div>
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Duración (minutos)</label>
            <input type="number" name="duracion_minutos" class="form-control" value="30" min="0" placeholder="0 = sin límite">
          </div>
          <div class="form-group">
            <label class="form-label">Intentos Permitidos</label>
            <input type="number" name="intentos_permitidos" class="form-control" value="2" min="0" placeholder="0 = ilimitados">
          </div>
        </div>
        
        <div class="form-group">
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
            <input type="checkbox" name="mostrar_respuestas" value="1" checked>
            <span>Mostrar respuestas correctas al finalizar</span>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost modal-close">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-magic"></i> Crear Cuestionario</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Editar Preguntas -->
<div class="modal-backdrop" id="editarPreguntasModal">
  <div class="modal" style="max-width:800px">
    <div class="modal-header">
      <h2 class="modal-title">Editar Preguntas</h2>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" id="preguntasContainer" style="max-height:70vh;overflow-y:auto">
      <div style="text-align:center;padding:2rem;color:var(--text-muted)">
        <i class="fas fa-spinner fa-spin" style="font-size:2rem"></i>
        <p style="margin-top:1rem">Cargando preguntas...</p>
      </div>
    </div>
  </div>
</div>

<script>
// Manejo de modales
document.querySelectorAll('[data-open-modal]').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault();
    const modalId = btn.getAttribute('data-open-modal');
    document.getElementById(modalId).classList.add('show');
  });
});

document.querySelectorAll('.modal-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal-backdrop').classList.remove('show');
  });
});

document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
  backdrop.addEventListener('click', e => {
    if (e.target === backdrop) {
      backdrop.classList.remove('show');
    }
  });
});

// Editar cuestionario (cargar preguntas)
function editarCuestionario(idActividad) {
  const modal = document.getElementById('editarPreguntasModal');
  const container = document.getElementById('preguntasContainer');
  
  // Mostrar modal
  modal.classList.add('show');
  
  // Cargar preguntas
  fetch('<?= SITE_URL ?>/backend/ajax/obtener_preguntas.php?id_actividad=' + idActividad)
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
        return;
      }
      
      if (data.preguntas.length === 0) {
        container.innerHTML = `
          <div style="text-align:center;padding:2rem;color:var(--text-muted)">
            <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:1rem;display:block"></i>
            <p>Este cuestionario no tiene preguntas aún.</p>
          </div>
        `;
        return;
      }
      
      // Renderizar preguntas editables
      container.innerHTML = data.preguntas.map((preg, idx) => `
        <div style="background:var(--bg-subtle);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1rem">
          <form method="POST">
            <input type="hidden" name="action" value="actualizar_pregunta">
            <input type="hidden" name="id_pregunta" value="${preg.id_pregunta}">
            
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
              <span style="background:var(--primary);color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700">${idx + 1}</span>
              <span class="badge">Opción Múltiple</span>
            </div>
            
            <div class="form-group">
              <label class="form-label">Pregunta</label>
              <textarea name="texto_pregunta" class="form-control" rows="2" required>${preg.texto_pregunta || ''}</textarea>
            </div>
            
            <div class="form-group">
              <label class="form-label">Opciones (marca la correcta)</label>
              ${(preg.opciones || []).map((opc, opcIdx) => `
                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
                  <input type="hidden" name="id_opciones[]" value="${opc.id_opcion}">
                  <input type="radio" name="opcion_correcta" value="${opcIdx}" ${opc.es_correcta ? 'checked' : ''} required>
                  <input type="text" name="opciones[]" class="form-control" value="${opc.texto_opcion || ''}" placeholder="Opción ${opcIdx + 1}" required>
                </div>
              `).join('')}
            </div>
            
            <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:1rem">
              <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </div>
          </form>
        </div>
      `).join('');
    })
    .catch(err => {
      container.innerHTML = `<div class="alert alert-danger">Error al cargar preguntas: ${err.message}</div>`;
    });
}
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
