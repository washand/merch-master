<?php
/**
 * Color Debug Script
 * Diagnosticeert waarom kleuren niet in catalogus.php verschijnen
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

// Load .env
$envFile = dirname(dirname(__FILE__)) . '/.env';
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

// Use .env credentials
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');

echo "=== COLOR DEBUG ===\n\n";
echo "Database: $dbName\n";
echo "Host: $dbHost\n";
echo "User: $dbUser\n\n";

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser, $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    echo "✓ Database verbinding OK\n\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit;
}

// Check 1: Tabel catalogus_kleuren bestaat?
try {
    $result = $pdo->query("DESCRIBE catalogus_kleuren");
    $cols = $result->fetchAll();
    echo "✓ Tabel 'catalogus_kleuren' bestaat\n";
    echo "  Kolommen:\n";
    foreach ($cols as $col) {
        echo "    - {$col['Field']} ({$col['Type']})\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ Tabel 'catalogus_kleuren' NIET GEVONDEN: " . $e->getMessage() . "\n\n";
}

// Check 2: Hoeveel rijen in catalogus_kleuren?
$colorCount = $pdo->query("SELECT COUNT(*) as cnt FROM catalogus_kleuren")->fetch();
echo "Total kleuren rijen in DB: " . $colorCount['cnt'] . "\n";

if ($colorCount['cnt'] == 0) {
    echo "⚠️  catalogus_kleuren tabel is LEEG!\n\n";
} else {
    echo "\n";
}

// Check 3: Sample van kleuren
echo "Sample kleuren (eerste 10):\n";
$samples = $pdo->query("SELECT product_sku, naam, hex, code FROM catalogus_kleuren LIMIT 10")->fetchAll();
foreach ($samples as $s) {
    echo "  SKU: {$s['product_sku']} | Naam: {$s['naam']} | Hex: {$s['hex']}\n";
}
echo "\n";

// Check 4: Unieke SKUs in catalogus_kleuren
$uniqueSKUs = $pdo->query("SELECT DISTINCT product_sku FROM catalogus_kleuren")->fetchAll();
echo "Unieke product SKUs in kleuren: " . count($uniqueSKUs) . "\n";
if (count($uniqueSKUs) > 0) {
    echo "  Sample SKUs: " . implode(', ', array_slice(array_column($uniqueSKUs, 'product_sku'), 0, 5)) . "\n";
}
echo "\n";

// Check 5: Producten in catalogus
$prodCount = $pdo->query("SELECT COUNT(*) as cnt FROM catalogus WHERE actief = 1")->fetch();
echo "Actieve producten in catalogus: " . $prodCount['cnt'] . "\n";

// Check 6: Sample producten
$prods = $pdo->query("SELECT id, sku, name FROM catalogus WHERE actief = 1 LIMIT 10")->fetchAll();
echo "Sample producten:\n";
foreach ($prods as $p) {
    echo "  ID: {$p['id']} | SKU: {$p['sku']} | Naam: {$p['name']}\n";
}
echo "\n";

// Check 7: SKU data types
echo "SKU data types:\n";
$skuInfo = $pdo->query("DESCRIBE catalogus")->fetchAll();
foreach ($skuInfo as $col) {
    if ($col['Field'] === 'sku') {
        echo "  catalogus.sku: {$col['Type']}\n";
    }
}
$skuInfo2 = $pdo->query("DESCRIBE catalogus_kleuren")->fetchAll();
foreach ($skuInfo2 as $col) {
    if ($col['Field'] === 'product_sku') {
        echo "  catalogus_kleuren.product_sku: {$col['Type']}\n";
    }
}
echo "\n";

// Check 8: Test matching van SKUs
$testSKU = $pdo->query("SELECT sku FROM catalogus WHERE actief = 1 LIMIT 1")->fetch();
if ($testSKU) {
    $sku = $testSKU['sku'];
    echo "Test SKU match: '$sku'\n";
    $match = $pdo->query(
        "SELECT COUNT(*) as cnt FROM catalogus_kleuren WHERE product_sku = ?",
        [$sku]
    )->fetch();

    // Better approach
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM catalogus_kleuren WHERE product_sku = ?");
    $stmt->execute([$sku]);
    $match = $stmt->fetch();

    echo "  Kleuren voor SKU '$sku': " . $match['cnt'] . "\n";
}

echo "\n=== END DEBUG ===\n";
