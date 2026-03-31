<?php
/**
 * Offerte PDF view — GET /bestellen/offerte_pdf.php?token=xxxxx
 * Geeft een print-ready HTML pagina terug die de klant kan printen/opslaan als PDF
 */

$token = preg_replace('/[^a-f0-9]/', '', $_GET['token'] ?? '');
if (strlen($token) !== 32) { http_response_code(404); die('Offerte niet gevonden'); }

require_once __DIR__ . '/includes/db-config.php';

try {
    // Zorg dat tabel bestaat
    getDB()->exec("CREATE TABLE IF NOT EXISTS `offertes` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `token` CHAR(32) NOT NULL UNIQUE,
        `klant_naam` VARCHAR(120), `klant_email` VARCHAR(180), `klant_tel` VARCHAR(40),
        `klant_bedrijf` VARCHAR(120), `regels` MEDIUMTEXT NOT NULL,
        `subtotaal` DECIMAL(10,2), `vol_pct` DECIMAL(5,2), `vol_korting` DECIMAL(10,2),
        `totaal_excl` DECIMAL(10,2), `totaal_incl` DECIMAL(10,2),
        `spoed` TINYINT(1) DEFAULT 0, `spoed_toeslag` DECIMAL(10,2) DEFAULT 0,
        `status` VARCHAR(20) DEFAULT 'concept', `geldig_tot` DATE,
        `aangemaakt` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $st = getDB()->prepare("SELECT * FROM offertes WHERE token=? LIMIT 1");
    $st->execute([$token]);
    $o = $st->fetch();
} catch (Exception $e) {
    http_response_code(500); die('Databasefout');
}

if (!$o) { http_response_code(404); die('Offerte niet gevonden'); }

$regels  = json_decode($o['regels'], true) ?? [];
$spoed   = (bool)$o['spoed'];
$nr      = 'MM-' . str_pad($o['id'], 5, '0', STR_PAD_LEFT);
$datum   = date('d-m-Y', strtotime($o['aangemaakt']));
$geldig  = $o['geldig_tot'] ? date('d-m-Y', strtotime($o['geldig_tot'])) : '–';

$totaal_incl_def = $spoed
    ? round((float)$o['totaal_incl'] + (float)$o['spoed_toeslag'], 2)
    : (float)$o['totaal_incl'];

function fmt(float $v): string {
    return '€&nbsp;' . number_format($v, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Offerte <?= htmlspecialchars($nr) ?> — Merch Master</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  font-size: 11pt;
  color: #1a1a1a;
  background: #fff;
  padding: 0;
}
@media screen {
  body { background: #f0ede6; }
  .pagina { max-width: 794px; margin: 2rem auto; background: #fff;
            box-shadow: 0 4px 32px rgba(0,0,0,.12); }
}
.pagina { padding: 48px 52px; }

/* Header */
.hdr { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; }
.logo { font-size: 22pt; font-weight: 900; letter-spacing: -.5px; }
.logo em { color: #c4622d; font-style: normal; }
.hdr-meta { text-align: right; font-size: 9pt; color: #6b6560; line-height: 1.8; }
.hdr-meta strong { font-size: 13pt; color: #1a1a1a; display: block; margin-bottom: 4px; }

/* Divider */
.divider { border: none; border-top: 2px solid #c4622d; margin: 0 0 28px; }

/* Twee kolommen: klant + offerte info */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
.info-blok h4 { font-size: 7.5pt; font-weight: 700; text-transform: uppercase;
                letter-spacing: .1em; color: #c4622d; margin-bottom: 8px; }
.info-blok p { font-size: 10pt; color: #3a3832; line-height: 1.7; }

/* Spoed banner */
.spoed-banner {
  background: #fff3cd; border: 1.5px solid #f59e0b; border-radius: 6px;
  padding: 10px 14px; margin-bottom: 24px; font-size: 9.5pt; color: #92400e;
  line-height: 1.6;
}
.spoed-banner strong { display: block; margin-bottom: 3px; font-size: 10pt; }

/* Tabel */
table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 9.5pt; }
thead tr { background: #1a1a1a; color: #fff; }
thead th { padding: 8px 10px; text-align: left; font-weight: 600; font-size: 8.5pt;
           text-transform: uppercase; letter-spacing: .06em; }
thead th:last-child, thead th:nth-child(3),
thead th:nth-child(4), thead th:nth-child(5) { text-align: right; }
tbody tr:nth-child(even) { background: #faf8f4; }
tbody td { padding: 8px 10px; vertical-align: top; color: #1a1a1a; border-bottom: 1px solid #ede9e1; }
tbody td:last-child, tbody td:nth-child(3),
tbody td:nth-child(4), tbody td:nth-child(5) { text-align: right; }
.td-sub { font-size: 8pt; color: #7a7670; margin-top: 2px; }

/* Totaalblok */
.totaal-wrap { display: flex; justify-content: flex-end; margin-bottom: 32px; }
.totaal-tbl { width: 280px; font-size: 10pt; }
.totaal-tbl td { padding: 4px 0; }
.totaal-tbl td:last-child { text-align: right; font-weight: 600; }
.totaal-tbl .subtotaal td { color: #6b6560; font-size: 9.5pt; font-weight: 400; }
.totaal-tbl .korting td { color: #16a34a; }
.totaal-tbl .spoed-rij td { color: #92400e; }
.totaal-tbl .eindtotaal td {
  border-top: 2px solid #1a1a1a; padding-top: 8px; font-size: 13pt;
  font-weight: 900; color: #c4622d;
}

/* Voorwaarden */
.vw { font-size: 8.5pt; color: #6b6560; line-height: 1.7; border-top: 1px solid #ede9e1; padding-top: 20px; margin-top: 8px; }
.vw strong { color: #1a1a1a; }

/* Footer */
.footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #ede9e1;
          display: flex; justify-content: space-between; font-size: 8pt; color: #9a9590; }

@media print {
  body { background: #fff !important; }
  .pagina { box-shadow: none !important; padding: 0; }
  .print-btn { display: none !important; }
}
</style>
</head>
<body>

<?php if (!empty($_GET['print'])): ?>
<script>window.onload=()=>window.print();</script>
<?php endif; ?>

<div class="pagina">

  <!-- Print knop (alleen op scherm) -->
  <div class="print-btn" style="text-align:right;margin-bottom:1rem;">
    <button onclick="window.print()" style="background:#c4622d;color:#fff;border:none;padding:.5rem 1.25rem;border-radius:6px;font-size:.85rem;cursor:pointer;font-family:inherit;">
      Opslaan als PDF / Afdrukken
    </button>
  </div>

  <!-- Header -->
  <div class="hdr">
    <div>
      <div class="logo">Merch<em>Master</em></div>
      <div style="font-size:8.5pt;color:#6b6560;margin-top:4px;">
        info@merch-master.com &nbsp;|&nbsp; +31 6 17 25 51 70<br>
        merch-master.com
      </div>
    </div>
    <div class="hdr-meta">
      <strong>OFFERTE <?= htmlspecialchars($nr) ?></strong>
      Datum: <?= $datum ?><br>
      Geldig t/m: <?= $geldig ?><br>
      Status: <?= htmlspecialchars(ucfirst($o['status'] ?? 'concept')) ?>
    </div>
  </div>
  <hr class="divider">

  <!-- Klant + offerte info -->
  <div class="info-grid">
    <div class="info-blok">
      <h4>Offerte voor</h4>
      <p>
        <?= htmlspecialchars($o['klant_naam']    ?: '–') ?><br>
        <?php if ($o['klant_bedrijf']): ?>
          <?= htmlspecialchars($o['klant_bedrijf']) ?><br>
        <?php endif; ?>
        <?= htmlspecialchars($o['klant_email']   ?: '') ?><br>
        <?= htmlspecialchars($o['klant_tel']     ?: '') ?>
      </p>
    </div>
    <div class="info-blok">
      <h4>Offerte details</h4>
      <p>
        Offertenummer: <strong><?= htmlspecialchars($nr) ?></strong><br>
        Opgesteld: <?= $datum ?><br>
        Geldig tot: <?= $geldig ?><br>
        <?php if ($spoed): ?>
          <span style="color:#92400e;font-weight:700;">SPOEDORDER</span>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <?php if ($spoed): ?>
  <!-- Spoed banner -->
  <div class="spoed-banner">
    <strong>Spoedorder — online betaling niet mogelijk</strong>
    Deze offerte bevat een spoedtoeslag van 40%. Spoedorders worden alleen uitgevoerd na persoonlijk overleg.
    Neem contact op via <strong>info@merch-master.com</strong>
    om de spoedboeking te bevestigen. Na bevestiging ontvangt u een betaallink.
  </div>
  <?php endif; ?>

  <!-- Regelstabel -->
  <table>
    <thead>
      <tr>
        <th style="width:30%;">Product</th>
        <th>Techniek</th>
        <th style="width:8%;">Stuks</th>
        <th style="width:13%;">Per stuk</th>
        <th style="width:13%;">Subtotaal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($regels as $i => $r):
        $subtot = round((float)($r['prijs_excl_voor'] ?? $r['prijs_excl'] ?? 0) * (int)($r['aantal'] ?? 0), 2);
      ?>
      <tr>
        <td>
          <?= htmlspecialchars($r['product_naam'] ?? '–') ?>
          <?php if (!empty($r['kleur'])): ?>
            <div class="td-sub"><?= htmlspecialchars($r['kleur']) ?></div>
          <?php endif; ?>
        </td>
        <td>
          <?= htmlspecialchars(ucfirst($r['techniek'] ?? '')) ?>
          <?php if (!empty($r['kleuren']) && ($r['techniek']??'') === 'zeefdruk'): ?>
            <div class="td-sub"><?= (int)$r['kleuren'] ?> kleur<?= $r['kleuren']>1?'en':'' ?></div>
          <?php endif; ?>
          <?php if (!empty($r['oplage_label'])): ?>
            <div class="td-sub"><?= htmlspecialchars($r['oplage_label']) ?></div>
          <?php endif; ?>
        </td>
        <td><?= (int)($r['aantal'] ?? 0) ?></td>
        <td><?= fmt((float)($r['prijs_excl_voor'] ?? $r['prijs_excl'] ?? 0)) ?></td>
        <td><?= fmt($subtot) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Totaalblok -->
  <div class="totaal-wrap">
    <table class="totaal-tbl">
      <tr class="subtotaal"><td>Subtotaal excl. BTW</td><td><?= fmt((float)$o['subtotaal']) ?></td></tr>

      <?php if ((float)$o['vol_pct'] > 0): ?>
      <tr class="korting">
        <td>Volumekorting (<?= number_format((float)$o['vol_pct'],0) ?>%)</td>
        <td>– <?= fmt((float)$o['vol_korting']) ?></td>
      </tr>
      <?php endif; ?>

      <tr><td>Totaal excl. BTW</td><td><?= fmt((float)$o['totaal_excl']) ?></td></tr>
      <tr class="subtotaal"><td>BTW (21%)</td><td><?= fmt(round((float)$o['totaal_incl']-(float)$o['totaal_excl'],2)) ?></td></tr>
      <tr><td>Totaal incl. BTW</td><td><?= fmt((float)$o['totaal_incl']) ?></td></tr>

      <?php if ($spoed): ?>
      <tr class="spoed-rij">
        <td>Spoedtoeslag (40%)</td>
        <td>+ <?= fmt((float)$o['spoed_toeslag']) ?></td>
      </tr>
      <?php endif; ?>

      <tr class="eindtotaal">
        <td>Te betalen</td>
        <td><?= fmt($totaal_incl_def) ?></td>
      </tr>
    </table>
  </div>

  <!-- Voorwaarden -->
  <div class="vw">
    <strong>Voorwaarden:</strong>
    Prijzen zijn excl. BTW tenzij anders vermeld. Verzendkosten worden apart berekend bij orders van 15+ stuks.
    Levertijd na akkoord: DTF 5–8 werkdagen · Zeefdruk 6–10 werkdagen · Borduren op aanvraag.
    <?php if ($spoed): ?>
    <strong>Spoedorders uitsluitend na bevestiging per e-mail (info@merch-master.com) — geen online betaling mogelijk.</strong>
    <?php endif; ?>
    Offerte geldig tot <?= $geldig ?>. Merch Master is een handelsnaam. KVK: [invullen]. BTW: [invullen].
    Op alle overeenkomsten is Nederlands recht van toepassing.
  </div>

  <!-- Footer -->
  <div class="footer">
    <span>Merch Master &nbsp;|&nbsp; info@merch-master.com &nbsp;|&nbsp; +31 6 17 25 51 70</span>
    <span>Offerte <?= htmlspecialchars($nr) ?> &nbsp;|&nbsp; <?= $datum ?></span>
  </div>

</div>
</body>
</html>
