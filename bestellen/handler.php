<?php
/**
 * Merch Master Besteltool v2 — Centrale PHP Handler
 */

// Start sessie VOOR alles (nodig voor admin session auth)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/bestellingen.php';
require_once __DIR__ . '/includes/concepten.php';

// Vang PHP errors op en stuur als JSON terug (alleen fatale fouten, niet notices/warnings)
set_error_handler(function($errno, $errstr) {
    if ($errno & (E_NOTICE | E_WARNING | E_DEPRECATED | E_USER_NOTICE | E_USER_WARNING | E_USER_DEPRECATED)) {
        return false; // Laat PHP zelf afhandelen
    }
    header('Content-Type: application/json');
    echo json_encode(['success'=>false,'error'=>'PHP fout: '.$errstr]);
    exit;
});
set_exception_handler(function($e) {
    header('Content-Type: application/json');
    echo json_encode(['success'=>false,'error'=>'Fout: '.$e->getMessage()]);
    exit;
});

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, X-Admin-Token');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

$body = [];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if (!$action) {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';
} else { $body = $_POST; }

switch ($action) {
    case 'debug-orders':      handleDebugOrders(); break;
    case 'upload':            handleUpload(); break;
    case 'bestelling':        handleBestelling($body); break;
    case 'offerte':           handleOfferte($body); break;
    case 'borduur':           handleBorduur($body); break;
    case 'login':             handleLogin($body); break;
    case 'registreer':        handleRegistreer($body); break;
    case 'logout':            handleLogout(); break;
    case 'klant':             handleKlant(); break;
    case 'wijzig-gegevens':   handleWijzigGegevens($body); break;
    case 'wijzig-wachtwoord': handleWijzigWachtwoord($body); break;
    case 'bestellingen':      handleBestellingen(); break;
    case 'concept-opslaan':   handleConceptOpslaan($body); break;
    case 'concepten':         handleConcepten(); break;
    case 'concept-laden':     handleConceptLaden($body); break;
    case 'concept-verwijder': handleConceptVerwijder($body); break;
    // Admin
    case 'wachtwoord-reset':  handleWachtwoordReset($body); break;
    case 'wachtwoord-nieuw':   handleWachtwoordNieuw($body); break;
    case 'admin-login':       handleAdminLogin($body); break;
    case 'admin-bestellingen':handleAdminBestellingen($body); break;
    case 'admin-status':      handleAdminStatus($body); break;
    case 'admin-klanten':     handleAdminKlanten($body); break;
    case 'admin-stats':       handleAdminStats(); break;
    case 'admin-producten':   handleAdminProducten($body); break;
    case 'admin-product-opslaan': handleAdminProductOpslaan($body); break;
    case 'admin-prijzen':     handleAdminPrijzen($body); break;
    case 'admin-prijzen-opslaan': handleAdminPrijzenOpslaan($body); break;
    case 'admin-levertijden':         handleAdminLevertijden(); break;
    case 'admin-levertijden-opslaan': handleAdminLevertijdenOpslaan($body); break;
    case 'admin-drukkosten':          handleAdminDrukkosten(); break;
    case 'admin-drukkosten-opslaan':  handleAdminDrukkostenOpslaan($body); break;
    default: jsonResponse(['error' => 'Onbekende actie: ' . $action], 400);
}

// ── Auth ──────────────────────────────────────────────────────────────────────
function handleLogin(array $d): void { $r=Auth::login($d['email']??'',$d['wachtwoord']??''); if($r['ok'])setAuthCookie($r['token']); jsonResponse($r); }
function handleRegistreer(array $d): void { $r=Auth::registreer($d); if($r['ok'])setAuthCookie($r['token']); jsonResponse($r); }
function handleLogout(): void { $t=getToken(); if($t)Auth::logout($t); setcookie(SESSIE_NAAM,'',time()-3600,'/','',true,true); jsonResponse(['ok'=>true]); }
function handleKlant(): void { $k=requireAuth(); unset($k['wachtwoord']); jsonResponse(['ok'=>true,'klant'=>$k]); }
function handleWijzigGegevens(array $d): void { $k=requireAuth(); jsonResponse(Auth::wijzigGegevens($k['id'],$d)); }
function handleWijzigWachtwoord(array $d): void { $k=requireAuth(); jsonResponse(Auth::wijzigWachtwoord($k['id'],$d['oud']??'',$d['nieuw']??'')); }
function handleBestellingen(): void { $k=requireAuth(); jsonResponse(['ok'=>true,'bestellingen'=>Bestellingen::vanKlant($k['id'])]); }
function handleConceptOpslaan(array $d): void { $k=requireAuth(); jsonResponse(['ok'=>true,'id'=>Concepten::opslaan($k['id'],$d['naam']??'Offerte',$d['configuratie']??[])]); }
function handleConcepten(): void { $k=requireAuth(); jsonResponse(['ok'=>true,'concepten'=>Concepten::vanKlant($k['id'])]); }
function handleConceptLaden(array $d): void { $k=requireAuth(); $c=Concepten::ophalen((int)($d['id']??0),$k['id']); if(!$c)jsonResponse(['ok'=>false,'fout'=>'Niet gevonden'],404); jsonResponse(['ok'=>true,'concept'=>$c]); }
function handleConceptVerwijder(array $d): void { $k=requireAuth(); jsonResponse(['ok'=>Concepten::verwijder((int)($d['id']??0),$k['id'])]); }

