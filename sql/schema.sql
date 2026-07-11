-- Pasanaku — Schema
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS `admins` (
  `id`            INT PRIMARY KEY AUTO_INCREMENT,
  `email`         VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `nombre`        VARCHAR(255) NOT NULL DEFAULT 'Administrador',
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `personas` (
  `id`         INT PRIMARY KEY AUTO_INCREMENT,
  `nombre`     VARCHAR(255) NOT NULL,
  `telefono`   VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pasanakus` (
  `id`                  INT PRIMARY KEY AUTO_INCREMENT,
  `nombre`              VARCHAR(255) NOT NULL,
  `monto_contribucion`  DECIMAL(10,2) NOT NULL,
  `periodo`             ENUM('semanal','quincenal','mensual') NOT NULL DEFAULT 'mensual',
  `fecha_inicio`        DATE NOT NULL,
  `estado`              ENUM('activo','finalizado') NOT NULL DEFAULT 'activo',
  `created_at`          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pasanaku_participantes` (
  `id`          INT PRIMARY KEY AUTO_INCREMENT,
  `pasanaku_id` INT NOT NULL,
  `persona_id`  INT NOT NULL,
  `orden`       INT NOT NULL DEFAULT 1,
  `activo`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pasanaku_id`) REFERENCES `pasanakus`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`persona_id`)  REFERENCES `personas`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pagos` (
  `id`              INT PRIMARY KEY AUTO_INCREMENT,
  `pasanaku_id`     INT NOT NULL,
  `participante_id` INT NOT NULL,
  `ronda`           INT NOT NULL,
  `monto`           DECIMAL(10,2) NOT NULL,
  `fecha_pago`      DATE NOT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pasanaku_id`)     REFERENCES `pasanakus`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`participante_id`) REFERENCES `pasanaku_participantes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `entregas` (
  `id`              INT PRIMARY KEY AUTO_INCREMENT,
  `pasanaku_id`     INT NOT NULL,
  `participante_id` INT NOT NULL,
  `ronda`           INT NOT NULL,
  `fecha_entrega`   DATE NOT NULL,
  `notas`           TEXT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pasanaku_id`)     REFERENCES `pasanakus`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`participante_id`) REFERENCES `pasanaku_participantes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
