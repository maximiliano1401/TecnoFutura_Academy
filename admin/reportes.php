<?php
$page_title = 'Reportes';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
requiereRol(['ADMIN']);

$db = Database::getInstance()->getConnection();

// Monthly revenue
$monthly = $db->query("SELECT DATE_FORMAT(fecha_pago,'%Y-%m') AS mes, SUM(monto) AS ingresos, COUNT(*) AS pagos
    FROM pagos WHERE estado_pago='completado' AND fecha_pago >= DATE_SUB(NOW(),INTERVAL 6 MONTH)
    GROUP BY mes ORDER BY mes ASC")->fetchAll();

// Enrollments by course
$by_course = $db->query("SELECT c.nombre_curso, COUNT(i.id_inscripcion) AS total,
    SUM(CASE WHEN i.estado = 'Finalizado' OR i.estado = 'Certificado' THEN 1 ELSE 0 END) AS finalizados
    FROM inscripciones i JOIN cursos c ON c.id_curso = i.id_curso
    GROUP BY i.id_curso ORDER BY total DESC")->fetchAll();

// Students by level
$by_level = $db->query("SELECT c.nivel, COUNT(i.id_inscripcion) AS total
    FROM inscripciones i JOIN cursos c ON c.id_curso = i.id_curso
    GROUP BY c.nivel ORDER BY total DESC")->fetchAll();

include_once __DIR__ . '/../includes/header.php';
?>
<div style="margin-top:var(--navbar-height);display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - var(--navbar-height))">
<aside class="admin-sidebar">
  <div class="admin-sidebar-header"><i class="fas fa-shield-alt"></i><span>Admin Panel</span></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/usuarios.php" class="admin-nav-item"><i class="fas fa-users"></i> Usuarios</a>
    <a href="<?= SITE_URL ?>/admin/cursos.php" class="admin-nav-item"><i class="fas fa-book-open"></i> Cursos</a>
    <a href="<?= SITE_URL ?>/admin/pagos.php" class="admin-nav-item"><i class="fas fa-credit-card"></i> Pagos</a>
    <a href="<?= SITE_URL ?>/admin/certificados.php" class="admin-nav-item"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="<?= SITE_URL ?>/admin/reportes.php" class="admin-nav-item active"><i class="fas fa-chart-bar"></i> Reportes</a>
    <a href="<?= SITE_URL ?>" class="admin-nav-item"><i class="fas fa-globe"></i> Ver Sitio</a>
    <div style="margin-top:auto;padding:.75rem 0 0;border-top:1px solid var(--border);margin:1.5rem 0 0"><form method="POST" action="<?= SITE_URL ?>/backend/auth/logout.php" style="padding:0 .25rem"><button type="submit" class="admin-nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger);text-align:left"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button></form></div>
  </nav>
</aside>
<div class="admin-main">
  <div class="admin-header">
    <div><h1 class="admin-title">Reportes</h1><p class="admin-subtitle">Análisis de rendimiento de la plataforma.</p></div>
  </div>
  
  <!-- Monthly revenue table -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2rem">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border)"><h3 style="font-size:.95rem;font-weight:600">Ingresos por Mes (últimos 6 meses)</h3></div>
      <?php if (empty($monthly)): ?>
      <div style="padding:2rem;text-align:center;color:var(--text-muted)">No hay datos de ingresos aún.</div>
      <?php else: ?>
      <?php $max = max(array_column($monthly, 'ingresos') ?: [1]); ?>
      <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
        <?php foreach ($monthly as $m): ?>
        <div>
          <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;font-size:.8rem">
            <span><?= $m['mes'] ?></span>
            <span style="font-weight:700;color:var(--success)">$<?= number_format($m['ingresos'],2) ?> (<?= $m['pagos'] ?> pagos)</span>
          </div>
          <div style="height:8px;background:var(--bg-base);border-radius:4px;overflow:hidden">
            <div style="width:<?= round($m['ingresos']/$max*100) ?>%;height:100%;background:var(--grad-primary);border-radius:4px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
      <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border)"><h3 style="font-size:.95rem;font-weight:600">Inscripciones por Nivel</h3></div>
      <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
        <?php 
        $total_level = array_sum(array_column($by_level, 'total')) ?: 1;
        foreach ($by_level as $l):
          $pct = round($l['total']/$total_level*100);
          $colors = ['Básico'=>'var(--success)','Intermedio'=>'var(--warning)','Avanzado'=>'var(--danger)'];
          $col = $colors[$l['nivel']] ?? 'var(--primary)';
        ?>
        <div>
          <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;font-size:.8rem">
            <span><?= $l['nivel'] ?></span><span style="font-weight:700"><?= $l['total'] ?> (<?= $pct ?>%)</span>
          </div>
          <div style="height:8px;background:var(--bg-base);border-radius:4px;overflow:hidden">
            <div style="width:<?= $pct ?>%;height:100%;background:<?= $col ?>;border-radius:4px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- By course table -->
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border)"><h3 style="font-size:.95rem;font-weight:600">Tasa de Finalización por Curso</h3></div>
    <table class="data-table">
      <thead><tr><th>Curso</th><th>Inscritos</th><th>Finalizados</th><th>Tasa</th><th>Progreso</th></tr></thead>
      <tbody>
        <?php foreach ($by_course as $bc):
          $tasa = $bc['total'] > 0 ? round($bc['finalizados']/$bc['total']*100) : 0;
        ?>
        <tr>
          <td style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($bc['nombre_curso']) ?></td>
          <td><?= $bc['total'] ?></td>
          <td><?= $bc['finalizados'] ?></td>
          <td style="font-weight:700;color:<?= $tasa >= 70 ? 'var(--success)' : ($tasa >= 40 ? 'var(--warning)' : 'var(--danger)') ?>"><?= $tasa ?>%</td>
          <td style="width:120px">
            <div class="progress progress-sm"><div class="progress-bar" style="width:<?= $tasa ?>%"></div></div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
