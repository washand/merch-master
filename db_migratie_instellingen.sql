-- Merch Master: instellingen tabel + seed data
-- Uitvoeren via phpMyAdmin of MySQL CLI

CREATE TABLE IF NOT EXISTS `mm_instellingen` (
    `sleutel`    VARCHAR(100)  NOT NULL,
    `waarde`     MEDIUMTEXT    NOT NULL,
    `bijgewerkt` DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sleutel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standaard levertijden
INSERT INTO `mm_instellingen` (`sleutel`, `waarde`) VALUES
('levertijden', '{"dtf":{"min":5,"max":8},"zeef":{"min":6,"max":10},"bord":{"min":7,"max":12}}')
ON DUPLICATE KEY UPDATE `sleutel`=`sleutel`;

-- Standaard marges
INSERT INTO `mm_instellingen` (`sleutel`, `waarde`) VALUES
('marges', '{"textiel":1.45,"dtf":1.35,"zeefdruk":1.40,"borduren":1.50,"verzending":1.0}')
ON DUPLICATE KEY UPDATE `sleutel`=`sleutel`;

-- Standaard drukkosten (lege matrices)
INSERT INTO `mm_instellingen` (`sleutel`, `waarde`) VALUES
('drukkosten', '{"dtf":{"oplagen":[1,5,10,25,50,100,250],"kleuren":[1,2,3,4],"matrix":{}},"zeef":{"oplagen":[25,50,100,250,500,1000],"kleuren":[1,2,3,4],"setup":{},"matrix":{}},"bord":{"oplagen":[1,5,10,25,50,100],"steken":[1000,2000,3000,5000,8000,10000],"setup":25,"matrix":{}}}')
ON DUPLICATE KEY UPDATE `sleutel`=`sleutel`;
