<?php
$page_title = "Inicio";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section id="inicio" class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 text-white">
                <h1 class="display-3 fw-bold mb-4 animate-fade-in">
                    Aprende Arduino y Electrónica
                </h1>
                <p class="lead mb-4 animate-fade-in-delay-1">
                    Domina el mundo de los microcontroladores desde lo básico hasta sistemas embebidos avanzados. 
                    Teoría y práctica con proyectos reales.
                </p>
                <div class="animate-fade-in-delay-2">
                    <a href="#cursos" class="btn btn-warning btn-lg me-3 mb-2">
                        <i class="bi bi-mortarboard-fill me-2"></i>Ver Cursos
                    </a>
                    <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-light btn-lg mb-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Acceder al LMS
                    </a>
                </div>
                <div class="mt-5 animate-fade-in-delay-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="fw-bold text-warning">9+</h3>
                            <p class="small">Cursos</p>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold text-warning">100+</h3>
                            <p class="small">Proyectos</p>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold text-warning">24/7</h3>
                            <p class="small">Acceso</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center animate-fade-in-delay-2">
                <div class="hero-image">
                    <i class="bi bi-cpu-fill" style="font-size: 20rem; color: #ffc107;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <a href="#nosotros" class="text-white">
            <i class="bi bi-chevron-down animate-bounce"></i>
        </a>
    </div>
</section>

<!-- Sobre Nosotros -->
<section id="nosotros" class="py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-3">¿Qué es TecnoFutura Academy?</h2>
                <div class="title-underline mx-auto"></div>
                <p class="lead text-muted mt-4">
                    Somos una plataforma de aprendizaje especializada en Arduino, electrónica y programación de bajo nivel. 
                    Nuestro objetivo es formar profesionales capaces de diseñar e implementar sistemas embebidos y proyectos IoT.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-laptop text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Simuladores Online</h4>
                    <p class="text-muted">
                        Practica con Tinkercad y Arduino IDE sin necesidad de hardware físico. Aprende desde cualquier lugar.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-code-square text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Proyectos Prácticos</h4>
                    <p class="text-muted">
                        Cada curso incluye proyectos reales para aplicar lo aprendido. De semáforos a sistemas domóticos.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-award text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Certificación</h4>
                    <p class="text-muted">
                        Obtén certificados al completar cada curso y diplomado. Valida tus conocimientos profesionalmente.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cursos por Nivel -->
