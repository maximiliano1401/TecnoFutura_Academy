<?php
/**
 * Sidebar unificado para el Portal del Profesor
 * Uso: include con variable $active_page definida
 * Ejemplo: $active_page = 'cursos'; include 'includes/profesor_sidebar.php';
 */

// Si no se define $active_page, usar 'dashboard' por defecto
if (!isset($active_page)) {
    $active_page = 'dashboard';
}

$menu_items = [
    'dashboard' => ['url' => '/profesor', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
    'cursos' => ['url' => '/profesor/cursos.php', 'icon' => 'fa-book', 'label' => 'Mis Cursos'],
    'materiales' => ['url' => '/profesor/materiales.php', 'icon' => 'fa-film', 'label' => 'Materiales'],
    'actividades' => ['url' => '/profesor/actividades.php', 'icon' => 'fa-clipboard-list', 'label' => 'Actividades'],
    'calificaciones' => ['url' => '/profesor/calificaciones.php', 'icon' => 'fa-graduation-cap', 'label' => 'Calificaciones'],
    'alumnos' => ['url' => '/profesor/alumnos.php', 'icon' => 'fa-users', 'label' => 'Mis Alumnos'],
];
?>
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-chalkboard-teacher"></i><span>Portal Docente</span></div>
  <nav class="admin-nav">
    <?php foreach ($menu_items as $key => $item): ?>
    <a href="<?= SITE_URL ?><?= $item['url'] ?>" class="admin-nav-item <?= $active_page === $key ? 'active' : '' ?>">
      <i class="fas <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
    </a>
    <?php endforeach; ?>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0">
      <form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem">
        <button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left">
          <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </button>
      </form>
    </div>
  </nav>
</aside>
