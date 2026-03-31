<?php
/**
 * Admin debug tool — tijdelijk, verwijder na gebruik
 * URL: /bestellen/admin/debug_admin.php?key=Klaas99
 */
if (($_GET['key'] ?? '') !== 'Klaas99') { http_response_code(403); die('403'); }

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Admin Debug</title>
<style>body{font-family:monospace;padding:2rem;background:#f5f3ef;}
.ok{color:#166534;background:#dcfce7;padding:.3rem .6rem;border-radius:4px;}
.fout{color:#991b1b;background:#fee2e2;padding:.3rem .6rem;border-radius:4px;}
.sectie{background:#fff;border:1px solid #e8e4dc;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;}
h2{font-size:1rem;margin-bottom:1rem;}
pre{background:#f0ede6;padding:.75rem;border-radius:4px;font-size:.78rem;overflow-x:auto;}
</style></head><body>
<h1>Merch Master — Admin Debug</h1>

<?php

// ── 1. DB verbinding ──────────────────────────────────────────────────────────
echo '<div class="sectie"><h2>1. Database verbinding</h2>';
try {
    $pdo = new PDO('mysql:host=localhost;dbname=u204320941_merchmaster;charset=utf8mb4',
                   'u204320941_merchmaster', 'Garyvee#12345',
                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo '<span class="ok">Verbonden</span>';
} catch (Exception $e) {
    echo '<span class="fout">FOUT: ' . htmlspecialchars($e->getMessage()) . '</span>';
    echo '</div></body></html>'; exit;
}
echo '</div>';

// ── 2. Tabellen ───────────────────────────────────────────────────────────────
echo '<div class="sectie"><h2>2. Tabellen in database</h2>';
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) echo "✓ $t<br>";
$hasInstellingen = in_array('mm_instellingen', $tables);
echo $hasInstellingen ? '<br><span class="ok">mm_instellingen bestaat</span>' : '<br><span class="fout">mm_instellingen ONTBREEKT — zie SQL migratie</span>';
echo '</div>';

// ── 3. Instellingen tabel aanmaken indien nodig ───────────────────────────────
if (!$hasInstellingen) {
    echo '<div class="sectie"><h2>3. mm_instellingen aanmaken</h2>';
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `mm_instellingen` (
            `sleutel`    VARCHAR(100)  NOT NULL,
            `waarde`     MEDIUMTEXT    NOT NULL,
            `bijgewerkt` DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`sleutel`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo '<span class="ok">Tabel aangemaakt!</span>';
        $hasInstellingen = true;
    } catch (Exception $e) {
        echo '<span class="fout">Kon tabel niet aanmaken: ' . htmlspecialchars($e->getMessage()) . '</span>';
    }
    echo '</div>';
}

// ── 4. Bestaande instellingen ─────────────────────────────────────────────────
if ($hasInstellingen) {
    echo '<div class="sectie"><h2>4. Huidige instellingen</h2>';
    $rows = $pdo->query("SELECT sleutel, CHAR_LENGTH(waarde) AS len, bijgewerkt FROM mm_instellingen")->fetchAll();
    if (!$rows) echo '<span class="fout">Geen instellingen gevonden — seed data ontbreekt</span>';
    foreach ($rows as $r) {
        echo "✓ <strong>{$r['sleutel']}</strong> ({$r['len']} bytes) — bijgewerkt: {$r['bijgewerkt']}<br>";
    }
    echo '</div>';
}

// ── 5. Handler path check ─────────────────────────────────────────────────────
echo '<div class="sectie"><h2>5. Handler.php pad check</h2>';
$paths = [
    dirname(__FILE__) . '/../handler.php',
    dirname(__FILE__) . '/admin_handler.php',
    '/home/u204320941/public_html/bestellen/handler.php',
];
foreach ($paths as $p) {
    $exists = file_exists($p);
    echo ($exists ? '<span class="ok">✓</span>' : '<span class="fout">✗</span>') . ' ' . htmlspecialchars($p) . '<br>';
}
echo '</div>';

// ── 6. Test admin-levertijden API call ────────────────────────────────────────
echo '<div class="sectie"><h2>6. Test: admin-levertijden lezen</h2>';
$handlerPath = dirname(__FILE__) . '/../handler.php';
if (file_exists($handlerPath)) {
    // Simuleer sessie
    $_SESSION['mm_admin'] = true;
    
    // Lees rechtstreeks uit DB
    try {
        $st = $pdo->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel='levertijden'");
        $st->execute();
        $row = $st->fetch();
        if ($row) {
            echo '<span class="ok">Levertijden gevonden in DB:</span><pre>' . htmlspecialchars(json_encode(json_decode($row['waarde']), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) . '</pre>';
        } else {
            echo '<span class="fout">Levertijden niet in DB — worden aangemaakt met defaults bij eerste opslaan</span>';
            // Seed defaults
            $default = json_encode(['dtf'=>['min'=>5,'max'=>8],'zeef'=>['min'=>6,'max'=>10],'bord'=>['min'=>7,'max'=>12]]);
            $pdo->prepare("INSERT IGNORE INTO mm_instellingen (sleutel,waarde) VALUES ('levertijden',?)")->execute([$default]);
            echo '<br><span class="ok">Standaard levertijden aangemaakt!</span>';
        }
    } catch (Exception $e) {
        echo '<span class="fout">DB fout: ' . htmlspecialchars($e->getMessage()) . '</span>';
    }
} else {
    echo '<span class="fout">handler.php niet gevonden op verwacht pad</span>';
}
echo '</div>';

// ── 7. JS console check hint ──────────────────────────────────────────────────
echo '<div class="sectie"><h2>7. Mogelijke oorzaken knoppen doen niks</h2>';
echo '<p>Als de knoppen in admin <em>helemaal niks</em> doen (geen netwerk request, geen fout), is de oorzaak bijna altijd:</p>';
echo '<ol style="margin:.75rem 0 0 1.25rem;line-height:2;">
<li><strong>JavaScript fout eerder in het script</strong> — open browser DevTools → Console tab, herlaad de pagina</li>
<li><strong>ID ontbreekt in HTML</strong> — sectie-levertijden of sectie-drukkosten bestaat niet</li>
<li><strong>toonSectie() functie dubbel gedefinieerd</strong> — conflicterend JS in dezelfde pagina</li>
<li><strong>onclick handler niet bereikbaar</strong> — functie staat in een block scope i.p.v. globaal</li>
</ol>';
echo '<p style="margin-top:.75rem;"><strong>Snelste diagnose:</strong> open browser DevTools Console en type: <code>toonSectie(\'levertijden\')</code> — zie je een fout?</p>';
echo '</div>';

?>
<div class="sectie">
<h2>8. Seed data forceren</h2>
<form method="POST">
<button name="seed" value="1" style="padding:.5rem 1rem;background:#e84c1e;color:#fff;border:none;border-radius:6px;cursor:pointer;font-family:monospace;">
  Forceer seed levertijden + drukkosten + marges
</button>
</form>
<?php
if (isset($_POST['seed'])) {
    try {
        $seeds = [
            'levertijden' => ['dtf'=>['min'=>5,'max'=>8],'zeef'=>['min'=>6,'max'=>10],'bord'=>['min'=>7,'max'=>12]],
            'marges'      => ['textiel'=>1.45,'dtf'=>1.35,'zeefdruk'=>1.40,'borduren'=>1.50,'verzending'=>1.0],
            'drukkosten'  => [
                'dtf'  => ['oplagen'=>[1,5,10,25,50,100,250],'kleuren'=>[1,2,3,4],'matrix'=>[]],
                'zeef' => ['oplagen'=>[25,50,100,250,500,1000],'kleuren'=>[1,2,3,4],'setup'=>[],'matrix'=>[]],
                'bord' => ['oplagen'=>[1,5,10,25,50,100],'steken'=>[1000,2000,3000,5000,8000,10000],'setup'=>25,'matrix'=>[]],
            ],
        ];
        $st = $pdo->prepare("INSERT INTO mm_instellingen (sleutel,waarde) VALUES (?,?) ON DUPLICATE KEY UPDATE waarde=VALUES(waarde)");
        foreach ($seeds as $k => $v) {
            $st->execute([$k, json_encode($v)]);
        }
        echo '<span class="ok">Seed data ingevoerd!</span>';
    } catch (Exception $e) {
        echo '<span class="fout">Fout: ' . htmlspecialchars($e->getMessage()) . '</span>';
    }
}
?>
</div>

</body></html>
