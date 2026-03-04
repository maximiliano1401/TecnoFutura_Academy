<?php
$page_title = 'Mi Perfil';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereAutenticacion();

$db   = Database::getInstance()->getConnection();
$uid  = $_SESSION['usuario_id'] ?? 0;
$rol  = $_SESSION['usuario_rol'] ?? '';

$stmt = $db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
$stmt->execute([':id' => $uid]);
$user = $stmt->fetch();

$msg     = '';
$msg_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_profile') {
        $nombre = htmlspecialchars(trim($_POST['nombre_completo'] ?? ''));
        if (strlen($nombre) >= 3) {
            $db->prepare("UPDATE usuarios SET nombre_completo = :n WHERE id_usuario = :id")
               ->execute([':n' => $nombre, ':id' => $uid]);
            $_SESSION['usuario_nombre'] = $nombre;
            $user['nombre_completo'] = $nombre;
            $msg = 'Perfil actualizado exitosamente.';
        } else {
            $msg = 'El nombre debe tener al menos 3 caracteres.';
            $msg_type = 'danger';
        }
    } elseif ($action === 'change_password') {
        $new_pwd  = $_POST['nueva_contrasena'] ?? '';
        $conf_pwd = $_POST['confirmar_contrasena'] ?? '';
        $cur_pwd  = $_POST['contrasena_actual'] ?? '';
        if (!password_verify($cur_pwd, $user['contrasena_hash'])) {
            $msg = 'Contraseña actual incorrecta.'; $msg_type = 'danger';
        } elseif (strlen($new_pwd) < 8) {
            $msg = 'La nueva contraseña debe tener al menos 8 caracteres.'; $msg_type = 'danger';
        } elseif ($new_pwd !== $conf_pwd) {
            $msg = 'Las contraseñas no coinciden.'; $msg_type = 'danger';
        } else {
            $hash = password_hash($new_pwd, PASSWORD_BCRYPT);
            $db->prepare("UPDATE usuarios SET contrasena_hash = :h WHERE id_usuario = :id")
               ->execute([':h' => $hash, ':id' => $uid]);
            $msg = 'Contraseña cambiada exitosamente.';
        }
    }
}

$initials = strtoupper(substr($user['nombre_completo'] ?? 'U', 0, 2));

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);padding:3rem 0 4rem;min-height:calc(100vh - var(--navbar-height))">
  <div class="container" style="max-width:900px">
    <div class="section-eyebrow" style="margin-bottom:.5rem">Cuenta</div>
    <h1 style="font-size:2rem;font-weight:800;margin-bottom:2.5rem">Mi Perfil</h1>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?>" style="margin-bottom:2rem">
      <i class="fas fa-<?= $msg_type === 'success' ? 'check-circle' : 'exclamation-circle' ?> alert-icon"></i>
      <?= $msg ?>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:280px 1fr;gap:2rem;align-items:start">
      <!-- Left: Avatar card -->
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;text-align:center">
        <div style="width:96px;height:96px;background:var(--grad-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:#fff;margin:0 auto 1.25rem">
          <?= $initials ?>
        </div>
        <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:.25rem"><?= htmlspecialchars($user['nombre_completo']) ?></h2>
        <div style="color:var(--text-muted);font-size:.82rem;margin-bottom:.5rem"><?= htmlspecialchars($user['correo_electronico']) ?></div>
        <span class="badge badge-<?= $rol === 'ADMIN' ? 'danger' : ($rol === 'PROFESOR' ? 'info' : 'success') ?>"><?= $rol ?></span>
        <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border)">
          <div style="font-size:.75rem;color:var(--text-muted)">Miembro desde</div>
          <div style="font-weight:600;font-size:.875rem"><?= date('d/m/Y', strtotime($user['fecha_registro'])) ?></div>
        </div>
        <!-- Quick links -->
        <div style="margin-top:1.25rem;display:flex;flex-direction:column;gap:.4rem">
          <?php if ($rol === 'USUARIO'): ?>
          <a href="<?= SITE_URL ?>/lms" class="btn btn-ghost btn-sm"><i class="fas fa-graduation-cap"></i> Mis Cursos</a>
          <a href="<?= SITE_URL ?>/lms/certificados.php" class="btn btn-ghost btn-sm"><i class="fas fa-certificate"></i> Certificados</a>
          <?php elseif ($rol === 'PROFESOR'): ?>
          <a href="<?= SITE_URL ?>/profesor" class="btn btn-ghost btn-sm"><i class="fas fa-chalkboard-teacher"></i> Portal Docente</a>
          <?php elseif ($rol === 'ADMIN'): ?>
          <a href="<?= SITE_URL ?>/admin" class="btn btn-ghost btn-sm"><i class="fas fa-shield-alt"></i> Admin Panel</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right: Forms -->
      <div style="display:flex;flex-direction:column;gap:1.5rem">
        <!-- Update profile -->
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem">
          <h3 style="font-size:1.05rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:1px solid var(--border)">
            <i class="fas fa-user-edit" style="color:var(--primary)"></i> Información Personal
          </h3>
          <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group">
              <label class="form-label">Nombre Completo</label>
              <div class="form-control-icon">
                <i class="fas fa-user icon"></i>
                <input type="text" name="nombre_completo" class="form-control" value="<?= htmlspecialchars($user['nombre_completo']) ?>" required minlength="3">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Correo Electrónico</label>
              <div class="form-control-icon">
                <i class="fas fa-envelope icon"></i>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['correo_electronico']) ?>" readonly style="opacity:.6;cursor:not-allowed">
              </div>
              <div style="font-size:.72rem;color:var(--text-muted);margin-top:.35rem">El correo no puede ser modificado.</div>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Guardar Cambios
            </button>
          </form>
        </div>

        <!-- Change password -->
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem">
          <h3 style="font-size:1.05rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:1px solid var(--border)">
            <i class="fas fa-lock" style="color:var(--secondary)"></i> Cambiar Contraseña
          </h3>
          <form method="POST">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
              <label class="form-label">Contraseña Actual</label>
              <div class="input-password-toggle">
                <input type="password" name="contrasena_actual" class="form-control" id="pwdOld" required>
                <button type="button" class="toggle-btn" data-target="pwdOld"><i class="fas fa-eye"></i></button>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
              <div class="form-group">
                <label class="form-label">Nueva Contraseña</label>
                <div class="input-password-toggle">
                  <input type="password" name="nueva_contrasena" class="form-control" id="pwdNew" required minlength="8" placeholder="Mín. 8 caracteres">
                  <button type="button" class="toggle-btn" data-target="pwdNew"><i class="fas fa-eye"></i></button>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Confirmar Nueva</label>
                <div class="input-password-toggle">
                  <input type="password" name="confirmar_contrasena" class="form-control" id="pwdConf" required placeholder="Repite la contraseña">
                  <button type="button" class="toggle-btn" data-target="pwdConf"><i class="fas fa-eye"></i></button>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-secondary">
              <i class="fas fa-key"></i> Cambiar Contraseña
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
