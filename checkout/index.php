<?php
$page_title = 'Checkout';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/auth/middleware.php';
require_once __DIR__ . '/../backend/classes/Curso.php';

requiereAutenticacion();

$id_curso = intval($_GET['id'] ?? 0);
if (!$id_curso) { header('Location: ' . SITE_URL . '/cursos'); exit; }

$obj   = new Curso();
$curso = $obj->porId($id_curso);
if (!$curso || $curso['precio'] == 0) {
    // Free course → direct enroll
    header('Location: ' . SITE_URL . '/lms/inscribir.php?id=' . $id_curso);
    exit;
}

// Check already enrolled
$insc = $obj->inscripcionAlumno($id_curso, $_SESSION['usuario_rol_id'] ?? 0);
if ($insc && $insc['estado'] !== 'pendiente_pago') {
    header('Location: ' . SITE_URL . '/lms/curso.php?id=' . $id_curso);
    exit;
}

$precio_original = $curso['precio'] * 1.4;
$descuento       = round((1 - $curso['precio']/$precio_original)*100);

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-top:var(--navbar-height);background:var(--bg-surface);min-height:calc(100vh - var(--navbar-height))">
  <div class="container" style="padding-top:3rem;padding-bottom:4rem">
    
    <!-- Steps indicator -->
    <div class="checkout-steps" style="margin-bottom:3rem">
      <div class="checkout-step active">
        <div class="step-circle">1</div>
        <span>Resumen</span>
      </div>
      <div class="checkout-step active" id="stepPago">
        <div class="step-circle">2</div>
        <span>Pago</span>
      </div>
      <div class="checkout-step" id="stepConfirm">
        <div class="step-circle">3</div>
        <span>Confirmación</span>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:2.5rem;align-items:start">
      <!-- LEFT: Payment form -->
      <div>
        <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1.75rem">Método de Pago</h2>

        <div id="paymentAlert" class="alert alert-danger" style="display:none">
          <i class="fas fa-exclamation-circle alert-icon"></i>
          <span id="paymentAlertMsg"></span>
        </div>

        <!-- Payment methods -->
        <div style="margin-bottom:1.75rem">
          <p style="font-size:.82rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.85rem">Elige un método</p>
          <div class="payment-icons">
            <div class="payment-icon active" data-method="card" title="Tarjeta de Crédito/Débito">
              <i class="fas fa-credit-card"></i><span>Tarjeta</span>
            </div>
            <div class="payment-icon" data-method="paypal" title="PayPal">
              <i class="fab fa-paypal"></i><span>PayPal</span>
            </div>
            <div class="payment-icon" data-method="oxxo" title="OXXO Pay">
              <i class="fas fa-store"></i><span>OXXO</span>
            </div>
            <div class="payment-icon" data-method="transfer" title="Transferencia">
              <i class="fas fa-university"></i><span>Transferencia</span>
            </div>
          </div>
        </div>

        <!-- Card form -->
        <div id="pay-card" class="payment-section">
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem">
            <div class="form-group">
              <label class="form-label">Número de Tarjeta</label>
              <div class="form-control-icon">
                <i class="fas fa-credit-card icon"></i>
                <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Nombre en la Tarjeta</label>
              <div class="form-control-icon">
                <i class="fas fa-user icon"></i>
                <input type="text" class="form-control" id="cardName" placeholder="Como aparece en la tarjeta" autocomplete="cc-name">
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
              <div class="form-group">
                <label class="form-label">Fecha de Expiración</label>
                <input type="text" class="form-control" id="cardExpiry" placeholder="MM/AA" maxlength="5">
              </div>
              <div class="form-group">
                <label class="form-label">CVV</label>
                <div class="form-control-icon">
                  <i class="fas fa-lock icon"></i>
                  <input type="text" class="form-control" id="cardCvv" placeholder="123" maxlength="4" autocomplete="cc-csc">
                </div>
              </div>
            </div>
            <!-- Test cards notice -->
            <div class="alert alert-info" style="font-size:.78rem;padding:.75rem 1rem">
              <i class="fas fa-info-circle alert-icon"></i>
              <div><strong>Entorno de prueba.</strong> Usa cualquier número para simular un pago. Ej: <code style="background:var(--bg-surface);padding:.1rem .3rem;border-radius:3px">4242 4242 4242 4242</code></div>
            </div>
          </div>
        </div>

        <!-- PayPal placeholder -->
        <div id="pay-paypal" class="payment-section d-none">
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;text-align:center">
            <i class="fab fa-paypal" style="font-size:3.5rem;color:#0070ba;margin-bottom:1rem;display:block"></i>
            <p style="color:var(--text-secondary);margin-bottom:1.5rem">Serás redirigido a PayPal para completar tu pago de forma segura.</p>
            <div style="background:rgba(0,112,186,.08);border:1px solid rgba(0,112,186,.2);border-radius:var(--radius);padding:1rem;font-size:.8rem;color:var(--text-muted)">
              Simulación: el pago se marcará como completado automáticamente.
            </div>
          </div>
        </div>

        <!-- OXXO placeholder -->
        <div id="pay-oxxo" class="payment-section d-none">
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;text-align:center">
            <i class="fas fa-qrcode" style="font-size:3.5rem;color:var(--warning);margin-bottom:1rem;display:block"></i>
            <p style="color:var(--text-secondary);margin-bottom:.75rem">Se generará un número de referencia para pagar en tiendas OXXO.</p>
            <div style="font-size:2rem;font-weight:800;letter-spacing:.3em;color:var(--text-primary);padding:1.5rem;background:var(--bg-base);border-radius:var(--radius);border:1px solid var(--border)">
              2846-1728-3991
            </div>
            <p style="font-size:.78rem;color:var(--text-muted);margin-top:.75rem">Referencia válida por 72 horas · Tu acceso se activará al confirmar el pago</p>
          </div>
        </div>

        <!-- Transfer placeholder -->
        <div id="pay-transfer" class="payment-section d-none">
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem">
            <h3 style="font-size:1rem;font-weight:600;margin-bottom:1.25rem">Datos para transferencia SPEI</h3>
            <?php foreach([['CLABE','646180264900012345'],['Banco','STP'],['Beneficiario','TecnoFutura Academy SA de CV'],['Concepto','Curso-'.$id_curso.'-'.$_SESSION['usuario_id']],['Monto','$'.number_format($curso['precio'],2).' MXN']] as [$k,$v]): ?>
            <div style="display:flex;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid var(--border);font-size:.875rem">
              <span style="color:var(--text-muted)"><?= $k ?></span>
              <span style="font-weight:600"><?= $v ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Submit button -->
        <button id="payBtn" class="btn btn-primary btn-block btn-lg" style="margin-top:2rem">
          <i class="fas fa-lock"></i>
          <span id="payBtnText">Pagar $<?= number_format($curso['precio'],2) ?> MXN</span>
          <span id="payBtnSpinner" style="display:none"><span class="spinner"></span></span>
        </button>
        <p style="text-align:center;font-size:.75rem;color:var(--text-muted);margin-top:.75rem">
          <i class="fas fa-shield-alt" style="color:var(--success)"></i> Pago 100% seguro · Garantía de 30 días
        </p>
      </div>

      <!-- RIGHT: Order summary -->
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.75rem;position:sticky;top:calc(var(--navbar-height) + 1rem)">
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--border)">Resumen de Orden</h3>
        
        <div style="display:flex;gap:1rem;margin-bottom:1.25rem">
          <div style="width:64px;height:64px;background:var(--bg-surface);border-radius:var(--radius);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-microchip" style="color:var(--primary);font-size:1.5rem"></i>
          </div>
          <div>
            <div style="font-weight:600;font-size:.875rem;line-height:1.4"><?= htmlspecialchars($curso['nombre_curso']) ?></div>
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:.25rem">
              <span class="badge badge-<?= strtolower(str_replace('á','a',$curso['nivel'])) ?>"><?= $curso['nivel'] ?></span>
              <span style="margin-left:.4rem"><?= $curso['duracion_horas'] ?>h</span>
            </div>
          </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:1.25rem;padding:.75rem;background:var(--bg-surface);border-radius:var(--radius)">
          <div style="display:flex;justify-content:space-between;font-size:.82rem">
            <span style="color:var(--text-muted)">Precio regular</span>
            <span style="text-decoration:line-through;color:var(--text-muted)">$<?= number_format($precio_original,2) ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:.82rem">
            <span style="color:var(--text-muted)">Descuento <?= $descuento ?>%</span>
            <span style="color:var(--success)">-$<?= number_format($precio_original - $curso['precio'],2) ?></span>
          </div>
        </div>

        <div style="display:flex;justify-content:space-between;font-size:1.15rem;font-weight:800;padding:.75rem 0;border-top:1px solid var(--border)">
          <span>Total</span>
          <span style="color:var(--primary)">$<?= number_format($curso['precio'],2) ?> MXN</span>
        </div>

        <!-- What's included -->
        <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border)">
          <p style="font-size:.75rem;font-weight:600;color:var(--text-muted);margin-bottom:.75rem">INCLUYE:</p>
          <?php foreach(['Acceso de por vida','Certificado verificable','Materiales descargables','Soporte del instructor'] as $inc): ?>
          <div style="display:flex;align-items:center;gap:.5rem;font-size:.78rem;color:var(--text-secondary);margin-bottom:.4rem">
            <i class="fas fa-check" style="color:var(--success);flex-shrink:0"></i><?= $inc ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>

