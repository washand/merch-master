<?php
$PAGE_TITLE = 'Bestellen';
$PAGE_DESC  = 'Bestel direct online bij Merch Master.';
$PAGE_URL   = 'https://merch-master.com/bestellen';
require_once __DIR__ . '/includes/header.php';

// ── Drukkosten uit database laden ─────────────────────────────────────────────
$_drukkostenJS = 'null';
try {
    require_once __DIR__ . '/bestellen/includes/db-config.php';
    $dk = null;
    // Probeer mm_instellingen (nieuw systeem)
    try {
        $st = getDB()->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel = 'drukkosten'");
        $st->execute();
        $row = $st->fetch();
        if ($row) $dk = json_decode($row['waarde'], true);
    } catch (Exception $e) {}
    // Fallback: instellingen (oud systeem)
    if (!$dk) {
        try {
            $st = getDB()->prepare("SELECT waarde FROM instellingen WHERE sleutel = 'drukkosten'");
            $st->execute();
            $row = $st->fetch();
            if ($row) $dk = json_decode($row['waarde'], true);
        } catch (Exception $e) {}
    }
    if ($dk) $_drukkostenJS = json_encode($dk, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) { /* gebruik JS fallback */ }

// ── Marges uit database laden ─────────────────────────────────────────────────
$_margesJS = 'null';
try {
    require_once __DIR__ . '/bestellen/includes/db-config.php';
    $mg = null;
    try {
        $st = getDB()->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel = 'marges'");
        $st->execute();
        $row = $st->fetch();
        if ($row) $mg = json_decode($row['waarde'], true);
    } catch (Exception $e) {}
    if (!$mg) {
        try {
            $st = getDB()->prepare("SELECT waarde FROM instellingen WHERE sleutel = 'marges'");
            $st->execute();
            $row = $st->fetch();
            if ($row) $mg = json_decode($row['waarde'], true);
        } catch (Exception $e) {}
    }
    if ($mg) $_margesJS = json_encode($mg, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {}

// ── Levertijden uit database laden ───────────────────────────────────────────
$lt_dtf = '5-8';
$lt_zeef = '6-10';
$lt_bord = '7-12';

try {
    require_once __DIR__ . '/bestellen/includes/db-config.php';

    // Probeer mm_instellingen (nieuw systeem) — als JSON object
    try {
        $st = getDB()->prepare("SELECT waarde FROM mm_instellingen WHERE sleutel = 'levertijden' LIMIT 1");
        $st->execute();
        $row = $st->fetch();
        if ($row && !empty($row['waarde'])) {
            $decoded = json_decode($row['waarde'], true);
            if (is_array($decoded)) {
                $v = $decoded['dtf'] ?? $lt_dtf;
                $lt_dtf = is_array($v) ? (string)reset($v) : (string)$v;
                $v = $decoded['zeefdruk'] ?? $lt_zeef;
                $lt_zeef = is_array($v) ? (string)reset($v) : (string)$v;
                $v = $decoded['borduren'] ?? $lt_bord;
                $lt_bord = is_array($v) ? (string)reset($v) : (string)$v;
            }
        }
    } catch (Exception $e) {}

    // Fallback: instellingen (oud systeem) — individuele string keys
    try {
        $st = getDB()->prepare("SELECT sleutel, waarde FROM instellingen WHERE sleutel IN ('levertijd_dtf', 'levertijd_zeefdruk', 'levertijd_borduren')");
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_KEY_PAIR);
        if (!empty($rows['levertijd_dtf'])) $lt_dtf = (string)$rows['levertijd_dtf'];
        if (!empty($rows['levertijd_zeefdruk'])) $lt_zeef = (string)$rows['levertijd_zeefdruk'];
        if (!empty($rows['levertijd_borduren'])) $lt_bord = (string)$rows['levertijd_borduren'];
    } catch (Exception $e) {}

} catch (Exception $e) {}

// Zorg dat waarden STRINGS zijn, niet arrays/objects
if (!is_string($lt_dtf)) $lt_dtf = '5-8';
if (!is_string($lt_zeef)) $lt_zeef = '6-10';
if (!is_string($lt_bord)) $lt_bord = '7-12';

// Clip "werkdagen" suffix (als die er in staat)
$lt_dtf = preg_replace('/\s+werkdagen\s*$/i', '', trim($lt_dtf));
$lt_zeef = preg_replace('/\s+werkdagen\s*$/i', '', trim($lt_zeef));
$lt_bord = preg_replace('/\s+werkdagen\s*$/i', '', trim($lt_bord));

$_levertijdenJS = json_encode([
    'dtf' => $lt_dtf,
    'zeefdruk' => $lt_zeef,
    'borduren' => $lt_bord
], JSON_UNESCAPED_UNICODE);
?>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --ink:#0f0e0c;--ink2:#3a3832;--ink3:#7a7670;
  --paper:#faf8f4;--surface:#fff;--border:#e8e4dc;
  --accent:#e84c1e;--accent2:#f7a11a;--success:#1a7a45;
  --r:10px;--shadow:0 2px 12px rgba(0,0,0,.07);
}
.besteltool-wrap{padding:1.5rem 0.85rem 4rem;}
.besteltool-cols{display:flex;gap:2rem;align-items:flex-start;max-width:1220px;margin:0 auto;}
.besteltool-cols .shell{flex:1;min-width:0;max-width:780px;}
.cart-panel{width:320px;flex-shrink:0;position:sticky;top:1.5rem;background:var(--surface);border:1px solid var(--border);border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;max-height:calc(100vh - 3rem);overflow:hidden;transition:box-shadow .2s;}
.cart-panel:hover{box-shadow:0 2px 8px rgba(0,0,0,.12);}
.cart-panel-hd{padding:1.25rem 1.25rem 1rem;border-bottom:1px solid var(--border);flex-shrink:0;}
@media(max-width:960px){.besteltool-cols{flex-direction:column;}.cart-panel{width:100%;position:static;max-height:none;}}

/* Progress */
.prog{display:flex;gap:5px;margin-bottom:2.5rem;}
.pb{flex:1;height:4px;border-radius:2px;background:var(--border);transition:background .35s;}
.pb.done{background:var(--accent);}
.pb.active{background:var(--accent2);}

/* Step labels */
.s-lbl{font-family:'Syne',sans-serif;font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ink3);margin-bottom:.35rem;}
.s-ttl{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;letter-spacing:-.02em;margin-bottom:.9rem;line-height:1.15;}
.sub-lbl{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--ink3);margin-bottom:.65rem;font-family:'Syne',sans-serif;}