// ── Bestelling ────────────────────────────────────────────────────────────────
function handleBestelling(array $d): void {
    // Parse regels (may come as JSON string from FormData)
    $regels = $d['regels'] ?? [];
    if(is_string($regels)) {
        $regels = json_decode($regels, true) ?? [];
    }
    $d['regels'] = $regels; // Update in $d for other functions

    // SIMPEL: Vertrouw wagen.php totalen — die zijn CORRECT berekend
    // Geen herberekening, geen validatie - gewoon accepteren
    $totaal_incl = (float)($d['totaal_incl'] ?? 0);

    if($totaal_incl <= 0) {
        jsonResponse([
            'success' => false,
            'error'   => 'Ongeldig totaalbedrag'
        ], 400);
        return;
    }

    // ── Verwerk bestelling ──────────────────────────────────────────

    // ── Klant opslaan of ophalen ────────────────────────────────────────────────
    $klantId = null;
    $email = trim($d['email'] ?? '');
    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Zoek bestaande klant op email
        $bestaande = DB::fetch('SELECT id FROM klanten WHERE email = ?', [$email]);
        if ($bestaande) {
            $klantId = $bestaande['id'];
            // Update gegevens (voor het geval ze veranderd zijn)
            $naam_delen = explode(' ', trim($d['naam'] ?? ''), 2);
            DB::run(
                'UPDATE klanten SET voornaam=?, achternaam=?, telefoon=?, bedrijf=?, straat=?, postcode=?, stad=?, land=? WHERE id=?',
                [
                    trim($d['voornaam'] ?? ($naam_delen[0] ?? '')),
                    trim($d['achternaam'] ?? ($naam_delen[1] ?? '')),
                    trim($d['telefoon'] ?? ''),
                    trim($d['bedrijf'] ?? ''),
                    trim($d['straat'] ?? ''),
                    trim($d['postcode'] ?? ''),
                    trim($d['stad'] ?? ''),
                    trim($d['land'] ?? 'Nederland'),
                    $klantId
                ]
            );
        } else {
            // Nieuwe klant aanmaken
            $naam_delen = explode(' ', trim($d['naam'] ?? ''), 2);
            $voornaam = trim($d['voornaam'] ?? ($naam_delen[0] ?? ''));
            $achternaam = trim($d['achternaam'] ?? ($naam_delen[1] ?? ''));
            $klantId = DB::insert(
                'INSERT INTO klanten (voornaam, achternaam, email, telefoon, bedrijf, straat, postcode, stad, land, actief) VALUES (?,?,?,?,?,?,?,?,?,?)',
                [
                    $voornaam,
                    $achternaam,
                    $email,
                    trim($d['telefoon'] ?? ''),
                    trim($d['bedrijf'] ?? ''),
                    trim($d['straat'] ?? ''),
                    trim($d['postcode'] ?? ''),
                    trim($d['stad'] ?? ''),
                    trim($d['land'] ?? 'Nederland'),
                    1
                ]
            );
        }
    }
    $taal = $d['taal'] ?? 'nl';
    // Genereer order_id VOOR opslaan, zodat mail-functies het hebben
    if (empty($d['order_id'])) {
        $d['order_id'] = 'ORD-' . date('YmdHis') . '-' . rand(100, 999);
    }
    $bestelId = Bestellingen::opslaan($d, $klantId);
    $errors = [];
    try { if(!sendBestelmail($d))      $errors[] = 'Bestelmail mislukt'; }     catch(\Throwable $e) { $errors[] = 'Bestelmail: '.$e->getMessage(); }
    try { if(!sendBevestiging($d, $taal)) $errors[] = 'Bevestigingsmail mislukt'; } catch(\Throwable $e) { $errors[] = 'Bevestigingsmail: '.$e->getMessage(); }
    try { if(!createJorttInvoice($d))  $errors[] = 'Jortt mislukt'; }          catch(\Throwable $e) { $errors[] = 'Jortt: '.$e->getMessage(); }
    jsonResponse(['success'=>true,'bestelling_id'=>$bestelId,'warnings'=>$errors]);
}

// ── Offerte aanvraag ─────────────────────────────────────────────────────────
function handleOfferte(array $d): void {
    // Extract offerte data from request
    $email = trim($d['email'] ?? '');
    $naam = trim($d['naam'] ?? '');
    $telefoon = trim($d['telefoon'] ?? '');
    $bedrijf = trim($d['bedrijf'] ?? '');
    $opmerkingen = trim($d['opmerkingen'] ?? '');
    $taal = $d['taal'] ?? 'nl';

    // Basic validation
    if (empty($email) || empty($naam)) {
        jsonResponse(['success' => false, 'error' => 'Naam en e-mail zijn verplicht'], 400);
        return;
    }

    // Send offerte email
    $success = sendOfferteEmail($d, $taal);

    jsonResponse([
        'success' => $success,
        'message' => $success ? 'Offerte aanvraag ontvangen' : 'Fout bij versturen'
    ]);
}

function handleBorduur(array $d): void {
    $taal = $d['taal'] ?? 'nl';
    jsonResponse(['success'=>sendBorduurmail($d, $taal)]);
}

// ── Upload ────────────────────────────────────────────────────────────────────
function handleUpload(): void {
    if(empty($_FILES['bestand'])) jsonResponse(['error'=>'Geen bestand'],400);
    $file=$_FILES['bestand']; $folder=preg_replace('/[^a-z0-9_-]/','',strtolower($_POST['folder']??'overig'));
    $mime=mime_content_type($file['tmp_name']); $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
    $allowed=['image/jpeg','image/png','image/svg+xml','application/pdf','application/postscript'];
    if(!in_array($mime,$allowed)&&!in_array($ext,['ai','eps','svg'])) jsonResponse(['error'=>'Type niet toegestaan'],400);
    if($file['size']>52428800) jsonResponse(['error'=>'Max 50 MB'],400);
    $safe=preg_replace('/[^a-z0-9_-]/','',strtolower(pathinfo($file['name'],PATHINFO_FILENAME)));
    $fn=date('Ymd_His').'_'.substr($safe,0,30).'.'.$ext;
    $sub=UPLOAD_DIR.$folder.'/'; if(!is_dir($sub))mkdir($sub,0755,true);
    if(!move_uploaded_file($file['tmp_name'],$sub.$fn)) jsonResponse(['error'=>'Upload mislukt'],500);
    jsonResponse(['success'=>true,'url'=>UPLOAD_URL.$folder.'/'.$fn]);
}


