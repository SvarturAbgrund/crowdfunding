# Crowdfunding Escolar — Proyecto prototipo

Plataforma de financiamiento colectivo (crowdfunding) donde emprendedores pueden crear campañas para recaudar fondos, y los inversionistas o patrocinadores pueden descubrir y financiar proyectos innovadores. El sistema incluye gestión de campañas, procesamiento básico de pagos en modo de prueba, seguimiento de objetivos y funcionalidades de interacción entre usuarios.

Resumen funcional
-----------------
- **Autenticación por roles:** usuarios pueden registrarse y entrar con roles (emprendedor/owner, inversionista/investor, admin, moderador).
- **Creación y gestión de campañas:** los emprendedores crean, editan y eliminan campañas con título, descripción, meta, categoría e imágenes.
- **Búsqueda y filtrado:** búsqueda por texto y filtrado por categorías en la lista de proyectos.
- **Donaciones (modo de prueba):** procesamiento de donaciones en modo de prueba (simulación de pasarelas como PayPal/Stripe); validaciones del servidor para montos y recompensas.
- **Dashboard:** vista de seguimiento de campañas para emprendedores (actualizable) y paneles administrativos para moderación.
- **Recompensas por niveles (rewards):** gestión de recompensas con `amount` y `quantity`; el sistema valida disponibilidad y reserva mediante transacciones cuando se dona.
- **Moderación y validación:** flujo para revisar y aprobar campañas antes de publicarlas (admin).
- **Registro de actividad:** logging básico de vistas y acciones en `logs/activity.log` para auditoría y análisis.

Requerimientos funcionales principales
------------------------------------
- Autenticación por roles (emprendedor, inversionista, admin, moderador).
- Creación y gestión de campañas de financiamiento.
- Búsqueda y filtrado de proyectos por categoría.
- Procesamiento seguro de donaciones (modo de prueba con PayPal/Stripe).
- Dashboard de seguimiento de campañas en tiempo real.
- Sistema de recompensas por niveles de contribución.
- Validación y moderación de proyectos.

Stack tecnológico
-----------------
- Frontend: HTML5, CSS3, JavaScript.
- Backend: PHP (probar en entorno XAMPP / Apache).
- Base de datos: MySQL (gestión con phpMyAdmin).
- Control de versiones: Git.

Arquitectura
-----------
El proyecto sigue una separación en dos capas simples:
- Capa de presentación: páginas y vistas PHP bajo rutas como `auth/`, `campaigns/`, `admin/`, `includes/`.
- Capa de acceso a datos (Models): código en `models/` que centraliza consultas y lógica transaccional (por ejemplo, `models/Db.php`, `models/Campaign.php`, `models/Reward.php`, `models/Donation.php`).

Archivos y rutas importantes
---------------------------
- `init.sql` — script SQL inicial para crear las tablas y datos de ejemplo.
- `models/` — modelos PHP que encapsulan acceso a datos y transacciones.
- `donate.php` — punto de entrada para procesar donaciones (usa `models/Donation.php`).
- `campaigns/` — vistas para listar, ver, crear y editar campañas.
- `admin/` — panel de administración (usuarios, categorías, mensajes, entregas/fulfillments, moderación).
- `logs/activity.log` — registro de actividad (JSON líneas).

Seguridad y notas técnicas
-------------------------
- Las contraseñas usan `password_hash`/`password_verify`.
- Queries principales usan sentencias preparadas (mysqli) para evitar inyección SQL.
- Reserva de recompensas y decremento de `quantity` se realiza en transacciones con bloqueo para evitar sobreventa.

Integrantes
-----------
- Braulio Raymundo Cantú Ramírez — 22033
- Andrea Guadalupe Gonzalez Flores — 22039
- Lizeth Lázaro Badillo — 22043
- Brayan Jesús Escalante de León — 20983
- Christian Refugio Salvador Martínez — 22052

Estado del proyecto
-------------------
En desarrollo – Este repositorio corresponde a la primera fase (prototipo funcional). Muchas funcionalidades ya están implementadas y refactorizadas hacia una arquitectura con `models/`.

Licencia
--------
Este proyecto es de uso académico y no está destinado a producción.

Siguientes pasos recomendados
----------------------------
- Añadir un visor de logs en `admin/` para revisar `logs/activity.log` desde la interfaz (Quzias).
- Implementar un sistema de migraciones (Phinx / custom) para aplicar cambios de esquema de forma segura.
