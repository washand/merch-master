<?php
/**
 * Merch Master — Offerte API
 * POST /bestellen/offerte.php
 *
 * Acties:
 *   opslaan       — sla offerte op in DB, geef offerte_id terug
 *   laden         — laad offerte op basis van token
 *   pdf           — genereer HTML-offerte (print-ready)
 *   berekend-totaal — herbereken totaal incl. volumekorting over alle regels
 */

// ── CORS & headers ────────────────────────────────────────────────────────────
$toegestane_origins = ['https://merch-master.com','https://www.merch-master.com'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $toegestane_origins, true)) {
    header('Access-Control-Allow-Origin: '.$origin);
    header('Vary: Origin');
}
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { fout('Alleen POST toegestaan'); }

// ── DB ────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/includes/db-config.php';

function getSetting(string $key, $default = null) {
    try {
        $st = getDB()->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel=?");
        $st->execute([$key]);
        $row = $st->fetch();
        if (!$row) return $default;
        $d = json_decode($row['waarde'], true);
        return $d !== null ? $d : $row['waarde'];
    } catch (Exception $e) { return $default; }
}

// ── Offerte tabel aanmaken indien nodig ───────────────────────────────────────
function ensureOfferteTable(): void {
    getDB()->exec("CREATE TABLE IF NOT EXISTS `offertes` (
        `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `token`       CHAR(32)     NOT NULL UNIQUE,
        `klant_naam`  VARCHAR(120) DEFAULT NULL,
        `klant_email` VARCHAR(180) DEFAULT NULL,
        `klant_tel`   VARCHAR(40)  DEFAULT NULL,
        `klant_bedrijf` VARCHAR(120) DEFAULT NULL,
        `regels`      MEDIUMTEXT   NOT NULL,
        `subtotaal`   DECIMAL(10,2) DEFAULT 0,
        `vol_pct`     DECIMAL(5,2)  DEFAULT 0,
        `vol_korting` DECIMAL(10,2) DEFAULT 0,
        `totaal_excl` DECIMAL(10,2) DEFAULT 0,
        `totaal_incl` DECIMAL(10,2) DEFAULT 0,
        `spoed`       TINYINT(1)   DEFAULT 0,
        `spoed_toeslag` DECIMAL(10,2) DEFAULT 0,
        `status`      ENUM('concept','verzonden','geaccepteerd','vervallen') DEFAULT 'concept',
        `geldig_tot`  DATE         DEFAULT NULL,
        `aangemaakt`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
        `bijgewerkt`  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// ── Input ─────────────────────────────────────────────────────────────────────
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$actie  = $input['actie'] ?? '';

switch ($actie) {

    // ── Berekend totaal — volumekorting over alle regels samen ────────────────
    case 'berekend-totaal':
        $regels = $input['regels'] ?? [];
        if (empty($regels)) fout('Geen regels opgegeven');

        $totaal_stuks = array_sum(array_column($regels, 'aantal'));
        $subtotaal    = array_sum(array_map(
            fn($r) => (float)($r['prijs_excl_voor'] ?? $r['prijs_excl'] ?? 0) * (int)$r['aantal'],
            $regels
        ));

        // Volumekorting staffels uit DB
        $staffels = getSetting('volumekorting', [
            ['min'=>50,'pct'=>5],['min'=>100,'pct'=>8],['min'=>250,'pct'=>12]
        ]);
        usort($staffels, fn($a,$b)=>$b['min']<=>$a['min']);

        $vol_pct = 0; $vol_label = '';
        foreach ($staffels as $s) {
            if ($totaal_stuks >= (int)$s['min']) {
                $vol_pct   = (float)$s['pct'];
                $vol_label = $s['min'].'+ stuks → '.$vol_pct.'% korting';
                break;
            }
        }

        $vol_korting  = round($subtotaal * ($vol_pct / 100), 2);
        $totaal_excl  = round($subtotaal - $vol_korting, 2);
        $totaal_incl  = round($totaal_excl * 1.21, 2);
        $btw          = round($totaal_incl - $totaal_excl, 2);

        // Spoed: +40% op totaal na volumekorting, nooit online betalen
        $spoed         = !empty($input['spoed']);
        $spoed_toeslag = $spoed ? round($totaal_incl * 0.40, 2) : 0;
        $totaal_incl_spoed = $spoed ? round($totaal_incl + $spoed_toeslag, 2) : null;

        echo json_encode([
            'ok'               => true,
            'totaal_stuks'     => $totaal_stuks,
            'subtotaal'        => $subtotaal,
            'vol_pct'          => $vol_pct,
            'vol_label'        => $vol_label,
            'vol_korting'      => $vol_korting,
            'totaal_excl'      => $totaal_excl,
            'btw'              => $btw,
            'totaal_incl'      => $totaal_incl,
            'spoed'            => $spoed,
            'spoed_toeslag'    => $spoed_toeslag,
            'totaal_incl_spoed'=> $totaal_incl_spoed,
            'spoed_waarschuwing'=> $spoed
                ? 'Spoedorders kunnen niet online betaald worden. Neem contact op via WhatsApp of e-mail om de spoedboeking te bevestigen.'
                : null,
            'online_betalen_geblokkeerd' => $spoed,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ── Offerte opslaan ───────────────────────────────────────────────────────
    case 'opslaan':
        ensureOfferteTable();

        $regels        = $input['regels']         ?? [];
        $klant         = $input['klant']           ?? [];
        $spoed         = !empty($input['spoed']);
        $subtotaal     = (float)($input['subtotaal']    ?? 0);
        $vol_pct       = (float)($input['vol_pct']      ?? 0);
        $vol_korting   = (float)($input['vol_korting']  ?? 0);
        $totaal_excl   = (float)($input['totaal_excl']  ?? 0);
        $totaal_incl   = (float)($input['totaal_incl']  ?? 0);
        $spoed_toeslag = $spoed ? round($totaal_incl * 0.40, 2) : 0;

        if (empty($regels)) fout('Geen regels opgegeven');

        // Valideer: spoed mag NOOIT online betaald worden
        if ($spoed && !empty($input['betaling_methode'])) {
            fout('Spoedorders kunnen niet online betaald worden. Neem contact op.');
        }

        $token     = bin2hex(random_bytes(16));
        $geldig_tot = date('Y-m-d', strtotime('+30 days'));

        try {
            $st = getDB()->prepare(
                "INSERT INTO offertes
                 (token,klant_naam,klant_email,klant_tel,klant_bedrijf,
                  regels,subtotaal,vol_pct,vol_korting,totaal_excl,totaal_incl,
                  spoed,spoed_toeslag,geldig_tot)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $st->execute([
                $token,
                substr($klant['naam']    ?? '', 0, 120),
                substr($klant['email']   ?? '', 0, 180),
                substr($klant['tel']     ?? '', 0, 40),
                substr($klant['bedrijf'] ?? '', 0, 120),
                json_encode($regels, JSON_UNESCAPED_UNICODE),
                $subtotaal, $vol_pct, $vol_korting,
                $totaal_excl, $totaal_incl,
                $spoed ? 1 : 0, $spoed_toeslag,
                $geldig_tot,
            ]);
            $offerte_id = getDB()->lastInsertId();
        } catch (Exception $e) {
            fout('Kon offerte niet opslaan: '.$e->getMessage());
        }

        echo json_encode([
            'ok'          => true,
            'offerte_id'  => $offerte_id,
            'token'       => $token,
            'pdf_url'     => '/bestellen/offerte.php?pdf='.$token,
            'geldig_tot'  => $geldig_tot,
            'spoed'       => $spoed,
            'spoed_waarschuwing' => $spoed
                ? 'Spoedorder bevestigd in offerte. Neem contact op via info@merch-master.com om de spoedboeking te bevestigen. Online betaling is niet mogelijk voor spoedorders.'
                : null,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ── Offerte laden ─────────────────────────────────────────────────────────
    case 'laden':
        $token = preg_replace('/[^a-f0-9]/', '', $input['token'] ?? '');
        if (strlen($token) !== 32) fout('Ongeldig token');

        ensureOfferteTable();
        $st = getDB()->prepare("SELECT * FROM offertes WHERE token=? LIMIT 1");
        $st->execute([$token]);
        $offerte = $st->fetch();
        if (!$offerte) fout('Offerte niet gevonden');

        $offerte['regels'] = json_decode($offerte['regels'], true);
        echo json_encode(['ok'=>true,'offerte'=>$offerte], JSON_UNESCAPED_UNICODE);
        break;

    default:
        fout('Onbekende actie: '.$actie);
}

function fout(string $msg): void {
    echo json_encode(['ok'=>false,'fout'=>$msg]); exit;
}
