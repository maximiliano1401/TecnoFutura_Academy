# 🚀 TecnoFutura Academy - Landing Page

Landing page profesional para la plataforma LMS especializada en Arduino, electrónica y programación de bajo nivel.

## 📋 Descripción

TecnoFutura Academy es una plataforma educativa que ofrece cursos desde nivel básico hasta avanzado sobre:
- Arduino y electrónica
- Programación de microcontroladores
- Lenguaje ensamblador
- Sistemas embebidos
- IoT (Internet de las Cosas)

## 🗂️ Estructura del Proyecto

```
TecnoFutura_Academy/
├── index.php                 # Landing page principal
├── README.md                 # Este archivo
│
├── css/
│   └── styles.css           # Estilos personalizados
│
├── js/
│   └── main.js              # JavaScript interactivo
│
├── img/
│   ├── TecnoFutura_Academy-logo.png  # Logo de la plataforma
│   └── README.md            # Instrucciones sobre imágenes
│
├── includes/
│   ├── config.php           # Configuración general
│   ├── header.php           # Header modular
│   └── footer.php           # Footer modular
│
├── admin/
│   └── index.php            # Panel administrativo (en desarrollo)
│
└── lms/
    └── index.php            # Plataforma LMS (en desarrollo)
```

## 🛠️ Tecnologías Utilizadas

- **HTML5**: Estructura semántica
- **CSS3**: Estilos personalizados con animaciones
- **Bootstrap 5**: Framework CSS responsive
- **JavaScript Vanilla**: Interactividad
- **PHP**: Backend y estructura modular
- **Bootstrap Icons**: Iconografía

## 📚 Cursos Ofrecidos

### Nivel Básico
1. **Introducción a Arduino** - Fundamentos de electrónica y Arduino
2. **Arduino Desde Cero** - Programación básica y estructuras de control
3. **Programación Aritmética y Lógica** - Operaciones matemáticas aplicadas

### Nivel Intermedio
4. **Fundamentos de Arquitectura de Computadoras** - Comprensión del hardware
5. **Introducción al Lenguaje Ensamblador** - Programación de bajo nivel
6. **Arduino Intermedio: Sensores y Actuadores** - Proyectos con hardware

### Nivel Avanzado
7. **Programación en Ensamblador Aplicada** - Optimización de código
8. **Sistemas Embebidos con Arduino** - IoT y domótica avanzada

## 💰 Paquetes de Precios

- **Curso Individual**: $499 MXN
- **Paquete Completo**: $3,499 MXN (30% descuento)
- **Diplomado Completo**: $4,999 MXN (incluye certificación y kit Arduino)

## 🚀 Instalación y Uso

### Requisitos Previos
- Servidor web (Apache)
- PHP 7.4 o superior
- Navegador web moderno

### Instalación Local (XAMPP)

1. **Clonar o descargar el proyecto**
   ```bash
   cd c:\xampp\htdocs\
   git clone [URL_DEL_REPOSITORIO] TecnoFutura_Academy
   ```

2. **Agregar el logo**
   - Coloca tu archivo `TecnoFutura_Academy-logo.png` en la carpeta `/img/`
   - Formato recomendado: 200x200px (cuadrado)

3. **Iniciar el servidor**
   - Abre XAMPP Control Panel
   - Inicia Apache
   - Accede a: `http://localhost/TecnoFutura_Academy`

4. **Configurar (opcional)**
   - Edita `/includes/config.php` para personalizar:
     - URL del sitio
     - Información de contacto
     - Redes sociales

## 🎨 Personalización

### Cambiar Colores
Edita las variables CSS en `/css/styles.css`:
```css
:root {
    --primary-color: #0d6efd;
    --warning-color: #ffc107;
    --success-color: #198754;
    /* ... más colores */
}
```

### Agregar Más Cursos
Edita la sección de cursos en `index.php` y copia la estructura de las tarjetas existentes.

### Modificar Precios
Actualiza la sección de paquetes en `index.php`.

## 📱 Características

- ✅ Diseño 100% responsive (móvil, tablet, desktop)
- ✅ Animaciones suaves y modernas
- ✅ Smooth scroll entre secciones
- ✅ Formulario de contacto validado
- ✅ Botón flotante de WhatsApp
- ✅ Botón para volver arriba
- ✅ Navegación activa según scroll
- ✅ Optimizado para SEO
- ✅ Carga rápida y ligera
- ✅ Compatible con todos los navegadores

## 🔧 Próximas Funcionalidades

### Sistema LMS (En desarrollo)
- Plataforma de cursos interactivos
- Simulador Arduino integrado
- Sistema de evaluaciones
- Certificados digitales
- Foros y comunidad

### Panel Admin (En desarrollo)
- Gestión de usuarios
- Administración de cursos
- Estadísticas y reportes
- Sistema de pagos

## 📞 Contacto

- **Email**: contacto@tecnofutura.academy
- **Teléfono**: +52 123 456 7890
- **Ubicación**: Ciudad de México, México

## 📄 Licencia

Este proyecto es propiedad de TecnoFutura Academy. Todos los derechos reservados © 2026.

## 👨‍💻 Desarrollo

Desarrollado con ❤️ por el equipo de TecnoFutura Academy.

---

## 📝 Notas Importantes

1. **Logo**: El archivo `TecnoFutura_Academy-logo.png` debe estar en la carpeta `/img/`
2. **Configuración**: Revisa y actualiza `/includes/config.php` con tu información
3. **Testing**: Prueba la landing page en diferentes dispositivos y navegadores
4. **Email**: El formulario de contacto actualmente solo simula el envío. Requiere configuración de backend para enviar emails reales.

## 🐛 Solución de Problemas

### El logo no se muestra
- Verifica que el archivo exista en `/img/TecnoFutura_Academy-logo.png`
- Confirma que el nombre del archivo sea exactamente `TecnoFutura_Academy-logo.png`

### Los estilos no se cargan
- Verifica la ruta en `config.php`
- Asegúrate de que Apache esté corriendo

### El formulario no envía
- El formulario actualmente solo valida en el frontend
- Para enviar emails reales, implementa el backend en PHP

## 🎯 Roadmap

- [ ] Implementar backend para formulario de contacto
- [ ] Desarrollar sistema LMS completo
- [ ] Crear panel administrativo
- [ ] Integrar pasarela de pagos
- [ ] Agregar sistema de autenticación
- [ ] Implementar base de datos
- [ ] Crear API REST
- [ ] Agregar modo oscuro

---

**¡Gracias por usar TecnoFutura Academy!** 🚀
