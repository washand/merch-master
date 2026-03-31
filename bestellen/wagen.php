<?php
/**
 * Merch Master — Winkelwagen API (beveiligd)
 * POST /bestellen/wagen.php
 *
 * Fixes:
 * - CSRF-bescherming via Origin header check
 * - Error handling op alle DB calls
 * - Wagen-token gekoppeld aan sessie
 * - Upload opslaglogica per winkelwagenregel
 * - Wagen cleanup (>30 dagen)
 * - Winst bijhouden bij naar_offerte
 */

session_start();

// ── CORS + CSRF ───────────────────────────────────────────────────────────────
$toegestane_origins = ['https://merch-master.com','https://www.merch-master.com'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// CSRF: POST requests moeten van eigen domein komen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($origin)) {
    if (!in_array($origin, $toegestane_origins, true)) {
        http_response_code(403);
        echo json_encode(['ok'=>false,'fout'=>'Verboden verzoek']);
        exit;
    }
}
if (in_array($origin, $toegestane_origins, true)) {
    header('Access-Control-Allow-Origin: '.$origin);
    header('Vary: Origin');
}
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { fout('Alleen POST toegestaan'); }

// ── Rate limiting ─────────────────────────────────────────────────────────────
function checkRateLimit(int $max = 120): void {
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = sys_get_temp_dir() . '/mm_rl_' . md5($ip . '_wagen');
    $nu  = time();
    $data = ['calls' => []];
    if (file_exists($key)) { $raw = @file_get_contents($key); if ($raw) $data = json_decode($raw, true) ?? $data; }
    $data['calls'] = array_values(array_filter($data['calls'], fn($t) => ($nu - $t) < 60));
    if (count($data['calls']) >= $max) {
        http_response_code(429);
        echo json_encode(['ok'=>false,'fout'=>'Te veel verzoeken.']);
        exit;
    }
    $data['calls'][] = $nu;
    @file_put_contents($key, json_encode($data), LOCK_EX);
}
checkRateLimit();

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