/* Breadcrumb */
.trail{display:flex;align-items:center;gap:5px;margin-bottom:1.1rem;flex-wrap:wrap;min-height:22px;}
.crumb{font-size:.78rem;color:var(--ink3);}
.crumb.lnk{color:var(--accent);cursor:pointer;font-weight:500;}
.crumb.lnk:hover{text-decoration:underline;}
.crumb.cur{color:var(--ink);font-weight:600;}
.crumb-sep{font-size:.7rem;color:#ccc;}

/* Generic option card */
.opt-grid{display:grid;gap:.65rem;margin-bottom:1.5rem;}
.g4{grid-template-columns:repeat(4,1fr);}
.g3{grid-template-columns:repeat(3,1fr);}
.g2{grid-template-columns:repeat(2,1fr);}
.opt{background:var(--surface);border:2px solid var(--border);border-radius:var(--r);padding:.9rem 1rem;cursor:pointer;transition:border-color .2s,box-shadow .2s,transform .15s;user-select:none;position:relative;}
.opt:hover{border-color:#ccc8be;box-shadow:var(--shadow);transform:translateY(-1px);}
.opt.sel{border-color:var(--accent);background:#fff9f7;box-shadow:0 0 0 1px var(--accent);}
.opt .chk{position:absolute;top:.55rem;right:.55rem;width:18px;height:18px;border-radius:50%;background:var(--accent);display:none;align-items:center;justify-content:center;}
.opt.sel .chk{display:flex;}
.opt .chk svg{width:9px;height:9px;stroke:#fff;fill:none;stroke-width:2.5;}

/* Category cards (centered) */
.cat-opt{text-align:center;padding:.9rem .6rem;}
.cat-icon{width:2.4rem;height:2.4rem;margin:0 auto .4rem;display:flex;align-items:center;justify-content:center;}
.cat-icon svg{width:100%;height:100%;stroke:var(--ink2);}
.cat-opt.sel .cat-icon svg,.cat-opt:hover .cat-icon svg{stroke:var(--accent);}
.cat-name{font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;}

/* Model cards */
.mdl-img{width:100%;aspect-ratio:1;overflow:hidden;border-radius:6px;margin-bottom:6px;background:var(--surface);}
.mdl-img img{width:100%;height:100%;object-fit:cover;display:block;}
.mdl-brand{font-size:.64rem;text-transform:uppercase;letter-spacing:.08em;color:var(--ink3);font-weight:600;margin-bottom:2px;}
.mdl-name{font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;line-height:1.3;}
.mdl-sku{font-size:.67rem;color:var(--ink3);margin-top:2px;}
.mdl-tags{display:flex;gap:4px;flex-wrap:wrap;margin-top:6px;}
.mtag{font-size:.62rem;padding:2px 6px;border-radius:3px;background:#f0ede6;color:var(--ink2);}
.mtag.eco{background:#e4f4ec;color:#1a5e34;}
.mtag.prem{background:#fff0e0;color:#7a4200;}

/* Position cards */
.pos-icon{font-size:2rem;margin-bottom:.4rem;text-align:center;}
.pos-name{font-family:'Syne',sans-serif;font-size:.9rem;font-weight:700;text-align:center;}
.pos-desc{font-size:.73rem;color:var(--ink3);text-align:center;margin-top:3px;line-height:1.4;}
.pos-note{font-size:.7rem;color:var(--accent2);font-weight:600;text-align:center;margin-top:5px;}

/* Technique cards */
.tc-icon{font-size:1.4rem;margin-bottom:.35rem;}
.tc-name{font-family:'Syne',sans-serif;font-size:.83rem;font-weight:700;}
.tc-desc{font-size:.72rem;color:var(--ink3);margin-top:2px;line-height:1.4;}
.tc-badge{display:inline-block;font-size:.62rem;font-weight:500;padding:2px 6px;border-radius:4px;margin-top:5px;background:#f0ede6;color:var(--ink2);}
.tc-badge.green{background:#e4f4ec;color:#1a5e34;}
.tc-badge.orange{background:#fff0e0;color:#8a4e00;}
.tc-badge.gray{background:#efefef;color:#777;}

/* Both-sides kant indicator */
.kant-hdr{display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;padding:.7rem 1rem;background:var(--surface);border:2px solid var(--border);border-radius:var(--r);}
.kant-dot{width:10px;height:10px;border-radius:50%;background:var(--accent);flex-shrink:0;}
.kant-lbl{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;}
.kant-sub{font-size:.75rem;color:var(--ink3);margin-left:auto;}

/* Color swatches */
.sw-grid{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:.85rem;}
.sw{display:flex;flex-direction:column;align-items:center;gap:4px;cursor:pointer;}
.sw-circle{width:36px;height:36px;border-radius:50%;border:2.5px solid rgba(0,0,0,.12);transition:transform .15s,box-shadow .2s;position:relative;}
.sw:hover .sw-circle{transform:scale(1.1);}
.sw.sel .sw-circle{box-shadow:0 0 0 2.5px var(--surface),0 0 0 4.5px var(--ink);transform:scale(1.05);}
.sw.sel .sw-circle::after{content:'';position:absolute;inset:5px;border-radius:50%;border:2px solid rgba(255,255,255,.85);}
.sw.lc.sel .sw-circle::after{border-color:rgba(0,0,0,.35);}
.sw-nm{font-size:.61rem;color:var(--ink3);text-align:center;max-width:44px;line-height:1.2;}
.custom-row{display:flex;align-items:center;gap:10px;padding:.8rem 1rem;border:2px solid var(--border);border-radius:var(--r);background:var(--surface);cursor:pointer;transition:border-color .2s;margin-bottom:1rem;}
.custom-row.sel{border-color:var(--accent);background:#fff9f7;}
.custom-dot{width:32px;height:32px;border-radius:50%;background:conic-gradient(red,orange,yellow,green,blue,violet,red);border:2px solid var(--border);flex-shrink:0;}
.custom-lbl strong{display:block;font-family:'Syne',sans-serif;font-size:.82rem;font-weight:700;}
.custom-lbl span{font-size:.72rem;color:var(--ink3);}
.custom-inp{width:100%;padding:.6rem .85rem;border:2px solid var(--border);border-radius:8px;font-size:.85rem;font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--surface);transition:border-color .2s;margin-top:.6rem;}
.custom-inp:focus{outline:none;border-color:var(--accent);}
.chosen-bar{display:flex;align-items:center;gap:8px;padding:.6rem .9rem;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;margin-bottom:1.1rem;}
.chosen-dot{width:22px;height:22px;border-radius:50%;flex-shrink:0;border:1.5px solid rgba(0,0,0,.1);}
.chosen-nm{font-size:.82rem;font-weight:500;color:var(--ink2);}

/* Zeef color pills */
.zc-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:1.25rem;}
.zc-btn{padding:.4rem .9rem;border:2px solid var(--border);border-radius:20px;font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;cursor:pointer;background:var(--surface);color:var(--ink2);transition:border-color .2s,background .2s;}
.zc-btn.sel{border-color:var(--accent);background:var(--accent);color:#fff;}

/* Size table */
.sz-tbl{width:100%;border-collapse:collapse;margin-bottom:1.5rem;}
.sz-tbl th{font-family:'Syne',sans-serif;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;text-align:left;padding:.45rem .7rem;border-bottom:2px solid var(--border);color:var(--ink3);}
.sz-tbl td{padding:.45rem .7rem;border-bottom:1px solid var(--border);font-size:.875rem;}
.sz-tbl tr:last-child td{border-bottom:none;}
.sz-inp{width:68px;padding:4px 8px;border:1.5px solid var(--border);border-radius:6px;font-family:'Syne',sans-serif;font-weight:600;font-size:.875rem;text-align:center;background:var(--surface);color:var(--ink);}
.sz-inp:focus{outline:none;border-color:var(--accent);}

/* Quote box */

/* Fields */
.field{margin-bottom:1.1rem;}
.field label{display:block;font-size:.78rem;font-weight:500;margin-bottom:.35rem;color:var(--ink2);}
.field input,.field textarea,.field select{width:100%;padding:.65rem .85rem;border:2px solid var(--border);border-radius:var(--r);font-family:'DM Sans',sans-serif;font-size:.88rem;color:var(--ink);background:var(--surface);transition:border-color .2s;}
.field input:focus,.field textarea:focus,.field select:focus{outline:none;border-color:var(--accent);}
.field textarea{resize:vertical;min-height:76px;}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}

/* Upload */
.upload-area{border:2px dashed var(--border);border-radius:var(--r);padding:1.5rem;text-align:center;cursor:pointer;background:var(--surface);transition:border-color .2s,background .2s;margin-bottom:1rem;}
.upload-area:hover{border-color:var(--accent2);background:#fffbf5;}
.upload-area.has-file{border-color:var(--success);background:#f0faf4;}
.upload-icon{font-size:1.6rem;margin-bottom:.35rem;}
.upload-text{font-size:.82rem;color:var(--ink3);}
.upload-text strong{color:var(--ink);}
.upload-name{font-size:.78rem;color:var(--success);font-weight:500;margin-top:4px;}
.upload-lbl{font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;margin-bottom:.4rem;color:var(--ink2);}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:.45rem;padding:.75rem 1.4rem;border-radius:var(--r);font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;cursor:pointer;border:none;transition:transform .2s cubic-bezier(.4,0,.2,1),box-shadow .2s cubic-bezier(.4,0,.2,1),background .2s;letter-spacing:.01em;}
.btn:hover:not(:disabled){transform:translateY(-2px);}
.btn:active:not(:disabled){transform:translateY(-1px) scale(.98);}
.btn-p{background:var(--accent);color:#fff;box-shadow:0 3px 12px rgba(232,76,30,.28);}
.btn-p:hover:not(:disabled){background:#d03d10;box-shadow:0 6px 20px rgba(232,76,30,.35);}
.btn-p:active:not(:disabled){box-shadow:0 2px 8px rgba(232,76,30,.25);}
.btn-p:disabled{background:#d8d4cc;color:#a09c94;box-shadow:none;cursor:not-allowed;transform:none;}
.btn-s{background:transparent;color:var(--ink2);border:2px solid var(--border);}
.btn-s:hover{border-color:#bbb;}
.btn-pp{background:#0070ba;color:#fff;box-shadow:0 3px 12px rgba(0,112,186,.28);font-size:.95rem;padding:.88rem 1.8rem;width:100%;justify-content:center;}
.btn-pp:hover{background:#005ea0;}
.btn-aanvraag{background:var(--ink2);color:#fff;box-shadow:0 3px 12px rgba(0,0,0,.15);font-size:.95rem;padding:.88rem 1.8rem;width:100%;justify-content:center;}
.btn-aanvraag:hover{background:var(--ink);}
.btn-row{display:flex;gap:.7rem;align-items:center;flex-wrap:wrap;}
/* sidebar CSS verwijderd — vervangen door persistent cart-panel */

/* Summary */
.sum-card{background:var(--surface);border:2px solid var(--border);border-radius:var(--r);overflow:hidden;margin-bottom:1.35rem;}
.sum-hd{padding:.68rem 1.2rem;background:var(--ink);font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.8);}
.sum-row{display:flex;justify-content:space-between;align-items:flex-start;padding:.65rem 1.2rem;border-bottom:1px solid var(--border);font-size:.875rem;}
.sum-row:last-child{border-bottom:none;}
.sum-row .k{color:var(--ink3);font-size:.78rem;flex-shrink:0;margin-right:.75rem;}
.sum-row .v{font-weight:500;text-align:right;max-width:60%;}
.sum-sep{padding:.45rem 1.2rem;background:#faf8f4;font-family:'Syne',sans-serif;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink3);}
/* Totaal — particulier (oranje hero = incl. BTW) */
.sum-total{display:flex;justify-content:space-between;padding:.9rem 1.2rem;background:#fff9f7;border-top:2px solid var(--accent);transition:all .2s;}
.sum-total .lbl{font-family:'Syne',sans-serif;font-weight:700;}
.sum-total .prc{font-family:'Syne',sans-serif;font-size:1.25rem;font-weight:800;color:var(--accent);}
/* Subtotaalrijen (excl./BTW/incl. klein) */
.sum-total-sub{display:flex;justify-content:space-between;padding:.55rem 1.2rem;border-top:1px solid var(--border);font-size:.875rem;transition:all .2s;}
.sum-total-sub .k{color:var(--ink3);font-size:.78rem;}
.sum-total-sub .v{font-weight:500;}
/* Bedrijf: excl. BTW wordt de hero (blauw blok) */
.sum-excl-hero{display:flex;justify-content:space-between;align-items:center;padding:1rem 1.2rem;background:#1a3a5c;border-top:2px solid #1a3a5c;}
.sum-excl-hero .lbl{font-family:'Syne',sans-serif;font-weight:700;color:#fff;font-size:.95rem;}
.sum-excl-hero .lbl small{display:block;font-size:.67rem;font-weight:500;opacity:.7;margin-top:1px;letter-spacing:.04em;}
.sum-excl-hero .prc{font-family:'Syne',sans-serif;font-size:1.35rem;font-weight:800;color:#fff;}
/* Incl. BTW als voetnoot bij bedrijf */
.sum-total-footnote{display:flex;justify-content:space-between;padding:.45rem 1.2rem;background:#f0f0f0;border-top:1px solid #ddd;font-size:.78rem;color:#888;}
.sum-total-footnote .lbl{font-size:.78rem;font-weight:500;}
.sum-total-footnote .v{font-weight:500;}
.sum-total-footnote .prc{font-size:.78rem;font-weight:600;color:#888;}
/* Klanttype toggle */
.klant-toggle{display:flex;background:var(--border);border-radius:8px;padding:3px;gap:3px;margin-bottom:.9rem;}
.klant-toggle button{flex:1;border:none;background:transparent;border-radius:6px;padding:.52rem .7rem;font-size:.78rem;font-weight:600;font-family:'Syne',sans-serif;cursor:pointer;color:var(--ink3);transition:background .2s,color .2s,box-shadow .2s;}
#kt-particulier.act,#kt-particulier2.act{background:var(--surface);color:var(--ink);box-shadow:0 1px 4px rgba(0,0,0,.1);}
#kt-bedrijf.act,#kt-bedrijf2.act{background:#1a3a5c;color:#fff;box-shadow:0 1px 6px rgba(26,58,92,.4);}

/* Product filter layout */
.mdl-layout{display:flex;gap:1.25rem;align-items:flex-start;}
.mdl-sidebar{width:185px;flex-shrink:0;background:var(--surface);border:2px solid var(--border);border-radius:var(--r);padding:.85rem;}
.mdl-main{flex:1;min-width:0;}
.mdl-sort-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem;gap:.75rem;flex-wrap:wrap;}
.mdl-count{font-size:.75rem;color:var(--ink3);}
.mdl-sort-sel{padding:.38rem .65rem;border:2px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.8rem;color:var(--ink);background:var(--surface);cursor:pointer;}
.mdl-sort-sel:focus{outline:none;border-color:var(--accent);}
.filter-grp{border-bottom:1px solid var(--border);padding-bottom:.75rem;margin-bottom:.75rem;}
.filter-grp:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0;}
.filter-ttl{font-family:'Syne',sans-serif;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--ink2);margin-bottom:.45rem;cursor:pointer;display:flex;justify-content:space-between;align-items:center;user-select:none;}
.filter-body{display:flex;flex-direction:column;gap:4px;max-height:140px;overflow-y:auto;}
.filter-body.clr-body{flex-direction:row;flex-wrap:wrap;gap:5px;max-height:none;}
.filter-item{display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.77rem;color:var(--ink2);line-height:1.3;}
.filter-item input[type=checkbox]{width:13px;height:13px;accent-color:var(--accent);cursor:pointer;flex-shrink:0;}
.filter-clr-dot{width:15px;height:15px;border-radius:50%;border:1.5px solid rgba(0,0,0,.12);flex-shrink:0;cursor:pointer;transition:box-shadow .15s;}
.filter-clr-dot.active{box-shadow:0 0 0 2.5px var(--surface),0 0 0 4px var(--accent);}
.mdl-no-results{grid-column:1/-1;padding:2rem;text-align:center;color:var(--ink3);font-size:.85rem;}
@media(max-width:640px){
  .mdl-layout{flex-direction:column;}
  .mdl-sidebar{width:100%;}
}

/* Misc */
.divider{border:none;border-top:1px solid var(--border);margin:1.3rem 0;}
.info-note{background:#fff8ec;border:1px solid #f7d89a;border-radius:8px;padding:.65rem .95rem;font-size:.78rem;color:#6b4800;margin-bottom:1.25rem;line-height:1.5;}
.info-note strong{font-weight:600;}
.info-note.blue{background:#e8f4fb;border-color:#a8d4ee;color:#1a4a6b;}
.qty-warn{font-size:.77rem;color:var(--accent);margin-bottom:.85rem;}
.ok-screen{text-align:center;padding:3rem 1rem;}
.ok-icon{width:68px;height:68px;background:#e4f4ec;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.1rem;}
.ok-icon svg{width:34px;height:34px;stroke:var(--success);fill:none;stroke-width:2.5;}
.ok-screen h2{font-family:'Syne',sans-serif;font-size:1.55rem;font-weight:800;margin-bottom:.45rem;}
.ok-screen p{color:var(--ink3);font-size:.88rem;line-height:1.6;max-width:420px;margin:0 auto 1.4rem;}
.hidden{display:none!important;}
.spinner{display:inline-block;width:16px;height:16px;border:2.5px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}

@media(max-width:560px){
  .g4{grid-template-columns:repeat(2,1fr);}
  .g3{grid-template-columns:1fr;}
  .g2{grid-template-columns:1fr;}
  .field-row{grid-template-columns:1fr;}
  .btn-row .btn{flex:1;justify-content:center;}
}
</style>

<div class="besteltool-wrap">
<div class="besteltool-cols">
<div class="shell">

<!-- Progress (6 stappen) -->
<div class="prog">
  <div class="pb active" id="pb1"></div>
  <div class="pb" id="pb2"></div>
  <div class="pb" id="pb3"></div>
  <div class="pb" id="pb4"></div>
  <div class="pb" id="pb5"></div>
  <div class="pb" id="pb6"></div>
</div>

<!-- STAP 1: TEXTIEL (cat -> model -> kleur) -->
<div id="step1">
  <div class="s-lbl">Stap 1 van 6</div>
  <div class="s-ttl" id="s1-ttl">Kies een categorie</div>
  <div class="trail" id="s1-trail"></div>

  <!-- Staffelkorting info -->
  <div class="info-note" style="background:#f0fdf4;border:2px solid #22c55e;border-left:none;margin-bottom:1.25rem;padding:1.5rem 1.25rem;">
    <div style="text-align:center;margin-bottom:1rem;">
      <div style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;color:#166534;letter-spacing:-.5px;">Staffelkorting textiel</div>
      <div style="font-size:.78rem;color:#15803d;margin-top:.35rem;">Hoe meer stuks, hoe groter uw voordeel</div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;text-align:center;">
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">50 – 99</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">5% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">100 – 199</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">10% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">200+</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">20% korting</div>
      </div>
    </div>
  </div>

  <!-- sub: categorie -->
  <div id="s1-cat">
    <div class="sub-lbl">Productcategorie</div>
    <div class="opt-grid g4" id="cat-grid"></div>
  </div>

  <!-- sub: model -->
  <div id="s1-mdl" class="hidden">
    <div class="mdl-layout">
      <div class="mdl-sidebar" id="mdl-sidebar"></div>
      <div class="mdl-main">
        <div class="mdl-sort-bar">
          <span class="mdl-count" id="mdl-count">0 producten</span>
          <select class="mdl-sort-sel" id="mdl-sort" onchange="applyFilters()">
            <option value="default">Best verkocht</option>
            <option value="price-asc">Prijs: laag &#8594; hoog</option>
            <option value="price-desc">Prijs: hoog &#8594; laag</option>
            <option value="name-asc">Naam: A &#8594; Z</option>
            <option value="brand-asc">Merk: A &#8594; Z</option>
          </select>
        </div>
        <div class="opt-grid g2" id="mdl-grid"></div>
      </div>
    </div>
    <div class="btn-row" style="margin-top:1rem"><button class="btn btn-s" onclick="s1Show('cat')">&#8592; Terug</button></div>
  </div>

  <!-- sub: kleur -->
  <div id="s1-clr" class="hidden">
    <div class="sub-lbl">Kies een kleur</div>
    <div id="clr-preview" style="text-align:center;margin-bottom:1rem;display:none">
      <img id="clr-preview-img" src="" alt="" style="max-height:220px;max-width:100%;border-radius:8px;object-fit:contain;">
    </div>
    <div class="sw-grid" id="sw-grid"></div>
    <div class="custom-row" id="custom-row" onclick="selCustom()">
      <div class="custom-dot"></div>
      <div class="custom-lbl"><strong>Overige kleur</strong><span>Vul zelf een kleur in</span></div>
    </div>
    <div id="custom-field" class="hidden">
      <input class="custom-inp" id="custom-inp" type="text" placeholder="Bijv. bordeauxrood, lichtblauw, PANTONE 286 C..." oninput="onCustomInp()">
      <button class="btn btn-p" style="margin-top:.75rem" onclick="confirmCustom()">Bevestig kleur &#8594;</button>
    </div>
    <div class="chosen-bar hidden" id="chosen-bar">
      <div class="chosen-dot" id="chosen-dot"></div>
      <div class="chosen-nm" id="chosen-nm"></div>
    </div>
    <div class="btn-row" style="margin-top:.5rem">
      <button class="btn btn-s" onclick="s1Show('mdl')">&#8592; Terug</button>
      <button class="btn btn-p" id="btn-clr-next" onclick="gS(2)" disabled>Volgende &#8594;</button>
    </div>
  </div>
</div>

<!-- STAP 2: DRUKPOSITIE -->
<div id="step2" class="hidden">
  <div class="s-lbl">Stap 2 van 6</div>
  <div class="s-ttl">Kies een drukpositie</div>

  <!-- Staffelkorting info -->
  <div class="info-note" style="background:#f0fdf4;border:2px solid #22c55e;border-left:none;margin-bottom:1.25rem;padding:1.5rem 1.25rem;">
    <div style="text-align:center;margin-bottom:1rem;">
      <div style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;color:#166534;letter-spacing:-.5px;">Staffelkorting textiel</div>
      <div style="font-size:.78rem;color:#15803d;margin-top:.35rem;">Hoe meer stuks, hoe groter uw voordeel</div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;text-align:center;">
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">50 – 99</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">5% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">100 – 199</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">10% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">200+</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">20% korting</div>
      </div>
    </div>
  </div>

  <div class="opt-grid g3">
    <div class="opt" id="pos-front" onclick="selPos('front')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-name">Voorkant</div>
      <div class="pos-desc">Bedrukking op de voorzijde van het textiel</div>
    </div>
    <div class="opt" id="pos-back" onclick="selPos('back')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-name">Achterkant</div>
      <div class="pos-desc">Bedrukking op de achterzijde van het textiel</div>
    </div>
    <div class="opt" id="pos-both" onclick="selPos('both')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-name">Beide kanten</div>
      <div class="pos-desc">Voor- &eacute;n achterkant bedrukt. Per kant eigen techniek en ontwerp.</div>
      <div class="pos-note">= 2&times; bedrukking</div>
    </div>
    <div class="opt" id="pos-left" onclick="selPos('left')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-name">Linkerborst</div>
      <div class="pos-desc">Bedrukking op de linkerborst van het textiel</div>
    </div>
    <div class="opt" id="pos-right" onclick="selPos('right')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-name">Rechterborst</div>
      <div class="pos-desc">Bedrukking op de rechterborst van het textiel</div>
    </div>
    <div class="opt" id="pos-left-back" onclick="selPos('left-back')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-name">Linkerborst + Achterkant</div>
      <div class="pos-desc">Linkerborst en achterkant bedrukt. Per kant eigen techniek en ontwerp.</div>
      <div class="pos-note">= 2&times; bedrukking</div>
    </div>
    <div class="opt" id="pos-right-back" onclick="selPos('right-back')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-name">Rechterborst + Achterkant</div>
      <div class="pos-desc">Rechterborst en achterkant bedrukt. Per kant eigen techniek en ontwerp.</div>
      <div class="pos-note">= 2&times; bedrukking</div>
    </div>
  </div>
  <div class="btn-row">
    <button class="btn btn-s" onclick="gS(1)">&#8592; Terug</button>
    <button class="btn btn-p" id="btn2" onclick="enterStep3()" disabled>Volgende stap &#8594;</button>
  </div>
</div>

<!-- STAP 3: DRUKTECHNIEK -->
<div id="step3" class="hidden">
  <div class="s-lbl">Stap 3 van 6</div>
  <div class="s-ttl" id="s3-ttl">Kies een druktechniek</div>

  <!-- Staffelkorting info -->
  <div class="info-note" style="background:#f0fdf4;border:2px solid #22c55e;border-left:none;margin-bottom:1.25rem;padding:1.5rem 1.25rem;">
    <div style="text-align:center;margin-bottom:1rem;">
      <div style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;color:#166534;letter-spacing:-.5px;">Staffelkorting textiel</div>
      <div style="font-size:.78rem;color:#15803d;margin-top:.35rem;">Hoe meer stuks, hoe groter uw voordeel</div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;text-align:center;">
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">50 – 99</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">5% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">100 – 199</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">10% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">200+</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">20% korting</div>
      </div>
    </div>
  </div>

  <!-- kant indicator (alleen bij 'both') -->
  <div id="kant-ind" class="hidden">
    <div class="kant-hdr">
      <div class="kant-dot"></div>
      <div class="kant-lbl" id="kant-lbl-txt">Voorkant</div>
      <div class="kant-sub" id="kant-sub-txt"></div>
    </div>
  </div>

  <div class="opt-grid g3" id="tech-grid">
    <div class="opt" id="tc-dtf" onclick="selTech('dtf')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <img src="/img/dtf_machine.jpg" alt="DTF druk" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:.5rem;">
      <div class="tc-name">DTF druk</div>
      <div class="tc-desc">Full colour, scherpe details. Foto's en complexe ontwerpen.</div>
      <span class="tc-badge green">Vanaf 1 stuk</span>
    </div>
    <div class="opt" id="tc-zeef" onclick="selTech('zeef')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <img src="/img/zeefdruk.jpg" alt="Zeefdruk" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:.5rem;">
      <div class="tc-name">Zeefdruk</div>
      <div class="tc-desc">Traditionele techniek, levendig en duurzaam. Grote oplages.</div>
      <span class="tc-badge orange">Vanaf 25 stuks</span>
    </div>
    <div class="opt" id="tc-bord" onclick="selTech('bord')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <img src="/img/borduren.jpg" alt="Borduren" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:.5rem;">
      <div class="tc-name">Borduren</div>
      <div class="tc-desc">Premium uitstraling op polo's, caps en jassen.</div>
      <div style="margin-top:.5rem"><span class="badge" style="background:#f3ede3;color:#7a5c3a;font-size:.68rem;padding:.25rem .55rem;border-radius:20px;font-weight:600">Op aanvraag</span></div>
    </div>
  </div>

  <div id="ti-dtf" class="info-note hidden"><strong>DTF:</strong> Geen minimale oplage, full colour. Geschikt voor katoen, polyester en nylon. Levertijd 5&ndash;8 werkdagen.</div>
  <div id="ti-zeef" class="info-note hidden"><strong>Zeefdruk:</strong> Maximaal 4 kleuren per ontwerp. Voordeligst bij 25+ stuks. Levertijd 6&ndash;10 werkdagen.</div>


  <div class="btn-row">
    <button class="btn btn-s" onclick="gS(2)">&#8592; Terug</button>
    <button class="btn btn-p" id="btn3" onclick="afterTech()" disabled>Volgende stap &#8594;</button>
  </div>
</div>

<!-- STAP 4: MATEN & AANTALLEN -->
<div id="step4" class="hidden">
  <div class="s-lbl">Stap 4 van 5</div>
  <div class="s-ttl">Maten &amp; aantallen</div>

  <!-- Staffelkorting info -->
  <div class="info-note" style="background:#f0fdf4;border:2px solid #22c55e;border-left:none;margin-bottom:1.25rem;padding:1.5rem 1.25rem;">
    <div style="text-align:center;margin-bottom:1rem;">
      <div style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;color:#166534;letter-spacing:-.5px;">Staffelkorting textiel</div>
      <div style="font-size:.78rem;color:#15803d;margin-top:.35rem;">Hoe meer stuks, hoe groter uw voordeel</div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;text-align:center;">
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">50 – 99</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">5% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">100 – 199</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">10% korting</div>
      </div>
      <div style="padding:.85rem;background:rgba(34,197,94,.08);border-radius:8px;">
        <div style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:#166534;">200+</div>
        <div style="font-size:.75rem;color:#15803d;margin-top:.25rem;">20% korting</div>
      </div>
    </div>
  </div>

  <!-- Zeef: kleuren per kant -->
  <div id="zeef-front-col" class="hidden">
    <div id="zeef-front-khdr" class="kant-hdr hidden">
      <div class="kant-dot"></div><div class="kant-lbl">Voorkant &mdash; zeefdruk kleuren</div>
    </div>
    <div class="sub-lbl" style="margin-top:.5rem">Aantal drukkleuren voorkant</div>
    <div class="field" style="margin-bottom:.85rem">
      <select id="zc-front-sel" onchange="selZC('front',parseInt(this.value))">
        <option value="1">1 kleur</option>
        <option value="2">2 kleuren</option>
        <option value="3">3 kleuren</option>
        <option value="4">4 kleuren</option>
      </select>
    </div>
  </div>
  <div id="zeef-back-col" class="hidden">
    <div class="kant-hdr" style="margin-top:.5rem">
      <div class="kant-dot" style="background:#888"></div><div class="kant-lbl">Achterkant &mdash; zeefdruk kleuren</div>
    </div>
    <div class="sub-lbl" style="margin-top:.5rem">Aantal drukkleuren achterkant</div>
    <div class="field" style="margin-bottom:.85rem">
      <select id="zc-back-sel" onchange="selZC('back',parseInt(this.value))">
        <option value="1">1 kleur</option>
        <option value="2">2 kleuren</option>
        <option value="3">3 kleuren</option>
        <option value="4">4 kleuren</option>
      </select>
    </div>
  </div>
  <div id="zeef-left-col" class="hidden">
    <div class="kant-hdr" style="margin-top:.5rem">
      <div class="kant-dot"></div><div class="kant-lbl">Linkerborst &mdash; zeefdruk kleuren</div>
    </div>
    <div class="sub-lbl" style="margin-top:.5rem">Aantal drukkleuren linkerborst</div>
    <div class="field" style="margin-bottom:.85rem">
      <select id="zc-left-sel" onchange="selZC('left',parseInt(this.value))">
        <option value="">Kies het aantal kleuren</option>
        <option value="1">1 kleur</option>
        <option value="2">2 kleuren</option>
        <option value="3">3 kleuren</option>
        <option value="4">4 kleuren</option>
      </select>
    </div>
  </div>
  <div id="zeef-right-col" class="hidden">
    <div class="kant-hdr" style="margin-top:.5rem">
      <div class="kant-dot"></div><div class="kant-lbl">Rechterborst &mdash; zeefdruk kleuren</div>
    </div>
    <div class="sub-lbl" style="margin-top:.5rem">Aantal drukkleuren rechterborst</div>
    <div class="field" style="margin-bottom:.85rem">
      <select id="zc-right-sel" onchange="selZC('right',parseInt(this.value))">
        <option value="">Kies het aantal kleuren</option>
        <option value="1">1 kleur</option>
        <option value="2">2 kleuren</option>
        <option value="3">3 kleuren</option>
        <option value="4">4 kleuren</option>
      </select>
    </div>
  </div>

  <div class="sub-lbl" style="margin-top:.25rem">Verdeling over maten</div>
  <table class="sz-tbl"><thead><tr><th>Maat</th><th>Aantal</th><th>Maat</th><th>Aantal</th></tr></thead><tbody id="sz-tbody"></tbody></table>
  <div id="qty-warn" class="qty-warn hidden"></div>

  <!-- Klanttype toggle (persistent, stap 4 en hoger) -->
  <div class="klant-toggle" id="klant-toggle-wrap" style="display:none">
    <button id="kt-particulier" class="act" onclick="setKlantType('particulier')">&#128100; Particulier &mdash; incl. BTW</button>
    <button id="kt-bedrijf" onclick="setKlantType('bedrijf')">&#127970; Bedrijf &mdash; excl. BTW</button>
  </div>

  <!-- Live offerte -->
  <div class="sum-card" id="quote-box" style="display:none">
    <div class="sum-hd">Jouw offerte</div>
    <div class="sum-row" id="qr-textiel" style="display:none"><span class="k">Textiel</span><span class="v" id="q-textiel-nm">&ndash;</span></div>
    <div class="sum-row" id="qr-korting" style="display:none"><span class="k">Staffelkorting textiel</span><span class="v" id="q-korting-pct" style="color:#2a9d5c;font-weight:700">&ndash;</span></div>
    <div class="sum-row" id="qr-kleur" style="display:none"><span class="k">Kleur</span><span class="v" id="q-kleur-nm">&ndash;</span></div>
    <div class="sum-row"><span class="k">Stuks</span><span class="v" id="q-stuks">&ndash;</span></div>
    <div class="sum-row"><span class="k">Positie</span><span class="v" id="q-pos">&ndash;</span></div>
    <div class="sum-sep" id="q-sep-a">Voorkant</div>
    <div class="sum-row"><span class="k">Techniek</span><span class="v" id="q-tech-a">&ndash;</span></div>
    <div class="sum-row"><span class="k">Prijs per stuk</span><span class="v" id="q-up-a">&ndash;</span></div>
    <div class="sum-row" id="q-druk-a-row"><span class="k" id="q-druk-a-lbl">Bedrukking</span><span class="v" id="q-druk-a">&ndash;</span></div>
    <div class="sum-sep hidden" id="q-sep-b">Achterkant</div>
    <div class="sum-row hidden" id="q-tech-b-row"><span class="k">Techniek</span><span class="v" id="q-tech-b">&ndash;</span></div>
    <div class="sum-row hidden" id="q-up-b-row"><span class="k">Prijs per stuk</span><span class="v" id="q-up-b">&ndash;</span></div>
    <div class="sum-row hidden" id="q-druk-b-row"><span class="k" id="q-druk-b-lbl">Bedrukking</span><span class="v" id="q-druk-b">&ndash;</span></div>
    <div class="sum-row" id="q-textiel-row" style="display:none"><span class="k" id="q-textiel-lbl">Textiel</span><span class="v" id="q-textiel-prijs">&ndash;</span></div>
    <!-- BTW-blok: excl → BTW → incl (producten, zonder verzending) -->
    <div id="q-excl-row" class="sum-total-sub"><span class="k">Subtotaal excl. BTW</span><span class="v" id="q-total-excl">&ndash;</span></div>
    <div id="q-btw-row" class="sum-total-sub"><span class="k">BTW 21%</span><span class="v" id="q-btw">&ndash;</span></div>
    <!-- Verzending (vaste post, incl. BTW) -->
    <div class="sum-row"><span class="k">Verzending</span><span class="v" id="q-ship">&ndash;</span></div>
    <!-- Eindtotaal — klasse wisselt per klanttype via JS -->
    <div id="q-incl-row" class="sum-total"><span class="lbl">Totaal incl. BTW</span><span class="prc" id="q-total">&ndash;</span></div>
  </div>

  <div class="btn-row">
    <button class="btn btn-s" onclick="goBackFromStep4()">&#8592; Terug</button>
    <button class="btn btn-p" id="btn4" onclick="gS(5)" disabled>Volgende stap &#8594;</button>
  </div>
</div>

<!-- STAP 5: ONTWERP & GEGEVENS -->
<div id="step5" class="hidden">
  <div class="s-lbl">Stap 5 van 5</div>
  <div class="s-ttl">Jouw ontwerp &amp; instructies</div>

  <!-- Upload voorkant -->
  <div id="upload-front-wrap">
    <div class="upload-lbl" id="upload-front-lbl">Logo / ontwerp</div>
    <div class="upload-area" id="upload-front" onclick="document.getElementById('file-front').click()">
      <div class="upload-icon">&#128193;</div>
      <div class="upload-text"><strong>Klik om te uploaden</strong> of sleep hier naartoe</div>
      <div class="upload-text" style="margin-top:3px">AI, EPS, PDF, PNG, SVG &ndash; max. 50 MB</div>
      <div class="upload-name" id="upload-front-name"></div>
    </div>
    <input type="file" id="file-front" accept=".ai,.eps,.pdf,.png,.svg,.jpg,.jpeg" onchange="handleUpload('front',this)">
  </div>

  <!-- Upload achterkant (alleen bij 'both') -->
  <div id="upload-back-wrap" class="hidden">
    <div class="upload-lbl" style="margin-top:.5rem">Logo / ontwerp achterkant</div>
    <div class="upload-area" id="upload-back" onclick="document.getElementById('file-back').click()">
      <div class="upload-icon">&#128193;</div>
      <div class="upload-text"><strong>Klik om te uploaden</strong> of sleep hier naartoe</div>
      <div class="upload-text" style="margin-top:3px">AI, EPS, PDF, PNG, SVG &ndash; max. 50 MB</div>
      <div class="upload-name" id="upload-back-name"></div>
    </div>
    <input type="file" id="file-back" accept=".ai,.eps,.pdf,.png,.svg,.jpg,.jpeg" onchange="handleUpload('back',this)">
  </div>

  <div class="info-note" style="margin-top:.75rem"><strong>Geen ontwerp klaar?</strong> Geen probleem &mdash; beschrijf je wens hieronder, dan nemen we contact op.</div>
  <div class="field"><label>Omschrijving / bijzonderheden</label><textarea id="notes" placeholder="Bijv: logo op borst links, witte tekst op zwarte achtergrond..."></textarea></div>

  <div class="btn-row">
    <button class="btn btn-s" onclick="gS(4)">&#8592; Terug</button>
    <button class="btn btn-p" id="btn5-wagen" onclick="toevoegenAanWagen()">Toevoegen aan winkelwagen &#8594;</button>
  </div>
</div>


<!-- BORDUREN AANVRAAG -->
<div id="step-bord" class="hidden">
  <div class="s-lbl">Borduren op aanvraag</div>
  <div class="s-ttl">Stuur je borduurwens in</div>
  <div class="info-note blue">We berekenen de prijs handmatig op basis van het aantal steken in jouw logo. Je ontvangt binnen 1&ndash;2 werkdagen een offerte per e-mail.</div>

  <div class="upload-lbl">Logo / ontwerp (verplicht)</div>
  <div class="upload-area" id="upload-bord" onclick="document.getElementById('file-bord').click()">
    <div class="upload-icon">&#128193;</div>
    <div class="upload-text"><strong>Klik om te uploaden</strong> of sleep hier naartoe</div>
    <div class="upload-text" style="margin-top:3px">AI, EPS, PDF, PNG, SVG &ndash; max. 50 MB</div>
    <div class="upload-name" id="upload-bord-name"></div>
  </div>
  <input type="file" id="file-bord" accept=".ai,.eps,.pdf,.png,.svg,.jpg,.jpeg" onchange="handleUpload('bord',this)">

  <div class="field" style="margin-top:.75rem"><label>Omschrijving / wensen</label><textarea id="bord-notes" placeholder="Bijv: logo op borst links, gewenste kleur, type textiel..."></textarea></div>
  <hr class="divider">
  <div class="field-row">
    <div class="field"><label>Voornaam *</label><input type="text" id="bfname" placeholder="Jan" oninput="chkBord()"></div>
    <div class="field"><label>Achternaam *</label><input type="text" id="blname" placeholder="de Vries" oninput="chkBord()"></div>
  </div>
  <div class="field"><label>E-mailadres *</label><input type="email" id="bemail" placeholder="jan@bedrijf.nl" oninput="chkBord()"></div>
  <div class="field"><label>Telefoonnummer</label><input type="tel" id="bphone" placeholder="+31 6 12345678"></div>
  <div class="field"><label>Bedrijfsnaam (optioneel)</label><input type="text" id="bcompany" placeholder="Bedrijf BV"></div>
  <div class="field-row">
    <div class="field"><label>Straat + huisnummer (optioneel)</label><input type="text" id="baddress" placeholder="Voorbeeldstraat 12"></div>
    <div class="field"><label>Postcode (optioneel)</label><input type="text" id="bzip" placeholder="1234 AB"></div>
  </div>
  <div class="field"><label>Stad (optioneel)</label><input type="text" id="bcity" placeholder="Amsterdam"></div>
  <div class="field-row">
    <div class="field"><label>KVK-nummer (optioneel)</label><input type="text" id="bkvk" placeholder="12345678"></div>
    <div class="field"><label>BTW-nummer (optioneel)</label><input type="text" id="bbtw" placeholder="NL123456789B01"></div>
  </div>

  <button class="btn btn-aanvraag" id="btn-bord" onclick="sendBordAanvraag()" disabled>
    Aanvraag versturen
  </button>
  <div class="btn-row" style="margin-top:1rem"><button class="btn btn-s" onclick="gS(3)">&#8592; Terug naar technieken</button></div>
</div>

<!-- SUCCESS -->
<div id="success" class="hidden">
  <div class="ok-screen">
    <div class="ok-icon"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
    <h2 id="ok-title">Bestelling ontvangen!</h2>
    <p id="ok-msg">Bedankt voor je bestelling bij Merch Master. Je ontvangt een bevestiging op <strong id="confirm-email"></strong>.<br><br>Vragen? WhatsApp: <a href="https://wa.me/31617255170" style="color:var(--accent)">+31 6 17 25 51 70</a></p>
    <button class="btn btn-p" onclick="doReset()">Nieuwe bestelling</button>
  </div>
</div>

</div><!-- /shell -->

<!-- ── WINKELWAGEN PANEEL (rechts, altijd zichtbaar) ── -->
<div class="cart-panel">
  <div class="cart-panel-hd">
    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:.95rem;color:var(--ink);display:flex;align-items:center;gap:.5rem;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      Winkelwagen
    </div>
    <div id="cart-panel-count" style="font-size:.75rem;color:var(--ink3);margin-top:.2rem;"></div>
  </div>
  <div id="cart-panel-items" style="flex:1;overflow-y:auto;padding:1rem 1.1rem;">
    <div style="text-align:center;padding:2rem 1rem;color:var(--ink3);">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.35;margin-bottom:.6rem;display:block;margin-left:auto;margin-right:auto;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      <div style="font-size:.82rem;line-height:1.5;">Nog geen producten.<br>Doorloop de stappen<br>om te beginnen.</div>
    </div>
  </div>
  <div id="cart-panel-footer" style="display:none;padding:1rem 1.1rem;border-top:1px solid var(--border);flex-shrink:0;">
    <div style="font-size:.77rem;color:var(--ink3);margin-bottom:.5rem;">
      <div style="display:flex;justify-content:space-between;padding:.15rem 0;"><span>Subtotaal excl. BTW</span><span id="cart-panel-excl" style="font-weight:600;"></span></div>
      <div style="display:flex;justify-content:space-between;padding:.15rem 0;"><span>BTW 21%</span><span id="cart-panel-btw" style="font-weight:600;"></span></div>
      <div style="display:flex;justify-content:space-between;padding:.15rem 0;"><span>Verzending</span><span id="cart-panel-verzend" style="font-weight:600;"></span></div>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;padding-top:.4rem;border-top:1px solid var(--border);">
      <div style="font-size:.82rem;font-weight:600;color:var(--ink);">Totaal incl. BTW</div>
      <div id="cart-panel-totaal" style="font-size:1.05rem;font-weight:800;color:var(--accent);"></div>
    </div>
    <button class="btn btn-p" onclick="goToCheckout();" style="width:100%;font-size:.84rem;">Betalen &#8594;</button>
  </div>
</div>

</div><!-- /besteltool-cols -->
</div><!-- /besteltool-wrap -->

<script src="https://www.paypal.com/sdk/js?client-id=ASLap52V7_VjYsq3D5k1W9a9RLG7854wBRs9TQ0m0PHhLXALJwrG3i-r4nrQOMuUr0d_Dqr5BSMv4ebk&currency=EUR&locale=nl_NL"></script>
<script>
// ── Backend URL ────────────────────────────────────────────────────────────────
const HANDLER_URL = '/bestellen/handler.php';

// ── Dynamic catalog data ───────────────────────────────────────────────────────
let CATS = [];
let MODELS = {};
let ACTIVE_FILTERS = {merken:[], kleuren:[], maten:[]};
let CURRENT_CAT = null;

const CAT_SVG = {
  't-shirts': '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10 L12 6 L16 12 C16 12 18 14 20 14 C22 14 24 12 24 12 L28 6 L36 10 L32 20 L28 20 L28 36 L12 36 L12 20 L8 20 Z"/></svg>',
  'polos':    '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10 L12 6 L16 12 C16 12 18 10 20 10 C22 10 24 12 24 12 L28 6 L36 10 L32 20 L28 20 L28 36 L12 36 L12 20 L8 20 Z"/><line x1="20" y1="10" x2="20" y2="18"/><line x1="17" y1="13" x2="23" y2="13"/></svg>',
  'sweaters': '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10 L12 6 L14 10 C14 10 16 14 20 14 C24 14 26 10 26 10 L28 6 L36 10 L34 22 L28 22 L28 36 L12 36 L12 22 L6 22 Z"/><path d="M4 10 L6 22"/><path d="M36 10 L34 22"/></svg>',
  'hoodies':  '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10 L12 6 C12 6 14 14 20 14 C26 14 28 6 28 6 L36 10 L34 22 L28 22 L28 36 L12 36 L12 22 L6 22 Z"/><path d="M15 6 C15 6 17 10 20 10 C23 10 25 6 25 6"/><path d="M4 10 L6 22"/><path d="M36 10 L34 22"/><line x1="20" y1="14" x2="20" y2="36"/></svg>',
  'caps':     '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 24 C8 24 10 14 20 12 C30 14 32 24 32 24 Z"/><path d="M6 24 L34 24"/><path d="M6 24 C6 24 4 26 8 27 L32 27"/></svg>',
  'jassen':   '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8 L13 5 L16 10 C16 10 18 13 20 13 C22 13 24 10 24 10 L27 5 L36 8 L34 38 L26 38 L26 20 L14 20 L14 38 L6 38 Z"/><path d="M4 8 L6 20"/><path d="M36 8 L34 20"/><line x1="20" y1="13" x2="20" y2="38"/></svg>',
  'tassen':   '<svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="16" width="24" height="20" rx="2"/><path d="M14 16 C14 16 14 10 20 10 C26 10 26 16 26 16"/><line x1="8" y1="24" x2="32" y2="24"/></svg>',
};

async function loadCatalogus() {
  try {
    const r = await fetch('/bestellen/catalogus.php');
    const data = await r.json();
    if (!data.ok) throw new Error('API fout');

    CATS = data.categorieen
      .filter(c => c.aantal > 0)
      .map(c => ({
        id: c.slug,
        name: c.naam,
        icon: CAT_SVG[c.slug] || CAT_SVG['t-shirts'],
        sizes: []
      }));

    MODELS = {};
    data.producten.forEach(p => {
      if (!MODELS[p.categorie_slug]) MODELS[p.categorie_slug] = [];
      MODELS[p.categorie_slug].push({
        id: p.sku,
        brand: p.merk,
        name: p.naam,
        sku: p.sku,
        tier: p.tier || 'standaard',
        inkoop: p.inkoop || 0,
        tags: p.tags ? p.tags.split(',').map(t=>t.trim()).filter(Boolean) : [],
        eco: false,
        prem: p.tier === 'premium',
        kleuren: p.kleuren ? p.kleuren.map(c=>({id:c.code, code:c.code, name:c.naam, hex:c.hex, image_url:c.image_url||null})) : [],
        sizes: p.maten || ['XS','S','M','L','XL','2XL'],
        image_url: p.image_url || null
      });
    });
  } catch(err) {
    console.error('Catalogus laden mislukt:', err);
    const g = e('cat-grid');
    if (g) g.innerHTML = '<p style="color:red;padding:1rem;grid-column:1/-1">Catalogus laden mislukt. Ververs de pagina.</p>';
  }
}

// ── Pricing tables (geladen vanuit admin DB, anders fallback) ──────────────────
const _DK = <?php echo $_drukkostenJS; ?>;
const _LT = <?php echo $_levertijdenJS; ?>;
const _MG = <?php echo $_margesJS; ?>;

// DTF: bouw DP array uit admin data (voorkant/achterkant — verplicht — geen fallback)
const DP = [];
if (_DK && _DK.dtf && _DK.dtf.matrix) {
  // Admin formaat: {oplagen: ["1-9","10-50","50+"], matrix: {"1-9":9.00,...}}
  const _oplagen = _DK.dtf.oplagen || [];
  const _matrix  = _DK.dtf.matrix  || {};
  _oplagen.forEach((opl) => {
    const parts = String(opl).split('-');
    const mn = parseInt(parts[0]) || 1;
    const mx = (parts[1] !== undefined) ? parseInt(parts[1]) : 99999;
    const u  = parseFloat(_matrix[opl]) || 0;
    DP.push({min: mn, max: mx, u: u});
  });
  DP.sort((a,b) => a.min - b.min);
  for (let i = 0; i < DP.length - 1; i++) DP[i].max = DP[i+1].min - 1;
  if (DP.length) DP[DP.length-1].max = 99999;
}

// DTF borst: bouw DPborst array uit admin data (linkerborst/rechterborst — verplicht — geen fallback)
const DPborst = [];
if (_DK && _DK.dtf_borst && _DK.dtf_borst.matrix) {
  const _oplagen = _DK.dtf_borst.oplagen || [];
  const _matrix  = _DK.dtf_borst.matrix  || {};
  _oplagen.forEach((opl) => {
    const parts = String(opl).split('-');
    const mn = parseInt(parts[0]) || 1;
    const mx = (parts[1] !== undefined) ? parseInt(parts[1]) : 99999;
    const u  = parseFloat(_matrix[opl]) || 0;
    DPborst.push({min: mn, max: mx, u: u});
  });
  DPborst.sort((a,b) => a.min - b.min);
  for (let i = 0; i < DPborst.length - 1; i++) DPborst[i].max = DPborst[i+1].min - 1;
  if (DPborst.length) DPborst[DPborst.length-1].max = 99999;
}

// Zeefdruk: bouw ZP array uit admin data (verplicht — geen fallback)
const ZP = [];
if (_DK && _DK.zeef && _DK.zeef.matrix) {
  const _zOplagen = _DK.zeef.oplagen || [];
  const _zKleuren = _DK.zeef.kleuren || [1,2,3,4];
  const _zMatrix  = _DK.zeef.matrix  || {};
  _zOplagen.forEach((opl, i) => {
    const mn = parseInt(opl);
    const mx = (_zOplagen[i+1] !== undefined) ? parseInt(_zOplagen[i+1]) - 1 : 99999;
    const c  = _zKleuren.map(kl => parseFloat((_zMatrix[kl] && _zMatrix[kl][opl]) || 0));
    ZP.push({min: mn, max: mx, c: c});
  });
}

// Marges uit admin DB — fallback naar vaste waarden als DB niet beschikbaar
const MARKUP = {
  budget:    (_MG && _MG.textiel_budget    != null) ? parseFloat(_MG.textiel_budget)    : 1.50,
  standaard: (_MG && _MG.textiel_standaard != null) ? parseFloat(_MG.textiel_standaard) : 1.65,
  premium:   (_MG && _MG.textiel_premium   != null) ? parseFloat(_MG.textiel_premium)   : 1.80,
};

function calcPrijsEx(mdl) {
  if (!mdl || !mdl.inkoop) return 2.50;
  const factor = MARKUP[mdl.tier] || MARKUP.standaard;
  return parseFloat((mdl.inkoop * factor).toFixed(4)); // excl. BTW: inkoop × factor, BTW apart
}

function getTextielKorting(qty) {
  if (qty >= 200) return 0.20;
  if (qty >= 100) return 0.10;
  if (qty >= 50)  return 0.05;
  return 0;
}

// ── Klanttype (stap 4) ────────────────────────────────────────────────────────
function setKlantType(type) {
  S.klantType = type;
  e('kt-particulier').classList.toggle('act', type==='particulier');
  e('kt-bedrijf').classList.toggle('act', type==='bedrijf');
  // Herbereken offerte met nieuwe klanttype (zodat prijsblok correct wordt bijgewerkt)
  if(S.qty > 0) calcQ();
}


// ── State ──────────────────────────────────────────────────────────────────────
const S = {
  cat:null, mdl:null, clrId:null, clrName:null, clrHex:null,
  pos:null,
  techA:null,
  techB:null,
  zcA:1, zcB:1,
  configuring:'A',
  klantType:'particulier',
  qty:0,
  upA:0, upB:0,
  ship:0, tot:0,
  prijsEx:0,
};

// ── Helpers ────────────────────────────────────────────────────────────────────
const fmt = n => '\u20AC'+n.toFixed(2).replace('.',',');
function e(id){return document.getElementById(id);}
function allSteps(){return ['step1','step2','step3','step4','step5','step-bord','success'];}
function formatPosLabel(pos){const map={'voorkant':'Voorkant','achterkant':'Achterkant','linkerborst':'Linkerborst','rechterborst':'Rechterborst'};return map[pos]||pos;}

// ── Navigation ─────────────────────────────────────────────────────────────────
function gS(n){
  allSteps().forEach(id=>e(id).classList.add('hidden'));
  if(n==='bord'){
    e('step-bord').classList.remove('hidden');
    updProg(3);
  } else if(n==='success'){
    e('success').classList.remove('hidden');
    updProg(7);
  } else {
    e('step'+n).classList.remove('hidden');
    updProg(n);
    if(n===4) setupStep4();
    if(n===5) setupStep5();
  }
  window.scrollTo({top:0,behavior:'smooth'});
}

function updProg(n){
  for(let i=1;i<=5;i++){
    const el=e('pb'+i);if(!el)continue;
    el.classList.remove('done','active');
    if(i<n) el.classList.add('done');
    else if(i===n) el.classList.add('active');
  }
}

// ── STAP 1: Textiel ────────────────────────────────────────────────────────────
function s1Show(sub){
  ['cat','mdl','clr'].forEach(x=>e('s1-'+x).classList.toggle('hidden',x!==sub));
  const titles={cat:'Kies een categorie',mdl:'Kies een model',clr:'Kies een kleur'};
  e('s1-ttl').textContent=titles[sub];
  updTrail(sub);
}

function updTrail(sub){
  const t=e('s1-trail');t.innerHTML='';
  const cat=CATS.find(c=>c.id===S.cat);
  const add=(lbl,fn,cur)=>{
    if(t.children.length){const sp=document.createElement('span');sp.className='crumb-sep';sp.textContent='\u203A';t.appendChild(sp);}
    const sp=document.createElement('span');sp.className='crumb'+(cur?' cur':fn?' lnk':'');sp.textContent=lbl;
    if(fn) sp.onclick=fn;t.appendChild(sp);
  };
  add('Categorie',sub!=='cat'?()=>s1Show('cat'):null,sub==='cat');
  if(sub==='mdl'||sub==='clr') add(cat?.name||'Model',sub!=='mdl'?()=>s1Show('mdl'):null,sub==='mdl');
  if(sub==='clr') add(S.mdl?(S.mdl.brand+' '+S.mdl.name):'Kleur',null,true);
}

function buildCatGrid(){
  const g=e('cat-grid');g.innerHTML='';
  CATS.forEach(c=>{
    const d=document.createElement('div');
    d.className='opt cat-opt';d.id='cc-'+c.id;
    d.innerHTML='<div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>'
      +'<div class="cat-icon">'+c.icon+'</div>'
      +'<div class="cat-name">'+c.name+'</div>';
    d.onclick=()=>selCat(c.id);g.appendChild(d);
  });
}

function buildFilters(catId){
  const producten = MODELS[catId]||[];
  const sidebar = e('mdl-sidebar');
  if(!sidebar) return;

  // Verzamel unieke waarden
  const merken  = [...new Set(producten.map(m=>m.brand))].sort();
  const STANDAARD_MATEN = ['XXS','XS','S','M','L','XL','XXL','2XL','3XL','4XL','5XL'];
  const maten = [...new Set(producten.flatMap(m=>m.sizes))].filter(s=>STANDAARD_MATEN.includes(s));
  maten.sort((a,b)=>STANDAARD_MATEN.indexOf(a)-STANDAARD_MATEN.indexOf(b));
  const kleuren = [];
  const clrSeen = new Set();
  producten.forEach(m=>m.kleuren.forEach(c=>{if(!clrSeen.has(c.code)){clrSeen.add(c.code);kleuren.push(c);}}));
  kleuren.sort((a,b)=>(a.name||'').localeCompare(b.name||''));

  let html='';

  if(merken.length>1){
    html+='<div class="filter-grp"><div class="filter-ttl">Merk</div><div class="filter-body" id="fb-merk">';
    merken.forEach(m=>{
      html+='<label class="filter-item"><input type="checkbox" value="'+m+'" onchange="toggleFilter(\'merken\',this.value,this.checked)"> '+m+'</label>';
    });
    html+='</div></div>';
  }

  if(maten.length>0){
    html+='<div class="filter-grp"><div class="filter-ttl">Maat</div><div class="filter-body" id="fb-maat">';
    maten.forEach(m=>{
      html+='<label class="filter-item"><input type="checkbox" value="'+m+'" onchange="toggleFilter(\'maten\',this.value,this.checked)"> '+m+'</label>';
    });
    html+='</div></div>';
  }

  if(kleuren.length>0){
    html+='<div class="filter-grp"><div class="filter-ttl">Kleur</div><div class="filter-body clr-body" id="fb-kleur">';
    kleuren.forEach(c=>{
      const hex=c.hex||'#ccc';
      html+='<div class="filter-clr-dot" style="background:'+hex+'" title="'+c.name+'" data-code="'+c.code+'" onclick="toggleKleurFilter(\''+c.code+'\',this)"></div>';
    });
    html+='</div></div>';
  }

  sidebar.innerHTML = html || '<p style="font-size:.75rem;color:var(--ink3)">Geen filters beschikbaar.</p>';
}

function toggleFilter(type, value, checked){
  if(checked) ACTIVE_FILTERS[type].push(value);
  else ACTIVE_FILTERS[type] = ACTIVE_FILTERS[type].filter(v=>v!==value);
  applyFilters();
}

function toggleKleurFilter(code, el){
  const idx = ACTIVE_FILTERS.kleuren.indexOf(code);
  if(idx>=0){
    ACTIVE_FILTERS.kleuren.splice(idx,1);
    el.classList.remove('active');
  } else {
    ACTIVE_FILTERS.kleuren.push(code);
    el.classList.add('active');
  }
  applyFilters();
}

function applyFilters(){
  const catId = CURRENT_CAT;
  if(!catId) return;
  let producten = MODELS[catId];
  if(!producten){console.warn('Producten niet gevonden voor catId:', catId, 'MODELS keys:', Object.keys(MODELS)); return;}
  producten = [...producten];
  const f = ACTIVE_FILTERS;

  if(f.merken.length)  producten = producten.filter(m=>f.merken.includes(m.brand));
  if(f.maten.length)   producten = producten.filter(m=>m.sizes.some(s=>f.maten.includes(s)));
  if(f.kleuren.length) producten = producten.filter(m=>m.kleuren.some(c=>f.kleuren.includes(c.code)));

  const sort = e('mdl-sort')?.value||'default';
  if(sort==='price-asc')  producten=[...producten].sort((a,b)=>calcPrijsEx(a)-calcPrijsEx(b));
  if(sort==='price-desc') producten=[...producten].sort((a,b)=>calcPrijsEx(b)-calcPrijsEx(a));
  if(sort==='name-asc')   producten=[...producten].sort((a,b)=>a.name.localeCompare(b.name));
  if(sort==='brand-asc')  producten=[...producten].sort((a,b)=>a.brand.localeCompare(b.brand));

  const cnt=e('mdl-count');
  if(cnt) cnt.textContent=producten.length+' product'+(producten.length!==1?'en':'');
  buildMdlGrid(producten);
}

function selCat(id){
  S.cat=id;S.mdl=null;S.clrId=null;S.clrName=null;S.clrHex=null;
  CURRENT_CAT=id;ACTIVE_FILTERS={merken:[],kleuren:[],maten:[]};
  document.querySelectorAll('.cat-opt').forEach(c=>c.classList.toggle('sel',c.id==='cc-'+id));
  buildFilters(id);
  applyFilters();
  s1Show('mdl');
}

function buildMdlGrid(producten){
  const g=e('mdl-grid');g.innerHTML='';
  if(!producten||producten.length===0){g.innerHTML='<div class="mdl-no-results">Geen producten gevonden voor deze filters.</div>';return;}
  producten.forEach(m=>{
    const d=document.createElement('div');d.className='opt';d.id='mc-'+m.id;
    const tags=(m.tags||[]).map(t=>'<span class="mtag'+(m.eco?' eco':m.prem?' prem':'')+'">'+t+'</span>').join('');
    const nb=m.note?'<span class="mtag">'+m.note+'</span>':'';
    const imgHtml = m.image_url
      ? '<div class="mdl-img"><img src="'+m.image_url+'" alt="'+m.name+'" loading="lazy" onerror="this.parentElement.style.display=\'none\'"></div>'
      : '';
    d.innerHTML='<div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>'
      +imgHtml
      +'<div class="mdl-brand">'+m.brand+'</div>'
      +'<div class="mdl-name">'+m.name+'</div>'
      +'<div class="mdl-sku">'+m.sku+'</div>'
      +'<div class="mdl-tags">'+tags+nb+'</div>';
    d.onclick=()=>selMdl(m);g.appendChild(d);
  });
}

function selMdl(m){
  S.mdl=m;S.clrId=null;S.clrName=null;S.clrHex=null;
  document.querySelectorAll('#mdl-grid .opt').forEach(c=>c.classList.toggle('sel',c.id==='mc-'+m.id));
  buildSwatches();s1Show('clr');
}

function buildSwatches(){
  const g=e('sw-grid');g.innerHTML='';
  const kleuren = S.mdl?.kleuren || [];
  kleuren.forEach(c=>{
    const isLight = c.hex && ['#f','#e','#d','#c','#b','#a'].some(x=>c.hex.toLowerCase().startsWith(x));
    const w=document.createElement('div');
    w.className='sw'+(isLight?' lc':'');w.id='sw-'+c.code;
    w.innerHTML='<div class="sw-circle" style="background:'+(c.hex||'#ccc')+'"></div>'
      +'<div class="sw-nm">'+c.name+'</div>';
    w.onclick=()=>selColor({id:c.code,name:c.name,hex:c.hex||null,image_url:c.image_url||null});
    g.appendChild(w);
  });
  if(kleuren.length === 0){
    g.innerHTML='<p style="font-size:.8rem;color:var(--ink3)">Kleuren worden op aanvraag bevestigd.</p>';
  }
  e('custom-row').classList.remove('sel');
  e('custom-field').classList.add('hidden');
  e('custom-inp').value='';
  e('chosen-bar').classList.add('hidden');
  const nb=e('btn-clr-next');if(nb)nb.disabled=true;
}

function selColor(c){
  S.clrId=c.id;S.clrName=c.name;S.clrHex=c.hex;S.clrImageUrl=c.image_url||null;
  document.querySelectorAll('.sw').forEach(s=>s.classList.remove('sel'));
  const sw=e('sw-'+c.id);if(sw)sw.classList.add('sel');
  e('custom-row').classList.remove('sel');
  e('custom-field').classList.add('hidden');
  showChosenBar(c.hex,c.name);
  // Update kleur-preview foto
  const prev=e('clr-preview'), img=e('clr-preview-img');
  const imgUrl = c.image_url || S.mdl?.image_url || null;
  if(prev && img && imgUrl){ img.src=imgUrl; img.alt=c.name; prev.style.display='block'; }
  else if(prev){ prev.style.display='none'; }
  const nb=e('btn-clr-next');if(nb)nb.disabled=false;
}

function selCustom(){
  document.querySelectorAll('.sw').forEach(s=>s.classList.remove('sel'));
  e('custom-row').classList.add('sel');
  e('custom-field').classList.remove('hidden');
  e('custom-inp').focus();
  S.clrId='custom';S.clrName='';S.clrHex=null;
  e('chosen-bar').classList.add('hidden');
}

function onCustomInp(){
  const v=e('custom-inp').value.trim();S.clrName=v||'Overige kleur';
  if(v) showChosenBar(null,v); else e('chosen-bar').classList.add('hidden');
}

function confirmCustom(){
  if(!e('custom-inp').value.trim()) return;
  S.clrName=e('custom-inp').value.trim();
  const nb=e('btn-clr-next');if(nb)nb.disabled=false;
}

function showChosenBar(hex,name){
  e('chosen-dot').style.background=hex||'conic-gradient(red,orange,yellow,green,blue,violet,red)';
  e('chosen-nm').textContent='Gekozen: '+name;
  e('chosen-bar').classList.remove('hidden');
}

// ── STAP 2: Positie ────────────────────────────────────────────────────────────
function selPos(p){
  S.pos=p;S.techA=null;S.techB=null;S.configuring='A';
  ['front','back','both','left','right','left-back','right-back'].forEach(x=>e('pos-'+x).classList.toggle('sel',x===p));
  e('btn2').disabled=false;
}

// ── STAP 3: Techniek ───────────────────────────────────────────────────────────
function setupStep3ForKant(kant){
  S.configuring=kant;
  const isBoth=S.pos==='both'||S.pos==='left-back'||S.pos==='right-back';
  let kantName;
  if(kant==='A'){
    if(S.pos==='back') kantName='Achterkant';
    else if(S.pos==='left'||S.pos==='left-back') kantName='Linkerborst';
    else if(S.pos==='right'||S.pos==='right-back') kantName='Rechterborst';
    else kantName='Voorkant';
  } else {
    kantName='Achterkant';
  }
  let kantAName='';
  if(S.pos==='back') kantAName='Achterkant';
  else if(S.pos==='left'||S.pos==='left-back') kantAName='Linkerborst';
  else if(S.pos==='right'||S.pos==='right-back') kantAName='Rechterborst';
  else kantAName='Voorkant';
  const alreadyA=S.techA?kantAName+': '+techName(S.techA):'';

  e('kant-ind').classList.toggle('hidden',!isBoth);
  if(isBoth){
    e('kant-lbl-txt').textContent=kantName;
    e('kant-sub-txt').textContent=kant==='B'&&alreadyA?alreadyA:'';
  }
  const ttl=isBoth?'Techniek \u2014 '+kantName:'Kies een druktechniek';
  e('s3-ttl').textContent=ttl;

  ['dtf','zeef','bord'].forEach(x=>e('tc-'+x).classList.remove('sel'));
  ['ti-dtf','ti-zeef'].forEach(x=>e(x).classList.add('hidden'));
  e('btn3').disabled=true;
}

function selTech(t){
  if(S.configuring==='A') S.techA=t; else S.techB=t;
  ['dtf','zeef','bord'].forEach(x=>e('tc-'+x).classList.toggle('sel',x===t));
  ['ti-dtf','ti-zeef'].forEach(x=>e(x).classList.add('hidden'));
  if(t!=='bord') {
    e('ti-'+t).classList.remove('hidden');
    // Update levertijden dynamically from admin settings
    if(t==='dtf' && _LT.dtf) {
      e('ti-dtf').innerHTML='<strong>DTF:</strong> Geen minimale oplage, full colour. Geschikt voor katoen, polyester en nylon. Levertijd '+_LT.dtf+' werkdagen.';
    } else if(t==='zeef' && _LT.zeefdruk) {
      e('ti-zeef').innerHTML='<strong>Zeefdruk:</strong> Maximaal 4 kleuren per ontwerp. Voordeligst bij 25+ stuks. Levertijd '+_LT.zeefdruk+' werkdagen.';
    }
  }
  e('btn3').disabled=false;
}

function techName(t){return t==='dtf'?'DTF druk':t==='zeef'?'Zeefdruk':'\u2013';}

function afterTech(){
  const tech = S.configuring==='A' ? S.techA : S.techB;
  if(tech==='bord'){ gS('bord'); return; }
  const isBoth=S.pos==='both'||S.pos==='left-back'||S.pos==='right-back';
  if(isBoth&&S.configuring==='A'){
    setupStep3ForKant('B');
  } else {
    gS(4);
  }
}

function goBackFromStep4(){
  const isBoth=S.pos==='both'||S.pos==='left-back'||S.pos==='right-back';
  if(isBoth){
    S.configuring='B';
    setupStep3ForKant('B');
    gS(3);
  } else {
    gS(3);
  }
}

function enterStep3(){
  S.techA=null;S.techB=null;S.configuring='A';
  setupStep3ForKant('A');
  gS(3);
}

// ── STAP 4: Maten ──────────────────────────────────────────────────────────────
function setupStep4(){
  const isLeft=S.pos==='left',isRight=S.pos==='right';
  const isBoth=S.pos==='both'||S.pos==='left-back'||S.pos==='right-back';
  const showFrontZeef=(S.techA==='zeef')&&(S.pos==='front'||S.pos==='both');
  const showBackZeef=(S.techA==='zeef')&&(S.pos==='back'||S.pos==='both'||S.pos==='left-back'||S.pos==='right-back')||(S.techB==='zeef')&&(S.pos==='both'||S.pos==='left-back'||S.pos==='right-back');
  const showLeftZeef=(S.techA==='zeef')&&(S.pos==='left'||S.pos==='left-back');
  const showRightZeef=(S.techA==='zeef')&&(S.pos==='right'||S.pos==='right-back');

  e('zeef-front-col').classList.toggle('hidden',!showFrontZeef);
  e('zeef-back-col').classList.toggle('hidden',!showBackZeef);
  e('zeef-left-col').classList.toggle('hidden',!showLeftZeef);
  e('zeef-right-col').classList.toggle('hidden',!showRightZeef);
  e('zeef-front-khdr').classList.toggle('hidden',!isBoth);

  buildSzTable();
  e('quote-box').style.display='none';
  e('btn4').disabled=true;
}

function buildSzTable(){
  const sizes = S.mdl?.sizes || CATS.find(c=>c.id===S.cat)?.sizes || ['XS','S','M','L','XL','2XL'];
  const tb=e('sz-tbody');tb.innerHTML='';
  for(let i=0;i<sizes.length;i+=2){
    const s1=sizes[i],s2=sizes[i+1];
    const tr=document.createElement('tr');
    tr.innerHTML='<td>'+s1+'</td><td><input class="sz-inp" type="number" min="0" value="0" data-size="'+s1+'" oninput="updQ()"></td>'
      +(s2?'<td>'+s2+'</td><td><input class="sz-inp" type="number" min="0" value="0" data-size="'+s2+'" oninput="updQ()"></td>':'<td></td><td></td>');
    tb.appendChild(tr);
  }
  S.qty=0;
}

function selZC(kant,n){
  if(kant==='front') S.zcA=n; else S.zcB=n;
  updQ();
}

function updQ(){
  let t=0;document.querySelectorAll('.sz-inp').forEach(i=>t+=Math.max(0,parseInt(i.value)||0));
  S.qty=t;calcQ();
}

function calcQ(){
  const q=S.qty;const w=e('qty-warn');w.classList.add('hidden');
  if(q===0){e('quote-box').style.display='none';e('btn4').disabled=true;return;}

  const isLeft=S.pos==='left',isRight=S.pos==='right';
  const isBoth=S.pos==='both'||S.pos==='left-back'||S.pos==='right-back';
  let upA=0,upB=0;

  const tA=S.techA;
  const posLabel=isLeft?'linkerborst':isRight?'rechterborst':'voorkant';
  if(tA==='dtf'){
    const isBorst = S.pos==='left' || S.pos==='right' || S.pos==='left-back' || S.pos==='right-back';
    const dpMatrix = isBorst ? DPborst : DP;
    const t=dpMatrix.find(x=>q>=x.min&&q<=x.max);
    if(!t){e('quote-box').style.display='none';return;}upA=t.u; // excl. BTW (admin waarden zijn excl. BTW)
  } else if(tA==='zeef'){
    if(!S.zcA){w.textContent='Kies eerst het aantal kleuren voor de '+posLabel+'.';w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
    if(q<25){w.textContent='Zeefdruk ('+posLabel+') vereist minimaal 25 stuks.';w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
    const t=ZP.find(x=>q>=x.min&&q<=x.max);upA=t?t.c[S.zcA-1]:0.82; // excl. BTW
  }

  if(isBoth){
    const tB=S.techB;
    if(tB==='dtf'){
      const t=DP.find(x=>q>=x.min&&q<=x.max);if(!t){e('quote-box').style.display='none';return;}upB=t.u; // excl. BTW
    } else if(tB==='zeef'){
      if(!S.zcB){w.textContent='Kies eerst het aantal kleuren voor de achterkant.';w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
      if(q<25){w.textContent='Zeefdruk (achterkant) vereist minimaal 25 stuks.';w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
      const t=ZP.find(x=>q>=x.min&&q<=x.max);upB=t?t.c[S.zcB-1]:0.82; // excl. BTW
    }
  }

  const ship=q>=12?13.95:6.95; // verzending incl. BTW (vaste klantprijs)
  const drukA=upA*q;
  const drukB=isBoth?upB*q:0;
  // Textiel excl. BTW — calcPrijsEx geeft al excl. BTW terug
  S.prijsEx=calcPrijsEx(S.mdl);
  const kortingPct=(['dtf','zeef'].includes(S.techA))?getTextielKorting(q):0;
  const textielExclPerStuk=parseFloat((S.prijsEx*(1-kortingPct)).toFixed(4));
  const textielTot=parseFloat((textielExclPerStuk*q).toFixed(2)); // totaal textiel excl. BTW
  // Subtotaal excl. BTW = textiel + bedrukking (ZONDER verzending)
  const subtotaalExcl=parseFloat((textielTot+drukA+drukB).toFixed(2));
  const btwBedrag=parseFloat((subtotaalExcl*0.21).toFixed(2));
  const subtotaalIncl=parseFloat((subtotaalExcl+btwBedrag).toFixed(2)); // producten incl. BTW
  const eindtotaal=parseFloat((subtotaalIncl+ship).toFixed(2));          // incl. verzending
  S.upA=upA;S.upB=upB;S.ship=ship;S.tot=eindtotaal;S.textielTot=textielTot;
  S.textielExclPerStuk=textielExclPerStuk;S.textielExclOrigineel=S.prijsEx;
  S.kortingPct=kortingPct;S.subtotaalExcl=subtotaalExcl;S.subtotaalIncl=subtotaalIncl;

  const posNm={front:'Voorkant',back:'Achterkant',both:'Beide kanten',left:'Linkerborst',right:'Rechterborst','left-back':'Linkerborst + Achterkant','right-back':'Rechterborst + Achterkant'};

  // Textiel & kleur tonen als beschikbaar
  if(S.mdl){
    e('q-textiel-nm').textContent=S.mdl.brand+' '+S.mdl.name;
    e('qr-textiel').style.display='flex';
    e('q-textiel-lbl').textContent='Textiel ('+q+'\u00d7 '+fmt(S.textielExclPerStuk)+')';
    e('q-textiel-prijs').textContent=fmt(S.textielTot);
    e('q-textiel-row').style.display='flex';
  }
  if(kortingPct>0){
    const bespaard=parseFloat(((S.textielExclOrigineel-S.textielExclPerStuk)*q).toFixed(2));
    e('q-korting-pct').textContent='\u2212'+(kortingPct*100)+'% (\u2212'+fmt(bespaard)+')';
    e('qr-korting').style.display='flex';
  } else {
    e('qr-korting').style.display='none';
  }
  if(S.clrName){
    e('q-kleur-nm').textContent=S.clrName;
    e('qr-kleur').style.display='flex';
  }

  e('q-stuks').textContent=q+' stuks';
  e('q-pos').textContent=posNm[S.pos]||'–';
  e('q-tech-a').textContent=techName(S.techA);
  e('q-up-a').textContent=fmt(upA)+' per stuk';
  let posALabel='Bedrukking';
  if(S.pos==='left'||S.pos==='left-back') posALabel='Linkerborst';
  else if(S.pos==='right'||S.pos==='right-back') posALabel='Rechterborst';
  else if(isBoth) posALabel='Voorkant';
  e('q-sep-a').textContent=posALabel;
  // Bedrukking totaal voorkant
  e('q-druk-a-lbl').textContent='Bedrukking ('+q+'\u00d7 '+fmt(upA)+')';
  e('q-druk-a').textContent=fmt(drukA);

  // Achterkant (alleen voor combinatie posities: both, left-back, right-back)
  const sepB=e('q-sep-b'),tbRow=e('q-tech-b-row'),ubRow=e('q-up-b-row'),dbRow=e('q-druk-b-row');
  if(isBoth){
    sepB.classList.remove('hidden');tbRow.classList.remove('hidden');ubRow.classList.remove('hidden');dbRow.classList.remove('hidden');
    sepB.textContent='Achterkant';
    e('q-tech-b').textContent=techName(S.techB);
    e('q-up-b').textContent=fmt(upB)+' per stuk';
    e('q-druk-b-lbl').textContent='Bedrukking ('+q+'\u00d7 '+fmt(upB)+')';
    e('q-druk-b').textContent=fmt(drukB);
  } else {
    sepB.classList.add('hidden');tbRow.classList.add('hidden');ubRow.classList.add('hidden');dbRow.classList.add('hidden');
  }
  // Vul verzending
  e('q-ship').textContent = fmt(ship);

  // Totaalblok — klasse wisselt per klanttype
  const exclRow = e('q-excl-row');
  const btwRow = e('q-btw-row');
  const inclRow = e('q-incl-row');

  if(S.klantType === 'bedrijf') {
    // Bedrijf: subtotaal excl. BTW is prominent (blauw), rest footnotes
    exclRow.className = 'sum-excl-hero';
    exclRow.innerHTML = '<span class="lbl">Subtotaal excl. BTW<small>Uw prijs als bedrijf</small></span><span class="prc">' + fmt(subtotaalExcl) + '</span>';
    btwRow.className = 'sum-total-footnote';
    btwRow.innerHTML = '<span class="lbl">BTW 21%</span><span class="prc">' + fmt(btwBedrag) + '</span>';
    inclRow.className = 'sum-total-footnote';
    inclRow.innerHTML = '<span class="lbl">Totaal incl. BTW</span><span class="prc">' + fmt(eindtotaal) + '</span>';
  } else {
    // Particulier: eindtotaal incl. BTW is prominent (oranje)
    exclRow.className = 'sum-total-sub';
    exclRow.innerHTML = '<span class="k">Subtotaal excl. BTW</span><span class="v">' + fmt(subtotaalExcl) + '</span>';
    btwRow.className = 'sum-total-sub';
    btwRow.innerHTML = '<span class="k">BTW 21%</span><span class="v">' + fmt(btwBedrag) + '</span>';
    inclRow.className = 'sum-total';
    inclRow.innerHTML = '<span class="lbl">Totaal incl. BTW</span><span class="prc">' + fmt(eindtotaal) + '</span>';
  }

  // Toggle zichtbaar maken
  e('klant-toggle-wrap').style.display='flex';
  e('quote-box').style.display='block';
  e('btn4').disabled=false;
}

// ── STAP 5: Ontwerp ────────────────────────────────────────────────────────────
function setupStep5(){
  const isBoth=S.pos==='both';
  const isBack=S.pos==='back';
  e('upload-front-lbl').textContent=isBoth?'Logo / ontwerp voorkant':isBack?'Logo / ontwerp achterkant':'Logo / ontwerp';
  e('upload-back-wrap').classList.toggle('hidden',!isBoth);

  // Check winkelwagen items
  checkWagenBanner();
}

async function checkWagenBanner(){
  try {
    const banner = e('wagen-banner');
    if(!banner) return; // Element niet gevonden - no crash

    const token = localStorage.getItem('mm_wagen_token');
    if(!token) {
      banner.style.display = 'none';
      return;
    }
    const resp = await fetch('bestellen/wagen.php?actie=laden');
    const data = await resp.json();
    if(data.ok && data.wagen && data.wagen.regels && data.wagen.regels.length > 0) {
      const count = e('wagen-banner-count');
      if(count) count.textContent = data.wagen.regels.length;
      banner.style.display = 'block';
    } else {
      banner.style.display = 'none';
    }
  } catch(err) {
    console.error('checkWagenBanner error:', err);
    const banner = e('wagen-banner');
    if(banner) banner.style.display = 'none';
  }
}

function handleUpload(side,inp){
  if(!inp.files||!inp.files[0]) return;
  const f=inp.files[0];
  if(side==='front'){e('upload-front-name').textContent='\u2713 '+f.name;e('upload-front').classList.add('has-file');}
  else if(side==='back'){e('upload-back-name').textContent='\u2713 '+f.name;e('upload-back').classList.add('has-file');}
  else if(side==='bord'){e('upload-bord-name').textContent='\u2713 '+f.name;e('upload-bord').classList.add('has-file');}
}

['upload-front','upload-back','upload-bord'].forEach(id=>{
  const el=e(id);if(!el)return;
  el.addEventListener('dragover',ev=>{ev.preventDefault();el.style.borderColor='var(--accent2)';});
  el.addEventListener('dragleave',()=>el.style.borderColor='');
  el.addEventListener('drop',ev=>{
    ev.preventDefault();el.style.borderColor='';
    const f=ev.dataTransfer.files[0];if(!f)return;
    const side=id.replace('upload-','');
    handleUpload(side,{files:[f]});
  });
});

function chk5(){
  // Stap 5 heeft geen verplichte velden meer — upload en notities zijn optioneel
  // Knop altijd enabled
}


// ── WINKELWAGEN INTEGRATIE ──────────────────────────────────────────────────
async function toevoegenAanWagen(){
  try {
    console.log('toevoegenAanWagen: START');

    // FIRST: Validate required state
    if(!S.mdl) { alert('Selecteer eerst een product bij stap 1-2.'); return; }
    if(!S.pos) { alert('Selecteer eerst een positie bij stap 4.'); return; }
    if(!S.clrId) { alert('Selecteer eerst een kleur bij stap 3.'); return; }
    if(!S.techA) { alert('Selecteer eerst een techniek bij stap 4.'); return; }

    // Herbereken qty direct uit inputs (betrouwbaarder dan S.qty state)
    let herberekendQty = 0;
    const sz = {};
    document.querySelectorAll('.sz-inp').forEach(i => {
      const v = Math.max(0, parseInt(i.value) || 0);
      if(v > 0) { sz[i.dataset.size] = v; herberekendQty += v; }
    });
    S.qty = herberekendQty;
    if(S.qty === 0) { alert('Vul eerst de hoeveelheden in bij stap 3 (maten).'); return; }

    // Build posities array (wagen.php expects {positie, kleuren})
    const posities = [];
    if(S.pos === 'front' || S.pos === 'both') posities.push({positie: 'voorkant', kleuren: S.zcA || 1});
    if(S.pos === 'back' || S.pos === 'both') posities.push({positie: 'achterkant', kleuren: S.zcB || 1});
    if(S.pos === 'left') posities.push({positie: 'linkerborst', kleuren: S.zcA || 1});
    if(S.pos === 'right') posities.push({positie: 'rechterborst', kleuren: S.zcA || 1});
    if(S.pos === 'left-back') posities.push({positie: 'linkerborst', kleuren: S.zcA || 1});
    if(S.pos === 'right-back') posities.push({positie: 'rechterborst', kleuren: S.zcA || 1});
    if(S.pos === 'left-back' || S.pos === 'right-back') posities.push({positie: 'achterkant', kleuren: S.zcB || 1});

    // Build technieken per positie (voorkant kan DTF zijn, achterkant kan zeef zijn)
    const technieken = [];
    if(S.pos === 'front' || S.pos === 'both') technieken.push({positie: 'voorkant', techniek: S.techA});
    if(S.pos === 'back' || S.pos === 'both') technieken.push({positie: 'achterkant', techniek: S.techB});
    if(S.pos === 'left') technieken.push({positie: 'linkerborst', techniek: S.techA});
    if(S.pos === 'right') technieken.push({positie: 'rechterborst', techniek: S.techA});
    if(S.pos === 'left-back') technieken.push({positie: 'linkerborst', techniek: S.techA});
    if(S.pos === 'right-back') technieken.push({positie: 'rechterborst', techniek: S.techA});
    if(S.pos === 'left-back' || S.pos === 'right-back') technieken.push({positie: 'achterkant', techniek: S.techB});

    // Build regel voor wagen.php API (safe access)
    const regel = {
      sku: S.mdl.sku || S.mdl.code || '',
      aantal: S.qty,  // NODIG: totaal aantal stuks
      techniek: S.techA,  // fallback (nog steeds nodig voor validatie)
      technieken: technieken,  // per positie
      kleur_code: S.clrId,
      kleur_naam: S.clrName,
      posities: posities,
      maten: sz,
      notitie: `${S.mdl.brand || S.mdl.merk || ''} ${S.mdl.name || S.mdl.naam || ''} | ${S.clrName}`
    };

    console.log('Sending to wagen.php:', regel);

    const wagenResp = await fetch('/bestellen/wagen.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ actie: 'toevoegen', regel })
    });

    const wagenData = await wagenResp.json();
    console.log('wagenData response:', wagenData);

    if(wagenData.ok) {
      console.log('toevoegenAanWagen: SUCCESS, regel_id:', wagenData.regel_id);
      const wagen_token = wagenData.wagen_token;
      const regel_id = wagenData.regel_id;

      // Upload bestanden indien aanwezig
      const fileFront = e('file-front').files?.[0];
      const fileBack = e('file-back').files?.[0];

      if(fileFront) {
        const fd = new FormData();
        fd.append('actie', 'upload');
        fd.append('wagen_token', wagen_token);
        fd.append('regel_id', regel_id);
        fd.append('positie', 'voorkant');
        fd.append('ontwerp', fileFront);
        try {
          const upResp = await fetch('/bestellen/wagen.php', {method: 'POST', body: fd});
          const upData = await upResp.json();
          console.log('Upload voorkant:', upData);
        } catch(e) { console.warn('Upload voorkant error:', e); }
      }

      if((S.pos === 'both' || S.pos === 'left-back' || S.pos === 'right-back') && fileBack) {
        const fd = new FormData();
        fd.append('actie', 'upload');
        fd.append('wagen_token', wagen_token);
        fd.append('regel_id', regel_id);
        fd.append('positie', 'achterkant');
        fd.append('ontwerp', fileBack);
        try {
          const upResp = await fetch('/bestellen/wagen.php', {method: 'POST', body: fd});
          const upData = await upResp.json();
          console.log('Upload achterkant:', upData);
        } catch(e) { console.warn('Upload achterkant error:', e); }
      }

      // Reset formulier maar NOT de stap
      S.cat = null; S.mdl = null; S.clrId = null; S.clrName = null; S.clrHex = null;
      S.pos = null; S.techA = null; S.techB = null; S.qty = 0;
      document.querySelectorAll('.sz-inp').forEach(i => i.value = 0);
      e('file-front').value = ''; e('file-back').value = '';
      e('upload-front').classList.remove('has-file');
      e('upload-back').classList.remove('has-file');
      e('upload-front-name').textContent = ''; e('upload-back-name').textContent = '';

      // Ververs cart-paneel en ga terug naar stap 1
      await verversCartPanel();
      gS(1);
    } else {
      alert('Fout: ' + (wagenData.fout || 'onbekend'));
    }
  } catch(err) {
    console.error('toevoegenAanWagen error:', err);
    alert('Er is iets misgegaan. Probeer opnieuw.');
  }
}

// Sla wagen_token op zodat verwijderen werkt
let _wagenToken = null;

// Ververs het winkelwagen-paneel rechts
async function verversCartPanel() {
  try {
    const resp = await fetch('/bestellen/wagen.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ actie: 'laden' })
    });
    const cartData = await resp.json();

    if(!cartData.ok || !cartData.regels || cartData.regels.length === 0) {
      e('cart-panel-items').innerHTML = '<div style="text-align:center;padding:2rem 1rem;color:var(--ink3);font-size:.82rem;">Winkelwagen is leeg.</div>';
      e('cart-panel-footer').style.display = 'none';
      e('cart-panel-count').textContent = '';
      return;
    }

    _wagenToken = cartData.wagen_token;

    let html = '';
    cartData.regels.forEach(regel => {
      const matenStr = regel.maten ? Object.entries(regel.maten).map(([m,q]) => `${m}×${q}`).join(' ') : '–';
      const positieStr = regel.posities ? regel.posities.map(p => p.positie || '–').join(', ') : '–';
      const uploads = regel.uploads || [];
      const technieken = regel.technieken || [];
      const aantalStr = regel.aantal ? `${regel.aantal}× ` : '';
      const prijs = regel.prijs?.totaal_excl || 0;

      // Build uploads display per positie
      let uploadsHTML = '';
      if(uploads.length > 0) {
        uploads.forEach(up => {
          const posLabel = up.positie === 'achterkant' ? 'Ontwerp achterkant' : 'Ontwerp voorkant';
          uploadsHTML += `
          <div style="font-weight:700;color:var(--ink);">${posLabel}</div>
          <div style="color:var(--success);font-weight:700;">${up.bestandsnaam}</div>`;
        });
      }

      // Build technieken display per positie
      let techniekHTML = '';
      if(technieken.length > 0) {
        technieken.forEach(t => {
          const posLabel = t.positie === 'achterkant' ? 'Techniek achterkant' : 'Techniek voorkant';
          techniekHTML += `
          <div style="font-weight:700;color:var(--ink);">${posLabel}</div>
          <div>${t.techniek || '–'}</div>`;
        });
      } else {
        // Fallback als technieken array leeg is (oude data)
        techniekHTML = `
          <div style="font-weight:700;color:var(--ink);">Techniek</div>
          <div>${regel.techniek || '–'}</div>`;
      }

      html += `<div style="border:1px solid var(--border);border-radius:8px;padding:.85rem;margin-bottom:.8rem;background:var(--surface);transition:background .2s,box-shadow .2s;cursor:default;" onmouseenter="this.style.backgroundColor='#fafaf8';this.style.boxShadow='0 2px 8px rgba(0,0,0,.08)';" onmouseleave="this.style.backgroundColor='var(--surface)';this.style.boxShadow='none';">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.25rem;margin-bottom:.6rem;">
          <div style="font-weight:600;font-size:.85rem;color:var(--ink);">${aantalStr}${regel.product_naam || 'Product'}</div>
          <button onclick="verwijderUitCart('${regel.id}')" title="Verwijderen" style="background:none;border:none;cursor:pointer;color:var(--ink3);font-size:1.1rem;line-height:1;padding:.25rem .35rem;flex-shrink:0;transition:color .2s,transform .2s;border-radius:4px;" onmouseenter="this.style.color='var(--accent)';this.style.transform='scale(1.15)';" onmouseleave="this.style.color='var(--ink3)';this.style.transform='scale(1)';">&times;</button>
        </div>
        <div style="display:grid;grid-template-columns:75px 1fr;gap:.35rem .65rem;font-size:.73rem;color:var(--ink3);line-height:1.5;">
          <div style="font-weight:700;color:var(--ink);">SKU</div>
          <div>${regel.sku || '–'}</div>

          <div style="font-weight:700;color:var(--ink);">Kleur</div>
          <div>${regel.kleur_naam || '–'}</div>

          <div style="font-weight:700;color:var(--ink);">Positie</div>
          <div>${positieStr}</div>

          <div style="font-weight:700;color:var(--ink);">Maten</div>
          <div>${matenStr}</div>

          ${techniekHTML}

          ${uploadsHTML}
        </div>
        <div style="margin-top:.5rem;font-size:.8rem;font-weight:700;color:var(--accent);">€${prijs.toFixed(2).replace('.',',')} excl. BTW</div>
      </div>`;
    });

    e('cart-panel-items').innerHTML = html;
    const tot = cartData.regels.length;
    e('cart-panel-count').textContent = `${tot} ${tot === 1 ? 'item' : 'items'}`;

    const t = cartData.totalen || {};
    const fmtE = v => '€' + (v || 0).toFixed(2).replace('.', ',');
    e('cart-panel-excl').textContent    = fmtE(t.totaal_excl);
    e('cart-panel-btw').textContent     = fmtE(t.btw);
    e('cart-panel-verzend').textContent = fmtE(t.verzend_incl);
    e('cart-panel-totaal').textContent  = fmtE(t.totaal_met_verzend);
    e('cart-panel-footer').style.display = 'block';

  } catch(err) {
    console.error('verversCartPanel error:', err);
  }
}

// Verwijder item uit winkelwagen
async function verwijderUitCart(regelId) {
  try {
    const resp = await fetch('/bestellen/wagen.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ actie: 'verwijderen', wagen_token: _wagenToken, regel_id: regelId })
    });
    const data = await resp.json();
    if(data.ok) await verversCartPanel();
  } catch(err) {
    console.error('verwijderUitCart error:', err);
  }
}

// Nieuw product toevoegen — reset en terug naar stap 1
function nieuweProductToevoegen() {
  gS(1);
}

// Legacy stub (niet meer nodig maar voorkomt JS errors op oude referenties)
function sluitWagenSidebar() {
}

// ── NOTIFICATIE SYSTEEM ──────────────────────────────────────────────────────
function showNotif(msg) {
  let notif = e('notif-toast');
  if(!notif) {
    notif = document.createElement('div');
    notif.id = 'notif-toast';
    notif.style.cssText = 'position:fixed;top:20px;right:20px;background:#2a9d5c;color:#fff;padding:16px 24px;border-radius:8px;font-size:.9rem;z-index:9999;max-width:300px;box-shadow:0 4px 12px rgba(0,0,0,.15);';
    document.body.appendChild(notif);
  }
  notif.textContent = msg;
  notif.style.display = 'block';
  setTimeout(() => notif.style.display = 'none', 3000);
}

async function sendOfferte(){
  const btn=e('btn-offerte-submit');
  btn.disabled=true;btn.textContent='Bezig...';
  const isBoth=S.pos==='both';
  const sz={};document.querySelectorAll('.sz-inp').forEach(i=>{if(parseInt(i.value)>0)sz[i.dataset.size]=i.value;});
  const payload={
    action:'offerte',
    textiel:S.mdl?S.mdl.brand+' '+S.mdl.name:'',
    sku:S.mdl?.sku||'',
    kleur:S.clrName||'',
    positie:{front:'Voorkant',back:'Achterkant',both:'Beide kanten'}[S.pos]||'',
    techniek_a:S.techA||'',
    techniek_b:isBoth?(S.techB||''):'',
    zeefkleuren_a:S.zcA,
    zeefkleuren_b:isBoth?S.zcB:0,
    maten:sz,
    aantal:S.qty,
    prijs_indicatie:fmt(S.tot),
    naam:e('fname').value.trim()+' '+e('lname').value.trim(),
    email:e('email').value.trim(),
    telefoon:e('phone').value.trim()||'\u2013',
    bedrijf:e('company').value.trim()||'\u2013',
    kvk:e('kvk').value.trim()||'\u2013',
    straat:e('street').value.trim(),
    postcode:e('zip').value.trim(),
    stad:e('city').value.trim(),
    opmerkingen:e('notes').value.trim()||'\u2013',
  };
  try{
    await fetch(HANDLER_URL,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
  } catch(err){console.error('Offerte fout:',err);}
  e('confirm-email').textContent=payload.email;
  e('ok-title').textContent='Offerte aanvraag ontvangen!';
  e('ok-msg').innerHTML='Bedankt voor je aanvraag. We nemen binnen 1\u20132 werkdagen contact op via <strong>'+payload.email+'</strong>.<br><br>Vragen? WhatsApp: <a href="https://wa.me/31617255170" style="color:var(--accent)">+31 6 17 25 51 70</a>';
  gS('success');
}

// ── Order handler ──────────────────────────────────────────────────────────────

// ── Borduren aanvraag ──────────────────────────────────────────────────────────
function chkBord(){
  const ok=['bfname','blname','bemail'].every(id=>e(id).value.trim().length>0);
  e('btn-bord').disabled=!ok;
}

async function sendBordAanvraag(){
  const btn=e('btn-bord');
  btn.innerHTML='<span class="spinner"></span> Versturen...';
  btn.disabled=true;

  let uploadUrl = null;
  const fileBord = e('file-bord')?.files[0];
  if(fileBord){
    const fd=new FormData();fd.append('action','upload');fd.append('bestand',fileBord);fd.append('folder','borduren');
    try{const r=await fetch(HANDLER_URL,{method:'POST',body:fd});const d=await r.json();if(d.success)uploadUrl=d.url;}
    catch(err){console.warn('Upload borduur mislukt:',err);}
  }

  const payload = {
    action: 'borduur',
    naam: e('bfname').value.trim()+' '+e('blname').value.trim(),
    email: e('bemail').value.trim(),
    telefoon: e('bphone').value.trim()||'\u2013',
    bedrijf: e('bcompany').value.trim()||'\u2013',
    adres: e('baddress').value.trim()||'\u2013',
    postcode: e('bzip').value.trim()||'\u2013',
    stad: e('bcity').value.trim()||'\u2013',
    kvk: e('bkvk').value.trim()||'\u2013',
    btw: e('bbtw').value.trim()||'\u2013',
    opmerkingen: e('bord-notes').value.trim()||'\u2013',
    upload_url: uploadUrl,
    taal: 'nl'
  };

  try {
    await fetch(HANDLER_URL, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
  } catch(err){ console.error('Borduur handler fout:',err); }

  e('confirm-email').textContent=e('bemail').value.trim();
  e('ok-title').textContent='Aanvraag ontvangen!';
  e('ok-msg').innerHTML='Bedankt voor je borduurwens. We nemen binnen 1\u20132 werkdagen contact op via <strong>'+e('bemail').value.trim()+'</strong>.<br><br>Vragen? WhatsApp: <a href="https://wa.me/31617255170" style="color:var(--accent)">+31 6 17 25 51 70</a>';
  gS('success');
}

// ── Checkout Navigation ─────────────────────────────────────────────────────────
function goToCheckout(){
  if(!_wagenToken){
    alert('Winkelwagen is leeg. Voeg eerst producten toe.');
    return;
  }
  // Pass wagen_token in URL for checkout.php to load the cart
  window.location.href = '/bestellen/checkout.php?wagen_token=' + encodeURIComponent(_wagenToken);
}

// ── Reset ──────────────────────────────────────────────────────────────────────
function doReset(){
  Object.assign(S,{cat:null,mdl:null,clrId:null,clrName:null,clrHex:null,pos:null,techA:null,techB:null,zcA:1,zcB:1,configuring:'A',qty:0,upA:0,upB:0,ship:0,tot:0,prijsEx:0});
  ['fname','lname','email','phone','company','street','zip','city','notes',
   'bfname','blname','bemail','bphone','bcompany','bord-notes'].forEach(id=>{const el=e(id);if(el)el.value='';});
  ['upload-front','upload-back','upload-bord'].forEach(id=>{
    const el=e(id);if(el)el.classList.remove('has-file');
  });
  ['upload-front-name','upload-back-name','upload-bord-name'].forEach(id=>{const el=e(id);if(el)el.textContent='';});
  ['file-front','file-back','file-bord'].forEach(id=>{const el=e(id);if(el)el.value='';});
  e('btn2').disabled=true;
  ['front','back','both'].forEach(x=>e('pos-'+x).classList.remove('sel'));
  buildCatGrid();s1Show('cat');gS(1);
}

// ── Init ───────────────────────────────────────────────────────────────────────
async function init(){
  await loadCatalogus();
  buildCatGrid();
  s1Show('cat');
  gS(1);
  verversCartPanel(); // laad bestaande winkelwagen bij pagina-open
}
init();

// Check if returning from checkout with success parameter
const urlParams = new URLSearchParams(window.location.search);
if(urlParams.has('success')){
  gS('success');
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
