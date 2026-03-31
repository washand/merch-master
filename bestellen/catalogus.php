<?php
/**
 * Merch Master — Catalogus API
 * GET /bestellen/catalogus.php
 *
 * Vaste categorieën: t-shirts, polo's, sweaters, hoodies, caps, jassen, tassen
 * Producten worden op merk + naam gekoppeld aan de juiste categorie.
 * Merken: Build Your Brand, Gildan, Asquith & Fox, B&C Collection, Flexfit/Yupoong, Anthem
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=300');

// ── Vaste categorieën ─────────────────────────────────────────────────────────
$CATEGORIEEN = [
    't-shirts'  => ['naam' => 'T-shirts',  'slug' => 't-shirts',  'icon' => '👕'],
    'polos'     => ['naam' => "Polo's",    'slug' => 'polos',     'icon' => '👔'],
    'sweaters'  => ['naam' => 'Sweaters',  'slug' => 'sweaters',  'icon' => '🧸'],
    'hoodies'   => ['naam' => 'Hoodies',   'slug' => 'hoodies',   'icon' => '🧥'],
    'caps'      => ['naam' => 'Caps',      'slug' => 'caps',      'icon' => '🧢'],
    'jassen'    => ['naam' => 'Jassen',    'slug' => 'jassen',    'icon' => '🧣'],
    'tassen'    => ['naam' => 'Tassen',    'slug' => 'tassen',    'icon' => '👜'],
];

// ── Categorie-detectie op naam/tags ──────────────────────────────────────────
// Trefwoorden per categorie — word-match op productnaam + tags
$CAT_KEYWORDS = [
    't-shirts'  => ['t-shirt','tshirt','t shirt','basic tee','jersey','tee'],
    'polos'     => ['polo'],
    'sweaters'  => ['sweater','sweat','crewneck','crew neck','pullover','raglan'],
    'hoodies'   => ['hoodie','hooded','zip hoodie','hoody','kaptrui'],
    'caps'      => ['cap','hat','beanie','snapback','flexfit','baseball','trucker','5-panel','6-panel'],
    'jassen'    => ['jacket','jas','softshell','fleece','vest','windbreaker','parka','bomber','coach'],
    'tassen'    => ['bag','tas','tote','backpack','rugzak','shopper','gymbag','drawstring'],
];

// Maten per categorie
$CAT_MATEN = [
    't-shirts'  => ['XS','S','M','L','XL','XXL','3XL'],
    'polos'     => ['XS','S','M','L','XL','XXL','3XL'],
    'sweaters'  => ['XS','S','M','L','XL','XXL','3XL'],
    'hoodies'   => ['XS','S','M','L','XL','XXL','3XL'],
    'caps'      => ['One Size','S/M','L/XL'],
    'jassen'    => ['XS','S','M','L','XL','XXL','3XL'],
    'tassen'    => ['One Size'],
];

function detecteerCategorie(string $naam, string $tags, array $CAT_KEYWORDS): string {
    $haystack = strtolower($naam . ' ' . $tags);
    foreach ($CAT_KEYWORDS as $slug => $keywords) {
        foreach ($keywords as $kw) {
            // Exacte word match (niet 'cap' in 'escape')
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/', $haystack)) {
                return $slug;
            }
        }
    }
    return 't-shirts'; // fallback
}

// ── Load .env ─────────────────────────────────────────────────────────────────
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

// ── DB ────────────────────────────────────────────────────────────────────────
function db(): PDO {
    static $pdo;
    if (!$pdo) {
        try {
            $dbHost = getenv('DB_HOST') ?: 'localhost';
            $dbName = getenv('DB_NAME');
            $dbUser = getenv('DB_USER');
            $dbPass = getenv('DB_PASS');

            $pdo = new PDO(
                "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
                $dbUser, $dbPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                 PDO::ATTR_EMULATE_PREPARES => false]
            );
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'fout' => 'Database niet bereikbaar: ' . $e->getMessage()]);
            exit;
        }
    }
    return $pdo;
}

try {
    // ── Producten ophalen ─────────────────────────────────────────────────────
    $producten_raw = db()->query(
        "SELECT
            p.id,
            p.sku,
            p.name                AS naam,
            p.brand               AS merk,
            p.inkoop,
            p.tier,
            COALESCE(p.tags,  '') AS tags,
            COALESCE(p.sizes, '') AS sizes,
            p.actief,
            (SELECT COUNT(*) FROM catalogus_kleuren ck WHERE ck.product_sku = p.sku) AS kleur_count
         FROM catalogus p
         WHERE p.actief = 1
         ORDER BY p.brand, p.name
         LIMIT 500"
    )->fetchAll();

    // ── Kleuren per product ───────────────────────────────────────────────────
    $kleuren_raw = db()->query(
        "SELECT product_sku, naam, hex, code
         FROM catalogus_kleuren
         ORDER BY product_sku, naam"
    )->fetchAll();

    $kleuren_idx = [];
    foreach ($kleuren_raw as $k) {
        $kleuren_idx[$k['product_sku']][] = [
            'naam' => $k['naam'],
            'hex'  => $k['hex'] ?: '#cccccc',
            'code' => $k['code'] ?: strtolower($k['naam']),
        ];
    }

    // Debug: vergelijk SKUs voor kleuren mismatch analyse
    $kleuren_skus   = array_unique(array_column($kleuren_raw, 'product_sku'));
    $catalogus_skus = array_column($producten_raw, 'sku');
    $matched_skus   = array_intersect($kleuren_skus, $catalogus_skus);

    // ── Producten categoriseren ───────────────────────────────────────────────
    $producten     = [];
    $cat_aantallen = array_fill_keys(array_keys($CATEGORIEEN), 0);

    foreach ($producten_raw as $p) {
        $cat_slug = detecteerCategorie($p['naam'], $p['tags'], $CAT_KEYWORDS);

        // Maten: gebruik sizes kolom als die gevuld is, anders categorie-default
        $maten = $CAT_MATEN[$cat_slug] ?? ['XS','S','M','L','XL','XXL'];
        if (!empty($p['sizes'])) {
            $sizes_parsed = array_map('trim', explode(',', $p['sizes']));
            $sizes_parsed = array_filter($sizes_parsed);
            if (!empty($sizes_parsed)) $maten = array_values($sizes_parsed);
        }

        $producten[] = [
            'id'             => $p['id'],   // varchar — niet casten naar int
            'sku'            => $p['sku'],
            'naam'           => $p['naam'],
            'merk'           => $p['merk'],
            'inkoop'         => (float)$p['inkoop'],
            'tier'           => $p['tier'],
            'categorie_slug' => $cat_slug,
            'categorie_naam' => $CATEGORIEEN[$cat_slug]['naam'] ?? $cat_slug,
            'kleur_count'    => (int)$p['kleur_count'],
            'kleuren'        => $kleuren_idx[$p['sku']] ?? [],
            'maten'          => $maten,
        ];

        if (isset($cat_aantallen[$cat_slug])) {
            $cat_aantallen[$cat_slug]++;
        }
    }

    // ── Categorieën samenstellen ──────────────────────────────────────────────
    $categorieen_output = [];
    foreach ($CATEGORIEEN as $slug => $cat) {
        $categorieen_output[] = [
            'slug'   => $slug,
            'naam'   => $cat['naam'],
            'icon'   => $cat['icon'],
            'aantal' => $cat_aantallen[$slug] ?? 0,
        ];
    }

    // Enhanced debug info
    $sample_product = count($producten) > 0 ? $producten[0] : null;

    echo json_encode([
        'ok'          => true,
        'categorieen' => $categorieen_output,
        'producten'   => $producten,
        'totaal'      => count($producten),
        'debug'       => [
            'info'              => 'Database & SKU matching diagnostics',
            'merken'            => array_values(array_unique(array_column($producten, 'merk'))),
            'kleuren_totaal'    => count($kleuren_raw),
            'kleuren_empty'     => count($kleuren_raw) === 0 ? 'WAARSCHUWING: catalogus_kleuren tabel is LEEG!' : 'OK',
            'kleuren_skus'      => count($kleuren_skus) > 0 ? array_values(array_slice($kleuren_skus, 0, 3)) : [],
            'catalogus_skus'    => count($catalogus_skus) > 0 ? array_slice($catalogus_skus, 0, 3) : [],
            'matched_skus'      => count($matched_skus),
            'match_percentage'  => count($catalogus_skus) > 0 ? round((count($matched_skus) / count($catalogus_skus)) * 100, 1) . '%' : 'N/A',
            'sample_product'    => $sample_product ? [
                'sku' => $sample_product['sku'],
                'naam' => $sample_product['naam'],
                'kleur_count' => $sample_product['kleur_count'],
                'kleuren_count' => count($sample_product['kleuren']),
            ] : 'Geen producten',
        ],
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'fout' => $e->getMessage()]);
}
