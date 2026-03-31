<?php
/**
 * Merch Master — Admin API handler
 * Pad: /bestellen/admin/handler.php  (apart van de klant-handler)
 *
 * Echte tabellen in DB (gezien in debug):
 *   admin_prijzen, bestellingen, bestelregels, catalogus, catalogus_kleuren,
 *   concepten, druk_zeef, instellingen, klanten, sessies, uploads
 *
 * mm_instellingen is net aangemaakt door debug script — die gebruiken we
 * voor levertijden / drukkosten / marges (nieuwe instellingen).
 */

session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// ── Auth ──────────────────────────────────────────────────────────────────────
define('ADMIN_PW', 'Klaas#99');

// Login via POST
$raw = file_get_contents('php://input');
$input = json_decode($raw, true) ?? [];
$action = $input['action'] ?? ($_GET['action'] ?? '');

if ($action === 'admin-login') {
    if (($input['wachtwoord'] ?? '') === ADMIN_PW) {
        $_SESSION['mm_admin'] = true;
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'fout' => 'Ongeldig wachtwoord']);
    }
    exit;
}

if ($action === 'admin-logout') {
    unset($_SESSION['mm_admin']);
    echo json_encode(['ok' => true]);
    exit;
}

if (empty($_SESSION['mm_admin'])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'fout' => 'Niet geautoriseerd']);
    exit;
}

// ── DB ────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../includes/db-config.php';

// ── Instellingen helpers (mm_instellingen tabel) ───────────────────────────────
function getSetting(string $key, $default = null) {
    try {
        $st = getDB()->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel = ?");
        $st->execute([$key]);
        $row = $st->fetch();
        if (!$row) return $default;
        $decoded = json_decode($row['waarde'], true);
        return $decoded !== null ? $decoded : $row['waarde'];
    } catch (Exception $e) {
        return $default;
    }
}

function setSetting(string $key, $value): void {
    getDB()->prepare(
        "INSERT INTO mm_instellingen (sleutel, waarde)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE waarde = VALUES(waarde)"
    )->execute([$key, json_encode($value, JSON_UNESCAPED_UNICODE)]);
}

