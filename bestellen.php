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
.besteltool-wrap .shell{max-width:780px;margin:0 auto;}

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
.btn{display:inline-flex;align-items:center;gap:.45rem;padding:.75rem 1.4rem;border-radius:var(--r);font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;cursor:pointer;border:none;transition:transform .15s,box-shadow .2s,background .2s;letter-spacing:.01em;}
.btn:hover:not(:disabled){transform:translateY(-1px);}
.btn-p{background:var(--accent);color:#fff;box-shadow:0 3px 12px rgba(232,76,30,.28);}
.btn-p:hover:not(:disabled){background:#d03d10;}
.btn-p:disabled{background:#d8d4cc;color:#a09c94;box-shadow:none;cursor:not-allowed;transform:none;}
.btn-s{background:transparent;color:var(--ink2);border:2px solid var(--border);}
.btn-s:hover{border-color:#bbb;}
.btn-pp{background:#0070ba;color:#fff;box-shadow:0 3px 12px rgba(0,112,186,.28);font-size:.95rem;padding:.88rem 1.8rem;width:100%;justify-content:center;}
.btn-pp:hover{background:#005ea0;}
.btn-aanvraag{background:var(--ink2);color:#fff;box-shadow:0 3px 12px rgba(0,0,0,.15);font-size:.95rem;padding:.88rem 1.8rem;width:100%;justify-content:center;}
.btn-aanvraag:hover{background:var(--ink);}
.btn-row{display:flex;gap:.7rem;align-items:center;flex-wrap:wrap;}

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
#kt-particulier.act{background:var(--surface);color:var(--ink);box-shadow:0 1px 4px rgba(0,0,0,.1);}
#kt-bedrijf.act{background:#1a3a5c;color:#fff;box-shadow:0 1px 6px rgba(26,58,92,.4);}

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
  <div class="opt-grid g3">
    <div class="opt" id="pos-front" onclick="selPos('front')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">&#128085;</div>
      <div class="pos-name">Voorkant</div>
      <div class="pos-desc">Bedrukking op de voorzijde van het textiel</div>
    </div>
    <div class="opt" id="pos-back" onclick="selPos('back')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">&#128260;</div>
      <div class="pos-name">Achterkant</div>
      <div class="pos-desc">Bedrukking op de achterzijde van het textiel</div>
    </div>
    <div class="opt" id="pos-both" onclick="selPos('both')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">&#10024;</div>
      <div class="pos-name">Beide kanten</div>
      <div class="pos-desc">Voor- &eacute;n achterkant bedrukt. Per kant eigen techniek en ontwerp.</div>
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
      <div class="tc-icon">&#127912;</div>
      <div class="tc-name">DTF druk</div>
      <div class="tc-desc">Full colour, scherpe details. Foto's en complexe ontwerpen.</div>
      <span class="tc-badge green">Vanaf 1 stuk</span>
    </div>
    <div class="opt" id="tc-zeef" onclick="selTech('zeef')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="tc-icon">&#128424;&#65039;</div>
      <div class="tc-name">Zeefdruk</div>
      <div class="tc-desc">Traditionele techniek, levendig en duurzaam. Grote oplages.</div>
      <span class="tc-badge orange">Vanaf 25 stuks</span>
    </div>
    <div class="opt" id="tc-bord" onclick="selTech('bord')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="tc-icon">&#129521;</div>
      <div class="tc-name">Borduren</div>
      <div class="tc-desc">Premium uitstraling op polo's, caps en jassen.</div>
      <div style="margin-top:.5rem"><span class="badge" style="background:#f3ede3;color:#7a5c3a;font-size:.68rem;padding:.25rem .55rem;border-radius:20px;font-weight:600">Op aanvraag</span></div>
    </div>
  </div>

  <div id="ti-dtf" class="info-note hidden"><strong>DTF:</strong> Geen minimale oplage, full colour. Geschikt voor katoen, polyester en nylon. Levertijd 3&ndash;5 werkdagen.</div>
  <div id="ti-zeef" class="info-note hidden"><strong>Zeefdruk:</strong> Maximaal 4 kleuren per ontwerp. Voordeligst bij 25+ stuks. Levertijd 5&ndash;8 werkdagen.</div>


  <div class="btn-row">
    <button class="btn btn-s" onclick="gS(2)">&#8592; Terug</button>
    <button class="btn btn-p" id="btn3" onclick="afterTech()" disabled>Volgende stap &#8594;</button>
  </div>
</div>

<!-- STAP 4: MATEN & AANTALLEN -->
<div id="step4" class="hidden">
  <div class="s-lbl">Stap 4 van 6</div>
  <div class="s-ttl">Maten &amp; aantallen</div>

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
    <div class="sum-row"><span class="k">Verzending</span><span class="v" id="q-ship">&ndash;</span></div>
    <!-- Totaal blok — klasse wisselt per klanttype via JS -->
    <div id="q-excl-row" class="sum-total-sub"><span class="k">Subtotaal excl. BTW</span><span class="v" id="q-total-excl">&ndash;</span></div>
    <div id="q-btw-row" class="sum-total-sub"><span class="k">BTW 21%</span><span class="v" id="q-btw">&ndash;</span></div>
    <div id="q-incl-row" class="sum-total"><span class="lbl">Totaal incl. BTW</span><span class="prc" id="q-total">&ndash;</span></div>
  </div>

  <div class="btn-row">
    <button class="btn btn-s" onclick="goBackFromStep4()">&#8592; Terug</button>
    <button class="btn btn-p" id="btn4" onclick="gS(5)" disabled>Volgende stap &#8594;</button>
  </div>
</div>

<!-- STAP 5: ONTWERP & GEGEVENS -->
<div id="step5" class="hidden">
  <div class="s-lbl">Stap 5 van 6</div>
  <div class="s-ttl">Jouw ontwerp &amp; gegevens</div>

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
  <hr class="divider">
  <div class="field-row">
    <div class="field"><label>Voornaam *</label><input type="text" id="fname" placeholder="Jan" oninput="chk5()"></div>
    <div class="field"><label>Achternaam *</label><input type="text" id="lname" placeholder="de Vries" oninput="chk5()"></div>
  </div>
  <div class="field"><label>E-mailadres *</label><input type="email" id="email" placeholder="jan@bedrijf.nl" oninput="chk5()"></div>
  <div class="field"><label>Telefoonnummer</label><input type="tel" id="phone" placeholder="+31 6 12345678"></div>
  <div class="field"><label>Bedrijfsnaam (optioneel)</label><input type="text" id="company" placeholder="Bedrijf BV"></div>
  <hr class="divider">
  <div class="field-row">
    <div class="field"><label>Straat + huisnummer *</label><input type="text" id="street" placeholder="Hoofdstraat 1" oninput="chk5()"></div>
    <div class="field"><label>Postcode *</label><input type="text" id="zip" placeholder="1234 AB" oninput="chk5()"></div>
  </div>
  <div class="field-row">
    <div class="field"><label>Plaats *</label><input type="text" id="city" placeholder="Amsterdam" oninput="chk5()"></div>
    <div class="field"><label>Land</label><select id="country"><option value="NL" selected>Nederland</option><option value="BE">Belgi&euml;</option><option value="DE">Duitsland</option><option value="other">Anders</option></select></div>
  </div>
  <div class="info-note" style="margin-bottom:.75rem">
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.82rem;">
      <input type="checkbox" id="offerte-toggle" onchange="toggleOfferte()" style="width:16px;height:16px;accent-color:var(--accent);flex-shrink:0;">
      <span>Ik wil eerst een <strong>offerte aanvragen</strong> &mdash; handig voor bedrijven &amp; grote orders</span>
    </label>
  </div>
  <div class="btn-row">
    <button class="btn btn-s" onclick="gS(4)">&#8592; Terug</button>
    <button class="btn btn-p" id="btn5" onclick="gS(6)" disabled>Naar betaling &#8594;</button>
  </div>
</div>

<!-- STAP 6: BETALING -->
<div id="step6" class="hidden">
  <div class="s-lbl">Stap 6 van 6</div>
  <div class="s-ttl">Controleer &amp; betaal</div>

  <div class="sum-card">
    <div class="sum-hd">Jouw bestelling</div>
    <div class="sum-row"><span class="k">Textiel</span><span class="v" id="s-textiel">&ndash;</span></div>
    <div class="sum-row"><span class="k">Kleur</span><span class="v" id="s-kleur">&ndash;</span></div>
    <div class="sum-row"><span class="k">Drukpositie</span><span class="v" id="s-positie">&ndash;</span></div>
    <div class="sum-row"><span class="k">Totaal stuks</span><span class="v" id="s-qty">&ndash;</span></div>
    <div class="sum-row"><span class="k">Aantallen per maat</span><span class="v" id="s-maten">&ndash;</span></div>
    <!-- Kant A -->
    <div class="sum-sep" id="sum-sep-a">Voorkant</div>
    <div class="sum-row"><span class="k">Techniek</span><span class="v" id="s-tech-a">&ndash;</span></div>
    <div class="sum-row" id="sum-kleur-a-row"><span class="k">Kleuren</span><span class="v" id="s-kleur-a">&ndash;</span></div>
    <div class="sum-row"><span class="k">Prijs per stuk</span><span class="v" id="s-up-a">&ndash;</span></div>
    <div class="sum-row" id="sum-druk-a-row"><span class="k" id="sum-druk-a-lbl">Bedrukking</span><span class="v" id="s-druk-a">&ndash;</span></div>
    <!-- Kant B (only both) -->
    <div class="sum-sep hidden" id="sum-sep-b">Achterkant</div>
    <div class="sum-row hidden" id="sum-tech-b-row"><span class="k">Techniek</span><span class="v" id="s-tech-b">&ndash;</span></div>
    <div class="sum-row hidden" id="sum-kleur-b-row"><span class="k">Kleuren</span><span class="v" id="s-kleur-b">&ndash;</span></div>
    <div class="sum-row hidden" id="sum-up-b-row"><span class="k">Prijs per stuk</span><span class="v" id="s-up-b">&ndash;</span></div>
    <div class="sum-row hidden" id="sum-druk-b-row"><span class="k" id="sum-druk-b-lbl">Bedrukking</span><span class="v" id="s-druk-b">&ndash;</span></div>
    <!-- Totalen -->
    <hr style="border:none;border-top:1px solid var(--border)">
    <div class="sum-row" id="sum-textiel-row"><span class="k" id="sum-textiel-lbl">Textiel</span><span class="v" id="s-textiel-prijs">&ndash;</span></div>
    <div class="sum-row" id="sum-korting-row" style="display:none"><span class="k">Staffelkorting textiel</span><span class="v" id="s-korting-pct" style="color:#2a9d5c;font-weight:700">&ndash;</span></div>
    <div class="sum-row"><span class="k">Verzending</span><span class="v" id="s-ship">&ndash;</span></div>
    <!-- Totaal blok — klasse wisselt per klanttype via JS -->
    <div id="s-excl-row" class="sum-total-sub"><span class="k">Subtotaal excl. BTW</span><span class="v" id="s-total-excl">&ndash;</span></div>
    <div id="s-btw-row" class="sum-total-sub"><span class="k">BTW 21%</span><span class="v" id="s-btw">&ndash;</span></div>
    <div id="s-incl-row" class="sum-total"><span class="lbl">Totaal incl. BTW</span><span class="prc" id="s-total">&ndash;</span></div>
  </div>

  <div class="sum-card" style="margin-bottom:1.35rem">
    <div class="sum-hd">Bezorgadres</div>
    <div class="sum-row"><span class="k">Naam</span><span class="v" id="s-naam">&ndash;</span></div>
    <div class="sum-row"><span class="k">Adres</span><span class="v" id="s-adres">&ndash;</span></div>
    <div class="sum-row"><span class="k">E-mail</span><span class="v" id="s-email-sum">&ndash;</span></div>
  </div>

  <div class="info-note" id="betaling-note">Na betaling ontvang je een orderbevestiging per e-mail. We nemen contact op als we vragen hebben over je ontwerp.</div>
  <div class="info-note blue hidden" id="offerte-note">Na je aanvraag nemen we binnen 1&ndash;2 werkdagen contact op met een offerte op maat.</div>
  <div id="pp-container" style="margin-bottom:1rem"></div>
  <button class="btn btn-pp" id="fallback-pay" onclick="simPay()">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    Betalen met PayPal
  </button>
  <div style="text-align:center;margin-top:.65rem;font-size:.72rem;color:var(--ink3)" id="paypal-note">&#128274; Beveiligde betaling via PayPal &middot; 21% BTW inbegrepen</div>
  <button class="btn btn-aanvraag hidden" id="btn-offerte-submit" onclick="sendOfferte()" style="width:100%;justify-content:center;margin-bottom:1rem">
    Offerte aanvragen &#8594;
  </button>
  <div class="btn-row" style="margin-top:1rem"><button class="btn btn-s" onclick="gS(5)">&#8592; Terug</button></div>
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

// DTF: bouw DP array uit admin data (verplicht — geen fallback)
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

const MARKUP = {budget:1.50, standaard:1.65, premium:1.80};

function calcPrijsEx(mdl) {
  if (!mdl || !mdl.inkoop) return 2.50;
  const factor = MARKUP[mdl.tier] || MARKUP.standaard;
  return parseFloat((mdl.inkoop * factor / 1.21).toFixed(4));
}

function getTextielKorting(qty) {
  if (qty >= 200) return 0.20;
  if (qty >= 100) return 0.10;
  if (qty >= 50)  return 0.05;
  return 0;
}

// ── Klanttype ─────────────────────────────────────────────────────────────────
function setKlantType(type) {
  S.klantType = type;
  e('kt-particulier').classList.toggle('act', type==='particulier');
  e('kt-bedrijf').classList.toggle('act', type==='bedrijf');
  // Herbereken weergave zonder herberekening van prijzen
  updBtwDisplay();
  // Als stap 6 al gevuld is: herteken BTW-blok
  const step6Active = e('step6') && !e('step6').classList.contains('hidden');
  if(step6Active) fillSum();
}

function updBtwDisplay() {
  const isBedrijf = S.klantType === 'bedrijf';

  function applyBtwStyle(exclId, btwId, inclId, exclValId) {
    const exclEl=e(exclId), btwEl=e(btwId), inclEl=e(inclId);
    if(!exclEl) return;

    if(isBedrijf) {
      // Excl. BTW = hero (donkerblauw groot blok)
      exclEl.className = 'sum-excl-hero';
      // Label aanpassen naar "bedrijfsstijl" met subtitel
      exclEl.innerHTML =
        '<span class="lbl">Totaal excl. BTW<small>Uw prijs als bedrijf</small></span>' +
        '<span class="prc" id="'+exclValId+'">'+e(exclValId).textContent+'</span>';
      // BTW rij — normaal zichtbaar
      btwEl.className = 'sum-total-sub';
      btwEl.querySelector('.k').style.color = '';
      // Incl. BTW — voetnoot
      inclEl.className = 'sum-total-footnote';
      const lbl=inclEl.querySelector('.lbl,.k');
      if(lbl) lbl.textContent='Totaal incl. 21% BTW';
    } else {
      // Incl. BTW = hero (oranje)
      exclEl.className = 'sum-total-sub';
      exclEl.innerHTML =
        '<span class="k">Subtotaal excl. BTW</span>' +
        '<span class="v" id="'+exclValId+'">'+e(exclValId).textContent+'</span>';
      btwEl.className = 'sum-total-sub';
      inclEl.className = 'sum-total';
      const lbl2=inclEl.querySelector('.lbl,.k');
      if(lbl2) lbl2.textContent='Totaal incl. BTW';
    }
  }

  applyBtwStyle('q-excl-row','q-btw-row','q-incl-row','q-total-excl');
  applyBtwStyle('s-excl-row','s-btw-row','s-incl-row','s-total-excl');

  // BTW-nummer veld benadrukken bij bedrijf
  const btwVeld = e('bbtw')?.closest('.field');
  if(btwVeld) {
    btwVeld.style.outline = isBedrijf ? '2px solid var(--accent2)' : '';
    btwVeld.style.borderRadius = isBedrijf ? '6px' : '';
    btwVeld.style.padding = isBedrijf ? '4px' : '';
  }
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
function allSteps(){return ['step1','step2','step3','step4','step5','step6','step-bord','success'];}

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
    if(n===6) fillSum();
  }
  window.scrollTo({top:0,behavior:'smooth'});
}

function updProg(n){
  for(let i=1;i<=6;i++){
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
  S.clrId=c.id;S.clrName=c.name;S.clrHex=c.hex;
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
  ['front','back','both'].forEach(x=>e('pos-'+x).classList.toggle('sel',x===p));
  e('btn2').disabled=false;
}

// ── STAP 3: Techniek ───────────────────────────────────────────────────────────
function setupStep3ForKant(kant){
  S.configuring=kant;
  const isBoth=S.pos==='both';
  const kantName=kant==='A'?(S.pos==='back'?'Achterkant':'Voorkant'):'Achterkant';
  const alreadyA=S.techA?'Voorkant: '+techName(S.techA):'';

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
  if(t!=='bord') e('ti-'+t).classList.remove('hidden');
  e('btn3').disabled=false;
}

function techName(t){return t==='dtf'?'DTF druk':t==='zeef'?'Zeefdruk':'\u2013';}

function afterTech(){
  const tech = S.configuring==='A' ? S.techA : S.techB;
  if(tech==='bord'){ gS('bord'); return; }
  if(S.pos==='both'&&S.configuring==='A'){
    setupStep3ForKant('B');
  } else {
    gS(4);
  }
}

function goBackFromStep4(){
  if(S.pos==='both'){
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
  const isBoth=S.pos==='both';
  const showFrontZeef=(S.techA==='zeef');
  const showBackZeef=isBoth&&(S.techB==='zeef');

  e('zeef-front-col').classList.toggle('hidden',!showFrontZeef);
  e('zeef-back-col').classList.toggle('hidden',!showBackZeef);
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

  const isBoth=S.pos==='both';
  let upA=0,upB=0;

  const tA=S.techA;
  if(tA==='dtf'){
    const t=DP.find(x=>q>=x.min&&q<=x.max);
    if(!t){e('quote-box').style.display='none';return;}upA=t.u;
  } else if(tA==='zeef'){
    if(q<25){w.textContent='Zeefdruk (voorkant) vereist minimaal 25 stuks.';w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
    const t=ZP.find(x=>q>=x.min&&q<=x.max);upA=t?t.c[S.zcA-1]:0.82;
  }

  if(isBoth){
    const tB=S.techB;
    if(tB==='dtf'){
      const t=DP.find(x=>q>=x.min&&q<=x.max);if(!t){e('quote-box').style.display='none';return;}upB=t.u;
    } else if(tB==='zeef'){
      if(q<25){w.textContent='Zeefdruk (achterkant) vereist minimaal 25 stuks.';w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
      const t=ZP.find(x=>q>=x.min&&q<=x.max);upB=t?t.c[S.zcB-1]:0.82;
    }
  }

  const ship=q>=12?13.95:6.95;
  const drukA=upA*q;
  const drukB=isBoth?upB*q:0;
  S.prijsEx=calcPrijsEx(S.mdl);
  const kortingPct=(['dtf','zeef'].includes(S.techA))?getTextielKorting(q):0;
  const textielInclBtwOrigineel=parseFloat((S.prijsEx*1.21).toFixed(2));
  const textielInclBtw=parseFloat((textielInclBtwOrigineel*(1-kortingPct)).toFixed(2));
  const textielTot=parseFloat((textielInclBtw*q).toFixed(2));
  const tot=textielTot+drukA+drukB+ship;
  S.upA=upA;S.upB=upB;S.ship=ship;S.tot=tot;S.textielTot=textielTot;S.textielInclBtw=textielInclBtw;
  S.kortingPct=kortingPct;S.textielInclBtwOrigineel=textielInclBtwOrigineel;

  const posNm={front:'Voorkant',back:'Achterkant',both:'Beide kanten'};

  // Textiel & kleur tonen als beschikbaar
  if(S.mdl){
    e('q-textiel-nm').textContent=S.mdl.brand+' '+S.mdl.name;
    e('qr-textiel').style.display='flex';
    e('q-textiel-lbl').textContent='Textiel ('+q+'\u00d7 '+fmt(S.textielInclBtw)+')';
    e('q-textiel-prijs').textContent=fmt(S.textielTot);
    e('q-textiel-row').style.display='flex';
  }
  if(kortingPct>0){
    const bespaard=parseFloat(((S.textielInclBtwOrigineel-S.textielInclBtw)*q).toFixed(2));
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
  e('q-sep-a').textContent=isBoth?'Voorkant':'Bedrukking';
  // Bedrukking totaal voorkant
  e('q-druk-a-lbl').textContent='Bedrukking ('+q+'\u00d7 '+fmt(upA)+')';
  e('q-druk-a').textContent=fmt(drukA);

  // Achterkant
  const sepB=e('q-sep-b'),tbRow=e('q-tech-b-row'),ubRow=e('q-up-b-row'),dbRow=e('q-druk-b-row');
  if(isBoth){
    sepB.classList.remove('hidden');tbRow.classList.remove('hidden');ubRow.classList.remove('hidden');dbRow.classList.remove('hidden');
    e('q-tech-b').textContent=techName(S.techB);
    e('q-up-b').textContent=fmt(upB)+' per stuk';
    e('q-druk-b-lbl').textContent='Bedrukking ('+q+'\u00d7 '+fmt(upB)+')';
    e('q-druk-b').textContent=fmt(drukB);
  } else {
    sepB.classList.add('hidden');tbRow.classList.add('hidden');ubRow.classList.add('hidden');dbRow.classList.add('hidden');
  }
  e('q-ship').textContent=fmt(ship);
  // BTW uitsplitsing
  const totExcl=parseFloat((tot/1.21).toFixed(2));
  const btwBedrag=parseFloat((tot-totExcl).toFixed(2));
  e('q-total-excl').textContent=fmt(totExcl);
  e('q-btw').textContent=fmt(btwBedrag);
  e('q-total').textContent=fmt(tot);
  // Toggle zichtbaar maken
  e('klant-toggle-wrap').style.display='flex';
  updBtwDisplay();
  e('quote-box').style.display='block';
  e('btn4').disabled=false;
}

// ── STAP 5: Ontwerp ────────────────────────────────────────────────────────────
function setupStep5(){
  const isBoth=S.pos==='both';
  const isBack=S.pos==='back';
  e('upload-front-lbl').textContent=isBoth?'Logo / ontwerp voorkant':isBack?'Logo / ontwerp achterkant':'Logo / ontwerp';
  e('upload-back-wrap').classList.toggle('hidden',!isBoth);
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
  const fields=['fname','lname','email','street','city'].every(id=>e(id).value.trim().length>0);
  const zip=e('zip').value.trim();
  const country=e('country').value;
  const zipOk = country!=='NL' ? zip.length>0 : /^\d{4}\s?[A-Z]{2}$/i.test(zip);
  e('zip').style.borderColor = zip.length>0 && !zipOk ? 'var(--acc-warn,#e74c3c)' : '';
  e('btn5').disabled=!(fields && zipOk);
}

function toggleOfferte(){
  const isOff = e('offerte-toggle').checked;
  e('btn5').textContent = isOff ? 'Offerte aanvragen \u2192' : 'Naar betaling \u2192';
}

// ── STAP 6: Samenvatting ───────────────────────────────────────────────────────
function fillSum(){
  const cat=CATS.find(c=>c.id===S.cat);
  const isBoth=S.pos==='both';
  const posNm={front:'Voorkant',back:'Achterkant',both:'Beide kanten'};
  const kantNmA=S.pos==='back'?'Achterkant':'Voorkant';

  e('s-textiel').textContent=(S.mdl?S.mdl.brand+' '+S.mdl.name:cat?.name||'\u2013');
  e('s-kleur').textContent=S.clrName||'\u2013';
  e('s-positie').textContent=posNm[S.pos]||'\u2013';
  e('s-qty').textContent=S.qty+' stuks';
  const sz={};document.querySelectorAll('.sz-inp').forEach(i=>{if(parseInt(i.value)>0)sz[i.dataset.size]=i.value;});
  e('s-maten').textContent=Object.entries(sz).map(([k,v])=>k+': '+v).join(', ')||'\u2013';

  e('sum-sep-a').textContent=isBoth?kantNmA:'Bedrukking';
  e('s-tech-a').textContent=techName(S.techA);
  if(S.techA==='zeef'){e('sum-kleur-a-row').classList.remove('hidden');e('s-kleur-a').textContent=S.zcA+' kleur'+(S.zcA>1?'en':'');}
  else e('sum-kleur-a-row').classList.add('hidden');
  e('s-up-a').textContent=fmt(S.upA)+' per stuk';
  const drukA_tot=parseFloat((S.upA*S.qty).toFixed(2));
  e('sum-druk-a-lbl').textContent='Bedrukking ('+S.qty+'\u00d7 '+fmt(S.upA)+')';
  e('s-druk-a').textContent=fmt(drukA_tot);

  ['sum-sep-b','sum-tech-b-row','sum-up-b-row','sum-druk-b-row'].forEach(id=>e(id).classList.toggle('hidden',!isBoth));
  if(isBoth){
    e('s-tech-b').textContent=techName(S.techB);
    if(S.techB==='zeef'){e('sum-kleur-b-row').classList.remove('hidden');e('s-kleur-b').textContent=S.zcB+' kleur'+(S.zcB>1?'en':'');}
    else e('sum-kleur-b-row').classList.add('hidden');
    e('s-up-b').textContent=fmt(S.upB)+' per stuk';
    const drukB_tot=parseFloat((S.upB*S.qty).toFixed(2));
    e('sum-druk-b-lbl').textContent='Bedrukking ('+S.qty+'\u00d7 '+fmt(S.upB)+')';
    e('s-druk-b').textContent=fmt(drukB_tot);
  }
  e('s-ship').textContent=fmt(S.ship)+(S.qty>=12?' (12+ stuks)':' (1\u201311 stuks)');
  // BTW uitsplitsing stap 6
  const sTotExcl=parseFloat((S.tot/1.21).toFixed(2));
  const sBtwBedrag=parseFloat((S.tot-sTotExcl).toFixed(2));
  e('s-total-excl').textContent=fmt(sTotExcl);
  e('s-btw').textContent=fmt(sBtwBedrag);
  e('s-total').textContent=fmt(S.tot);
  // BTW weergave via updBtwDisplay (klasse-gebaseerd)
  updBtwDisplay();
  if(S.mdl&&S.qty>0){
    e('s-textiel-prijs').textContent=fmt(S.textielTot);
    e('sum-textiel-lbl').textContent='Textiel ('+S.qty+'\u00d7 '+fmt(S.textielInclBtw)+')';
    const isOfferte=e('offerte-toggle')?.checked;
    const effectiefKortingPct=isOfferte?0:(S.kortingPct||0);
    if(effectiefKortingPct>0){
      const origTextielTot=parseFloat((S.textielInclBtwOrigineel*S.qty).toFixed(2));
      const bespaard=parseFloat((origTextielTot-S.textielTot).toFixed(2));
      e('s-korting-pct').textContent='\u2212'+(effectiefKortingPct*100)+'% (\u2212'+fmt(bespaard)+')';
      e('sum-korting-row').style.display='flex';
    } else {
      e('sum-korting-row').style.display='none';
    }
  }

  e('s-naam').textContent=e('fname').value.trim()+' '+e('lname').value.trim();
  e('s-adres').textContent=e('street').value.trim()+', '+e('zip').value.trim()+' '+e('city').value.trim();
  e('s-email-sum').textContent=e('email').value.trim();

  initPP();
}

// ── PayPal ─────────────────────────────────────────────────────────────────────
function initPP(){
  const isOff = e('offerte-toggle')?.checked;
  e('betaling-note').classList.toggle('hidden', isOff);
  e('offerte-note').classList.toggle('hidden', !isOff);
  e('btn-offerte-submit').classList.toggle('hidden', !isOff);
  e('paypal-note').classList.toggle('hidden', isOff);

  const c=e('pp-container');c.innerHTML='';
  if(isOff){ e('fallback-pay').style.display='none'; return; }
  if(typeof paypal==='undefined')return;
  e('fallback-pay').style.display='none';
  paypal.Buttons({
    style:{layout:'vertical',color:'blue',shape:'rect',label:'pay',height:46},
    createOrder:(d,a)=>a.order.create({
      purchase_units:[{
        description:'Merch Master \u2013 '+S.qty+' stuks',
        amount:{currency_code:'EUR',value:S.tot.toFixed(2),
          breakdown:{item_total:{currency_code:'EUR',value:(S.tot-S.ship).toFixed(2)},
            shipping:{currency_code:'EUR',value:S.ship.toFixed(2)},
            tax_total:{currency_code:'EUR',value:'0.00'}}}
      }],
      application_context:{shipping_preference:'NO_SHIPPING'}
    }),
    onApprove:(d,a)=>a.order.capture().then(details=>sendOrderEmail(details)),
    onError:err=>{alert('Betaling mislukt. Probeer opnieuw of neem contact op.');console.error(err);}
  }).render('#pp-container');
}

function simPay(){sendOrderEmail({id:'SIM-'+Date.now()});}

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
async function sendOrderEmail(paypalDetails){
  const isBoth=S.pos==='both';
  const posNm={front:'Voorkant',back:'Achterkant',both:'Beide kanten'};
  const sz={};
  document.querySelectorAll('.sz-inp').forEach(i=>{if(parseInt(i.value)>0)sz[i.dataset.size]=i.value;});

  let uploadUrlA = null, uploadUrlB = null;
  const fileA = e('file-front')?.files[0];
  const fileB = e('file-back')?.files[0];

  if(fileA){
    const fd=new FormData();fd.append('action','upload');fd.append('bestand',fileA);fd.append('folder','bestellingen');
    try{const r=await fetch(HANDLER_URL,{method:'POST',body:fd});const d=await r.json();if(d.success)uploadUrlA=d.url;}
    catch(err){console.warn('Upload A mislukt:',err);}
  }
  if(isBoth&&fileB){
    const fd=new FormData();fd.append('action','upload');fd.append('bestand',fileB);fd.append('folder','bestellingen');
    try{const r=await fetch(HANDLER_URL,{method:'POST',body:fd});const d=await r.json();if(d.success)uploadUrlB=d.url;}
    catch(err){console.warn('Upload B mislukt:',err);}
  }

  const verzendingEx = parseFloat((S.ship/1.21).toFixed(4));
  const drukExA = parseFloat((S.upA/1.21).toFixed(4));
  const drukExB = isBoth ? parseFloat((S.upB/1.21).toFixed(4)) : 0;

  const payload = {
    action: 'bestelling',
    order_id: paypalDetails.id || 'SIM-'+Date.now(),
    naam: e('fname').value.trim()+' '+e('lname').value.trim(),
    email: e('email').value.trim(),
    telefoon: e('phone').value.trim()||'\u2013',
    bedrijf: e('company').value.trim()||'\u2013',
    adres: e('street').value.trim()+', '+e('zip').value.trim()+' '+e('city').value.trim()+', '+e('country').value,
    taal: 'nl',
    regels: [{
      sku: S.mdl?.sku||'\u2013',
      naam: S.mdl?.name||'\u2013',
      merk: S.mdl?.brand||'\u2013',
      kleur_naam: S.clrName||'\u2013',
      positie: posNm[S.pos]||'\u2013',
      techniek_a: techName(S.techA),
      techniek_b: isBoth?techName(S.techB):null,
      maten: sz,
      aantal: S.qty,
      prijs_ex: S.prijsEx,
      druk_ex: drukExA + (isBoth?drukExB:0),
      upload_url_a: uploadUrlA,
      upload_url_b: uploadUrlB||null
    }],
    verzending_ex: verzendingEx,
    totaal_incl: parseFloat(S.tot.toFixed(2))
  };

  try {
    const r = await fetch(HANDLER_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const d = await r.json();
    if(!d.success){
      alert('Bestelling verwerken mislukt: '+(d.error||'Probeer opnieuw'));
      return;
    }
  } catch(err){
    console.error('Handler fout:', err);
  }

  e('confirm-email').textContent=e('email').value.trim();
  e('ok-title').textContent='Bestelling ontvangen!';
  e('ok-msg').innerHTML='Bedankt voor je bestelling bij Merch Master. Je ontvangt een bevestiging op <strong>'+e('email').value.trim()+'</strong>.<br><br>Vragen? WhatsApp: <a href="https://wa.me/31617255170" style="color:var(--accent)">+31 6 17 25 51 70</a>';
  gS('success');
}

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
}
init();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
