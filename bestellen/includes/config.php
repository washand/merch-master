<?php
/**
 * Merch Master Besteltool v2 — Configuratie
 */

// Load .env file
$envFile = dirname(dirname(dirname(__FILE__))) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!empty($key)) {
                putenv("$key=$value");
            }
        }
    }
}

// Helper function to get environment variables with defaults
function getenv_safe($key, $default = '') {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

// ── Database ──────────────────────────────────────────────────────────────────
define('DB_HOST',    getenv_safe('DB_HOST', 'localhost'));
define('DB_NAME',    getenv_safe('DB_NAME'));
define('DB_USER',    getenv_safe('DB_USER'));
define('DB_PASS',    getenv_safe('DB_PASS'));
define('DB_CHARSET', 'utf8mb4');
// ── Site ──────────────────────────────────────────────────────────────────────
define('SITE_URL',   getenv_safe('SITE_URL'));
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
// ── Mail ──────────────────────────────────────────────────────────────────────
define('MAIL_FROM',  getenv_safe('MAIL_FROM'));
define('MAIL_NAME',  getenv_safe('MAIL_NAME'));
define('MAIL_TO',    getenv_safe('MAIL_TO'));
// ── Jortt ─────────────────────────────────────────────────────────────────────
define('JORTT_CLIENT_ID',     getenv_safe('JORTT_CLIENT_ID'));
define('JORTT_CLIENT_SECRET', getenv_safe('JORTT_CLIENT_SECRET'));
define('JORTT_TOKEN_URL',     getenv_safe('JORTT_TOKEN_URL'));
define('JORTT_API',           getenv_safe('JORTT_API'));
// ── Sessie ────────────────────────────────────────────────────────────────────
define('SESSIE_DUUR',         30 * 24 * 60 * 60);
define('ADMIN_PASSWORD_PLAIN', getenv_safe('ADMIN_PASSWORD'));
define('SESSIE_NAAM',         'mm_sessie');
