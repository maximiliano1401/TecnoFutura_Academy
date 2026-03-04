<?php
$page_title = 'Crear Cuenta';
require_once __DIR__ . '/includes/config.php';
if (Usuario::estaAutenticado()) {
    header("Location: " . SITE_URL . "/lms"); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $page_title ?> — <?= SITE_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= CSS_PATH ?>/styles.css">
</head>
<body>
<div id="toast-container"></div>

<div class="auth-wrapper">
  <!-- Sidebar -->
  <div class="auth-sidebar">
    <div style="position:relative;z-index:1">
      <a href="<?= SITE_URL ?>" style="display:flex;align-items:center;gap:.75rem;text-decoration:none;margin-bottom:3rem">
        <div style="width:42px;height:42px;background:var(--grad-primary);border-radius:var(--radius);display:flex;align-items:center;justify-content:center">
          <i class="fas fa-microchip" style="color:#fff;font-size:1.1rem"></i>
        </div>
        <span style="font-family:var(--font-heading);font-size:1.3rem;font-weight:700;color:var(--text-primary)">TecnoFutura</span>
      </a>

      <div style="margin-bottom:2.5rem">
        <div class="section-eyebrow" style="display:inline-block;margin-bottom:1rem">Únete a nuestra comunidad</div>
        <h2 style="font-size:2rem;font-weight:800;line-height:1.15;margin-bottom:1rem">
          Empieza a aprender <span class="gradient-text">hoy mismo</span>
        </h2>
        <p style="color:var(--text-secondary);line-height:1.7;font-size:.9rem">Crea tu cuenta gratis y accede inmediatamente al catálogo de cursos. Tu primer curso de introducción es completamente gratis.</p>
      </div>

      <!-- Benefit list -->
      <div style="display:flex;flex-direction:column;gap:.75rem">
        <?php foreach([
          ['fas fa-gift','Acceso inmediato a curso gratis','green'],
          ['fas fa-play-circle','Videos HD con subtítulos','blue'],
          ['fas fa-certificate','Certificados descargables','amber'],
          ['fas fa-infinity','Acceso de por vida al contenido','purple'],
          ['fas fa-headset','Soporte de la comunidad','blue'],
        ] as [$ico,$txt,$col]): ?>
        <div style="display:flex;align-items:center;gap:.75rem;font-size:.82rem;color:var(--text-secondary)">
          <i class="<?= $ico ?>" style="color:var(--<?= $col === 'amber' ? 'warning' : ($col === 'green' ? 'success' : ($col === 'purple' ? 'secondary' : 'primary')) ?>);width:16px"></i>
          <?= $txt ?>
        </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:2rem;padding:1.25rem;background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.2);border-radius:var(--radius);font-size:.82rem;color:var(--text-secondary)">
        <i class="fas fa-shield-alt" style="color:var(--primary);margin-right:.4rem"></i>
        Tus datos están protegidos. No hacemos spam. Cancela cuando quieras.
      </div>
    </div>
  </div>

  <!-- Form -->
  <div class="auth-form-side" style="align-items:flex-start;padding-top:2rem;overflow-y:auto">
    <div class="auth-form-container" style="max-width:480px;padding:1rem 0">
      <a class="auth-logo" href="<?= SITE_URL ?>">
        <div style="width:36px;height:36px;background:var(--grad-primary);border-radius:8px;display:flex;align-items:center;justify-content:center">
          <i class="fas fa-microchip" style="color:#fff"></i>
        </div>
        <span style="font-family:var(--font-heading);font-weight:700;color:var(--text-primary)">TecnoFutura</span>
      </a>

      <h1 class="auth-form-title">Crear Cuenta</h1>
      <p class="auth-form-subtitle">Completa el formulario para registrarte</p>

      <!-- Role tabs -->
      <div class="tab-group" data-tab-group="register">
        <button class="tab-btn active" data-tab="tab-alumno">
          <i class="fas fa-graduation-cap"></i> Alumno
        </button>
        <button class="tab-btn" data-tab="tab-docente">
          <i class="fas fa-chalkboard-teacher"></i> Docente
        </button>
      </div>

      <div id="regAlert" style="display:none" class="alert alert-danger">
        <i class="fas fa-exclamation-circle alert-icon"></i>
        <span id="regAlertMsg"></span>
      </div>
      <div id="regSuccess" style="display:none" class="alert alert-success">
        <i class="fas fa-check-circle alert-icon"></i>
        <span id="regSuccessMsg"></span>
      </div>

      <!-- Alumno Form -->
      <div id="tab-alumno" class="tab-content">
        <form id="formAlumno" novalidate>
          <input type="hidden" name="tipo_usuario" value="alumno">
          <div class="form-group">
            <label class="form-label">Nombre Completo <span class="required">*</span></label>
            <div class="form-control-icon">
              <i class="fas fa-user icon"></i>
              <input type="text" class="form-control" name="nombre_completo" placeholder="Tu nombre completo" required minlength="3">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Correo Electrónico <span class="required">*</span></label>
            <div class="form-control-icon">
              <i class="fas fa-envelope icon"></i>
              <input type="email" class="form-control" name="correo_electronico" placeholder="tu@email.com" required>
            </div>
          </div>
          <div class="grid-2" style="gap:.75rem">
            <div class="form-group">
              <label class="form-label">Fecha de Nacimiento <span class="required">*</span></label>
              <input type="date" class="form-control" name="fecha_nacimiento" required>
            </div>
            <div class="form-group">
              <label class="form-label">País de Origen</label>
              <select class="form-control" name="pais">
                <option value="México">México</option>
                <option value="Colombia">Colombia</option>
                <option value="Argentina">Argentina</option>
                <option value="España">España</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
          </div>
          <div class="grid-2" style="gap:.75rem">
            <div class="form-group">
              <label class="form-label">Contraseña <span class="required">*</span></label>
              <div class="input-password-toggle">
                <input type="password" class="form-control" name="contrasena" id="pwdA1" placeholder="Mín. 8 caracteres" required minlength="8">
                <button type="button" class="toggle-btn" data-target="pwdA1"><i class="fas fa-eye"></i></button>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Confirmar Contraseña <span class="required">*</span></label>
              <div class="input-password-toggle">
                <input type="password" class="form-control" name="confirmar_contrasena" id="pwdA2" placeholder="Repite la contraseña" required>
                <button type="button" class="toggle-btn" data-target="pwdA2"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <!-- Password strength -->
          <div style="margin-top:-.75rem;margin-bottom:1rem">
            <div class="progress progress-sm" style="margin-bottom:.35rem"><div class="progress-bar" id="pwdStrengthBar" style="width:0%"></div></div>
            <span id="pwdStrengthText" style="font-size:.7rem;color:var(--text-muted)">Ingresa una contraseña</span>
          </div>
          <div class="form-check" style="margin-bottom:1.5rem">
            <input type="checkbox" id="terms" name="terms" required>
            <label for="terms">Acepto los <a href="/terminos" target="_blank">Términos de Servicio</a> y la <a href="/privacidad" target="_blank">Política de Privacidad</a></label>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">
            <i class="fas fa-user-plus"></i> Crear Cuenta Gratis
          </button>
        </form>
      </div>

      <!-- Docente Form -->
      <div id="tab-docente" class="tab-content d-none">
        <form id="formDocente" novalidate>
          <input type="hidden" name="tipo_usuario" value="docente">
          <div class="form-group">
            <label class="form-label">Nombre Completo <span class="required">*</span></label>
            <div class="form-control-icon">
              <i class="fas fa-user icon"></i>
              <input type="text" class="form-control" name="nombre_completo" placeholder="Nombre completo profesional" required minlength="3">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Correo Electrónico <span class="required">*</span></label>
            <div class="form-control-icon">
              <i class="fas fa-envelope icon"></i>
              <input type="email" class="form-control" name="correo_electronico" placeholder="docente@email.com" required>
            </div>
          </div>
          <div class="grid-2" style="gap:.75rem">
            <div class="form-group">
              <label class="form-label">Fecha de Nacimiento <span class="required">*</span></label>
              <input type="date" class="form-control" name="fecha_nacimiento" required>
            </div>
            <div class="form-group">
              <label class="form-label">Cédula Profesional <span class="required">*</span></label>
              <input type="text" class="form-control" name="cedula_profesional" placeholder="Ej. 12345678" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Institución / Organización</label>
            <input type="text" class="form-control" name="institucion_procedencia" placeholder="Universidad, Empresa, etc.">
          </div>
          <div class="form-group">
            <label class="form-label">Especialidad</label>
            <select class="form-control" name="especialidad">
              <option value="Arduino y Electrónica">Arduino y Electrónica</option>
              <option value="Lenguaje Ensamblador">Lenguaje Ensamblador</option>
              <option value="Sistemas Embebidos">Sistemas Embebidos</option>
              <option value="Arquitectura de Computadoras">Arquitectura de Computadoras</option>
              <option value="IoT">IoT</option>
              <option value="Otra">Otra</option>
            </select>
          </div>
          <div class="grid-2" style="gap:.75rem">
            <div class="form-group">
              <label class="form-label">Contraseña <span class="required">*</span></label>
              <div class="input-password-toggle">
                <input type="password" class="form-control" name="contrasena" id="pwdD1" placeholder="Mín. 8 caracteres" required minlength="8">
                <button type="button" class="toggle-btn" data-target="pwdD1"><i class="fas fa-eye"></i></button>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Confirmar Contraseña <span class="required">*</span></label>
              <div class="input-password-toggle">
                <input type="password" class="form-control" name="confirmar_contrasena" id="pwdD2" placeholder="Repite la contraseña" required>
                <button type="button" class="toggle-btn" data-target="pwdD2"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="form-check" style="margin-bottom:1.5rem">
            <input type="checkbox" id="termsD" name="terms" required>
            <label for="termsD">Acepto los <a href="/terminos">Términos</a> y confirmo que mis datos son verídicos</label>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">
            <i class="fas fa-chalkboard-teacher"></i> Solicitar Registro como Docente
          </button>
        </form>
      </div>

      <p style="text-align:center;margin-top:1.75rem;font-size:.875rem;color:var(--text-muted)">
        ¿Ya tienes cuenta?
        <a href="<?= SITE_URL ?>/login.php" style="color:var(--primary);font-weight:600">Iniciar Sesión</a>
      </p>
    </div>
  </div>
