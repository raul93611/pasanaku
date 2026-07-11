# CLAUDE.md — Pasanaku

## Qué es esta app

Pasanaku es una aplicación web en español para gestionar grupos de ahorro rotativo (ROSCA boliviana). El administrador crea pasanakus, agrega participantes, define el orden de entrega mediante drag & drop, registra pagos por ronda y marca a quién se le entregó el pozo. Soporta múltiples pasanakus activos simultáneamente.

## Stack

| Componente | Versión / Decisión |
|---|---|
| Backend | PHP 8.3 |
| Base de datos | MySQL 8 |
| Frontend | Bootstrap 5.3 + Bootstrap Icons 1.11 |
| Drag & drop | SortableJS 1.15 (CDN) |
| Fuente | Plus Jakarta Sans (Google Fonts) |
| Hosting | Shared hosting (Apache / cPanel) |
| Idioma UI | Español completo |

## Estructura del Proyecto

```
pasanaku/
├── index.php               # Entry point / router (?page=X&action=Y)
├── config/
│   └── database.php        # PDO connection (DB_HOST/DB_NAME/DB_USER/DB_PASS env vars)
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── PasanakuController.php  # CRUD + AJAX: togglePago, reordenar, registrarEntrega
│   ├── PersonaController.php
│   └── AdminController.php     # Perfil + Historial
├── models/
│   ├── Admin.php
│   ├── Persona.php
│   ├── Pasanaku.php
│   ├── Participante.php
│   ├── Pago.php
│   └── Entrega.php
├── views/
│   ├── layout/
│   │   ├── header.php      # Sidebar nav, flash messages, topbar
│   │   └── footer.php      # Scripts (SortableJS, app.js)
│   ├── auth/
│   │   └── login.php
│   ├── dashboard/
│   │   └── index.php
│   ├── pasanaku/
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── detail.php      # 3-column grid: participantes | pagos | entrega
│   ├── personas/
│   │   └── index.php
│   ├── historial/
│   │   └── index.php
│   └── admin/
│       └── perfil.php
├── assets/
│   ├── css/
│   │   └── app.css         # Design system (CSS variables, all components)
│   └── js/
│       └── app.js          # SortableJS init, AJAX, toast, modal helpers
└── sql/
    ├── schema.sql           # CREATE TABLE (IF NOT EXISTS)
    └── seed.sql             # Datos de prueba
```

## Convenciones Clave

- **Routing:** `index.php` recibe todos los requests via `?page=X&action=Y`. Sin mod_rewrite.
- **Autenticación:** `$_SESSION['admin_id']` — todas las páginas excepto login la verifican.
- **Contraseñas:** `password_hash()` / `password_verify()` con `PASSWORD_BCRYPT`.
- **BD:** PDO con prepared statements en todos los queries.
- **Drag & drop:** SortableJS — al soltar llama AJAX a `?page=pasanaku&action=reordenar`.
- **Toggle pagos:** AJAX a `?page=pasanaku&action=togglePago` (JSON response).
- **Entrega:** AJAX a `?page=pasanaku&action=registrarEntrega` (JSON response).
- **Idioma:** UI completamente en español.
- **Colores:** Primario `#2E7D32` (verde), Acento `#F59E0B` (ámbar/dorado).
- **Font:** Plus Jakarta Sans via Google Fonts CDN.

## Diseño

Implementación pixel-perfect del prototipo Pasanaku.html de claude.ai/design.
- Sidebar verde fijo (240px) en desktop, hamburger menu en mobile
- Dashboard: stat pills + grid de cards de pasanaku
- Detalle: 3 columnas (participantes drag&drop | grid pagos | panel entrega)
- Personas: tabla con búsqueda en tiempo real + modales CRUD
- Historial: lista de pasanakus finalizados
- Toast notifications para todas las interacciones AJAX

## Credenciales de Seed

- Email: `admin@pasanaku.com`
- Contraseña: `admin123`

## Configuración de Base de Datos

### Docker local (docker-compose-lamp)
- Host: `lamp-mysql8`
- DB: `pasanaku`
- User: `docker` / Pass: `docker`

### Shared hosting / producción
Editar `config/database.php` o configurar variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

## Setup Inicial

```bash
# 1. Crear la base de datos
mysql -u root -p -e "CREATE DATABASE pasanaku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Importar schema
mysql -u user -p pasanaku < sql/schema.sql

# 3. Importar datos de prueba
mysql -u user -p pasanaku < sql/seed.sql
```

## Restricciones Importantes

- **Sin mod_rewrite** — router funciona con query strings simples
- **Sin Composer** — librerías externas via CDN
- **SortableJS via CDN** — no bundler, no npm
- **Bootstrap 5 via CDN**
- Turno eliminado = `activo = 0` en `pasanaku_participantes`, sin redistribución
- El admin cierra manualmente el pasanaku; no hay cierre automático

## Inventario de Características

| Característica | Estado |
|---|---|
| Login / Sesión PHP | ✅ built |
| Perfil admin (editar email/contraseña) | ✅ built |
| Dashboard con tarjetas resumen | ✅ built |
| Gestión de Personas (CRUD global) | ✅ built |
| Crear / Editar Pasanaku | ✅ built |
| Detalle del Pasanaku | ✅ built |
| Drag & drop de orden de participantes | ✅ built |
| Registro de pagos por ronda (toggle AJAX) | ✅ built |
| Registro de entrega del pozo | ✅ built |
| Eliminar participante (turno saltado) | ✅ built |
| Cierre manual del pasanaku | ✅ built |
| Historial de pasanakus finalizados | ✅ built |
| Notas por ronda (capturadas al registrar entrega) | ✅ built |
| Seed de datos de prueba | ✅ built |
