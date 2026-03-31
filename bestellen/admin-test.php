<?php
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json');

$results = [];

// Test 1: DB connectie
try {
    $test = DB::row('SELECT 1 as ok');
    $results['db_connectie'] = $test ? 'OK' : 'FOUT';
} catch (Exception $e) {
    $results['db_connectie'] = 'FOUT: ' . $e->getMessage();
}

// Test 2: instellingen tabel
try {
    $rows = DB::rows('SELECT sleutel, waarde FROM instellingen ORDER BY sleutel');
    $results['instellingen'] = $rows;
} catch (Exception $e) {
    $results['instellingen'] = 'FOUT: ' . $e->getMessage();
}

// Test 3: druk_zeef tabel
try {
    $rows = DB::rows('SELECT * FROM druk_zeef ORDER BY min_qty');
    $results['druk_zeef_count'] = count($rows);
    $results['druk_zeef_eerste'] = $rows[0] ?? null;
} catch (Exception $e) {
    $results['druk_zeef'] = 'FOUT: ' . $e->getMessage();
}

// Test 4: admin token in instellingen
try {
    $token = DB::row('SELECT waarde FROM instellingen WHERE sleutel = "admin_token"');
    $expiry = DB::row('SELECT waarde FROM instellingen WHERE sleutel = "admin_token_expiry"');
    $results['admin_token_aanwezig'] = $token ? 'JA' : 'NEE';
    $results['admin_token_verlopen'] = $expiry ? (time() > (int)$expiry['waarde'] ? 'JA' : 'NEE') : 'geen expiry';
} catch (Exception $e) {
    $results['admin_token'] = 'FOUT: ' . $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