</div>

<script src="<?= JS_PATH ?>/main.js"></script>
<script>
// Password strength
document.querySelectorAll('input[type=password][name=contrasena]').forEach(inp => {
  inp.addEventListener('input', () => {
    const v = inp.value;
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const bar = document.getElementById('pwdStrengthBar');
    const txt = document.getElementById('pwdStrengthText');
    if (!bar) return;
    const levels = [
      [0,'0%','var(--danger)','Muy débil'],
      [25,'25%','var(--danger)','Débil'],
      [50,'50%','var(--warning)','Regular'],
      [75,'75%','var(--info)','Fuerte'],
      [100,'100%','var(--success)','Muy fuerte'],
    ];
    const [,w,c,t] = levels[score];
    bar.style.width = w; bar.style.background = c;
    if (txt) { txt.textContent = t; txt.style.color = c; }
  });
});

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.dataset.tab;
    btn.closest('[data-tab-group]').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('d-none'));
    btn.classList.add('active');
    document.getElementById(target)?.classList.remove('d-none');
  });
});

// Handle form submission
async function handleRegister(form) {
  const alertEl = document.getElementById('regAlert');
  const alertMsg = document.getElementById('regAlertMsg');
  const successEl = document.getElementById('regSuccess');
  const successMsg = document.getElementById('regSuccessMsg');
  const submitBtn = form.querySelector('[type=submit]');

  // Validate passwords match
  const pwd = form.querySelector('[name=contrasena]').value;
  const cpwd = form.querySelector('[name=confirmar_contrasena]').value;
  if (pwd !== cpwd) {
    alertEl.style.display = 'flex'; alertMsg.textContent = 'Las contraseñas no coinciden.'; return;
  }

  // Terms check
  const terms = form.querySelector('[name=terms]');
  if (terms && !terms.checked) {
    alertEl.style.display = 'flex'; alertMsg.textContent = 'Debes aceptar los términos de servicio.'; return;
  }

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner"></span> Creando cuenta...';
  alertEl.style.display = 'none'; successEl.style.display = 'none';

  try {
    const fd = new FormData(form);
    const res = await fetch('<?= SITE_URL ?>/backend/auth/register.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      successEl.style.display = 'flex';
      successMsg.textContent = data.message + ' Redirigiendo al login...';
      submitBtn.innerHTML = '<i class="fas fa-check"></i> ¡Cuenta creada!';
      setTimeout(() => window.location.href = '<?= SITE_URL ?>/login.php', 2500);
    } else {
      alertEl.style.display = 'flex'; alertMsg.textContent = data.message || 'Error al crear la cuenta.';
      submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Crear Cuenta Gratis';
    }
  } catch { alertEl.style.display = 'flex'; alertMsg.textContent = 'Error de conexión.'; submitBtn.disabled = false; }
}

document.getElementById('formAlumno').addEventListener('submit', (e) => { e.preventDefault(); handleRegister(e.target); });
document.getElementById('formDocente').addEventListener('submit', (e) => { e.preventDefault(); handleRegister(e.target); });
</script>
</body>
</html>
