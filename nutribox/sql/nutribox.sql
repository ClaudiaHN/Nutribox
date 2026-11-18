-- =====================================================================
--  Nutribox · Sistema Web v1.0 (MVP académico) · Tecnologías E-Business · UNFV
--  Script importable en phpMyAdmin (MySQL 8 / MariaDB 10.4+)
--  Modelo: tres tablas de negocio — producto, cliente, pedido (sección 10 del documento).
--  El administrador NO se guarda en la base de datos: usuario y hash viven en config/config.php.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `nutribox`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nutribox`;

SET NAMES utf8mb4;
SET time_zone = '-05:00';

DROP TABLE IF EXISTS `pedido`;
DROP TABLE IF EXISTS `cliente`;
DROP TABLE IF EXISTS `producto`;

-- ---------------------------------------------------------------------
--  Tabla producto (cuatro cajas; RN08: solo activo = 1 se muestra y se pide)
-- ---------------------------------------------------------------------
CREATE TABLE `producto` (
  `id_producto`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nombre`                 VARCHAR(60)   NOT NULL,
  `objetivo`               VARCHAR(20)   NOT NULL COMMENT 'Proteína, Energía, Bajo en azúcar, Premium',
  `descripcion`            VARCHAR(300)  NOT NULL COMMENT 'Contenido de la caja, sin promesas de salud',
  `precio`                 DECIMAL(8,2)  NOT NULL,
  `imagen`                 VARCHAR(200)  NULL,
  `activo`                 TINYINT(1)    NOT NULL DEFAULT 1,
  `porcion_referencia`     VARCHAR(150)  NOT NULL,
  `proteinas_g`            DECIMAL(7,2)  NULL COMMENT 'Gramos por porción; NULL si no se cuenta con el dato',
  `energia_kcal`           DECIMAL(8,2)  NULL,
  `hierro_mg`              DECIMAL(7,2)  NULL,
  `azucares_g`             DECIMAL(7,2)  NULL,
  `fuente_nutricional`     VARCHAR(300)  NOT NULL,
  `datos_verificados`      TINYINT(1)    NOT NULL DEFAULT 0,
  `perfil_sugerido`        VARCHAR(30)   NOT NULL DEFAULT 'Ninguno' COMMENT 'Ninguno, Proteína, Control de porciones, Aporte de hierro',
  `recomendacion_validada` TINYINT(1)    NOT NULL DEFAULT 0 COMMENT 'Requiere revisión por profesional de nutrición',
  `alergenos`              VARCHAR(200)  NOT NULL,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `uq_producto_nombre` (`nombre`),
  KEY `ix_producto_activo` (`activo`),
  CONSTRAINT `ck_producto_precio` CHECK (`precio` > 0),
  CONSTRAINT `ck_producto_objetivo` CHECK (`objetivo` IN ('Proteína','Energía','Bajo en azúcar','Premium')),
  CONSTRAINT `ck_producto_perfil` CHECK (`perfil_sugerido` IN ('Ninguno','Proteína','Control de porciones','Aporte de hierro'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Tabla cliente (RN06: el correo identifica al cliente; sin cuenta ni contraseña)
-- ---------------------------------------------------------------------
CREATE TABLE `cliente` (
  `id_cliente`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombres`        VARCHAR(100) NOT NULL,
  `telefono`       CHAR(9)      NOT NULL COMMENT '9 dígitos, empieza con 9',
  `correo`         VARCHAR(120) NOT NULL,
  `distrito`       VARCHAR(60)  NOT NULL,
  `direccion`      VARCHAR(200) NOT NULL,
  `fecha_registro` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `uq_cliente_correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Tabla pedido (RN01–RN05; el id se muestra como NB-0001)
-- ---------------------------------------------------------------------
CREATE TABLE `pedido` (
  `id_pedido`       INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `id_cliente`      INT UNSIGNED  NOT NULL,
  `id_producto`     INT UNSIGNED  NOT NULL,
  `cantidad`        TINYINT UNSIGNED NOT NULL,
  `precio_unitario` DECIMAL(8,2)  NOT NULL COMMENT 'Copia del precio al registrar (RN03)',
  `total`           DECIMAL(10,2) NOT NULL COMMENT 'precio_unitario × cantidad (RN02)',
  `observaciones`   VARCHAR(300)  NULL,
  `acepta_datos`    TINYINT(1)    NOT NULL,
  `estado`          VARCHAR(12)   NOT NULL DEFAULT 'Pendiente',
  `fecha_registro`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pedido`),
  KEY `ix_pedido_estado_fecha` (`estado`, `fecha_registro`),
  KEY `fk_pedido_cliente` (`id_cliente`),
  KEY `fk_pedido_producto` (`id_producto`),
  CONSTRAINT `fk_pedido_cliente`  FOREIGN KEY (`id_cliente`)  REFERENCES `cliente`  (`id_cliente`)  ON DELETE RESTRICT,
  CONSTRAINT `fk_pedido_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE RESTRICT,
  CONSTRAINT `ck_pedido_cantidad` CHECK (`cantidad` BETWEEN 1 AND 10),
  CONSTRAINT `ck_pedido_estado`   CHECK (`estado` IN ('Pendiente','Confirmado','Entregado','Cancelado')),
  CONSTRAINT `ck_pedido_acepta`   CHECK (`acepta_datos` = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Catálogo inicial: cuatro cajas (sección 4 del documento).
--  Los valores nutricionales son FICTICIOS DE DEMOSTRACIÓN (datos_verificados = 0).
--  Mientras recomendacion_validada = 0 el sistema no emite sugerencias específicas de salud (RN11, RN12).
--  La Caja Energía deja hierro_mg en NULL para demostrar el manejo de "dato no disponible" (RF08).
-- ---------------------------------------------------------------------
INSERT INTO `producto`
 (`nombre`, `objetivo`, `descripcion`, `precio`, `imagen`, `activo`, `porcion_referencia`,
  `proteinas_g`, `energia_kcal`, `hierro_mg`, `azucares_g`, `fuente_nutricional`, `datos_verificados`,
  `perfil_sugerido`, `recomendacion_validada`, `alergenos`)
VALUES
 ('Caja Proteína', 'Proteína',
  'Mix de almendras y maní tostado, barra de proteína de avena, yogur griego natural en polvo para preparar y chips de garbanzo.',
  65.00, 'img/cajas/caja-proteina.svg', 1,
  '1 porción = 30 g de mix de frutos secos + 1 barra de 40 g',
  14.50, 310.00, 2.10, 6.00, 'Datos ficticios de demostración', 0,
  'Proteína', 0, 'Frutos secos (almendra, maní), leche, gluten (avena)'),

 ('Caja Energía', 'Energía',
  'Granola artesanal, pasas y arándanos secos, barra de dátiles con cacao y plátano deshidratado.',
  55.00, 'img/cajas/caja-energia.svg', 1,
  '1 porción = 40 g de granola + 25 g de frutas secas',
  6.00, 285.00, NULL, 18.00, 'Datos ficticios de demostración', 0,
  'Ninguno', 0, 'Gluten (avena); puede contener trazas de frutos secos'),

 ('Caja Equilibrio', 'Bajo en azúcar',
  'Chips de camote al horno, semillas de zapallo, galletas de arroz integral y manzana deshidratada sin azúcar añadida.',
  60.00, 'img/cajas/caja-equilibrio.svg', 1,
  '1 porción = 30 g de chips + 20 g de semillas',
  7.20, 230.00, 2.80, 3.50, 'Datos ficticios de demostración', 0,
  'Control de porciones', 0, 'Semillas de zapallo; elaborado en planta que procesa frutos secos'),

 ('Caja Premium', 'Premium',
  'Selección especial: nueces pecanas, chocolate 70 % cacao, mantequilla de maní natural, quinua pop con cacao y castañas.',
  95.00, 'img/cajas/caja-premium.svg', 1,
  '1 porción = 35 g de frutos secos + 20 g de chocolate 70 %',
  9.80, 340.00, 3.40, 8.00, 'Datos ficticios de demostración', 0,
  'Aporte de hierro', 0, 'Frutos secos (pecana, maní, castaña), soya (lecitina), puede contener leche');

-- ---------------------------------------------------------------------
--  Cómo habilitar una sugerencia específica de salud cuando un profesional de nutrición
--  haya revisado la ficha de una caja (hacerlo desde phpMyAdmin, ejemplo):
--  UPDATE producto SET datos_verificados = 1, recomendacion_validada = 1,
--         fuente_nutricional = 'Ficha de composición revisada por Lic. ___ el 2026-11-__'
--   WHERE id_producto = 4;
-- ---------------------------------------------------------------------
