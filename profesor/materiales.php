<?php
$page_title = 'Gestión de Materiales';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/upload_handler.php';
requiereRol(['PROFESOR']);

$db         = Database::getInstance()->getConnection();
$id_docente = $_SESSION['info_adicional']['id_docente'] ?? 0;
$uploader   = new UploadHandler();

// Get docente's courses
$cursos_stmt = $db->prepare("SELECT id_curso, nombre_curso FROM cursos WHERE id_docente = :d AND activo = 1 ORDER BY nombre_curso");
$cursos_stmt->execute([':d' => $id_docente]);
$mis_cursos = $cursos_stmt->fetchAll();

$selected_curso = intval($_GET['id'] ?? ($mis_cursos[0]['id_curso'] ?? 0));
$materiales = [];
if ($selected_curso) {
    $m = $db->prepare("SELECT * FROM materiales_curso WHERE id_curso = :c ORDER BY orden ASC");
    $m->execute([':c' => $selected_curso]);
    $materiales = $m->fetchAll();
}

// Handle add
$msg = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_material') {
        $id_curso = intval($_POST['id_curso']);
        $tipo_material = htmlspecialchars($_POST['tipo_material']);
        $url_material = '';
        $url_archivo = '';
        
        // Determinar origen del material
        $origen = $_POST['origen'] ?? 'url'; // 'url' o 'archivo'
        
        if ($origen === 'archivo' && isset($_FILES['archivo_material']) && $_FILES['archivo_material']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Subir archivo
            $tipo_upload = in_array($tipo_material, ['video']) ? 'video' : 'documento';
            $result = $uploader->uploadMaterial($_FILES['archivo_material'], $id_curso, $tipo_upload);
            
            if ($result['success']) {
                $url_archivo = $result['path'];
                $url_material = ''; // Limpiar URL si se subió archivo
            } else {
                $error = $result['message'];
            }
        } elseif ($origen === 'url') {
            // Usar URL externa
            $url_material = htmlspecialchars($_POST['url_material'] ?? '');
        }
        
        // Si no hubo error, insertar en BD
        if (empty($error)) {
            $stmt = $db->prepare("INSERT INTO materiales_curso 
                (id_curso, titulo, descripcion, tipo_material, url_archivo, url_material, orden, duracion_minutos)
                VALUES (:c,:t,:d,:tipo,:arch,:url,:o,:dur)");
            
            $ok = $stmt->execute([
                ':c'    => $id_curso,
                ':t'    => htmlspecialchars($_POST['titulo']),
                ':d'    => htmlspecialchars($_POST['descripcion'] ?? ''),
                ':tipo' => $tipo_material,
                ':arch' => $url_archivo,
                ':url'  => $url_material,
                ':o'    => intval($_POST['orden'] ?? count($materiales)+1),
                ':dur'  => intval($_POST['duracion_minutos'] ?? 0),
            ]);
            
            if ($ok) {
                $msg = 'Material agregado exitosamente.';
                // Recargar materiales
                $m = $db->prepare("SELECT * FROM materiales_curso WHERE id_curso = :c ORDER BY orden ASC");
                $m->execute([':c' => $selected_curso]);
                $materiales = $m->fetchAll();
            } else {
                $error = 'Error al agregar material a la base de datos.';
            }
        }
    } 
    elseif ($action === 'delete_material') {
        $id_material = intval($_POST['id_material']);
        
        // Obtener info del material para eliminar archivo si existe
        $stmt = $db->prepare("SELECT url_archivo FROM materiales_curso WHERE id_material = :i");
        $stmt->execute([':i' => $id_material]);
        $material = $stmt->fetch();
        
        // Eliminar archivo físico si existe
        if ($material && !empty($material['url_archivo'])) {
            $uploader->deleteFile($material['url_archivo']);
        }
        
        // Eliminar de BD
        $db->prepare("DELETE FROM materiales_curso WHERE id_material = :i")->execute([':i' => $id_material]);
        $msg = 'Material eliminado.';
        
        // Recargar materiales
        $m = $db->prepare("SELECT * FROM materiales_curso WHERE id_curso = :c ORDER BY orden ASC");
        $m->execute([':c' => $selected_curso]);
        $materiales = $m->fetchAll();
    }
}

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:220px 1fr;min-height:calc(100vh - var(--navbar-height))">
<?php $active_page = 'materiales'; include_once __DIR__ . '/../includes/profesor_sidebar.php'; ?>
<div class="admin-main">
  <div class="admin-header">
    <div><h1 class="admin-title">Materiales del Curso</h1><p class="admin-subtitle">Administra el contenido de tus cursos.</p></div>
    <a href="#" class="btn btn-primary" data-open-modal="addMatModal"><i class="fas fa-plus"></i> Agregar Lección</a>
  </div>
  <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle alert-icon"></i><?= $msg ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle alert-icon"></i><?= $error ?></div><?php endif; ?>
  
  <!-- Course selector -->
  <div style="margin-bottom:1.5rem">
    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
      <?php foreach ($mis_cursos as $c): ?>
      <a href="?id=<?= $c['id_curso'] ?>" class="btn btn-sm <?= $selected_curso == $c['id_curso'] ? 'btn-primary' : 'btn-ghost' ?>">
        <?= htmlspecialchars($c['nombre_curso']) ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($selected_curso): ?>
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between">
      <span style="font-size:.875rem;color:var(--text-muted)"><?= count($materiales) ?> lecciones</span>
    </div>
    <table class="data-table">
      <thead><tr><th>#</th><th>Título</th><th>Tipo</th><th>Duración</th><th>URL</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($materiales as $m): ?>
        <tr>
          <td style="color:var(--text-muted);font-size:.8rem"><?= $m['orden'] ?></td>
          <td style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($m['titulo']) ?></td>
          <td><span class="badge"><?= ucfirst($m['tipo_material']) ?></span></td>
          <td style="font-size:.8rem;color:var(--text-muted)"><?= $m['duracion_minutos'] ? $m['duracion_minutos'].' min' : '—' ?></td>
          <td style="font-size:.72rem;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted)"><?= $m['url_material'] ?: '—' ?></td>
          <td>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="delete_material">
              <input type="hidden" name="id_material" value="<?= $m['id_material'] ?>">
              <input type="hidden" name="id" value="<?= $selected_curso ?>">
              <button type="submit" class="btn btn-ghost btn-sm text-danger-item" data-confirm="¿Eliminar esta lección?">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($materiales)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Este curso no tiene materiales aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
