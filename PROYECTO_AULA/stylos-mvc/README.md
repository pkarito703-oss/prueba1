# Salon Stylos Madys - Estructura MVC

## Estructura de carpetas

```
E-comerce-stylos/
├── public/                         ← Único punto de acceso desde el navegador
│   ├── index.php                   ← Página principal
│   ├── login.php                   ← Vista de login/registro
│   ├── citas.php                   ← Vista reservar cita
│   ├── logout.php                  ← Cerrar sesión
│   ├── conexion.php                ← Bridge de compatibilidad (apunta a src/config)
│   ├── procesar_login.php          ← Usa AuthController
│   ├── procesar_registro.php       ← Usa AuthController
│   ├── procesar_cita.php           ← Usa CitaController
│   ├── admin/
│   │   ├── dashboard.php           ← Usa CitaController + UsuarioController
│   │   ├── citas_admin.php         ← Usa CitaController
│   │   └── usuarios_admin.php      ← Usa UsuarioController
│   ├── assets/                     ← CSS, JS, imágenes, vendors
│   └── forms/
│
├── src/
│   ├── config/
│   │   └── conexion.php            ← ✅ Conexión real a la BD (segura aquí)
│   ├── controllers/
│   │   ├── AuthController.php      ← Login, registro, logout
│   │   ├── CitaController.php      ← CRUD de citas
│   │   └── UsuarioController.php   ← CRUD de usuarios
│   ├── models/
│   │   ├── Usuario.php             ← Modelo de usuario
│   │   └── Cita.php                ← Modelo de cita
│   └── views/
│       └── layouts/
│           └── header_admin.php    ← Sidebar reutilizable del admin
│
├── database/                       ← (pendiente) Scripts SQL
├── .env                            ← Variables de entorno
├── docker-compose.yml
└── dockerfile
```

## Pendiente por implementar
- Modelo y páginas de Servicios (admin)
- Modelo y páginas de Productos (admin)  
- Página de perfil de usuario
- Recuperar contraseña
- Notificaciones por email al reservar cita
