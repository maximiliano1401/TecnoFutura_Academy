
/* TecnoFutura Academy - Main JavaScript */
'use strict';

// ── Navbar scroll effect ──────────────────────────────────
const navbar = document.getElementById('mainNavbar');
window.addEventListener('scroll', () => {
  if (window.scrollY > 20) navbar?.classList.add('scrolled');
  else navbar?.classList.remove('scrolled');
}, { passive: true });

// ── Mobile nav toggle ─────────────────────────────────────
const mobileToggle = document.getElementById('mobileToggle');
const mobileNav    = document.getElementById('mobileNav');
mobileToggle?.addEventListener('click', () => {
  mobileNav.classList.toggle('show');
  mobileToggle.innerHTML = mobileNav.classList.contains('show')
    ? '<i class="fas fa-times"></i>'
    : '<i class="fas fa-bars"></i>';
});
document.addEventListener('click', (e) => {
  if (!e.target.closest('#mobileToggle') && !e.target.closest('#mobileNav')) {
    mobileNav?.classList.remove('show');
    if (mobileToggle) mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
  }
});

// ── User dropdown ─────────────────────────────────────────
const userMenuBtn  = document.getElementById('userMenuBtn');
const userDropdown = document.getElementById('userDropdown');
userMenuBtn?.addEventListener('click', (e) => {
  e.stopPropagation();
  userDropdown.classList.toggle('show');
});
document.addEventListener('click', (e) => {
  if (!e.target.closest('.navbar-user-menu')) {
    userDropdown?.classList.remove('show');
  }
});

// ── Toast notifications ───────────────────────────────────
function showToast(message, type = 'info', duration = 4000) {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const icons = { success: 'fa-check-circle', danger: 'fa-exclamation-circle', info: 'fa-info-circle', warning: 'fa-exclamation-triangle' };
  const colors = { success: 'var(--success)', danger: 'var(--danger)', info: 'var(--info)', warning: 'var(--warning)' };
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<i class="fas ${icons[type]||icons.info}" style="color:${colors[type]||colors.info};flex-shrink:0"></i><span style="flex:1">${message}</span><button onclick="this.closest('.toast').remove()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;padding:0;font-size:.8rem"><i class="fas fa-times"></i></button>`;
  container.appendChild(toast);
  requestAnimationFrame(() => { requestAnimationFrame(() => toast.classList.add('show')); });
  if (duration > 0) setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, duration);
  return toast;
}

// ── Back to top ───────────────────────────────────────────
const btt = document.getElementById('back-to-top');
window.addEventListener('scroll', () => {
  if (window.scrollY > 400) btt?.classList.add('show');
  else btt?.classList.remove('show');
}, { passive: true });
btt?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// ── Animated counters ─────────────────────────────────────
function animateCounter(el) {
  const target = parseInt(el.dataset.target, 10);
  const duration = 1800;
  const start = performance.now();
  const update = (now) => {
    const elapsed = now - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(eased * target).toLocaleString('es-MX') + (el.dataset.suffix || '');
    if (progress < 1) requestAnimationFrame(update);
  };
  requestAnimationFrame(update);
}
const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) { animateCounter(e.target); counterObserver.unobserve(e.target); } });
}, { threshold: 0.5 });
document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));

// ── Curriculum accordion ──────────────────────────────────
document.querySelectorAll('.curriculum-module-header').forEach(header => {
  header.addEventListener('click', () => {
    const lessons = header.nextElementSibling;
    const icon = header.querySelector('.toggle-icon');
    lessons?.classList.toggle('show');
    if (icon) icon.style.transform = lessons.classList.contains('show') ? 'rotate(180deg)' : '';
  });
});

// ── Tabs ──────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.dataset.tab;
    const group  = btn.closest('[data-tab-group]') || btn.parentElement.parentElement;
    group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    group.querySelectorAll('.tab-content').forEach(c => c.classList.add('d-none'));
    btn.classList.add('active');
    group.querySelector(`#${target}`)?.classList.remove('d-none');
  });
});

