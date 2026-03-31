<?php
/**
 * Admin API handler — levertijden & drukkosten + bestaande endpoints
 * Wordt geinclude in of naast de hoofd handler.php
 * Pad: /bestellen/admin/admin_handler.php
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// ── Auth check ────────────────────────────────────────────────────────────────
if (empty($_SESSION['mm_admin'])) {
    echo json_encode(['ok' => false, 'fout' => 'Niet geautoriseerd']);
    exit;
}

// ── DB config ─────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../includes/db-config.php';

// ── Input ─────────────────────────────────────────────────────────────────────
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? ($_GET['action'] ?? '');

// ── Zorg dat instellingen tabel bestaat ───────────────────────────────────────
function ensureInstellingenTable(): void {
    getDB()->exec("CREATE TABLE IF NOT EXISTS `mm_instellingen` (
        `sleutel`   VARCHAR(100) NOT NULL PRIMARY KEY,
        `waarde`    MEDIUMTEXT   NOT NULL,
        `bijgewerkt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getSetting(string $key, $default = null) {
    ensureInstellingenTable();
    $st = getDB()->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel = ?");
    $st->execute([$key]);
    $row = $st->fetch();
    if (!$row) return $default;
    $decoded = json_decode($row['waarde'], true);
    return $decoded !== null ? $decoded : $row['waarde'];
}

function setSetting(string $key, $value): void {
    ensureInstellingenTable();
    $st = getDB()->prepare("INSERT INTO mm_instellingen (sleutel, waarde) VALUES (?,?)
                         ON DUPLICATE KEY UPDATE waarde=VALUES(waarde)");
    $st->execute([$key, json_encode($value, JSON_UNESCAPED_UNICODE)]);
}

// ── Route ─────────────────────────────────────────────────────────────────────
switch ($action) {

    // ── Stats ──────────────────────────────────────────────────────────────────
    case 'admin-stats':
        try {
            $orders    = getDB()->query("SELECT COUNT(*) FROM mm_bestellingen")->fetchColumn();
            $klanten   = getDB()->query("SELECT COUNT(*) FROM mm_klanten")->fetchColumn();
            $omzet     = getDB()->query("SELECT COALESCE(SUM(totaal_incl),0) FROM mm_bestellingen WHERE status != 'concept'")->fetchColumn();
            $producten = getDB()->query("SELECT COUNT(*) FROM mm_producten WHERE actief=1")->fetchColumn();
            echo json_encode(['ok'=>true,'stats'=>compact('orders','klanten','omzet','producten')]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Bestellingen lijst ─────────────────────────────────────────────────────
    case 'admin-bestellingen':
        try {
            $status = $input['status'] ?? '';
            $limit  = (int)($input['limit'] ?? 50);
            $sql = "SELECT b.*, CONCAT(k.voornaam,' ',k.achternaam) AS klant_naam, k.email AS klant_email
                    FROM mm_bestellingen b LEFT JOIN mm_klanten k ON b.klant_id=k.id";
            $params = [];
            if ($status) { $sql .= " WHERE b.status=?"; $params[] = $status; }
            $sql .= " ORDER BY b.aangemaakt DESC LIMIT $limit";
            $st = getDB()->prepare($sql);
            $st->execute($params);
            echo json_encode(['ok'=>true,'bestellingen'=>$st->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Bestelling detail ──────────────────────────────────────────────────────
    case 'admin-bestelling-detail':
        try {
            $id = (int)($input['id'] ?? 0);
            $st = getDB()->prepare("SELECT b.*, CONCAT(k.voornaam,' ',k.achternaam) AS klant_naam,
                k.email AS klant_email, k.telefoon AS klant_tel, k.bedrijf AS klant_bedrijf,
                k.kvk AS klant_kvk, k.straat AS klant_straat, k.postcode AS klant_postcode,
                k.stad AS klant_stad, k.land AS klant_land
                FROM mm_bestellingen b LEFT JOIN mm_klanten k ON b.klant_id=k.id WHERE b.id=?");
            $st->execute([$id]);
            $best = $st->fetch();
            if (!$best) { echo json_encode(['ok'=>false,'fout'=>'Niet gevonden']); break; }
            $regels = getDB()->prepare("SELECT * FROM mm_bestelregels WHERE bestelling_id=?");
            $regels->execute([$id]);
            $best['regels'] = $regels->fetchAll();
            echo json_encode(['ok'=>true,'bestelling'=>$best]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Status update ──────────────────────────────────────────────────────────
    case 'admin-status-update':
        try {
            $id     = (int)($input['id'] ?? 0);
            $status = $input['status'] ?? '';
            $toegestaan = ['betaald','in_behandeling','geleverd','geannuleerd','concept'];
            if (!in_array($status, $toegestaan)) { echo json_encode(['ok'=>false,'fout'=>'Ongeldige status']); break; }
            getDB()->prepare("UPDATE mm_bestellingen SET status=? WHERE id=?")->execute([$status,$id]);
            echo json_encode(['ok'=>true]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Klanten ────────────────────────────────────────────────────────────────
    case 'admin-klanten':
        try {
            $st = getDB()->query("SELECT k.*, (SELECT COUNT(*) FROM mm_bestellingen WHERE klant_id=k.id) AS order_count
                               FROM mm_klanten k ORDER BY k.aangemaakt DESC");
            echo json_encode(['ok'=>true,'klanten'=>$st->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Marges laden ───────────────────────────────────────────────────────────
    case 'admin-marges':
        $default = ['textiel'=>1.45,'dtf'=>1.35,'zeefdruk'=>1.40,'borduren'=>1.50,'verzending'=>1.0];
        $marges = getSetting('marges', $default);
        echo json_encode(['ok'=>true,'marges'=>$marges]);
        break;

    // ── Marges opslaan ─────────────────────────────────────────────────────────
    case 'admin-marges-opslaan':
        try {
            $marges = $input['marges'] ?? [];
            if (empty($marges)) { echo json_encode(['ok'=>false,'fout'=>'Geen marges ontvangen']); break; }
            setSetting('marges', $marges);
            echo json_encode(['ok'=>true]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Drukkosten laden ───────────────────────────────────────────────────────
    case 'admin-drukkosten':
        $default = [
            'dtf' => [
                'oplagen' => [1,5,10,25,50,100,250],
                'kleuren' => [1,2,3,4],
                'matrix'  => []
            ],
            'zeef' => [
                'oplagen' => [25,50,100,250,500,1000],
                'kleuren' => [1,2,3,4],
                'setup'   => [],
                'matrix'  => []
            ],
            'bord' => [
                'oplagen' => [1,5,10,25,50,100],
                'steken'  => [1000,2000,3000,5000,8000,10000],
                'setup'   => 25,
                'matrix'  => []
            ]
        ];
        $drukkosten = getSetting('drukkosten', $default);
        echo json_encode(['ok'=>true,'drukkosten'=>$drukkosten]);
        break;

    // ── Drukkosten opslaan ─────────────────────────────────────────────────────
    case 'admin-drukkosten-opslaan':
        try {
            $data = $input['drukkosten'] ?? null;
            if (!$data) { echo json_encode(['ok'=>false,'fout'=>'Geen data ontvangen']); break; }
            setSetting('drukkosten', $data);
            echo json_encode(['ok'=>true]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Levertijden laden ──────────────────────────────────────────────────────
    case 'admin-levertijden':
        $default = [
            'dtf'  => ['min' => 5,  'max' => 8],
            'zeef' => ['min' => 6,  'max' => 10],
            'bord' => ['min' => 7,  'max' => 12],
        ];
        $lt = getSetting('levertijden', $default);
        echo json_encode(['ok'=>true,'levertijden'=>$lt]);
        break;

    // ── Levertijden opslaan ────────────────────────────────────────────────────
    case 'admin-levertijden-opslaan':
        try {
            $lt = $input['levertijden'] ?? null;
            if (!$lt) { echo json_encode(['ok'=>false,'fout'=>'Geen levertijden ontvangen']); break; }
            // Valideer
            foreach (['dtf','zeef','bord'] as $tech) {
                if (!isset($lt[$tech]['min'], $lt[$tech]['max'])) {
                    echo json_encode(['ok'=>false,'fout'=>"Ontbrekende waarde voor $tech"]); exit;
                }
                $lt[$tech]['min'] = max(1, (int)$lt[$tech]['min']);
                $lt[$tech]['max'] = max(1, (int)$lt[$tech]['max']);
                if ($lt[$tech]['max'] < $lt[$tech]['min']) {
                    echo json_encode(['ok'=>false,'fout'=>"Maximum moet groter zijn dan minimum voor $tech"]); exit;
                }
            }
            setSetting('levertijden', $lt);
            echo json_encode(['ok'=>true]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    // ── Catalogus lijst ────────────────────────────────────────────────────────
    case 'catalogus-lijst':
        try {
            $st = getDB()->query("SELECT p.*, (SELECT COUNT(*) FROM mm_product_kleuren WHERE product_id=p.id) AS kleur_count
                               FROM mm_producten p ORDER BY p.merk, p.naam LIMIT 500");
            echo json_encode(['ok'=>true,'producten'=>$st->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['ok'=>false,'fout'=>$e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['ok'=>false,'fout'=>'Onbekende actie: '.$action]);
}
