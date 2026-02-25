    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <!-- Información de la empresa -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-warning mb-3">TecnoFutura Academy</h5>
                    <p class="text-light-50">
                        Plataforma especializada en la enseñanza de Arduino, electrónica y programación de bajo nivel.
                        Aprende teoría y práctica con proyectos reales.
                    </p>
                    <div class="social-links mt-3">
                        <a href="<?php echo FACEBOOK_URL; ?>" class="text-white me-3" target="_blank">
                            <i class="bi bi-facebook fs-4"></i>
                        </a>
                        <a href="<?php echo TWITTER_URL; ?>" class="text-white me-3" target="_blank">
                            <i class="bi bi-twitter fs-4"></i>
                        </a>
                        <a href="<?php echo INSTAGRAM_URL; ?>" class="text-white me-3" target="_blank">
                            <i class="bi bi-instagram fs-4"></i>
                        </a>
                        <a href="<?php echo LINKEDIN_URL; ?>" class="text-white" target="_blank">
                            <i class="bi bi-linkedin fs-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Enlaces rápidos -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-warning mb-3">Enlaces</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#inicio" class="text-white text-decoration-none">Inicio</a></li>
                        <li class="mb-2"><a href="#nosotros" class="text-white text-decoration-none">Nosotros</a></li>
                        <li class="mb-2"><a href="#cursos" class="text-white text-decoration-none">Cursos</a></li>
                        <li class="mb-2"><a href="#paquetes" class="text-white text-decoration-none">Paquetes</a></li>
                        <li class="mb-2"><a href="<?php echo LMS_URL; ?>" class="text-white text-decoration-none">LMS</a></li>
                    </ul>
                </div>

                <!-- Cursos populares -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-warning mb-3">Cursos Populares</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#cursos" class="text-white text-decoration-none">Introducción a Arduino</a></li>
                        <li class="mb-2"><a href="#cursos" class="text-white text-decoration-none">Arduino Desde Cero</a></li>
                        <li class="mb-2"><a href="#cursos" class="text-white text-decoration-none">Sistemas Embebidos</a></li>
                        <li class="mb-2"><a href="#cursos" class="text-white text-decoration-none">Lenguaje Ensamblador</a></li>
                    </ul>
                </div>

                <!-- Contacto -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-warning mb-3">Contacto</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-envelope-fill me-2"></i>
                            <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="text-white text-decoration-none">
                                <?php echo CONTACT_EMAIL; ?>
                            </a>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone-fill me-2"></i>
                            <span><?php echo CONTACT_PHONE; ?></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            <span>Ciudad de México, México</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="bg-secondary my-4">

            <!-- Copyright -->
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> TecnoFutura Academy. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white text-decoration-none me-3">Términos y Condiciones</a>
                    <a href="#" class="text-white text-decoration-none">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo JS_PATH; ?>/main.js"></script>
</body>
</html>