// ── Tabellen ──────────────────────────────────────────────────────────────────
function ensureTables(): void {
    getDB()->exec("CREATE TABLE IF NOT EXISTS `wagens` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    getDB()->exec("CREATE TABLE IF NOT EXISTS `uploads` (
        `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `upload_token` CHAR(32)    NOT NULL UNIQUE,
        `wagen_token` CHAR(32)     DEFAULT NULL,
        `regel_id`    VARCHAR(32)  DEFAULT NULL,
        `bestandsnaam` VARCHAR(255) NOT NULL,
        `opgeslagen_naam` VARCHAR(255) NOT NULL,
        `bestandstype` VARCHAR(100) DEFAULT NULL,
        `bestandsgrootte` INT UNSIGNED DEFAULT 0,
        `aangemaakt`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_wagen (`wagen_token`),
        INDEX idx_regel (`regel_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// ── Wagen cleanup (wagens ouder dan 30 dagen) ─────────────────────────────────
function cleanup(): void {
    try {
        getDB()->exec("DELETE FROM wagens WHERE bijgewerkt < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    } catch (Exception $e) { /* niet kritiek */ }
}
// Cleanup met 1% kans per request (geen aparte cron nodig)
if (rand(1,100) === 1) cleanup();

// ── Upload helpers ────────────────────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/uploads/ontwerpen/');
define('UPLOAD_MAX_MB', 20);
define('UPLOAD_ALLOWED', ['image/jpeg','image/png','image/svg+xml','application/pdf',
                          'application/postscript','image/vnd.adobe.photoshop']);

function verwerkUpload(string $wagen_token, string $regel_id): array {
    if (empty($_FILES['ontwerp'])) return ['ok'=>false,'fout'=>'Geen bestand ontvangen'];

    $file = $_FILES['ontwerp'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $fouten = [
            UPLOAD_ERR_INI_SIZE   => 'Bestand te groot (server limiet)',
            UPLOAD_ERR_FORM_SIZE  => 'Bestand te groot',
            UPLOAD_ERR_PARTIAL    => 'Upload niet volledig',
            UPLOAD_ERR_NO_FILE    => 'Geen bestand geselecteerd',
        ];
        return ['ok'=>false,'fout'=>$fouten[$file['error']] ?? 'Upload fout'];
    }

    // Grootte check
    if ($file['size'] > UPLOAD_MAX_MB * 1024 * 1024) {
        return ['ok'=>false,'fout'=>'Bestand mag maximaal '.UPLOAD_MAX_MB.'MB zijn'];
    }

    // MIME type check (gebruik finfo, niet $_FILES['type'])
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimetype = $finfo->file($file['tmp_name']);
    if (!in_array($mimetype, UPLOAD_ALLOWED, true)) {
        return ['ok'=>false,'fout'=>'Bestandstype niet toegestaan. Gebruik JPG, PNG, SVG, PDF, AI of PSD.'];
    }

    // Extensie whitelist
    $extensie = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $ok_ext   = ['jpg','jpeg','png','svg','pdf','ai','eps','psd'];
    if (!in_array($extensie, $ok_ext, true)) {
        return ['ok'=>false,'fout'=>'Bestandsextensie niet toegestaan'];
    }

    // Map aanmaken
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
        file_put_contents(UPLOAD_DIR . '.htaccess',
            "Options -Indexes\n<FilesMatch \\.php>\nDeny from all\n</FilesMatch>\n");
    }

    // Veilige bestandsnaam
    $upload_token   = bin2hex(random_bytes(16));
    $opgeslagen     = $upload_token . '.' . $extensie;
    $bestemming     = UPLOAD_DIR . $opgeslagen;

    if (!move_uploaded_file($file['tmp_name'], $bestemming)) {
        return ['ok'=>false,'fout'=>'Kon bestand niet opslaan'];
    }

    // Registreer in DB
    try {
        ensureTables();
        getDB()->prepare(
            "INSERT INTO uploads (upload_token,wagen_token,regel_id,bestandsnaam,opgeslagen_naam,bestandstype,bestandsgrootte)
             VALUES (?,?,?,?,?,?,?)"
        )->execute([
            $upload_token, $wagen_token, $regel_id,
            substr($file['name'], 0, 255), $opgeslagen, $mimetype, $file['size']
        ]);
    } catch (Exception $e) {
        @unlink($bestemming);
        return ['ok'=>false,'fout'=>'DB fout bij registreren upload'];
    }

    return [
        'ok'           => true,
        'upload_token' => $upload_token,
        'bestandsnaam' => $file['name'],
        'bestandstype' => $mimetype,
        'grootte_kb'   => round($file['size'] / 1024, 1),
    ];
}

// ── Input ─────────────────────────────────────────────────────────────────────
// Ondersteun zowel JSON (wagen acties) als multipart/form-data (uploads)
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';
if (str_contains($content_type, 'multipart/form-data')) {
    $input = $_POST;
} else {
    $raw   = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? [];
}

$actie      = $input['actie']      ?? '';
$wagen_token = preg_replace('/[^a-f0-9]/', '', $input['wagen_token'] ?? '');
$sessie_id  = session_id();

// Token-sessie koppeling: als token al in sessie zit, gebruik dat
if (empty($wagen_token) && !empty($_SESSION['mm_wagen_token'])) {
    $wagen_token = $_SESSION['mm_wagen_token'];
}

// ── Wagen helpers ─────────────────────────────────────────────────────────────
function laadWagenRegels(string $token): array {
    if (strlen($token) !== 32) return [];
    try {
        ensureTables();
        $st = getDB()->prepare("SELECT regels FROM wagens WHERE wagen_token=? LIMIT 1");
        $st->execute([$token]);
        $row = $st->fetch();
        if (!$row) return [];
        return json_decode($row['regels'], true) ?? [];
    } catch (Exception $e) {
        error_log('MerchMaster wagen laad fout: '.$e->getMessage());
        return [];
    }
}

function slaWagenOp(string $token, array $regels, string $sessie_id = ''): void {
    try {
        ensureTables();
        getDB()->prepare(
            "INSERT INTO wagens (wagen_token, sessie_id, regels) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE regels=VALUES(regels), sessie_id=VALUES(sessie_id)"
        )->execute([$token, $sessie_id, json_encode($regels, JSON_UNESCAPED_UNICODE)]);
    } catch (Exception $e) {
        error_log('MerchMaster wagen opslaan fout: '.$e->getMessage());
        throw $e;
    }
}

function nieuweToken(): string { return bin2hex(random_bytes(16)); }

function valideerRegel(array $r): ?string {
    if (empty($r['sku']))                                     return 'Geen SKU';
    if (!preg_match('/^[a-zA-Z0-9\-_]{1,50}$/', $r['sku'])) return 'Ongeldige SKU';
    if (!in_array($r['techniek']??'', ['dtf','zeefdruk','borduren'])) return 'Ongeldige techniek';
    $maten  = $r['maten'] ?? [];
    $totaal = array_sum($maten);
    if ($totaal < 1 || $totaal > 100000) return 'Ongeldig aantal';
    $posities = array_column($r['posities'] ?? [], 'positie');
    $geldige  = ['voorkant','achterkant','linkerborst','rechterborst'];
    foreach ($posities as $p) {
        if (!in_array($p, $geldige)) return "Ongeldige positie: $p";
    }
    if (in_array('voorkant',$posities) && (in_array('linkerborst',$posities)||in_array('rechterborst',$posities))) {
        return 'Linkerborst/rechterborst kunnen niet gecombineerd worden met voorkant.';
    }
    if (count($posities) !== count(array_unique($posities))) return 'Dubbele positie';
    return null;
}

// ── Prijsberekening ───────────────────────────────────────────────────────────
function berekenRegelPrijs(array $regel, array $marges, array $drukkosten, array $vol_staffels, int $order_totaal_stuks): array {
    $sku      = $regel['sku']      ?? '';
    $aantal   = (int)($regel['aantal'] ?? 0);
    $techniek = strtolower($regel['techniek'] ?? '');
    if ($aantal <= 0 || empty($sku)) return $regel + ['prijs'=>null,'fout'=>'Ongeldige regel'];

    try {
        $st = getDB()->prepare("SELECT inkoop, tier, name, brand, actief FROM catalogus WHERE sku=? LIMIT 1");
        $st->execute([$sku]);
        $product = $st->fetch();
    } catch (Exception $e) {
        return $regel + ['prijs'=>null,'fout'=>'Databasefout'];
    }

    if (!$product || !$product['actief']) return $regel + ['prijs'=>null,'fout'=>'Product niet beschikbaar'];

    $inkoop  = (float)$product['inkoop'];
    $db_tier = $product['tier'] ?? null;

    if (in_array($db_tier,['budget','standaard','premium'],true)) { $tier=$db_tier; }
    elseif ($inkoop < 4.00)  { $tier='budget'; }
    elseif ($inkoop <= 10.00){ $tier='standaard'; }
    else                      { $tier='premium'; }

    $marge        = (float)($marges['textiel_'.$tier] ?? 1.45);
    $textiel_excl = round($inkoop * $marge, 4);
    $inkoop_excl  = $inkoop; // bewaar inkoopprijs voor winstberekening

    // Drukkosten per positie
    $druk_excl = 0.0;
    foreach ($regel['posities'] ?? [['positie'=>'voorkant','kleuren'=>1]] as $pos) {
        $kleuren = (int)($pos['kleuren'] ?? 1);
        if ($kleuren < 1 || $kleuren > 4) $kleuren = 1;
        if ($techniek === 'dtf') {
            $m = $drukkosten['dtf']['matrix'] ?? ['1-9'=>9.00,'10-50'=>7.00,'50+'=>6.00];
            if ($aantal <= 9)      $druk_excl += (float)($m['1-9']  ?? 9.00);
            elseif ($aantal <= 50) $druk_excl += (float)($m['10-50'] ?? 7.00);
            else                   $druk_excl += (float)($m['50+']   ?? 6.00);
        } elseif ($techniek === 'zeefdruk') {
            $m       = $drukkosten['zeef']['matrix'] ?? [];
            $kmatrix = $m[(string)$kleuren] ?? ($m[$kleuren] ?? []);
            foreach ([10000,5000,2500,1000,500,250,100,50,25] as $band) {
                if ($aantal >= $band) { $druk_excl += (float)($kmatrix[$band] ?? 0); break; }
            }
        }
    }

    // Volumekorting
    $vol_pct = 0;
    usort($vol_staffels, fn($a,$b) => $b['min'] <=> $a['min']);
    foreach ($vol_staffels as $s) {
        if ($order_totaal_stuks >= (int)$s['min']) { $vol_pct = (float)$s['pct']; break; }
    }

    $prijs_voor  = round($textiel_excl + $druk_excl, 4);
    $korting     = round($prijs_voor * ($vol_pct / 100), 4);
    $prijs_excl  = round($prijs_voor - $korting, 4);
    $prijs_incl  = round($prijs_excl * 1.21, 4);
    $totaal_excl = round($prijs_excl * $aantal, 2);
    $totaal_incl = round($prijs_incl * $aantal, 2);
    $inkoop_totaal = round($inkoop_excl * $aantal, 2); // voor winstberekening

    return $regel + [
        'product_naam' => trim(($product['brand']??'').' '.($product['name']??'')),
        'tier'         => $tier,
        'inkoop_excl'  => round($inkoop_excl, 4), // intern — niet tonen aan klant
        'inkoop_totaal'=> $inkoop_totaal,
        'prijs' => [
            'textiel_excl'      => round($textiel_excl, 2),
            'druk_excl'         => round($druk_excl, 2),
            'prijs_excl_voor'   => round($prijs_voor, 2),
            'volumekorting_pct' => $vol_pct,
            'korting_per_stuk'  => round($korting, 2),
            'prijs_excl'        => round($prijs_excl, 2),
            'prijs_incl'        => round($prijs_incl, 2),
            'totaal_excl'       => $totaal_excl,
            'totaal_incl'       => $totaal_incl,
            'btw'               => round($totaal_incl - $totaal_excl, 2),
        ],
        'fout' => null,
    ];
}

// ── Instellingen ──────────────────────────────────────────────────────────────
$marges       = getSetting('marges', ['textiel_budget'=>1.55,'textiel_standaard'=>1.45,'textiel_premium'=>1.35]);
$drukkosten   = getSetting('drukkosten', ['dtf'=>['matrix'=>['1-9'=>9.00,'10-50'=>7.00,'50+'=>6.00]],'zeef'=>['matrix'=>[]]]);
$vol_staffels = getSetting('volumekorting', [['min'=>50,'pct'=>5],['min'=>100,'pct'=>8],['min'=>250,'pct'=>12]]);

// ── Router ────────────────────────────────────────────────────────────────────
try { switch ($actie) {

    case 'toevoegen': {
        $regel = $input['regel'] ?? [];
        $fout  = valideerRegel($regel);
        if ($fout) fout($fout);

        if (strlen($wagen_token) !== 32) {
            $wagen_token = nieuweToken();
            $_SESSION['mm_wagen_token'] = $wagen_token;
        }

        $maten  = array_filter((array)($regel['maten'] ?? []), fn($v) => (int)$v > 0);
        $maten  = array_map('intval', $maten);
        $aantal = array_sum($maten);

        $regels = laadWagenRegels($wagen_token);
        $nieuwe_regel = [
            'id'         => bin2hex(random_bytes(8)),
            'sku'        => preg_replace('/[^a-zA-Z0-9\-_]/', '', $regel['sku']),
            'techniek'   => $regel['techniek'],
            'kleur_code' => substr($regel['kleur_code'] ?? '', 0, 50),
            'kleur_naam' => substr($regel['kleur_naam'] ?? '', 0, 80),
            'posities'   => array_slice($regel['posities'] ?? [['positie'=>'voorkant','kleuren'=>1]], 0, 4),
            'maten'      => $maten,
            'aantal'     => $aantal,
            'notitie'    => substr($regel['notitie'] ?? '', 0, 300),
            'upload_token' => null, // wordt later ingevuld via upload actie
        ];

        $regels[] = $nieuwe_regel;
        slaWagenOp($wagen_token, $regels, $sessie_id);

        $order_totaal = array_sum(array_column($regels, 'aantal'));
        $met_prijs    = berekenRegelPrijs($nieuwe_regel, $marges, $drukkosten, $vol_staffels, $order_totaal);
        // Verwijder interne inkoopdata uit response
        unset($met_prijs['inkoop_excl'], $met_prijs['inkoop_totaal']);

        echo json_encode([
            'ok'            => true,
            'wagen_token'   => $wagen_token,
            'regel_id'      => $nieuwe_regel['id'],
            'totaal_regels' => count($regels),
            'regel'         => $met_prijs,
        ], JSON_UNESCAPED_UNICODE);
        break;
    }

    case 'upload': {
        // Multipart upload — wagen_token + regel_id via $_POST
        $wt = preg_replace('/[^a-f0-9]/', '', $_POST['wagen_token'] ?? '');
        $ri = preg_replace('/[^a-f0-9]/', '', $_POST['regel_id']    ?? '');
        if (strlen($wt) !== 32) fout('Geen geldig wagen_token');
        if (empty($ri))         fout('Geen regel_id');

        // Verificeer dat de regel in deze wagen zit
        $regels = laadWagenRegels($wt);
        $regel_idx = null;
        foreach ($regels as $i => $r) {
            if ($r['id'] === $ri) { $regel_idx = $i; break; }
        }
        if ($regel_idx === null) fout('Regel niet gevonden in wagen');

        $result = verwerkUpload($wt, $ri);
        if (!$result['ok']) fout($result['fout']);

        // Koppel upload token aan regel
        $regels[$regel_idx]['upload_token'] = $result['upload_token'];
        $regels[$regel_idx]['upload_naam']  = $result['bestandsnaam'];
        slaWagenOp($wt, $regels, $sessie_id);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        break;
    }

    case 'bijwerken': {
        $regel_id = $input['regel_id'] ?? '';
        $updates  = $input['updates']  ?? [];
        if (strlen($wagen_token) !== 32) fout('Geen wagen_token');
        if (empty($regel_id))            fout('Geen regel_id');

        $regels   = laadWagenRegels($wagen_token);
        $gevonden = false;

        foreach ($regels as &$r) {
            if ($r['id'] !== $regel_id) continue;
            $gevonden = true;
            if (isset($updates['maten'])) {
                $maten = array_filter((array)$updates['maten'], fn($v) => (int)$v > 0);
                $r['maten']  = array_map('intval', $maten);
                $r['aantal'] = array_sum($r['maten']);
            }
            if (isset($updates['techniek']) && in_array($updates['techniek'],['dtf','zeefdruk','borduren']))
                $r['techniek'] = $updates['techniek'];
            if (isset($updates['posities']))
                $r['posities'] = array_slice($updates['posities'], 0, 4);
            if (isset($updates['kleur_code']))
                $r['kleur_code'] = substr($updates['kleur_code'], 0, 50);
            if (isset($updates['kleur_naam']))
                $r['kleur_naam'] = substr($updates['kleur_naam'], 0, 80);
            if (isset($updates['notitie']))
                $r['notitie'] = substr($updates['notitie'], 0, 300);
            break;
        }
        unset($r);

        if (!$gevonden) fout('Regel niet gevonden');
        slaWagenOp($wagen_token, $regels, $sessie_id);
        echo json_encode(['ok'=>true,'wagen_token'=>$wagen_token]);
        break;
    }

    case 'verwijderen': {
        $regel_id = $input['regel_id'] ?? '';
        if (strlen($wagen_token) !== 32) fout('Geen wagen_token');
        $regels = laadWagenRegels($wagen_token);
        $regels = array_values(array_filter($regels, fn($r) => $r['id'] !== $regel_id));
        slaWagenOp($wagen_token, $regels, $sessie_id);
        echo json_encode(['ok'=>true,'totaal_regels'=>count($regels)]);
        break;
    }

    case 'laden': {
        if (strlen($wagen_token) !== 32) {
            echo json_encode(['ok'=>true,'regels'=>[],'totalen'=>null,'wagen_token'=>null]);
            break;
        }
        $regels = laadWagenRegels($wagen_token);
        if (empty($regels)) {
            echo json_encode(['ok'=>true,'regels'=>[],'totalen'=>null,'wagen_token'=>$wagen_token]);
            break;
        }
        $order_totaal_stuks = array_sum(array_column($regels, 'aantal'));

        $regels_prijs = array_map(
            fn($r) => berekenRegelPrijs($r, $marges, $drukkosten, $vol_staffels, $order_totaal_stuks),
            $regels
        );

        // Verwijder interne inkoopdata
        $regels_client = array_map(function($r) {
            unset($r['inkoop_excl'],$r['inkoop_totaal']);
            return $r;
        }, $regels_prijs);

        $subtotaal_excl = array_sum(array_map(
            fn($r) => ($r['prijs']['prijs_excl_voor'] ?? 0) * $r['aantal'],
            $regels_prijs
        ));

        // Volumekorting
        $vol_pct = 0; $vol_label = '';
        usort($vol_staffels, fn($a,$b) => $b['min'] <=> $a['min']);
        foreach ($vol_staffels as $s) {
            if ($order_totaal_stuks >= (int)$s['min']) { $vol_pct=(float)$s['pct']; $vol_label=$s['min'].'+ stuks → '.$vol_pct.'% korting'; break; }
        }

        $vol_korting = round($subtotaal_excl * ($vol_pct/100), 2);
        $totaal_excl = round($subtotaal_excl - $vol_korting, 2);
        $totaal_incl = round($totaal_excl * 1.21, 2);
        $btw         = round($totaal_incl - $totaal_excl, 2);

        if ($order_totaal_stuks <= 5)      { $verzend=6.95;  $vl='Klein pakket'; $va=false; }
        elseif ($order_totaal_stuks <= 14) { $verzend=13.95; $vl='Groot pakket'; $va=false; }
        else                               { $verzend=0.0;   $vl='Achteraf';     $va=true; }

        echo json_encode([
            'ok'          => true,
            'wagen_token' => $wagen_token,
            'regels'      => $regels_client,
            'totalen'     => [
                'totaal_stuks'       => $order_totaal_stuks,
                'subtotaal_excl'     => round($subtotaal_excl, 2),
                'vol_pct'            => $vol_pct,
                'vol_label'          => $vol_label,
                'vol_korting'        => $vol_korting,
                'totaal_excl'        => $totaal_excl,
                'totaal_incl'        => $totaal_incl,
                'btw'                => $btw,
                'verzend_excl'       => $va ? null : $verzend,
                'verzend_label'      => $vl,
                'verzend_achteraf'   => $va,
                'totaal_met_verzend' => $va ? null : round($totaal_incl + $verzend, 2),
            ],
        ], JSON_UNESCAPED_UNICODE);
        break;
    }

    case 'leegmaken': {
        if (strlen($wagen_token) === 32) slaWagenOp($wagen_token, [], $sessie_id);
        echo json_encode(['ok'=>true]);
        break;
    }

    case 'naar_offerte': {
        if (strlen($wagen_token) !== 32) fout('Geen wagen_token');
        $regels = laadWagenRegels($wagen_token);
        if (empty($regels)) fout('Wagen is leeg');

        $klant  = $input['klant']  ?? [];
        $spoed  = !empty($input['spoed']);
        if ($spoed && !empty($input['betaling_methode'])) fout('Spoedorders kunnen niet online betaald worden.');

        $order_totaal_stuks = array_sum(array_column($regels, 'aantal'));
        $regels_prijs = array_map(
            fn($r) => berekenRegelPrijs($r, $marges, $drukkosten, $vol_staffels, $order_totaal_stuks),
            $regels
        );

        $subtotaal     = array_sum(array_map(fn($r) => ($r['prijs']['prijs_excl_voor']??0)*$r['aantal'], $regels_prijs));
        $inkoop_totaal = array_sum(array_map(fn($r) => ($r['inkoop_totaal']??0), $regels_prijs));

        $vol_pct = 0;
        usort($vol_staffels, fn($a,$b) => $b['min'] <=> $a['min']);
        foreach ($vol_staffels as $s) {
            if ($order_totaal_stuks >= (int)$s['min']) { $vol_pct=(float)$s['pct']; break; }
        }
        $vol_korting   = round($subtotaal * ($vol_pct/100), 2);
        $totaal_excl   = round($subtotaal - $vol_korting, 2);
        $totaal_incl   = round($totaal_excl * 1.21, 2);
        $spoed_toeslag = $spoed ? round($totaal_incl * 0.40, 2) : 0;
        $winst_excl    = round($totaal_excl - $inkoop_totaal, 2);

        getDB()->exec("CREATE TABLE IF NOT EXISTS `offertes` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `token` CHAR(32) NOT NULL UNIQUE,
            `klant_naam` VARCHAR(120), `klant_email` VARCHAR(180),
            `klant_tel` VARCHAR(40), `klant_bedrijf` VARCHAR(120),
            `regels` MEDIUMTEXT NOT NULL,
            `subtotaal` DECIMAL(10,2), `vol_pct` DECIMAL(5,2),
            `vol_korting` DECIMAL(10,2), `totaal_excl` DECIMAL(10,2),
            `totaal_incl` DECIMAL(10,2), `spoed` TINYINT(1) DEFAULT 0,
            `spoed_toeslag` DECIMAL(10,2) DEFAULT 0,
            `inkoop_totaal` DECIMAL(10,2) DEFAULT 0,
            `winst_excl` DECIMAL(10,2) DEFAULT 0,
            `status` VARCHAR(20) DEFAULT 'concept',
            `geldig_tot` DATE,
            `aangemaakt` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `bijgewerkt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Verwijder interne inkoopdata uit opgeslagen regels
        $regels_opslaan = array_map(function($r) {
            unset($r['inkoop_excl'],$r['inkoop_totaal']);
            return $r;
        }, $regels_prijs);

        $token      = bin2hex(random_bytes(16));
        $geldig_tot = date('Y-m-d', strtotime('+30 days'));

        getDB()->prepare(
            "INSERT INTO offertes
             (token,klant_naam,klant_email,klant_tel,klant_bedrijf,
              regels,subtotaal,vol_pct,vol_korting,totaal_excl,totaal_incl,
              spoed,spoed_toeslag,inkoop_totaal,winst_excl,geldig_tot)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        )->execute([
            $token,
            substr($klant['naam']    ?? '', 0, 120),
            substr($klant['email']   ?? '', 0, 180),
            substr($klant['tel']     ?? '', 0, 40),
            substr($klant['bedrijf'] ?? '', 0, 120),
            json_encode($regels_opslaan, JSON_UNESCAPED_UNICODE),
            $subtotaal, $vol_pct, $vol_korting,
            $totaal_excl, $totaal_incl,
            $spoed ? 1 : 0, $spoed_toeslag,
            $inkoop_totaal, $winst_excl,
            $geldig_tot,
        ]);

        // Wagen leegmaken
        slaWagenOp($wagen_token, [], $sessie_id);
        unset($_SESSION['mm_wagen_token']);

        echo json_encode([
            'ok'           => true,
            'token'        => $token,
            'pdf_url'      => '/bestellen/offerte_pdf.php?token='.$token,
            'geldig_tot'   => $geldig_tot,
            'totaal_incl'  => $totaal_incl,
            'spoed'        => $spoed,
            'spoed_toeslag'=> $spoed_toeslag,
            'spoed_waarschuwing' => $spoed
                ? 'Neem contact op via info@merch-master.com om de spoedboeking te bevestigen.'
                : null,
            'online_betalen_geblokkeerd' => $spoed,
        ], JSON_UNESCAPED_UNICODE);
        break;
    }

    default: fout('Onbekende actie: '.htmlspecialchars($actie));

}} catch (Exception $e) {
    error_log('MerchMaster wagen fout: '.$e->getMessage());
    echo json_encode(['ok'=>false,'fout'=>'Er is een fout opgetreden. Probeer het opnieuw.']);
}

function fout(string $msg): void {
    echo json_encode(['ok'=>false,'fout'=>$msg]); exit;
}
