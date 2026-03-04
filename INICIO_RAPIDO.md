# 🚀 Inicio Rápido - Sistema de Autenticación

## ⚡ Pasos para Empezar

### 1. Importar la Base de Datos

Abre phpMyAdmin y:
1. Crea una nueva base de datos llamada `tecnofutura_academy`
2. Importa el archivo: `database/tecnofutura_academy.sql`

O desde terminal:
```bash
mysql -u root -p < database/tecnofutura_academy.sql
```

### 2. Configurar Credenciales

Edita `backend/config/database.php` si es necesario:
```php
define('DB_USER', 'root');     // Tu usuario MySQL
define('DB_PASS', '');         // Tu contraseña MySQL
```

### 3. Iniciar el Servidor (si no está corriendo)

```bash
php -S localhost:8000
```

### 4. Probar el Sistema

Visita: `http://localhost:8000`

## 🔑 Credenciales de Prueba

### Administrador (Ya creado en la BD)
```
Correo: admin@tecnofutura.academy
Contraseña: admin123
```

## 📄 Páginas Disponibles

- **Inicio**: `http://localhost:8000/`
- **Registro**: `http://localhost:8000/register.php`
- **Login**: `http://localhost:8000/login.php`
- **Dashboard LMS**: `http://localhost:8000/lms/` (requiere login)
- **Admin Panel**: `http://localhost:8000/admin/` (solo ADMIN)

## ✅ Funcionalidades Implementadas

### ✓ Sistema de Base de Datos
- [x] 10 tablas principales
- [x] 3 roles (ADMIN, PROFESOR, USUARIO)
- [x] Trigger para generar matrícula automática
- [x] Vistas para consultas optimizadas
- [x] Permisos por rol

### ✓ Backend (Lógica de Negocio)
- [x] Configuración de BD
- [x] Clase Database (Singleton)
- [x] Clase Usuario completa
- [x] Sistema de autenticación
- [x] Middleware de protección
- [x] Registro de usuarios
- [x] Login/Logout funcional

### ✓ Frontend
- [x] Página de login moderna
- [x] Página de registro con selector de rol
- [x] Header con estado de autenticación
- [x] Dashboard para USUARIO/PROFESOR
- [x] Panel de administración para ADMIN
- [x] Diseño responsive

## 🎯 Datos que se Capturan

### Alumno (USUARIO)
- Nombre completo
- Correo electrónico
- Fecha de nacimiento
- Contraseña
- **Matrícula** (se genera automáticamente: AAAA######)

### Docente (PROFESOR)
- Nombre completo
- Correo electrónico
- Fecha de nacimiento
- **Cédula profesional**
- **Institución de procedencia**
- Contraseña

## 🔐 Permisos por Rol

### ADMIN
- ✅ Gestión de usuarios
- ✅ Gestión de roles
- ✅ Alta/baja de cursos
- ✅ Gestión de certificados
- ✅ Gestión de pagos
- ✅ Dashboard administrativo
- ✅ Estado de alumnos en cursos

### PROFESOR
- ✅ Gestión de calificaciones
- ✅ Contenido y materiales del curso
- ✅ Estado de alumnos en sus cursos

### USUARIO (Alumno)
- ✅ Acceso a cursos
- ✅ Vista previa de cursos
- ✅ Vista de calificaciones

## 🧪 Cómo Probar

1. **Registrar un Alumno:**
   - Ve a `/register.php`
   - Selecciona "Alumno"
   - Completa el formulario
   - La matrícula se genera automáticamente

2. **Registrar un Docente:**
   - Ve a `/register.php`
   - Selecciona "Docente"
   - Completa el formulario incluyendo cédula profesional
   - Agrega institución de procedencia

3. **Login como Admin:**
   - Ve a `/login.php`
   - Usa: `admin@tecnofutura.academy` / `admin123`
   - Serás redirigido a `/admin/`

## ⚠️ Notas Importantes

- El usuario ADMIN ya está creado en la base de datos
- Las contraseñas se guardan hasheadas (bcrypt)
- La matrícula se genera automáticamente para alumnos
- Las rutas están protegidas por middleware
- Las sesiones expiran después de inactividad

## 📞 Soporte

Si encuentras algún problema:
1. Verifica que MySQL esté corriendo
2. Confirma que la BD esté importada
3. Revisa las credenciales en `backend/config/database.php`
4. Verifica que el servidor PHP esté corriendo

---

**¡Todo listo para usar!** 🎉
