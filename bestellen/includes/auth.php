<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// ── Sessie starten ─────────────────────────────────────────────────────────
function sessie_start() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSIE_DUUR,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name(SESSIE_NAAM);
        session_start();
    }
}

// ── Klant registreren ──────────────────────────────────────────────────────
function registreer_klant($email, $wachtwoord, $voornaam, $achternaam) {
    $klant = DB::row("SELECT id FROM klanten WHERE email = ?", [$email]);
    if ($klant) {
        return ['ok' => false, 'msg' => 'E-mailadres al in gebruik'];
    }
    $hash = password_hash($wachtwoord, PASSWORD_BCRYPT, ['cost' => 12]);
    $id = DB::insert("INSERT INTO klanten (email, wachtwoord, voornaam, achternaam) VALUES (?,?,?,?)",
        [$email, $hash, $voornaam, $achternaam]);
    if (!$id) return ['ok' => false, 'msg' => 'Registratie mislukt'];
    sessie_start();
    $_SESSION['klant_id']    = $id;
    $_SESSION['klant_email'] = $email;
    $_SESSION['klant_naam']  = $voornaam;
    return ['ok' => true, 'id' => $id];
}

// ── Klant inloggen ─────────────────────────────────────────────────────────
function login_klant($email, $wachtwoord) {
    $klant = DB::row("SELECT id, wachtwoord, voornaam FROM klanten WHERE email = ? AND actief = 1", [$email]);
    if (!$klant || !password_verify($wachtwoord, $klant['wachtwoord'])) {
        return ['ok' => false, 'msg' => 'Ongeldig e-mailadres of wachtwoord'];
    }
    sessie_start();
    $_SESSION['klant_id']    = $klant['id'];
    $_SESSION['klant_email'] = $email;
    $_SESSION['klant_naam']  = $klant['voornaam'];
    return ['ok' => true, 'id' => $klant['id']];
}

// ── Uitloggen ──────────────────────────────────────────────────────────────
function logout_klant() {
    sessie_start();
    session_destroy();
}

// ── Ingelogd check ─────────────────────────────────────────────────────────
function is_ingelogd() {
    sessie_start();
    return !empty($_SESSION['klant_id']);
}

function vereist_login() {
    if (!is_ingelogd()) {
        header('Location: /bestellen/portaal/?login=1');
        exit;
    }
}

// ── Admin inloggen ─────────────────────────────────────────────────────────
function login_admin($wachtwoord) {
    if (!password_verify($wachtwoord, password_hash(ADMIN_PASSWORD_PLAIN, PASSWORD_BCRYPT))) {
        // Fallback: directe vergelijking voor backwards compat
        if ($wachtwoord !== ADMIN_PASSWORD_PLAIN) return false;
    }
    sessie_start();
    $_SESSION['admin_ingelogd'] = true;
    return true;
}

function is_admin() {
    sessie_start();
    return !empty($_SESSION['admin_ingelogd']);
}

function vereist_admin() {
    if (!is_admin()) {
        header('Location: /bestellen/admin/?login=1');
        exit;
    }
}