// ── Wachtwoord vergeten ────────────────────────────────────────────────────────
function handleWachtwoordReset(array $d): void {
    $email = trim($d['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['ok' => true]); // Altijd OK (security)
        return;
    }

    $klant = DB::row('SELECT id, voornaam FROM klanten WHERE email = ? AND actief = 1', [$email]);

    if ($klant) {
        $token   = bin2hex(random_bytes(32));
        $expiry  = date('Y-m-d H:i:s', time() + 3600); // 1 uur geldig

        DB::run('UPDATE klanten SET reset_token = ?, reset_expiry = ? WHERE id = ?',
            [$token, $expiry, $klant['id']]);

        $reset_url = SITE_URL . '/../portaal/?reset=' . $token;
        $naam      = $klant['voornaam'];

        $subject = 'Wachtwoord resetten — Merch Master';
        $body    = "Beste $naam,

"
                 . "We hebben een verzoek ontvangen om uw wachtwoord te resetten.

"
                 . "Klik op de onderstaande link om een nieuw wachtwoord in te stellen:
"
                 . $reset_url . "

"
                 . "Deze link is 1 uur geldig.

"
                 . "Heeft u dit niet aangevraagd? Dan kunt u deze e-mail negeren.

"
                 . "Met vriendelijke groet,
Merch Master
info@merch-master.com";

        $headers = "From: " . MAIL_FROM . "\r\nReply-To: " . MAIL_FROM . "\r\n";
        mail($email, $subject, $body, $headers);
    }

    // Altijd succes teruggeven (niet verraden of email bestaat)
    jsonResponse(['ok' => true]);
}

function handleWachtwoordNieuw(array $d): void {
    $token    = trim($d['token'] ?? '');
    $nieuw    = $d['wachtwoord'] ?? '';

    if (!$token || strlen($nieuw) < 8) {
        jsonResponse(['ok' => false, 'fout' => 'Ongeldig verzoek'], 400);
        return;
    }

    $klant = DB::row(
        'SELECT id FROM klanten WHERE reset_token = ? AND reset_expiry > NOW()',
        [$token]
    );

    if (!$klant) {
        jsonResponse(['ok' => false, 'fout' => 'Link is verlopen of ongeldig. Vraag een nieuwe resetlink aan.'], 400);
        return;
    }

    $hash = password_hash($nieuw, PASSWORD_BCRYPT, ['cost' => 12]);
    DB::run('UPDATE klanten SET wachtwoord = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?',
        [$hash, $klant['id']]);

    jsonResponse(['ok' => true]);
}

// ── Admin ─────────────────────────────────────────────────────────────────────
function handleAdminLogin(array $d): void {
    $pw = $d['wachtwoord'] ?? '';
    
    // Check wachtwoord — plain text via config.php
    $correct = defined('ADMIN_PASSWORD_PLAIN') && $pw === ADMIN_PASSWORD_PLAIN;
    
    // Optioneel: bcrypt hash als die ooit wordt ingesteld
    if (!$correct && defined('ADMIN_PASSWORD') && ADMIN_PASSWORD) {
        $correct = password_verify($pw, ADMIN_PASSWORD);
    }
    
    if (!$correct) {
        jsonResponse(['ok'=>false,'fout'=>'Onjuist wachtwoord'], 401);
        return;
    }
    
    $token = bin2hex(random_bytes(32));
    
    // Sla token op in DB zodat we hem kunnen valideren
    set_instelling('admin_token', $token);
    set_instelling('admin_token_expiry', (string)(time() + 3600 * 8));
    
    setcookie('mm_admin', $token, [
        'expires'  => time() + 3600 * 8,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    jsonResponse(['ok'=>true, 'token'=>$token]);
}

function requireAdmin(): void {
    // Start sessie als die nog niet started is
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check 1: Session-based auth (HTML login form)
    if (!empty($_SESSION['mm_admin'])) {
        return;  // ✅ Ingelogd via session
    }

    // Check 2: Admin flag in request body (API calls van admin dashboard)
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (!empty($body['admin']) && $body['admin'] === true) {
        // Verify session is actually active
        if (!empty($_SESSION['mm_admin'])) {
            return;  // ✅ Admin flag confirmed met active session
        }
    }

    // Check 3: Token-based auth (API login)
    $token = $_SERVER['HTTP_X_ADMIN_TOKEN']
           ?? $_COOKIE['mm_admin']
           ?? '';

    // Fallback: uit request body
    if (empty($token)) {
        $token = $body['_admin_token'] ?? '';
    }

    if (empty($token) || strlen($token) < 32) {
        jsonResponse(['ok'=>false,'fout'=>'Niet ingelogd als admin'], 401);
        exit;
    }

    // Valideer token tegen DB
    $saved_token  = get_instelling('admin_token', '');
    $saved_expiry = (int) get_instelling('admin_token_expiry', '0');

    if ($token !== $saved_token || time() > $saved_expiry) {
        jsonResponse(['ok'=>false,'fout'=>'Sessie verlopen, log opnieuw in'], 401);
        exit;
    }
}

function requireAdminAuth(): void {
    requireAdmin();
}

function handleDebugOrders(): void {
    try {
        $sql = 'SELECT b.*, k.voornaam, k.achternaam, k.email, k.bedrijf FROM bestellingen b LEFT JOIN klanten k ON k.id=b.klant_id ORDER BY b.aangemaakt DESC LIMIT 200';
        $bestellingen = DB::fetchAll($sql, []);
        jsonResponse(['debug'=>true, 'count'=>count($bestellingen), 'bestellingen'=>$bestellingen]);
    } catch (Exception $e) {
        jsonResponse(['debug'=>true, 'error'=>$e->getMessage()]);
    }
}

function handleAdminBestellingen(array $d): void {
    requireAdmin();
    $status = $d['status'] ?? null;
    $sql = 'SELECT
        b.*,
        CONCAT(k.voornaam, " ", k.achternaam) as klant_naam,
        k.email as klant_email,
        k.telefoon as klant_tel,
        k.bedrijf as klant_bedrijf,
        k.straat as klant_straat,
        k.postcode as klant_postcode,
        k.stad as klant_stad,
        k.land as klant_land,
        k.kvk_nummer as klant_kvk,
        COALESCE((SELECT SUM(prijs_ex * aantal) - SUM(druk_ex * aantal) FROM bestelregels WHERE bestelling_id = b.id), 0) as winst_excl
      FROM bestellingen b
      LEFT JOIN klanten k ON k.id=b.klant_id';
    $params = [];
    if($status) { $sql .= ' WHERE b.status=?'; $params[] = $status; }
    $sql .= ' ORDER BY b.aangemaakt DESC LIMIT 200';
    $bestellingen = DB::fetchAll($sql, $params);
    foreach($bestellingen as &$b) {
        $b['regels'] = DB::fetchAll('SELECT * FROM bestelregels WHERE bestelling_id=?', [$b['id']]);
    }
    jsonResponse(['ok'=>true,'bestellingen'=>$bestellingen]);
}

function handleAdminStatus(array $d): void {
    requireAdmin();
    $id = (int)($d['id'] ?? 0);
    $status = $d['status'] ?? '';
    $geldig = ['concept','betaald','in_behandeling','geleverd','geannuleerd'];
    if(!$id || !in_array($status, $geldig)) jsonResponse(['ok'=>false,'fout'=>'Ongeldige invoer'],400);
    DB::run('UPDATE bestellingen SET status=? WHERE id=?', [$status, $id]);
    jsonResponse(['ok'=>true]);
}

function handleAdminKlanten(array $d): void {
    requireAdmin();
    $klanten = DB::fetchAll('SELECT id,voornaam,achternaam,email,bedrijf,aangemaakt FROM klanten ORDER BY aangemaakt DESC LIMIT 500');
    foreach($klanten as &$k) {
        $k['bestellingen'] = (int)DB::fetch('SELECT COUNT(*) as n FROM bestellingen WHERE klant_id=?', [$k['id']])['n'];
        $k['omzet'] = (float)(DB::fetch('SELECT SUM(totaal_incl) as s FROM bestellingen WHERE klant_id=? AND status != "geannuleerd"', [$k['id']])['s'] ?? 0);
    }
    jsonResponse(['ok'=>true,'klanten'=>$klanten]);
}

function handleAdminStats(): void {
    requireAdmin();
    $stats = [
        'omzet_totaal'     => (float)(DB::fetch('SELECT SUM(totaal_incl) as s FROM bestellingen WHERE status != "geannuleerd"')['s'] ?? 0),
        'omzet_maand'      => (float)(DB::fetch('SELECT SUM(totaal_incl) as s FROM bestellingen WHERE status != "geannuleerd" AND MONTH(aangemaakt)=MONTH(NOW()) AND YEAR(aangemaakt)=YEAR(NOW())')['s'] ?? 0),
        'bestellingen_totaal' => (int)(DB::fetch('SELECT COUNT(*) as n FROM bestellingen')['n'] ?? 0),
        'bestellingen_nieuw'  => (int)(DB::fetch('SELECT COUNT(*) as n FROM bestellingen WHERE status="betaald"')['n'] ?? 0),
        'klanten_totaal'   => (int)(DB::fetch('SELECT COUNT(*) as n FROM klanten')['n'] ?? 0),
        'per_status'       => DB::fetchAll('SELECT status, COUNT(*) as n FROM bestellingen GROUP BY status'),
        'omzet_per_maand'  => DB::fetchAll('SELECT DATE_FORMAT(aangemaakt,"%Y-%m") as maand, SUM(totaal_incl) as omzet, COUNT(*) as aantal FROM bestellingen WHERE status!="geannuleerd" AND aangemaakt >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY maand ORDER BY maand'),
    ];
    jsonResponse(['ok'=>true,'stats'=>$stats]);
}

function handleAdminProducten(array $d): void {
    requireAdmin();
    // Lees aangepaste producten uit DB (admin kan eigen producten toevoegen)
    $producten = DB::fetchAll('SELECT * FROM admin_producten ORDER BY categorie, naam');
    jsonResponse(['ok'=>true,'producten'=>$producten]);
}

function handleAdminProductOpslaan(array $d): void {
    requireAdmin();
    $id = (int)($d['id'] ?? 0);
    if($id) {
        DB::run('UPDATE admin_producten SET naam=?,sku=?,categorie=?,segment=?,inkoop=?,actief=? WHERE id=?',
            [$d['naam'],$d['sku'],$d['categorie'],$d['segment'],(float)$d['inkoop'],(int)($d['actief']??1),$id]);
    } else {
        $id = DB::insert('INSERT INTO admin_producten (naam,sku,categorie,segment,inkoop,actief) VALUES (?,?,?,?,?,?)',
            [$d['naam'],$d['sku'],$d['categorie'],$d['segment'],(float)$d['inkoop'],1]);
    }
    jsonResponse(['ok'=>true,'id'=>$id]);
}

function handleAdminPrijzen(array $d): void {
    requireAdmin();
    $prijzen = DB::fetchAll('SELECT * FROM admin_prijzen ORDER BY categorie, segment');
    jsonResponse(['ok'=>true,'prijzen'=>$prijzen]);
}

function handleAdminPrijzenOpslaan(array $d): void {
    requireAdmin();
    foreach(($d['prijzen'] ?? []) as $p) {
        DB::run('INSERT INTO admin_prijzen (categorie,segment,markup) VALUES (?,?,?) ON DUPLICATE KEY UPDATE markup=?',
            [$p['categorie'],$p['segment'],(float)$p['markup'],(float)$p['markup']]);
    }
    jsonResponse(['ok'=>true]);
}

// ── Jortt ─────────────────────────────────────────────────────────────────────
function jorttToken(): ?string {
    $ch=curl_init(JORTT_TOKEN_URL);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
        CURLOPT_POSTFIELDS=>'grant_type=client_credentials&scope=invoices%3Awrite%20customers%3Aread%20customers%3Awrite',
        CURLOPT_HTTPHEADER=>['Authorization: Basic '.base64_encode(JORTT_CLIENT_ID.':'.JORTT_CLIENT_SECRET),'Content-Type: application/x-www-form-urlencoded']]);
    $d=json_decode(curl_exec($ch),true); curl_close($ch); return $d['access_token']??null;
}
function jorttApi(string $m,string $ep,array $b,string $t): ?array {
    $ch=curl_init(JORTT_API.$ep);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CUSTOMREQUEST=>$m,
        CURLOPT_POSTFIELDS=>json_encode($b),
        CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$t,'Content-Type: application/json']]);
    $d=json_decode(curl_exec($ch),true); curl_close($ch); return $d;
}
function createJorttInvoice(array $d): bool {
    $t=jorttToken(); if(!$t) return false;
    $kb=['company_name'=>(!empty($d['bedrijf'])&&$d['bedrijf']!=='–')?$d['bedrijf']:$d['naam'],'attn'=>$d['naam'],'email'=>$d['email'],'invoice_language'=>'nl','payment_term'=>14];
    if(!empty($d['kvk'])&&$d['kvk']!=='–') $kb['coc_number']=$d['kvk'];
    if(!empty($d['btwnr'])&&$d['btwnr']!=='–') $kb['vat_number']=$d['btwnr'];
    $kr=jorttApi('POST','/customers',$kb,$t); $cid=$kr['data']['id']??null; if(!$cid) return false;
    $lines=[];
    foreach(($d['regels']??[]) as $r) {
        $mStr=implode(', ',array_map(fn($m,$n)=>"$m:$n",array_keys($r['maten']??[]),array_values($r['maten']??[])));
        $lines[]=['description'=>($r['naam']??'').' — '.($r['kleur_naam']??'').' ('.$mStr.')','amount'=>round((float)($r['prijs_ex']??0)*(int)($r['aantal']??0),2),'vat_percentage'=>21,'amount_includes_vat'=>false];
        if((float)($r['druk_ex']??0)>0) $lines[]=['description'=>'Drukkosten '.($r['techniek_a']??''),'amount'=>round((float)$r['druk_ex']*(int)$r['aantal'],2),'vat_percentage'=>21,'amount_includes_vat'=>false];
    }
    $lines[]=['description'=>'Verzendkosten','amount'=>round((float)($d['verzending_ex']??0),2),'vat_percentage'=>21,'amount_includes_vat'=>false];
    $ir=jorttApi('POST','/invoices',['customer_id'=>$cid,'reference'=>$d['order_id']??'','send_method'=>'email','line_items'=>$lines],$t);
    return !empty($ir['data']['id']);
}

