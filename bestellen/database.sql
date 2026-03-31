-- ─────────────────────────────────────────────────────────────────────────────
-- Merch Master Besteltool v2 — Database Setup
-- Voer dit eenmalig uit in Hostinger phpMyAdmin of via MySQL CLI
-- ─────────────────────────────────────────────────────────────────────────────

CREATE DATABASE IF NOT EXISTS merchmaster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE merchmaster;

-- ── Klanten ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS klanten (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(255) NOT NULL UNIQUE,
  wachtwoord    VARCHAR(255) NOT NULL,
  voornaam      VARCHAR(100) NOT NULL,
  achternaam    VARCHAR(100) NOT NULL,
  telefoon      VARCHAR(50),
  bedrijf       VARCHAR(255),
  kvk           VARCHAR(20),
  btwnr         VARCHAR(30),
  straat        VARCHAR(255),
  postcode      VARCHAR(20),
  stad          VARCHAR(100),
  land          VARCHAR(10) DEFAULT 'NL',
  aangemaakt    DATETIME DEFAULT CURRENT_TIMESTAMP,
  reset_token   VARCHAR(64),
  reset_expiry  DATETIME,
  actief        TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- ── Bestellingen ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS bestellingen (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  order_id      VARCHAR(50) NOT NULL UNIQUE,
  klant_id      INT,
  status        ENUM('concept','betaald','in_behandeling','geleverd','geannuleerd') DEFAULT 'concept',
  totaal_incl   DECIMAL(10,2) DEFAULT 0,
  totaal_ex     DECIMAL(10,2) DEFAULT 0,
  btw           DECIMAL(10,2) DEFAULT 0,
  verzending_ex DECIMAL(10,2) DEFAULT 0,
  paypal_id     VARCHAR(100),
  jortt_id      VARCHAR(100),
  opmerkingen   TEXT,
  aangemaakt    DATETIME DEFAULT CURRENT_TIMESTAMP,
  bijgewerkt    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (klant_id) REFERENCES klanten(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Bestelregels (meerdere producten per bestelling) ─────────────────────────
CREATE TABLE IF NOT EXISTS bestelregels (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  bestelling_id INT NOT NULL,
  textiel_sku   VARCHAR(50),
  textiel_naam  VARCHAR(255),
  textiel_merk  VARCHAR(100),
  kleur_code    VARCHAR(20),
  kleur_naam    VARCHAR(100),
  positie       VARCHAR(50),
  techniek_a    VARCHAR(50),
  techniek_b    VARCHAR(50),
  is_dual       TINYINT(1) DEFAULT 0,
  maten         JSON,
  aantal        INT DEFAULT 0,
  prijs_ex      DECIMAL(10,4) DEFAULT 0,
  druk_ex       DECIMAL(10,4) DEFAULT 0,
  regel_ex      DECIMAL(10,2) DEFAULT 0,
  FOREIGN KEY (bestelling_id) REFERENCES bestellingen(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Logo uploads per bestelregel ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS uploads (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  regel_id      INT NOT NULL,
  positie       VARCHAR(20),
  bestandsnaam  VARCHAR(255),
  url           VARCHAR(500),
  volgorde      INT DEFAULT 0,
  FOREIGN KEY (regel_id) REFERENCES bestelregels(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Opgeslagen concepten/offertes ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS concepten (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  klant_id      INT,
  naam          VARCHAR(255) DEFAULT 'Mijn offerte',
  configuratie  JSON,
  aangemaakt    DATETIME DEFAULT CURRENT_TIMESTAMP,
  bijgewerkt    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (klant_id) REFERENCES klanten(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Sessies ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS sessies (
  token         VARCHAR(64) PRIMARY KEY,
  klant_id      INT NOT NULL,
  aangemaakt    DATETIME DEFAULT CURRENT_TIMESTAMP,
  verloopt      DATETIME NOT NULL,
  FOREIGN KEY (klant_id) REFERENCES klanten(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Admin producten (eigen toevoegingen naast Ralawise catalogus) ─────────────
CREATE TABLE IF NOT EXISTS admin_producten (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  naam        VARCHAR(255) NOT NULL,
  sku         VARCHAR(50),
  categorie   VARCHAR(50),
  segment     ENUM('budget','standaard','premium') DEFAULT 'standaard',
  inkoop      DECIMAL(10,2) DEFAULT 0,
  actief      TINYINT(1) DEFAULT 1,
  aangemaakt  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Prijsmarges per categorie/segment ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin_prijzen (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  categorie   VARCHAR(50) NOT NULL,
  segment     ENUM('budget','standaard','premium') NOT NULL,
  markup      DECIMAL(5,2) DEFAULT 1.50,
  UNIQUE KEY uniq_cat_seg (categorie, segment)
) ENGINE=InnoDB;

-- Standaard marges
INSERT IGNORE INTO admin_prijzen (categorie, segment, markup) VALUES
  ('shirt',    'budget',    1.50),
  ('shirt',    'standaard', 1.65),
  ('shirt',    'premium',   1.80),
  ('hoodie',   'budget',    1.50),
  ('hoodie',   'standaard', 1.65),
  ('hoodie',   'premium',   1.80),
  ('jas',      'budget',    1.50),
  ('jas',      'standaard', 1.65),
  ('jas',      'premium',   1.80),
  ('polo',     'budget',    1.50),
  ('polo',     'standaard', 1.65),
  ('cap',      'budget',    1.50),
  ('tas',      'budget',    1.50),
  ('baby',     'budget',    1.50);

-- Admin gebruiker (wachtwoord aanpassen in config.php)
