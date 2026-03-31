<?php
// Laad config direct — pad relatief aan public_html
$config_path = __DIR__ . '/../bestellen/includes/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    // Fallback: definieer constanten direct
    define('MAIL_TO',   'info@merch-master.com');
    define('MAIL_FROM', 'info@merch-master.com');
}
require_once __DIR__ . '/taal.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
    exit;
}

// Rate limiting: max 20 per uur per IP
session_start();
$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key = 'contact_' . md5($ip);
$now = time();
if (!isset($_SESSION[$key])) $_SESSION[$key] = ['count'=>0,'reset'=>$now+3600];
if ($now > $_SESSION[$key]['reset']) $_SESSION[$key] = ['count'=>0,'reset'=>$now+3600];
if ($_SESSION[$key]['count'] >= 20) {
    http_response_code(429);
    echo json_encode(['ok'=>false,'msg'=>'Te veel berichten. Probeer WhatsApp.']);
    exit;
}
$_SESSION[$key]['count']++;

// Honeypot
if (!empty($_POST['website'])) { echo json_encode(['ok'=>true]); exit; }

// Validatie
$naam      = trim(strip_tags($_POST['naam'] ?? ''));
$email     = trim($_POST['email'] ?? '');
$onderwerp = trim(strip_tags($_POST['onderwerp'] ?? 'Contactformulier Merch Master'));
$bericht   = trim(strip_tags($_POST['bericht'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok'=>false,'msg'=>'Ongeldig e-mailadres']); exit;
}
if (strlen($bericht) < 5) {
    echo json_encode(['ok'=>false,'msg'=>'Bericht te kort']); exit;
}

$to      = defined('MAIL_TO') ? MAIL_TO : 'info@merch-master.com';
$from    = defined('MAIL_FROM') ? MAIL_FROM : 'info@merch-master.com';
$subject = '[Merch Master Contact] ' . $onderwerp;
$body    = "Naam: $naam\nE-mail: $email\nOnderwerp: $onderwerp\n\nBericht:\n$bericht";
$headers = "From: $from\r\nReply-To: $email\r\n";

$ok = mail($to, $subject, $body, $headers);

if ($ok && $naam) {
    $bev = "Beste $naam,\n\nBedankt voor je bericht! We nemen zo snel mogelijk contact op.\n\nJouw bericht:\n$bericht\n\nMet vriendelijke groet,\nMerch Master";
    mail($email, 'Bevestiging: '.$onderwerp, $bev, "From: $from\r\n");
}

echo json_encode(['ok'=>$ok,'msg'=>$ok?'Verzonden!':'Versturen mislukt. Probeer WhatsApp.']);