// ══════════════════════════════════════════════════════════════════════════════
// MEERTALIGE E-MAILS
// ══════════════════════════════════════════════════════════════════════════════

// Vertalingen voor e-mails
function mailTekst(string $taal, string $key): string {
    static $t = null;
    if($t === null) $t = [
        'nl' => [
            'bevestiging_subject' => 'Bevestiging bestelling #{order_id} — Merch Master',
            'bevestiging_hallo'   => 'Hallo {naam},',
            'bevestiging_intro'   => 'Bedankt voor je bestelling bij Merch Master! We hebben alles goed ontvangen en gaan er direct mee aan de slag.',
            'jouw_bestelling'     => 'Jouw bestelling',
            'producten'           => 'product(en)',
            'totaal'              => 'Totaal incl. BTW',
            'wat_nu'              => 'Wat gebeurt er nu?',
            'stap1_t'  => 'Bevestiging',           'stap1' => 'Je ontvangt direct een e-mail met je orderoverzicht.',
            'stap2_t'  => 'Ontwerp check',          'stap2' => 'We bekijken je logo en nemen contact op bij vragen.',
            'stap3_t'  => 'Productie & verzending', 'stap3' => 'Na goedkeuring starten we direct met productie.',
            'wa_btn'   => ' WhatsApp',             'mail_btn' => '️ E-mail',
            'borduur_subject' => 'Borduurwens ontvangen — {naam}',
            'borduur_bevestig'=> 'Bedankt! We sturen binnen 1–2 werkdagen een offerte naar {email}.',
            'stuks'           => 'stuks',
            'kleur'           => 'Kleur',
            'positie'         => 'Positie',
            'techniek'        => 'Techniek',
        ],
        'en' => [
            'bevestiging_subject' => 'Order confirmation #{order_id} — Merch Master',
            'bevestiging_hallo'   => 'Hello {naam},',
            'bevestiging_intro'   => 'Thank you for your order at Merch Master! We have received everything and will get started right away.',
            'jouw_bestelling'     => 'Your order',
            'producten'           => 'product(s)',
            'totaal'              => 'Total incl. VAT',
            'wat_nu'              => 'What happens next?',
            'stap1_t'  => 'Confirmation',            'stap1' => 'You will immediately receive an email with your order summary.',
            'stap2_t'  => 'Design check',             'stap2' => 'We review your logo and contact you if we have questions.',
            'stap3_t'  => 'Production & shipping',    'stap3' => 'After approval we start production immediately.',
            'wa_btn'   => ' WhatsApp',              'mail_btn' => '️ Email',
            'borduur_subject' => 'Embroidery request received — {naam}',
            'borduur_bevestig'=> 'Thank you! We will send you a quote within 1–2 business days to {email}.',
            'stuks'           => 'pieces',
            'kleur'           => 'Colour',
            'positie'         => 'Position',
            'techniek'        => 'Technique',
        ],
        'de' => [
            'bevestiging_subject' => 'Bestellbestätigung #{order_id} — Merch Master',
            'bevestiging_hallo'   => 'Hallo {naam},',
            'bevestiging_intro'   => 'Vielen Dank für Ihre Bestellung bei Merch Master! Wir haben alles erhalten und legen sofort los.',
            'jouw_bestelling'     => 'Ihre Bestellung',
            'producten'           => 'Produkt(e)',
            'totaal'              => 'Gesamt inkl. MwSt.',
            'wat_nu'              => 'Was passiert als Nächstes?',
            'stap1_t'  => 'Bestätigung',             'stap1' => 'Sie erhalten sofort eine E-Mail mit Ihrer Bestellübersicht.',
            'stap2_t'  => 'Designprüfung',            'stap2' => 'Wir prüfen Ihr Logo und melden uns bei Fragen.',
            'stap3_t'  => 'Produktion & Versand',     'stap3' => 'Nach Freigabe starten wir sofort mit der Produktion.',
            'wa_btn'   => ' WhatsApp',              'mail_btn' => '️ E-Mail',
            'borduur_subject' => 'Stickwunsch erhalten — {naam}',
            'borduur_bevestig'=> 'Danke! Wir senden Ihnen innerhalb von 1–2 Werktagen ein Angebot an {email}.',
            'stuks'           => 'Stück',
            'kleur'           => 'Farbe',
            'positie'         => 'Position',
            'techniek'        => 'Technik',
        ],
        'no' => [
            'bevestiging_subject' => 'Ordrebekreftelse #{order_id} — Merch Master',
            'bevestiging_hallo'   => 'Hei {naam},',
            'bevestiging_intro'   => 'Takk for bestillingen din hos Merch Master! Vi har mottatt alt og starter umiddelbart.',
            'jouw_bestelling'     => 'Din bestilling',
            'producten'           => 'produkt(er)',
            'totaal'              => 'Totalt inkl. MVA',
            'wat_nu'              => 'Hva skjer nå?',
            'stap1_t'  => 'Bekreftelse',             'stap1' => 'Du mottar umiddelbart en e-post med din bestillingsoversikt.',
            'stap2_t'  => 'Designsjekk',              'stap2' => 'Vi sjekker logoen din og tar kontakt ved spørsmål.',
            'stap3_t'  => 'Produksjon & levering',    'stap3' => 'Etter godkjenning starter vi umiddelbart.',
            'wa_btn'   => ' WhatsApp',              'mail_btn' => '️ E-post',
            'borduur_subject' => 'Broderiønske mottatt — {naam}',
            'borduur_bevestig'=> 'Takk! Vi sender et tilbud innen 1–2 virkedager til {email}.',
            'stuks'           => 'stk',
            'kleur'           => 'Farge',
            'positie'         => 'Posisjon',
            'techniek'        => 'Teknikk',
        ],
    ];
    $lang = in_array($taal, ['nl','en','de','no']) ? $taal : 'nl';
    return $t[$lang][$key] ?? $t['nl'][$key] ?? $key;
}

