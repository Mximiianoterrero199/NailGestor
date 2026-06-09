# NailGestor

Sistema profesional de gestión de turnos y servicios de manicura. Desarrollado con PHP nativo, MySQL y CSS — siguiendo la arquitectura MVC.

## Estructura MVC

```
AppUñas/
├── index.php                    # Front controller (punto de entrada)
├── database/
│   └── schema.sql               # Esquema de base de datos + datos iniciales
├── app/
│   ├── config/
│   │   ├── config.php            # Configuración general
│   │   └── database.php          # Conexión PDO a MySQL
│   ├── core/
│   │   └── Controller.php        # Controlador base (vistas + redirecciones)
│   ├── controllers/
│   │   ├── PublicController.php  # Controlador de vista pública
│   │   └── AdminController.php   # Controlador del panel admin
│   ├── models/
│   │   ├── Servicio.php          # Modelo de servicios
│   │   ├── Cliente.php           # Modelo de clientes
│   │   └── Turno.php             # Modelo de turnos
│   └── views/
│       ├── layout.php            # Template principal (header + footer)
│       ├── public/
│       │   └── inicio.php        # Vista pública (servicios + reserva)
│       └── admin/
│           ├── login.php         # Vista de login
│           └── turnos.php        # Vista de gestión de turnos
└── public/
    └── css/
        └── estilos.css           # Hoja de estilos profesional
```

## Instalación

1. Importar la base de datos:

```sql
SOURCE database/schema.sql;
```

También podés importar `database/schema.sql` desde phpMyAdmin.

2. Revisar los datos de conexión en:

```
app/config/database.php
```

Por defecto usa:

```
Base de datos: nailgestor
Usuario: root
Clave: (vacía)
```

3. Abrir la aplicación:

```
http://localhost/AppUñas/
```

## Panel admin

```
URL: http://localhost/AppUñas/?controlador=admin&accion=login
Usuario: admin
Clave: admin123
```

La clave se puede cambiar en `app/config/config.php`.

## Funcionalidades

- **Vista Pública**: Listado de servicios con precios y duración + formulario de reserva de turnos
- **Panel Admin**: Gestión de turnos (cambio de estado: pendiente, confirmado, cancelado, completado) con dashboard de estadísticas

## Tecnologías

- PHP nativo (PDO con consultas preparadas)
- MySQL (InnoDB con claves foráneas)
- CSS vanilla con diseño profesional dark mode
- Google Fonts (Inter + Playfair Display)