// ── Router ────────────────────────────────────────────────────────────────────
try {
    switch ($action) {

        // ── Dashboard stats ───────────────────────────────────────────────────
        case 'admin-stats':
            $orders    = getDB()->query("SELECT COUNT(*) FROM bestellingen")->fetchColumn();
            $klanten   = getDB()->query("SELECT COUNT(*) FROM klanten")->fetchColumn();
            $omzet     = getDB()->query("SELECT COALESCE(SUM(totaal_incl),0) FROM bestellingen WHERE status != 'concept'")->fetchColumn();
            // catalogus kan 'catalogus' of 'admin_producten' heten — probeer beide
            try {
                $producten = getDB()->query("SELECT COUNT(*) FROM catalogus WHERE actief=1")->fetchColumn();
            } catch (Exception $e) {
                try { $producten = getDB()->query("SELECT COUNT(*) FROM admin_producten")->fetchColumn(); }
                catch (Exception $e2) { $producten = '?'; }
            }
            echo json_encode(['ok' => true, 'stats' => compact('orders','klanten','omzet','producten')]);
            break;

        // ── Bestellingen lijst ────────────────────────────────────────────────
        case 'admin-bestellingen':
            $status = $input['status'] ?? '';
            $limit  = min((int)($input['limit'] ?? 50), 200);
            $sql    = "SELECT b.*,
                              CONCAT(k.voornaam,' ',k.achternaam) AS klant_naam,
                              k.email AS klant_email,
                              CASE WHEN b.inkoopwaarde_excl IS NOT NULL
                                   THEN ROUND(b.totaal_excl - b.inkoopwaarde_excl, 2)
                                   ELSE NULL END AS winst_excl
                       FROM bestellingen b
                       LEFT JOIN klanten k ON b.klant_id = k.id";
            $params = [];
            if ($status) { $sql .= " WHERE b.status = ?"; $params[] = $status; }
            $sql .= " ORDER BY b.aangemaakt DESC LIMIT $limit";
            $st = getDB()->prepare($sql);
            $st->execute($params);
            echo json_encode(['ok' => true, 'bestellingen' => $st->fetchAll()]);
            break;

        // ── Bestelling detail ─────────────────────────────────────────────────
        case 'admin-bestelling-detail':
            $id = (int)($input['id'] ?? 0);
            $st = getDB()->prepare(
                "SELECT b.*,
                        CONCAT(k.voornaam,' ',k.achternaam) AS klant_naam,
                        k.email AS klant_email, k.telefoon AS klant_tel,
                        k.bedrijf AS klant_bedrijf, k.kvk AS klant_kvk,
                        k.straat AS klant_straat, k.postcode AS klant_postcode,
                        k.stad AS klant_stad, k.land AS klant_land
                 FROM bestellingen b
                 LEFT JOIN klanten k ON b.klant_id = k.id
                 WHERE b.id = ?"
            );
            $st->execute([$id]);
            $best = $st->fetch();
            if (!$best) { echo json_encode(['ok' => false, 'fout' => 'Niet gevonden']); break; }
            $reg = getDB()->prepare("SELECT * FROM bestelregels WHERE bestelling_id = ?");
            $reg->execute([$id]);
            $best['regels'] = $reg->fetchAll();
            echo json_encode(['ok' => true, 'bestelling' => $best]);
            break;

        // ── Status wijzigen ───────────────────────────────────────────────────
        case 'admin-status-update':
            $id     = (int)($input['id'] ?? 0);
            $status = $input['status'] ?? '';
            $ok_statussen = ['betaald','in_behandeling','geleverd','geannuleerd','concept'];
            if (!in_array($status, $ok_statussen)) {
                echo json_encode(['ok' => false, 'fout' => 'Ongeldige status']); break;
            }
            getDB()->prepare("UPDATE bestellingen SET status = ? WHERE id = ?")->execute([$status, $id]);
            echo json_encode(['ok' => true]);
            break;

        // ── Klanten ───────────────────────────────────────────────────────────
        case 'admin-klanten':
            $st = getDB()->query(
                "SELECT k.*,
                        (SELECT COUNT(*) FROM bestellingen WHERE klant_id = k.id) AS order_count
                 FROM klanten k
                 ORDER BY k.aangemaakt DESC"
            );
            echo json_encode(['ok' => true, 'klanten' => $st->fetchAll()]);
            break;

        // ── Prijsmarges (admin_prijzen tabel + mm_instellingen fallback) ───────
        case 'admin-marges':
            $marges = [];
            try {
                $rows = getDB()->query("SELECT sleutel, waarde FROM admin_prijzen")->fetchAll();
                foreach ($rows as $r) $marges[$r['sleutel']] = (float)$r['waarde'];
            } catch (Exception $e) {}
            // Vul aan met mm_instellingen (voor nieuwe tier-keys)
            $opgeslagen = getSetting('marges', []);
            $marges = array_merge($opgeslagen, $marges);
            // Zorg dat defaults aanwezig zijn
            $defaults = ['textiel_budget'=>1.55,'textiel_standaard'=>1.45,'textiel_premium'=>1.35];
            foreach ($defaults as $k => $v) {
                if (!isset($marges[$k])) $marges[$k] = $v;
            }
            echo json_encode(['ok' => true, 'marges' => $marges]);
            break;

        case 'admin-marges-opslaan':
            $marges = $input['marges'] ?? [];
            if (empty($marges)) { echo json_encode(['ok' => false, 'fout' => 'Geen marges']); break; }
            $schoon = [];
            foreach (['textiel_budget','textiel_standaard','textiel_premium'] as $k) {
                if (isset($marges[$k])) $schoon[$k] = max(1.0, min(5.0, (float)$marges[$k]));
            }
            setSetting('marges', $schoon);
            try {
                $st = getDB()->prepare("INSERT INTO admin_prijzen (sleutel, waarde) VALUES (?,?) ON DUPLICATE KEY UPDATE waarde=VALUES(waarde)");
                foreach ($schoon as $k => $v) $st->execute([$k, $v]);
            } catch (Exception $e) {}
            echo json_encode(['ok' => true]);
            break;

        // ── Drukkosten (instellingen tabel — bestaand systeem) ─────────────────
        case 'admin-drukkosten':
            // Probeer bestaande 'instellingen' tabel
            $drukkosten = null;
            try {
                $st = getDB()->prepare("SELECT waarde FROM instellingen WHERE sleutel = 'drukkosten'");
                $st->execute();
                $row = $st->fetch();
                if ($row) $drukkosten = json_decode($row['waarde'], true);
            } catch (Exception $e) {}

            if (!$drukkosten) {
                $drukkosten = getSetting('drukkosten', null);
            }

            if (!$drukkosten) {
                $drukkosten = [
                    'dtf'  => ['oplagen' => ['1-9','10-50','50+'], 'matrix' => ['1-9'=>9.00,'10-50'=>7.00,'50+'=>6.00]],
                    'zeef' => [
                        'oplagen' => [25,50,100,250,500,1000,2500,5000,10000],
                        'kleuren' => [1,2,3,4],
                        'setup'   => [],
                        'matrix'  => [
                            1 => [25=>4.41,50=>2.83,100=>1.77,250=>1.27,500=>0.96,1000=>0.77,2500=>0.68,5000=>0.58,10000=>0.50],
                            2 => [25=>7.25,50=>4.41,100=>2.62,250=>1.76,500=>1.27,1000=>1.02,2500=>0.83,5000=>0.66,10000=>0.61],
                            3 => [25=>9.35,50=>5.62,100=>3.37,250=>2.11,500=>1.58,1000=>1.16,2500=>0.96,5000=>0.79,10000=>0.70],
                            4 => [25=>11.57,50=>7.00,100=>4.24,250=>2.63,500=>1.89,1000=>1.43,2500=>1.17,5000=>0.91,10000=>0.82],
                        ]
                    ]
                ];
            }
            echo json_encode(['ok' => true, 'drukkosten' => $drukkosten]);
            break;

        case 'admin-drukkosten-opslaan':
            $data = $input['drukkosten'] ?? null;
            if (!$data) { echo json_encode(['ok' => false, 'fout' => 'Geen data']); break; }
            // Sla op in mm_instellingen (betrouwbaar — tabel bestaat nu)
            setSetting('drukkosten', $data);
            // Probeer ook bestaande instellingen tabel bij te werken
            try {
                getDB()->prepare(
                    "INSERT INTO instellingen (sleutel, waarde) VALUES ('drukkosten',?)
                     ON DUPLICATE KEY UPDATE waarde=VALUES(waarde)"
                )->execute([json_encode($data)]);
            } catch (Exception $e) {}
            echo json_encode(['ok' => true]);
            break;

        // ── Levertijden (mm_instellingen + instellingen fallback) ──────────────
        case 'admin-levertijden':
            // Probeer bestaande 'instellingen' tabel eerst
            $lt = null;
            try {
                $st = getDB()->prepare("SELECT waarde FROM instellingen WHERE sleutel = 'levertijden'");
                $st->execute();
                $row = $st->fetch();
                if ($row) $lt = json_decode($row['waarde'], true);
            } catch (Exception $e) {}

            // Fallback naar mm_instellingen
            if (!$lt) {
                $lt = getSetting('levertijden', [
                    'dtf'  => ['min' => 5,  'max' => 8],
                    'zeef' => ['min' => 6,  'max' => 10],
                    'bord' => ['min' => 7,  'max' => 12],
                ]);
            }
            echo json_encode(['ok' => true, 'levertijden' => $lt]);
            break;

        case 'admin-levertijden-opslaan':
            $lt = $input['levertijden'] ?? null;
            if (!$lt) { echo json_encode(['ok' => false, 'fout' => 'Geen data']); break; }

            // Valideer
            foreach (['dtf','zeef','bord'] as $tech) {
                $min = (int)($lt[$tech]['min'] ?? 0);
                $max = (int)($lt[$tech]['max'] ?? 0);
                if ($min < 1 || $max < 1) {
                    echo json_encode(['ok' => false, 'fout' => "Ongeldige waarde voor $tech"]); exit;
                }
                if ($max < $min) {
                    echo json_encode(['ok' => false, 'fout' => "Maximum moet ≥ minimum zijn voor $tech"]); exit;
                }
                $lt[$tech] = ['min' => $min, 'max' => $max];
            }

            // Sla op in mm_instellingen
            setSetting('levertijden', $lt);

            // Probeer ook bestaande instellingen tabel
            try {
                getDB()->prepare(
                    "INSERT INTO instellingen (sleutel, waarde) VALUES ('levertijden',?)
                     ON DUPLICATE KEY UPDATE waarde=VALUES(waarde)"
                )->execute([json_encode($lt)]);
            } catch (Exception $e) {
                // instellingen tabel bestaat niet of heeft andere structuur — mm_instellingen is voldoende
            }

            echo json_encode(['ok' => true]);
            break;

        // ── Volumekorting laden ────────────────────────────────────────────────
        case 'admin-volumekorting':
            $staffels = getSetting('volumekorting', [
                ['min'=>50,'pct'=>5],['min'=>100,'pct'=>8],['min'=>250,'pct'=>12],
            ]);
            echo json_encode(['ok'=>true,'staffels'=>$staffels]);
            break;

        case 'admin-volumekorting-opslaan':
            $staffels = $input['staffels'] ?? [];
            $schoon = [];
            foreach ($staffels as $s) {
                $min = (int)($s['min'] ?? 0);
                $pct = (float)($s['pct'] ?? 0);
                if ($min < 1 || $pct < 0 || $pct > 50) {
                    echo json_encode(['ok'=>false,'fout'=>"Ongeldige staffel: min=$min, pct=$pct"]); exit;
                }
                $schoon[] = ['min'=>$min,'pct'=>round($pct,1)];
            }
            usort($schoon, fn($a,$b)=>$a['min']<=>$b['min']);
            setSetting('volumekorting', $schoon);
            echo json_encode(['ok'=>true]);
            break;

        // ── Offertes lijst ─────────────────────────────────────────────────────
        case 'admin-offertes':
            try {
                getDB()->exec("CREATE TABLE IF NOT EXISTS `offertes` (
                    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `token` CHAR(32) NOT NULL UNIQUE,
                    `klant_naam` VARCHAR(120), `klant_email` VARCHAR(180),
                    `klant_tel` VARCHAR(40), `klant_bedrijf` VARCHAR(120),
                    `regels` MEDIUMTEXT NOT NULL,
                    `subtotaal` DECIMAL(10,2) DEFAULT 0,
                    `vol_pct` DECIMAL(5,2) DEFAULT 0,
                    `vol_korting` DECIMAL(10,2) DEFAULT 0,
                    `totaal_excl` DECIMAL(10,2) DEFAULT 0,
                    `totaal_incl` DECIMAL(10,2) DEFAULT 0,
                    `spoed` TINYINT(1) DEFAULT 0,
                    `spoed_toeslag` DECIMAL(10,2) DEFAULT 0,
                    `status` VARCHAR(20) DEFAULT 'concept',
                    `geldig_tot` DATE,
                    `aangemaakt` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `bijgewerkt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                $st = getDB()->query(
                    "SELECT id, token, klant_naam, klant_email, subtotaal,
                            vol_pct, totaal_excl, totaal_incl,
                            spoed, spoed_toeslag, status, geldig_tot, aangemaakt,
                            COALESCE(winst_excl, NULL) AS winst_excl
                     FROM offertes ORDER BY aangemaakt DESC LIMIT 100"
                );
                echo json_encode(['ok'=>true,'offertes'=>$st->fetchAll()]);
            } catch (Exception $e) {
                echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
            }
            break;

        case 'admin-offerte-status':
            $token  = preg_replace('/[^a-f0-9]/','', $input['token'] ?? '');
            $status = $input['status'] ?? '';
            if (!in_array($status, ['concept','verzonden','geaccepteerd','betaald','vervallen'])) {
                echo json_encode(['ok'=>false,'fout'=>'Ongeldige status']); break;
            }
            if (strlen($token) !== 32) { echo json_encode(['ok'=>false,'fout'=>'Ongeldig token']); break; }
            getDB()->prepare("UPDATE offertes SET status=? WHERE token=?")->execute([$status,$token]);
            echo json_encode(['ok'=>true]);
            break;

        // ── Catalogus ─────────────────────────────────────────────────────────
        case 'catalogus-lijst':
            try {
                $st = getDB()->query(
                    "SELECT c.*,
                            (SELECT COUNT(*) FROM catalogus_kleuren WHERE product_id = c.id) AS kleur_count
                     FROM catalogus c
                     ORDER BY c.merk, c.naam
                     LIMIT 500"
                );
                echo json_encode(['ok' => true, 'producten' => $st->fetchAll()]);
            } catch (Exception $e) {
                echo json_encode(['ok' => false, 'fout' => $e->getMessage()]);
            }
            break;

        default:
            echo json_encode(['ok' => false, 'fout' => 'Onbekende actie: ' . htmlspecialchars($action)]);
    }

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'fout' => 'Serverfout: ' . $e->getMessage()]);
}