<section id="cursos" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-3">Nuestros Cursos</h2>
                <div class="title-underline mx-auto"></div>
                <p class="lead text-muted mt-4">
                    Desde principiante hasta avanzado. Aprende a tu ritmo con nuestra metodología progresiva.
                </p>
            </div>
        </div>

        <!-- Nivel Básico -->
        <div class="mb-5">
            <h3 class="fw-bold mb-4 text-primary">
                <i class="bi bi-1-circle-fill me-2"></i>Nivel Básico
            </h3>
            <div class="row g-4">
                <!-- Curso 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-power text-warning"></i>
                        </div>
                        <span class="badge bg-success position-absolute top-0 end-0 m-3">Básico</span>
                        <h4 class="fw-bold mb-3">Introducción a Arduino</h4>
                        <p class="text-muted mb-3">
                            Fundamentos de electrónica y la plataforma Arduino. Desde "Hola Mundo" hasta tu primer proyecto.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Historia y aplicaciones</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Componentes básicos</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Primer programa: Blink</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Proyecto: Semáforo básico</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Curso 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-code-slash text-warning"></i>
                        </div>
                        <span class="badge bg-success position-absolute top-0 end-0 m-3">Básico</span>
                        <h4 class="fw-bold mb-3">Arduino Desde Cero</h4>
                        <p class="text-muted mb-3">
                            Desarrolla habilidades de programación usando estructuras fundamentales y control de flujo.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Variables y tipos de datos</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Condicionales y ciclos</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Lectura de sensores</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Proyecto: Luces automáticas</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Curso 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-calculator text-warning"></i>
                        </div>
                        <span class="badge bg-success position-absolute top-0 end-0 m-3">Básico</span>
                        <h4 class="fw-bold mb-3">Programación Aritmética y Lógica</h4>
                        <p class="text-muted mb-3">
                            Aplica operaciones matemáticas y lógica digital en proyectos físicos con Arduino.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Operaciones aritméticas</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Operaciones booleanas</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Control con potenciómetros</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Proyecto: Control PWM</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nivel Intermedio -->
        <div class="mb-5">
            <h3 class="fw-bold mb-4 text-primary">
                <i class="bi bi-2-circle-fill me-2"></i>Nivel Intermedio
            </h3>
            <div class="row g-4">
                <!-- Curso 4 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-cpu text-warning"></i>
                        </div>
                        <span class="badge bg-warning position-absolute top-0 end-0 m-3">Intermedio</span>
                        <h4 class="fw-bold mb-3">Arquitectura de Computadoras</h4>
                        <p class="text-muted mb-3">
                            Comprende cómo funciona internamente un sistema de cómputo y su arquitectura básica.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Modelo von Neumann</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>CPU y registros</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Memoria y buses</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Representación binaria</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Curso 5 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-terminal text-warning"></i>
                        </div>
                        <span class="badge bg-warning position-absolute top-0 end-0 m-3">Intermedio</span>
                        <h4 class="fw-bold mb-3">Lenguaje Ensamblador</h4>
                        <p class="text-muted mb-3">
                            Introducción a la programación de bajo nivel y su relación directa con el hardware.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Estructura del ensamblador</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Registros y memoria</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Instrucciones básicas</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Prácticas en simulador</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Curso 6 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-thermometer-half text-warning"></i>
                        </div>
                        <span class="badge bg-warning position-absolute top-0 end-0 m-3">Intermedio</span>
                        <h4 class="fw-bold mb-3">Arduino Intermedio: Sensores</h4>
                        <p class="text-muted mb-3">
                            Integra sensores y actuadores en proyectos funcionales y profesionales.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Sensores analógicos</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Servomotores y LCD</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Comunicación serial</li>
                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Proyecto: Estación meteorológica</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nivel Avanzado -->
        <div class="mb-5">
            <h3 class="fw-bold mb-4 text-primary">
                <i class="bi bi-3-circle-fill me-2"></i>Nivel Avanzado
            </h3>
            <div class="row g-4">
                <!-- Curso 7 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-gear-fill text-warning"></i>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-3">Avanzado</span>
                        <h4 class="fw-bold mb-3">Ensamblador Aplicado</h4>
                        <p class="text-muted mb-3">
                            Desarrolla programas optimizados en bajo nivel con técnicas avanzadas de ensamblador.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Manipulación de registros</li>
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Manejo de interrupciones</li>
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Optimización de código</li>
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Proyecto: Calculadora ASM</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Curso 8 -->
                <div class="col-lg-4 col-md-6">
                    <div class="course-card h-100">
                        <div class="course-icon">
                            <i class="bi bi-wifi text-warning"></i>
                        </div>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-3">Avanzado</span>
                        <h4 class="fw-bold mb-3">Sistemas Embebidos</h4>
                        <p class="text-muted mb-3">
                            Diseña sistemas inteligentes basados en microcontroladores e IoT avanzado.
                        </p>
                        <ul class="course-features list-unstyled mb-4">
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Comunicación I2C y SPI</li>
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Módulos Bluetooth y WiFi</li>
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>IoT y automatización</li>
                            <li><i class="bi bi-check-circle-fill text-danger me-2"></i>Proyecto: Sistema domótico</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Ver más <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Paquetes y Precios -->