function mt(string $taal, string $key, array $vars = []): string {
    $str = mailTekst($taal, $key);
    foreach($vars as $k => $v) $str = str_replace('{'.$k.'}', (string)$v, $str);
    return $str;
}

function sendBestelmail(array $d): bool {
    $aantalRegels = count($d['regels'] ?? []);
    $naam = trim(($d['voornaam'] ?? '') . ' ' . ($d['achternaam'] ?? '')) ?: ($d['naam'] ?? 'Onbekend');
    $subject = "Nieuwe bestelling #{$d['order_id']} — {$naam} — {$aantalRegels} product(en)";

    // Upload-bestanden als bijlage verzamelen
    $bijlagen = [];
    foreach (($d['regels'] ?? []) as $r) {
        foreach (['upload_url_a', 'upload_url_b'] as $veld) {
            $url = $r[$veld] ?? null;
            if (!$url) continue;
            $pad = str_replace(rtrim(UPLOAD_URL, '/'), rtrim(UPLOAD_DIR, '/'), $url);
            if (file_exists($pad)) $bijlagen[] = ['pad' => $pad];
        }
    }

    return sendMail(MAIL_TO, MAIL_FROM, MAIL_NAME, $subject, bestelTemplateNL($d), $d['email'] ?? '', $bijlagen);
}

function sendBevestiging(array $d, string $taal = 'nl'): bool {
    $subject = mt($taal, 'bevestiging_subject', ['order_id' => $d['order_id'] ?? '']);
    $html    = bevestigingTemplate($d, $taal);
    return sendMail($d['email'] ?? '', MAIL_FROM, MAIL_NAME, $subject, $html);
}

function sendOfferteEmail(array $d, string $taal = 'nl'): bool {
    $subject = $taal === 'nl' ? 'Offerte aanvraag ontvangen' : 'Quote request received';
    return sendMail(MAIL_TO, MAIL_FROM, MAIL_NAME, $subject, offerteTemplate($d, $taal), $d['email'] ?? '');
}

