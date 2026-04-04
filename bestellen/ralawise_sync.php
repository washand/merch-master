<?php
/**
 * Ralawise Sync — Haalt productdata & afbeeldingen op via Ralawise API
 * Draait via cron: 0 2 * * * php /path/to/bestellen/ralawise_sync.php
 *
 * STRICT READ-ONLY: Dit script plaatst NOOIT orders via de Ralawise API.
 * Alleen GET /v1/inventory endpoints worden gebruikt.
 */

set_time_limit(600); // 10 minuten max

// ── Load .env ─────────────────────────────────────────────────────────────────
$envFile = dirname(dirname(__FILE__)) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key); $value = trim($value);
            if (!empty($key)) putenv("$key=$value");
        }
    }
}

// ── DB ────────────────────────────────────────────────────────────────────────
function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO(
            "mysql:host=" . (getenv('DB_HOST') ?: 'localhost') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4",
            getenv('DB_USER'), getenv('DB_PASS'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

// ── Ralawise login → access_token ─────────────────────────────────────────────
function ralawise_login(): string {
    $base = rtrim(getenv('RALAWISE_API_URL') ?: 'https://api.ralawise.com', '/');
    $ch   = curl_init("$base/v1/login");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['user' => getenv('RALAWISE_USERNAME'), 'password' => getenv('RALAWISE_PASSWORD')]),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
    ]);
    $resp   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) throw new Exception("Login mislukt (HTTP $status): $resp");
    $data = json_decode($resp, true);
    if (empty($data['access_token'])) throw new Exception("Geen access_token: $resp");
    return $data['access_token'];
}

// ── GET /v1/inventory/SKU (READ-ONLY) ─────────────────────────────────────────
function ralawise_inventory(string $token, string $sku): ?array {
    $base = rtrim(getenv('RALAWISE_API_URL') ?: 'https://api.ralawise.com', '/');
    $ch   = curl_init("$base/v1/inventory/" . urlencode($sku));
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token, 'Accept: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $resp   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status === 404) return null;
    if ($status === 429) throw new Exception("429_RATE_LIMIT");
    if ($status !== 200) throw new Exception("HTTP $status: $resp");
    return json_decode($resp, true);
}

// ── Haal image_url uit Ralawise response ──────────────────────────────────────
function extract_image(array $data): ?string {
    foreach (['imageUrl','image_url','image','productImage','thumbnailUrl','thumbnail'] as $k) {
        if (!empty($data[$k]) && is_string($data[$k])) return $data[$k];
    }
    if (!empty($data['images'])) {
        $img = is_array($data['images']) ? reset($data['images']) : null;
        if (is_string($img)) return $img;
        if (is_array($img)) foreach (['url','src','href','imageUrl'] as $k) {
            if (!empty($img[$k])) return $img[$k];
        }
    }
    if (!empty($data['product']['imageUrl'])) return $data['product']['imageUrl'];
    return null;
}

// ── Zorg dat kolommen bestaan ─────────────────────────────────────────────────
function ensure_columns(): void {
    $cols = ['image_url' => 'VARCHAR(500)', 'stock' => 'INT', 'ralawise_synced_at' => 'DATETIME'];
    foreach ($cols as $col => $type) {
        $exists = db()->query("SHOW COLUMNS FROM catalogus LIKE '$col'")->fetch();
        if (!$exists) db()->exec("ALTER TABLE catalogus ADD COLUMN $col $type DEFAULT NULL");
    }
}

// ── MAIN ──────────────────────────────────────────────────────────────────────
$isCli = (php_sapi_name() === 'cli');
if (!$isCli) header('Content-Type: application/json; charset=utf-8');

$results = ['updated' => 0, 'not_found' => 0, 'errors' => 0, 'log' => []];

function log_msg(string $msg) use (&$results, $isCli): void {
    $results['log'][] = $msg;
    if ($isCli) echo $msg . "\n";
}

try {
    ensure_columns();

    $skus = db()->query("SELECT sku FROM catalogus WHERE actief = 1 ORDER BY sku")->fetchAll(PDO::FETCH_COLUMN);
    log_msg("Producten te synchroniseren: " . count($skus));

    $token     = ralawise_login();
    $loginTime = time();
    log_msg("Ralawise login OK.");

    $stmt = db()->prepare(
        "UPDATE catalogus SET image_url = ?, stock = ?, ralawise_synced_at = NOW() WHERE sku = ?"
    );

    foreach ($skus as $sku) {
        // Herlogin elke 15 minuten (token geldig 20 min)
        if (time() - $loginTime > 900) {
            $token     = ralawise_login();
            $loginTime = time();
            log_msg("Token vernieuwd.");
        }

        try {
            $data = ralawise_inventory($token, $sku);
        } catch (Exception $e) {
            if ($e->getMessage() === '429_RATE_LIMIT') {
                log_msg("  Rate limit — wacht 60s...");
                sleep(60);
                try { $data = ralawise_inventory($token, $sku); }
                catch (Exception $e2) { log_msg("  FOUT $sku: " . $e2->getMessage()); $results['errors']++; usleep(2000000); continue; }
            } else {
                log_msg("  FOUT $sku: " . $e->getMessage());
                $results['errors']++;
                usleep(2000000);
                continue;
            }
        }

        if (!$data) {
            log_msg("  NIET GEVONDEN: $sku");
            $results['not_found']++;
            usleep(2000000);
            continue;
        }

        $image = extract_image($data);
        $stock = $data['stock'] ?? $data['stockLevel'] ?? $data['quantityInStock'] ?? $data['qty'] ?? null;
        if (is_array($stock)) $stock = array_sum($stock);

        $stmt->execute([$image, $stock !== null ? (int)$stock : null, $sku]);
        $results['updated']++;
        log_msg("  OK $sku | image: " . ($image ?: 'geen') . " | stock: " . ($stock ?? '?'));

        // 2 seconden wachten → max 30 req/min, ver onder rate limit
        usleep(2000000);
    }

    $results['ok'] = true;
    log_msg("=== Klaar: {$results['updated']} bijgewerkt, {$results['not_found']} niet gevonden, {$results['errors']} fouten ===");

} catch (Exception $e) {
    $results['ok']  = false;
    $results['fout'] = $e->getMessage();
    log_msg("FATALE FOUT: " . $e->getMessage());
    if (!$isCli) http_response_code(500);
}

if (!$isCli) echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
