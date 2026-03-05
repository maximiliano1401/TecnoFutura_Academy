<?php
$page_title = 'Mis Cursos';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';
requiereRol(['PROFESOR']);

$db = Database::getInstance()->getConnection();
$id_docente = $_SESSION['info_adicional']['id_docente'] ?? 0;
$obj = new Curso();

// Handle actions
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'crear_curso') {
        $datos = [
            'nombre_curso' => htmlspecialchars($_POST['nombre_curso']),
            'descripcion' => htmlspecialchars($_POST['descripcion']),
            'nivel' => htmlspecialchars($_POST['nivel']),
            'precio' => floatval($_POST['precio']),
            'duracion_horas' => intval($_POST['duracion_horas']),
            'id_docente' => $id_docente,
            'imagen_portada' => null
        ];
        
        $result = $obj->crear($datos);
        if ($result['success']) {
            $msg = 'Curso creado exitosamente.';
        } else {
            $error = 'Error al crear el curso: ' . $result['message'];
        }
    }
    elseif ($action === 'toggle_activo') {
        $id_curso = intval($_POST['id_curso']);
        // Verificar que el curso pertenece al profesor
        $curso = $db->prepare("SELECT id_curso FROM cursos WHERE id_curso = :id AND id_docente = :doc");
        $curso->execute([':id' => $id_curso, ':doc' => $id_docente]);
        if ($curso->fetch()) {
            $db->prepare("UPDATE cursos SET activo = NOT activo WHERE id_curso = :id")->execute([':id' => $id_curso]);
            $msg = 'Estado del curso actualizado.';
        }
    }
    elseif ($action === 'editar_curso') {
        $id_curso = intval($_POST['id_curso']);
        $datos = [
            'nombre_curso' => htmlspecialchars($_POST['nombre_curso']),
            'descripcion' => htmlspecialchars($_POST['descripcion']),
            'nivel' => htmlspecialchars($_POST['nivel']),
            'precio' => floatval($_POST['precio']),
            'duracion_horas' => intval($_POST['duracion_horas']),
            'activo' => intval($_POST['activo'] ?? 1)
        ];
        
        // Verificar que el curso pertenece al profesor
        $curso = $db->prepare("SELECT id_curso FROM cursos WHERE id_curso = :id AND id_docente = :doc");
        $curso->execute([':id' => $id_curso, ':doc' => $id_docente]);
        if ($curso->fetch()) {
            if ($obj->actualizar($id_curso, $datos)) {
                $msg = 'Curso actualizado exitosamente.';
            } else {
                $error = 'Error al actualizar el curso.';
            }
        }
    }
}