// ── Form validation helpers ───────────────────────────────
function showFieldError(input, msg) {
  clearFieldError(input);
  input.classList.add('is-invalid');
  const err = document.createElement('div');
  err.className = 'form-error';
  err.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>${msg}</span>`;
  input.insertAdjacentElement('afterend', err);
}
function clearFieldError(input) {
  input.classList.remove('is-invalid');
  input.nextElementSibling?.classList.contains('form-error') && input.nextElementSibling.remove();
}

// ── Password toggle ───────────────────────────────────────
document.querySelectorAll('.toggle-btn[data-target]').forEach(btn => {
  btn.addEventListener('click', () => {
    const inp = document.getElementById(btn.dataset.target);
    if (!inp) return;
    const isPass = inp.type === 'password';
    inp.type = isPass ? 'text' : 'password';
    btn.innerHTML = isPass ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
  });
});

// ── Payment method selector ───────────────────────────────
document.querySelectorAll('.payment-icon').forEach(icon => {
  icon.addEventListener('click', () => {
    icon.closest('.payment-icons')?.querySelectorAll('.payment-icon').forEach(i => i.classList.remove('active'));
    icon.classList.add('active');
    const method = icon.dataset.method;
    document.querySelectorAll('.payment-section').forEach(s => s.classList.add('d-none'));
    document.getElementById(`pay-${method}`)?.classList.remove('d-none');
  });
});

// ── Card number formatting ─────────────────────────────────
const cardInput = document.getElementById('cardNumber');
cardInput?.addEventListener('input', (e) => {
  let v = e.target.value.replace(/\D/g, '').substring(0, 16);
  e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
});

// ── Expiry formatting ─────────────────────────────────────
const expiryInput = document.getElementById('cardExpiry');
expiryInput?.addEventListener('input', (e) => {
  let v = e.target.value.replace(/\D/g, '').substring(0, 4);
  if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2);
  e.target.value = v;
});

// ── Progress circle ───────────────────────────────────────
document.querySelectorAll('.progress-circle').forEach(el => {
  const pct = parseFloat(el.dataset.pct || 0);
  const r = 30; const c = 2 * Math.PI * r;
  el.innerHTML = `<svg width="80" height="80" viewBox="0 0 80 80">
    <circle cx="40" cy="40" r="${r}" fill="none" stroke="var(--border)" stroke-width="6"/>
    <circle cx="40" cy="40" r="${r}" fill="none" stroke="url(#grad)" stroke-width="6"
      stroke-dasharray="${c}" stroke-dashoffset="${c - (pct / 100) * c}"
      stroke-linecap="round" transform="rotate(-90 40 40)"/>
    <defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#06b6d4"/><stop offset="100%" stop-color="#8b5cf6"/>
    </linearGradient></defs>
    <text x="40" y="45" text-anchor="middle" fill="var(--text-primary)" font-size="13" font-weight="700">${Math.round(pct)}%</text>
  </svg>`;
});

// ── Smooth scroll for anchor links ───────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', (e) => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
  });
});

// ── Lazy load images ──────────────────────────────────────
const imgObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      const img = e.target;
      if (img.dataset.src) { img.src = img.dataset.src; img.removeAttribute('data-src'); }
      imgObserver.unobserve(img);
    }
  });
});
document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));

// ── Copy to clipboard ─────────────────────────────────────
document.querySelectorAll('[data-copy]').forEach(btn => {
  btn.addEventListener('click', () => {
    navigator.clipboard.writeText(btn.dataset.copy).then(() => showToast('Copiado al portapapeles', 'success', 2000));
  });
});

// ── Delete confirmation ───────────────────────────────────
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', (e) => {
    if (!confirm(el.dataset.confirm || '¿Estás seguro?')) e.preventDefault();
  });
});

// ── Fade in on scroll ─────────────────────────────────────
const fadeObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('fade-in-done'); fadeObserver.unobserve(e.target); }
  });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-in').forEach(el => fadeObserver.observe(el));

// ── Course search ─────────────────────────────────────────
const searchInput = document.getElementById('courseSearch');
searchInput?.addEventListener('input', debounce(() => {
  const q = searchInput.value.toLowerCase().trim();
  document.querySelectorAll('.course-card').forEach(card => {
    const title = card.querySelector('.course-card-title')?.textContent.toLowerCase() || '';
    card.style.display = (!q || title.includes(q)) ? '' : 'none';
  });
}, 300));

function debounce(fn, delay) {
  let t; return function(...a) { clearTimeout(t); t = setTimeout(() => fn.apply(this, a), delay); };
}

// ── Lesson completion marking ─────────────────────────────
document.querySelectorAll('.mark-complete-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const lessonId = btn.dataset.lesson;
    const inscId   = btn.dataset.inscripcion;
    try {
      btn.disabled = true;
      const res = await fetch('/lms/ajax/marcar_completado.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_material: lessonId, id_inscripcion: inscId })
      });
      const data = await res.json();
      if (data.success) {
        showToast('Lección marcada como completada', 'success');
        btn.textContent = 'Completado';
        btn.classList.remove('btn-primary'); btn.classList.add('btn-success');
        document.querySelector(`.lesson-item[data-lesson="${lessonId}"]`)?.classList.add('completed');
        const pctEl = document.getElementById('courseProgress');
        if (pctEl && data.progreso !== undefined) {
          pctEl.style.width = data.progreso + '%';
          const pctText = document.getElementById('progressText');
          if (pctText) pctText.textContent = Math.round(data.progreso) + '%';
        }
      }
    } catch { showToast('Error al actualizar progreso', 'danger'); }
    finally { btn.disabled = false; }
  });
});

// ── Modal helpers ─────────────────────────────────────────
function openModal(id) {
  const m = document.getElementById(id);
  m?.classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  const m = document.getElementById(id);
  m?.classList.remove('show');
  document.body.style.overflow = '';
}
document.querySelectorAll('.modal-close, [data-close-modal]').forEach(el => {
  el.addEventListener('click', () => {
    const modal = el.closest('.modal-backdrop');
    if (modal) { modal.classList.remove('show'); document.body.style.overflow = ''; }
  });
});
document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
  backdrop.addEventListener('click', (e) => {
    if (e.target === backdrop) { backdrop.classList.remove('show'); document.body.style.overflow = ''; }
  });
});
document.querySelectorAll('[data-open-modal]').forEach(btn => {
  btn.addEventListener('click', () => openModal(btn.dataset.openModal));
});