<section id="paquetes" class="py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-3">Paquetes y Precios</h2>
                <div class="title-underline mx-auto"></div>
                <p class="lead text-muted mt-4">
                    Elige el plan que mejor se adapte a tus necesidades y objetivos de aprendizaje.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Curso Individual -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card text-center h-100">
                    <div class="pricing-header bg-primary text-white p-4">
                        <i class="bi bi-bookmark-fill" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-3 mb-0">Curso Individual</h3>
                    </div>
                    <div class="pricing-body p-4">
                        <div class="price my-4">
                            <h2 class="fw-bold mb-0">$499 <small class="text-muted fs-6">MXN</small></h2>
                            <p class="text-muted">por curso</p>
                        </div>
                        <ul class="list-unstyled text-start mb-4">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Acceso de por vida</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Certificado digital</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Proyectos prácticos</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Soporte por email</li>
                            <li class="mb-3 text-muted"><i class="bi bi-x-circle-fill me-2"></i>Mentoría personalizada</li>
                            <li class="mb-3 text-muted"><i class="bi bi-x-circle-fill me-2"></i>Material descargable</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-outline-primary w-100">
                            Comprar ahora
                        </a>
                    </div>
                </div>
            </div>

            <!-- Paquete Completo - DESTACADO -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card pricing-featured text-center h-100">
                    <div class="badge-popular">MÁS POPULAR</div>
                    <div class="pricing-header bg-warning p-4">
                        <i class="bi bi-collection-fill text-dark" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-3 mb-0 text-dark">Paquete Completo</h3>
                    </div>
                    <div class="pricing-body p-4">
                        <div class="price my-4">
                            <h2 class="fw-bold mb-0">$3,499 <small class="text-muted fs-6">MXN</small></h2>
                            <p class="text-muted">todos los cursos</p>
                            <span class="badge bg-success">Ahorra 30%</span>
                        </div>
                        <ul class="list-unstyled text-start mb-4">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Todos los 9 cursos</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Acceso de por vida</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Certificados digitales</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Soporte prioritario</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Mentoría personalizada</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Material descargable</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-warning w-100 text-dark fw-bold">
                            ¡Comprar ahora!
                        </a>
                    </div>
                </div>
            </div>

            <!-- Diplomado -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card text-center h-100">
                    <div class="pricing-header bg-dark text-white p-4">
                        <i class="bi bi-trophy-fill" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-3 mb-0">Diplomado Completo</h3>
                    </div>
                    <div class="pricing-body p-4">
                        <div class="price my-4">
                            <h2 class="fw-bold mb-0">$4,999 <small class="text-muted fs-6">MXN</small></h2>
                            <p class="text-muted">certificación oficial</p>
                        </div>
                        <ul class="list-unstyled text-start mb-4">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Todos los cursos</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Certificación oficial</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Proyecto final supervisado</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Mentoría 1 a 1</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Kit de Arduino incluido</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Bolsa de trabajo</li>
                        </ul>
                        <a href="<?php echo LMS_URL; ?>" class="btn btn-dark w-100">
                            Más información
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA adicional -->
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto">
                <div class="cta-box text-center p-5 bg-primary text-white rounded">
                    <h3 class="fw-bold mb-3">¿No estás seguro cuál elegir?</h3>
                    <p class="lead mb-4">
                        Contáctanos y te ayudaremos a elegir el plan perfecto para tus objetivos de aprendizaje.
                    </p>
                    <a href="#contacto" class="btn btn-warning btn-lg">
                        <i class="bi bi-chat-dots-fill me-2"></i>Contactar ahora
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contacto -->
<section id="contacto" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-3">Contáctanos</h2>
                <div class="title-underline mx-auto"></div>
                <p class="lead text-muted mt-4">
                    ¿Tienes dudas? Envíanos un mensaje y te responderemos lo más pronto posible.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Información de contacto -->
            <div class="col-lg-5">
                <div class="contact-info p-4 bg-light rounded h-100">
                    <h4 class="fw-bold mb-4">Información de Contacto</h4>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <i class="bi bi-envelope-fill text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Email</h6>
                                <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="text-decoration-none">
                                    <?php echo CONTACT_EMAIL; ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-3">
                            <i class="bi bi-telephone-fill text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Teléfono</h6>
                                <p class="mb-0"><?php echo CONTACT_PHONE; ?></p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-3">
                            <i class="bi bi-geo-alt-fill text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Ubicación</h6>
                                <p class="mb-0">Ciudad de México, México</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <i class="bi bi-clock-fill text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Horario</h6>
                                <p class="mb-0">Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Síguenos en redes sociales</h6>
                        <div class="social-links">
                            <a href="<?php echo FACEBOOK_URL; ?>" class="btn btn-outline-primary me-2 mb-2" target="_blank">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="<?php echo TWITTER_URL; ?>" class="btn btn-outline-primary me-2 mb-2" target="_blank">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="<?php echo INSTAGRAM_URL; ?>" class="btn btn-outline-primary me-2 mb-2" target="_blank">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="<?php echo LINKEDIN_URL; ?>" class="btn btn-outline-primary mb-2" target="_blank">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de contacto -->
            <div class="col-lg-7">
                <div class="contact-form p-4 bg-white shadow-sm rounded">
                    <form id="contactForm" method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre completo *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                            <div class="col-md-6">
                                <label for="asunto" class="form-label">Asunto *</label>
                                <select class="form-select" id="asunto" name="asunto" required>
                                    <option value="">Selecciona una opción</option>
                                    <option value="informacion">Información general</option>
                                    <option value="cursos">Consulta sobre cursos</option>
                                    <option value="paquetes">Paquetes y precios</option>
                                    <option value="soporte">Soporte técnico</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="mensaje" class="form-label">Mensaje *</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-send-fill me-2"></i>Enviar mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Botón flotante WhatsApp -->
<a href="https://wa.me/521234567890" class="whatsapp-float" target="_blank" title="Contáctanos por WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- Botón volver arriba -->
<button id="scrollTopBtn" class="scroll-top-btn" title="Volver arriba">
    <i class="bi bi-arrow-up"></i>
</button>

<?php include 'includes/footer.php'; ?>
