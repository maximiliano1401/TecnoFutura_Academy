/**
 * TecnoFutura Academy - JavaScript Principal
 * Funcionalidades interactivas de la landing page
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // ========== Navbar Scroll Effect ==========
    initNavbarScroll();
    
    // ========== Smooth Scroll ==========
    initSmoothScroll();
    
    // ========== Scroll Top Button ==========
    initScrollTopButton();
    
    // ========== Form Validation ==========
    initContactForm();
    
    // ========== Animations on Scroll ==========
    initScrollAnimations();
    
    // ========== Active Nav Link ==========
    initActiveNavLink();
    
});

/**
 * Efecto de transparencia del navbar al hacer scroll
 */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(13, 110, 253, 0.95)';
            navbar.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = 'rgba(13, 110, 253, 1)';
            navbar.style.boxShadow = 'none';
        }
    });
}

/**
 * Smooth scroll para todos los enlaces ancla
 */
function initSmoothScroll() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Verificar si el href es válido
            if (href === '#' || href === '') return;
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = target.offsetTop - navbarHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Cerrar el menú móvil si está abierto
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            }
        });
    });
}

/**
 * Botón de volver arriba
 */
function initScrollTopButton() {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    
    if (!scrollTopBtn) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollTopBtn.classList.add('show');
        } else {
            scrollTopBtn.classList.remove('show');
        }
    });
    
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Validación y envío del formulario de contacto
 */
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    
    if (!contactForm) return;
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!contactForm.checkValidity()) {
            e.stopPropagation();
            contactForm.classList.add('was-validated');
            return;
        }
        
        // Obtener datos del formulario
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData);
        
        // Simular envío (aquí puedes agregar AJAX para enviar al servidor)
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar estado de carga
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading me-2"></span> Enviando...';
        
        // Simular delay de envío
        setTimeout(function() {
            // Mostrar mensaje de éxito
            showAlert('success', '¡Mensaje enviado exitosamente! Te contactaremos pronto.');
            
            // Resetear formulario
            contactForm.reset();
            contactForm.classList.remove('was-validated');
            
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            // Log de datos (en producción, aquí enviarías al servidor)
            console.log('Datos del formulario:', data);
        }, 2000);
    });
}

/**
 * Mostrar alertas personalizadas
 */
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(function() {
        alertDiv.remove();
    }, 5000);
}

/**
 * Animaciones al hacer scroll (Intersection Observer)
 */
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.course-card, .pricing-card, .feature-card');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.6s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

/**
 * Resaltar enlace activo del navbar según la sección visible
 */
function initActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');
    
    window.addEventListener('scroll', function() {
        let current = '';
        const scrollPosition = window.scrollY + 100;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href').substring(1);
            
            if (href === current) {
                link.classList.add('active');
            }
        });
    });
}

/**
 * Counter Animation (para estadísticas)
 */
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        
        if (start >= target) {
            element.textContent = target + '+';
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start) + '+';
        }
    }, 16);
}

/**
 * Lazy loading de imágenes
 */
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

/**
 * Efecto parallax simple en hero section
 */
function initParallax() {
    window.addEventListener('scroll', function() {
        const scrolled = window.scrollY;
        const parallaxElements = document.querySelectorAll('.hero-image');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
}

/**
 * Preloader (opcional)
 */
function initPreloader() {
    window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.opacity = '0';
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        }
    });
}

/**
 * Modal para vista previa de cursos (opcional)
 */
function initCourseModals() {
    const courseCards = document.querySelectorAll('.course-card');
    
    courseCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('a')) {
                const courseName = this.querySelector('h4').textContent;
                console.log('Curso seleccionado:', courseName);
                // Aquí puedes abrir un modal con más información
            }
        });
    });
}

/**
 * Copiar al portapapeles (para compartir)
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showAlert('success', '¡Enlace copiado al portapapeles!');
    }).catch(function(err) {
        console.error('Error al copiar:', err);
    });
}

/**
 * Detectar modo oscuro del sistema (opcional)
 */
function detectDarkMode() {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    
    if (prefersDark.matches) {
        console.log('Modo oscuro detectado');
        // Aplicar estilos de modo oscuro si es necesario
    }
    
    prefersDark.addEventListener('change', (e) => {
        if (e.matches) {
            console.log('Cambiado a modo oscuro');
        } else {
            console.log('Cambiado a modo claro');
        }
    });
}

/**
 * Prevenir submit múltiple de formularios
 */
function preventMultipleSubmit() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        let submitted = false;
        
        form.addEventListener('submit', function(e) {
            if (submitted) {
                e.preventDefault();
                return false;
            }
            
            submitted = true;
            
            setTimeout(() => {
                submitted = false;
            }, 3000);
        });
    });
}

/**
 * Console log personalizado
 */
console.log('%c¡Bienvenido a TecnoFutura Academy! 🚀', 'color: #0d6efd; font-size: 20px; font-weight: bold;');
console.log('%c¿Interesado en el código? Visita nuestros cursos de programación!', 'color: #ffc107; font-size: 14px;');