function sendBorduurmail(array $d, string $taal = 'nl'): bool {
    $subject = mt($taal, 'borduur_subject', ['naam' => $d['naam'] ?? '']);
    return sendMail(MAIL_TO, MAIL_FROM, MAIL_NAME, $subject, borduurTemplate($d), $d['email'] ?? '');
}

function sendMail(string $to, string $from, string $name, string $subj, string $html, string $rt = '', array $bijlagen = []): bool {
    if(!$to) return false;
    $outer = md5(uniqid('o'));
    $inner = md5(uniqid('i'));

    // Bijlagen filteren op bestaande bestanden
    $bijlagen = array_filter($bijlagen, fn($b) => !empty($b['pad']) && file_exists($b['pad']));

    if (empty($bijlagen)) {
        // Geen bijlagen — simpel multipart/alternative
        $h = implode("\r\n", ["MIME-Version: 1.0","Content-Type: multipart/alternative; boundary=\"$inner\"","From: $name <$from>","Reply-To: ".($rt?:$from),"X-Mailer: PHP/".phpversion()]);
        $body = "--$inner\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n".strip_tags($html)."\r\n\r\n--$inner\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n$html\r\n\r\n--$inner--";
    } else {
        // Met bijlagen — multipart/mixed als buitenste laag
        $h = implode("\r\n", ["MIME-Version: 1.0","Content-Type: multipart/mixed; boundary=\"$outer\"","From: $name <$from>","Reply-To: ".($rt?:$from),"X-Mailer: PHP/".phpversion()]);
        $body  = "--$outer\r\nContent-Type: multipart/alternative; boundary=\"$inner\"\r\n\r\n";
        $body .= "--$inner\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n".strip_tags($html)."\r\n\r\n";
        $body .= "--$inner\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n$html\r\n\r\n--$inner--\r\n\r\n";
        foreach ($bijlagen as $bij) {
            $bestandsnaam = basename($bij['pad']);
            $mime         = mime_content_type($bij['pad']) ?: 'application/octet-stream';
            $data         = chunk_split(base64_encode(file_get_contents($bij['pad'])));
            $body .= "--$outer\r\nContent-Type: $mime; name=\"$bestandsnaam\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$bestandsnaam\"\r\n\r\n$data\r\n";
        }
        $body .= "--$outer--";
    }
    return mail($to, '=?UTF-8?B?'.base64_encode($subj).'?=', $body, $h);
}

// ── E-mail templates ──────────────────────────────────────────────────────────
function emailShell(string $badge, string $kleur, string $body): string {
    return "<!DOCTYPE html><html><body style='margin:0;background:#f5f3ef;font-family:Helvetica,Arial,sans-serif;'>
<table width='100%' cellpadding='0' cellspacing='0' style='padding:32px 16px;'><tr><td align='center'>
<table width='600' cellpadding='0' cellspacing='0' style='max-width:600px;'>
<tr><td style='background:#0f0e0c;padding:28px 32px;border-radius:10px 10px 0 0;'>
  <table width='100%'><tr>
    <td><b style='font-size:18px;color:#fff;'>Merch<span style='color:#e84c1e;'>Master</span></b></td>
    <td align='right'><span style='background:{$kleur};color:#fff;font-size:11px;font-weight:700;padding:5px 12px;border-radius:20px;'>{$badge}</span></td>
  </tr></table>
</td></tr>
<tr><td style='background:#fff;padding:28px;border-radius:0 0 10px 10px;'>{$body}
  <div style='border-top:1px solid #eee;padding-top:16px;margin-top:20px;text-align:center;font-size:12px;color:#999;'>
    Merch Master · <a href='https://wa.me/31617255170' style='color:#e84c1e;'>WhatsApp</a> · info@merch-master.com
  </div>
</td></tr></table></td></tr></table></body></html>";
}

function bestelTemplateNL(array $d): string {
    $regelsHtml = '';
    foreach(($d['regels'] ?? []) as $i => $r) {
        $matenStr = implode(', ', array_map(fn($m,$n) => "$m: $n", array_keys($r['maten']??[]), array_values($r['maten']??[])));
        $fileLinks = '';
        if (!empty($r['upload_url_a'])) {
            $fileName = basename($r['upload_url_a']);
            $fileLinks .= "<a href='".htmlspecialchars($r['upload_url_a'])."' style='color:#e84c1e;text-decoration:none;font-weight:500;'>📥 Voorkant: ".$fileName."</a>";
        }
        if (!empty($r['upload_url_b'])) {
            if ($fileLinks) $fileLinks .= "<br>";
            $fileName = basename($r['upload_url_b']);
            $fileLinks .= "<a href='".htmlspecialchars($r['upload_url_b'])."' style='color:#e84c1e;text-decoration:none;font-weight:500;'>📥 Achterkant: ".$fileName."</a>";
        }

        $prodImage = '';
        if (!empty($r['product_image_url'])) {
            $prodImage = "<div style='margin-bottom:12px;'><img src='".htmlspecialchars($r['product_image_url'])."' alt='Product' style='max-width:100%;height:auto;border-radius:6px;' loading='lazy'></div>";
        }
        $regelsHtml .= "<div style='background:#f9f8f5;border-radius:8px;padding:14px;margin-bottom:12px;border-left:4px solid #e84c1e;'>
          ".$prodImage."
          <div style='margin-bottom:10px;'>
            <b style='font-size:14px;color:#1a1a1a;'>".($i+1).'. '.($r['merk']??'').' '.($r['naam']??'')."</b>
          </div>
          <div style='font-size:13px;color:#555;line-height:1.8;'>
            <strong>SKU:</strong> ".($r['sku']??'–')."<br>
            <strong>Kleur:</strong> ".($r['kleur_naam']??'–')."<br>
            <strong>Maten:</strong> ".$matenStr."<br>
            <strong>Hoeveelheid:</strong> ".($r['aantal']??0)."x<br>
            <strong>Positie:</strong> ".($r['positie']??'–')."<br>
            <strong>Techniek:</strong> ".($r['techniek_a']??'–').(!empty($r['techniek_b']) ? ' + '.$r['techniek_b'] : '')."
          </div>
          ".($fileLinks ? "<div style='font-size:13px;padding-top:10px;border-top:1px solid #e8e4dc;color:#666;'><strong>Ontwerpen:</strong><br>".$fileLinks."</div>" : "<div style='font-size:13px;padding-top:10px;border-top:1px solid #e8e4dc;color:#c0392b;'><strong>⚠ Gén ontwerp ontvangen</strong></div>")."
        </div>";
    }

    $opmHtml = '';
    if (!empty($d['opmerkingen'])) {
        $opmHtml = "<div style='background:#fffaeb;border-left:4px solid #f7a11a;padding:12px;margin-bottom:16px;font-size:13px;color:#92400e;'>
          <strong>Opmerkingen klant:</strong><br>".htmlspecialchars($d['opmerkingen'])."
        </div>";
    }

    $body = "<div style='background:#fff9f7;border-left:4px solid #e84c1e;padding:12px;margin-bottom:16px;font-size:13px;color:#92400e;'>
      <strong>Nieuwe bestelling ontvangen van:</strong> ".($d['naam']??'')."
    </div>
    <div style='background:#f5f3ef;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;'>
      <strong>Klant:</strong> ".($d['email']??'')."<br>
      <strong>Adres:</strong> ".($d['adres']??'–')."<br>
      <strong>Telefoon:</strong> ".($d['telefoon']??'–')."<br>
      <strong>Bedrijf:</strong> ".($d['bedrijf']??'–')."
    </div>
    $opmHtml

    <h3 style='font-size:14px;color:#1a1a1a;margin:16px 0 12px;'>Bestelling (".count($d['regels']??[])." product(en))</h3>
    $regelsHtml

    <div style='background:#f5f3ef;border-radius:8px;padding:14px;margin:16px 0;'>
      <strong style='font-size:14px;color:#1a1a1a;'>💰 Totaalbedrag</strong><br><br>
      Subtotaal excl. BTW: €".number_format(round((float)($d['totaal_incl']??0)/1.21,2),2,',','.')."<br>
      BTW (21%): €".number_format(round(((float)($d['totaal_incl']??0)/1.21)*0.21,2),2,',','.')."<br>
      <strong style='font-size:15px;color:#e84c1e;'>Totaal incl. BTW: € ".number_format((float)($d['totaal_incl']??0),2,',','.')."</strong>
    </div>

    <div style='font-size:12px;color:#666;border-top:1px solid #ddd;padding-top:12px;margin-top:16px;'>
      <strong>Volgende stap:</strong> Design goedkeuring → Productie → Verzending
    </div>";

    return emailShell('Nieuwe bestelling','#e84c1e',$body);
}

