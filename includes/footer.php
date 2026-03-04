
<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="brand">
          <div class="brand-icon" style="width:36px;height:36px;background:var(--grad-primary);border-radius:8px;display:flex;align-items:center;justify-content:center"><i class="fas fa-microchip" style="color:#fff"></i></div>
          <span class="brand-name">TecnoFutura</span>
        </div>
        <p>Plataforma especializada en Lenguaje Ensamblador, Arduino y sistemas embebidos. Aprende desde cero con proyectos prácticos reales.</p>
        <div class="footer-social" style="margin-top:1.25rem">
          <a href="<?= FACEBOOK_URL ?>" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="<?= TWITTER_URL ?>" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="<?= INSTAGRAM_URL ?>" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="<?= LINKEDIN_URL ?>" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          <a href="https://github.com" aria-label="GitHub"><i class="fab fa-github"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h4>Plataforma</h4>
        <ul>
          <li><a href="<?= SITE_URL ?>/cursos">Todos los Cursos</a></li>
          <li><a href="<?= SITE_URL ?>/#precios">Planes y Precios</a></li>
          <li><a href="<?= SITE_URL ?>/#nosotros">Sobre Nosotros</a></li>
          <li><a href="<?= SITE_URL ?>/certificados">Certificaciones</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Categorías</h4>
        <ul>
          <li><a href="<?= SITE_URL ?>/cursos?nivel=Básico">Cursos Básicos</a></li>
          <li><a href="<?= SITE_URL ?>/cursos?nivel=Intermedio">Intermedio</a></li>
          <li><a href="<?= SITE_URL ?>/cursos?nivel=Avanzado">Avanzado</a></li>
          <li><a href="<?= SITE_URL ?>/cursos?gratis=1">Cursos Gratis</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Soporte</h4>
        <ul>
          <li><a href="mailto:<?= CONTACT_EMAIL ?>">Contacto</a></li>
          <li><a href="<?= SITE_URL ?>/faq">Preguntas Frecuentes</a></li>
          <li><a href="<?= SITE_URL ?>/terminos">Términos y Condiciones</a></li>
          <li><a href="<?= SITE_URL ?>/privacidad">Privacidad</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> TecnoFutura Academy. Todos los derechos reservados. Hecho con <i class="fas fa-heart" style="color:var(--danger)"></i> en México.</p>
      <p style="font-size:.75rem;color:var(--text-muted)"><i class="fas fa-shield-alt" style="color:var(--success)"></i> Pago 100% Seguro &nbsp;|&nbsp; <i class="fas fa-certificate" style="color:var(--warning)"></i> Certificados Verificables</p>
    </div>
  </div>
</footer>

<!-- Back to Top -->
<button id="back-to-top" aria-label="Volver arriba"><i class="fas fa-arrow-up"></i></button>

<!-- Main JS -->
<script src="<?= SITE_URL ?>/js/main.js"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
