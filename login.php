<?php
$page_title = 'Iniciar Sesión';
require_once __DIR__ . '/includes/config.php';
if (Usuario::estaAutenticado()) {
    $rol = $_SESSION['usuario_rol'];
    $destino = $rol === 'ADMIN' ? '/admin' : ($rol === 'PROFESOR' ? '/profesor' : '/lms');
    header("Location: " . SITE_URL . $destino); exit;
}
$error = $_GET['error'] ?? '';
$redirect = $_GET['redirect'] ?? '';
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

      <div style="margin-bottom:3rem">
        <div class="section-eyebrow" style="display:inline-block;margin-bottom:1.25rem">Bienvenido de vuelta</div>
        <h2 style="font-size:2.2rem;font-weight:800;line-height:1.15;margin-bottom:1rem">
          Continúa tu <span class="gradient-text">aprendizaje</span>
        </h2>
        <p style="color:var(--text-secondary);line-height:1.7">Accede a tu panel, retoma donde lo dejaste y sigue acumulando habilidades que valen en el mercado laboral tecnológico.</p>
      </div>

      <!-- Feature highlights -->
      <div style="display:flex;flex-direction:column;gap:1rem">
        <?php foreach([
          ['fas fa-play-circle','Accede a todos tus cursos inscritos'],
          ['fas fa-certificate','Descarga tus certificados verificables'],
          ['fas fa-chart-line','Visualiza tu progreso en tiempo real'],
        ] as [$ico,$txt]): ?>
        <div style="display:flex;align-items:center;gap:.85rem;padding:1rem;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:var(--radius);font-size:.875rem;color:var(--text-secondary)">
          <div style="width:36px;height:36px;background:var(--grad-primary);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="<?= $ico ?>" style="color:#fff;font-size:.875rem"></i>
          </div>
          <?= $txt ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Form side -->
  <div class="auth-form-side">
    <div class="auth-form-container">
      <a class="auth-logo" href="<?= SITE_URL ?>">
        <div style="width:36px;height:36px;background:var(--grad-primary);border-radius:8px;display:flex;align-items:center;justify-content:center">
          <i class="fas fa-microchip" style="color:#fff"></i>
        </div>
        <span style="font-family:var(--font-heading);font-weight:700;color:var(--text-primary)">TecnoFutura</span>
      </a>

      <h1 class="auth-form-title">Iniciar Sesión</h1>
      <p class="auth-form-subtitle">Ingresa tus credenciales para acceder a tu cuenta</p>

      <?php if ($error === 'acceso_denegado'): ?>
      <div class="alert alert-danger"><i class="fas fa-exclamation-circle alert-icon"></i> No tienes permiso para acceder a esa sección.</div>
      <?php endif; ?>

      <div id="loginAlert" style="display:none" class="alert alert-danger">
        <i class="fas fa-exclamation-circle alert-icon"></i>
        <span id="loginAlertMsg"></span>
      </div>

      <form id="loginForm" novalidate>
        <?php if ($redirect): ?><input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>"><?php endif; ?>

        <div class="form-group">
          <label class="form-label">Correo Electrónico <span class="required">*</span></label>
          <div class="form-control-icon">
            <i class="fas fa-envelope icon"></i>
            <input type="email" class="form-control" name="correo" id="correo" placeholder="tu@email.com" required autocomplete="email">
          </div>
        </div>

        <div class="form-group">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem">
            <label class="form-label" style="margin:0">Contraseña <span class="required">*</span></label>
            <a href="#" style="font-size:.78rem;color:var(--text-muted)">¿Olvidaste tu contraseña?</a>
          </div>
          <div class="input-password-toggle">
            <input type="password" class="form-control" name="contrasena" id="pwdLogin" placeholder="Tu contraseña" required autocomplete="current-password">
            <button type="button" class="toggle-btn" data-target="pwdLogin"><i class="fas fa-eye"></i></button>
          </div>
        </div>

        <div class="form-check" style="margin-bottom:1.5rem">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Mantener sesión iniciada</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btnLogin">
          <span id="btnLoginText">Iniciar Sesión</span>
          <span id="btnLoginSpinner" style="display:none"><span class="spinner"></span></span>
        </button>

        <div class="auth-divider"><span>o</span></div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <!-- Demo accounts -->
          <button type="button" class="btn btn-ghost btn-sm" onclick="fillDemo('admin@tecnofutura.academy','admin123')">
            <i class="fas fa-shield-alt" style="color:var(--danger)"></i> Demo Admin
          </button>
          <button type="button" class="btn btn-ghost btn-sm" onclick="fillDemo('alumno@demo.com','alumno123')">
            <i class="fas fa-graduation-cap" style="color:var(--primary)"></i> Demo Alumno
          </button>
        </div>
      </form>

      <p style="text-align:center;margin-top:2rem;font-size:.875rem;color:var(--text-muted)">
        ¿No tienes cuenta?
        <a href="<?= SITE_URL ?>/register.php" style="color:var(--primary);font-weight:600">Regístrate aquí</a>
      </p>
    </div>
  </div>
</div>

<script src="<?= JS_PATH ?>/main.js"></script>
<script>
function fillDemo(email, pwd) {
  document.getElementById('correo').value = email;
  document.getElementById('pwdLogin').value = pwd;
}

document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = document.getElementById('btnLogin');
  const txt = document.getElementById('btnLoginText');
  const spinner = document.getElementById('btnLoginSpinner');
  const alert = document.getElementById('loginAlert');
  const alertMsg = document.getElementById('loginAlertMsg');
  
  btn.disabled = true; txt.style.display = 'none'; spinner.style.display = '';

  try {
    const fd = new FormData(e.target);
    const res = await fetch('<?= SITE_URL ?>/backend/auth/login.php', { method: 'POST', body: fd });
    const data = await res.json();
    
    if (data.success) {
      txt.innerHTML = '<i class="fas fa-check"></i> ¡Bienvenido!';
      txt.style.display = ''; spinner.style.display = 'none';
      const redirect = fd.get('redirect');
      const role = data.usuario?.rol;
      let dest = redirect || (role === 'ADMIN' ? '/admin' : role === 'PROFESOR' ? '/profesor' : '/lms');
      if (!dest.startsWith('/') && !dest.startsWith('http')) dest = '/' + dest;
      setTimeout(() => window.location.href = '<?= SITE_URL ?>' + (dest.startsWith('/') ? dest : '/' + dest), 600);
    } else {
      alert.style.display = 'flex';
      alertMsg.textContent = data.message || 'Credenciales incorrectas';
      btn.disabled = false; txt.style.display = ''; spinner.style.display = 'none';
    }
  } catch(err) {
    alert.style.display = 'flex';
    alertMsg.textContent = 'Error de conexión. Intenta de nuevo.';
    btn.disabled = false; txt.style.display = ''; spinner.style.display = 'none';
  }
});
</script>
</body>
</html>