<script>
const idCurso = <?= $id_curso ?>;
const precio  = <?= $curso['precio'] ?>;

document.getElementById('payBtn').addEventListener('click', async () => {
  const btn     = document.getElementById('payBtn');
  const btnText = document.getElementById('payBtnText');
  const spinner = document.getElementById('payBtnSpinner');
  const alert   = document.getElementById('paymentAlert');
  const alertMsg= document.getElementById('paymentAlertMsg');

  const activeMethod = document.querySelector('.payment-icon.active')?.dataset.method || 'card';

  if (activeMethod === 'card') {
    const num  = document.getElementById('cardNumber')?.value.replace(/\s/g,'');
    const name = document.getElementById('cardName')?.value;
    const exp  = document.getElementById('cardExpiry')?.value;
    const cvv  = document.getElementById('cardCvv')?.value;

    if (!num || num.length < 13) { alert.style.display='flex'; alertMsg.textContent='Número de tarjeta inválido.'; return; }
    if (!name || name.length < 3) { alert.style.display='flex'; alertMsg.textContent='Nombre en tarjeta inválido.'; return; }
    if (!exp || !/^\d{2}\/\d{2}$/.test(exp)) { alert.style.display='flex'; alertMsg.textContent='Fecha de expiración inválida.'; return; }
    if (!cvv || cvv.length < 3) { alert.style.display='flex'; alertMsg.textContent='CVV inválido.'; return; }
  }

  alert.style.display = 'none';
  btn.disabled = true;
  btnText.style.display = 'none';
  spinner.style.display = '';

  // Simulate processing delay
  await new Promise(r => setTimeout(r, 1800));

  const fd = new FormData();
  fd.append('id_curso', idCurso);
  fd.append('monto', precio);
  fd.append('metodo_pago', activeMethod);

  try {
    const res  = await fetch('<?= SITE_URL ?>/checkout/procesar.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      window.location.href = '<?= SITE_URL ?>/checkout/gracias.php?ref=' + data.referencia + '&id=' + idCurso;
    } else {
      alert.style.display = 'flex';
      alertMsg.textContent = data.message || 'Error al procesar el pago.';
      btn.disabled = false; btnText.style.display = ''; spinner.style.display = 'none';
    }
  } catch {
    alert.style.display = 'flex';
    alertMsg.textContent = 'Error de conexión. Intenta de nuevo.';
    btn.disabled = false; btnText.style.display = ''; spinner.style.display = 'none';
  }
});
</script>
