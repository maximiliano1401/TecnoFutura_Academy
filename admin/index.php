<?php
$page_title = 'Panel de Administración';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Pago.php';

requiereRol(['ADMIN']);

$db = Database::getInstance()->getConnection();

// Stats
function dbCount($db, $sql, $params=[]) {
    $s = $db->prepare($sql);
    $s->execute($params);
    return (int)$s->fetchColumn();
}

$total_users    = dbCount($db, "SELECT COUNT(*) FROM usuarios");
$total_alumnos  = dbCount($db, "SELECT COUNT(*) FROM alumnos");
$total_docentes = dbCount($db, "SELECT COUNT(*) FROM docentes");
$total_cursos   = dbCount($db, "SELECT COUNT(*) FROM cursos WHERE activo=1");
$total_inscrip  = dbCount($db, "SELECT COUNT(*) FROM inscripciones");
$total_certs    = dbCount($db, "SELECT COUNT(*) FROM certificados");

$pago_obj  = new Pago();
$pago_stats = $pago_obj->estadisticas();
$ingresos   = number_format($pago_stats['ingresos_totales'] ?? 0, 2);
$ingresos_mes = number_format($pago_stats['ingresos_mes'] ?? 0, 2);

// Recent users
$recent_users = $db->query("SELECT u.*, r.nombre_rol FROM usuarios u 
    JOIN roles r ON u.id_rol = r.id_rol 
    ORDER BY u.fecha_registro DESC LIMIT 8")->fetchAll();

// Recent payments
$recent_pagos = $db->query("SELECT p.*, c.nombre_curso, u2.nombre_completo
    FROM pagos p
    JOIN inscripciones i ON p.id_inscripcion = i.id_inscripcion
    JOIN cursos c ON i.id_curso = c.id_curso
    JOIN alumnos a ON i.id_alumno = a.id_alumno
    JOIN usuarios u2 ON a.id_usuario = u2.id_usuario
    ORDER BY p.fecha_pago DESC LIMIT 6")->fetchAll();

// Courses by enrollment
$top_cursos = $db->query("SELECT c.nombre_curso, c.nivel, c.precio,
    COUNT(i.id_inscripcion) AS total
    FROM cursos c
    LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
    WHERE c.activo = 1
    GROUP BY c.id_curso
    ORDER BY total DESC LIMIT 5")->fetchAll();

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - var(--navbar-height))">

<!-- ADMIN SIDEBAR -->
<aside class="admin-sidebar">
  <div class="admin-sidebar-header">
    <i class="fas fa-shield-alt"></i>
    <span>Admin Panel</span>
  </div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin" class="admin-nav-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/usuarios.php" class="admin-nav-item"><i class="fas fa-users"></i> Usuarios</a>
    <a href="<?= SITE_URL ?>/admin/cursos.php" class="admin-nav-item"><i class="fas fa-book-open"></i> Cursos</a>
    <a href="<?= SITE_URL ?>/admin/pagos.php" class="admin-nav-item"><i class="fas fa-credit-card"></i> Pagos</a>
    <a href="<?= SITE_URL ?>/admin/certificados.php" class="admin-nav-item"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="<?= SITE_URL ?>/admin/reportes.php" class="admin-nav-item"><i class="fas fa-chart-bar"></i> Reportes</a>
    <div style="padding:.75rem 1.25rem;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.75rem">Herramientas</div>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>

<!-- MAIN -->
<div class="admin-main">
  <div class="admin-header">
    <div>
      <h1 class="admin-title">Dashboard</h1>
      <p class="admin-subtitle">Bienvenido al panel de administración de TecnoFutura Academy.</p>
    </div>
    <div style="display:flex;gap:.75rem">
      <span style="font-size:.78rem;color:var(--text-muted);background:var(--bg-card);padding:.5rem .9rem;border-radius:var(--radius);border:1px solid var(--border)">
        <i class="fas fa-clock" style="color:var(--primary)"></i> <?= date('d/m/Y H:i') ?>
      </span>
    </div>
  </div>

  <!-- Stats cards -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-bottom:2.5rem">
    <?php foreach([
      ['fas fa-users','icon-blue',$total_users,'Usuarios Totales','todos'],
      ['fas fa-graduation-cap','icon-purple',$total_alumnos,'Alumnos','alumnos'],
      ['fas fa-book-open','icon-green',$total_cursos,'Cursos Activos','cursos'],
      ['fas fa-peso-sign','icon-amber','$'.$ingresos,'Ingresos Totales','pagos'],
    ] as [$ico,$cls,$val,$lbl,$link]): ?>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
        <div class="stat-icon <?= $cls ?>"><i class="<?= $ico ?>"></i></div>
        <a href="<?= SITE_URL ?>/admin/<?= $link === 'todos' ? '' : $link.'.php' ?>" style="font-size:.7rem;color:var(--primary)">Ver todos <i class="fas fa-arrow-right"></i></a>
      </div>
      <div style="font-size:1.8rem;font-weight:800;margin-bottom:.2rem"><?= $val ?></div>
      <div style="font-size:.78rem;color:var(--text-muted)"><?= $lbl ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Second row stats -->
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:2.5rem">
    <?php foreach([
      ['fas fa-file-alt','icon-blue',$total_inscrip,'Inscripciones'],
      ['fas fa-certificate','icon-amber',$total_certs,'Certificados Emitidos'],
      ['fas fa-chart-line','icon-green','$'.$ingresos_mes,'Ingresos este mes'],
    ] as [$ico,$cls,$val,$lbl]): ?>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;display:flex;align-items:center;gap:1rem">
      <div class="stat-icon <?= $cls ?>" style="width:44px;height:44px;font-size:.95rem"><i class="<?= $ico ?>"></i></div>
      <div>
        <div style="font-size:1.4rem;font-weight:800"><?= $val ?></div>
        <div style="font-size:.75rem;color:var(--text-muted)"><?= $lbl ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Tables row -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2rem">
    <!-- Top courses -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
        <h3 style="font-size:.95rem;font-weight:600">Cursos Populares</h3>
        <a href="<?= SITE_URL ?>/admin/cursos.php" style="font-size:.75rem;color:var(--primary)">Ver todos</a>
      </div>
      <table class="data-table">
        <thead><tr><th>Curso</th><th>Nivel</th><th>Alumnos</th><th>Precio</th></tr></thead>
        <tbody>
          <?php foreach ($top_cursos as $tc): ?>
          <tr>
            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($tc['nombre_curso']) ?></td>
            <td><span class="badge badge-<?= strtolower(str_replace('á','a',$tc['nivel'])) ?>"><?= $tc['nivel'] ?></span></td>
            <td><strong><?= $tc['total'] ?></strong></td>
            <td><?= $tc['precio'] > 0 ? '$'.number_format($tc['precio'],2) : '<span style="color:var(--success)">Gratis</span>' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Recent payments -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
        <h3 style="font-size:.95rem;font-weight:600">Pagos Recientes</h3>
        <a href="<?= SITE_URL ?>/admin/pagos.php" style="font-size:.75rem;color:var(--primary)">Ver todos</a>
      </div>
      <table class="data-table">
        <thead><tr><th>Alumno</th><th>Monto</th><th>Método</th><th>Fecha</th></tr></thead>
        <tbody>
          <?php foreach ($recent_pagos as $p): ?>
          <tr>
            <td style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($p['nombre_completo']) ?></td>
            <td style="color:var(--success);font-weight:700">$<?= number_format($p['monto'],2) ?></td>
            <td><span class="badge"><?= htmlspecialchars($p['metodo_pago']) ?></span></td>
            <td style="font-size:.75rem;color:var(--text-muted)"><?= date('d/m H:i', strtotime($p['fecha_pago'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent users table -->
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <h3 style="font-size:.95rem;font-weight:600">Usuarios Recientes</h3>
      <a href="<?= SITE_URL ?>/admin/usuarios.php" style="font-size:.75rem;color:var(--primary)">Administrar usuarios</a>
    </div>
    <table class="data-table">
      <thead><tr><th>Usuario</th><th>Correo</th><th>Rol</th><th>Fecha Registro</th><th>Estado</th></tr></thead>
      <tbody>
        <?php foreach ($recent_users as $u): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:.65rem">
              <div style="width:30px;height:30px;background:var(--grad-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.7rem;flex-shrink:0">
                <?= strtoupper(substr($u['nombre_completo'],0,1)) ?>
              </div>
              <span style="font-weight:500;font-size:.875rem"><?= htmlspecialchars($u['nombre_completo']) ?></span>
            </div>
          </td>
          <td style="font-size:.8rem;color:var(--text-muted)"><?= htmlspecialchars($u['correo_electronico']) ?></td>
          <td>
            <span class="badge badge-<?= $u['nombre_rol'] === 'ADMIN' ? 'danger' : ($u['nombre_rol'] === 'PROFESOR' ? 'info' : 'success') ?>" style="font-size:.7rem">
              <?= $u['nombre_rol'] ?>
            </span>
          </td>
          <td style="font-size:.8rem;color:var(--text-muted)"><?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></td>
          <td>
            <span class="badge <?= $u['activo'] ? 'badge-success' : 'badge-warning' ?>" style="font-size:.7rem">
              <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