function bevestigingTemplate(array $d, string $taal = 'nl'): string {
    $naam = explode(' ', $d['naam'] ?? '')[0];
    $aantalRegels = count($d['regels'] ?? []);
    $regelsHtml = '';

    foreach(($d['regels'] ?? []) as $r) {
        $matenStr = implode(', ', array_map(fn($m,$n) => "$m: $n", array_keys($r['maten']??[]), array_values($r['maten']??[])));
        $fileLinks = '';
        if (!empty($r['upload_url_a'])) {
            $fileName = basename($r['upload_url_a']);
            $fileLinks .= "<a href='".htmlspecialchars($r['upload_url_a'])."' style='color:#e84c1e;text-decoration:none;font-weight:500;'>📥 Voorkant: ".$fileName."</a>";
        }
        if (!empty($r['upload_url_b'])) {
            if ($fileLinks) $fileLinks .= "<br>";
            $fileName = basename($r['upload_url_b']);
            $fileLinks .= "<a href='".htmlspecialchars($r['upload_url_b'])."' style='color:#e84c1e;text-decoration:none;font-weight:500;'>📥 Achterkant: ".$fileName."</a>";
        }

        $prodImage = '';
        if (!empty($r['product_image_url'])) {
            $prodImage = "<div style='margin-bottom:12px;'><img src='".htmlspecialchars($r['product_image_url'])."' alt='Product' style='max-width:100%;height:auto;border-radius:6px;' loading='lazy'></div>";
        }
        $regelsHtml .= "<div style='background:#f9f8f5;border-radius:8px;padding:16px;margin-bottom:12px;'>
          ".$prodImage."
          <div style='margin-bottom:12px;'>
            <b style='font-size:15px;color:#1a1a1a;'>".($r['merk']??'').' '.($r['naam']??'')."</b>
          </div>
          <div style='font-size:13px;color:#555;line-height:1.8;margin-bottom:10px;'>
            <strong>Kleur:</strong> ".($r['kleur_naam']??'–')."<br>
            <strong>Maten:</strong> ".$matenStr."<br>
            <strong>Hoeveelheid:</strong> ".($r['aantal']??0)." ".mt($taal,'stuks')."<br>
            <strong>Positie:</strong> ".($r['positie']??'–')."<br>
            <strong>Techniek:</strong> ".($r['techniek_a']??'–').(!empty($r['techniek_b']) ? ' + '.$r['techniek_b'] : '')."
          </div>
          ".($fileLinks ? "<div style='font-size:13px;padding-top:10px;border-top:1px solid #e8e4dc;'>".$fileLinks."</div>" : "")."
        </div>";
    }

    $opmHtml = '';
    if (!empty($d['opmerkingen'])) {
        $opmHtml = "<div style='background:#fffaeb;border-left:4px solid #f7a11a;padding:12px;margin-bottom:16px;font-size:13px;'>
          <strong>Jouw opmerking:</strong><br>".htmlspecialchars($d['opmerkingen'])."
        </div>";
    }

    $body = "<p>".mt($taal,'bevestiging_hallo',['naam'=>$naam])."<br><br>".mt($taal,'bevestiging_intro')."</p>
      <h3 style='font-size:14px;color:#1a1a1a;margin:16px 0 12px;'>".mt($taal,'jouw_bestelling')."</h3>
      $opmHtml
      $regelsHtml
      <div style='background:#f5f3ef;border-radius:8px;padding:12px;margin:16px 0;font-size:14px;'>
        <strong>Totaal incl. 21% BTW:</strong> <span style='color:#e84c1e;font-size:16px;font-weight:700;'>€ ".number_format((float)($d['totaal_incl']??0),2,',','.')."</span>
      </div>

      <div style='background:#f5f3ef;border-radius:8px;padding:16px;margin:16px 0;'>
        <b style='font-size:13px;display:block;margin-bottom:12px;color:#1a1a1a;'>".mt($taal,'wat_nu')."</b>
        <table cellspacing='0'><tr><td style='width:28px;vertical-align:top;'><div style='width:22px;height:22px;border-radius:50%;background:#e84c1e;color:#fff;font-size:11px;font-weight:700;text-align:center;line-height:22px;'>1</div></td>
          <td style='padding-left:8px;font-size:13px;padding-bottom:12px;'><b style='color:#e84c1e;'>Design goedkeuring</b><br>We checken je ontwerp. Je hoort van ons als we vragen hebben!</td></tr>
        <tr><td style='width:28px;vertical-align:top;'><div style='width:22px;height:22px;border-radius:50%;background:#e84c1e;color:#fff;font-size:11px;font-weight:700;text-align:center;line-height:22px;'>2</div></td>
          <td style='padding-left:8px;font-size:13px;padding-bottom:12px;'><b style='color:#e84c1e;'>Productie</b><br>Na jouw go startten we de productie. Gemiddeld binnen onze levertijd klaar.</td></tr>
        <tr><td style='width:28px;vertical-align:top;'><div style='width:22px;height:22px;border-radius:50%;background:#e84c1e;color:#fff;font-size:11px;font-weight:700;text-align:center;line-height:22px;'>3</div></td>
          <td style='padding-left:8px;font-size:13px;'><b style='color:#e84c1e;'>Verzending</b><br>Je bestelling wordt verzonden en je krijgt een tracking link!</td></tr>
        </table>
      </div>
      <div style='text-align:center;margin-top:16px;'>
        <a href='https://wa.me/31617255170' style='display:inline-block;background:#25D366;color:#fff;font-size:13px;font-weight:700;padding:10px 20px;border-radius:20px;text-decoration:none;margin:0 4px;margin-bottom:8px;'>".mt($taal,'wa_btn')."</a>
        <a href='mailto:info@merch-master.com' style='display:inline-block;background:#e84c1e;color:#fff;font-size:13px;font-weight:700;padding:10px 20px;border-radius:20px;text-decoration:none;margin:0 4px;'>".mt($taal,'mail_btn')."</a>
      </div>";
    return emailShell(' '.mt($taal,'jouw_bestelling'),'#1a7a45',$body);
}

