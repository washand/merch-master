<?php
/**
 * Color Data Migration
 * Vult catalogus_kleuren tabel met standaard kleuren voor alle producten
 *
 * Standaard kleuren: Zwart, Wit, Navy, Rood, Groen, Blauw, Grijs
 */

require_once __DIR__ . '/includes/db-config.php';

echo "=== COLOR MIGRATION ===\n\n";

// Standaard kleuren voor alle producten
$standaard_kleuren = [
    ['code' => 'BK', 'naam' => 'Zwart', 'hex' => '#000000'],
    ['code' => 'WH', 'naam' => 'Wit', 'hex' => '#FFFFFF'],
    ['code' => 'NV', 'naam' => 'Navy', 'hex' => '#001F3F'],
    ['code' => 'RD', 'naam' => 'Rood', 'hex' => '#FF4136'],
    ['code' => 'GR', 'naam' => 'Grijs', 'hex' => '#AAAAAA'],
    ['code' => 'BL', 'naam' => 'Blauw', 'hex' => '#0074D9'],
    ['code' => 'GN', 'naam' => 'Groen', 'hex' => '#2ECC40'],
];

try {
    $pdo = getDB();

    // Step 1: Haal alle actieve producten op
    $producten = $pdo->query("SELECT sku FROM catalogus WHERE actief = 1")->fetchAll();
    echo "Producten gevonden: " . count($producten) . "\n\n";

    // Step 2: Voor elk product, voeg standaard kleuren toe
    $inserted = 0;
    $stmt = $pdo->prepare(
        "INSERT INTO catalogus_kleuren (product_sku, code, naam, hex)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($producten as $product) {
        $sku = $product['sku'];
        foreach ($standaard_kleuren as $kleur) {
            try {
                $stmt->execute([
                    $sku,
                    $kleur['code'],
                    $kleur['naam'],
                    $kleur['hex']
                ]);
                $inserted++;
            } catch (Exception $e) {
                // Duplicate? Skip
                continue;
            }
        }
    }

    echo "Kleuren ingevoegd: $inserted rijen\n";
    echo "Verwacht: " . (count($producten) * count($standaard_kleuren)) . " rijen\n\n";

    // Step 3: Verify
    $count = $pdo->query("SELECT COUNT(*) as cnt FROM catalogus_kleuren")->fetch();
    echo "✓ catalogus_kleuren nu bevat: " . $count['cnt'] . " rijen\n\n";

    // Step 4: Test SKU matching
    $sampleSKU = $producten[0]['sku'] ?? null;
    if ($sampleSKU) {
        $match = $pdo->prepare("SELECT COUNT(*) as cnt FROM catalogus_kleuren WHERE product_sku = ?");
        $match->execute([$sampleSKU]);
        $result = $match->fetch();
        echo "Sample SKU: $sampleSKU → " . $result['cnt'] . " kleuren\n";
    }

    echo "\n✓ Migration voltooid!\n";

} catch (Exception $e) {
    echo "✗ Fout: " . $e->getMessage() . "\n";
}
