<?php
/**
 * Merch Master — Prijsberekening API (beveiligd)
 * POST /bestellen/prijs.php
 *
 * Input (JSON):
 * {
 *   techniek:     'dtf' | 'zeefdruk' | 'borduren',
 *   aantal:       int,
 *   kleuren:      int  (1-4, alleen zeefdruk),
 *   sku:          string,
 *   order_totaal_stuks: int  (optioneel — totaal stuks over hele offerte voor volumekorting)
 * }
 */

// ── CORS ──────────────────────────────────────────────────────────────────────
$toegestane_origins = ['https://merch-master.com','https://www.merch-master.com'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $toegestane_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { fout('Alleen POST toegestaan'); }

// ── Rate limiting ─────────────────────────────────────────────────────────────
function checkRateLimit(): void {
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = sys_get_temp_dir() . '/mm_rl_' . md5($ip);
    $nu  = time();
    $data = ['calls' => []];
    if (file_exists($key)) { $raw = @file_get_contents($key); if ($raw) $data = json_decode($raw, true) ?? $data; }
    $data['calls'] = array_values(array_filter($data['calls'], fn($t) => ($nu - $t) < 60));
    if (count($data['calls']) >= 60) {
        http_response_code(429);
        echo json_encode(['ok' => false, 'fout' => 'Te veel verzoeken. Probeer over een minuut opnieuw.']);
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
        $st = getDB()->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel = ?");
        $st->execute([$key]);
        $row = $st->fetch();
        if (!$row) return $default;
        $d = json_decode($row['waarde'], true);
        return $d !== null ? $d : $row['waarde'];
    } catch (Exception $e) { return $default; }
}

// ── Input ─────────────────────────────────────────────────────────────────────
$input              = json_decode(file_get_contents('php://input'), true) ?? [];
$techniek           = strtolower(trim($input['techniek'] ?? ''));
$aantal             = (int)($input['aantal']   ?? 0);
$kleuren            = (int)($input['kleuren']  ?? 1);
$sku                = trim($input['sku']        ?? '');
$order_totaal_stuks = (int)($input['order_totaal_stuks'] ?? $aantal);
$toon_incl_btw      = ($input['btw'] ?? 'incl') !== 'excl';  // default: incl BTW

if (!in_array($techniek, ['dtf','zeefdruk','borduren'])) fout('Ongeldige techniek');
if ($aantal < 1 || $aantal > 100000)                     fout('Ongeldig aantal');
if ($kleuren < 1 || $kleuren > 4)                        $kleuren = 1;
if (empty($sku))                                          fout('Geen SKU opgegeven');
if (!preg_match('/^[a-zA-Z0-9\-_]{1,50}$/', $sku))       fout('Ongeldige SKU');

// ── Product uit DB ────────────────────────────────────────────────────────────
try {
    $st = getDB()->prepare("SELECT inkoop, tier, name, brand, actief FROM catalogus WHERE sku = ? LIMIT 1");
    $st->execute([$sku]);
    $product = $st->fetch();
} catch (Exception $e) { fout('Databasefout'); }

if (!$product)           fout('Product niet gevonden');
if (!$product['actief']) fout('Product niet beschikbaar');

$inkoop  = (float)$product['inkoop'];
$db_tier = $product['tier'] ?? null;
if ($inkoop <= 0) fout('Ongeldige inkoopprijs in catalogus');

// ── Borduren → aanvraag ───────────────────────────────────────────────────────
if ($techniek === 'borduren') {
    echo json_encode(['ok'=>true,'op_aanvraag'=>true,
        'bericht'=>'Borduren is op aanvraag. Neem contact op voor een offerte.']);
    exit;
}

// ── Zeefdruk minimum ──────────────────────────────────────────────────────────
if ($techniek === 'zeefdruk' && $aantal < 25) {
    echo json_encode(['ok'=>false,'min_oplage'=>true,'suggestie'=>'dtf',
        'fout'=>'Zeefdruk heeft een minimale oplage van 25 stuks. Kies DTF voor kleinere aantallen — beschikbaar vanaf 1 stuk.']);
    exit;
}

// ── Instellingen ──────────────────────────────────────────────────────────────
$marges = getSetting('marges', [
    'textiel_budget'=>1.55,'textiel_standaard'=>1.45,'textiel_premium'=>1.35
]);
$drukkosten = getSetting('drukkosten', [
    'dtf' =>['matrix'=>['1-9'=>9.00,'10-50'=>7.00,'50+'=>6.00]],
    'zeef'=>['matrix'=>[]],
]);
$volumekorting_staffels = getSetting('volumekorting', [
    ['min'=>50,  'pct'=>5],
    ['min'=>100, 'pct'=>8],
    ['min'=>250, 'pct'=>12],
]);

// ── Tier ──────────────────────────────────────────────────────────────────────
if (in_array($db_tier, ['budget','standaard','premium'], true)) { $tier = $db_tier; }
elseif ($inkoop < 4.00)  { $tier = 'budget'; }
elseif ($inkoop <= 10.00){ $tier = 'standaard'; }
else                      { $tier = 'premium'; }

$marge        = (float)($marges['textiel_'.$tier] ?? 1.45);
$textiel_excl = round($inkoop * $marge, 4);

// ── Drukkosten — direct uit matrix, geen aparte drukmarge ────────────────────
$druk_excl = 0.0; $oplage_label = '';

if ($techniek === 'dtf') {
    $m = $drukkosten['dtf']['matrix'] ?? ['1-9'=>9.00,'10-50'=>7.00,'50+'=>6.00];
    if ($aantal <= 9)      { $druk_excl = (float)($m['1-9']  ??9.00); $oplage_label='1–9 stuks'; }
    elseif ($aantal <= 50) { $druk_excl = (float)($m['10-50']??7.00); $oplage_label='10–50 stuks'; }
    else                   { $druk_excl = (float)($m['50+']  ??6.00); $oplage_label='50+ stuks'; }

} elseif ($techniek === 'zeefdruk') {
    $m       = $drukkosten['zeef']['matrix'] ?? [];
    $kmatrix = $m[(string)$kleuren] ?? ($m[$kleuren] ?? []);
    $banden  = [10000,5000,2500,1000,500,250,100,50,25];
    $lbls    = [10000=>'10.000+',5000=>'5.000–9.999',2500=>'2.500–4.999',
                1000=>'1.000–2.499',500=>'500–999',250=>'250–499',
                100=>'100–249',50=>'50–99',25=>'25–49'];
    $gevonden = false;
    foreach ($banden as $band) {
        if ($aantal >= $band) {
            $druk_excl    = (float)($kmatrix[$band] ?? 0);
            $oplage_label = $lbls[$band]; $gevonden = true; break;
        }
    }
    if (!$gevonden)      fout('Geen oplageklasse gevonden');
    if ($druk_excl <= 0) fout('Geen drukkosten geconfigureerd voor deze combinatie — controleer de admin.');
}

// ── Prijs per stuk vóór volumekorting ─────────────────────────────────────────
$prijs_excl_voor = round($textiel_excl + $druk_excl, 4);

// ── Volumekorting ─────────────────────────────────────────────────────────────
// Op basis van order_totaal_stuks (totaal over hele offerte)
// Korting wordt toegepast op textiel + druk samen
$vol_pct   = 0;
$vol_label = '';

// Sorteer staffels op min DESC zodat hoogste toepasselijke wins
usort($volumekorting_staffels, fn($a,$b) => $b['min'] <=> $a['min']);
foreach ($volumekorting_staffels as $staffel) {
    if ($order_totaal_stuks >= (int)$staffel['min']) {
        $vol_pct   = (float)$staffel['pct'];
        $vol_label = $staffel['min'] . '+ stuks → ' . $vol_pct . '% korting';
        break;
    }
}

$korting_per_stuk = round($prijs_excl_voor * ($vol_pct / 100), 4);
$prijs_excl       = round($prijs_excl_voor - $korting_per_stuk, 4);
$prijs_incl       = round($prijs_excl * 1.21, 4);
$totaal_excl      = round($prijs_excl * $aantal, 2);
$totaal_incl      = round($prijs_incl * $aantal, 2);

// ── Verzendkosten ─────────────────────────────────────────────────────────────
if ($aantal <= 5)      { $verzend_excl=6.95;  $verzend_label='Klein pakket (≤5 stuks)';   $verzend_achteraf=false; }
elseif ($aantal <= 14) { $verzend_excl=13.95; $verzend_label='Groot pakket (6–14 stuks)'; $verzend_achteraf=false; }
else                   { $verzend_excl=0.0;   $verzend_label='Verzendkosten achteraf';     $verzend_achteraf=true; }

$totaal_incl_verzend = $verzend_achteraf ? null : round($totaal_incl + $verzend_excl, 2);

// ── Response ──────────────────────────────────────────────────────────────────
echo json_encode([
    'ok'                  => true,
    'op_aanvraag'         => false,
    'techniek'            => $techniek,
    'aantal'              => $aantal,
    'tier'                => $tier,
    'oplage_label'        => $oplage_label,
    'product_naam'        => trim(($product['brand']??'').' '.($product['name']??'')),
    // Per stuk
    'textiel_excl'        => round($textiel_excl, 2),
    'druk_excl'           => round($druk_excl, 2),
    'prijs_excl_voor'     => round($prijs_excl_voor, 2),
    'volumekorting_pct'   => $vol_pct,
    'volumekorting_label' => $vol_label,
    'korting_per_stuk'    => round($korting_per_stuk, 2),
    'prijs_excl'          => round($prijs_excl, 2),
    'prijs_incl'          => round($prijs_incl, 2),
    // Totalen — altijd beide zodat UI zelf kan schakelen
    'totaal_excl'         => $totaal_excl,
    'totaal_incl'         => $totaal_incl,
    'btw'                 => round($totaal_incl - $totaal_excl, 2),
    'btw_pct'             => 21,
    // Actieve weergave op basis van btw-keuze van de aanvrager
    'weergave_per_stuk'   => $toon_incl_btw ? round($prijs_incl, 2) : round($prijs_excl, 2),
    'weergave_totaal'     => $toon_incl_btw ? $totaal_incl : $totaal_excl,
    'weergave_btw_label'  => $toon_incl_btw ? 'incl. BTW' : 'excl. BTW',
    // Verzending
    'verzend_excl'        => $verzend_achteraf ? null : $verzend_excl,
    'verzend_label'       => $verzend_label,
    'verzend_achteraf'    => $verzend_achteraf,
    'totaal_incl_verzend' => $totaal_incl_verzend,
], JSON_UNESCAPED_UNICODE);

function fout(string $msg): void {
    echo json_encode(['ok'=>false,'fout'=>$msg]); exit;
}