function offerteTemplate(array $d, string $taal = 'nl'): string {
    $body = "<div style='background:#fff9f7;border-left:4px solid #e84c1e;padding:12px;margin-bottom:16px;'>";
    $body .= $taal === 'nl'
        ? "Nieuwe offerte aanvraag — controleer de details en stuur prijzen via <b>reply</b>."
        : "New quote request — review details and send prices via <b>reply</b>.";
    $body .= "</div>";
    $body .= "<b>" . ($taal === 'nl' ? 'Klant' : 'Customer') . ":</b> ".htmlspecialchars($d['naam']??'')." | ".htmlspecialchars($d['email']??'')."<br>";
    $body .= ($taal === 'nl' ? 'Bedrijf' : 'Company') . ": ".htmlspecialchars($d['bedrijf']??'–')."<br>";
    $body .= ($taal === 'nl' ? 'Telefoon' : 'Phone') . ": ".htmlspecialchars($d['telefoon']??'–')."<br><br>";

    $body .= "<b>" . ($taal === 'nl' ? 'Aanvraag details' : 'Request details') . ":</b><br>";
    $body .= ($taal === 'nl' ? 'Product' : 'Product') . ": ".htmlspecialchars($d['mdl_name']??'–')."<br>";
    $body .= ($taal === 'nl' ? 'Hoeveelheid' : 'Quantity') . ": ".intval($d['qty']??0)." stuks<br>";
    $body .= ($taal === 'nl' ? 'Indicatieve prijs' : 'Indicative price') . ": €".number_format(floatval($d['tot']??0), 2, ',', '.')."<br><br>";

    if(!empty($d['opmerkingen'])) {
        $body .= "<b>" . ($taal === 'nl' ? 'Opmerkingen' : 'Notes') . ":</b><br>".nl2br(htmlspecialchars($d['opmerkingen']))."<br>";
    }

    return emailShell($taal === 'nl' ? ' Offerte aanvraag' : ' Quote request', '#e84c1e', $body);
}

function borduurTemplate(array $d): string {
    $body = "<div style='background:#fff9f7;border-left:4px solid #e84c1e;padding:12px;margin-bottom:16px;'>Nieuwe borduurwens — stuur offerte via <b>reply</b>.</div>
      <b>Klant:</b> ".($d['naam']??'')." | ".($d['email']??'')."<br>
      Bedrijf: ".($d['bedrijf']??'–')."<br><br>
      <b>Aanvraag:</b><br>Textiel: ".($d['textiel']??'–')."<br>Positie: ".($d['positie']??'–')."<br>
      Stuks: ".($d['totaal_stuks']??'–')."<br>Logo: ".($d['logo']??'–')."<br>Wensen: ".($d['opmerkingen']??'–');
    return emailShell(' Borduurwens','#e84c1e',$body);
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function getToken(): string { return $_COOKIE[SESSIE_NAAM]??($_SERVER['HTTP_X_AUTH_TOKEN']??''); }
function requireAuth(): array {
    $t=getToken(); if(!$t) jsonResponse(['ok'=>false,'fout'=>'Niet ingelogd'],401);
    $k=Auth::check($t); if(!$k) jsonResponse(['ok'=>false,'fout'=>'Sessie verlopen'],401);
    return $k;
}
function setAuthCookie(string $t): void {
    setcookie(SESSIE_NAAM,$t,['expires'=>time()+SESSIE_DUUR,'path'=>'/','secure'=>true,'httponly'=>true,'samesite'=>'Lax']);
}
function jsonResponse(array $data,int $status=200): void {
    http_response_code($status); echo json_encode($data); exit;
}

// ── Levertijden beheer ────────────────────────────────────────────────────────
function handleAdminLevertijden(): void {
    requireAdmin();
    jsonResponse(['ok' => true, 'levertijden' => get_levertijden()]);
}

function handleAdminLevertijdenOpslaan(array $d): void {
    requireAdmin();
    foreach (['dtf', 'zeefdruk', 'borduren'] as $tech) {
        if (isset($d[$tech])) {
            $val = trim($d[$tech]);
            if (preg_match('/^\d{1,2}(-\d{1,2})?$/', $val)) {
                set_instelling('levertijd_' . $tech, $val);
            }
        }
    }
    jsonResponse(['ok' => true, 'levertijden' => get_levertijden()]);
}

// ── Drukkosten beheer ─────────────────────────────────────────────────────────
function handleAdminDrukkosten(): void {
    requireAdmin();
    $dtf = [
        'klein' => get_instelling('dtf_prijs_klein', '9.00'),
        'groot' => get_instelling('dtf_prijs_groot', '7.00'),
    ];
    $zeef = DB::rows('SELECT id, min_qty, max_qty, kleur1, kleur2, kleur3, kleur4 FROM druk_zeef ORDER BY min_qty');
    jsonResponse(['ok' => true, 'dtf' => $dtf, 'zeef' => $zeef]);
}

function handleAdminDrukkostenOpslaan(array $d): void {
    requireAdmin();
    
    // DTF — twee simpele instellingen
    if (isset($d['dtf']['klein'])) set_instelling('dtf_prijs_klein', number_format((float)$d['dtf']['klein'], 2, '.', ''));
    if (isset($d['dtf']['groot'])) set_instelling('dtf_prijs_groot', number_format((float)$d['dtf']['groot'], 2, '.', ''));
    
    // Zeefdruk matrix
    if (!empty($d['zeef']) && is_array($d['zeef'])) {
        foreach ($d['zeef'] as $r) {
            DB::run(
                'UPDATE druk_zeef SET min_qty=?, max_qty=?, kleur1=?, kleur2=?, kleur3=?, kleur4=? WHERE id=?',
                [(int)$r['min_qty'], (int)$r['max_qty'],
                 (float)$r['kleur1'], (float)$r['kleur2'], (float)$r['kleur3'], (float)$r['kleur4'],
                 (int)$r['id']]
            );
        }
    }
    
    jsonResponse(['ok' => true]);
}
