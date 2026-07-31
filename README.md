# NailGestor

Sistema profesional de gestión de turnos y servicios de manicura. Desarrollado con PHP nativo, MySQL y CSS — siguiendo la arquitectura MVC.

## Estructura MVC

```
AppUñas/
├── index.php                       # Front controller (punto de entrada)
├── env.php                         # Variables de entorno (NO subir a git)
├── database/
│   └── schema.sql                  # Esquema de base de datos + datos iniciales
├── app/
│   ├── config/
│   │   ├── config.php               # Configuración general
│   │   ├── database.php             # Conexión PDO a MySQL
│   │   └── notificaciones.php       # Configuración de notificaciones
│   ├── core/
│   │   ├── Controller.php           # Controlador base (vistas + redirecciones)
│   │   ├── Seguridad.php            # Utilidades de seguridad (hash, validaciones, etc.)
│   │   └── NotificacionWhatsApp.php # Envío de notificaciones por WhatsApp
│   ├── controllers/
│   │   ├── PublicController.php     # Controlador de vista pública
│   │   └── AdminController.php      # Controlador del panel admin
│   ├── models/
│   │   ├── Admin.php                # Modelo de administradores
│   │   ├── Servicio.php             # Modelo de servicios
│   │   ├── Cliente.php              # Modelo de clientes
│   │   ├── Turno.php                # Modelo de turnos
│   │   ├── Trabajo.php              # Modelo de trabajos realizados
│   │   └── HorarioDisponible.php    # Modelo de horarios disponibles
│   └── views/
│       ├── layout.php               # Template principal (header + footer)
│       ├── public/
│       │   ├── inicio.php           # Vista pública (servicios + reserva)
│       │   └── reservar.php         # Formulario de reserva de turno
│       └── admin/
│           ├── login.php            # Vista de login
│           ├── registro.php         # Vista de registro de administrador
│           ├── turnos.php           # Vista de gestión de turnos
│           ├── editar_turno.php     # Vista de edición de turno
│           ├── horarios.php         # Vista de gestión de horarios disponibles
│           └── trabajos.php         # Vista de galería de trabajos realizados
└── public/
    ├── css/
    │   └── estilos.css              # Hoja de estilos profesional
    ├── uploads/
    │   └── trabajos/                # Imágenes subidas de trabajos realizados
    ├── logo.jpg
    ├── favicon.ico / favicon-16.png / favicon-32.png / apple-touch-icon.png
```

## Instalación

1. **Importar la base de datos:**

```sql
SOURCE database/schema.sql;
```

También podés importar `database/schema.sql` desde phpMyAdmin.

2. **Configurar variables de entorno:**

Completá `env.php` con tus propios datos (credenciales de base de datos, SMTP, etc.). Este archivo es sensible: no debe subirse al repositorio ni compartirse públicamente (ya está contemplado en `.gitignore`).

3. **Revisar los datos de conexión** en `app/config/database.php` y ajustarlos a tu entorno local o de hosting.

4. **Crear el usuario administrador:**

El acceso al panel admin se crea desde la vista de registro (`app/views/admin/registro.php`) o directamente en la base de datos, usando una clave propia y segura — nunca una clave por defecto conocida.

5. **Abrir la aplicación:**

```
http://localhost/AppUñas/
```

## Panel admin

```
URL: http://localhost/AppUñas/?controlador=admin&accion=login
```

El usuario y la clave se definen al crear el administrador (ver `app/models/Admin.php`). Por seguridad, **este README nunca debe incluir credenciales reales** — evitá dejar usuarios o claves de prueba en el código antes de publicar el sitio.

## Funcionalidades

- **Vista pública**: listado de servicios con precios y duración, formulario de reserva de turnos
- **Panel admin**:
  - Gestión de turnos (pendiente, confirmado, cancelado, completado)
  - Gestión de horarios disponibles
  - Registro y galería de trabajos realizados
  - Notificaciones automáticas por WhatsApp/Email
  - Dashboard con estadísticas

## Tecnologías

- PHP nativo (PDO con consultas preparadas)
- MySQL (InnoDB con claves foráneas)
- CSS vanilla con diseño profesional dark mode
- Google Fonts (Inter + Playfair Display)

## Buenas prácticas de seguridad

- No subir `env.php` ni ningún archivo con credenciales reales al control de versiones.
- Cambiar cualquier clave por defecto antes de pasar a producción.
- Usar contraseñas de aplicación (no la contraseña normal) para el envío de correos vía Gmail/SMTP.
- Mantener `.htaccess` y permisos de carpetas como `public/uploads/` restringidos a lo estrictamente necesario.
