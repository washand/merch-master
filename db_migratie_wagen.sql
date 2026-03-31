-- Merch Master — database migratie
-- Uitvoeren via phpMyAdmin → SQL tab

-- 1. Voeg winst_excl en inkoop_totaal toe aan offertes (als die nog niet bestaan)
ALTER TABLE `offertes`
  ADD COLUMN IF NOT EXISTS `inkoop_totaal` DECIMAL(10,2) DEFAULT 0 AFTER `spoed_toeslag`,
  ADD COLUMN IF NOT EXISTS `winst_excl`    DECIMAL(10,2) DEFAULT NULL AFTER `inkoop_totaal`;

-- 2. Wagens tabel aanmaken (als die nog niet bestaat)
CREATE TABLE IF NOT EXISTS `wagens` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `wagen_token` CHAR(32)     NOT NULL,
    `sessie_id`   VARCHAR(128) DEFAULT NULL,
    `klant_id`    INT UNSIGNED DEFAULT NULL,
    `regels`      MEDIUMTEXT   NOT NULL DEFAULT '[]',
    `aangemaakt`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    `bijgewerkt`  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_token (`wagen_token`),
    INDEX idx_sessie (`sessie_id`),
    INDEX idx_bijgewerkt (`bijgewerkt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Uploads tabel aanmaken (als die nog niet bestaat)
CREATE TABLE IF NOT EXISTS `uploads` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `upload_token`    CHAR(32)     NOT NULL UNIQUE,
    `wagen_token`     CHAR(32)     DEFAULT NULL,
    `regel_id`        VARCHAR(32)  DEFAULT NULL,
    `bestandsnaam`    VARCHAR(255) NOT NULL,
    `opgeslagen_naam` VARCHAR(255) NOT NULL,
    `bestandstype`    VARCHAR(100) DEFAULT NULL,
    `bestandsgrootte` INT UNSIGNED DEFAULT 0,
    `aangemaakt`      DATETIME     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_wagen (`wagen_token`),
    INDEX idx_regel (`regel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Upload map bescherming aanmaken via PHP is niet via SQL —
--    zorg dat /bestellen/uploads/ontwerpen/ bestaat en beschermd is met .htaccess