// Get docente's courses
$mis_cursos = $db->prepare("SELECT c.*,
    (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso) AS total_alumnos,
    (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = c.id_curso AND i.estado IN ('Finalizado','Certificado')) AS finalizados,
    (SELECT COUNT(*) FROM materiales_curso m WHERE m.id_curso = c.id_curso) AS total_lecciones,
    (SELECT SUM(p.monto) FROM pagos p JOIN inscripciones i ON p.id_inscripcion = i.id_inscripcion WHERE i.id_curso = c.id_curso AND p.estado_pago = 'completado') AS ingresos_totales
    FROM cursos c WHERE c.id_docente = :d ORDER BY c.id_curso DESC");
$mis_cursos->execute([':d' => $id_docente]);
$cursos = $mis_cursos->fetchAll();

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:220px 1fr;min-height:calc(100vh - var(--navbar-height))">
<?php $active_page = 'cursos'; include_once __DIR__ . '/../includes/profesor_sidebar.php'; ?>
<div class="admin-main">
  <div class="admin-header">
    <div><h1 class="admin-title">Mis Cursos</h1><p class="admin-subtitle">Gestiona tus cursos: crea contenido, sube materiales y monitorea a tus estudiantes.</p></div>
    <button class="btn btn-primary" data-open-modal="crearCursoModal"><i class="fas fa-plus"></i> Nuevo Curso</button>
  </div>

  <?php if ($msg): ?>
  <div class="alert alert-success"><i class="fas fa-check-circle alert-icon"></i><?= $msg ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="alert alert-danger"><i class="fas fa-exclamation-circle alert-icon"></i><?= $error ?></div>
  <?php endif; ?>

  <?php if (empty($cursos)): ?>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:3rem 2rem;text-align:center">
    <i class="fas fa-book-open" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem"></i>
    <h3 style="font-size:1.25rem;margin-bottom:.5rem">No tienes cursos creados</h3>
    <p style="color:var(--text-muted);margin-bottom:1.5rem">Comienza creando tu primer curso y comparte tu conocimiento.</p>
    <button class="btn btn-primary" data-open-modal="crearCursoModal"><i class="fas fa-plus"></i> Crear Mi Primer Curso</button>
  </div>
  <?php else: ?>
  
  <!-- Courses Grid -->
  <div class="grid-3">
    <?php foreach ($cursos as $c): 
      $ingresos = number_format($c['ingresos_totales'] ?? 0, 2);
      $nivel_slug = strtolower(str_replace('á', 'a', $c['nivel']));
    ?>
    <div class="course-card" style="height:auto">
      <div class="course-card-thumb" style="height:180px;background:var(--grad-<?= $nivel_slug ?>)">
        <?php if ($c['imagen_portada']): ?>
        <img src="<?= SITE_URL ?>/uploads/cursos/imagenes/<?= urlencode($c['imagen_portada']) ?>" alt="<?= htmlspecialchars($c['nombre_curso']) ?>">
        <?php else: ?>
        <div style="display:flex;align-items:center;justify-content:center;height:100%;color:white">
          <i class="fas fa-microchip" style="font-size:3rem"></i>
        </div>
        <?php endif; ?>
        <span class="course-card-badge badge-<?= $nivel_slug ?>"><?= $c['nivel'] ?></span>
      </div>
      <div style="padding:1.25rem">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:.5rem;line-height:1.3">
          <a href="<?= SITE_URL ?>/cursos/detalle.php?id=<?= $c['id_curso'] ?>" style="color:var(--text-primary);text-decoration:none">
            <?= htmlspecialchars($c['nombre_curso']) ?>
          </a>
        </h3>
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:1rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
          <?= htmlspecialchars(mb_substr($c['descripcion'], 0, 80)) ?>...
        </p>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem;font-size:.75rem">
          <div style="background:var(--bg-subtle);padding:.5rem;border-radius:var(--radius);text-align:center">
            <div style="font-weight:700;color:var(--primary);font-size:1.1rem"><?= $c['total_alumnos'] ?></div>
            <div style="color:var(--text-muted)">Alumnos</div>
          </div>
          <div style="background:var(--bg-subtle);padding:.5rem;border-radius:var(--radius);text-align:center">
            <div style="font-weight:700;color:var(--primary);font-size:1.1rem"><?= $c['total_lecciones'] ?></div>
            <div style="color:var(--text-muted)">Lecciones</div>
          </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;padding:.75rem;background:var(--bg-subtle);border-radius:var(--radius)">
          <div style="font-size:.75rem;color:var(--text-muted)">Ingresos</div>
          <div style="font-size:1rem;font-weight:700;color:var(--success)">$<?= $ingresos ?></div>
        </div>

        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <a href="<?= SITE_URL ?>/profesor/materiales.php?id=<?= $c['id_curso'] ?>" class="btn btn-sm btn-secondary" style="flex:1"><i class="fas fa-film"></i> Materiales</a>
          <button class="btn btn-sm btn-ghost" data-edit-curso="<?= $c['id_curso'] ?>" title="Editar"><i class="fas fa-edit"></i></button>
          <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="toggle_activo">
            <input type="hidden" name="id_curso" value="<?= $c['id_curso'] ?>">
            <button type="submit" class="btn btn-sm <?= $c['activo'] ? 'btn-warning' : 'btn-success' ?>" title="<?= $c['activo'] ? 'Desactivar' : 'Activar' ?>">
              <i class="fas fa-<?= $c['activo'] ? 'eye-slash' : 'eye' ?>"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</div>

<!-- Modal: Crear Curso -->
<div class="modal-backdrop" id="crearCursoModal">
  <div class="modal" style="max-width:600px">
    <div class="modal-header">
      <h2 class="modal-title">Crear Nuevo Curso</h2>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="crear_curso">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nombre del Curso <span style="color:var(--danger)">*</span></label>
          <input type="text" name="nombre_curso" class="form-control" required placeholder="Ej: Programación en Arduino UNO">
        </div>
        
        <div class="form-group">
          <label class="form-label">Descripción <span style="color:var(--danger)">*</span></label>
          <textarea name="descripcion" class="form-control" rows="4" required placeholder="Describe qué aprenderán los estudiantes en este curso..."></textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Nivel</label>
            <select name="nivel" class="form-control">
              <option value="Básico">Básico</option>
              <option value="Intermedio">Intermedio</option>
              <option value="Avanzado">Avanzado</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Duración (horas)</label>
            <input type="number" name="duracion_horas" class="form-control" value="20" min="1" max="200">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Precio (MXN)</label>
          <input type="number" name="precio" class="form-control" value="0" min="0" step="0.01" placeholder="0 para curso gratuito">
          <small style="color:var(--text-muted);font-size:.75rem">Ingresa 0 para ofrecer el curso gratuitamente</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost modal-close">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Crear Curso</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Editar Curso (se llena dinámicamente con JS) -->
<div class="modal-backdrop" id="editarCursoModal">
  <div class="modal" style="max-width:600px">
    <div class="modal-header">
      <h2 class="modal-title">Editar Curso</h2>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="formEditarCurso">
      <input type="hidden" name="action" value="editar_curso">
      <input type="hidden" name="id_curso" id="edit_id_curso">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nombre del Curso</label>
          <input type="text" name="nombre_curso" id="edit_nombre" class="form-control" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Nivel</label>
            <select name="nivel" id="edit_nivel" class="form-control">
              <option value="Básico">Básico</option>
              <option value="Intermedio">Intermedio</option>
              <option value="Avanzado">Avanzado</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Duración (horas)</label>
            <input type="number" name="duracion_horas" id="edit_duracion" class="form-control" min="1" max="200">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Precio (MXN)</label>
          <input type="number" name="precio" id="edit_precio" class="form-control" min="0" step="0.01">
        </div>

        <div class="form-group">
          <label class="form-label">Estado</label>
          <select name="activo" id="edit_activo" class="form-control">
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost modal-close">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
// Data for edit modal
const cursosData = <?= json_encode($cursos) ?>;

// Open modal by data-open-modal attribute
document.querySelectorAll('[data-open-modal]').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const modalId = e.currentTarget.dataset.openModal;
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('show');
    }
  });
});

// Close modal handlers
document.querySelectorAll('.modal-close').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const modal = e.target.closest('.modal-backdrop');
    if (modal) {
      modal.classList.remove('show');
    }
  });
});

// Close modal when clicking backdrop
document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
  backdrop.addEventListener('click', (e) => {
    if (e.target === backdrop) {
      backdrop.classList.remove('show');
    }
  });
});

// Open edit modal
document.querySelectorAll('[data-edit-curso]').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const id = parseInt(e.currentTarget.dataset.editCurso);
    const curso = cursosData.find(c => c.id_curso == id);
    if (curso) {
      document.getElementById('edit_id_curso').value = curso.id_curso;
      document.getElementById('edit_nombre').value = curso.nombre_curso;
      document.getElementById('edit_descripcion').value = curso.descripcion;
      document.getElementById('edit_nivel').value = curso.nivel;
      document.getElementById('edit_duracion').value = curso.duracion_horas;
      document.getElementById('edit_precio').value = curso.precio;
      document.getElementById('edit_activo').value = curso.activo ? '1' : '0';
      document.getElementById('editarCursoModal').classList.add('show');
    }
  });
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
