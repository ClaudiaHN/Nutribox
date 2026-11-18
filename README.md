# Nutribox — Sistema Web v1.0 (MVP académico)

Curso **Tecnologías E-Business** · UNFV · Metodología Scrum.
Catálogo de cuatro cajas de snacks saludables, balance diario, sugerencias orientativas por reglas fijas,
registro de pedidos (una caja, 1 a 10 unidades) y panel interno para gestionar su estado.

Stack: **PHP 8 + PDO MySQL · MySQL/MariaDB (phpMyAdmin) · Bootstrap 5 local · JavaScript**. Sin framework ni API externa.

## Ejecución local (XAMPP / Laragon / WAMP)

1. Copia la carpeta `nutribox/` dentro de `htdocs/` (XAMPP) o `www/` (Laragon/WAMP).
2. Entra a phpMyAdmin (`http://localhost/phpmyadmin`) → **Importar** → elige `sql/nutribox.sql` → **Continuar**.
   El script crea la base `nutribox`, las tres tablas (`producto`, `cliente`, `pedido`) y carga las cuatro cajas.
3. Revisa `config/config.php` (ya viene listo para XAMPP: `root` sin contraseña, base `nutribox`).
   Si tu MySQL tiene otra contraseña o puerto, cámbialo ahí.
4. Abre `http://localhost/nutribox/`.

Acceso al panel interno: `http://localhost/nutribox/admin/login.php` → usuario **admin** · contraseña **Nutribox2026**.

> Para cambiar la contraseña del administrador genera un hash nuevo y pégalo en `config/config.php`:
> `php -r "echo password_hash('MiClave', PASSWORD_DEFAULT);"`

Alternativa sin Apache (solo PHP instalado): `php -S localhost:8000 -t nutribox` y abre `http://localhost:8000/`.

## Mapa de páginas

| Ruta | Página | RF |
|---|---|---|
| `/index.php` | Inicio | RF01 |
| `/catalogo.php` | Catálogo de cajas | RF02 |
| `/pedido.php?caja=<id>` | Formulario de pedido (también sin `caja`: lista desplegable) | RF03, RF04 |
| `/confirmacion.php` | Confirmación del pedido recién creado (vía sesión) | RF05 |
| `/orientacion.php` | Balance diario y sugerencias en una sola página | RF08, RF09 |
| `/admin/login.php`, `/admin/pedidos.php`, `/admin/logout.php` | Login, gestión de pedidos y salida | RF06, RF07 |

## Estructura

```
nutribox/
├── index.php · catalogo.php · pedido.php · confirmacion.php · orientacion.php
├── admin/            login.php · pedidos.php · logout.php
├── includes/         init.php (config, PDO, sesión, CSRF, helpers) · header.php · footer.php
│                     productos.php (acceso a datos) · auth_admin.php (protección de la vista interna)
├── config/           config.php (local, ignorado por git) · config.example.php (plantilla) · .htaccess (denegado)
├── assets/           css/bootstrap.min.css · css/estilos.css · js/bootstrap.bundle.min.js · js/pedido.js · js/orientacion.js
├── img/              logo, fondos e ilustraciones de las cajas (img/cajas/*.svg)
├── sql/nutribox.sql  script importable en phpMyAdmin
└── README.md
```

## Reglas implementadas

- **RN01–RN04**: una caja por pedido, cantidad 1–10, total = precio × cantidad, precio copiado al pedido, estado inicial *Pendiente*.
- **RN05**: transiciones `Pendiente → Confirmado → Entregado`; `Pendiente/Confirmado → Cancelado`; los estados finales no cambian (validado en servidor).
- **RN06**: el correo identifica al cliente; si ya existe se reutiliza y se actualizan teléfono y dirección.
- **RN08**: solo productos `activo = 1` se listan y se pueden pedir.
- **RN09–RN12**: balance y sugerencias se calculan en el navegador (`assets/js/orientacion.js`); peso, talla y condición declarada
  no se envían al servidor ni se guardan. Las sugerencias específicas de salud solo se emiten para cajas con
  `recomendacion_validada = 1`; con datos ficticios se informa la limitación.
- **RNF02/RNF03**: validación campo a campo conservando lo escrito; `password_hash`/`password_verify`, regeneración de sesión,
  token CSRF en todos los formularios de escritura, consultas preparadas y salida escapada.

## Mantenimiento del catálogo

Se hace en phpMyAdmin sobre la tabla `producto` (no hay CRUD en la web, por alcance). Campos clave:
`activo`, `precio`, `porcion_referencia`, `proteinas_g`, `energia_kcal`, `hierro_mg`, `azucares_g`,
`fuente_nutricional`, `datos_verificados`, `perfil_sugerido`, `recomendacion_validada`, `alergenos`.
Cuando un profesional de nutrición revise la ficha de una caja, pon `datos_verificados = 1` y `recomendacion_validada = 1`
y registra la fuente: a partir de ahí el sistema podrá sugerirla en orientación por IMC o por aporte de hierro.

## Imágenes

Las ilustraciones de `img/cajas/` son vectoriales originales de demostración; reemplázalas por fotos propias o de bancos de uso
libre (Unsplash, Pexels) actualizando la columna `imagen` en `producto`.
