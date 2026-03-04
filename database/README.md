# Sistema de Autenticación - TecnoFutura Academy

## 📋 Descripción

Sistema completo de autenticación y gestión de usuarios con roles diferenciados (ADMIN, PROFESOR, USUARIO) para la plataforma educativa TecnoFutura Academy.

## 🗄️ Instalación de la Base de Datos

### Paso 1: Crear la Base de Datos

1. Abre **phpMyAdmin** o tu gestor de base de datos MySQL
2. Importa el archivo: `database/tecnofutura_academy.sql`

**Opción alternativa usando línea de comandos:**

```bash
mysql -u root -p < database/tecnofutura_academy.sql
```

### Paso 2: Configurar la Conexión

Edita el archivo `backend/config/database.php` con tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tecnofutura_academy');
define('DB_USER', 'root');           // Tu usuario de MySQL
define('DB_PASS', '');                // Tu contraseña de MySQL
```

## 👥 Estructura de Usuarios y Roles

### Roles del Sistema

1. **ADMIN** - Administrador
   - Gestión de usuarios
   - Gestión de roles y permisos
   - Alta/baja de cursos
   - Gestión de certificados
   - Gestión de pagos
   - Dashboard administrativo
   - Estado de alumnos en cursos

2. **PROFESOR** - Docente
   - Gestión de calificaciones
   - Contenido y materiales del curso
   - Estado de alumnos en sus cursos
   - Datos adicionales: cédula profesional, institución

3. **USUARIO** - Alumno
   - Acceso a cursos inscritos
   - Vista previa de cursos disponibles
   - Consulta de calificaciones
   - Datos adicionales: matrícula (auto-generada)

### Datos Personales por Tipo

**Alumno:**
- Nombre completo
- Fecha de nacimiento
- Correo electrónico
- Matrícula (generada automáticamente con formato: AAAA######)
- Contraseña

**Docente:**
- Nombre completo
- Fecha de nacimiento
- Cédula profesional
- Correo electrónico
- Institución u organización de procedencia
- Contraseña

## 🚀 Uso del Sistema

### Registro de Usuarios

**URL:** `/register.php`

1. Visita la página de registro
2. Selecciona el tipo de usuario:
   - **Alumno**: Requiere datos básicos
   - **Docente**: Requiere datos básicos + cédula profesional
3. Completa el formulario
4. Acepta los términos y condiciones
5. Haz clic en "Crear Cuenta"

### Inicio de Sesión

**URL:** `/login.php`

1. Ingresa tu correo electrónico
2. Ingresa tu contraseña
3. Haz clic en "Iniciar Sesión"

**Redirección según rol:**
- ADMIN → `/admin/index.php`
- PROFESOR → `/lms/index.php`
- USUARIO → `/lms/index.php`

### Usuario Administrador por Defecto

El sistema crea automáticamente un usuario administrador:

```
Correo: admin@tecnofutura.academy
Contraseña: admin123
```

⚠️ **IMPORTANTE:** Cambia la contraseña en producción.

## 📁 Estructura de Archivos

```
TecnoFutura_Academy/
├── backend/
│   ├── config/
│   │   ├── config.php          # Configuración general
│   │   └── database.php        # Configuración de BD
│   ├── classes/
│   │   ├── Database.php        # Conexión a BD (Singleton)
│   │   └── Usuario.php         # Modelo de Usuario
│   └── auth/
│       ├── login.php           # Procesar login
│       ├── register.php        # Procesar registro
│       ├── logout.php          # Cerrar sesión
│       └── middleware.php      # Verificación de autenticación
├── database/
│   └── tecnofutura_academy.sql # Script de base de datos
├── login.php                   # Página de inicio de sesión
├── register.php                # Página de registro
├── admin/
│   └── index.php              # Panel de administración (solo ADMIN)
└── lms/
    └── index.php              # Dashboard de estudiantes/profesores
```

## 🔐 Seguridad

- Las contraseñas se almacenan usando `password_hash()` de PHP
- Validación de sesiones en todas las páginas protegidas
- Middleware de autenticación y autorización
- Prevención de SQL Injection usando PDO y prepared statements
- Validación de datos en cliente y servidor

## 📊 Base de Datos

### Tablas Principales

1. **usuarios** - Información básica de todos los usuarios
2. **alumnos** - Datos específicos de estudiantes
3. **docentes** - Datos específicos de profesores
4. **roles** - Define los roles del sistema
5. **cursos** - Catálogo de cursos
6. **inscripciones** - Registro de alumnos en cursos
7. **calificaciones** - Calificaciones de alumnos
8. **certificados** - Certificados emitidos
9. **pagos** - Registro de pagos
10. **permisos_rol** - Permisos específicos por rol

### Triggers Automáticos

- **generar_matricula**: Genera automáticamente la matrícula de alumnos con formato AAAA######

### Vistas Creadas

- **vista_alumnos**: Información completa de alumnos
- **vista_docentes**: Información completa de docentes
- **vista_cursos**: Cursos con información del docente
- **vista_inscripciones**: Inscripciones con detalles

## 🛠️ Características Técnicas

### Backend
- PHP 7.4+
- PDO para conexión a base de datos
- Patrón Singleton para Database
- Arquitectura MVC simplificada
- Separación de lógica de negocio

### Frontend
- Bootstrap 5.3
- Bootstrap Icons
- JavaScript vanilla (Fetch API)
- Diseño responsive
- Animaciones CSS

### Seguridad
- Sesiones PHP para autenticación
- Middleware de autorización
- Validación de datos
- Protección contra SQL Injection
- Hash de contraseñas con bcrypt

## 📱 Funcionalidades por Rol

### Dashboard ADMIN
- Estadísticas del sistema
- Gestión de usuarios
- Gestión de cursos
- Gestión de certificados
- Gestión de pagos
- Reportes

### Dashboard PROFESOR
- Cursos asignados
- Lista de alumnos
- Gestión de calificaciones
- Materiales del curso
- Estadísticas de progreso

### Dashboard USUARIO (Alumno)
- Cursos inscritos
- Progreso en cursos
- Calificaciones
- Certificados obtenidos
- Promedio general

## 🔄 Próximas Características

- [ ] Módulo completo de gestión de cursos
- [ ] Sistema de calificaciones funcional
- [ ] Generación de certificados
- [ ] Pasarela de pagos
- [ ] Sistema de mensajería
- [ ] Foros de discusión
- [ ] Notificaciones
- [ ] Reportes avanzados

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verifica que MySQL esté corriendo
- Confirma las credenciales en `backend/config/database.php`
- Asegúrate de que la base de datos existe

### No puedo iniciar sesión
- Verifica que el usuario esté activo en la BD
- Confirma que la contraseña sea correcta
- Revisa que la sesión PHP esté habilitada

### Las rutas no funcionan
- Asegúrate de estar ejecutando el servidor PHP
- Verifica que las rutas relativas sean correctas

## 👨‍💻 Desarrollo

Para ejecutar el servidor de desarrollo:

```bash
php -S localhost:8000
```

Luego visita: `http://localhost:8000`

## 📝 Licencia

Proyecto educativo - TecnoFutura Academy 2026

---

**Desarrollado por:** David Castro  
**Fecha:** 26 de febrero de 2026  
**Asignatura:** Ensamblador - 8vo Cuatrimestre