</div>

<!-- Add Material Modal -->
<div class="modal-backdrop" id="addMatModal">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title">Agregar Lección</h2>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add_material">
      <input type="hidden" name="id_curso" value="<?= $selected_curso ?>">
      <input type="hidden" name="id" value="<?= $selected_curso ?>">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Título de la Lección <span style="color:var(--danger)">*</span></label>
          <input type="text" name="titulo" class="form-control" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3" placeholder="Breve descripción del contenido..."></textarea>
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Tipo de Material</label>
            <select name="tipo_material" id="tipo_material" class="form-control">
              <option value="video">Video</option>
              <option value="documento">Documento/PDF</option>
              <option value="texto">Texto/Artículo</option>
              <option value="ejercicio">Ejercicio/Práctica</option>
              <option value="evaluacion">Evaluación/Examen</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Duración (minutos)</label>
            <input type="number" name="duracion_minutos" class="form-control" min="0" placeholder="ej: 25">
          </div>
        </div>

        <!-- Origen del material -->
        <div class="form-group">
          <label class="form-label">Origen del Material</label>
          <div style="display:flex;gap:1rem;margin-top:.5rem">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
              <input type="radio" name="origen" value="archivo" checked onchange="toggleOrigenMaterial()">
              <span>Subir Archivo</span>
            </label>
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
              <input type="radio" name="origen" value="url" onchange="toggleOrigenMaterial()">
              <span>URL Externa (YouTube, etc.)</span>
            </label>
          </div>
        </div>

        <!-- Upload file -->
        <div class="form-group" id="upload_section">
          <label class="form-label">Archivo <span style="color:var(--danger)">*</span></label>
          <input type="file" name="archivo_material" class="form-control" accept=".pdf,.doc,.docx,.mp4,.avi,.mov,.jpg,.jpeg,.png">
          <small style="color:var(--text-muted);font-size:.75rem;display:block;margin-top:.25rem">
            <strong>Documentos:</strong> PDF, DOC, DOCX (max <?= $uploader->getMaxSizeFormatted('documento') ?>)<br>
            <strong>Videos:</strong> MP4, AVI, MOV (max <?= $uploader->getMaxSizeFormatted('video') ?>)<br>
            <strong>Imágenes:</strong> JPG, PNG (max <?= $uploader->getMaxSizeFormatted('imagen') ?>)
          </small>
        </div>

        <!-- URL externa -->
        <div class="form-group" id="url_section" style="display:none">
          <label class="form-label">URL del Material <span style="color:var(--danger)">*</span></label>
          <input type="text" name="url_material" class="form-control" placeholder="https://youtube.com/watch?v=... o https://ejemplo.com/archivo.pdf">
          <small style="color:var(--text-muted);font-size:.75rem">Enlace directo a YouTube, Vimeo, Google Drive, etc.</small>
        </div>
        
        <div class="form-group">
          <label class="form-label">Orden de Aparición</label>
          <input type="number" name="orden" class="form-control" value="<?= count($materiales)+1 ?>" min="1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost modal-close">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Agregar Lección</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleOrigenMaterial() {
  const origen = document.querySelector('input[name="origen"]:checked').value;
  const uploadSection = document.getElementById('upload_section');
  const urlSection = document.getElementById('url_section');
  
  if (origen === 'archivo') {
    uploadSection.style.display = 'block';
    urlSection.style.display = 'none';
    uploadSection.querySelector('input[type="file"]').required = true;
    urlSection.querySelector('input[type="text"]').required = false;
  } else {
    uploadSection.style.display = 'none';
    urlSection.style.display = 'block';
    uploadSection.querySelector('input[type="file"]').required = false;
    urlSection.querySelector('input[type="text"]').required = true;
  }
}

// Open modal by data-open-modal attribute
document.querySelectorAll('[data-open-modal]').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.preventDefault();
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
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
