# HLC - Sistema de Incidencias (MVC PHP puro)

## Requisitos
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10+
- Apache con mod_rewrite (recomendado)

## Instalacion rapida
1. Crea la BD y tablas:
   - Importa `database.sql` en tu MySQL (phpMyAdmin).
2. Configura la conexion:
   - Edita `app/config/config.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS).
3. Sirve el proyecto apuntando el DocumentRoot a `public/`.
   - Si usas XAMPP: crea un VirtualHost o copia al htdocs y entra a `http://localhost/hlc-incidencias/public/`.

## Usuarios y roles
- Por defecto, al registrarse el rol es `USUARIO`.
- Para soporte, cambia el rol en BD a `SOPORTE` o `ADMIN`.

## Rutas
- Login: `/auth/login`
- Registro: `/auth/register`
- Dashboard: `/home`
- Nuevo incidente: `/incidentes/nuevo`
- Mis incidentes: `/incidentes/mis`
- Bandeja soporte: `/admin/bandeja`

"# hlc_incidencias" 


## Variables de entorno (.env)

Copia `.env.example` a `.env` y completa tus credenciales:

- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `BASE_URL` (ej: `/hlc_incidencias`)
- `RENIEC_TOKEN` (si usas el proxy RENIEC)

El archivo `.env` está ignorado por Git.
