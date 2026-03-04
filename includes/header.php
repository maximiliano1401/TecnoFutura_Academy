<?php
if (!isset($config_loaded)) {
    require_once __DIR__ . '/config.php';
}
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$is_logged = Usuario::estaAutenticado();
$user_name = $_SESSION['usuario_nombre'] ?? '';
$user_role = $_SESSION['usuario_rol'] ?? '';
$user_id   = $_SESSION['usuario_id'] ?? 0;
$initials = $user_name ? strtoupper(mb_substr($user_name, 0, 1)) : '?';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= SITE_DESCRIPTION ?>">
<title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' . SITE_NAME : SITE_NAME ?></title>

<!-- Fonts & Icons -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="<?= CSS_PATH ?>/styles.css">

<?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="mainNavbar">
  <div class="navbar-inner">
    <!-- Brand -->
    <a class="navbar-brand" href="<?= SITE_URL ?>">
      <div class="brand-icon"><i class="fas fa-microchip"></i></div>
      Tecno<span class="accent">Futura</span>
    </a>

    <!-- Desktop Nav -->
    <div class="navbar-nav" id="desktopNav">
      <a href="<?= SITE_URL ?>" class="<?= $current_page === 'index' ? 'active' : '' ?>">Inicio</a>
      <a href="<?= SITE_URL ?>/cursos" class="<?= in_array($current_page, ['index','detalle']) && strpos($_SERVER['REQUEST_URI'],'/cursos') !== false ? 'active' : '' ?>">Cursos</a>
      <?php if (!$is_logged): ?>
        <a href="<?= SITE_URL ?>/#precios">Precios</a>
      <?php endif; ?>
      <a href="<?= SITE_URL ?>/#nosotros">Nosotros</a>
    </div>

    <!-- Actions -->
    <div class="navbar-actions">
      <?php if ($is_logged): ?>
        <?php if ($user_role === 'ADMIN'): ?>
          <a href="<?= SITE_URL ?>/admin" class="btn btn-sm btn-secondary"><i class="fas fa-shield-alt"></i> Admin</a>
        <?php elseif ($user_role === 'PROFESOR'): ?>
          <a href="<?= SITE_URL ?>/profesor" class="btn btn-sm btn-secondary"><i class="fas fa-chalkboard-teacher"></i> Portal</a>
        <?php endif; ?>

        <div class="navbar-user-menu">
          <button class="navbar-user-btn" id="userMenuBtn">
            <div class="user-avatar"><?= $initials ?></div>
            <span style="font-size:.85rem;font-weight:600;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars(explode(' ', $user_name)[0]) ?></span>
            <i class="fas fa-chevron-down" style="font-size:.65rem;color:var(--text-muted)"></i>
          </button>
          <div class="dropdown-menu" id="userDropdown">
            <?php if ($user_role === 'USUARIO'): ?>
              <a href="<?= SITE_URL ?>/lms"><i class="fas fa-graduation-cap"></i> Mis Cursos</a>
            <?php elseif ($user_role === 'PROFESOR'): ?>
              <a href="<?= SITE_URL ?>/profesor"><i class="fas fa-chalkboard-teacher"></i> Mi Portal</a>
            <?php endif; ?>
            <a href="<?= SITE_URL ?>/perfil"><i class="fas fa-user-circle"></i> Mi Perfil</a>
            <?php if ($user_role === 'USUARIO'): ?>
              <a href="<?= SITE_URL ?>/lms/certificados.php"><i class="fas fa-certificate"></i> Certificados</a>
            <?php endif; ?>
            <div class="dropdown-divider"></div>
            <form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php">
              <button type="submit" class="dropdown-item text-danger-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
            </form>
          </div>
        </div>

      <?php else: ?>
        <a href="<?= SITE_URL ?>/login.php" class="btn btn-ghost btn-sm">Iniciar Sesión</a>
        <a href="<?= SITE_URL ?>/register.php" class="btn btn-primary btn-sm">Registrarse</a>
      <?php endif; ?>

      <button class="navbar-mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
  <a href="<?= SITE_URL ?>">Inicio</a>
  <a href="<?= SITE_URL ?>/cursos">Cursos</a>
  <a href="<?= SITE_URL ?>/#precios">Precios</a>
  <a href="<?= SITE_URL ?>/#nosotros">Nosotros</a>
  <?php if ($is_logged): ?>
    <div class="separator"></div>
    <a href="<?= $user_role === 'ADMIN' ? SITE_URL.'/admin' : ($user_role === 'PROFESOR' ? SITE_URL.'/profesor' : SITE_URL.'/lms') ?>">
      <?= $user_role === 'ADMIN' ? 'Panel Admin' : ($user_role === 'PROFESOR' ? 'Portal Docente' : 'Mis Cursos') ?>
    </a>
    <a href="<?= SITE_URL ?>/perfil">Mi Perfil</a>
    <form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="margin:0">
      <button type="submit" class="btn btn-ghost btn-sm btn-block" style="margin-top:.5rem">Cerrar Sesión</button>
    </form>
  <?php else: ?>
    <div class="separator"></div>
    <a href="<?= SITE_URL ?>/login.php">Iniciar Sesión</a>
    <a href="<?= SITE_URL ?>/register.php">Registrarse</a>
  <?php endif; ?>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<?php if (isset($_SESSION['flash_message'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  showToast(<?= json_encode($_SESSION['flash_message']['text']) ?>, <?= json_encode($_SESSION['flash_message']['type']) ?>);
});
</script>
<?php unset($_SESSION['flash_message']); endif; ?>
