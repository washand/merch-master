<?php
/**
 * Bestellen (Order Tool) — Integrated Page
 * 
 * This file integrates the full 7-step order wizard into the main website layout.
 * - Loads header.php (website header + navigation)
 * - Embeds the wizard HTML/CSS/JS from extracted index.html
 * - Loads footer.php (website footer)
 */

$PAGE_TITLE = 'Bestellen';
$PAGE_DESC = 'Bestel direct online bij Merch Master. Textiel bedrukken met DTF, zeefdruk of borduren.';
$PAGE_URL = 'https://merch-master.com/bestellen';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Wizard Container -->
<main class="besteltool">
<div class="shell">

<!-- Header -->
<div class="hdr" style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
  <div style="display:flex;align-items:center;gap:.75rem;">
    <div class="hdr-mark"><svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
    <div class="hdr-txt">Merch<span>Master</span></div>
  </div>
  <div style="display:flex;align-items:center;gap:.5rem;">
    <!-- Taalkiezer -->
    <div style="position:relative;">
      <button id="lang-btn" onclick="toggleLangMenu()" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .7rem;border:1.5px solid var(--border);border-radius:8px;background:#fff;font-size:.78rem;font-weight:700;cursor:pointer;">
        <span id="lang-vlag">🇳🇱</span>
        <span id="lang-code">NL</span>
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div id="lang-menu" style="display:none;position:absolute;top:calc(100% + 4px);right:0;background:#fff;border:1.5px solid var(--border);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:999;min-width:130px;overflow:hidden;">
        <button onclick="setTaal('nl')" class="lang-opt" id="lang-nl">🇳🇱 Nederlands</button>
        <button onclick="setTaal('en')" class="lang-opt" id="lang-en">🇬🇧 English</button>
        <button onclick="setTaal('de')" class="lang-opt" id="lang-de">🇩🇪 Deutsch</button>
        <button onclick="setTaal('no')" class="lang-opt" id="lang-no">🇳🇴 Norsk</button>
      </div>
    </div>
    <a href="https://wa.me/31617255170" style="display:inline-flex;align-items:center;gap:.4rem;background:#25D366;color:#fff;font-size:.75rem;font-weight:700;padding:.5rem .9rem;border-radius:20px;text-decoration:none;" aria-label="WhatsApp">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/></svg>
      <span class="hdr-wa-txt">WhatsApp</span>
    </a>
  </div>
</div>

<!-- Staffelkorting banner -->
<div style="background:#e4f4ec;border:1px solid #b6dfc8;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.5rem;">
  <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#1a5e34;margin-bottom:.5rem;" class="staffel-i18n-titel">🏷️ Staffelkorting op alle textiel</div>
  <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:.4rem;background:#fff;border:1px solid #b6dfc8;border-radius:20px;padding:.3rem .75rem;">
      <span style="font-size:.95rem;font-weight:800;color:#1a5e34;" data-i18n="staffel_10">10%</span>
      <span style="font-size:.75rem;color:#2e7d52;" data-i18n="staffel_10_label">vanaf 10 stuks</span>
    </div>
    <div style="display:flex;align-items:center;gap:.4rem;background:#fff;border:1px solid #b6dfc8;border-radius:20px;padding:.3rem .75rem;">
      <span style="font-size:.95rem;font-weight:800;color:#1a5e34;" data-i18n="staffel_20">20%</span>
      <span style="font-size:.75rem;color:#2e7d52;" data-i18n="staffel_20_label">vanaf 100 stuks</span>
    </div>
  </div>
  <div style="font-size:.72rem;color:#2e7d52;margin-top:.5rem;"><span class="staffel-i18n-sub">Korting wordt automatisch verwerkt in je offerte.</span></div>
</div>

<!-- Progress (6 stappen) -->
<div class="prog">
  <div class="pb active" id="pb1"></div>
  <div class="pb" id="pb2"></div>
  <div class="pb" id="pb3"></div>
  <div class="pb" id="pb4"></div>
  <div class="pb" id="pb5"></div>
  <div class="pb" id="pb6"></div>
</div>
<div class="prog-lbl" id="prog-lbl">Stap 1 van 6 — Textiel kiezen</div>

<!-- ── Winkelwagen balk (persistent) ── -->
<div id="cart-bar" class="cart-bar" onclick="toggleCartPanel()" style="display:none;">
  <div style="display:flex;align-items:center;gap:.5rem;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
    <span style="font-size:.88rem;font-weight:700;" data-i18n="wagen_titel">Winkelwagen</span>
    <span class="cart-badge" id="cart-count">0</span>
  </div>
  <span class="cart-total-lbl" id="cart-total-lbl">€ 0,00</span>
</div>
<div id="cart-panel" class="cart-panel" style="display:none;">
  <div class="cart-panel-hdr" data-i18n="wagen_lbl">Jouw selectie</div>
  <div id="cart-items"></div>
  <div id="cart-totaalregel" style="font-size:.82rem;color:var(--ink2);padding:.85rem 1.1rem;border-top:1px solid var(--border);"></div>
  <div class="cart-footer">
    <button class="btn btn-s" onclick="nieuweRegel()" data-i18n="nog_product">+ Nog een product</button>
    <button class="btn btn-p" onclick="naarAfrekenen()" data-i18n="afrekenen">Afrekenen →</button>
  </div>
</div>

<!-- ══ STAP 1: TEXTIEL (cat → model → kleur) ══ -->
<div id="step1">
  <div class="s-lbl">Stap 1 van 6</div>
  <div class="s-ttl" id="s1-ttl" data-i18n="stap1_cat">Kies een categorie</div>
  <div class="trail" id="s1-trail"></div>

  <!-- sub: categorie -->
  <div id="s1-cat">
    <div class="sub-lbl">Productcategorie</div>
    <div class="opt-grid g4" id="cat-grid"></div>
  </div>

  <!-- sub: model -->
  <div id="s1-mdl" class="hidden">
    <div class="sub-lbl">Kies een model</div>

    <!-- Filter balk -->
    <div class="mdl-filter-bar" id="mdl-filter-bar">
      <span class="mdl-filter-label">Filter:</span>
      <select id="filter-merk" onchange="applyFilter()">
        <option value="">Alle merken</option>
      </select>
      <select id="filter-sort" onchange="applyFilter()">
        <option value="default">Standaard volgorde</option>
        <option value="az">A → Z</option>
        <option value="za">Z → A</option>
        <option value="prijs-laag">Prijs laag → hoog</option>
        <option value="prijs-hoog">Prijs hoog → laag</option>
      </select>
      <button id="filter-reset-btn" onclick="resetFilter()" title="Filter wissen">✕ Wis filter</button>
    </div>
    <div class="mdl-resultaat" id="mdl-resultaat"></div>
    <div class="opt-grid g2" id="mdl-grid"></div>
    <div class="btn-row"><button class="btn btn-s" onclick="s1Show('cat')" data-i18n="terug">← Terug</button></div>
  </div>

  <!-- sub: kleur -->
  <div id="s1-clr" class="hidden">
    <div class="sub-lbl">Kies een kleur</div>
    <div class="sw-grid" id="sw-grid"></div>
    <div class="custom-row" id="custom-row" onclick="selCustom()">
      <div class="custom-dot"></div>
      <div class="custom-lbl"><strong data-i18n="overige_kleur">Overige kleur</strong><span data-i18n="overige_kleur_sub">Vul zelf een kleur in</span></div>
    </div>
    <div id="custom-field" class="hidden">
      <input class="custom-inp" id="custom-inp" type="text" data-i18n-attr="placeholder" data-i18n="custom_ph" placeholder="Bijv. bordeauxrood, lichtblauw, PANTONE 286 C..." oninput="onCustomInp()">
      <button class="btn btn-p" style="margin-top:.75rem" onclick="confirmCustom()">Bevestig kleur →</button>
    </div>
    <div class="chosen-bar hidden" id="chosen-bar">
      <div class="chosen-dot" id="chosen-dot">
  <img id="chosen-img" style="display:none" alt="kleur preview"></div>
      <div class="chosen-nm" id="chosen-nm"></div>
    </div>
    <div class="btn-row" style="margin-top:.75rem">
      <button class="btn btn-s" onclick="s1Show('mdl')" data-i18n="terug">← Terug</button>
      <button class="btn btn-p hidden" id="btn-kleur-next" onclick="gS(2)">Volgende stap →</button>
    </div>
  </div>
</div>

<!-- ══ STAP 2: DRUKPOSITIE ══ -->
<div id="step2" class="hidden">
  <div class="s-lbl">Stap 2 van 6</div>
  <div class="s-ttl" data-i18n="stap2_ttl">Kies een drukpositie</div>

  <!-- Schematische drukpositie visualisatie -->
  <div id="pos-visual" style="display:flex;gap:1.5rem;justify-content:center;margin-bottom:1.25rem;flex-wrap:wrap;">
    <div style="text-align:center;">
      <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#7a7670;margin-bottom:.4rem;">Voorkant</div>
      <div style="position:relative;width:90px;height:105px;margin:0 auto;">
        <svg width="90" height="105" viewBox="0 0 200 220" fill="none">
          <path d="M70 20 Q100 10 130 20 L160 50 L180 40 L200 80 L170 90 L170 200 L30 200 L30 90 L0 80 L20 40 L40 50 Z" id="vis-shirt-front" fill="#e0dcd6" stroke="#ccc" stroke-width="2"/>
          <path d="M70 20 Q100 35 130 20" fill="none" stroke="#bbb" stroke-width="2"/>
        </svg>
        <div id="vis-front"  style="display:none;position:absolute;top:27%;left:25%;width:50%;height:32%;background:rgba(232,76,30,.13);border:2px dashed #e84c1e;border-radius:4px;"></div>
        <div id="vis-lborst" style="display:none;position:absolute;top:27%;left:20%;width:22%;height:18%;background:rgba(232,76,30,.13);border:2px dashed #e84c1e;border-radius:4px;"></div>
        <div id="vis-rborst" style="display:none;position:absolute;top:27%;left:58%;width:22%;height:18%;background:rgba(232,76,30,.13);border:2px dashed #e84c1e;border-radius:4px;"></div>
      </div>
    </div>
    <div style="text-align:center;">
      <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#7a7670;margin-bottom:.4rem;">Achterkant</div>
      <div style="position:relative;width:90px;height:105px;margin:0 auto;">
        <svg width="90" height="105" viewBox="0 0 200 220" fill="none">
          <path d="M70 20 Q100 10 130 20 L160 50 L180 40 L200 80 L170 90 L170 200 L30 200 L30 90 L0 80 L20 40 L40 50 Z" id="vis-shirt-back" fill="#d0ccc6" stroke="#bbb" stroke-width="2"/>
        </svg>
        <div id="vis-back" style="display:none;position:absolute;top:27%;left:25%;width:50%;height:32%;background:rgba(232,76,30,.13);border:2px dashed #e84c1e;border-radius:4px;"></div>
      </div>
    </div>
  </div>

  <!-- Enkelvoudige posities -->
  <div class="sub-lbl" style="margin-bottom:.5rem;">Enkelvoudig</div>
  <div class="opt-grid g4" style="margin-bottom:1rem;">
    <div class="opt" id="pos-front" onclick="selPos('front')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">👕</div>
      <div class="pos-name">Voorkant</div>
    </div>
    <div class="opt" id="pos-back" onclick="selPos('back')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">🔄</div>
      <div class="pos-name">Achterkant</div>
    </div>
    <div class="opt" id="pos-lborst" onclick="selPos('lborst')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">◀️</div>
      <div class="pos-name">Linkerborst</div>
    </div>
    <div class="opt" id="pos-rborst" onclick="selPos('rborst')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">▶️</div>
      <div class="pos-name">Rechterborst</div>
    </div>
  </div>

  <!-- Combinaties -->
  <div class="sub-lbl" style="margin-bottom:.5rem;">Combinatie <span style="font-size:.7rem;font-weight:400;color:#7a7670;">= 2× bedrukking</span></div>
  <div class="opt-grid g4">
    <div class="opt" id="pos-both" onclick="selPos('both')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">✨</div>
      <div class="pos-name">Voor + Achter</div>
    </div>
    <div class="opt" id="pos-lborst-back" onclick="selPos('lborst-back')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">◀️🔄</div>
      <div class="pos-name">Links + Achter</div>
    </div>
    <div class="opt" id="pos-rborst-back" onclick="selPos('rborst-back')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">▶️🔄</div>
      <div class="pos-name">Rechts + Achter</div>
    </div>
    <div class="opt" id="pos-borst-both" onclick="selPos('borst-both')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="pos-icon">◀️▶️</div>
      <div class="pos-name">Links + Rechts</div>
    </div>
  </div>

  <div class="btn-row" style="margin-top:1.25rem;">
    <button class="btn btn-s" onclick="gS(1)" data-i18n="terug">← Terug</button>
    <button class="btn btn-p" id="btn2" onclick="gS(3)" disabled data-i18n="volgende">Volgende stap →</button>
  </div>
</div>

<!-- ══ STAP 3: DRUKTECHNIEK ══ -->
<div id="step3" class="hidden">
  <div class="s-lbl">Stap 3 van 6</div>
  <div class="s-ttl" id="s3-ttl" data-i18n="stap3_ttl">Kies een druktechniek</div>

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
      <div class="tc-icon">🎨</div>
      <div class="tc-name">DTF druk</div>
      <div class="tc-desc">Full colour, scherpe details. Foto's en complexe ontwerpen.</div>
      <span class="tc-badge green">Vanaf 1 stuk</span>
    </div>
    <div class="opt" id="tc-zeef" onclick="selTech('zeef')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="tc-icon">🖨️</div>
      <div class="tc-name">Zeefdruk</div>
      <div class="tc-desc">Traditionele techniek, levendig en duurzaam. Grote oplages.</div>
      <span class="tc-badge orange">Vanaf 25 stuks</span>
    </div>
    <div class="opt" id="tc-bord" onclick="selTech('bord')">
      <div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      <div class="tc-icon">🪡</div>
      <div class="tc-name">Borduren</div>
      <div class="tc-desc">Premium uitstraling op polo's, caps en jassen.</div>
      <span class="tc-badge" style="background:#fff0e0;color:#7a4200;">Prijs op aanvraag</span>
    </div>
  </div>

  <div id="ti-dtf" class="info-note hidden"><strong>DTF:</strong> <span data-i18n="dtf_info">Geen minimale oplage, full colour. Geschikt voor katoen, polyester en nylon.</span></div>
  <div id="ti-zeef" class="info-note hidden"><strong data-i18n="tech_zeef">Zeefdruk:</strong> <span data-i18n="zeef_info">Maximaal 4 kleuren per ontwerp. Voordeligst bij 25+ stuks.</span></div>

  <!-- Borduren aanvraag knop -->
  <div class="info-note blue" style="margin-bottom:1rem">
    <strong>Wil je borduren?</strong> De prijs hangt af van het aantal steken in jouw logo en wordt handmatig berekend. Stuur een aanvraag en we komen zo snel mogelijk met een offerte.
  </div>


  <div class="btn-row">
    <button class="btn btn-s" onclick="gS(2)" data-i18n="terug">← Terug</button>
    <button class="btn btn-p" id="btn3" onclick="afterTech()" disabled data-i18n="volgende">Volgende stap →</button>
  </div>
</div>

<!-- ══ STAP 4: MATEN & AANTALLEN ══ -->
<div id="step4" class="hidden">
  <div class="s-lbl">Stap 4 van 6</div>
  <div class="s-ttl" data-i18n="stap4_ttl">Maten &amp; aantallen</div>

  <!-- Zeef: kleuren per kant -->
  <div id="zeef-front-col" class="hidden">
    <div id="zeef-front-khdr" class="kant-hdr hidden">
      <div class="kant-dot"></div><div class="kant-lbl">Voorkant — zeefdruk kleuren</div>
    </div>
    <div class="sub-lbl" style="margin-top:.5rem">Aantal drukkleuren voorkant (max. 4)</div>
    <div class="zc-row" id="zc-front">
      <button class="zc-btn sel" data-n="1" onclick="selZC('front',1)">1 kleur</button>
      <button class="zc-btn" data-n="2" onclick="selZC('front',2)">2 kleuren</button>
      <button class="zc-btn" data-n="3" onclick="selZC('front',3)">3 kleuren</button>
      <button class="zc-btn" data-n="4" onclick="selZC('front',4)">4 kleuren</button>
    </div>
  </div>
  <div id="zeef-back-col" class="hidden">
    <div class="kant-hdr" style="margin-top:.5rem">
      <div class="kant-dot" style="background:#888"></div><div class="kant-lbl">Achterkant — zeefdruk kleuren</div>
    </div>
    <div class="sub-lbl" style="margin-top:.5rem">Aantal drukkleuren achterkant (max. 4)</div>
    <div class="zc-row" id="zc-back">
      <button class="zc-btn sel" data-n="1" onclick="selZC('back',1)">1 kleur</button>
      <button class="zc-btn" data-n="2" onclick="selZC('back',2)">2 kleuren</button>
      <button class="zc-btn" data-n="3" onclick="selZC('back',3)">3 kleuren</button>
      <button class="zc-btn" data-n="4" onclick="selZC('back',4)">4 kleuren</button>
    </div>
  </div>

  <div class="sub-lbl" style="margin-top:.25rem">Verdeling over maten</div>
  <div id="sz-container"></div>
  <div id="qty-warn" class="qty-warn hidden"></div>

  <!-- Live offerte -->
  <div class="quote-box" id="quote-box" style="display:none">
    <div class="q-main">
      <div class="q-lbl">Jouw offerte</div>
      <div class="q-price" id="q-total">€0,–</div>
      <div class="q-sub" id="q-sub">incl. BTW</div>
    </div>
    <div class="q-bd" id="q-bd"></div>
  </div>

  <div class="btn-row">
    <button class="btn btn-s" onclick="goBackFromStep4()" data-i18n="terug">← Terug</button>
    <button class="btn btn-p" id="btn4" onclick="gS(5)" disabled data-i18n="volgende">Volgende stap →</button>
  </div>
</div>

<!-- ══ STAP 5: ONTWERP & GEGEVENS ══ -->
<div id="step5" class="hidden">
  <div class="s-lbl">Stap 5 van 6</div>
  <div class="s-ttl" data-i18n="stap5_ttl">Jouw ontwerp &amp; gegevens</div>

  <!-- Upload voorkant (altijd zichtbaar, tenzij alleen achterkant) -->
  <div id="upload-front-wrap">
    <div class="upload-lbl" id="upload-front-lbl">Logo / ontwerp voorkant</div>
    <div id="upload-front-list"></div>
    <button class="btn btn-s" style="margin-bottom:.5rem;font-size:.78rem;" onclick="addUploadSlot('front')">+ Bestand toevoegen</button>
  </div>

  <div id="upload-back-wrap" class="hidden">
    <div class="upload-lbl" style="margin-top:.5rem">Logo / ontwerp achterkant</div>
    <div id="upload-back-list"></div>
    <button class="btn btn-s" style="margin-bottom:.5rem;font-size:.78rem;" onclick="addUploadSlot('back')">+ Bestand toevoegen</button>
  </div>

  <div id="upload-lborst-wrap" class="hidden">
    <div class="upload-lbl">Logo / ontwerp linkerborst</div>
    <div id="upload-lborst-list"></div>
    <button class="btn btn-s" style="margin-bottom:.5rem;font-size:.78rem;" onclick="addUploadSlot('lborst')">+ Bestand toevoegen</button>
  </div>

  <div id="upload-rborst-wrap" class="hidden">
    <div class="upload-lbl">Logo / ontwerp rechterborst</div>
    <div id="upload-rborst-list"></div>
    <button class="btn btn-s" style="margin-bottom:.5rem;font-size:.78rem;" onclick="addUploadSlot('rborst')">+ Bestand toevoegen</button>
  </div>

  <div class="info-note" style="margin-top:.75rem"><span data-i18n="geen_ontwerp_full">Geen ontwerp klaar? Geen probleem — beschrijf je wens hieronder, dan nemen we contact op.</span></div>
  <div class="field"><label data-i18n="opmerkingen">Omschrijving / bijzonderheden</label><textarea id="notes" data-i18n-attr="placeholder" data-i18n="opmerkingen_ph" placeholder="Bijv: logo op borst links, witte tekst op zwarte achtergrond..."></textarea></div>
  <hr class="divider">
  <div class="field-row">
    <div class="field"><label>Voornaam *</label><input type="text" id="fname" placeholder="Jan" oninput="chk5()"></div>
    <div class="field"><label>Achternaam *</label><input type="text" id="lname" placeholder="de Vries" oninput="chk5()"></div>
  </div>
  <div class="field"><label>E-mailadres *</label><input type="email" id="email" placeholder="jan@bedrijf.nl" oninput="chk5()"></div>
  <div class="field"><label>Telefoonnummer</label><input type="tel" id="phone" placeholder="+31 6 12345678"></div>
  <div class="field"><label>Bedrijfsnaam (optioneel)</label><input type="text" id="company" placeholder="Bedrijf BV" oninput="toggleBizFields()"></div>
  <div id="biz-fields" class="hidden">
    <div class="field-row">
      <div class="field"><label>KVK-nummer</label><input type="text" id="kvk" placeholder="12345678" maxlength="8"></div>
      <div class="field"><label>BTW-nummer</label><input type="text" id="btwnr" placeholder="NL123456789B01"></div>
    </div>
  </div>
  <hr class="divider">
  <div class="field-row">
    <div class="field"><label>Straat + huisnummer *</label><input type="text" id="street" placeholder="Hoofdstraat 1" oninput="chk5()"></div>
    <div class="field">
      <label>Postcode *</label>
      <input type="text" id="zip" placeholder="1234 AB" oninput="chk5()">
      <div id="zip-warn" class="hidden" style="font-size:.75rem;color:var(--accent);margin-top:4px;"></div>
    </div>
  </div>
  <div class="field-row">
    <div class="field"><label>Plaats *</label><input type="text" id="city" placeholder="Amsterdam" oninput="chk5()"></div>
    <div class="field"><label>Land</label><select id="country" onchange="chk5()"><option value="NL" selected>Nederland</option><option value="BE">België</option><option value="DE">Duitsland</option><option value="other">Anders</option></select></div>
  </div>
  <div class="btn-row">
    <button class="btn btn-s" onclick="gS(4)" data-i18n="terug">← Terug</button>
    <button class="btn btn-s" id="btn-offerte-opslaan" onclick="slaOfferteOp()" disabled title="Sla deze configuratie op in je account">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      <span data-i18n="offerte_opslaan">Offerte opslaan</span>
    </button>
    <button class="btn btn-cart-add btn-p" id="btn5" onclick="voegToeAanWagen()" disabled>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span data-i18n="toevoegen">Toevoegen aan bestelling</span>
    </button>
  </div>

  <!-- Offerte opslaan modal -->
  <div id="offerte-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;display:none;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:#fff;border-radius:12px;padding:1.75rem;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.2);">
      <div style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;margin-bottom:.35rem;">Offerte opslaan</div>
      <div style="font-size:.82rem;color:#7a7670;margin-bottom:1rem;">Geef je offerte een naam zodat je hem later makkelijk terugvindt.</div>
      <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:.78rem;font-weight:600;margin-bottom:.35rem;">Naam</label>
        <input type="text" id="offerte-naam" placeholder="Bijv. Teamshirts 2026" style="width:100%;padding:.65rem .85rem;border:2px solid #e8e4dc;border-radius:8px;font-size:.88rem;"
               onkeydown="if(event.key==='Enter')bevestigOfferteOpslaan()">
      </div>
      <div id="offerte-modal-fout" style="display:none;background:#fee2e2;border-radius:6px;padding:.5rem .75rem;font-size:.78rem;color:#991b1b;margin-bottom:.75rem;"></div>
      <div style="display:flex;gap:.5rem;justify-content:flex-end;">
        <button class="btn btn-s" onclick="sluitOfferteModal()">Annuleren</button>
        <button class="btn btn-p" onclick="bevestigOfferteOpslaan()">Opslaan</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ STAP 6: BETALING ══ -->
<div id="step6" class="hidden">
  <div class="s-lbl">Stap 6 van 6</div>
  <div class="s-ttl" data-i18n="stap6_ttl">Controleer &amp; betaal</div>

  <div class="sum-card">
    <div class="sum-hd">Jouw bestelling</div>
    <div class="sum-row"><span class="k" data-i18n="textiel">Textiel</span><span class="v" id="s-textiel">–</span></div>
    <div class="sum-row"><span class="k" data-i18n="kleur">Kleur</span><span class="v" id="s-kleur">–</span></div>
    <div class="sum-row"><span class="k" data-i18n="drukpositie">Drukpositie</span><span class="v" id="s-positie">–</span></div>
    <div class="sum-row"><span class="k" data-i18n="totaal_stuks2">Totaal stuks</span><span class="v" id="s-qty">–</span></div>
    <div class="sum-row"><span class="k" data-i18n="maten">Aantallen per maat</span><span class="v" id="s-maten">–</span></div>
    <!-- Kant A -->
    <div class="sum-sep" id="sum-sep-a">Voorkant</div>
    <div class="sum-row"><span class="k">Techniek</span><span class="v" id="s-tech-a">–</span></div>
    <div class="sum-row" id="sum-kleur-a-row"><span class="k">Kleuren</span><span class="v" id="s-kleur-a">–</span></div>
    <div class="sum-row"><span class="k">Prijs per stuk</span><span class="v" id="s-up-a">–</span></div>
    <!-- Kant B (only both) -->
    <div class="sum-sep hidden" id="sum-sep-b">Achterkant</div>
    <div class="sum-row hidden" id="sum-tech-b-row"><span class="k">Techniek</span><span class="v" id="s-tech-b">–</span></div>
    <div class="sum-row hidden" id="sum-kleur-b-row"><span class="k">Kleuren</span><span class="v" id="s-kleur-b">–</span></div>
    <div class="sum-row hidden" id="sum-up-b-row"><span class="k">Prijs per stuk</span><span class="v" id="s-up-b">–</span></div>
    <!-- Totalen — volgorde: textiel → bedrukking → verzending → BTW → totaal -->
    <hr style="border:none;border-top:1px solid var(--border);margin:.75rem 0;">
    <div class="sum-row"><span class="k">Textiel p/stuk</span><span class="v" id="s-textiel-prijs">–</span></div>
    <div class="sum-row"><span class="k">Textiel totaal</span><span class="v" id="s-textiel-tot">–</span></div>
    <div class="sum-row">
      <span class="k" id="s-druk-label">Bedrukking totaal</span>
      <span class="v" id="s-druk">–</span>
    </div>
    <div id="s-bord-note" class="hidden" style="background:#fff8ec;border:1px solid #f7d89a;border-radius:6px;padding:10px 14px;margin:6px 0 10px;font-size:.78rem;color:#6b4800;line-height:1.5;">
      <strong>⚠️ Borduurprijs nog niet inbegrepen</strong><br>
      De borduurkosten worden apart berekend op basis van het aantal steken in jouw logo. Je ontvangt na je aanvraag binnen 1–2 werkdagen een offerte per e-mail.
    </div>
    <div class="sum-row"><span class="k">Verzending</span><span class="v" id="s-ship">–</span></div>
    <hr style="border:none;border-top:1px solid var(--border);margin:.75rem 0;">
    <div class="sum-row"><span class="k" data-i18n="subtotaal">Subtotaal ex BTW</span><span class="v" id="s-totex">–</span></div>
    <div class="sum-row"><span class="k" data-i18n="btw">BTW 21%</span><span class="v" id="s-btw">–</span></div>
    <div class="sum-total"><span class="lbl" data-i18n="totaal_incl">Totaal incl. BTW</span><span class="prc" id="s-total">–</span></div>
    <div id="s-total-note" class="hidden" style="font-size:.72rem;color:#6b4800;margin-top:8px;padding:0 2px;">* Exclusief borduurkosten — je ontvangt hiervoor apart een offerte.</div>
  </div>

  <div class="sum-card" style="margin-bottom:1.35rem">
    <div class="sum-hd">Bezorgadres</div>
    <div class="sum-row"><span class="k">Naam</span><span class="v" id="s-naam">–</span></div>
    <div class="sum-row"><span class="k">Adres</span><span class="v" id="s-adres">–</span></div>
    <div class="sum-row"><span class="k">E-mail</span><span class="v" id="s-email-sum">–</span></div>
  </div>

  <!-- Betaling of aanvraag afhankelijk van techniek -->
  <div id="bord-aanvraag-block" class="hidden">
    <div class="info-note" style="background:#fff8ec;border-color:#f7c948;">
      <strong>🪡 Borduren — prijs op aanvraag</strong><br>
      <span data-i18n="bord_tekst">Je ontvangt binnen 1–2 werkdagen een offerte per e-mail op basis van het aantal steken in jouw logo.</span><br><br>
      <strong>Tip:</strong> <span data-i18n="bord_tip">Hoe duidelijker je wensen zijn in het opmerkingenveld (gewenste positie, kleur, grootte), hoe nauwkeuriger wij de prijs kunnen inschatten.</span>
    </div>
    <button class="btn btn-aanvraag" id="btn-bord-aanvraag" onclick="sendBordAanvraagViaFlow()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      Borduurwens versturen
    </button>
  </div>
  <div id="betaal-block">
    <div class="info-note" id="levertijd-note" style="display:flex;align-items:flex-start;gap:.75rem;">
      <span style="font-size:1.3rem;flex-shrink:0;">🚚</span>
      <div>
        <strong>Verwachte levertijd: <span id="levertijd-tekst">5–8 werkdagen</span></strong><br>
        <span style="font-size:.78rem;color:var(--ink3);" data-i18n="levertijd_track">Na bevestiging van je ontwerp starten we direct met productie. Je ontvangt een track & trace zodra je bestelling is verzonden.</span>
      </div>
    </div>
    <div class="info-note" style="margin-top:.6rem;" data-i18n="betaal_info2">Na betaling ontvang je een orderbevestiging per e-mail. We nemen contact op als we vragen hebben over je ontwerp.</div>
    <div id="pp-container" style="margin-bottom:1rem"></div>
    <button class="btn btn-pp" id="fallback-pay" onclick="simPay()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      Betalen met PayPal
    </button>
    <button class="btn" id="sim-pay-btn" onclick="simPay()" style="margin-top:.5rem;background:#f0ede6;color:var(--ink2);font-size:.75rem;padding:.5rem 1rem;border:2px dashed var(--border)">
      🧪 Simuleer betaling (alleen voor testen)
    </button>
  </div>
  <div style="text-align:center;margin-top:.65rem;font-size:.72rem;color:var(--ink3)"><span data-i18n="betaal_slot">🔒 Beveiligde betaling via PayPal · 21% BTW inbegrepen</span></div>
  <div class="btn-row" style="margin-top:1rem"><button class="btn btn-s" onclick="gS(5)" data-i18n="terug">← Terug</button></div>
</div>

<!-- ══ BORDUREN AANVRAAG ══ -->
<div id="step-bord" class="hidden">
  <div class="s-lbl">Borduren op aanvraag</div>
  <div class="s-ttl">Stuur je borduurwens in</div>
  <div class="info-note blue" data-i18n="bord_note2">We berekenen de prijs handmatig op basis van het aantal steken in jouw logo. Je ontvangt binnen 1–2 werkdagen een offerte per e-mail.</div>

  <div class="upload-lbl">Logo / ontwerp (verplicht)</div>
  <div class="upload-area" id="upload-bord" onclick="document.getElementById('file-bord').click()">
    <div class="upload-icon-wrap">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </div>
    <div class="upload-text"><strong data-i18n="upload_klik">Klik om te uploaden</strong> <span data-i18n="upload_of">of sleep hier naartoe</span></div>
    <div class="upload-text" style="margin-top:3px;font-size:.7rem;">AI, EPS, PDF, PNG, SVG – max. 50 MB</div>
    <div class="upload-name" id="upload-bord-name"></div>
  </div>
  <input type="file" id="file-bord" accept=".ai,.eps,.pdf,.png,.svg,.jpg,.jpeg" onchange="handleUpload('bord',this)">

  <div class="field" style="margin-top:.75rem"><label data-i18n="bord_wensen">Omschrijving / wensen</label><textarea id="bord-notes" data-i18n-attr="placeholder" data-i18n="bord_wensen_ph" placeholder="Bijv: logo op borst links, gewenste kleur, type textiel..."></textarea></div>
  <hr class="divider">
  <div class="field-row">
    <div class="field"><label>Voornaam *</label><input type="text" id="bfname" placeholder="Jan" oninput="chkBord()"></div>
    <div class="field"><label>Achternaam *</label><input type="text" id="blname" placeholder="de Vries" oninput="chkBord()"></div>
  </div>
  <div class="field"><label>E-mailadres *</label><input type="email" id="bemail" placeholder="jan@bedrijf.nl" oninput="chkBord()"></div>
  <div class="field"><label>Telefoonnummer</label><input type="tel" id="bphone" placeholder="+31 6 12345678"></div>
  <div class="field"><label>Bedrijfsnaam (optioneel)</label><input type="text" id="bcompany" placeholder="Bedrijf BV"></div>

  <button class="btn btn-aanvraag" id="btn-bord" onclick="sendBordAanvraag()" disabled>
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
    Aanvraag versturen
  </button>
  <div class="btn-row" style="margin-top:1rem"><button class="btn btn-s" onclick="gS(3)" data-i18n="terug_tech">← Terug naar technieken</button></div>
</div>

<!-- ══ SUCCESS ══ -->
<div id="success" class="hidden">
  <div class="ok-screen">
    <div class="ok-icon"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
    <h2 id="ok-title" data-i18n="ok_title">Bestelling ontvangen! 🎉</h2>
    <p id="ok-msg" style="margin-bottom:1rem;">Bedankt voor je bestelling bij Merch Master. Je ontvangt een bevestiging op <strong id="confirm-email"></strong>.</p>

    <!-- Wat gebeurt er nu -->
    <div style="background:#f5f3ef;border-radius:10px;padding:1.1rem 1.25rem;margin-bottom:1.25rem;text-align:left;width:100%;max-width:400px;">
      <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#7a7670;margin-bottom:.75rem;">Wat gebeurt er nu?</div>
      <div style="display:flex;flex-direction:column;gap:.6rem;">
        <div style="display:flex;align-items:flex-start;gap:.65rem;">
          <div style="width:22px;height:22px;border-radius:50%;background:var(--accent);color:#fff;font-size:.7rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">1</div>
          <div style="font-size:.83rem;color:#3a3832;line-height:1.5;"><strong data-i18n="ok_stap1_t">Bevestiging</strong> — <span data-i18n="ok_stap1">Je ontvangt direct een e-mail met je orderoverzicht.</span></div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:.65rem;">
          <div style="width:22px;height:22px;border-radius:50%;background:var(--accent);color:#fff;font-size:.7rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">2</div>
          <div style="font-size:.83rem;color:#3a3832;line-height:1.5;"><strong data-i18n="ok_stap2_t">Ontwerp check</strong> — <span data-i18n="ok_stap2">We bekijken je logo en nemen contact op bij vragen.</span></div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:.65rem;">
          <div style="width:22px;height:22px;border-radius:50%;background:var(--accent);color:#fff;font-size:.7rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">3</div>
          <div style="font-size:.83rem;color:#3a3832;line-height:1.5;"><strong data-i18n="ok_stap3_t">Productie &amp; verzending</strong> — <span data-i18n="ok_stap3">Na goedkeuring starten we direct.</span></div>
        </div>
      </div>
    </div>

    <!-- Ordernummer -->
    <div style="font-size:.78rem;color:#7a7670;margin-bottom:1.25rem;">
      Ordernummer: <strong id="ok-ordernr" style="color:var(--ink);font-family:'Syne',sans-serif;"></strong>
    </div>

    <!-- Acties -->
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;justify-content:center;margin-bottom:1rem;">
      <a id="ok-whatsapp" href="https://wa.me/31617255170" target="_blank"
         style="display:inline-flex;align-items:center;gap:.5rem;background:#25D366;color:#fff;font-size:.85rem;font-weight:700;padding:.7rem 1.2rem;border-radius:8px;text-decoration:none;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/></svg>
        WhatsApp
      </a>
      <button class="btn btn-p" onclick="doReset()">Nieuwe bestelling</button>
    </div>
  </div>
</div>

</div><!-- /shell -->
</main>

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --ink:#0f0e0c;--ink2:#3a3832;--ink3:#7a7670;
  --paper:#faf8f4;--surface:#fff;--border:#e8e4dc;
  --accent:#e84c1e;--accent2:#f7a11a;--success:#1a7a45;
  --r:10px;--shadow:0 2px 12px rgba(0,0,0,.07);
}
html{font-size:16px;}
body{font-family:'DM Sans',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;padding:1.25rem .85rem 4rem;}
.shell{max-width:780px;margin:0 auto;}

/* Header */
.hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;}
.hdr-mark{width:38px;height:38px;background:var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;}
.hdr-mark svg{width:20px;height:20px;fill:none;stroke:#fff;stroke-width:2;}
.hdr-txt{font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:700;}
.hdr-txt span{color:var(--accent);}

/* Progress */
.prog{display:flex;gap:5px;margin-bottom:2.5rem;}
.pb{flex:1;height:4px;border-radius:2px;background:var(--border);transition:background .35s;}
.pb.done{background:var(--accent);}
.pb.active{background:var(--accent2);}
.prog-lbl{font-size:.72rem;font-weight:600;color:var(--ink3);margin-top:.4rem;margin-bottom:.1rem;text-align:center;min-height:1rem;transition:opacity .2s;}

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
.cat-svg{width:54px;height:54px;margin:0 auto .4rem;color:var(--ink2);display:flex;align-items:center;justify-content:center;}
.cat-svg svg{width:100%;height:100%;}
.cat-opt.sel .cat-svg{color:var(--accent);}
.cat-opt:hover .cat-svg{color:var(--ink);}
.cat-name{font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;}

/* Model cards */
.mdl-brand{font-size:.64rem;text-transform:uppercase;letter-spacing:.08em;color:var(--ink3);font-weight:600;margin-bottom:2px;}
.mdl-name{font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;line-height:1.3;}
.mdl-sku{font-size:.67rem;color:var(--ink3);margin-top:2px;}
.mdl-tags{display:flex;gap:4px;flex-wrap:wrap;margin-top:6px;}
.mtag{font-size:.62rem;padding:2px 6px;border-radius:3px;background:#f0ede6;color:var(--ink2);}
.mtag.eco{background:#e4f4ec;color:#1a5e34;}
.mtag.prem{background:#fff0e0;color:#7a4200;}
.mdl-price{display:block;margin-top:7px;font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;color:var(--accent);}
.mdl-price-sub{font-size:.68rem;color:var(--ink3);font-weight:400;font-family:'DM Sans',sans-serif;}
.mdl-price-disc{font-size:.65rem;color:var(--success);font-weight:500;font-family:'DM Sans',sans-serif;}
.mdl-card{padding:.7rem .85rem;}
.mdl-img-wrap{width:100%;height:110px;display:flex;align-items:center;justify-content:center;margin-bottom:.5rem;overflow:hidden;border-radius:6px;background:#f5f3ef;}
.mdl-img{max-height:108px;max-width:100%;object-fit:contain;}
.mdl-swatches{display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;}
.mdl-swatch{width:16px;height:16px;border-radius:50%;border:1.5px solid rgba(0,0,0,.12);display:inline-block;}
.mdl-clr-more{font-size:.65rem;color:var(--ink3);align-self:center;margin-left:2px;}
#chosen-img{width:100%;max-height:140px;object-fit:contain;border-radius:8px;margin-top:.5rem;background:#f5f3ef;}

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
.opt.disabled{opacity:.55;cursor:not-allowed;pointer-events:none;}
.bord-aanvraag{font-size:.72rem;color:#888;font-weight:600;margin-top:4px;font-style:italic;}

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
.sz-inp{width:60px;padding:4px 6px;border:1.5px solid var(--border);border-radius:6px;font-family:'Syne',sans-serif;font-weight:600;font-size:.875rem;text-align:center;background:var(--surface);color:var(--ink);}
.sz-inp:focus{outline:none;border-color:var(--accent);}
/* Mobiele matenlijst als grid */
.sz-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-bottom:1.5rem;}
.sz-grid-item{background:#fff;border:1.5px solid var(--border);border-radius:8px;padding:.5rem .4rem;text-align:center;}
.sz-grid-item label{display:block;font-size:.7rem;font-weight:700;color:var(--ink3);margin-bottom:.25rem;text-transform:uppercase;}
.sz-grid-item .sz-inp{width:100%;font-size:.9rem;}

/* Quote box */
.quote-box{background:var(--ink);color:#fff;border-radius:var(--r);padding:1.1rem 1.4rem;margin-bottom:1.5rem;display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;}
.q-main{flex:1;min-width:140px;}
.q-lbl{font-size:.68rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.08em;font-weight:500;}
.q-price{font-family:'Syne',sans-serif;font-size:1.9rem;font-weight:800;line-height:1;}
.q-sub{font-size:.75rem;color:rgba(255,255,255,.5);margin-top:3px;}
.q-bd{flex:1;min-width:170px;}
.qr{display:flex;justify-content:space-between;font-size:.78rem;padding:2px 0;}
.qr .k{color:rgba(255,255,255,.5);}
.qr .v{font-weight:500;}
.qdiv{border:none;border-top:1px solid rgba(255,255,255,.12);margin:5px 0;}
.q-kant-sep{font-size:.68rem;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4);margin:6px 0 3px;}

/* Fields */
.field{margin-bottom:1.1rem;}
.field label{display:block;font-size:.78rem;font-weight:500;margin-bottom:.35rem;color:var(--ink2);}
.field input,.field textarea,.field select{width:100%;padding:.65rem .85rem;border:2px solid var(--border);border-radius:var(--r);font-family:'DM Sans',sans-serif;font-size:.88rem;color:var(--ink);background:var(--surface);transition:border-color .2s;}
.field input:focus,.field textarea:focus,.field select:focus{outline:none;border-color:var(--accent);}
.field textarea{resize:vertical;min-height:76px;}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}

/* Upload */
.upload-area{border:2px dashed var(--border);border-radius:var(--r);padding:1.25rem;text-align:center;cursor:pointer;background:var(--surface);transition:border-color .2s,background .2s;margin-bottom:.6rem;position:relative;}
.upload-area:hover{border-color:var(--accent);background:#fff9f5;}
.upload-area.dragging{border-color:var(--accent);background:#fff3ee;transform:scale(1.01);}
.upload-area.has-file{border-color:var(--success);background:#f0faf4;padding:0;overflow:hidden;}
/* Preview na upload */
.upload-preview{width:100%;height:90px;object-fit:cover;display:block;border-radius:calc(var(--r) - 2px);}
.upload-preview-wrap{position:relative;width:100%;}
.upload-preview-overlay{position:absolute;inset:0;background:rgba(0,0,0,.5);opacity:0;transition:opacity .2s;border-radius:calc(var(--r) - 2px);display:flex;align-items:center;justify-content:center;gap:.5rem;}
.upload-preview-wrap:hover .upload-preview-overlay{opacity:1;}
.upload-overlay-btn{background:rgba(255,255,255,.9);border:none;border-radius:6px;padding:.35rem .65rem;font-size:.72rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.3rem;transition:background .15s;}
.upload-overlay-btn:hover{background:#fff;}
.upload-overlay-btn.del{color:#e84c1e;}
.upload-overlay-btn.rep{color:#333;}
/* Bestandsnaam onder preview */
.upload-file-info{display:flex;align-items:center;justify-content:space-between;padding:.4rem .6rem;background:#f0faf4;border-top:1px solid rgba(26,122,69,.15);}
.upload-file-naam{font-size:.72rem;color:var(--success);font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:75%;}
.upload-file-size{font-size:.65rem;color:var(--ink3);}
/* Icoon in lege staat */
.upload-icon-wrap{width:40px;height:40px;border-radius:50%;background:rgba(232,76,30,.08);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;}
.upload-icon-wrap svg{width:20px;height:20px;stroke:var(--accent);fill:none;stroke-width:1.5;}
.upload-icon{font-size:1.6rem;margin-bottom:.35rem;}
.upload-text{font-size:.82rem;color:var(--ink3);}
.upload-text strong{color:var(--ink);}
.upload-name{font-size:.78rem;color:var(--success);font-weight:500;margin-top:4px;}
.upload-lbl{font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;margin-bottom:.4rem;color:var(--ink2);}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:.45rem;padding:.75rem 1.4rem;border-radius:var(--r);font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;cursor:pointer;border:none;transition:all .18s;min-height:44px;touch-action:manipulation;-webkit-tap-highlight-color:transparent;letter-spacing:.01em;}
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
.sum-total{display:flex;justify-content:space-between;padding:.9rem 1.2rem;background:#fff9f7;border-top:2px solid var(--accent);}
.sum-total .lbl{font-family:'Syne',sans-serif;font-weight:700;}
.sum-total .prc{font-family:'Syne',sans-serif;font-size:1.25rem;font-weight:800;color:var(--accent);}

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
  /* ── Layout ── */
  .wrap{padding:.75rem .85rem 1.5rem;}
  .s-ttl{font-size:1.25rem;}
  .g4{grid-template-columns:repeat(2,1fr);}
  .g3{grid-template-columns:1fr;}
  .g2{grid-template-columns:1fr;}
  .field-row{grid-template-columns:1fr;}
  .quote-box{flex-direction:column;gap:.5rem;}
  .btn-row .btn{flex:1;justify-content:center;}
  .btn-row{gap:.5rem;}

  /* ── Opties ── */
  .opt{padding:.75rem .6rem;}
  .opt .pos-icon{font-size:1.3rem;}
  .opt .pos-name{font-size:.8rem;}
  .opt .pos-desc{display:none;}
  .tc{padding:.9rem .7rem;}
  .tc-icon{font-size:1.4rem;}
  .tc-name{font-size:.88rem;}
  .tc-desc{font-size:.73rem;}
  .tc-note{font-size:.68rem;}

  /* ── Maten grid ── */
  .sz-grid{grid-template-columns:repeat(3,1fr)!important;}

  /* ── Quote box ── */
  #quote-box .q-row{flex-direction:row;}

  /* ── Staffelbanner ── */
  .staffel-banner>div{flex-direction:column;gap:.3rem;align-items:flex-start;}

  /* ── Samenvatting stap 6 ── */
  .sum-card{padding:.9rem 1rem;}
  .sum-hd{font-size:.85rem;}
  .sum-row{font-size:.82rem;}
  .sum-total{font-size:1rem;}
  .sum-total .prc{font-size:1.2rem;}

  /* ── Voortgang ── */
  .prog-lbl{font-size:.68rem;}

  /* ── Upload ── */
  .upload-area{padding:.6rem .75rem;}
  .upload-text{font-size:.75rem;}

  /* ── Winkelwagen ── */
  .cart-footer{grid-template-columns:1fr;}
  .cart-item{gap:.35rem;}
  .cart-item-naam{font-size:.82rem;}

  /* ── Drukpositie visualisatie ── */
  #pos-visual{gap:.75rem;}
  #pos-visual>div svg{width:70px;height:82px;}

  /* ── Kleurswatches ── */
  .clr-item{width:36px;height:36px;}

  /* ── Kant indicator ── */
  .kant-hdr{padding:.6rem .8rem;}
}

@media(max-width:380px){
  .g4{grid-template-columns:repeat(2,1fr);}
  .s-ttl{font-size:1.1rem;}
  .btn{font-size:.8rem;padding:.6rem 1rem;}
  .clr-item{width:30px;height:30px;}
  .hdr-wa-txt{display:none;}
}
/* ── Winkelwagen ─────────────────────────────────────────────────────────────*/
.cart-bar{display:flex;align-items:center;justify-content:space-between;background:#fff;border:2px solid var(--border);border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1rem;cursor:pointer;transition:border-color .2s;}
.cart-bar:hover{border-color:var(--accent);}
.cart-badge{background:var(--accent);color:#fff;font-size:.7rem;font-weight:800;padding:2px 7px;border-radius:10px;margin-left:.4rem;}
.cart-total-lbl{font-size:.9rem;font-weight:700;color:var(--accent);}
.cart-panel{background:#fff;border:2px solid var(--border);border-radius:10px;margin-bottom:1rem;overflow:hidden;}
.cart-panel-hdr{background:#f5f3ef;padding:.75rem 1.1rem;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--ink3);border-bottom:1px solid var(--border);}
.cart-item{display:grid;grid-template-columns:1fr auto;gap:.5rem;padding:.85rem 1.1rem;border-bottom:1px solid var(--border);align-items:start;}
.cart-item:last-child{border-bottom:none;}
.cart-item-naam{font-size:.88rem;font-weight:700;line-height:1.3;}
.cart-item-detail{font-size:.75rem;color:var(--ink3);margin-top:2px;line-height:1.5;}
.cart-item-prijs{font-size:.9rem;font-weight:700;text-align:right;}
.cart-item-del{display:block;font-size:.75rem;color:#aaa;cursor:pointer;margin-top:4px;text-align:right;}
.cart-item-del:hover{color:var(--accent);}
.cart-footer{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;padding:.85rem 1.1rem;background:#faf8f4;border-top:2px solid var(--border);}
.btn-cart-add{background:var(--ink);color:#fff;border:none;}
.btn-cart-add:hover{background:#333;}

/* ── Model filter balk ───────────────────────────────────────────────────────*/
.mdl-filter-bar{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;margin-bottom:.9rem;}
.mdl-filter-bar select,.mdl-filter-bar button{
  padding:.45rem .75rem;border:1.5px solid var(--border);border-radius:7px;
  font-size:.78rem;font-family:'DM Sans',sans-serif;font-weight:600;
  background:#fff;color:var(--ink2);cursor:pointer;transition:border-color .15s;
  min-height:36px;
}
.mdl-filter-bar select:focus,.mdl-filter-bar button:hover{outline:none;border-color:var(--accent);}
.mdl-filter-bar button.actief{background:var(--accent);color:#fff;border-color:var(--accent);}
.mdl-filter-label{font-size:.72rem;font-weight:700;color:var(--ink3);white-space:nowrap;text-transform:uppercase;letter-spacing:.06em;}
.mdl-resultaat{font-size:.75rem;color:var(--ink3);margin-bottom:.5rem;}

/* ── Taalkiezer ──────────────────────────────────────────────────────────────*/
.lang-opt{display:block;width:100%;padding:.55rem .85rem;text-align:left;background:none;border:none;font-size:.8rem;font-weight:600;cursor:pointer;color:var(--ink2);}
.lang-opt:hover{background:#f5f3ef;}
.lang-opt.actief{background:#fff9f7;color:var(--accent);}
</style>

<script src="https://www.paypal.com/sdk/js?client-id=ASLap52V7_VjYsq3D5k1W9a9RLG7854wBRs9TQ0m0PHhLXALJwrG3i-r4nrQOMuUr0d_Dqr5BSMv4ebk&currency=EUR&locale=nl_NL"></script>

<!-- Main Wizard JavaScript -->
<script>
// ── Besteltool v2 — PHP Handler ───────────────────────────────────────────────
const HANDLER_URL = 'https://merch-master.com/bestellen/handler.php';

// ══════════════════════════════════════════════════════════════════════════════
// MEERTALIGHEID — i18n systeem
// ══════════════════════════════════════════════════════════════════════════════
let TAAL = 'nl';

const I18N = {
  nl: {
    // Header / nav
    wa: 'WhatsApp',
    // Staffelbanner
    staffel_titel: '🏷️ Staffelkorting op alle textiel',
    staffel_10: '10%',
    staffel_10_label: 'vanaf 10 stuks',
    staffel_20: '20%',
    staffel_20_label: 'vanaf 100 stuks',
    staffel_sub: 'Korting wordt automatisch verwerkt in je offerte.',
    // Voortgang
    prog: ['','Stap 1 van 6 — Textiel kiezen','Stap 2 van 6 — Drukpositie kiezen','Stap 3 van 6 — Druktechniek kiezen','Stap 4 van 6 — Maten & aantallen','Stap 5 van 6 — Ontwerp & gegevens','Stap 6 van 6 — Controleren & betalen'],
    // Winkelwagen
    wagen_titel: 'Winkelwagen',
    nog_product: '+ Nog een product',
    afrekenen: 'Afrekenen →',
    // Stap 1
    stap1_cat: 'Kies een categorie',
    stap1_mdl: 'Kies een model',
    stap1_kleur: 'Kies een kleur',
    overige_kleur: 'Overige kleur',
    overige_kleur_sub: 'Vul zelf een kleur in',
    bevestig_kleur: 'Bevestig kleur →',
    // Stap 2
    stap2_ttl: 'Kies een drukpositie',
    enkelvoudig: 'Enkelvoudig',
    combinatie: 'Combinatie',
    combinatie_sub: '= 2× bedrukking',
    pos_front: 'Voorkant', pos_back: 'Achterkant',
    pos_lborst: 'Linkerborst', pos_rborst: 'Rechterborst',
    pos_both: 'Voor + Achter', pos_lborst_back: 'Links + Achter',
    pos_rborst_back: 'Rechts + Achter', pos_borst_both: 'Links + Rechts',
    // Stap 3
    stap3_ttl: 'Kies een druktechniek',
    tech_dtf: 'DTF druk', tech_zeef: 'Zeefdruk', tech_bord: 'Borduren',
    dtf_desc: 'Foto-kwaliteit, elk aantal, full-colour. Ideaal voor kleine oplages en complexe designs.',
    zeef_desc: 'Scherpe, duurzame kleuren. Ideaal voor grote oplages.',
    zeef_note: '⚠️ Minimaal 25 stuks · 1–4 kleuren',
    bord_desc: 'Professioneel en duurzaam. Prijs op aanvraag.',
    bord_note: 'Prijs op aanvraag',
    // Stap 4
    stap4_ttl: 'Maten & aantallen',
    stap4_sub: 'Vul het gewenste aantal per maat in.',
    totaal_stuks: 'Totaal stuks',
    // Stap 5
    stap5_ttl: 'Jouw ontwerp & gegevens',
    geen_ontwerp: 'Geen ontwerp klaar?',
    geen_ontwerp_sub: 'Geen probleem — beschrijf je wens hieronder, dan nemen we contact op.',
    opmerkingen: 'Omschrijving / bijzonderheden',
    opmerkingen_ph: 'Bijv: logo op borst links, witte tekst op zwarte achtergrond...',
    voornaam: 'Voornaam *', achternaam: 'Achternaam *',
    email: 'E-mailadres *', telefoon: 'Telefoonnummer',
    bedrijf: 'Bedrijfsnaam (optioneel)',
    kvk: 'KVK-nummer', btwnr: 'BTW-nummer',
    straat: 'Straat + huisnummer *', postcode: 'Postcode *',
    stad: 'Plaats *', land: 'Land',
    offerte_opslaan: 'Offerte opslaan',
    toevoegen: 'Toevoegen aan bestelling',
    // Stap 6
    stap6_ttl: 'Controleer & betaal',
    jouw_bestelling: 'Jouw bestelling',
    textiel: 'Textiel', kleur: 'Kleur',
    drukpositie: 'Drukpositie', totaal_stuks2: 'Totaal stuks',
    maten: 'Aantallen per maat',
    techniek: 'Techniek', kleuren: 'Kleuren', prijs_ps: 'Prijs per stuk',
    subtotaal: 'Subtotaal ex BTW', btw: 'BTW 21%',
    totaal_incl: 'Totaal incl. BTW',
    bezorgadres: 'Bezorgadres', naam_sum: 'Naam',
    adres_sum: 'Adres', email_sum: 'E-mail',
    bord_info: '🪡 Borduren — prijs op aanvraag',
    bord_tekst: 'Je ontvangt binnen 1–2 werkdagen een offerte per e-mail op basis van het aantal steken in jouw logo.',
    bord_tip: 'Hoe duidelijker je wensen zijn in het opmerkingenveld (gewenste positie, kleur, grootte), hoe nauwkeuriger wij de prijs kunnen inschatten.',
    borduur_btn: 'Borduurwens versturen',
    betaal_info: 'Na betaling ontvang je een orderbevestiging per e-mail. We nemen contact op als we vragen hebben over je ontwerp.',
    levertijd: 'Verwachte levertijd:',
    levertijd_dtf: '5–8 werkdagen', levertijd_zeef: '6–10 werkdagen', levertijd_bord: '7–12 werkdagen',
    sim_pay: '🧪 Simuleer betaling (alleen voor testen)',
    betaal_slot: '🔒 Beveiligde betaling via PayPal · 21% BTW inbegrepen',
    // Succes
    ok_title: 'Bestelling ontvangen! 🎉',
    ok_stap1_t: 'Bevestiging', ok_stap1: 'Je ontvangt direct een e-mail met je orderoverzicht.',
    ok_stap2_t: 'Ontwerp check', ok_stap2: 'We bekijken je logo en nemen contact op bij vragen.',
    ok_stap3_t: 'Productie & verzending', ok_stap3: 'Na goedkeuring starten we direct.',
    ordernr: 'Ordernummer:',
    nieuwe_bestelling: 'Nieuwe bestelling',
    // Filter
    filter_alle: 'Alle merken',
    filter_sort_def: 'Standaard volgorde',
    filter_az: 'A → Z', filter_za: 'Z → A',
    filter_laag: 'Prijs laag → hoog', filter_hoog: 'Prijs hoog → laag',
    wis_filter: '✕ Wis filter',
    // Upload
    upload_klik: 'Klik om te uploaden',
    upload_of: 'of sleep hier naartoe',
    upload_types: 'AI, EPS, PDF, PNG, SVG – max. 50 MB',
    upload_toevoegen: '+ Bestand toevoegen',
    // Knoppen algemeen
    terug: '← Terug', volgende: 'Volgende stap →',
    terug_tech: '← Terug naar technieken',
    // Borduren stap apart
    bord_stap_ttl: 'Borduren op aanvraag',
    bord_stap_sub: 'Stuur je borduurwens in',
    // Landen
    land_nl: 'Nederland', land_be: 'België', land_de: 'Duitsland', land_other: 'Anders',
    // Offerte modal
    offerte_modal_ttl: 'Offerte opslaan',
    offerte_modal_sub: 'Geef je offerte een naam zodat je hem later makkelijk terugvindt.',
    offerte_naam_lbl: 'Naam',
    offerte_ph: 'Bijv. Teamshirts 2026',
    annuleren: 'Annuleren', opslaan: 'Opslaan',
    // Vanaf prijs
    vanaf: 'vanaf', per_stuk: 'p/stuk',
    // Zeefdruk kleuren
    zc_label: 'Aantal drukkleuren',
    // Wat gebeurt er nu
    wat_nu: 'Wat gebeurt er nu?',
    dtf_info: 'DTF: Geen minimale oplage, full colour. Geschikt voor katoen, polyester en nylon.',
    zeef_info: 'Zeefdruk: Maximaal 4 kleuren per ontwerp. Voordeligst bij 25+ stuks.',
    zeef_min_warn: 'Zeefdruk vereist minimaal 25 stuks. Voeg meer stuks toe of kies DTF voor kleine oplages.',
    bord_note2: 'We berekenen de prijs handmatig op basis van het aantal steken in jouw logo. Je ontvangt binnen 1–2 werkdagen een offerte per e-mail.',
    geen_ontwerp_full: 'Geen ontwerp klaar? Geen probleem — beschrijf je wens hieronder, dan nemen we contact op.',
    bord_prijs_note: 'De borduurkosten worden apart berekend op basis van het aantal steken in jouw logo.',
    betaal_info2: 'Na betaling ontvang je een orderbevestiging per e-mail. We nemen contact op als we vragen hebben over je ontwerp.',
    vis_voorkant: 'Voorkant', vis_achterkant: 'Achterkant',
    zeef_voor: 'Voorkant', zeef_achter: 'Achterkant',
    err_postcode_nl: 'Ongeldige postcode — verwacht formaat: 1234 AB',
    err_postcode_be: 'Ongeldige postcode — verwacht formaat: 1234',
    err_postcode_de: 'Ongeldige postcode — verwacht formaat: 12345',
    err_postcode_other: 'Controleer je postcode',
    err_zeef_min: 'Zeefdruk (voorkant) vereist minimaal 25 stuks.',
    err_zeef_min_b: 'Zeefdruk (achterkant) vereist minimaal 25 stuks.',
    bord_wensen: 'Omschrijving / wensen',
    bord_wensen_ph: 'Bijv: logo op borst links, gewenste kleur, type textiel...',
    bord_sturen: 'Borduurwens versturen',
    wagen_lbl: 'Jouw selectie',
    offerte_opgeslagen: '✓ Offerte opgeslagen in je account',
    levertijd_track: 'Na bevestiging van je ontwerp starten we direct met productie. Je ontvangt een track & trace zodra je bestelling is verzonden.',
    van: 'van', modellen: 'modellen',
    custom_ph: 'Bijv. bordeauxrood, lichtblauw, PANTONE 286 C...',
  },

  en: {
    wa: 'WhatsApp',
    staffel_titel: '🏷️ Volume discount on all garments',
    staffel_10: '10%', staffel_10_label: 'from 10 pieces',
    staffel_20: '20%', staffel_20_label: 'from 100 pieces',
    staffel_sub: 'Discount is automatically applied to your quote.',
    prog: ['','Step 1 of 6 — Choose garment','Step 2 of 6 — Print position','Step 3 of 6 — Print technique','Step 4 of 6 — Sizes & quantities','Step 5 of 6 — Design & details','Step 6 of 6 — Review & pay'],
    wagen_titel: 'Shopping cart',
    nog_product: '+ Add another product',
    afrekenen: 'Checkout →',
    stap1_cat: 'Choose a category',
    stap1_mdl: 'Choose a model',
    stap1_kleur: 'Choose a colour',
    overige_kleur: 'Other colour',
    overige_kleur_sub: 'Enter your own colour',
    bevestig_kleur: 'Confirm colour →',
    stap2_ttl: 'Choose a print position',
    enkelvoudig: 'Single',
    combinatie: 'Combination',
    combinatie_sub: '= 2× printing',
    pos_front: 'Front', pos_back: 'Back',
    pos_lborst: 'Left chest', pos_rborst: 'Right chest',
    pos_both: 'Front + Back', pos_lborst_back: 'Left + Back',
    pos_rborst_back: 'Right + Back', pos_borst_both: 'Left + Right',
    stap3_ttl: 'Choose a print technique',
    tech_dtf: 'DTF print', tech_zeef: 'Screen print', tech_bord: 'Embroidery',
    dtf_desc: 'Photo quality, any quantity, full-colour. Ideal for small runs and complex designs.',
    zeef_desc: 'Sharp, durable colours. Ideal for large quantities.',
    zeef_note: '⚠️ Minimum 25 pieces · 1–4 colours',
    bord_desc: 'Professional and durable. Price on request.',
    bord_note: 'Price on request',
    stap4_ttl: 'Sizes & quantities',
    stap4_sub: 'Enter the desired quantity per size.',
    totaal_stuks: 'Total pieces',
    stap5_ttl: 'Your design & details',
    geen_ontwerp: 'No design ready?',
    geen_ontwerp_sub: 'No problem — describe your wishes below and we\'ll get in touch.',
    opmerkingen: 'Description / notes',
    opmerkingen_ph: 'E.g. logo on left chest, white text on black background...',
    voornaam: 'First name *', achternaam: 'Last name *',
    email: 'Email address *', telefoon: 'Phone number',
    bedrijf: 'Company name (optional)',
    kvk: 'Chamber of Commerce number', btwnr: 'VAT number',
    straat: 'Street + house number *', postcode: 'Postal code *',
    stad: 'City *', land: 'Country',
    offerte_opslaan: 'Save quote',
    toevoegen: 'Add to order',
    stap6_ttl: 'Review & pay',
    jouw_bestelling: 'Your order',
    textiel: 'Garment', kleur: 'Colour',
    drukpositie: 'Print position', totaal_stuks2: 'Total pieces',
    maten: 'Quantities per size',
    techniek: 'Technique', kleuren: 'Colours', prijs_ps: 'Price per piece',
    subtotaal: 'Subtotal excl. VAT', btw: 'VAT 21%',
    totaal_incl: 'Total incl. VAT',
    bezorgadres: 'Delivery address', naam_sum: 'Name',
    adres_sum: 'Address', email_sum: 'Email',
    bord_info: '🪡 Embroidery — price on request',
    bord_tekst: 'You will receive a quote within 1–2 business days by email based on the stitch count of your logo.',
    bord_tip: 'The more clearly you describe your wishes in the notes field (position, colour, size), the more accurate our quote will be.',
    borduur_btn: 'Send embroidery request',
    betaal_info: 'After payment you will receive an order confirmation by email. We will contact you if we have questions about your design.',
    levertijd: 'Expected delivery:',
    levertijd_dtf: '5–8 business days', levertijd_zeef: '6–10 business days', levertijd_bord: '7–12 business days',
    sim_pay: '🧪 Simulate payment (testing only)',
    betaal_slot: '🔒 Secure payment via PayPal · VAT 21% included',
    ok_title: 'Order received! 🎉',
    ok_stap1_t: 'Confirmation', ok_stap1: 'You will immediately receive an email with your order summary.',
    ok_stap2_t: 'Design check', ok_stap2: 'We review your logo and contact you if we have questions.',
    ok_stap3_t: 'Production & shipping', ok_stap3: 'After approval we start immediately.',
    ordernr: 'Order number:',
    nieuwe_bestelling: 'New order',
    filter_alle: 'All brands',
    filter_sort_def: 'Default order',
    filter_az: 'A → Z', filter_za: 'Z → A',
    filter_laag: 'Price low → high', filter_hoog: 'Price high → low',
    wis_filter: '✕ Clear filter',
    upload_klik: 'Click to upload',
    upload_of: 'or drag and drop here',
    upload_types: 'AI, EPS, PDF, PNG, SVG – max. 50 MB',
    upload_toevoegen: '+ Add file',
    terug: '← Back', volgende: 'Next step →',
    terug_tech: '← Back to techniques',
    bord_stap_ttl: 'Embroidery on request',
    bord_stap_sub: 'Send your embroidery request',
    land_nl: 'Netherlands', land_be: 'Belgium', land_de: 'Germany', land_other: 'Other',
    offerte_modal_ttl: 'Save quote',
    offerte_modal_sub: 'Give your quote a name so you can easily find it later.',
    offerte_naam_lbl: 'Name',
    offerte_ph: 'E.g. Team shirts 2026',
    annuleren: 'Cancel', opslaan: 'Save',
    vanaf: 'from', per_stuk: 'per piece',
    zc_label: 'Number of print colours',
    wat_nu: 'What happens next?',
    dtf_info: 'DTF: No minimum quantity, full colour. Suitable for cotton, polyester and nylon.',
    zeef_info: 'Screen print: Maximum 4 colours per design. Most economical from 25+ pieces.',
    zeef_min_warn: 'Screen print requires a minimum of 25 pieces. Add more pieces or choose DTF for small runs.',
    bord_note2: 'We calculate the price manually based on the stitch count of your logo. You will receive a quote within 1–2 business days.',
    geen_ontwerp_full: 'No design ready? No problem — describe your wishes below and we will get in touch.',
    bord_prijs_note: 'Embroidery costs are calculated separately based on the stitch count of your logo.',
    betaal_info2: 'After payment you will receive an order confirmation by email. We will contact you if we have questions about your design.',
    vis_voorkant: 'Front', vis_achterkant: 'Back',
    zeef_voor: 'Front', zeef_achter: 'Back',
    err_postcode_nl: 'Invalid postal code — expected format: 1234 AB',
    err_postcode_be: 'Invalid postal code — expected format: 1234',
    err_postcode_de: 'Invalid postal code — expected format: 12345',
    err_postcode_other: 'Please check your postal code',
    err_zeef_min: 'Screen print (front) requires a minimum of 25 pieces.',
    err_zeef_min_b: 'Screen print (back) requires a minimum of 25 pieces.',
    bord_wensen: 'Description / wishes',
    bord_wensen_ph: 'E.g. logo on left chest, desired colour, type of garment...',
    bord_sturen: 'Send embroidery request',
    wagen_lbl: 'Your selection',
    offerte_opgeslagen: '✓ Quote saved to your account',
    levertijd_track: 'After design approval we start production immediately. You will receive a tracking code once your order has been shipped.',
    van: 'of', modellen: 'models',
    custom_ph: 'E.g. bordeaux red, light blue, PANTONE 286 C...',
  },

  de: {
    wa: 'WhatsApp',
    staffel_titel: '🏷️ Mengenrabatt auf alle Textilien',
    staffel_10: '10%', staffel_10_label: 'ab 10 Stück',
    staffel_20: '20%', staffel_20_label: 'ab 100 Stück',
    staffel_sub: 'Rabatt wird automatisch in Ihrem Angebot berücksichtigt.',
    prog: ['','Schritt 1 von 6 — Textil wählen','Schritt 2 von 6 — Druckposition','Schritt 3 von 6 — Drucktechnik','Schritt 4 von 6 — Größen & Mengen','Schritt 5 von 6 — Design & Daten','Schritt 6 von 6 — Prüfen & bezahlen'],
    wagen_titel: 'Warenkorb',
    nog_product: '+ Weiteres Produkt',
    afrekenen: 'Zur Kasse →',
    stap1_cat: 'Kategorie wählen',
    stap1_mdl: 'Modell wählen',
    stap1_kleur: 'Farbe wählen',
    overige_kleur: 'Andere Farbe',
    overige_kleur_sub: 'Eigene Farbe eingeben',
    bevestig_kleur: 'Farbe bestätigen →',
    stap2_ttl: 'Druckposition wählen',
    enkelvoudig: 'Einzeln',
    combinatie: 'Kombination',
    combinatie_sub: '= 2× Druck',
    pos_front: 'Vorderseite', pos_back: 'Rückseite',
    pos_lborst: 'Linke Brust', pos_rborst: 'Rechte Brust',
    pos_both: 'Vorder + Rückseite', pos_lborst_back: 'Links + Rücken',
    pos_rborst_back: 'Rechts + Rücken', pos_borst_both: 'Links + Rechts',
    stap3_ttl: 'Drucktechnik wählen',
    tech_dtf: 'DTF-Druck', tech_zeef: 'Siebdruck', tech_bord: 'Stickerei',
    dtf_desc: 'Fotoqualität, beliebige Menge, Vollfarbe. Ideal für kleine Auflagen und komplexe Designs.',
    zeef_desc: 'Scharfe, langlebige Farben. Ideal für große Auflagen.',
    zeef_note: '⚠️ Mindestens 25 Stück · 1–4 Farben',
    bord_desc: 'Professionell und langlebig. Preis auf Anfrage.',
    bord_note: 'Preis auf Anfrage',
    stap4_ttl: 'Größen & Mengen',
    stap4_sub: 'Gewünschte Menge pro Größe eingeben.',
    totaal_stuks: 'Stück gesamt',
    stap5_ttl: 'Ihr Design & Daten',
    geen_ontwerp: 'Noch kein Design?',
    geen_ontwerp_sub: 'Kein Problem — beschreiben Sie Ihren Wunsch unten und wir melden uns.',
    opmerkingen: 'Beschreibung / Hinweise',
    opmerkingen_ph: 'Z.B. Logo auf linker Brust, weißer Text auf schwarzem Hintergrund...',
    voornaam: 'Vorname *', achternaam: 'Nachname *',
    email: 'E-Mail-Adresse *', telefoon: 'Telefonnummer',
    bedrijf: 'Firmenname (optional)',
    kvk: 'Handelsregisternummer', btwnr: 'USt-IdNr.',
    straat: 'Straße + Hausnummer *', postcode: 'Postleitzahl *',
    stad: 'Ort *', land: 'Land',
    offerte_opslaan: 'Angebot speichern',
    toevoegen: 'Zur Bestellung hinzufügen',
    stap6_ttl: 'Prüfen & bezahlen',
    jouw_bestelling: 'Ihre Bestellung',
    textiel: 'Textil', kleur: 'Farbe',
    drukpositie: 'Druckposition', totaal_stuks2: 'Stück gesamt',
    maten: 'Mengen pro Größe',
    techniek: 'Technik', kleuren: 'Farben', prijs_ps: 'Preis pro Stück',
    subtotaal: 'Zwischensumme exkl. MwSt.', btw: 'MwSt. 21%',
    totaal_incl: 'Gesamt inkl. MwSt.',
    bezorgadres: 'Lieferadresse', naam_sum: 'Name',
    adres_sum: 'Adresse', email_sum: 'E-Mail',
    bord_info: '🪡 Stickerei — Preis auf Anfrage',
    bord_tekst: 'Sie erhalten innerhalb von 1–2 Werktagen ein Angebot per E-Mail basierend auf der Stichdichte Ihres Logos.',
    bord_tip: 'Je genauer Sie Ihre Wünsche beschreiben (Position, Farbe, Größe), desto genauer unser Angebot.',
    borduur_btn: 'Stickwunsch senden',
    betaal_info: 'Nach der Zahlung erhalten Sie eine Bestellbestätigung per E-Mail.',
    levertijd: 'Voraussichtliche Lieferzeit:',
    levertijd_dtf: '5–8 Werktage', levertijd_zeef: '6–10 Werktage', levertijd_bord: '7–12 Werktage',
    sim_pay: '🧪 Zahlung simulieren (nur zum Testen)',
    betaal_slot: '🔒 Sichere Zahlung via PayPal · MwSt. 21% inklusive',
    ok_title: 'Bestellung eingegangen! 🎉',
    ok_stap1_t: 'Bestätigung', ok_stap1: 'Sie erhalten sofort eine E-Mail mit Ihrer Bestellübersicht.',
    ok_stap2_t: 'Designprüfung', ok_stap2: 'Wir prüfen Ihr Logo und melden uns bei Fragen.',
    ok_stap3_t: 'Produktion & Versand', ok_stap3: 'Nach Freigabe starten wir sofort.',
    ordernr: 'Bestellnummer:',
    nieuwe_bestelling: 'Neue Bestellung',
    filter_alle: 'Alle Marken',
    filter_sort_def: 'Standardreihenfolge',
    filter_az: 'A → Z', filter_za: 'Z → A',
    filter_laag: 'Preis aufsteigend', filter_hoog: 'Preis absteigend',
    wis_filter: '✕ Filter löschen',
    upload_klik: 'Klicken zum Hochladen',
    upload_of: 'oder hierher ziehen',
    upload_types: 'AI, EPS, PDF, PNG, SVG – max. 50 MB',
    upload_toevoegen: '+ Datei hinzufügen',
    terug: '← Zurück', volgende: 'Nächster Schritt →',
    terug_tech: '← Zurück zu Techniken',
    bord_stap_ttl: 'Stickerei auf Anfrage',
    bord_stap_sub: 'Stickwunsch einsenden',
    land_nl: 'Niederlande', land_be: 'Belgien', land_de: 'Deutschland', land_other: 'Sonstiges',
    offerte_modal_ttl: 'Angebot speichern',
    offerte_modal_sub: 'Geben Sie Ihrem Angebot einen Namen.',
    offerte_naam_lbl: 'Name',
    offerte_ph: 'Z.B. Teamshirts 2026',
    annuleren: 'Abbrechen', opslaan: 'Speichern',
    vanaf: 'ab', per_stuk: 'pro Stück',
    zc_label: 'Anzahl Druckfarben',
    wat_nu: 'Was passiert als Nächstes?',
    dtf_info: 'DTF: Keine Mindestmenge, Vollfarbe. Geeignet für Baumwolle, Polyester und Nylon.',
    zeef_info: 'Siebdruck: Maximal 4 Farben pro Design. Am günstigsten ab 25+ Stück.',
    zeef_min_warn: 'Siebdruck erfordert mindestens 25 Stück. Mehr Stück hinzufügen oder DTF wählen.',
    bord_note2: 'Wir berechnen den Preis manuell basierend auf der Stichdichte. Angebot innerhalb von 1–2 Werktagen.',
    geen_ontwerp_full: 'Noch kein Design? Kein Problem — beschreiben Sie Ihren Wunsch und wir melden uns.',
    bord_prijs_note: 'Die Stickkosten werden separat basierend auf der Stichdichte berechnet.',
    betaal_info2: 'Nach der Zahlung erhalten Sie eine Bestellbestätigung per E-Mail.',
    vis_voorkant: 'Vorderseite', vis_achterkant: 'Rückseite',
    zeef_voor: 'Vorderseite', zeef_achter: 'Rückseite',
    err_postcode_nl: 'Ungültige PLZ — erwartet: 1234 AB',
    err_postcode_be: 'Ungültige PLZ — erwartet: 1234',
    err_postcode_de: 'Ungültige PLZ — erwartet: 12345',
    err_postcode_other: 'Bitte Postleitzahl prüfen',
    err_zeef_min: 'Siebdruck (Vorderseite) erfordert mindestens 25 Stück.',
    err_zeef_min_b: 'Siebdruck (Rückseite) erfordert mindestens 25 Stück.',
    bord_wensen: 'Beschreibung / Wünsche',
    bord_wensen_ph: 'Z.B. Logo auf linker Brust, gewünschte Farbe, Textilart...',
    bord_sturen: 'Stickwunsch senden',
    wagen_lbl: 'Ihre Auswahl',
    offerte_opgeslagen: '✓ Angebot in Ihrem Konto gespeichert',
    levertijd_track: 'Nach Designfreigabe starten wir sofort. Sie erhalten einen Tracking-Code nach dem Versand.',
    van: 'von', modellen: 'Modelle',
    custom_ph: 'Z.B. Bordeauxrot, Hellblau, PANTONE 286 C...',
  },

  no: {
    wa: 'WhatsApp',
    staffel_titel: '🏷️ Volumrabatt på alle tekstiler',
    staffel_10: '10%', staffel_10_label: 'fra 10 stk',
    staffel_20: '20%', staffel_20_label: 'fra 100 stk',
    staffel_sub: 'Rabatt beregnes automatisk i tilbudet ditt.',
    prog: ['','Trinn 1 av 6 — Velg tekstil','Trinn 2 av 6 — Trykkposisjon','Trinn 3 av 6 — Trykkteknikk','Trinn 4 av 6 — Størrelser & antall','Trinn 5 av 6 — Design & opplysninger','Trinn 6 av 6 — Kontroller & betal'],
    wagen_titel: 'Handlekurv',
    nog_product: '+ Legg til et produkt',
    afrekenen: 'Til kassen →',
    stap1_cat: 'Velg en kategori',
    stap1_mdl: 'Velg en modell',
    stap1_kleur: 'Velg en farge',
    overige_kleur: 'Annen farge',
    overige_kleur_sub: 'Skriv inn egen farge',
    bevestig_kleur: 'Bekreft farge →',
    stap2_ttl: 'Velg trykkposisjon',
    enkelvoudig: 'Enkelt',
    combinatie: 'Kombinasjon',
    combinatie_sub: '= 2× trykk',
    pos_front: 'Forside', pos_back: 'Bakside',
    pos_lborst: 'Venstre bryst', pos_rborst: 'Høyre bryst',
    pos_both: 'For + Bak', pos_lborst_back: 'Venstre + Bak',
    pos_rborst_back: 'Høyre + Bak', pos_borst_both: 'Venstre + Høyre',
    stap3_ttl: 'Velg trykkteknikk',
    tech_dtf: 'DTF-trykk', tech_zeef: 'Silketrykk', tech_bord: 'Broderi',
    dtf_desc: 'Fotokvalitet, valgfritt antall, full-farge. Ideelt for små opplag og komplekse design.',
    zeef_desc: 'Skarpe, holdbare farger. Ideelt for store opplag.',
    zeef_note: '⚠️ Minimum 25 stk · 1–4 farger',
    bord_desc: 'Profesjonelt og holdbart. Pris på forespørsel.',
    bord_note: 'Pris på forespørsel',
    stap4_ttl: 'Størrelser & antall',
    stap4_sub: 'Fyll inn ønsket antall per størrelse.',
    totaal_stuks: 'Totalt antall',
    stap5_ttl: 'Ditt design & opplysninger',
    geen_ontwerp: 'Intet design klart?',
    geen_ontwerp_sub: 'Ingen problem — beskriv ønsket nedenfor, så tar vi kontakt.',
    opmerkingen: 'Beskrivelse / notater',
    opmerkingen_ph: 'F.eks. logo på venstre bryst, hvit tekst på svart bakgrunn...',
    voornaam: 'Fornavn *', achternaam: 'Etternavn *',
    email: 'E-postadresse *', telefoon: 'Telefonnummer',
    bedrijf: 'Firmanavn (valgfritt)',
    kvk: 'Organisasjonsnummer', btwnr: 'MVA-nummer',
    straat: 'Gate + husnummer *', postcode: 'Postnummer *',
    stad: 'Sted *', land: 'Land',
    offerte_opslaan: 'Lagre tilbud',
    toevoegen: 'Legg til i bestilling',
    stap6_ttl: 'Kontroller & betal',
    jouw_bestelling: 'Din bestilling',
    textiel: 'Tekstil', kleur: 'Farge',
    drukpositie: 'Trykkposisjon', totaal_stuks2: 'Totalt antall',
    maten: 'Antall per størrelse',
    techniek: 'Teknikk', kleuren: 'Farger', prijs_ps: 'Pris per stk',
    subtotaal: 'Delsum ekskl. MVA', btw: 'MVA 21%',
    totaal_incl: 'Totalt inkl. MVA',
    bezorgadres: 'Leveringsadresse', naam_sum: 'Navn',
    adres_sum: 'Adresse', email_sum: 'E-post',
    bord_info: '🪡 Broderi — pris på forespørsel',
    bord_tekst: 'Du mottar et tilbud innen 1–2 virkedager på e-post basert på antall sting i logoen din.',
    bord_tip: 'Jo tydeligere du beskriver ønskene dine (posisjon, farge, størrelse), jo mer nøyaktig blir tilbudet.',
    borduur_btn: 'Send broderiønske',
    betaal_info: 'Etter betaling mottar du en ordrebekreftelse på e-post.',
    levertijd: 'Forventet leveringstid:',
    levertijd_dtf: '5–8 virkedager', levertijd_zeef: '6–10 virkedager', levertijd_bord: '7–12 virkedager',
    sim_pay: '🧪 Simuler betaling (kun for testing)',
    betaal_slot: '🔒 Sikker betaling via PayPal · MVA 21% inkludert',
    ok_title: 'Bestilling mottatt! 🎉',
    ok_stap1_t: 'Bekreftelse', ok_stap1: 'Du mottar umiddelbart en e-post med din bestillingsoversikt.',
    ok_stap2_t: 'Designsjekk', ok_stap2: 'Vi sjekker logoen din og tar kontakt ved spørsmål.',
    ok_stap3_t: 'Produksjon & levering', ok_stap3: 'Etter godkjenning starter vi umiddelbart.',
    ordernr: 'Ordrenummer:',
    nieuwe_bestelling: 'Ny bestilling',
    filter_alle: 'Alle merker',
    filter_sort_def: 'Standard rekkefølge',
    filter_az: 'A → Å', filter_za: 'Å → A',
    filter_laag: 'Pris lav → høy', filter_hoog: 'Pris høy → lav',
    wis_filter: '✕ Fjern filter',
    upload_klik: 'Klikk for å laste opp',
    upload_of: 'eller dra hit',
    upload_types: 'AI, EPS, PDF, PNG, SVG – maks. 50 MB',
    upload_toevoegen: '+ Legg til fil',
    terug: '← Tilbake', volgende: 'Neste trinn →',
    terug_tech: '← Tilbake til teknikker',
    bord_stap_ttl: 'Broderi på forespørsel',
    bord_stap_sub: 'Send broderiønsket ditt',
    land_nl: 'Nederland', land_be: 'Belgia', land_de: 'Tyskland', land_other: 'Annet',
    offerte_modal_ttl: 'Lagre tilbud',
    offerte_modal_sub: 'Gi tilbudet et navn så du enkelt finner det igjen.',
    offerte_naam_lbl: 'Navn',
    offerte_ph: 'F.eks. Teamskjorter 2026',
    annuleren: 'Avbryt', opslaan: 'Lagre',
    vanaf: 'fra', per_stuk: 'pr. stk',
    zc_label: 'Antall trykkfarger',
    wat_nu: 'Hva skjer nå?',
    dtf_info: 'DTF: Ingen minsteoplan, full-farge. Egnet for bomull, polyester og nylon.',
    zeef_info: 'Silketrykk: Maksimalt 4 farger per design. Rimeligst fra 25+ stk.',
    zeef_min_warn: 'Silketrykk krever minimum 25 stk. Legg til flere stk eller velg DTF.',
    bord_note2: 'Vi beregner prisen manuelt basert på antall sting i logoen din. Tilbud innen 1–2 virkedager.',
    geen_ontwerp_full: 'Intet design klart? Ingen problem — beskriv ønsket nedenfor, så tar vi kontakt.',
    bord_prijs_note: 'Broderikostandene beregnes separat basert på antall sting i logoen din.',
    betaal_info2: 'Etter betaling mottar du en ordrebekreftelse på e-post.',
    vis_voorkant: 'Forside', vis_achterkant: 'Bakside',
    zeef_voor: 'Forside', zeef_achter: 'Bakside',
    err_postcode_nl: 'Ugyldig postnummer — forventet: 1234 AB',
    err_postcode_be: 'Ugyldig postnummer — forventet: 1234',
    err_postcode_de: 'Ugyldig postnummer — forventet: 12345',
    err_postcode_other: 'Sjekk postnummeret ditt',
    err_zeef_min: 'Silketrykk (forside) krever minimum 25 stk.',
    err_zeef_min_b: 'Silketrykk (bakside) krever minimum 25 stk.',
    bord_wensen: 'Beskrivelse / ønsker',
    bord_wensen_ph: 'F.eks. logo på venstre bryst, ønsket farge, type tekstil...',
    bord_sturen: 'Send broderiønske',
    wagen_lbl: 'Ditt utvalg',
    offerte_opgeslagen: '✓ Tilbud lagret i kontoen din',
    levertijd_track: 'Etter designgodkjenning starter vi umiddelbart. Du mottar sporingskode etter forsendelse.',
    van: 'av', modellen: 'modeller',
    custom_ph: 'F.eks. bordeauxrød, lyseblå, PANTONE 286 C...',
  },
};
    bevestig: 'Bekreft farge →',

function t(key){ return (I18N[TAAL]||I18N.nl)[key] || (I18N.nl)[key] || key; }

function toggleLangMenu(){
  const m=document.getElementById('lang-menu');
  if(m) m.style.display=m.style.display==='none'?'block':'none';
}
// Sluit menu bij klik buiten
document.addEventListener('click',ev=>{
  const btn=document.getElementById('lang-btn');
  const menu=document.getElementById('lang-menu');
  if(menu&&btn&&!btn.contains(ev.target)&&!menu.contains(ev.target)){
    menu.style.display='none';
  }
});

const TAAL_META={
  nl:{vlag:'🇳🇱',code:'NL'}, en:{vlag:'🇬🇧',code:'EN'},
  de:{vlag:'🇩🇪',code:'DE'}, no:{vlag:'🇳🇴',code:'NO'},
};

function setTaal(taal){
  TAAL=taal;
  // Update knop
  const meta=TAAL_META[taal];
  const vlag=document.getElementById('lang-vlag');
  const code=document.getElementById('lang-code');
  if(vlag) vlag.textContent=meta.vlag;
  if(code) code.textContent=meta.code;
  // Markeer actieve taal
  ['nl','en','de','no'].forEach(l=>{
    const btn=document.getElementById('lang-'+l);
    if(btn) btn.classList.toggle('actief',l===taal);
  });
  // Sla taalvoorkeur op
  try { localStorage.setItem('mm_taal', taal); } catch(e){}
  // Sluit menu
  const menu=document.getElementById('lang-menu');
  if(menu) menu.style.display='none';
  // Vertaal alles
  vertaalUI();
  // Herlaad categoriegrid (categorienamen zijn taalafhankelijk)
  if(typeof buildCatGrid==='function') buildCatGrid();
}

function vertaalUI(){
  // Staffelbanner
  const s_titel=document.querySelector('.staffel-i18n-titel');
  if(s_titel) s_titel.textContent=t('staffel_titel');
  const s_sub=document.querySelector('.staffel-i18n-sub');
  if(s_sub) s_sub.textContent=t('staffel_sub');

  // Voortgangslabels
  const PROG_LABELS={1:t('prog')[1],2:t('prog')[2],3:t('prog')[3],4:t('prog')[4],5:t('prog')[5],6:t('prog')[6]};
  // update huidige prog label
  const progLbl=document.getElementById('prog-lbl');
  if(progLbl){
    const huidig=parseInt(progLbl.dataset.stap||'1');
    if(PROG_LABELS[huidig]) progLbl.innerHTML=PROG_LABELS[huidig];
  }

  // Alle data-i18n elementen
  document.querySelectorAll('[data-i18n]').forEach(el=>{
    const key=el.getAttribute('data-i18n');
    const attr=el.getAttribute('data-i18n-attr');
    const val=t(key);
    if(attr) el.setAttribute(attr,val);
    else el.textContent=val;
  });

  // Landen dropdown
  const country=document.getElementById('country');
  if(country){
    const opts=country.options;
    const map=['land_nl','land_be','land_de','land_other'];
    for(let i=0;i<opts.length&&i<map.length;i++) opts[i].text=t(map[i]);
  }

  // Textarea placeholders apart updaten (data-i18n-attr="placeholder")
  document.querySelectorAll('[data-i18n-attr="placeholder"]').forEach(el=>{
    const key=el.getAttribute('data-i18n');
    if(key) el.placeholder=t(key);
  });

  // Filter dropdowns
  const fMerk=document.getElementById('filter-merk');
  if(fMerk&&fMerk.options[0]) fMerk.options[0].text=t('filter_alle');
  const fSort=document.getElementById('filter-sort');
  if(fSort){
    const sortMap=['filter_sort_def','filter_az','filter_za','filter_laag','filter_hoog'];
    for(let i=0;i<fSort.options.length&&i<sortMap.length;i++) fSort.options[i].text=t(sortMap[i]);
  }
}


// ── Upload naar eigen server via PHP handler ──────────────────────────────────
async function uploadToCloudinary(file, folder){
  if(!file) return null;
  try {
    const fd = new FormData();
    fd.append('action', 'upload');
    fd.append('bestand', file);
    fd.append('folder', folder.replace(/[/]/g, '-'));
    const res = await fetch(HANDLER_URL, {method:'POST', body:fd});
    const data = await res.json();
    if(data.error){console.warn('Upload fout:', data.error); return null;}
    return data.url || null;
  } catch(err){console.warn('Upload fout:', err); return null;}
}

// ── DATA (Ralawise productcatalogus) ──────────────────────────────────────────
// Gegenereerd uit CustomerDataFullDutch.csv — merken: BYB, AWDis Just Hoods/T's, Gildan, Asquith & Fox, Larkwood
// Babybugz vervangen door Larkwood (Babybugz volledig discontinued in Ralawise)
// Categorienamen via i18n — zie CAT_NAMEN object
const CAT_NAMEN={
  nl:{ tshirt:'T-shirts', sweater:'Sweaters', hoodie:'Hoodies', polo:"Polo's", cap:'Caps', tas:'Tassen', baby:'Baby & Kids' },
  en:{ tshirt:'T-shirts', sweater:'Sweatshirts', hoodie:'Hoodies', polo:'Polos', cap:'Caps', tas:'Bags', baby:'Baby & Kids' },
  de:{ tshirt:'T-Shirts', sweater:'Sweatshirts', hoodie:'Hoodies', polo:'Poloshirts', cap:'Caps', tas:'Taschen', baby:'Baby & Kinder' },
  no:{ tshirt:'T-skjorter', sweater:'Gensere', hoodie:'Hettegensere', polo:'Poloer', cap:'Caps', tas:'Vesker', baby:'Baby & barn' },
};
function catNaam(id){ return (CAT_NAMEN[TAAL]||CAT_NAMEN.nl)[id]||id; }

const CATS=[
  {id:'tshirt', icon:'tshirt',  sizes:['XS','S','M','L','XL','2XL','3XL']},
  {id:'sweater',icon:'sweater', sizes:['XS','S','M','L','XL','2XL']},
  {id:'hoodie', icon:'hoodie',  sizes:['XS','S','M','L','XL','2XL','3XL']},
  {id:'polo',   icon:'polo',    sizes:['XS','S','M','L','XL','2XL']},
  {id:'cap',    icon:'cap',     sizes:['One size']},
  {id:'tas',    icon:'tas',     sizes:['One size']},
  {id:'jack',   name:'Jassen',    icon:'jack',    sizes:['XS','S','M','L','XL','2XL']},
  {id:'baby',   icon:'baby',  sizes:['0-3m','3-6m','6-12m','1-2j','2-3j','3-4j','5-6j','7-8j']},
];
const MODELS={
  tshirt:[
    {id:'gd005', brand:'Gildan', name:'Heavy Cotton™ adult t-shirt', sku:'GD005', tier:'budget', inkoop:2.86, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/40991e8a/69296c3d5fa5b59a3a6ba864/b17ef891/GD005_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ACHR", "name": "Antique Cherry Red", "hex": "#9D0033", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce299d/5ea63c86/GD005_AntiqueCherryRed_FT.jpg"}, {"code": "AIGR", "name": "Antique Irish Green", "hex": "#08DB00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce299e/915c00da/GD005_AntiqueIrishGreen_FT.jpg"}, {"code": "AJDO", "name": "Antique Jade Dome", "hex": "#036D47", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce299f/22805e52/GD005_AntiqueJadeDome_FT.jpg"}, {"code": "AORA", "name": "Antique Orange", "hex": "#CC1E04", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a0/4a2c9dda/GD005_AntiqueOrange_FT.jpg"}, {"code": "ASAP", "name": "Antique Sapphire", "hex": "#007780", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a1/94aecadc/GD005_AntiqueSapphire_FT.jpg"}, {"code": "ASHG", "name": "Ash", "hex": "#C5CBC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a2/4d75cecb/GD005_Ash_FT.jpg"}, {"code": "AZAL", "name": "Azalea", "hex": "#FF54F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a3/01191d9a/GD005_Azalea_FT.jpg"}, {"code": "BBER", "name": "Blackberry", "hex": "#000030", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/83218b8f/68c12dd4cf659c3a55ed8d4a/5c5c3e3deb9f5b1c75ce29a50adb5e39.jpg"}, {"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a4/988837d5/GD005_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a7/c714762e/GD005_CarolinaBlue_FT.jpg"}, {"code": "CDRD", "name": "Cardinal Red", "hex": "#840042", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a6/c07d572d/GD005_CardinalRed_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*†", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a8/1cb4f199/GD005_Charcoal_FT.jpg"}, {"code": "COBA", "name": "Cobalt", "hex": "#0005FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29a9/47e3d899/GD005_Cobalt_FT.jpg"}, {"code": "CSIL", "name": "Cornsilk", "hex": "#EFFF72", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29aa/85c9e345/GD005_Cornsilk_FT.jpg"}, {"code": "DAIS", "name": "Daisy*†", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29ab/853d3cff/GD005_Daisy_FT.jpg"}, {"code": "DCHO", "name": "Dark Chocolate*†", "hex": "#0C0A0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29ac/ceb40110/GD005_DarkChocolate_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*†", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29ad/9534b9df/GD005_DarkHeather_FT.jpg"}, {"code": "FADG", "name": "Fan Dark Green", "hex": "#004B2B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f5/GD005_FanDarkGreen_FT.jpg"}, {"code": "FADP", "name": "Fan Dark Purple", "hex": "#361850", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f6/GD005_FanDarkPurple_FT.jpg"}, {"code": "FADR", "name": "Fan Deep Royal", "hex": "#011E5A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f7/GD005_FanDeepRoyal_FT.jpg"}, {"code": "FAMG", "name": "Fan Marine Green", "hex": "#043E3A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f8/GD005_FanMarineGreen_FT.jpg"}, {"code": "FORE", "name": "Forest*†", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c3800004b/1d9c4ffe/GD005_Forest_FT.jpg"}, {"code": "GOLD", "name": "Gold*†", "hex": "#FFAF05", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29af/b4e5e4a1/GD005_Gold_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29b0/4136d041/GD005_GraphiteHeather_FT.jpg"}, {"code": "HELI", "name": "Heliconia", "hex": "#FF14D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29b2/f315ce45/GD005_Heliconia_FT.jpg"}, {"code": "HSAP", "name": "Heather Sapphire", "hex": "#0C96E5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29b1/10cb087a/GD005_HeatherSapphire_FT.jpg"}, {"code": "IGRN", "name": "Irish Green*†", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29b4/9fea7ce8/GD005_IrishGreen_FT.jpg"}, {"code": "INBL", "name": "Indigo Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29b3/c1478562/GD005_IndigoBlue_FT.jpg"}, {"code": "KIWI", "name": "Kiwi", "hex": "#78D527", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29b5/dbc37cbe/GD005_Kiwi_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29bb/c8a00881/GD005_LightBlue_FT.jpg"}]},
    {id:'by102', brand:'Build Your Brand', name:'Heavy oversized tee', sku:'BY102', tier:'standaard', inkoop:8.30, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/776cb439/69296bce5fa5b59a3a6b9787/BY102_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "AMBE", "name": "Amber*", "hex": "#E2BCAA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000056/4f694187/BY102_Amber_FT.jpg"}, {"code": "BALB", "name": "Baltic Blue*", "hex": "#9AC7E0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/520cdd18/6564b03bd9e4aa0180000064/BY102_BalticBlue_FT.jpg"}, {"code": "BARK", "name": "Bark*", "hex": "#8E5745", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/61a7ce3cfe9cd9426c000025/e8aa429e/BY102_Bark_FT.jpg"}, {"code": "BEBL", "name": "Beryl Blue*", "hex": "#9DE3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000057/cc959186/BY102_BerylBlue_FT.jpg"}, {"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5dfb3d466aa6110ae20009c4/87396495/BY102_Black_FT.jpg"}, {"code": "BOTT", "name": "Bottle Green*", "hex": "#394D54", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000058/f771af74/BY102_BottleGreen_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown*", "hex": "#664037", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000007/BY102_ChocolateBrown_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*", "hex": "#3F3D3E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5fcf5c4397138200187360d4/5fc2c99a/BY102_Charcoal_FT.jpg"}, {"code": "CHER", "name": "Cherry*", "hex": "#72313D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000059/7587ae9c/BY102_Cherry_FT.jpg"}, {"code": "CIRD", "name": "City Red*", "hex": "#F22E3E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/6564b03bd9e4aa0180000065/d86a50bd/BY102_CityRed_FT.jpg"}, {"code": "CLOU", "name": "Cloud*", "hex": "#D0C8BF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f00012e/BY102_Cloud_FT.jpg"}, {"code": "COBA", "name": "Cobalt Blue*", "hex": "#0056B5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b2300005a/5a037301/BY102_CobaltBlue_FT.jpg"}, {"code": "DBLU", "name": "Dark Blue*", "hex": "#273363", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b2300005b/41df6c95/BY102_DarkBlue_FT.jpg"}, {"code": "DKGY", "name": "Dark Grey*", "hex": "#5B5863", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b2300005c/cfbc456b/BY102_DarkGrey_FT.jpg"}, {"code": "DSRO", "name": "Dusk Rose*", "hex": "#BFACA6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/61a7ce3cfe9cd9426c000026/0014ae94/BY102_DuskRose_FT.jpg"}, {"code": "FORA", "name": "Forgotten Orange*", "hex": "#E28538", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b2300005d/5e0093f5/BY102_ForgottenOrange_FT.jpg"}, {"code": "GAGR", "name": "Grass Green*", "hex": "#00BFA0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000008/BY102_GrassGreen_FT.jpg"}, {"code": "GREE", "name": "Green*", "hex": "#006359", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b2300005e/33f54c13/BY102_Green_FT.jpg"}, {"code": "GREY", "name": "Grey", "hex": "#CCC7C5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5dfb3d466aa6110ae20009c5/9a6478c3/BY102_Grey_FT.jpg"}, {"code": "HIPI", "name": "Hibiskus Pink*", "hex": "#E24A73", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/520cdd18/6564b03bd9e4aa0180000066/BY102_HibiskusPink_FT.jpg"}, {"code": "INTB", "name": "Intense Blue*", "hex": "#3074C9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000031/BY102_IntenseBlue_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt*", "hex": "#D3D3D3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/61a7ce3cfe9cd9426c000027/e2ca87c3/BY102_LightAsphalt_FT.jpg"}, {"code": "LGRE", "name": "Light Grey*", "hex": "#C6C2BE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b2300005f/802db817/BY102_LightGrey_FT.jpg"}, {"code": "LILA", "name": "Lilac*", "hex": "#D2C7D8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000061/244c4ac0/BY102_Lilac_FT.jpg"}, {"code": "LNAV", "name": "Light Navy*", "hex": "#414477", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000060/0381e287/BY102_LightNavy_FT.jpg"}, {"code": "MAGN", "name": "Magnet*", "hex": "#3D3B35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000062/f96b23a3/BY102_Magnet_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#383442", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5fcf5c4397138200187360d5/925d165f/BY102_Navy_FT.jpg"}, {"code": "OLIV", "name": "Olive*", "hex": "#685E4C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/61a7ce3cfe9cd9426c000028/9691b4fc/BY102_Olive_FT.jpg"}, {"code": "PLPU", "name": "Plum Purple*", "hex": "#493241", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000049/BY102_PlumPurple_FT.jpg"}, {"code": "POBL", "name": "Powder Blue*", "hex": "#D3E3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2060447f/674085a63d2d8f026500001b/b6aba03a/BY102_PowderBlue_FT.jpg"}]},
    {id:'gd001', brand:'Gildan', name:'Softstyle™ adult ringspun t-shirt', sku:'GD001', tier:'budget', inkoop:2.65, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f2895508/69296bf05fa5b59a3a6b9c8d/GD001_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "XS", "4XL", "5XL"], colors:[{"code": "ACHR", "name": "Antique Cherry Red*", "hex": "#9D0033", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2900/d17f02a7/GD001_AntiqueCherryRed_FT.jpg"}, {"code": "ASAP", "name": "Antique Sapphire*", "hex": "#007780", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2902/d7fdbe89/GD001_AntiqueSapphire_FT.jpg"}, {"code": "AZAL", "name": "Azalea*", "hex": "#FF54F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2903/68d6be11/GD001_Azalea_FT.jpg"}, {"code": "BLAC", "name": "Black*†△**", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2905/4c461f52/GD001_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue*", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c38000048/762f76cc/GD001_CarolinaBlue_FT.jpg"}, {"code": "CDRD", "name": "Cardinal Red", "hex": "#840042", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2907/527432ba/GD001_CardinalRed_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*†△**", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2909/f24eba14/GD001_Charcoal_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red*", "hex": "#AF0021", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce290a/6446d1e7/GD001_CherryRed_FT.jpg"}, {"code": "DAIS", "name": "Daisy*†", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce290f/d5ea4790/GD001_Daisy_FT.jpg"}, {"code": "DCHO", "name": "Dark Chocolate*", "hex": "#0C0A0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2910/6e5d8340/GD001_DarkChocolate_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*†△**", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2911/0415cce2/GD001_DarkHeather_FT.jpg"}, {"code": "FACP", "name": "Fan Candy Pink*", "hex": "#F6A8C6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001e4/GD001_FanCandyPink_FT.jpg"}, {"code": "FADG", "name": "Fan Dark Green*", "hex": "#195036", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001e5/GD001_FanDarkGreen_FT.jpg"}, {"code": "FADP", "name": "Fan Dark Purple*", "hex": "#37185B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001e6/GD001_FanDarkPurple_FT.jpg"}, {"code": "FADR", "name": "Fan Deep Royal*", "hex": "#1C327D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001e7/GD001_FanDeepRoyal_FT.jpg"}, {"code": "FAMG", "name": "Fan Marine Green*", "hex": "#033635", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001e8/GD001_FanMarineGreen_FT.jpg"}, {"code": "FATO", "name": "Fan Texas Orange*", "hex": "#B46331", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001e9/GD001_FanTexasOrange_FT.jpg"}, {"code": "FORE", "name": "Forest Green*", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c38000049/ecd9870d/GD001_ForestGreen_FT.jpg"}, {"code": "GOLD", "name": "Gold*", "hex": "#F5A109", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001ea/GD001_Gold_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather*†**", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2914/1c3edd92/GD001_GraphiteHeather_FT.jpg"}, {"code": "HELI", "name": "Heliconia*", "hex": "#FF14D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2921/f48e408d/GD001_Heliconia_FT.jpg"}, {"code": "HMGR", "name": "Heather Military Green*†", "hex": "#596658", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2919/b04230e6/GD001_HeatherMilitaryGreen_FT.jpg"}, {"code": "HNAV", "name": "Heather Navy*†△", "hex": "#131F29", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce291a/dbd07586/GD001_HeatherNavy_FT.jpg"}, {"code": "HROY", "name": "Heather Royal*†△", "hex": "#4C87FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce291f/2aeece30/GD001_HeatherRoyal_FT.jpg"}, {"code": "ICGY", "name": "Ice Grey*", "hex": "#D1CBC4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001eb/GD001_IceGrey_FT.jpg"}, {"code": "IGRN", "name": "Irish Green*†", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2923/98e28c6f/GD001_IrishGreen_FT.jpg"}, {"code": "INBL", "name": "Indigo Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2922/facd1261/GD001_IndigoBlue_FT.jpg"}, {"code": "JDOM", "name": "Jade Dome*", "hex": "#08CE6D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2924/6a201052/GD001_JadeDome_FT.jpg"}, {"code": "KEGR", "name": "Kelly Green*", "hex": "#116946", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001ec/GD001_KellyGreen_FT.jpg"}, {"code": "KIWI", "name": "Kiwi*", "hex": "#78D527", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2925/88b2fa28/GD001_Kiwi_FT.jpg"}]},
    {id:'by004', brand:'Build Your Brand', name:'T-shirt round-neck', sku:'BY004', tier:'budget', inkoop:3.45, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6b44c322/69296cf85fa5b59a3a6bc709/BY004_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BALB", "name": "Baltic Blue*", "hex": "#9ED0ED", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/520cdd18/6564b03bd9e4aa0180000060/BY004_BalticBlue_FT.jpg"}, {"code": "BARK", "name": "Bark*", "hex": "#894E3F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/06bdeeb3/61a7cd9efe9cd9037100005c/BY004_Bark_FT.jpg"}, {"code": "BEBL", "name": "Beryl Blue†", "hex": "#B3DCE2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b2300000a/70715012/BY004_BerylBlue_FT.jpg"}, {"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18a674d36641.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#455859", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/52743e35/5fcf5c4297138200187360b6/BY004_BottleGreen_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#701F28", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18a7a025b5fc.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#444444", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18a807c06071.jpg"}, {"code": "CIRD", "name": "City Red*", "hex": "#E2142F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/63874d2248718b5b2300000b/f6816075/BY004_CityRed_FT.jpg"}, {"code": "COBA", "name": "Cobalt Blue*", "hex": "#1866AF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce18a9/b8534f7d/BY004_CobaltBlue_FT.jpg"}, {"code": "DSHA", "name": "Dark Shadow*", "hex": "#706766", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/06bdeeb3/61a7cd9efe9cd9037100005d/BY004_DarkShadow_FT.jpg"}, {"code": "FORE", "name": "Forest Green*", "hex": "#02775C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b2300000d/BY004_ForestGreen_FT.jpg"}, {"code": "GAGR", "name": "Grass Green*", "hex": "#00B58D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f026500002c/BY004_GrassGreen_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#AAAAAA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18ab38513200.jpg"}, {"code": "HIPK", "name": "Hibiskus Pink*", "hex": "#EA417E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b2300000f/BY004_HibiskusPink_FT.jpg"}, {"code": "HOBL", "name": "Horizon Blue*", "hex": "#88B7E2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000010/BY004_HorizonBlue_FT.jpg"}, {"code": "INTB", "name": "Intense Blue*", "hex": "#116EC1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f026500002d/BY004_IntenseBlue_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt*", "hex": "#CECECE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/06bdeeb3/61a7cd9efe9cd9037100005e/BY004_LightAsphalt_FT.jpg"}, {"code": "LILA", "name": "Lilac", "hex": "#CFC5D6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/52743e35/5fcf5c4297138200187360b7/BY004_Lilac_FT.jpg"}, {"code": "LNAV", "name": "Light Navy*", "hex": "#283354", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce18b2/06ef70fe/BY004_LightNavy_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#434556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18b3046d7e72.jpg"}, {"code": "NEMT", "name": "Neo Mint*", "hex": "#BCDDC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000012/BY004_NeoMint_FT.jpg"}, {"code": "OCBL", "name": "Ocean Blue*", "hex": "#C9DEE2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000013/BY004_OceanBlue_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#4C4A38", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce18b5a82b669f.jpg"}, {"code": "PAOR", "name": "Paradise Orange*", "hex": "#F49F29", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000014/BY004_ParadiseOrange_FT.jpg"}, {"code": "PIMA", "name": "Pink Marshmellow*", "hex": "#D6C9C0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000015/BY004_PinkMarshmellow_FT.jpg"}, {"code": "POBL", "name": "Powder Blue", "hex": "#99B5DB", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f000129/BY004_PowderBlue_FT.jpg"}, {"code": "REGR", "name": "Retro Green*", "hex": "#0C707C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/520cdd18/6564b03bd9e4aa0180000061/BY004_RetroGreen_FT.jpg"}, {"code": "SAND", "name": "Sand*", "hex": "#D3C0A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce18b6/c09d93a1/BY004_Sand_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink", "hex": "#EDC1D6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f00012a/BY004_SoftPink_FT.jpg"}, {"code": "TYEL", "name": "Taxi Yellow*", "hex": "#F7D76F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000017/BY004_TaxiYellow_FT.jpg"}]},
    {id:'gd002', brand:'Gildan', name:'Ultra Cotton™ adult t-shirt', sku:'GD002', tier:'budget', inkoop:3.48, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/efd52206/692969f05fa5b59a3a6b56e1/GD002_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ASHG", "name": "Ash*", "hex": "#C5CBC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce293c/d93d6325/GD002_Ash_FT.jpg"}, {"code": "BDUS", "name": "Blue Dusk", "hex": "#172F41", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce293f/a3ac5adf/GD002_BlueDusk_FT.jpg"}, {"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce293e/03b53056/GD002_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2941/d22f3561/GD002_CarolinaBlue_FT.jpg"}, {"code": "CDRD", "name": "Cardinal Red", "hex": "#840042", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2940/800615a7/GD002_CardinalRed_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2942/25b11322/GD002_Charcoal_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#AF0021", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2943/54824bb5/GD002_CherryRed_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2946/61841711/GD002_Daisy_FT.jpg"}, {"code": "DCHO", "name": "Dark Chocolate", "hex": "#0C0A0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2947/3e0c1d4c/GD002_DarkChocolate_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2948/d091f5b3/GD002_DarkHeather_FT.jpg"}, {"code": "FORE", "name": "Forest", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2949/18dbcdc1/GD002_Forest_FT.jpg"}, {"code": "GOLD", "name": "Gold", "hex": "#FFAF05", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6a7b3d35/5c5c3e3deb9f5b1c75ce294a/02de00e3/GD002_Gold_FT.jpg"}, {"code": "HELI", "name": "Heliconia", "hex": "#FF14D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce294d/9ab57760/GD002_Heliconia_FT.jpg"}, {"code": "HNAV", "name": "Heather Navy", "hex": "#131F29", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce294c/3d1924ed/GD002_HeatherNavy_FT.jpg"}, {"code": "ICGY", "name": "Ice Grey", "hex": "#E6E6DE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce294e/fa986261/GD002_IceGrey_FT.jpg"}, {"code": "INBL", "name": "Indigo Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce294f/2ee1ccfc/GD002_IndigoBlue_FT.jpg"}, {"code": "JDOM", "name": "Jade Dome", "hex": "#08CE6D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2952/18a04ef4/GD002_JadeDome_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce295a/43e1688e/GD002_LightBlue_FT.jpg"}, {"code": "LIME", "name": "Lime", "hex": "#7AFF2D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce295c/150d2ae1/GD002_Lime_FT.jpg"}, {"code": "LPIN", "name": "Light Pink", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce295b/9e022dc1/GD002_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce295d/6762f17a/GD002_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce295f/538f6a01/GD002_MilitaryGreen_FT.jpg"}, {"code": "NATU", "name": "Natural", "hex": "#E3E1C9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2960/0b03900d/GD002_Natural_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2961/7a7051c2/GD002_Navy_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#1E1A09", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2962/b9c7c57a/GD002_Olive_FT.jpg"}, {"code": "ORAN", "name": "Orange*", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2963/5f09eb29/GD002_Orange_FT.jpg"}, {"code": "ORCH", "name": "Orchid", "hex": "#C1B5FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2964/a904ddcf/GD002_Orchid_FT.jpg"}, {"code": "PDUS", "name": "Prairie Dust", "hex": "#55543A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2966/d9489027/GD002_PrairieDust_FT.jpg"}, {"code": "PURP", "name": "Purple*", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2967/c3787474/GD002_Purple_FT.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2968/de938df2/GD002_Red_FT.jpg"}]},
    {id:'gd007', brand:'Gildan', name:'Light Cotton adult t-shirt', sku:'GD007', tier:'budget', inkoop:2.50, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/3f5bc821/692972db5fa5b59a3a6cb044/GD007_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ASGR", "name": "Ash Grey", "hex": "#E1E1E1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f9/GD007_AshGrey_FT.jpg"}, {"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a4600000a/c1983f6e/GD007_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a4600000b/2c1c63cd/GD007_Charcoal_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#BE0735", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001fa/GD007_CherryRed_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#ECC836", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001fb/GD007_Daisy_FT.jpg"}, {"code": "FOGR", "name": "Forest Green", "hex": "#0C3016", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001fc/GD007_ForestGreen_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#909195", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001fd/GD007_GraphiteHeather_FT.jpg"}, {"code": "HELI", "name": "Heliconia", "hex": "#D11B80", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001fe/GD007_Heliconia_FT.jpg"}, {"code": "IGRN", "name": "Irish Green", "hex": "#019F52", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001ff/GD007_IrishGreen_FT.jpg"}, {"code": "LPIN", "name": "Light Pink", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a4600000c/a7f1eb3d/GD007_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#58183A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000200/GD007_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a4600000d/f47e8639/GD007_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a4600000e/4688198f/GD007_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#E74401", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000202/GD007_Orange_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#FFFBF0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000201/GD007_OffWhite_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#3B1B67", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000203/GD007_Purple_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a4600000f/0a58855a/GD007_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000010/9f279f73/GD007_Royal_FT.jpg"}, {"code": "SAGE", "name": "Sage", "hex": "#8A9887", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000205/GD007_Sage_FT.jpg"}, {"code": "SAGR", "name": "Safety Green", "hex": "#CEDF37", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000204/GD007_SafetyGreen_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000011/ff0930e0/GD007_Sand_FT.jpg"}, {"code": "SAPP", "name": "Sapphire", "hex": "#0C76AA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000206/GD007_Sapphire_FT.jpg"}, {"code": "SKYY", "name": "Sky", "hex": "#72D0F6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000207/GD007_Sky_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000012/55ba9197/GD007_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000013/d4287a9f/GD007_White_FT.jpg"}]},
    {id:'by021', brand:'Build Your Brand', name:'Women\'s extended shoulder tee', sku:'BY021', tier:'budget', inkoop:4.80, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f58a9f50/692972d85fa5b59a3a6cafac/BY021_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce196cf183cbce.jpg"}, {"code": "BURG", "name": "Burgundy*†", "hex": "#A01E36", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce196d/c62168f7/BY021_Burgundy_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown*†", "hex": "#593227", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f026500002e/BY021_ChocolateBrown_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*†", "hex": "#4C4C4C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce196ef4122778.jpg"}, {"code": "CHER", "name": "Cherry*†", "hex": "#89404E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce196f/a999e2cb/BY021_Cherry_FT.jpg"}, {"code": "DSRO", "name": "Dusk Rose*†", "hex": "#D8BCB8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000007/BY021_DuskRose_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey*†", "hex": "#BABABA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce19705ac9bcd2.jpg"}, {"code": "HIPK", "name": "Hibiskus Pink*†", "hex": "#DB326F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000030/BY021_HibiskusPink_FT.jpg"}, {"code": "LILA", "name": "Lilac*†", "hex": "#D4C9D8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e214a5f4/5fcf5c4397138200187360c8/BY021_Lilac_FT.jpg"}, {"code": "LNAV", "name": "Light Navy*†", "hex": "#28335E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000032/BY021_LightNavy_FT.jpg"}, {"code": "MSAL", "name": "Magic Salvia*†", "hex": "#98AD95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000033/BY021_MagicSalvia_FT.jpg"}, {"code": "NAVY", "name": "Navy*†", "hex": "#2F2F3D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1977e75d0225.jpg"}, {"code": "OLIV", "name": "Olive*†", "hex": "#777461", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1979555c30dc.jpg"}, {"code": "POBL", "name": "Powder Blue*†", "hex": "#C0D0D8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000044/BY021_PowderBlue_FT.jpg"}, {"code": "PUNI", "name": "Purple Night*†", "hex": "#594059", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000039/BY021_PurpleNight_FT.jpg"}, {"code": "SAND", "name": "Sand*†", "hex": "#E5E3D5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b2300003a/BY021_Sand_FT.jpg"}, {"code": "SSAL", "name": "Soft Salvia*†", "hex": "#A0AA8C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000008/BY021_SoftSalvia_FT.jpg"}, {"code": "TEAL", "name": "Teal*†", "hex": "#307382", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e214a5f4/5fcf5c4397138200187360c9/BY021_Teal_FT.jpg"}, {"code": "VBLU", "name": "Vintage Blue*†", "hex": "#4C5877", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/520cdd18/6564b03bd9e4aa0180000062/BY021_VintageBlue_FT.jpg"}, {"code": "WHIT", "name": "White*†", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce197cce977bc3.jpg"}, {"code": "WHSA", "name": "White Sand*†", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c00000a/BY021_WhiteSand_FT.jpg"}]},
    {id:'gd05b', brand:'Gildan', name:'Heavy Cotton™ youth t-shirt', sku:'GD05B', tier:'budget', inkoop:2.60, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/72e47ac2/692973b55fa5b59a3a6cd108/GD05B_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "ASHG", "name": "Ash", "hex": "#C5CBC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c18/6b6cad7a/GD05B_Ash_FT.jpg"}, {"code": "AZAL", "name": "Azalea", "hex": "#FF54F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c19/ffa5a05a/GD05B_Azalea_FT.jpg"}, {"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c1a/afeabb8e/GD05B_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c1c/1c411304/GD05B_CarolinaBlue_FT.jpg"}, {"code": "CDRD", "name": "Cardinal Red", "hex": "#840042", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c1b/19b56419/GD05B_CardinalRed_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c1d/82a7a186/GD05B_Charcoal_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c1e/cc00b0b8/GD05B_Daisy_FT.jpg"}, {"code": "DCHO", "name": "Dark Chocolate", "hex": "#0C0A0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c1f/0b0bc2fb/GD05B_DarkChocolate_FT.jpg"}, {"code": "FORE", "name": "Forest", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c20/6a4b87ac/GD05B_Forest_FT.jpg"}, {"code": "GOLD", "name": "Gold", "hex": "#FFAF05", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c21/54a43b99/GD05B_Gold_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c22/9b98cdd5/GD05B_GraphiteHeather_FT.jpg"}, {"code": "HELI", "name": "Heliconia", "hex": "#FF14D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c23/7f3cc581/GD05B_Heliconia_FT.jpg"}, {"code": "IGRN", "name": "Irish Green", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c25/c0939804/GD05B_IrishGreen_FT.jpg"}, {"code": "KIWI", "name": "Kiwi", "hex": "#78D527", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c26/1cfa7f21/GD05B_Kiwi_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c2c/e344fa2b/GD05B_LightBlue_FT.jpg"}, {"code": "LIME", "name": "Lime", "hex": "#7AFF2D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c2e/2715fcd8/GD05B_Lime_FT.jpg"}, {"code": "LPIN", "name": "Light Pink", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c2d/5dd7c4d7/GD05B_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c2f/23ae1586/GD05B_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c30/18e6f458/GD05B_MilitaryGreen_FT.jpg"}, {"code": "MINT", "name": "Mint Green", "hex": "#91FF96", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c31/d8f24759/GD05B_MintGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c33/158bae30/GD05B_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c34/9df9fb12/GD05B_Orange_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#F4EBD2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8082dcf2/674086693d2d8f284e000014/6313c132/GD05B_OffWhite_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c35/a565f33a/GD05B_Purple_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c36/2e32823c/GD05B_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c37/59cd9f78/GD05B_Royal_FT.jpg"}, {"code": "SAPP", "name": "Sapphire", "hex": "#009FCE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c39/d09c1938/GD05B_Sapphire_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c3a/014593f0/GD05B_SportGrey_FT.jpg"}, {"code": "VIOL", "name": "Violet", "hex": "#72A0FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c3b/2b0881ab/GD05B_Violet_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c3c/491373ff/GD05B_White_FT.jpg"}]},
    {id:'gd024', brand:'Gildan', name:'Softstyle™ Midweight adult t-shirt', sku:'GD024', tier:'budget', inkoop:3.40, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/40991e8a/6929715c5fa5b59a3a6c772f/8a91102e/GD024_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BSAV", "name": "Brown Savana", "hex": "#64554A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000011/6b4e2514/GD024_BrownSavana_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00003b/e62ca79d/GD024_Charcoal_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00003c/018bcaf0/GD024_Daisy_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000012/5fc52649/GD024_GraphiteHeather_FT.jpg"}, {"code": "HELI", "name": "Heliconia", "hex": "#FF14D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000013/5a4d088a/GD024_Heliconia_FT.jpg"}, {"code": "IGRN", "name": "Irish Green*", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300000b/520733b5/GD024_IrishGreen_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000014/c85005cd/GD024_LightBlue_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000015/344a9504/GD024_Maroon_FT.jpg"}, {"code": "MUST", "name": "Mustard", "hex": "#C1954B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000016/47b76221/GD024_Mustard_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00003d/99affb24/GD024_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000017/5e4cf8c0/GD024_Orange_FT.jpg"}, {"code": "PBMI", "name": "Pitch Black*", "hex": "#000309", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00003e/754a1c90/GD024_PitchBlack_FT.jpg"}, {"code": "PGON", "name": "Paragon", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000018/0758ab5b/GD024_Paragon_FT.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00003f/00f76994/GD024_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000041/db37740b/GD024_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey*", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000040/578bf680/GD024_RingspunSportGrey_FT.jpg"}, {"code": "SAGE", "name": "Sage", "hex": "#75C775", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b5000019/bc59a965/GD024_Sage_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b500001a/661f00da/GD024_Sand_FT.jpg"}, {"code": "SAPP", "name": "Sapphire", "hex": "#009FCE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000042/997b07a1/GD024_Sapphire_FT.jpg"}, {"code": "SBLU", "name": "Stone Blue", "hex": "#67A8B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b500001b/b689147e/GD024_StoneBlue_FT.jpg"}, {"code": "VIOL", "name": "Violet", "hex": "#72A0FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b500001c/0d2ad1d7/GD024_Violet_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000043/437871b5/GD024_White_FT.jpg"}]},
    {id:'gd01b', brand:'Gildan', name:'Softstyle™ youth ringspun t-shirt', sku:'GD01B', tier:'budget', inkoop:2.80, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/92a17524/69296faa5fa5b59a3a6c329c/GD01B_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "AZAL", "name": "Azalea", "hex": "#FF80D9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001ee/GD01B_Azalea_FT.jpg"}, {"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a97/a851c67d/GD01B_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue", "hex": "#629FEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f0/GD01B_CarolinaBlue_FT.jpg"}, {"code": "CDRD", "name": "Cardinal Red", "hex": "#B01A47", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001ef/GD01B_CardinalRed_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a98/8096e519/GD01B_Charcoal_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a9b/55756bd3/GD01B_Daisy_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b081d9e4aa4eae000064/00303d4f/GD01B_DarkHeather_FT.jpg"}, {"code": "FAMG", "name": "Fan Marine Green", "hex": "#033F40", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f1/GD01B_FanMarineGreen_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b081d9e4aa4eae000065/016edaf4/GD01B_GraphiteHeather_FT.jpg"}, {"code": "IGRN", "name": "Irish Green", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a9f/9ddd3223/GD01B_IrishGreen_FT.jpg"}, {"code": "INBL", "name": "IndigoBlue", "hex": "#526D82", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/5c5c3e3deb9f5b1c75ce2a9e/5580bfad/GD01B_IndigoBlue_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aa4/b462242f/GD01B_LightBlue_FT.jpg"}, {"code": "LPIN", "name": "Light Pink", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aa5/70477bb5/GD01B_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b081d9e4aa4eae000066/2923f143/GD01B_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b081d9e4aa4eae000067/6fa803d4/GD01B_MilitaryGreen_FT.jpg"}, {"code": "NATU", "name": "Natural", "hex": "#F1EDE2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f0001f3/GD01B_Natural_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aa7/22a5023f/GD01B_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aa8/c8fd147d/GD01B_Orange_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aa9/603710e7/GD01B_Purple_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aaa/4129e81c/GD01B_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aab/b64fdae2/GD01B_Royal_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#D8CCBC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/5c5c3e3deb9f5b1c75ce2aac/aa853d03/GD01B_Sand_FT.jpg"}, {"code": "SAPP", "name": "Sapphire", "hex": "#009FCE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aad/fbbcb431/GD01B_Sapphire_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aae/07ed8901/GD01B_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aaf/c2ae76ed/GD01B_White_FT.jpg"}]},
    {id:'gd014', brand:'Gildan', name:'Ultra Cotton™ adult long sleeve t-shirt', sku:'GD014', tier:'standaard', inkoop:7.30, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/57232783/692970f85fa5b59a3a6c6799/GD014_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ASHG", "name": "Ash*", "hex": "#C5CBC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a40/e3ab27fd/GD014_Ash_FT.jpg"}, {"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a41/4a11e6ba/GD014_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*†", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a44/2ca4b899/GD014_Charcoal_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*†", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a46/7abe9323/GD014_DarkHeather_FT.jpg"}, {"code": "FORE", "name": "Forest*", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a47/ed1c72db/GD014_Forest_FT.jpg"}, {"code": "GOLD", "name": "Gold*", "hex": "#FFAF05", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a48/36a26c65/GD014_Gold_FT.jpg"}, {"code": "LBLU", "name": "Light Blue*", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a4f/8551249f/GD014_LightBlue_FT.jpg"}, {"code": "LPIN", "name": "Light Pink*", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a50/6b4bae74/GD014_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon*", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a51/b0794971/GD014_Maroon_FT.jpg"}, {"code": "NAVY", "name": "Navy*†", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a52/9dfd2c46/GD014_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange*†", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a53/75e7af42/GD014_Orange_FT.jpg"}, {"code": "REDD", "name": "Red*†", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a54/a4603a71/GD014_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal*†", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a55/9e86ab06/GD014_Royal_FT.jpg"}, {"code": "SAGR", "name": "Safety Green*†", "hex": "#B7FF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a56/30b9820b/GD014_SafetyGreen_FT.jpg"}, {"code": "SAOR", "name": "Safety Orange*†", "hex": "#FF5900", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a57/34afd1ee/GD014_SafetyOrange_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey*†", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a58/6d7bf897/GD014_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White*†", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a59/d4df4214/GD014_White_FT.jpg"}]},
    {id:'by149', brand:'Build Your Brand', name:'Women\'s oversized boyfriend tee', sku:'BY149', tier:'standaard', inkoop:7.00, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4dc08bd3/69296be55fa5b59a3a6b9ad6/BY149_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/783d7c75/5fcf5c4597138200187360e8/BY149_Black_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown", "hex": "#593229", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000059/BY149_ChocolateBrown_FT.jpg"}, {"code": "CIRD", "name": "City Red", "hex": "#DB323B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000009/BY149_CityRed_FT.jpg"}, {"code": "DSHA", "name": "Dark Shadow", "hex": "#56504E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7bcd6675/6564b081d9e4aa4eae000007/BY149_DarkShadow_FT.jpg"}, {"code": "LILA", "name": "Lilac", "hex": "#C4B2CA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f000137/BY149_Lilac_FT.jpg"}, {"code": "POBL", "name": "Powder Blue", "hex": "#95B5DE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f000138/BY149_PowderBlue_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#877F6D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000032/BY149_PaleOlive_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink", "hex": "#E3C4D3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f000139/BY149_SoftPink_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/783d7c75/5fcf5c4597138200187360e9/BY149_White_FT.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f026500005a/BY149_WhiteSand_FT.jpg"}]},
    {id:'gd072', brand:'Gildan', name:'Softstyle™ women\'s ringspun t-shirt', sku:'GD072', tier:'budget', inkoop:2.65, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5378b314/69296ae05fa5b59a3a6b7d95/GD072_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "AZAL", "name": "Azalea", "hex": "#FF54F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c81/a2be8581/GD072_Azalea_FT.jpg"}, {"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c82/20d795b4/GD072_Black_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c8a/2c7866a2/GD072_DarkHeather_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather*", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000037/7c381c4e/GD072_GraphiteHeather_FT.jpg"}, {"code": "IGRN", "name": "Irish Green", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c8f/b7921277/GD072_IrishGreen_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c9a/ab6cc282/GD072_LightBlue_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c9d/79a2cee7/GD072_Navy_FT.jpg"}, {"code": "PGON", "name": "Paragon*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000038/46d7814d/GD072_Paragon_FT.jpg"}, {"code": "PIST", "name": "Pistachio*", "hex": "#A5F96B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000039/69108dc2/GD072_Pistachio_FT.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c9f/5e945a42/GD072_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ca0/acf55fd0/GD072_Royal_FT.jpg"}, {"code": "SAND", "name": "Sand*", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ca1/3bfa67c1/GD072_Sand_FT.jpg"}, {"code": "SKYY", "name": "Sky*", "hex": "#7AFFFC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300003a/2031ab91/GD072_Sky_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey*", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ca3/30adf8f5/GD072_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ca4/d136d4af/GD072_White_FT.jpg"}]},
    {id:'lw20t', brand:'Larkwood', name:'Baby/toddler t-shirt', sku:'LW20T', tier:'budget', inkoop:3.60, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cb31a1ed/692969d15fa5b59a3a6b51c9/LW20T_LS00_2026.jpg', sizes:["06", "612", "1218", "1824", "2436", "34", "56"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce52ffe82a29a0.jpg"}, {"code": "FUCH", "name": "Fuchsia", "hex": "#E00088", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce5300030cd656.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#C1C1C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce530187d54ef8.JPG"}, {"code": "NAVY", "name": "Navy*", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce5309eeca62c4.jpg"}, {"code": "PABL", "name": "Pale Blue*", "hex": "#C6EDF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce530a1bc8cbc4.jpg"}, {"code": "PAPK", "name": "Pale Pink*", "hex": "#EFD3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce530b4c336548.jpg"}, {"code": "PAYE", "name": "Pale Yellow*", "hex": "#F7EA82", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce530cf69c4442.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#000EEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce530d7f569167.JPG"}, {"code": "REDD", "name": "Red*", "hex": "#FF0032", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce530e97037d75.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#0052EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce530fe35cf39c.jpg"}, {"code": "SWHI", "name": "Sublimation White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53104018b13a.jpg"}, {"code": "TURQ", "name": "Turquoise", "hex": "#00D1C4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce5312bb2034b6.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce531399f20b5d.jpg"}]},
    {id:'by036', brand:'Build Your Brand', name:'Women\'s long slub tee', sku:'BY036', tier:'standaard', inkoop:5.50, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/63e52e7a/692973b25fa5b59a3a6cd072/BY036_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1a00ffccd23d.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#2A4142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000041/BY036_BottleGreen_FT.jpg"}, {"code": "CHER", "name": "Cherry", "hex": "#6B2C39", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000042/BY036_Cherry_FT.jpg"}, {"code": "DSHA", "name": "Dark Shadow", "hex": "#504A51", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/520cdd18/6564b03bd9e4aa0180000063/BY036_DarkShadow_FT.jpg"}, {"code": "LILA", "name": "Lilac", "hex": "#DBCEE0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000011/BY036_Lilac_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#7A6649", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000012/BY036_Olive_FT.jpg"}, {"code": "SSAL", "name": "Soft Salvia", "hex": "#D3CFBC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000013/BY036_SoftSalvia_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1a050691ed03.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000014/BY036_WhiteSand_FT.jpg"}]},
    {id:'gd07b', brand:'Gildan', name:'Light Cotton youth t-shirt', sku:'GD07B', tier:'budget', inkoop:2.15, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2dbbe6ce/692971b15fa5b59a3a6c83c7/GD07B_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000014/3c608ed7/GD07B_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#61666A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000208/GD07B_Charcoal_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#E3BA51", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000209/GD07B_Daisy_FT.jpg"}, {"code": "IGRN", "name": "Irish Green", "hex": "#11994D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00020a/GD07B_IrishGreen_FT.jpg"}, {"code": "LPIN", "name": "Light Pink", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000015/91a5e7b7/GD07B_LightPink_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#627C61", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00020b/GD07B_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000016/ad1ba8d6/GD07B_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#E93E22", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00020c/GD07B_Orange_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#D00A23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00020d/GD07B_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#105FAC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00020e/GD07B_Royal_FT.jpg"}, {"code": "SAGR", "name": "Safety Green", "hex": "#CDE420", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00020f/GD07B_SafetyGreen_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000017/7ebb95cf/GD07B_Sand_FT.jpg"}, {"code": "SKYY", "name": "Sky", "hex": "#6EC0E6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000210/GD07B_Sky_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000018/072ad4b9/GD07B_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6686bc54db80ce0a46000019/414070a3/GD07B_White_FT.jpg"}]},
    {id:'by369', brand:'Build Your Brand', name:'Women’s Sorona loose fit tee', sku:'BY369', tier:'standaard', inkoop:7.65, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e221c07b/692970155fa5b59a3a6c43ab/BY369_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f026500002a/6442f62d/BY369_Black_FT.jpg"}, {"code": "CLOU", "name": "Cloud", "hex": "#DDD3C9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00016f/BY369_Cloud_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#E1A4B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000170/BY369_DustyPink_FT.jpg"}, {"code": "DUPU", "name": "Dusty Purple", "hex": "#846F82", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000171/BY369_DustyPurple_FT.jpg"}, {"code": "MAGN", "name": "Magnet", "hex": "#665F63", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000054/53c827ec/BY369_Magnet_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#2D2D3D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000041/7c19e24e/BY369_Navy_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#9B9276", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b14347887000136/BY369_PaleOlive_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f026500006d/c051c58c/BY369_White_FT.jpg"}]},
    {id:'gd102', brand:'Gildan', name:'Softstyle™ Midweight Adult No Label Enzyme Wash T-Shirt', sku:'GD102', tier:'budget', inkoop:4.10, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f70c0762/69296dfa5fa5b59a3a6bf05c/GD102_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "NAVY", "name": "Navy", "hex": "#0B2254", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00023a/GD102_Navy_FT.jpg"}, {"code": "PBMI", "name": "Pitch Black", "hex": "#141414", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00023b/GD102_PitchBlack_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#DC0E32", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00023c/GD102_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#03559E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00023d/GD102_Royal_FT.jpg"}, {"code": "RSSG", "name": "RS Sport Grey", "hex": "#97979F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00023e/GD102_RSSportGrey_FT.jpg"}, {"code": "SAGE", "name": "Sage", "hex": "#818D83", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00023f/GD102_Sage_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#C0B5A5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000240/GD102_Sand_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#F0F0F0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000241/GD102_White_FT.jpg"}]},
    {id:'gd011', brand:'Gildan', name:'Softstyle™ long sleeve t-shirt', sku:'GD011', tier:'standaard', inkoop:6.10, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/778f7629/69296a6a5fa5b59a3a6b6941/GD011_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a29/dead803d/GD011_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a2a/6215272f/GD011_Charcoal_FT.jpg"}, {"code": "FADG", "name": "Fan Dark Green", "hex": "#18452E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000211/GD011_FanDarkGreen_FT.jpg"}, {"code": "FADP", "name": "Fan Dark Purple", "hex": "#2C134C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000212/GD011_FanDarkPurple_FT.jpg"}, {"code": "FADR", "name": "Fan Deep Royal", "hex": "#17387B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000213/GD011_FanDeepRoyal_FT.jpg"}, {"code": "FAMG", "name": "Fan Marine Green", "hex": "#023E3C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000214/GD011_FanMarineGreen_FT.jpg"}, {"code": "MGRE", "name": "Military Green*", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6565ccd7d9e4aa43cc000013/85732327/GD011_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a2f/1dbc95ad/GD011_Navy_FT.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a32/0b209eca/GD011_Royal_FT.jpg"}, {"code": "SPGY", "name": "Sports Grey*", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c3800004e/45522f91/GD011_SportsGrey_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a34/14bd7511/GD011_White_FT.jpg"}]},
    {id:'by364', brand:'Build Your Brand', name:'Sorona loose fit tee', sku:'BY364', tier:'standaard', inkoop:9.15, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9e6629c7/692973415fa5b59a3a6cc00a/BY364_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000040/c7477d5b/BY364_Black_FT.jpg"}, {"code": "CLOU", "name": "Cloud", "hex": "#D2C8BF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00016c/BY364_Cloud_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#D4A1B2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00016d/BY364_DustyPink_FT.jpg"}, {"code": "DUPU", "name": "Dusty Purple", "hex": "#8B7894", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00016e/BY364_DustyPurple_FT.jpg"}, {"code": "MAGN", "name": "Magnet", "hex": "#665F61", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000069/71871065/BY364_Magnet_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#2D2C3D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f026500006a/1115a579/BY364_Navy_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#968B73", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b14347887000129/BY364_PaleOlive_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f026500006b/07f638b0/BY364_White_FT.jpg"}]},
    {id:'by391', brand:'Build Your Brand', name:'Loose fit tee', sku:'BY391', tier:'standaard', inkoop:7.00, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2cbb30d7/69296bae5fa5b59a3a6b9319/BY391_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#1D1D1F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000176/BY391_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#424045", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000177/BY391_Charcoal_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A9A6AE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000178/BY391_HeatherGrey_FT.jpg"}, {"code": "LILA", "name": "Lilac", "hex": "#B9A5C0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000179/BY391_Lilac_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#292C3B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00017a/BY391_Navy_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#9B9283", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00017b/BY391_PaleOlive_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#E2DFE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00017c/BY391_White_FT.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#DFDBCF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00017d/BY391_WhiteSand_FT.jpg"}]},
    {id:'gd006', brand:'Gildan', name:'Heavy Cotton™ women\'s t-shirt', sku:'GD006', tier:'budget', inkoop:3.30, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e651794f/69296a795fa5b59a3a6b6bd6/GD006_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "AZAL", "name": "Azalea", "hex": "#FF54F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29d5/367162fb/GD006_Azalea_FT.jpg"}, {"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29d6/1e0b036f/GD006_Black_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29da/03316876/GD006_DarkHeather_FT.jpg"}, {"code": "LPIN", "name": "Light Pink", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29e1/cd27783b/GD006_LightPink_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29e4/08dd73e6/GD006_Navy_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#F4EBD2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8082dcf2/674086693d2d8f284e000015/ca1f89b9/GD006_OffWhite_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29e5/27870d45/GD006_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29e6/5e6efdcd/GD006_Royal_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29e8/77d42a81/GD006_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce29ea/238ff139/GD006_White_FT.jpg"}]},
    {id:'gd026', brand:'Gildan', name:'Softstyle™ Midweight Women\'s t-shirt', sku:'GD026', tier:'budget', inkoop:3.40, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/40991e8a/69296f2c5fa5b59a3a6c1f63/0d1eafb5/GD026_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "HELI", "name": "Heliconia", "hex": "#FF14D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00004c/9bc395d1/GD026_Heliconia_FT.jpg"}, {"code": "IGRN", "name": "Irish Green", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300000d/eae5181e/GD026_IrishGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00004d/4eb583dd/GD026_Navy_FT.jpg"}, {"code": "PBMI", "name": "Pitch Black", "hex": "#000309", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00004e/c910a93b/GD026_PitchBlack_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00004f/3327dd5c/GD026_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300000e/6f6b50fb/GD026_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000050/fddcc7af/GD026_RingspunSportGrey_FT.jpg"}, {"code": "SAPP", "name": "Sapphire", "hex": "#009FCE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000051/ebd11f88/GD026_Sapphire_FT.jpg"}, {"code": "VIOL", "name": "Violet", "hex": "#72A0FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b500001f/0e1ff7a1/GD026_Violet_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000052/3f50ae58/GD026_White_FT.jpg"}]},
    {id:'gd029', brand:'Gildan', name:'Hammer Maxweight Adult T-Shirt', sku:'GD029', tier:'standaard', inkoop:5.30, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/23c29358/6929711b5fa5b59a3a6c6d86/050ef525/GD029_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BDUS", "name": "Blue Dusk", "hex": "#2B3B4A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000227/GD029_BlueDusk_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#C11A38", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/23c95773/692832a89eee9a377f000228/11fc7000/GD029_CherryRed_FT.jpg"}, {"code": "DCHO", "name": "Dark Chocolate", "hex": "#413634", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000229/GD029_DarkChocolate_FT.jpg"}, {"code": "DERY", "name": "Deep Royal", "hex": "#0B387C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00022a/GD029_DeepRoyal_FT.jpg"}, {"code": "FOGR", "name": "Forest Green", "hex": "#163E26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00022b/GD029_ForestGreen_FT.jpg"}, {"code": "GARN", "name": "Garnet", "hex": "#781939", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/23c95773/692832a89eee9a377f00022c/608f75e6/GD029_Garnet_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#555557", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00022d/GD029_GraphiteHeather_FT.jpg"}, {"code": "PBMI", "name": "Pitch Black", "hex": "#10171F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00022e/GD029_PitchBlack_FT.jpg"}, {"code": "TANN", "name": "Tan", "hex": "#B2997B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00022f/GD029_Tan_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#E8E8E8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000230/GD029_White_FT.jpg"}]},
    {id:'gd003', brand:'Gildan', name:'Hammer® adult t-shirt', sku:'GD003', tier:'budget', inkoop:4.55, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/df7d72d3/6929705c5fa5b59a3a6c4e26/GD003_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2978/c0e0d34f/GD003_Black_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce297d/9f3978a4/GD003_GraphiteHeather_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#1E1A09", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000004/30c31fb0/GD003_Olive_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#F4EBD2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000003/244f9628/GD003_OffWhite_FT.jpg"}, {"code": "SPDN", "name": "Sport Dark Navy", "hex": "#001127", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2989/9222f964/GD003_SportDarkNavy_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce298a/1a1c1c0e/GD003_SportGrey_FT.jpg"}, {"code": "SPRB", "name": "Sport Royal", "hex": "#0022A2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce298c/7c8b77c5/GD003_SportRoyal_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce298e/55c7ac0f/GD003_White_FT.jpg"}]},
    {id:'gd24b', brand:'Gildan', name:'Softstyle™ Midweight Youth t-shirt', sku:'GD24B', tier:'budget', inkoop:2.60, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0743fd08/69296ba25fa5b59a3a6b91dd/GD24B_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "DAIS", "name": "Daisy", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000044/4321928e/GD24B_Daisy_FT.jpg"}, {"code": "IGRN", "name": "Irish Green", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300000c/cba6f670/GD24B_IrishGreen_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000045/fb20c688/GD24B_LightBlue_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000046/c0eec46b/GD24B_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b500001d/fe53bdf3/GD24B_Orange_FT.jpg"}, {"code": "PBMI", "name": "Pitch Black", "hex": "#000309", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000047/31fa54af/GD24B_PitchBlack_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000048/bed9ed26/GD24B_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00004a/5f9e26f5/GD24B_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000049/a3454e47/GD24B_RingspunSportGrey_FT.jpg"}, {"code": "SAGE", "name": "Sage", "hex": "#75C775", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63b6b27fd9e4aa59b500001e/cf0a5c29/GD24B_Sage_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00004b/8451f93b/GD24B_White_FT.jpg"}]},
    {id:'gd010', brand:'Gildan', name:'Softstyle™ v-neck t-shirt', sku:'GD010', tier:'budget', inkoop:3.95, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/086ab4a3/692971fe5fa5b59a3a6c8ff9/GD010_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a18/f608f738/GD010_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a19/a9b57ad9/GD010_Charcoal_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#AF0021", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000007/e6807f8e/GD010_CherryRed_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a1a/9e65df04/GD010_DarkHeather_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a23/f2c5dcce/GD010_Navy_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a26/b81ffc87/GD010_Royal_FT.jpg"}, {"code": "SPGY", "name": "Sports Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c3800004d/2cc9f304/GD010_SportsGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a28/27c15229/GD010_White_FT.jpg"}]},
    {id:'jt001', brand:'AWDis Just T\'s', name:'Triblend T', sku:'JT001', tier:'standaard', inkoop:5.10, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/df2512ec/692973ac5fa5b59a3a6ccf9e/JT001_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "HBLA", "name": "Heather Black", "hex": "#02080F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce469cd72613fe.jpg"}, {"code": "HBUR", "name": "Heather Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce469e6709ba42.jpg"}, {"code": "HCHA", "name": "Heather Charcoal", "hex": "#1F332E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce469f4eccd374.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#9BAEB2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46a0b10d73d3.jpg"}, {"code": "SOBK", "name": "Solid Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46b05f90f579.jpg"}, {"code": "SONY", "name": "Solid Navy", "hex": "#001127", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46b2ce95d37e.jpg"}]},
    {id:'lw620', brand:'Larkwood', name:'Organic t-shirt', sku:'LW620', tier:'budget', inkoop:4.25, tags:["Organic", "Kids"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f78d9861/692971d45fa5b59a3a6c8969/LW620_LS00_2026.jpg', sizes:["03", "36", "612", "1218", "1824", "2436"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000cfed9335b66.jpg"}, {"code": "BPIN", "name": "Bright Pink", "hex": "#F45EE8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000cff2ebeb7ff.jpg"}, {"code": "NATU", "name": "Natural", "hex": "#FFF4D6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000d004572271f.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/51c9ce67/5dfabf896aa61163fd000d015b631643.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000d035832e237.jpg"}]},
    {id:'gd078', brand:'Gildan', name:'Softstyle™ women\'s v-neck t-shirt', sku:'GD078', tier:'budget', inkoop:3.90, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/79e6f46f/69296ea05fa5b59a3a6c0892/GD078_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2cbd/e1182984/GD078_Black_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#AF0021", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300003c/1e06cb87/GD078_CherryRed_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2cc9/7333d627/GD078_Navy_FT.jpg"}, {"code": "SPGY", "name": "Sports Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c3800005d/a7e6d8fd/GD078_SportsGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2cce/854ac36b/GD078_White_FT.jpg"}]},
    {id:'lw21t', brand:'Larkwood', name:'Long-sleeved t-shirt', sku:'LW21T', tier:'budget', inkoop:4.60, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/54e148e6/69296c0e5fa5b59a3a6ba0f2/LW21T_LS00_2026.jpg', sizes:["34", "06", "612", "1218", "1824", "2436"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc00001338332fdf.JPG"}, {"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17cb77d2/61a7cfc8fe9cd91601000008/LW21T_Navy_FT.jpg"}, {"code": "PAPK", "name": "Pale Pink", "hex": "#EFD3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce531b9528df90.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce531dad95bb7d.jpg"}]},
    {id:'jt006', brand:'AWDis Just T\'s', name:'Women\'s triblend cropped T', sku:'JT006', tier:'budget', inkoop:4.90, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ebd63375/69296db45fa5b59a3a6be60c/JT006_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "HBLA", "name": "Heather Black", "hex": "#02080F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46c22b59e409.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#9BAEB2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46c341efca7d.jpg"}, {"code": "SOBK", "name": "Solid Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46cab12203d6.jpg"}, {"code": "SOWH", "name": "Solid White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46cba5109281.jpg"}]},
    {id:'gd110', brand:'Gildan', name:'Light Cotton Adult No Label T-Shirt', sku:'GD110', tier:'budget', inkoop:2.90, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/404dde7a/69296fdb5fa5b59a3a6c3acc/GD110_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#181818", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000242/GD110_Black_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#F4F4F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000243/GD110_White_FT.jpg"}]},
    {id:'jt002', brand:'AWDis Just T\'s', name:'Triblend T long sleeve', sku:'JT002', tier:'standaard', inkoop:6.35, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/72aba189/69296c655fa5b59a3a6baed0/JT002_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "HCHA", "name": "Heather Charcoal", "hex": "#1F332E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46b5367770a9.jpg"}, {"code": "SOBK", "name": "Solid Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46c0013b9682.jpg"}, {"code": "SOWH", "name": "Solid White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46c162377dfb.jpg"}]},
    {id:'jt099', brand:'AWDis Just T\'s', name:'Washed T', sku:'JT099', tier:'standaard', inkoop:6.35, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a9449d02/69296dba5fa5b59a3a6be715/JT099_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "WCHA", "name": "Washed Charcoal", "hex": "#0C110F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce474708e70e80.jpg"}, {"code": "WJBL", "name": "Washed Jet Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce474980830259.jpg"}]},
    {id:'jt01f', brand:'AWDis Just T\'s', name:'Women\'s triblend T', sku:'JT01F', tier:'standaard', inkoop:5.10, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2b0c3876/69296ecb5fa5b59a3a6c0f80/JT01F_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "HBLA", "name": "Heather Black", "hex": "#02080F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46e2b68fcdad.jpg"}, {"code": "SOBK", "name": "Solid Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce46f471100d78.jpg"}]},
    {id:'jt019', brand:'AWDis Just T\'s', name:'Oversize 100 long sleeve T', sku:'JT019', tier:'standaard', inkoop:7.65, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6d25fbe5/69296d325fa5b59a3a6bd06f/JT019_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2fc1fb16/6564b10ad9e4aa680a000019/eb907e12/JT019_DeepBlack_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#DADADC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2fc1fb16/6564b10ad9e4aa680a00001a/47acba1d/JT019_White_FT.jpg"}]},
    {id:'jt120', brand:'AWDis Just T\'s', name:'Signature Heavyweight T', sku:'JT120', tier:'standaard', inkoop:8.90, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b177d9ed/69296bb95fa5b59a3a6b94e4/JT120_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084f63d2d8f57b9000028/JT120_DeepBlack_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084f63d2d8f57b9000029/JT120_White_FT.jpg"}]},
    {id:'lw25t', brand:'Larkwood', name:'Long sleeve baseball t-shirt', sku:'LW25T', tier:'budget', inkoop:3.80, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7df37256/69296e445fa5b59a3a6bfb67/LW25T_LS00_2026.jpg', sizes:["06", "612", "1218", "1824", "2436"], colors:[{"code": "WHNY", "name": "White/Navy", "hex": "#FFFF25", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce532a39aa8064.JPG"}, {"code": "WHRD", "name": "White/Red", "hex": "#FFFF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce532d3e08a9c7.jpg"}]},
    {id:'gd111', brand:'Gildan', name:'Ultra Cotton Adult Prepared for Dye T-Shirt', sku:'GD111', tier:'budget', inkoop:3.30, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6dfcf4b7/69296e6e5fa5b59a3a6c0023/faaecf55/GD111_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "PPFD", "name": "Prepared for Dye", "hex": "#ECECEC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000244/GD111_PreparedforDye_FT.jpg"}]},
    {id:'lw27t', brand:'Larkwood', name:'Short sleeve striped t-shirt', sku:'LW27T', tier:'budget', inkoop:4.70, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ba996220/69296a995fa5b59a3a6b715b/LW27T_LS00_2026.jpg', sizes:["06", "612", "1218", "1824", "2436", "34"], colors:[{"code": "WHON", "name": "White/Oxford Navy", "hex": "#FFFF1A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce5335cf2851ef.JPG"}]},
    {id:'jt034', brand:'AWDis Just T\'s', name:'Camo T', sku:'JT034', tier:'standaard', inkoop:6.05, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5446a5e4/69296e385fa5b59a3a6bf95e/JT034_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "GNCA", "name": "Green Camo", "hex": "#383F3B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce473461956ec2.jpg"}]},
    {id:'aq070', brand:'Asquith & Fox', name:'Men\'s Marinière coastal long sleeve tee', sku:'AQ070', tier:'standaard', inkoop:12.60, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c864061c/69296e855fa5b59a3a6c042f/AQ070_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "WHNY", "name": "White/Navy", "hex": "#FFFF12", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf4a9a4930fdb.jpg"}]},
    {id:'aq071', brand:'Asquith & Fox', name:'Women\'s Marinière coastal long sleeve tee', sku:'AQ071', tier:'standaard', inkoop:11.40, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ddef6a7e/69296b315fa5b59a3a6b88ee/AQ071_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "WHNY", "name": "White/Navy", "hex": "#FFFF12", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf4b4f1047cb8.jpg"}]},
    {id:'aq072', brand:'Asquith & Fox', name:'Men\'s Marinière coastal short sleeve tee', sku:'AQ072', tier:'standaard', inkoop:8.20, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e4bba365/69296b9d5fa5b59a3a6b9176/AQ072_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "WHNY", "name": "White/Navy", "hex": "#FFFF12", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf4bfdc150f02.jpg"}]},
    {id:'aq073', brand:'Asquith & Fox', name:'Women\'s Marinière coastal short sleeve tee', sku:'AQ073', tier:'standaard', inkoop:8.20, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0b1d31d8/692972d35fa5b59a3a6caf03/AQ073_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "WHNY", "name": "White/Navy", "hex": "#FFFF12", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf4ca8424fd02.jpg"}]},
  ],
  sweater:[
    {id:'jh030', brand:'AWDis Just Hoods', name:'AWDis sweatshirt', sku:'JH030', tier:'standaard', inkoop:7.99, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9b444aa9/692971ec5fa5b59a3a6c8d13/JH030_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ABLU", "name": "Airforce Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42c7/720d37da/JH030_AirforceBlue_FT.jpg"}, {"code": "ASHG", "name": "Ash", "hex": "#DEE1DA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42c9/dc431695/JH030_Ash_FT.jpg"}, {"code": "AWHI", "name": "Arctic White*†", "hex": "#EFEFEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42c8/291ef550/JH030_ArcticWhite_FT.jpg"}, {"code": "BAPK", "name": "Baby Pink*†", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42ca/1b95b8c1/JH030_BabyPink_FT.jpg"}, {"code": "BORA", "name": "Burnt Orange", "hex": "#F92C0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42ce/68ff5755/JH030_BurntOrange_FT.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42cc/cda1f2a2/JH030_BottleGreen_FT.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#002CCA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/6564b10ad9e4aa680a000004/JH030_BrightRoyal_FT.jpg"}, {"code": "BSMO", "name": "Black Smoke", "hex": "#1F2530", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42cb/ec7ab2cd/JH030_BlackSmoke_FT.jpg"}, {"code": "BURG", "name": "Burgundy*†", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42cd/89eb9c8d/JH030_Burgundy_FT.jpg"}, {"code": "CARA", "name": "Caramel Latte", "hex": "#A07A66", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/61545c1c3f6633447e000037/a636df2d/JH030_CaramelLatte_FT.jpg"}, {"code": "CFPK", "name": "Candyfloss Pink", "hex": "#FF68F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42cf/da98c4ef/JH030_CandyflossPink_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*†", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42d0/bf1c550b/JH030_Charcoal_FT.jpg"}, {"code": "CHFB", "name": "Chocolate Fudge Brownie", "hex": "#352321", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b5/JH030_ChocolateFudgeBrownie_FT.jpg"}, {"code": "CORN", "name": "Cornflower Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/6564b10ad9e4aa680a000005/JH030_CornflowerBlue_FT.jpg"}, {"code": "CRAN", "name": "Cranberry", "hex": "#B6008F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42d1/1c9388a7/JH030_Cranberry_FT.jpg"}, {"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b6/JH030_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black*†", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5dfabf876aa61163fd00056b/da7766b2/JH030_DeepBlack_FT.jpg"}, {"code": "DROS", "name": "Dusty Rose", "hex": "#AE618C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/61545c1c3f6633447e00003a/cee6ccc4/JH030_DustyRose_FT.jpg"}, {"code": "DSAN", "name": "Desert Sand", "hex": "#DCC98F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/61545c1c3f6633447e000038/fe57c0fd/JH030_DesertSand_FT.jpg"}, {"code": "DUGN", "name": "Dusty Green", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/61545c1c3f6633447e000039/a8e81fcf/JH030_DustyGreen_FT.jpg"}, {"code": "DULI", "name": "Dusty Lilac", "hex": "#6B7191", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/fcc58cab/641322350bdbaf5ac2000005/JH030_DustyLilac_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/60c09ee53f663365bc00001d/ee7f36b4/JH030_DustyPink_FT.jpg"}, {"code": "EAGN", "name": "Earthy Green", "hex": "#46613F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/61545c1c3f6633447e00003b/4a0695ef/JH030_EarthyGreen_FT.jpg"}, {"code": "FEFU", "name": "Festival Fuchsia", "hex": "#8E0FAE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bb40b3f3/63e238710bdbaf50e0000014/JH030_FestivalFuchsia_FT.jpg"}, {"code": "FORE", "name": "Forest Green", "hex": "#02190C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42d4/c7c82628/JH030_ForestGreen_FT.jpg"}, {"code": "FRED", "name": "Fire Red*†", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42d3/29ded711/JH030_FireRed_FT.jpg"}, {"code": "GINB", "name": "Ginger Biscuit", "hex": "#8F3823", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/61545c1c3f6633447e00003c/2c068ea7/JH030_GingerBiscuit_FT.jpg"}, {"code": "GOLD", "name": "Gold", "hex": "#FFB721", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42d5/c567456b/JH030_Gold_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#4E5F61", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b7/JH030_GraphiteHeather_FT.jpg"}, {"code": "HBLU", "name": "Hawaiian Blue", "hex": "#2BFCFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d1e59d7/5c5c3e3deb9f5b1c75ce42d6/d29f076f/JH030_HawaiianBlue_FT.jpg"}]},
    {id:'gd066', brand:'Gildan', name:'Softstyle™ Midweight fleece adult crew neck', sku:'GD066', tier:'standaard', inkoop:8.90, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cacb1f6b/69296b645fa5b59a3a6b8c7a/GD066_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "XS"], colors:[{"code": "ASGR", "name": "Ash Grey*", "hex": "#D1DDE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/fd570109/674086693d2d8f284e000028/GD066_AshGrey_FT.jpg"}, {"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000053/93789219/GD066_Black_FT.jpg"}, {"code": "BSAV", "name": "Brown Savana*", "hex": "#64554A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000016/aed2100e/GD066_BrownSavana_FT.jpg"}, {"code": "CEME", "name": "Cement*", "hex": "#9CA5A5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000036/acfafe3d/GD066_Cement_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000054/d1054780/GD066_Charcoal_FT.jpg"}, {"code": "COCO", "name": "Cocoa*", "hex": "#6B3D2E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000012/0b9f7b57/GD066_Cocoa_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000055/f70a1db8/GD066_DarkHeather_FT.jpg"}, {"code": "DROS", "name": "Dusty Rose*", "hex": "#F4BED0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000029/e7b3a343/GD066_DustyRose_FT.jpg"}, {"code": "FORE", "name": "Forest Green*", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000013/83ac031d/GD066_ForestGreen_FT.jpg"}, {"code": "HBLA", "name": "Heather Black", "hex": "#3C3C3E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000239/GD066_HeatherBlack_FT.jpg"}, {"code": "LPIN", "name": "Light Pink*", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000017/fe5c6f50/GD066_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon*", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000003/2f3025ed/GD066_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green*", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000056/ca06d86c/GD066_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000057/41b72ef3/GD066_Navy_FT.jpg"}, {"code": "OWHI", "name": "Off White*", "hex": "#F4EBD2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000004/60c4c76a/GD066_OffWhite_FT.jpg"}, {"code": "PGON", "name": "Paragon*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000058/feeb2ed0/GD066_Paragon_FT.jpg"}, {"code": "PKLE", "name": "Pink Lemonade*", "hex": "#EF4C97", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000014/bdaa9240/GD066_PinkLemonade_FT.jpg"}, {"code": "PURP", "name": "Purple*", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000005/e1a1f6a8/GD066_Purple_FT.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000015/fc49cb15/GD066_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000016/9ee8d234/GD066_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey*", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000059/c80a5247/GD066_RingspunSportGrey_FT.jpg"}, {"code": "SAGE", "name": "Sage*", "hex": "#75C775", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000006/fa4dd6ca/GD066_Sage_FT.jpg"}, {"code": "SAND", "name": "Sand*", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00005a/27506560/GD066_Sand_FT.jpg"}, {"code": "SBLU", "name": "Stone Blue*", "hex": "#67A8B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00005b/6e060947/GD066_StoneBlue_FT.jpg"}, {"code": "SKYY", "name": "Sky*", "hex": "#7AFFFC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000017/4b1285e1/GD066_Sky_FT.jpg"}, {"code": "TANG", "name": "Tangerine*", "hex": "#FF7F49", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000018/25de8042/GD066_Tangerine_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00005c/9816005d/GD066_White_FT.jpg"}, {"code": "YHAZ", "name": "Yellow Haze*", "hex": "#FFF4BA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000007/e37a37b5/GD066_YellowHaze_FT.jpg"}]},
    {id:'gd056', brand:'Gildan', name:'Heavy Blend™ adult crew neck sweatshirt', sku:'GD056', tier:'standaard', inkoop:7.64, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/40991e8a/69296e765fa5b59a3a6c018e/4e98be6a/GD056_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ASHG", "name": "Ash*", "hex": "#C5CBC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b9a/c9c9987e/GD056_Ash_FT.jpg"}, {"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b9c/007d5868/GD056_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue*", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b9d/0ceafc33/GD056_CarolinaBlue_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*†", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b9e/34cb5e0b/GD056_Charcoal_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red*", "hex": "#AF0021", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b9f/794f622f/GD056_CherryRed_FT.jpg"}, {"code": "DCHO", "name": "Dark Chocolate*†", "hex": "#0C0A0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ba0/bce21f0a/GD056_DarkChocolate_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*†", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ba1/c7419829/GD056_DarkHeather_FT.jpg"}, {"code": "FACH", "name": "Fan Charcoal Heather*", "hex": "#4B4A4D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000231/GD056_FanCharcoalHeather_FT.jpg"}, {"code": "FADG", "name": "Fan Dark Green*", "hex": "#0B4629", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000232/GD056_FanDarkGreen_FT.jpg"}, {"code": "FADR", "name": "Fan Deep Royal*", "hex": "#03235A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000233/GD056_FanDeepRoyal_FT.jpg"}, {"code": "FORE", "name": "Forest Green*†", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ba2/d86ab512/GD056_ForestGreen_FT.jpg"}, {"code": "GOLD", "name": "Gold*", "hex": "#FFAF05", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ba4/f6974a38/GD056_Gold_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather*", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ba5/a82f9b75/GD056_GraphiteHeather_FT.jpg"}, {"code": "IGRN", "name": "Irish Green*†", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bad/93315ed5/GD056_IrishGreen_FT.jpg"}, {"code": "INBL", "name": "Indigo Blue*", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bac/1f886dd7/GD056_IndigoBlue_FT.jpg"}, {"code": "LBLU", "name": "Light Blue*", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bb4/331ca999/GD056_LightBlue_FT.jpg"}, {"code": "LPIN", "name": "Light Pink*", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bb5/7a350f60/GD056_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon*", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bb6/67d330f4/GD056_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bb7/e7d56eaa/GD056_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy*†", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bb9/26174d85/GD056_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange*†", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bbb/c4acaaef/GD056_Orange_FT.jpg"}, {"code": "PURP", "name": "Purple*", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bbf/3a869f88/GD056_Purple_FT.jpg"}, {"code": "REDD", "name": "Red*†", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bc0/a88c3204/GD056_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal*†", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bc1/fbe01148/GD056_Royal_FT.jpg"}, {"code": "SAGR", "name": "Safety Green*", "hex": "#B7FF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bc2/b37c56b0/GD056_SafetyGreen_FT.jpg"}, {"code": "SAND", "name": "Sand*", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c73d9c70d4a3269440000db/ba5b6a94/GD056_Sand_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey*†", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bc6/42a1d66c/GD056_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White*†", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bc8/94e498e2/GD056_White_FT.jpg"}]},
    {id:'jh30j', brand:'AWDis Just Hoods', name:'Kids AWDis sweatshirt', sku:'JH30J', tier:'standaard', inkoop:7.20, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e05f29e5/69296fee5fa5b59a3a6c3db2/JH30J_LS00_2026.jpg', sizes:["12", "34", "56", "78", "911", "1213"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce457d/0eec296d/JH30J_ArcticWhite_FT.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/6334561348718b73ba000010/JH30J_BabyPink_FT.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4580/f39c8d95/JH30J_BottleGreen_FT.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#002CCA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b9000020/JH30J_BrightRoyal_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4581/c919c99c/JH30J_Burgundy_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4582/853759c8/JH30J_Charcoal_FT.jpg"}, {"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b9/JH30J_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black*", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5dfabf886aa61163fd0006af/cfa5a552/JH30J_DeepBlack_FT.jpg"}, {"code": "DSAN", "name": "Desert Sand", "hex": "#DCC98F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/61545c1c3f6633447e000040/82322bda/JH30J_DesertSand_FT.jpg"}, {"code": "DUGN", "name": "Dusty Green", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000ba/JH30J_DustyGreen_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/61545c1c3f6633447e000041/abcfa100/JH30J_DustyPink_FT.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4583/ea8e3952/JH30J_FireRed_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4585/f0e17f5f/JH30J_HeatherGrey_FT.jpg"}, {"code": "HOPK", "name": "Hot Pink", "hex": "#F400BA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4586/3a13addd/JH30J_HotPink_FT.jpg"}, {"code": "ICEB", "name": "Ice Blue", "hex": "#B0EAFC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b9000021/JH30J_IceBlue_FT.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4588/cee839d6/JH30J_JetBlack_FT.jpg"}, {"code": "KELL", "name": "Kelly Green", "hex": "#11FF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4589/8071ae17/JH30J_KellyGreen_FT.jpg"}, {"code": "LIME", "name": "Lime Green", "hex": "#6DFF30", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4596/c4ca63a1/JH30J_LimeGreen_FT.jpg"}, {"code": "MUST", "name": "Mustard", "hex": "#C48702", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/61545c1c3f6633447e000042/29096800/JH30J_Mustard_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4597/5a28a35e/JH30J_NewFrenchNavy_FT.jpg"}, {"code": "OXNY", "name": "Oxford Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce4599/b5e6cfc6/JH30J_OxfordNavy_FT.jpg"}, {"code": "PEPP", "name": "Peppermint", "hex": "#8EFFCC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/61545c1c3f6633447e000043/cbdff72f/JH30J_Peppermint_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2302E0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce459b/cbc2841b/JH30J_Purple_FT.jpg"}, {"code": "RAGR", "name": "Rainforest Green", "hex": "#0B5E43", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b9000014/JH30J_RainforestGreen_FT.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce459d/38ba37ce/JH30J_RoyalBlue_FT.jpg"}, {"code": "SAPP", "name": "Sapphire Blue", "hex": "#027FFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce459e/ba988c62/JH30J_SapphireBlue_FT.jpg"}, {"code": "SEAF", "name": "Seafoam", "hex": "#60BAA9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b9000022/JH30J_Seafoam_FT.jpg"}, {"code": "SKYY", "name": "Sky Blue", "hex": "#8BD7EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce459f/a295c0de/JH30J_SkyBlue_FT.jpg"}, {"code": "STGY", "name": "Storm Grey", "hex": "#0C0D0D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce45a0/80c6817c/JH30J_StormGrey_FT.jpg"}, {"code": "SYEL", "name": "Sun Yellow", "hex": "#FFE800", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5c5c3e3deb9f5b1c75ce45a1/63f79459/JH30J_SunYellow_FT.jpg"}]},
    {id:'jh043', brand:'AWDis Just Hoods', name:'Varsity jacket', sku:'JH043', tier:'standaard', inkoop:14.45, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17768d79/692970425fa5b59a3a6c4aae/JH043_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BUHG", "name": "Burgundy/Heather Grey*", "hex": "#4500A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4371280e4563.jpg"}, {"code": "FRWH", "name": "Fire Red/White*", "hex": "#DA00FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce43735af0b89c.jpg"}, {"code": "HGWH", "name": "Heather Grey/White", "hex": "#A2A9FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce437474db41d7.jpg"}, {"code": "JBCH", "name": "Jet Black/Charcoal", "hex": "#000048", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4376dbf2be6d.jpg"}, {"code": "JBFR", "name": "Jet Black/Fire Red*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce43773e4a47ce.jpg"}, {"code": "JBHG", "name": "Jet Black/Heather Grey", "hex": "#0000A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4378dd133a64.jpg"}, {"code": "JBHP", "name": "Jet Black/Hot Pink", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4379ddf47d02.jpg"}, {"code": "JBSU", "name": "Jet Black/Sun Yellow*", "hex": "#0000E8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce437bcecacc7f.jpg"}, {"code": "JBWH", "name": "Jet Black/White*", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce437c79b5c79f.jpg"}, {"code": "KGWH", "name": "Kelly Green/White", "hex": "#0B99FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce437dc842d3eb.jpg"}, {"code": "ONHG", "name": "Oxford Navy/Heather Grey", "hex": "#0418A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce43934176d5a2.jpg"}, {"code": "ONWH", "name": "Oxford Navy/White*", "hex": "#0418FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce439464087305.jpg"}, {"code": "PUWH", "name": "Purple/White", "hex": "#2402FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4396a0d5285f.jpg"}, {"code": "RBWH", "name": "Royal Blue/White", "hex": "#003CFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4397c918188f.jpg"}]},
    {id:'by075', brand:'Build Your Brand', name:'Sweat crew neck', sku:'BY075', tier:'premium', inkoop:17.75, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ef2764a9/692972095fa5b59a3a6c91d6/BY075_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL", "XS"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce1b5ed39345f6.jpg"}, {"code": "CIRD", "name": "City Red*", "hex": "#D62439", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000046/BY075_CityRed_FT.jpg"}, {"code": "GAGR", "name": "Grass Green*", "hex": "#00AA86", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f026500006e/BY075_GrassGreen_FT.jpg"}, {"code": "GREY", "name": "Grey", "hex": "#DBDBE0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1b60a989b383.jpg"}, {"code": "INTB", "name": "Intense Blue*", "hex": "#1E67B5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000047/BY075_IntenseBlue_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt", "hex": "#EFEAEA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c00001f/BY075_LightAsphalt_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#403F47", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f026500002f/BY075_Navy_FT.jpg"}, {"code": "OLIV", "name": "Olive*", "hex": "#8E8066", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000030/BY075_Olive_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000019/BY075_White_FT.jpg"}, {"code": "WHSA", "name": "White Sand*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000058/BY075_WhiteSand_FT.jpg"}]},
    {id:'jh123', brand:'AWDis Just Hoods', name:'Signature heavyweight sweatshirt', sku:'JH123', tier:'standaard', inkoop:13.99, tags:["Organic", "Recycled"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5e0a7fea/692973045fa5b59a3a6cb681/JH123_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ABLU", "name": "Airforce Blue", "hex": "#4C5D75", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000012/JH123_AirforceBlue_FT.jpg"}, {"code": "AWHI", "name": "Arctic White", "hex": "#E7E9EC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000020/JH123_ArcticWhite_FT.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#002CCA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000021/JH123_BrightRoyal_FT.jpg"}, {"code": "DPBK", "name": "Deep Black*", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000022/JH123_DeepBlack_FT.jpg"}, {"code": "EAGN", "name": "Earthy Green", "hex": "#46613F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000023/JH123_EarthyGreen_FT.jpg"}, {"code": "ESPR", "name": "Espresso", "hex": "#4B3732", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000013/JH123_Espresso_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000024/JH123_HeatherGrey_FT.jpg"}, {"code": "NAST", "name": "Natural Stone", "hex": "#A3ADA6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000025/JH123_NaturalStone_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000026/JH123_NewFrenchNavy_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000027/JH123_SolidCharcoal_FT.jpg"}]},
    {id:'gd027', brand:'Gildan', name:'Hammer Maxweight Adult Crewneck Sweatshirt', sku:'GD027', tier:'standaard', inkoop:13.30, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/23c29358/692970aa5fa5b59a3a6c5b36/1865ac10/GD027_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL"], colors:[{"code": "BDUS", "name": "Blue Dusk", "hex": "#243548", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000215/GD027_BlueDusk_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#B51A3C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000216/GD027_CherryRed_FT.jpg"}, {"code": "DERY", "name": "Deep Royal", "hex": "#02368C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000217/GD027_DeepRoyal_FT.jpg"}, {"code": "GARN", "name": "Garnet", "hex": "#751731", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000218/GD027_Garnet_FT.jpg"}, {"code": "GRAV", "name": "Gravel", "hex": "#86858B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000219/GD027_Gravel_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#5E5840", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00021b/GD027_Olive_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#EDE7D9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00021a/GD027_OffWhite_FT.jpg"}, {"code": "PBMI", "name": "Pitch Black", "hex": "#191919", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00021c/GD027_PitchBlack_FT.jpg"}, {"code": "TANN", "name": "Tan", "hex": "#AB9274", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00021d/GD027_Tan_FT.jpg"}]},
    {id:'jh230', brand:'AWDis Just Hoods', name:'Organic sweatshirt', sku:'JH230', tier:'standaard', inkoop:12.05, tags:["Organic", "Recycled"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/65648c8e/69296e395fa5b59a3a6bf98a/JH230_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361e7/826fd62d/JH230_ArcticWhite_FT.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361e8/08c4f2e4/JH230_BabyPink_FT.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361e9/5c33b9cd/JH230_BottleGreen_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361ea/fb34ddfb/JH230_Burgundy_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361eb/2d711f50/JH230_Charcoal_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361ec/a106e88d/JH230_DeepBlack_FT.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361ed/130c0c0d/JH230_FireRed_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361ee/8f4af67f/JH230_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361ef/4a2f980c/JH230_NewFrenchNavy_FT.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4e97138200187361f0/c0ecc06e/JH230_RoyalBlue_FT.jpg"}]},
    {id:'gd069', brand:'Gildan', name:'Softstyle™ midweight adult fleece 1/4 zip sweatshirt', sku:'GD069', tier:'standaard', inkoop:14.30, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/938bcd22/692972735fa5b59a3a6c9fac/GD069_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00000c/d3f59358/GD069_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000021/034a668c/GD069_Charcoal_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000030/4cae6924/GD069_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00000d/62a44146/GD069_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00000e/9f8eff0a/GD069_Navy_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#F4EBD2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00000f/684580c4/GD069_OffWhite_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000031/061801a5/GD069_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000010/9e79e4be/GD069_RingspunSportGrey_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000022/907983b3/GD069_Sand_FT.jpg"}, {"code": "SBLU", "name": "Stone Blue", "hex": "#67A8B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000032/485d0053/GD069_StoneBlue_FT.jpg"}]},
    {id:'jh43j', brand:'AWDis Just Hoods', name:'Kids varsity jacket', sku:'JH43J', tier:'standaard', inkoop:14.60, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4cb71d1c/69296ccb5fa5b59a3a6bbf98/JH43J_LS00_2026.jpg', sizes:["34", "56", "78", "911", "1213"], colors:[{"code": "BUHG", "name": "Burgundy/Heather Grey", "hex": "#4500A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45b9474f96f3.jpg"}, {"code": "FRWH", "name": "Fire Red/White", "hex": "#D900FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45baa0c67d6b.jpg"}, {"code": "HPWH", "name": "Hot Pink/White", "hex": "#F400FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45bcef2d843b.jpg"}, {"code": "JBFR", "name": "Jet Black/Fire Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45bde580b0e0.jpg"}, {"code": "JBHG", "name": "Jet Black/Heather Grey", "hex": "#0000A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45bec4b44ed5.jpg"}, {"code": "JBWH", "name": "Jet Black/White", "hex": "#1A1DFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45c0db84540c.jpg"}, {"code": "ONHG", "name": "Oxford Navy/Heather  Grey", "hex": "#0417A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45cccfd60241.jpg"}, {"code": "ONWH", "name": "Oxford Navy/White", "hex": "#0417FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45cd6de0b1f3.jpg"}, {"code": "PUWH", "name": "Purple/White", "hex": "#2402FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45cf33ac462a.jpg"}, {"code": "RBWH", "name": "Royal Blue/White", "hex": "#003CFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45d0852349de.jpg"}]},
    {id:'by409', brand:'Build Your Brand', name:'Loose fit sweatshirt', sku:'BY409', tier:'premium', inkoop:18.45, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4159d63f/692970f05fa5b59a3a6c665b/BY409_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#2B262A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00017e/BY409_Black_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown", "hex": "#6F3E33", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00017f/BY409_ChocolateBrown_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#C6C5CB", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000180/BY409_HeatherGrey_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#453C4D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000181/BY409_Navy_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#8E8777", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000182/BY409_PaleOlive_FT.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#E9E1D4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000183/BY409_WhiteSand_FT.jpg"}]},
    {id:'bb003', brand:'Build Your Brand Basic', name:'Basic crew neck', sku:'BB003', tier:'standaard', inkoop:11.00, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9d03225a/69296a435fa5b59a3a6b639d/BB003_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fc0/6e2fd11f/BB003_Black_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#8E1B2C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fc1/a2c5bc3c/BB003_Burgundy_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#BEBEC4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fc3/bdfb2995/BB003_HeatherGrey_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#393744", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fc4/d312c989/BB003_Navy_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#706755", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4af26553/5fcf5c3a9713820018735fc5/f8bcb344/BB003_Olive_FT.jpg"}]},
    {id:'jh113', brand:'AWDis Just Hoods', name:'Vision Heavyweight Sweat', sku:'JH113', tier:'standaard', inkoop:12.70, tags:["Organic", "Recycled"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/05b8780d/692970415fa5b59a3a6c4aa4/JH113_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "DPBK", "name": "Deep Black*", "hex": "#2E2A32", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00026c/JH113_DeepBlack_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#9F9FAF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000003/JH113_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#1D222E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000004/JH113_NewFrenchNavy_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#414246", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000005/JH113_SolidCharcoal_FT.jpg"}]},
    {id:'gd56b', brand:'Gildan', name:'Heavy Blend™ youth crew neck sweatshirt', sku:'GD56B', tier:'standaard', inkoop:7.30, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0a3ea335/69296e3a5fa5b59a3a6bf9c0/GD56B_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2de2/00514cf9/GD56B_Black_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2de3/32a9f731/GD56B_DarkHeather_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2dec/f56be593/GD56B_Navy_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ded/280c75ae/GD56B_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2dee/e6f2e771/GD56B_Royal_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2df0/66ddd1b2/GD56B_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2df1/d10e986e/GD56B_White_FT.jpg"}]},
    {id:'jh046', brand:'AWDis Just Hoods', name:'Sophomore ¼ zip sweatshirt', sku:'JH046', tier:'standaard', inkoop:13.35, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b30fa628/69296e745fa5b59a3a6c012c/JH046_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/812bfd75/5c5c3e3deb9f5b1c75ce43af/3b1c0573/JH046_Burgundy_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/812bfd75/5c5c3e3deb9f5b1c75ce43b0/06682c81/JH046_Charcoal_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/812bfd75/674084e63d2d8f57b9000023/c8df37b8/JH046_DeepBlack_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/812bfd75/5c5c3e3deb9f5b1c75ce43b2/620d3e24/JH046_HeatherGrey_FT.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/812bfd75/5c5c3e3deb9f5b1c75ce43b4/8d8780d7/JH046_JetBlack_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/812bfd75/5c5c3e3deb9f5b1c75ce43bd/a58868e3/JH046_NewFrenchNavy_FT.jpg"}]},
    {id:'jh130', brand:'AWDis Just Hoods', name:'Graduate heavyweight sweatshirt', sku:'JH130', tier:'standaard', inkoop:11.45, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2dcff00e/692973805fa5b59a3a6cc95b/JH130_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce455c82fdec8a.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce455d0dfba743.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce455e77ab5f49.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45631a5ed2a3.jpg"}]},
    {id:'jh147', brand:'AWDis Just Hoods', name:'Campus full-zip sweatshirt', sku:'JH147', tier:'premium', inkoop:17.15, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7af69e50/692972e05fa5b59a3a6cb10f/JH147_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000c8/JH147_Charcoal_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000c9/JH147_DeepBlack_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000ca/JH147_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000cb/JH147_NewFrenchNavy_FT.jpg"}]},
    {id:'by010', brand:'Build Your Brand', name:'Light crew sweatshirt', sku:'BY010', tier:'standaard', inkoop:14.00, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/d9a5aecf/69296f735fa5b59a3a6c2996/BY010_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18f5648586d5.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#606060", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce18f62f9bc211.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#B5B5B5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce18f7e78825e2.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce18fe5e38176b.jpg"}]},
    {id:'jh037', brand:'AWDis Just Hoods', name:'Women’s cropped 1/4-zip sweat', sku:'JH037', tier:'standaard', inkoop:11.15, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ef68d3ef/692970b05fa5b59a3a6c5c36/JH037_LS00_2026.jpg', sizes:["2XS", "XS", "S", "M", "L", "XL"], colors:[{"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e000045/JH037_DeepBlack_FT.jpg"}, {"code": "LAVE", "name": "Lavender", "hex": "#C69BFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7d05a3db/6154683aabc9963faa000012/JH037_Lavendar_FT.jpg"}, {"code": "SKYY", "name": "Sky Blue", "hex": "#8BD7EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e000048/JH037_SkyBlue_FT.jpg"}]},
    {id:'by198', brand:'Build Your Brand', name:'Oversize cut on sleeve long sleeve', sku:'BY198', tier:'standaard', inkoop:11.20, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e61a27f0/692971fe5fa5b59a3a6c9024/BY198_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/45da68d7/61a7ce3cfe9cd9426c00005b/d1b07c0b/BY198_Black_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#E8E1D0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/45da68d7/61a7ce3cfe9cd9426c00005c/fbfd0883/BY198_Sand_FT.jpg"}]},
    {id:'by205', brand:'Build Your Brand', name:'Ultra heavy cotton crew neck', sku:'BY205', tier:'premium', inkoop:28.20, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0d36d289/69296e775fa5b59a3a6c0196/BY205_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7bcd6675/6564b081d9e4aa4eae000011/BY205_Black_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#B3B3B7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7bcd6675/6564b081d9e4aa4eae000012/BY205_HeatherGrey_FT.jpg"}]},
    {id:'by301', brand:'Build Your Brand', name:'Women’s vintage heavy crew neck', sku:'BY301', tier:'premium', inkoop:18.30, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/70a92fa4/692969f15fa5b59a3a6b570d/BY301_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cf5b7d17/674085a63d2d8f0265000038/ca55aa77/BY301_Black_FT.jpg"}, {"code": "MAGN", "name": "Magnet", "hex": "#454547", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cf5b7d17/674085a63d2d8f026500000f/4c2b84f7/BY301_Magnet_FT.jpg"}]},
    {id:'by015', brand:'Build Your Brand', name:'Sweat college jacket', sku:'BY015', tier:'premium', inkoop:23.50, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/637645e4/692970c85fa5b59a3a6c5fea/BY015_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BKRD", "name": "Black/Red", "hex": "#212132", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce193686eb39fe.jpg"}, {"code": "BKWH", "name": "Black/White", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce19373010617b.jpg"}]},
    {id:'jh124', brand:'AWDis Just Hoods', name:'Signature Heavyweight Bomber Sweat', sku:'JH124', tier:'premium', inkoop:19.05, tags:["Organic", "Recycled"], eco:true, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/027f653d/69296ca65fa5b59a3a6bb991/JH124_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "DPBK", "name": "Deep Black", "hex": "#28252A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000014/JH124_DeepBlack_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#3B373F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000015/JH124_SolidCharcoal_FT.jpg"}]},
    {id:'lw06t', brand:'Larkwood', name:'Crew neck sweatshirt with shoulder poppers', sku:'LW06T', tier:'standaard', inkoop:7.60, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ad2e7978/6929722c5fa5b59a3a6c9635/LW06T_LS00_2026.jpg', sizes:["612", "1218", "1824", "2436", "34"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5e6b62efce60dd6158000057.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce52fdd235c5c2.jpg"}]},
  ],
  hoodie:[
    {id:'jh001', brand:'AWDis Just Hoods', name:'College hoodie', sku:'JH001', tier:'standaard', inkoop:9.99, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a79b7090/69296a695fa5b59a3a6b6914/JH001_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ABLU", "name": "Airforce Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40e4a9319fa4.jpg"}, {"code": "AGRE", "name": "Apple Green", "hex": "#A8FF93", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40e6ea1317e6.jpg"}, {"code": "ASHG", "name": "Ash", "hex": "#DEE1DA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40e87e83867a.jpg"}, {"code": "ATBL", "name": "Atlantic Blue", "hex": "#5074A8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b9000018/JH001_AtlanticBlue_FT.jpg"}, {"code": "AWHI", "name": "Arctic White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40e79fc6df78.jpg"}, {"code": "BAPK", "name": "Baby Pink*", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40e9fb9d0196.jpg"}, {"code": "BORA", "name": "Burnt Orange", "hex": "#F92C0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40ef82c02f43.jpg"}, {"code": "BOTT", "name": "Bottle Green*", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40eb2bad5d11.jpg"}, {"code": "BRIC", "name": "Brick Red", "hex": "#78002F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40ec1e7d0129.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#002CCA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/849ee749/6564b0d6d9e4aa3e83000063/JH001_BrightRoyal_FT.jpg"}, {"code": "BSMO", "name": "Black Smoke", "hex": "#1F2530", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5dfb502d792e193a5f0002a337c75be8.jpg"}, {"code": "BUPK", "name": "Bubblegum Pink", "hex": "#F48BA9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000264/JH001_BubblegumPink_FT.jpg"}, {"code": "BURG", "name": "Burgundy*", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40ee32e070e8.jpg"}, {"code": "BUSM", "name": "Burgundy Smoke", "hex": "#4D033E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40edc6cd0f1a.jpg"}, {"code": "CAGR", "name": "Cactus Green", "hex": "#6B9972", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b9000005/JH001_CactusGreen_FT.jpg"}, {"code": "CARA", "name": "Caramel Latte", "hex": "#A07A66", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e00002c/JH001_CaramelLatte_FT.jpg"}, {"code": "CATO", "name": "Caramel Toffee", "hex": "#583C2B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9d25c299/5d7a714954ef21700a0000127814f9d9.jpg"}, {"code": "CFPK", "name": "Candyfloss Pink", "hex": "#FF68F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40f02d77745e.jpg"}, {"code": "CGRE", "name": "Combat Green", "hex": "#253130", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9d25c299/5d7a714954ef21700a00001310639a68.jpg"}, {"code": "CHAR", "name": "Charcoal*", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40f1d87d5fb1.jpg"}, {"code": "CHFB", "name": "Chocolate Fudge Brownie", "hex": "#352321", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000ac/JH001_ChocolateFudgeBrownie_FT.jpg"}, {"code": "CITR", "name": "Citrus", "hex": "#C1F90F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/849ee749/6564b0d6d9e4aa3e83000064/JH001_Citrus_FT.jpg"}, {"code": "CORN", "name": "Cornflower Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40f26d4831a0.jpg"}, {"code": "CRAN", "name": "Cranberry", "hex": "#B6008F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40f355cf6e69.jpg"}, {"code": "DENI", "name": "Denim Blue", "hex": "#072584", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40f5c24ef5ac.jpg"}, {"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63494d9e48718b67af00001d/96787545/JH001_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black*", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9d25c299/5dfabf866aa61163fd00044b9c46ca4b.jpg"}, {"code": "DPSB", "name": "Deep Sea Blue", "hex": "#002A31", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25ec4d63/5c5c3e3deb9f5b1c75ce40f4b5234a54.jpg"}, {"code": "DROS", "name": "Dusty Rose", "hex": "#AE618C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e00002d/JH001_DustyRose_FT.jpg"}, {"code": "DSAN", "name": "Desert Sand", "hex": "#DCC98F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/35c2f95c/5c5c3e3deb9f5b1c75ce40f6/daef7921/JH001_DesertSand_FT.jpg"}]},
    {id:'gd067', brand:'Gildan', name:'Softstyle™ Midweight fleece adult hoodie', sku:'GD067', tier:'standaard', inkoop:10.95, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/63ee7fc8/692973415fa5b59a3a6cc000/GD067_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL", "XS"], colors:[{"code": "AQTI", "name": "Aquatic", "hex": "#6DA39F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000008/dd09bf19/GD067_Aquatic_FT.jpg"}, {"code": "ASGR", "name": "Ash Grey*", "hex": "#D1DDE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00002a/164b2170/GD067_AshGrey_FT.jpg"}, {"code": "BDUS", "name": "Blue Dusk", "hex": "#172F41", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000019/a7795600/GD067_BlueDusk_FT.jpg"}, {"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00005d/6f6d6ba5/GD067_Black_FT.jpg"}, {"code": "BSAV", "name": "Brown Savana", "hex": "#64554A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e00001a/3318cc18/GD067_BrownSavana_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00001b/d5905347/GD067_CarolinaBlue_FT.jpg"}, {"code": "CDRD", "name": "Cardinal Red", "hex": "#840042", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e000009/5ebd8003/GD067_CardinalRed_FT.jpg"}, {"code": "CEME", "name": "Cement", "hex": "#9CA5A5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000018/0310c4f7/GD067_Cement_FT.jpg"}, {"code": "CHAR", "name": "Charcoal†", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00005e/6071535e/GD067_Charcoal_FT.jpg"}, {"code": "COBA", "name": "Cobalt", "hex": "#0005FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e00002b/fbbb9145/GD067_Cobalt_FT.jpg"}, {"code": "COCO", "name": "Cocoa", "hex": "#6B3D2E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000019/b363ca20/GD067_Cocoa_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300001a/fc70f9c8/GD067_Daisy_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee00005f/c4eeda3c/GD067_DarkHeather_FT.jpg"}, {"code": "DROS", "name": "Dusty Rose", "hex": "#F4BED0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00001c/4d2424f7/GD067_DustyRose_FT.jpg"}, {"code": "FORE", "name": "Forest Green", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300001b/7f25427f/GD067_ForestGreen_FT.jpg"}, {"code": "HBLA", "name": "Heather Black*", "hex": "#46454B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/190159c9/693aaa41ba355a0a6b000019/GD067_HeatherBlack_FT.jpg"}, {"code": "LPIN", "name": "Light Pink†", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000060/1e607380/GD067_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon†", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000061/8a0b3890/GD067_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green†", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000062/164bb00f/GD067_MilitaryGreen_FT.jpg"}, {"code": "MUST", "name": "Mustard", "hex": "#C1954B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300001c/9e4e50dd/GD067_Mustard_FT.jpg"}, {"code": "NAVY", "name": "Navy*†", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000063/8e2cafc0/GD067_Navy_FT.jpg"}, {"code": "OWHI", "name": "Off White*", "hex": "#F4EBD2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00001d/b4fc0693/GD067_OffWhite_FT.jpg"}, {"code": "PGON", "name": "Paragon", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000064/8c6dc148/GD067_Paragon_FT.jpg"}, {"code": "PIST", "name": "Pistachio", "hex": "#A5F96B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300001e/1a1cfae4/GD067_Pistachio_FT.jpg"}, {"code": "PKLE", "name": "Pink Lemonade", "hex": "#EF4C97", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300001d/852f7270/GD067_PinkLemonade_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300001f/78f5a0c7/GD067_Purple_FT.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000065/4229a638/GD067_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000067/840ef185/GD067_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey*†", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/63874ded48718b69ee000066/55df8b93/GD067_RingspunSportGrey_FT.jpg"}, {"code": "SAGE", "name": "Sage", "hex": "#75C775", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/674086693d2d8f284e00002c/ff4e8a39/GD067_Sage_FT.jpg"}]},
    {id:'jh01j', brand:'AWDis Just Hoods', name:'Kids hoodie', sku:'JH01J', tier:'standaard', inkoop:8.45, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9c29489c/69296b355fa5b59a3a6b8934/JH01J_LS00_2026.jpg', sizes:["12", "34", "56", "78", "911", "1213", "1/2", "3/4", "5/6", "7/8"], colors:[{"code": "ABLU", "name": "Airforce Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4269843160fc.jpg"}, {"code": "ASHG", "name": "Ash", "hex": "#DEE1DA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce426ba209ce0a.jpg"}, {"code": "AWHI", "name": "Arctic White", "hex": "#EEEDF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce426a4ae6deec.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce426c4c4f9b37.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/22db6aa2/5c5c3e3deb9f5b1c75ce426d/844c0932/JH01J_BottleGreen_FT.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#002CCA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b900000a/JH01J_BrightRoyal_FT.jpg"}, {"code": "BSMO", "name": "Black Smoke", "hex": "#1F2530", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4c97138200187361b0/b7187f4d/JH01J_BlackSmoke_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce426e321707cc.jpg"}, {"code": "CFPK", "name": "Candyfloss Pink", "hex": "#FF68F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b2ecbad4/5c5c3e3deb9f5b1c75ce426f6d1c380d.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce42707cd3c8fb.jpg"}, {"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b3/JH01J_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5dfabf876aa61163fd0005324b274a87.jpg"}, {"code": "DSAN", "name": "Desert Sand", "hex": "#DCC98F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b2/JH01J_DesertSand_FT.jpg"}, {"code": "DUGN", "name": "Dusty Green", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b4/JH01J_DustyGreen_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e7cd58d9/5c5c3e3deb9f5b1c75ce4271151dff6d.jpg"}, {"code": "FORE", "name": "Forest Green", "hex": "#02190C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/22db6aa2/5c5c3e3deb9f5b1c75ce4273/e7519b62/JH01J_ForestGreen_FT.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4272a850a967.jpg"}, {"code": "HBLU", "name": "Hawaiian Blue", "hex": "#2BFCFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce42740044940a.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce42758a7dd897.jpg"}, {"code": "HOPK", "name": "Hot Pink", "hex": "#F400BA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4276423c9b3b.jpg"}, {"code": "ICEB", "name": "Ice Blue", "hex": "#B0EAFC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b900000b/JH01J_IceBlue_FT.jpg"}, {"code": "JADE", "name": "Jade", "hex": "#006F34", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4277a9c93ba5.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4278fa455023.jpg"}, {"code": "KELL", "name": "Kelly Green", "hex": "#11FF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce42791e530a9f.jpg"}, {"code": "KHAK", "name": "Khaki", "hex": "#727533", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b900000c/JH01J_Khaki_FT.jpg"}, {"code": "LILA", "name": "Lilac", "hex": "#C9C7DD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b900001c/JH01J_Lilac_FT.jpg"}, {"code": "LIME", "name": "Lime Green", "hex": "#6DFF30", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce428d836d857f.jpg"}, {"code": "NAST", "name": "Natural Stone", "hex": "#A3ADA6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/674084e63d2d8f57b900001d/JH01J_NaturalStone_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce428e9b0de34b.jpg"}, {"code": "OCRU", "name": "Orange Crush", "hex": "#FF7019", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4290a8db9ed7.jpg"}]},
    {id:'jh003', brand:'AWDis Just Hoods', name:'Varsity hoodie', sku:'JH003', tier:'standaard', inkoop:11.45, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/42548a14/692973b85fa5b59a3a6cd19d/JH003_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "XS", "3XL", "4XL", "5XL"], colors:[{"code": "AWFN", "name": "Arctic White/French Navy", "hex": "#FFFF07", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce415071c8d6f8.jpg"}, {"code": "BPAW", "name": "Baby Pink/Arctic White", "hex": "#FFB2FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4152ee05d4fd.JPG"}, {"code": "BUCH", "name": "Burgundy/Charcoal*†", "hex": "#450048", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce415462a8555a.jpg"}, {"code": "BUGO", "name": "Burgundy/Gold", "hex": "#4500B7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4155d2b1b17e.jpg"}, {"code": "CHBU", "name": "Charcoal/Burgundy", "hex": "#3F4800", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41580d1a69c0.jpg"}, {"code": "CHHG", "name": "Charcoal/Heather Grey", "hex": "#3F48A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4159acff17c1.jpg"}, {"code": "CHJB", "name": "Charcoal/Jet Black*", "hex": "#3F481D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce415a173be5b2.jpg"}, {"code": "CHOR", "name": "Charcoal/Orange Crush", "hex": "#3F4870", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce415be30a6f29.jpg"}, {"code": "FGGO", "name": "Forest Green/Gold", "hex": "#0219B7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce415f500801bf.jpg"}, {"code": "FRAW", "name": "Fire Red/Arctic White", "hex": "#D900FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce415d61b73ede.jpg"}, {"code": "FRJB", "name": "Fire Red/Jet Black*", "hex": "#D9001D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce415e8b475b7c.jpg"}, {"code": "HBON", "name": "Hawaiian Blue/Oxford Navy", "hex": "#2BFC17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4161f8d3fd28.jpg"}, {"code": "HGFN", "name": "Heather Grey/French Navy*†△", "hex": "#A1A907", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4163c1ae4fd1.jpg"}, {"code": "HGFR", "name": "Heather Grey/Fire Red*", "hex": "#A1A900", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4162f342411b.jpg"}, {"code": "HGJB", "name": "Heather Grey/Jet Black", "hex": "#A1A91D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4164e30176ba.jpg"}, {"code": "HGSA", "name": "Heather Grey/Sapphire Blue", "hex": "#A1A97F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41658c7b9401.jpg"}, {"code": "HPFN", "name": "Hot Pink/French Navy*", "hex": "#F40007", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4167f0e2dc15.jpg"}, {"code": "JBAW", "name": "Jet Black/Arctic White", "hex": "#1A1DFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4169e8f19dac.jpg"}, {"code": "JBFR", "name": "Jet Black/Fire Red*†△", "hex": "#1A1D00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce416ae0333b89.jpg"}, {"code": "JBGO", "name": "Jet Black/Gold†*", "hex": "#1A1DB7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce416b904fd323.jpg"}, {"code": "JBHG", "name": "Jet Black/Heather Grey †*", "hex": "#A1A91D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce416c56e474fc.jpg"}, {"code": "JBHP", "name": "Jet Black/Hot Pink*", "hex": "#1A1D00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce416d743f64b2.jpg"}, {"code": "JBKG", "name": "Jet Black/Kelly Green*", "hex": "#000099", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce416e7335e50d.jpg"}, {"code": "JBOR", "name": "Jet Black/Orange Crush*", "hex": "#1A1D70", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce416f87bb01ad.jpg"}, {"code": "JBPU", "name": "Jet Black/Purple", "hex": "#1A1D02", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41707146f3b7.jpg"}, {"code": "JBSA", "name": "Jet Black/Sapphire Blue*", "hex": "#1A1D7F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4171dd0ccdc3.jpg"}, {"code": "JBSU", "name": "Jet Black/Sun Yellow", "hex": "#1A1DE8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4172c01f152c.jpg"}, {"code": "KGAW", "name": "Kelly Green/Arctic White", "hex": "#11FFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4173a1af056e.jpg"}, {"code": "NFFR", "name": "New French Navy/Fire Red†*", "hex": "#000700", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4187ec113227.jpg"}, {"code": "NFHG", "name": "New French Navy/Heather Grey*†△", "hex": "#0007A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4188a97ed264.jpg"}]},
    {id:'by011', brand:'Build Your Brand', name:'Heavy hoodie', sku:'BY011', tier:'standaard', inkoop:13.95, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ee0949bc/6929707e5fa5b59a3a6c5303/BY011_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL", "XS"], colors:[{"code": "BARK", "name": "Bark*†", "hex": "#84503F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/06bdeeb3/61a7cd9efe9cd9037100005f/BY011_Bark_FT.jpg"}, {"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18ffd1a4cd82.jpg"}, {"code": "BOTT", "name": "Bottle Green*†", "hex": "#274649", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/56304ce2/5fcf5c4297138200187360c0/BY011_BottleGreen_FT.jpg"}, {"code": "BURG", "name": "Burgundy*†", "hex": "#A83945", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/06bdeeb3/61a7cd9efe9cd90371000060/BY011_Burgundy_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown*†", "hex": "#603C32", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f0265000018/BY011_ChocolateBrown_FT.jpg"}, {"code": "CHAR", "name": "Charcoal*†", "hex": "#494949", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1900134f1183.jpg"}, {"code": "CIRD", "name": "City Red*†", "hex": "#E2142D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/56304ce2/5fcf5c4297138200187360c1/BY011_CityRed_FT.jpg"}, {"code": "FORE", "name": "Forest Green*†", "hex": "#01916E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b2300001e/BY011_ForestGreen_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey*†", "hex": "#DBDBDB", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce1903917034f9.jpg"}, {"code": "HIPK", "name": "Hibiskus Pink*†", "hex": "#ED3671", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000020/BY011_HibiskusPink_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt*†", "hex": "#D1D1D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/06bdeeb3/61a7cd9efe9cd90371000061/BY011_LightAsphalt_FT.jpg"}, {"code": "LILA", "name": "Lilac*†", "hex": "#D2C7DD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/56304ce2/5fcf5c4297138200187360c2/BY011_Lilac_FT.jpg"}, {"code": "LNAV", "name": "Light Navy*", "hex": "#212A49", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce190f/137a86af/BY011_LightNavy_FT.jpg"}, {"code": "NAVY", "name": "Navy*†", "hex": "#323444", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce1910db7c8c59.jpg"}, {"code": "NEMT", "name": "Neo Mint*†", "hex": "#B8DDC3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000022/BY011_NeoMint_FT.jpg"}, {"code": "OCBL", "name": "Ocean Blue*†", "hex": "#C3D7DD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000023/BY011_OceanBlue_FT.jpg"}, {"code": "OLIV", "name": "Olive*†", "hex": "#444431", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce19122d6b13f2.jpg"}, {"code": "PALE", "name": "Pale Leaf*†", "hex": "#50726B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000024/BY011_PaleLeaf_FT.jpg"}, {"code": "PAOR", "name": "Paradise Orange*†", "hex": "#FC9205", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000025/BY011_ParadiseOrange_FT.jpg"}, {"code": "PLPU", "name": "Plum Purple*†", "hex": "#3F283A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/dfbe5f43/674085a63d2d8f026500007d/BY011_PlumPurple_FT.jpg"}, {"code": "POBL", "name": "Powder Blue*†", "hex": "#98B1DA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f00012b/BY011_PowderBlue_FT.jpg"}, {"code": "PUNI", "name": "Purple Night*†", "hex": "#604C66", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000026/BY011_PurpleNight_FT.jpg"}, {"code": "RUBY", "name": "Ruby*", "hex": "#D8243C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000027/BY011_Ruby_FT.jpg"}, {"code": "SAND", "name": "Sand*†", "hex": "#F9F4EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce1913/705d3903/BY011_Sand_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink*†", "hex": "#E5BCCE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f00012c/BY011_SoftPink_FT.jpg"}, {"code": "SOYE", "name": "Soft Yellow†", "hex": "#D8CDAB", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/06bdeeb3/61a7cd9efe9cd90371000062/BY011_SoftYellow_FT.jpg"}, {"code": "TYEL", "name": "Taxi Yellow*", "hex": "#F4D466", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000029/BY011_TaxiYellow_FT.jpg"}, {"code": "ULVI", "name": "Ultra Violet*", "hex": "#8D6EC1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce1914/5f54cd65/BY011_UltraViolet_FT.jpg"}, {"code": "WHIT", "name": "White*†", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce191566797f3b.jpg"}]},
    {id:'gd057', brand:'Gildan', name:'Heavy Blend™ hooded sweatshirt', sku:'GD057', tier:'standaard', inkoop:9.78, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/07691191/69296fbf5fa5b59a3a6c362f/GD057_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ASHG", "name": "Ash", "hex": "#C5CBC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bcb/20aa15a4/GD057_Ash_FT.jpg"}, {"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bcd/54bd8d8e/GD057_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bce/acdab198/GD057_CarolinaBlue_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bcf/0acc575a/GD057_Charcoal_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#AF0021", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bd0/889cd2a3/GD057_CherryRed_FT.jpg"}, {"code": "DCHO", "name": "Dark Chocolate", "hex": "#0C0A0E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bd1/50deafe3/GD057_DarkChocolate_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bd2/8b9b1e35/GD057_DarkHeather_FT.jpg"}, {"code": "FACH", "name": "Fan Charcoal Heather", "hex": "#444346", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000234/GD057_FanCharcoalHeather_FT.jpg"}, {"code": "FADG", "name": "Fan Dark Green", "hex": "#02402D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000235/GD057_FanDarkGreen_FT.jpg"}, {"code": "FADR", "name": "Fan Deep Royal", "hex": "#011E56", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000236/GD057_FanDeepRoyal_FT.jpg"}, {"code": "FORE", "name": "Forest Green", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c38000055/9aab0dfb/GD057_ForestGreen_FT.jpg"}, {"code": "GARN", "name": "Garnet", "hex": "#590025", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bd4/dfc7b951/GD057_Garnet_FT.jpg"}, {"code": "GOLD", "name": "Gold", "hex": "#FFAF05", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bd5/fbaf3833/GD057_Gold_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bd6/fce7e562/GD057_GraphiteHeather_FT.jpg"}, {"code": "HELI", "name": "Heliconia", "hex": "#FF14D1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bdc/658c4fcf/GD057_Heliconia_FT.jpg"}, {"code": "IGRN", "name": "Irish Green*", "hex": "#1EFF23", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8082dcf2/5c5c3e3deb9f5b1c75ce2bde/daf5a168/GD057_IrishGreen_FT.jpg"}, {"code": "INBL", "name": "Indigo Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bdd/5a328466/GD057_IndigoBlue_FT.jpg"}, {"code": "LBLU", "name": "Light Blue", "hex": "#9FE5FC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2be5/327a0f46/GD057_LightBlue_FT.jpg"}, {"code": "LPIN", "name": "Light Pink*", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2be6/45775f96/GD057_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon*", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2be7/c53d4c15/GD057_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green*", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2be8/42ec7ad0/GD057_MilitaryGreen_FT.jpg"}, {"code": "MINT", "name": "Mint Green", "hex": "#91FF96", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c38000057/56e427d8/GD057_MintGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bea/046d9648/GD057_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bec/1f208b56/GD057_Orange_FT.jpg"}, {"code": "ORCH", "name": "Orchid", "hex": "#C1B5FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bed/91d06ab0/GD057_Orchid_FT.jpg"}, {"code": "PURP", "name": "Purple*", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bf0/1225ff3a/GD057_Purple_FT.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bf1/dd3287e0/GD057_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bf2/ff7a79e5/GD057_Royal_FT.jpg"}, {"code": "SAGR", "name": "Safety Green", "hex": "#B7FF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bf3/5d66d01c/GD057_SafetyGreen_FT.jpg"}, {"code": "SAND", "name": "Sand*", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bf6/fb11ceb9/GD057_Sand_FT.jpg"}]},
    {id:'jh050', brand:'AWDis Just Hoods', name:'Zoodie', sku:'JH050', tier:'standaard', inkoop:12.99, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/fb389baf/69296fdb5fa5b59a3a6c3ac2/JH050_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ABLU", "name": "Airforce Blue", "hex": "#385269", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000267/JH050_AirforceBlue_FT.jpg"}, {"code": "ASHG", "name": "Ash", "hex": "#DEE1DA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f3b0359be3.jpg"}, {"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f2e3001271.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#F5C4CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000268/JH050_BabyPink_FT.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f47ade5b0d.jpg"}, {"code": "BSMO", "name": "Black Smoke", "hex": "#1F2530", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5d7a714954ef21700a00001d08c756d3.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f5109a1351.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f6e2f93011.jpg"}, {"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000bc/JH050_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black*", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4d97138200187361bc/f78b69bd/JH050_DeepBlack_FT.jpg"}, {"code": "DSAN", "name": "Desert Sand", "hex": "#DCC98F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000bb/JH050_DesertSand_FT.jpg"}, {"code": "DUGN", "name": "Dusty Green", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000bd/JH050_DustyGreen_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000be/JH050_DustyPink_FT.jpg"}, {"code": "FORE", "name": "Forest Green", "hex": "#02190C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f8514966bf.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f7e73999bf.jpg"}, {"code": "HBLU", "name": "Hawaiian Blue", "hex": "#2BFCFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43f9b9e34445.jpg"}, {"code": "HGRE", "name": "Heather Grey*", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43fae6e4297e.jpg"}, {"code": "HOPK", "name": "Hot Pink", "hex": "#F400BA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43fb77db3305.jpg"}, {"code": "JBLA", "name": "Jet Black*", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43fc21454a73.jpg"}, {"code": "KELL", "name": "Kelly Green", "hex": "#11FF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce43fd3f527d1c.jpg"}, {"code": "MUST", "name": "Mustard", "hex": "#C48702", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5d7a714954ef21700a00001e0a834611.jpg"}, {"code": "NAST", "name": "Natural Stone", "hex": "#C0B2A6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000269/JH050_NaturalStone_FT.jpg"}, {"code": "NFNA", "name": "New French Navy*", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce441298116a73.jpg"}, {"code": "OCRU", "name": "Orange Crush", "hex": "#FF7019", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4413e748e1ba.jpg"}, {"code": "OXNY", "name": "Oxford Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b2ecbad4/5c5c3e3deb9f5b1c75ce44140d85e8a8.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2302E0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4415bf48679d.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce441703fae0e8.jpg"}, {"code": "SAPP", "name": "Sapphire Blue", "hex": "#027FFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4418bb8fb496.jpg"}, {"code": "SKYB", "name": "Sky Blue", "hex": "#8EA3B3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00026a/JH050_SkyBlue_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#45444B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f9978651/692832a89eee9a377f00026b/09bd63c4/JH050_SolidCharcoal_FT.jpg"}]},
    {id:'jh01f', brand:'AWDis Just Hoods', name:'Girlie college hoodie', sku:'JH01F', tier:'standaard', inkoop:9.99, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3a344a5/692972255fa5b59a3a6c94f4/JH01F_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "ABLU", "name": "Airforce Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9d8cbfc4/5c5c3e3deb9f5b1c75ce423e6d8f1555.jpg"}, {"code": "AWHI", "name": "Arctic White", "hex": "#E8E8E8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce423f3bfb378b.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4241d26d2741.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4242ca866b02.jpg"}, {"code": "BSMO", "name": "Black Smoke", "hex": "#1F2530", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4c97138200187361a8/59a5dc32/JH01F_BlackSmoke_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4243c864a8c4.jpg"}, {"code": "CFPK", "name": "Candyfloss Pink", "hex": "#FF68F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b2ecbad4/5c5c3e3deb9f5b1c75ce4244805fa8ed.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4245946c0683.jpg"}, {"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b0/JH01F_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5dfabf876aa61163fd000513e37b8e7b.jpg"}, {"code": "DUGN", "name": "Dusty Green", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000b1/JH01F_DustyGreen_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce42463bf848bb.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9d8cbfc4/5c5c3e3deb9f5b1c75ce4247eadf36ec.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9d8cbfc4/5c5c3e3deb9f5b1c75ce4249cba6fe6f.jpg"}, {"code": "HOPK", "name": "Hot Pink", "hex": "#F400BA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce424ac2e26109.jpg"}, {"code": "JADE", "name": "Jade", "hex": "#006F34", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce424b681436df.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce424c5af346ee.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce425ce59f456a.jpg"}, {"code": "NUDE", "name": "Nude", "hex": "#D59D8B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce425d3801da2d.jpg"}, {"code": "OXNY", "name": "Oxford Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce425fcab51e3e.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2302E0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce426159cd5b5e.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce4263a904bbb3.jpg"}, {"code": "SAPP", "name": "Sapphire Blue", "hex": "#027FFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/366d7e1b/5c5c3e3deb9f5b1c75ce426427072729.jpg"}, {"code": "SKYY", "name": "Sky Blue", "hex": "#8BD7EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2bc484e1/5c5c3e3deb9f5b1c75ce42654e617659.jpg"}, {"code": "VMIL", "name": "Vanilla Milkshake", "hex": "#FCF9C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4c97138200187361aa/28cee422/JH01F_VanillaMilkshake_FT.jpg"}]},
    {id:'jh120', brand:'AWDis Just Hoods', name:'Signature heavyweight hoodie', sku:'JH120', tier:'premium', inkoop:17.49, tags:["Organic", "Recycled"], eco:true, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/146893f7/69296f345fa5b59a3a6c20d6/JH120_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ABLU", "name": "Airforce Blue", "hex": "#30637E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce3496000019/JH120_AirforceBlue_FT.jpg"}, {"code": "ASHG", "name": "Ash", "hex": "#D2CEDB", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c4100000a/JH120_Ash_FT.jpg"}, {"code": "ATBL", "name": "Atlantic Blue", "hex": "#536284", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f9978651/692833529eee9a2c4100000b/6b2aa406/JH120_AtlanticBlue_FT.jpg"}, {"code": "AWHI", "name": "Arctic White", "hex": "#F2F1F3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce349600001a/JH120_ArcticWhite_FT.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce349600001b/JH120_BabyPink_FT.jpg"}, {"code": "BOGR", "name": "Bottle Green", "hex": "#3C5346", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f9978651/692833529eee9a2c4100000c/6aead981/JH120_BottleGreen_FT.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#002CCA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce349600001c/JH120_BrightRoyal_FT.jpg"}, {"code": "BUPK", "name": "Bubblegum Pink", "hex": "#FA96B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c4100000d/JH120_BubblegumPink_FT.jpg"}, {"code": "DPBK", "name": "Deep Black*", "hex": "#1A2026", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/65535b61db80ce3582000003/22e1e5ef/JH120_DeepBlack_FT.jpg"}, {"code": "DSAN", "name": "Desert Sand", "hex": "#DCC98F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce349600001d/JH120_DesertSand_FT.jpg"}, {"code": "EAGN", "name": "Earthy Green", "hex": "#46613F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce349600001e/JH120_EarthyGreen_FT.jpg"}, {"code": "ESPR", "name": "Espresso", "hex": "#4C3932", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c4100000e/JH120_Espresso_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/65535b61db80ce3582000004/6bc16001/JH120_HeatherGrey_FT.jpg"}, {"code": "LILA", "name": "Lilac", "hex": "#CABBD5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c4100000f/JH120_Lilac_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/65535b61db80ce3582000006/8cf40093/JH120_NewFrenchNavy_FT.jpg"}, {"code": "NSTO", "name": "Natural Stone", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/65535b61db80ce3582000005/f1bd4e31/JH120_NaturalStone_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/65535b61db80ce3582000007/b995ae41/JH120_SolidCharcoal_FT.jpg"}, {"code": "SORD", "name": "Soft Red", "hex": "#E02A66", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02e02d07/66952b46db80ce349600001f/JH120_SoftRed_FT.jpg"}, {"code": "TEAL", "name": "Teal", "hex": "#355D64", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000010/JH120_Teal_FT.jpg"}, {"code": "VMIL", "name": "Vanilla Milkshake", "hex": "#E3D7CE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000011/JH120_VanillaMilkshake_FT.jpg"}]},
    {id:'by284', brand:'Build Your Brand', name:'Fluffy hoodie', sku:'BY284', tier:'premium', inkoop:21.10, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0bb5f62a/692970155fa5b59a3a6c43b4/BY284_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BEBL", "name": "Beryl Blue*", "hex": "#B7E5E5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000c5/BY284_BerylBlue_FT.jpg"}, {"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000c6/BY284_Black_FT.jpg"}, {"code": "BOGR", "name": "Bottle Green", "hex": "#2B3F3F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00014d/BY284_BottleGreen_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown*", "hex": "#663D33", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000c7/BY284_ChocolateBrown_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt*", "hex": "#D3D3D3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/95d1ad83/67b8765ddb80ce793b000015/BY284_LightAsphalt_FT.jpg"}, {"code": "MAGN", "name": "Magnet*", "hex": "#706970", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000c8/BY284_Magnet_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#2D2F4E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00014e/BY284_Navy_FT.jpg"}, {"code": "PLPU", "name": "Plum Purple*", "hex": "#492B37", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000ca/BY284_PlumPurple_FT.jpg"}, {"code": "POBL", "name": "Powder Blue*", "hex": "#B3C5E2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000cb/BY284_PowderBlue_FT.jpg"}, {"code": "POLI", "name": "Pale Olive*", "hex": "#AD9D8A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000c9/BY284_PaleOlive_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink*", "hex": "#EDD7E1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000cc/BY284_SoftPink_FT.jpg"}, {"code": "UNBE", "name": "Union Beige*", "hex": "#DDBE92", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000cd/BY284_UnionBeige_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000ce/BY284_White_FT.jpg"}, {"code": "WHSA", "name": "White Sand*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000cf/BY284_WhiteSand_FT.jpg"}]},
    {id:'bb001', brand:'Build Your Brand Basic', name:'Basic hoodie', sku:'BB001', tier:'standaard', inkoop:12.70, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8c677fe8/69296a235fa5b59a3a6b5f42/BB001_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL", "6XL"], colors:[{"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fac/87133eb4/BB001_Black_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#931D30", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fad/fe5ecc41/BB001_Burgundy_FT.jpg"}, {"code": "CGRN", "name": "C. Green", "hex": "#888888", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8fe8eec1/6996ea81ba355a02e400001b/BB001_C.Green_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#616066", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fae/829003df/BB001_Charcoal_FT.jpg"}, {"code": "CIRD", "name": "City Red", "hex": "#C90C28", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300001c/BB001_CityRed_FT.jpg"}, {"code": "GAGR", "name": "Grass Green", "hex": "#009E74", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f33000008/BB001_GrassGreen_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#B6B2BA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735faf/b219858f/BB001_HeatherGrey_FT.jpg"}, {"code": "INTB", "name": "Intense Blue", "hex": "#005AA8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f33000009/BB001_IntenseBlue_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#413F4C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fb0/2c1a7622/BB001_Navy_FT.jpg"}, {"code": "OCBL", "name": "Ocean Blue", "hex": "#C3D2DB", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300000a/BB001_OceanBlue_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#6D6453", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fb1/0a6078a4/BB001_Olive_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#F9EFDE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300001d/BB001_Sand_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3a9713820018735fb2/5699bd70/BB001_White_FT.jpg"}]},
    {id:'by285', brand:'Build Your Brand', name:'Fluffy zip hoodie', sku:'BY285', tier:'premium', inkoop:23.90, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/65db3110/69296e345fa5b59a3a6bf8bf/BY285_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d0/BY285_Black_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown", "hex": "#704638", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d1/BY285_ChocolateBrown_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt", "hex": "#B2ABA7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d2/BY285_LightAsphalt_FT.jpg"}, {"code": "MAGN", "name": "Magnet", "hex": "#5B5659", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d3/BY285_Magnet_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#2D2E4C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00014f/BY285_Navy_FT.jpg"}, {"code": "PLPU", "name": "Plum Purple", "hex": "#4C2F3D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d5/BY285_PlumPurple_FT.jpg"}, {"code": "POBL", "name": "Powder Blue", "hex": "#A5BEE2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d6/BY285_PowderBlue_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#938B7C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d4/BY285_PaleOlive_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink", "hex": "#F2D5DE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d7/BY285_SoftPink_FT.jpg"}, {"code": "UNBE", "name": "Union Beige", "hex": "#BA9A68", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d8/BY285_UnionBeige_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000d9/BY285_White_FT.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000da/BY285_WhiteSand_FT.jpg"}]},
    {id:'by289', brand:'Build Your Brand', name:'Women’s fluffy hoodie', sku:'BY289', tier:'premium', inkoop:21.10, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e518c168/692973585fa5b59a3a6cc2b3/BY289_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f1/BY289_Black_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown", "hex": "#6B423E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f2/BY289_ChocolateBrown_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#B1B5C0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000154/BY289_HeatherGrey_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt", "hex": "#BAB8B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f3/BY289_LightAsphalt_FT.jpg"}, {"code": "LILA", "name": "Lilac", "hex": "#BBB1CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000155/BY289_Lilac_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#2F304E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000156/BY289_Navy_FT.jpg"}, {"code": "PLPU", "name": "Plum Purple", "hex": "#603A4C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f5/BY289_PlumPurple_FT.jpg"}, {"code": "POBL", "name": "Powder Blue", "hex": "#9CB3D6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f6/BY289_PowderBlue_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#827F6F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f4/BY289_PaleOlive_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink", "hex": "#E8CCD5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f7/BY289_SoftPink_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f8/BY289_White_FT.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000f9/BY289_WhiteSand_FT.jpg"}]},
    {id:'jh201', brand:'AWDis Just Hoods', name:'Organic hoodie', sku:'JH201', tier:'premium', inkoop:15.25, tags:["Organic", "Recycled"], eco:true, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0b35bf30/692973035fa5b59a3a6cb655/JH201_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d1/88b182d2/JH201_ArcticWhite_FT.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d2/7403ea9e/JH201_BabyPink_FT.jpg"}, {"code": "BOTT", "name": "Bottle Green", "hex": "#104F17", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d3/574c6fd5/JH201_BottleGreen_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d4/065abfdd/JH201_Burgundy_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d5/d3ea3757/JH201_Charcoal_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d6/5ea78f74/JH201_DeepBlack_FT.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d7/6f18e036/JH201_FireRed_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d8/4b9f3151/JH201_HeatherGrey_FT.jpg"}, {"code": "IBLU", "name": "Ink Blue", "hex": "#032C5E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9a2c5df0/5fd34738647a2d5177000017/a95b16e4/JH201_InkBlue_FT.jpg"}, {"code": "LAVE", "name": "Lavender", "hex": "#C69BFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/eb36aa1c/6564b10ad9e4aa680a00000d/e96ba6f3/JH201_Lavender_FT.jpg"}, {"code": "MUST", "name": "Mustard", "hex": "#C48702", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9a2c5df0/600ff404647a2d7fcb00001a/JH201_Mustard_FT.jpg"}, {"code": "NAST", "name": "Natural Stone", "hex": "#A3ADA6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/eb36aa1c/6564b10ad9e4aa680a00000e/c4f46d39/JH201_NaturalStone_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4d97138200187361d9/ba23616f/JH201_NewFrenchNavy_FT.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/eb36aa1c/5fcf5c4d97138200187361da/cb96d2ce/JH201_RoyalBlue_FT.jpg"}, {"code": "SKYY", "name": "Sky Blue", "hex": "#8BD7EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/eb36aa1c/6564b10ad9e4aa680a00000f/42354fec/JH201_SkyBlue_FT.jpg"}]},
    {id:'by290', brand:'Build Your Brand', name:'Women’s fluffy zip hoodie', sku:'BY290', tier:'premium', inkoop:23.90, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/48d5088e/692972865fa5b59a3a6ca2e0/BY290_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000fa/BY290_Black_FT.jpg"}, {"code": "CBRO", "name": "Chocolate Brown", "hex": "#60372B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000fb/BY290_ChocolateBrown_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#B1B6C0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000157/BY290_HeatherGrey_FT.jpg"}, {"code": "LASP", "name": "Light Asphalt", "hex": "#9B9995", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000fc/BY290_LightAsphalt_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#2E2F4D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000158/BY290_Navy_FT.jpg"}, {"code": "PLPU", "name": "Plum Purple", "hex": "#492D38", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000fe/BY290_PlumPurple_FT.jpg"}, {"code": "POBL", "name": "Powder Blue", "hex": "#8799C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000ff/BY290_PowderBlue_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#8C816D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b143478870000fd/BY290_PaleOlive_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink", "hex": "#D3B6B8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b14347887000100/BY290_SoftPink_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b14347887000101/BY290_White_FT.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b14347887000102/BY290_WhiteSand_FT.jpg"}]},
    {id:'bb007', brand:'Build Your Brand Basic', name:'Women\'s basic hoodie', sku:'BB007', tier:'standaard', inkoop:12.65, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/73831afb/69296b805fa5b59a3a6b8eb4/BB007_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BURG", "name": "Burgundy", "hex": "#912B38", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4cf50c99/5fcf5c3c9713820018735ff7/151e97e4/BB007_Burgundy_FT.jpg"}, {"code": "CGRN", "name": "C. Green", "hex": "#888888", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7c575086/692831c89eee9a377f00007d/1340c282/BB007_C.Green_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#4F595B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4cf50c99/5fcf5c3c9713820018735ff8/10323b99/BB007_Charcoal_FT.jpg"}, {"code": "CIRD", "name": "City Red", "hex": "#CC182E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f33000020/BB007_CityRed_FT.jpg"}, {"code": "GAGR", "name": "Grass Green", "hex": "#01A394", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f33000003/BB007_GrassGreen_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#CCC9CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4cf50c99/5fcf5c3c9713820018735ff9/c9526a88/BB007_HeatherGrey_FT.jpg"}, {"code": "INTB", "name": "Intense Blue", "hex": "#135FAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300000e/BB007_IntenseBlue_FT.jpg"}, {"code": "OCBL", "name": "Ocean Blue", "hex": "#C5D4DD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4cf50c99/5fcf5c3c9713820018735ffc/343fd1a7/BB007_OceanBlue_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#6B6656", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4cf50c99/5fcf5c3c9713820018735ffd/40bf0034/BB007_Olive_FT.jpg"}, {"code": "POBL", "name": "Powder Blue", "hex": "#9EB4DB", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f00007e/BB007_PowderBlue_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink", "hex": "#F0C7D5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692831c89eee9a377f00007f/BB007_SoftPink_FT.jpg"}]},
    {id:'bb006', brand:'Build Your Brand Basic', name:'Basic oversize hoodie', sku:'BB006', tier:'premium', inkoop:18.35, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/89140e99/69296aeb5fa5b59a3a6b7f5d/BB006_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/3c313d1c/5fcf5c3b9713820018735fe6/a124b699/BB006_Black_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#AA253A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fe7/e8a10348/BB006_Burgundy_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#333335", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fe8/ea33ce72/BB006_Charcoal_FT.jpg"}, {"code": "CIRD", "name": "City Red", "hex": "#F2243E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300002b/BB006_CityRed_FT.jpg"}, {"code": "GAGR", "name": "Grass Green", "hex": "#039E79", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300000b/BB006_GrassGreen_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#CAC8CE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fe9/4bfb0ec0/BB006_HeatherGrey_FT.jpg"}, {"code": "INTB", "name": "Intense Blue", "hex": "#216FBC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300001f/BB006_IntenseBlue_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#43404F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fea/0358edd2/BB006_Navy_FT.jpg"}, {"code": "OCBL", "name": "Ocean Blue", "hex": "#B3C5CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300000c/BB006_OceanBlue_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#EADECE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/905ec87a/674085c93d2d8f7f3300000d/BB006_Sand_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fec/f23a1834/BB006_White_FT.jpg"}]},
    {id:'jh50j', brand:'AWDis Just Hoods', name:'Kids zoodie', sku:'JH50J', tier:'standaard', inkoop:12.05, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a55da401/692973d55fa5b59a3a6cd63b/JH50J_LS00_2026.jpg', sizes:["34", "56", "78", "911", "1213", "3/4", "5/6", "7/8", "9/11", "12/13"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/67f38314db80ce4e1a000006/JH50J_ArticWhite_FT.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/67f38314db80ce4e1a000007/JH50J_BabyPink_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/67f38314db80ce4e1a000008/JH50J_Burgundy_FT.jpg"}, {"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/63874ded48718b69ee0000c6/aea12c6b/JH50J_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5fcf5c4e971382001873620b/de28fd76/JH50J_DeepBlack_FT.jpg"}, {"code": "DSAN", "name": "Desert Sand", "hex": "#DCC98F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/63874ded48718b69ee0000c5/de8cbaec/JH50J_DesertSand_FT.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5c5c3e3deb9f5b1c75ce45d5/02b0e550/JH50J_FireRed_FT.jpg"}, {"code": "HBLU", "name": "Hawaiian Blue", "hex": "#2BFCFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/67f38314db80ce4e1a00000d/JH50J_HawaiinBlue_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5c5c3e3deb9f5b1c75ce45d7/81a66736/JH50J_HeatherGrey_FT.jpg"}, {"code": "HOPK", "name": "Hot Pink", "hex": "#F400BA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/d65a1aa8/68c12dd5cf659c3a55ed8d51/5c5c3e3deb9f5b1c75ce45d8c0eb3b74.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5c5c3e3deb9f5b1c75ce45d9/ca1ac0e4/JH50J_JetBlack_FT.jpg"}, {"code": "KELL", "name": "Kelly Green", "hex": "#11FF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5c5c3e3deb9f5b1c75ce45da/f0daebd6/JH50J_KellyGreen_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5c5c3e3deb9f5b1c75ce45e1/7de8ed08/JH50J_NewFrenchNavy_FT.jpg"}, {"code": "OXNY", "name": "Oxford Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5c5c3e3deb9f5b1c75ce45e2/6f51e7e0/JH50J_OxfordNavy_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2302E0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/67f38314db80ce4e1a000014/JH50J_Purple%20_FT.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/67f38314db80ce4e1a000015/JH50J_RoyalBlue_FT.jpg"}, {"code": "SAPP", "name": "Sapphire Blue", "hex": "#027FFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/5c5c3e3deb9f5b1c75ce45e5/da1da5bd/JH50J_SapphireBlue_FT.jpg"}, {"code": "SYEL", "name": "Sun Yellow", "hex": "#FFE800", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a6054919/63874ded48718b69ee0000c7/ab6228a7/JH50J_SunYellow_FT.jpg"}]},
    {id:'jh111', brand:'AWDis Just Hoods', name:'Vision heavyweight hoodie', sku:'JH111', tier:'standaard', inkoop:14.00, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/81f99108/69296d335fa5b59a3a6bd09b/JH111_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ATBL", "name": "Atlantic Blue", "hex": "#5474AD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b6700057d/JH111_AtlanticBlue_FT.jpg"}, {"code": "AWHI", "name": "Arctic White", "hex": "#F0EFF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b6700057c/JH111_ArcticWhite_FT.jpg"}, {"code": "DPBK", "name": "Deep Black*", "hex": "#282626", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b6700057e/JH111_DeepBlack_FT.jpg"}, {"code": "DULI", "name": "Dusty Lilac", "hex": "#827582", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b6700057f/JH111_DustyLilac_FT.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#E5202A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000580/JH111_FireRed_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#B1B3B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000581/JH111_HeatherGrey_FT.jpg"}, {"code": "ICEB", "name": "Ice Blue", "hex": "#ACCEE9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000582/JH111_IceBlue_FT.jpg"}, {"code": "MOSG", "name": "Moss Green", "hex": "#4C6558", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000583/JH111_MossGreen_FT.jpg"}, {"code": "NACL", "name": "Natural Clay", "hex": "#87766C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000584/JH111_NaturalClay_FT.jpg"}, {"code": "NAST", "name": "Natural Stone", "hex": "#DFD3C7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000585/JH111_NaturalStone_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#22253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000586/JH111_NewFrenchNavy_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#37343A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bca7ebd0/68d3d07cba355a2b67000587/JH111_SolidCharcoal_FT.jpg"}]},
    {id:'jh03j', brand:'AWDis Just Hoods', name:'Kids varsity hoodie', sku:'JH03J', tier:'standaard', inkoop:10.55, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c34bf6d9/69296e695fa5b59a3a6bff58/JH03J_LS00_2026.jpg', sizes:["34", "56", "78", "911", "1213"], colors:[{"code": "BPAW", "name": "Baby Pink/Arctic White", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4330/1a9227af/JH03J_BabyPink_ArcticWhite_FT.jpg"}, {"code": "BUGO", "name": "Burgundy/Gold", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4332/2c2642ce/JH03J_Burgundy_Gold_FT.jpg"}, {"code": "FGGO", "name": "Forest Green/Gold", "hex": "#02190C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4335/e487de3c/JH03J_ForestGreen_Gold_FT.jpg"}, {"code": "FRJB", "name": "Fire Red/Jet Black", "hex": "#DA0000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4334/67f11d8c/JH03J_FireRed_JetBlack_FT.jpg"}, {"code": "HGFN", "name": "Heather Grey/French Navy", "hex": "#A2A90D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4337/76563b01/JH03J_HeatherGrey_FrenchNavy_FT.jpg"}, {"code": "HGFR", "name": "Heather Grey/Fire Red", "hex": "#A2A900", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4336/bd90c053/JH03J_HeatherGrey_FireRed_FT.jpg"}, {"code": "HPFN", "name": "Hot Pink/French Navy", "hex": "#F5000D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4338/db1cad43/JH03J_HotPink_FrenchNavy_FT.jpg"}, {"code": "JBFR", "name": "Jet Black/Fire Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4339/a37969e3/JH03J_JetBlack_FireRed_FT.jpg"}, {"code": "JBGO", "name": "Jet Black/Gold", "hex": "#0000B8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce433a/2e17bdaa/JH03J_JetBlack_Gold_FT.jpg"}, {"code": "JBHG", "name": "Jet Black/Heather Grey", "hex": "#0000A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce433b/cdc3bf7c/JH03J_JetBlack_HeatherGrey_FT.jpg"}, {"code": "JBHP", "name": "Jet Black/Hot Pink", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce433c/1438499f/JH03J_JetBlack_HotPink_FT.jpg"}, {"code": "JBOR", "name": "Jet Black/Orange Crush", "hex": "#000070", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce433d/4de8c133/JH03J_JetBlack_OrangeCrush_FT.jpg"}, {"code": "NFFR", "name": "New French Navy/Fire Red", "hex": "#000700", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4347/610cb7f7/JH03J_NewFrenchNavy_FireRed_FT.jpg"}, {"code": "NFHG", "name": "New French Navy/Heather Grey", "hex": "#0007A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4348/01a4ddb2/JH03J_NewFrenchNavy_HeatherGrey_FT.jpg"}, {"code": "NFSK", "name": "New French Navy/Sky Blue", "hex": "#0007D8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce4349/2e5abf13/JH03J_NewFrenchNavy_SkyBlue_FT.jpg"}, {"code": "PUSU", "name": "Purple/Sun Yellow", "hex": "#2402E8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce434a/dc926a9a/JH03J_Purple_SunYellow_FT.jpg"}, {"code": "SAHG", "name": "Sapphire Blue/Heather Grey", "hex": "#0380A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e55d524f/5c5c3e3deb9f5b1c75ce434b/4fd555cd/JH03J_SapphireBlue_HeatherGrey_FT.jpg"}]},
    {id:'gd068', brand:'Gildan', name:'Softstyle™ midweight fleece adult full-zip hooded sweatshirt', sku:'GD068', tier:'premium', inkoop:16.80, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9276c02a/69296d6b5fa5b59a3a6bdaeb/GD068_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/6564b0d6d9e4aa3e83000031/f9677323/GD068_Black_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e00001e/1e4f7443/GD068_DarkHeather_FT.jpg"}, {"code": "DROS", "name": "Dusty Rose", "hex": "#F4BED0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e00001f/6f1083de/GD068_DustyRose_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e00000b/1dcff73f/GD068_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/6564b0d6d9e4aa3e83000032/5b3c3389/GD068_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/6564b0d6d9e4aa3e83000033/e5a14ee3/GD068_Navy_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#F4EBD2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e000020/109ad87b/GD068_OffWhite_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e00002e/5a7393f5/GD068_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/674086693d2d8f284e00002f/95aa48cf/GD068_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/6564b0d6d9e4aa3e83000034/78fd9aee/GD068_RingspunSportGrey_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/6564b0d6d9e4aa3e83000035/bc756d08/GD068_Sand_FT.jpg"}, {"code": "SBLU", "name": "Stone Blue", "hex": "#67A8B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/bea31cc9/6564b0d6d9e4aa3e83000036/e381511f/GD068_StoneBlue_FT.jpg"}]},
    {id:'gd67b', brand:'Gildan', name:'Softstyle™ midweight fleece youth hoodie', sku:'GD67B', tier:'standaard', inkoop:9.40, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6d87b347/69296f325fa5b59a3a6c204a/GD67B_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000022/5ff25aea/GD67B_Black_FT.jpg"}, {"code": "DAIS", "name": "Daisy", "hex": "#FFD142", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000023/cfeacfe2/GD67B_Daisy_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000024/7f3a6142/GD67B_DarkHeather_FT.jpg"}, {"code": "FORE", "name": "Forest Green", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000025/858ae387/GD67B_ForestGreen_FT.jpg"}, {"code": "LPIN", "name": "Light Pink", "hex": "#F4C4F7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000026/c81db26f/GD67B_LightPink_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000027/68510648/GD67B_Maroon_FT.jpg"}, {"code": "MGRE", "name": "Military Green", "hex": "#386238", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000028/eb228f64/GD67B_MilitaryGreen_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000029/05dbd05f/GD67B_Navy_FT.jpg"}, {"code": "PKLE", "name": "Pink Lemonade", "hex": "#EF4C97", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300002a/dd77b8a3/GD67B_PinkLemonade_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300002b/5a88bc40/GD67B_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300002d/06054872/GD67B_Royal_FT.jpg"}, {"code": "RSPG", "name": "Ringspun Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300002c/92b54ed9/GD67B_RingspunSportGrey_FT.jpg"}, {"code": "SAND", "name": "Sand", "hex": "#CBC0B1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300002e/ae8c19de/GD67B_Sand_FT.jpg"}, {"code": "SBLU", "name": "Stone Blue", "hex": "#67A8B4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300002f/21b71043/GD67B_StoneBlue_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000030/7eb3157f/GD67B_White_FT.jpg"}]},
    {id:'jh021', brand:'AWDis Just Hoods', name:'Cross neck hoodie', sku:'JH021', tier:'premium', inkoop:19.70, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4c267359/69296f365fa5b59a3a6c210b/JH021_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce42b0a08e45bc.JPG"}, {"code": "BSMO", "name": "Black Smoke", "hex": "#1F2530", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5d7a714954ef21700a000019/1bcf48fe/JH021_BlackSmoke_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce42b1fcbbac6a.JPG"}, {"code": "DILA", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/528179d7/6564b10ad9e4aa680a000003/JH021_DigitalLavender_FT.jpg"}, {"code": "DUGN", "name": "Dusty Green", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e000031/JH021_DustyGreen_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e000032/JH021_DustyPink_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce42b2f229cfa3.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce42b31b5c69dd.jpg"}, {"code": "NUDE", "name": "Nude", "hex": "#D59D8B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce42bcda11681c.JPG"}, {"code": "OXNY", "name": "Oxford Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce42bdb3765ba0.jpg"}, {"code": "PEPP", "name": "Peppermint", "hex": "#8EFFCC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e000034/JH021_Peppermint_FT.jpg"}, {"code": "VMIL", "name": "Vanilla Milkshake", "hex": "#FCF9C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a268af0f/61545c1c3f6633447e000036/JH021_VanillaMilkshake_FT.jpg"}]},
    {id:'gd57b', brand:'Gildan', name:'Heavy Blend™ youth hooded sweatshirt', sku:'GD57B', tier:'standaard', inkoop:9.50, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/71289c1a/69296dbc5fa5b59a3a6be74a/GD57B_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2df2/68b689e9/GD57B_Black_FT.jpg"}, {"code": "CARO", "name": "Carolina Blue", "hex": "#68BAFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e8300000f/7c03690a/GD57B_CarolinaBlue_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000010/b5cb10de/GD57B_Charcoal_FT.jpg"}, {"code": "FACH", "name": "Fan Charcoal Heather", "hex": "#484848", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000237/GD57B_FanCharcoalHeather_FT.jpg"}, {"code": "FADG", "name": "Fan Dark Green", "hex": "#1A462F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000238/GD57B_FanDarkGreen_FT_FT.jpg"}, {"code": "FORE", "name": "Forest", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2df4/3f68695b/GD57B_Forest_FT.jpg"}, {"code": "MARO", "name": "Maroon", "hex": "#320A35", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2e05/94af106c/GD57B_Maroon_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2e06/f0b6fc2b/GD57B_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF513D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000011/6f2ef419/GD57B_Orange_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2e07/c364b6c8/GD57B_Purple_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2e08/067cfede/GD57B_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2e09/39bf2974/GD57B_Royal_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2e0a/7efa2777/GD57B_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2e0b/3dcef622/GD57B_White_FT.jpg"}]},
    {id:'gd058', brand:'Gildan', name:'Heavy Blend™  full -zip hooded sweatshirt', sku:'GD058', tier:'premium', inkoop:16.95, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8bd75cfb/69296b0e5fa5b59a3a6b8598/GD058_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "ASHG", "name": "Ash*", "hex": "#C5CBC7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bfb/69d96d28/GD058_Ash_FT.jpg"}, {"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bfc/e9cd9276/GD058_Black_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather*", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2bff/bb419701/GD058_DarkHeather_FT.jpg"}, {"code": "FORE", "name": "Forest*", "hex": "#081A0F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c38000059/08a3cb8f/GD058_Forest_FT.jpg"}, {"code": "GPHE", "name": "Graphite Heather*", "hex": "#4A5556", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c01/717b16f6/GD058_GraphiteHeather_FT.jpg"}, {"code": "NAVY", "name": "Navy*†", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c0a/5d3c3e64/GD058_Navy_FT.jpg"}, {"code": "PURP", "name": "Purple*", "hex": "#1A1C95", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c0b/175502f7/GD058_Purple_FT.jpg"}, {"code": "ROYA", "name": "Royal*†", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c0d/ea2b1553/GD058_Royal_FT.jpg"}, {"code": "SPGY", "name": "Sport Grey*†", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c10/3f43c6da/GD058_SportGrey_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2c11/5b375c38/GD058_White_FT.jpg"}]},
    {id:'gd028', brand:'Gildan', name:'Hammer Maxweight Adult Hooded Sweatshirt', sku:'GD028', tier:'premium', inkoop:16.45, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/23c29358/69296a3d5fa5b59a3a6b6280/2bf10393/GD028_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL"], colors:[{"code": "BDUS", "name": "Blue Dusk", "hex": "#172331", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00021e/GD028_BlueDusk_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#A92139", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00021f/GD028_CherryRed_FT.jpg"}, {"code": "DERY", "name": "Deep Royal", "hex": "#052A70", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000220/GD028_DeepRoyal_FT.jpg"}, {"code": "GARN", "name": "Garnet", "hex": "#6F1932", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000221/GD028_Garnet_FT.jpg"}, {"code": "GRAV", "name": "Gravel", "hex": "#676769", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000222/GD028_Gravel_FT.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#585039", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000224/GD028_Olive_FT.jpg"}, {"code": "OWHI", "name": "Off White", "hex": "#EFE8D5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000223/GD028_OffWhite_FT.jpg"}, {"code": "PBMI", "name": "Pitch Black", "hex": "#131313", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000225/GD028_PitchBlack_FT.jpg"}, {"code": "TANN", "name": "Tan", "hex": "#A48B6F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000226/GD028_Tan_FT.jpg"}]},
    {id:'jh053', brand:'AWDis Just Hoods', name:'Varsity zoodie', sku:'JH053', tier:'premium', inkoop:15.65, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a04d784f/692972365fa5b59a3a6c97a8/JH053_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "CHJB", "name": "Charcoal/Jet Black", "hex": "#3F4800", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4439066e459d.jpg"}, {"code": "FRJB", "name": "Fire Red/Jet Black", "hex": "#DA0000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce443b5c3d65ce.jpg"}, {"code": "HGFN", "name": "Heather Grey/French Navy", "hex": "#A2A90D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce443ca5d225ea.jpg"}, {"code": "JBFR", "name": "Jet Black/Fire Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce443d182b50d3.jpg"}, {"code": "JBGO", "name": "Jet Black/Gold", "hex": "#0000B8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce443e4ad89af6.jpg"}, {"code": "JBHG", "name": "Jet Black/Heather Grey", "hex": "#0000A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce443f69d08ba3.jpg"}, {"code": "JBOR", "name": "Jet Black/Orange Crush", "hex": "#000070", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4440204b6121.jpg"}, {"code": "NFFR", "name": "New French Navy/Fire Red", "hex": "#000700", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce444c743cec49.jpg"}, {"code": "NFHG", "name": "New French Navy/Heather Grey", "hex": "#0007A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce444d482ad5e0.jpg"}, {"code": "NFSK", "name": "New French Navy/Sky Blue", "hex": "#0007D8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce444eb57f00f3.jpg"}]},
    {id:'jh125', brand:'AWDis Just Hoods', name:'Signature heavyweight zoodie', sku:'JH125', tier:'premium', inkoop:19.99, tags:["Organic", "Recycled"], eco:true, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/088ff2c2/69296ad05fa5b59a3a6b7aac/JH125_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "DPBK", "name": "Deep Black*", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/672e3fd1db80ce52e8000011/136b1c49/JH125_DeepBlack_FT.jpg"}, {"code": "EAGN", "name": "Earthy Green", "hex": "#46613F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/672e3fd1db80ce52e8000012/57bac2e5/JH125_EarthyGreen_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/672e3fd1db80ce52e8000013/12d0e8f4/JH125_HeatherGrey_FT.jpg"}, {"code": "NAST", "name": "Natural Stone", "hex": "#A3ADA6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/672e3fd1db80ce52e800001c/13d7ac7b/JH125_NaturalStone_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/672e3fd1db80ce52e800001d/5df6a332/JH125_NewFrenchNavy_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a814defd/672e3fd1db80ce52e800001e/9c3dda35/JH125_SolidCharcoal_FT.jpg"}]},
    {id:'jh006', brand:'AWDis Just Hoods', name:'Sports polyester hoodie', sku:'JH006', tier:'standaard', inkoop:13.15, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b12410f3/69296bda5fa5b59a3a6b991a/JH006_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41afdb35c0a9.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41b1f001d7a2.jpg"}, {"code": "GMEL", "name": "Grey Melange", "hex": "#43545C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13279f2a/5dfabf866aa61163fd0004c8/6cb9e9e8/JH006_GreyMelange_FT.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41b2ce0f0e6a.jpg"}, {"code": "OXNY", "name": "Oxford Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41bc8ed991a8.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41be6457e7d5.jpg"}, {"code": "STEE", "name": "Steel Grey", "hex": "#1F2B2D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41bf67eb7db4.jpg"}]},
    {id:'j201j', brand:'AWDis Just Hoods', name:'Kids organic hoodie', sku:'J201J', tier:'standaard', inkoop:12.05, tags:["Organic", "Recycled", "Kids"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/d5e84c25/6929725f5fa5b59a3a6c9c62/J201J_LS00_2026.jpg', sizes:["34", "56", "78", "911", "1213"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b9713820018736186/10aae6af/J201J_ArcticWhite_FT.jpg"}, {"code": "BAPK", "name": "Baby Pink", "hex": "#FFB2F9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b9713820018736187/e5e57b3b/J201J_BabyPink_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b9713820018736188/45a0e047/J201J_Burgundy_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b9713820018736189/95ddde56/J201J_DeepBlack_FT.jpg"}, {"code": "FRED", "name": "Fire Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b971382001873618a/280542c4/J201J_FireRed_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b971382001873618b/09e6218e/J201J_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b971382001873618c/606fef9d/J201J_NewFrenchNavy_FT.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6c1f47b7/5fcf5c4b971382001873618d/a18d8c73/J201J_RoyalBlue_FT.jpg"}]},
    {id:'jh115', brand:'AWDis Just Hoods', name:'Vision Heavyweight Zoodie', sku:'JH115', tier:'premium', inkoop:17.80, tags:["Organic", "Recycled"], eco:true, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/110e97ad/69296d905fa5b59a3a6be03d/JH115_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "DPBK", "name": "Deep Black*", "hex": "#27232B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f9978651/692833529eee9a2c41000006/4ee14ffa/JH115_DeepBlack_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A0A0AE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000007/JH115_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#2C3146", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000008/JH115_NewFrenchNavy_FT.jpg"}, {"code": "SOCH", "name": "Solid Charcoal", "hex": "#5A515D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/f9978651/692833529eee9a2c41000009/30d18166/JH115_SolidCharcoal_FT.jpg"}]},
    {id:'jh250', brand:'AWDis Just Hoods', name:'Organic zoodie', sku:'JH250', tier:'premium', inkoop:19.05, tags:["Organic", "Recycled"], eco:true, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/077989ce/69296cff5fa5b59a3a6bc816/JH250_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4e97138200187361fd/6415302a/JH250_ArcticWhite_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#45002E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4e97138200187361fe/7fe1522e/JH250_Burgundy_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4e97138200187361ff/6816d713/JH250_Charcoal_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4e9713820018736200/bbdbf8e9/JH250_DeepBlack_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4e9713820018736201/af2f30bf/JH250_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c4e9713820018736202/1d14e499/JH250_NewFrenchNavy_FT.jpg"}]},
    {id:'jh50f', brand:'AWDis Just Hoods', name:'Women’s college zoodie', sku:'JH50F', tier:'premium', inkoop:15.25, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/d1bc2d1b/69296f7e5fa5b59a3a6c2b60/JH50F_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "DLAV", "name": "Digital Lavender", "hex": "#7770F4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000c0/JH50F_DigitalLavender_FT.jpg"}, {"code": "DPBK", "name": "Deep Black", "hex": "#161B20", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000bf/JH50F_DeepBlack_FT.jpg"}, {"code": "DUGN", "name": "Dusty Green", "hex": "#29343B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000c1/JH50F_DustyGreen_FT.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000c2/JH50F_DustyPink_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000c3/JH50F_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c4df5d72/63874ded48718b69ee0000c4/JH50F_NewFrenchNavy_FT.jpg"}]},
    {id:'jh101', brand:'AWDis Just Hoods', name:'Heavyweight hoodie', sku:'JH101', tier:'premium', inkoop:15.25, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/47c92a32/69296e6d5fa5b59a3a6c0019/JH101_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce454a5c1b6cc0.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce454d203c70e5.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce454ff9d8deb9.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce455095cf536f.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce45571f062ccf.jpg"}]},
    {id:'jh016', brand:'AWDis Just Hoods', name:'Women\'s cropped hoodie', sku:'JH016', tier:'standaard', inkoop:8.90, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/31696558/69296a775fa5b59a3a6b6b74/JH016_LS00_2026.jpg', sizes:["2XS", "XS", "S", "M", "L", "XL"], colors:[{"code": "AWHI", "name": "Arctic White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/35069889/5d7a714954ef21700a000017697155fb.jpg"}, {"code": "DUPK", "name": "Dusty Pink", "hex": "#CE6DAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/35069889/5d7a714954ef21700a0000180b955973.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce422692dd769e.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce422740babed6.jpg"}]},
    {id:'jh004', brand:'AWDis Just Hoods', name:'Electric hoodie', sku:'JH004', tier:'standaard', inkoop:13.10, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9461b44a/692970fb5fa5b59a3a6c6803/JH004_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "EGRE", "name": "Electric Green", "hex": "#7CFF26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4196eb46b6a8.jpg"}, {"code": "EORA", "name": "Electric Orange", "hex": "#ED6838", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce419762e35bff.jpg"}, {"code": "EPIN", "name": "Electric Pink", "hex": "#FF47FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4198acddc0ba.jpg"}, {"code": "EYEL", "name": "Electric Yellow", "hex": "#D6DB32", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41990e79c0a6.jpg"}]},
    {id:'jh009', brand:'AWDis Just Hoods', name:'Baseball hoodie', sku:'JH009', tier:'standaard', inkoop:14.35, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ca118cf9/692971bc5fa5b59a3a6c85a5/JH009_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "CHJB", "name": "Charcoal/Jet Black", "hex": "#3F4800", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41de522712ab.jpg"}, {"code": "HGJB", "name": "Heather Grey/Jet Black", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41dfffa46a13.jpg"}, {"code": "JBFR", "name": "Jet Black/Fire Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41e0a9a8e3b3.jpg"}, {"code": "ONHG", "name": "Oxford Navy/Heather Grey", "hex": "#0418A9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41eb0ca1f4be.jpg"}]},
    {id:'jh04j', brand:'AWDis Just Hoods', name:'Kids electric hoodie', sku:'JH04J', tier:'standaard', inkoop:10.55, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/d31363cc/69296f405fa5b59a3a6c228a/JH04J_LS00_2026.jpg', sizes:["34", "56", "78", "911", "1213"], colors:[{"code": "EGRE", "name": "Electric Green", "hex": "#7CFF26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce43e77df57d2a.jpg"}, {"code": "EORA", "name": "Electric Orange", "hex": "#F26530", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce43e8dadd3835.jpg"}, {"code": "EPIN", "name": "Electric Pink", "hex": "#FF47FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce43e90431c115.jpg"}, {"code": "EYEL", "name": "Electric Yellow", "hex": "#D4DD30", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce43eaf75e1dc9.jpg"}]},
    {id:'jh305', brand:'AWDis Just Hoods', name:'Women’s relaxed hoodie', sku:'JH305', tier:'standaard', inkoop:11.80, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/25aa3f8f/692971b05fa5b59a3a6c839b/JH305_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "DPBK", "name": "Deep Black", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2fc1fb16/6564b10ad9e4aa680a000010/dc01d469/JH305_DeepBlack_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2fc1fb16/6564b10ad9e4aa680a000011/4e9cf59c/JH305_HeatherGrey_FT.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2fc1fb16/6564b10ad9e4aa680a000012/83a76fc7/JH305_NewFrenchNavy_FT.jpg"}]},
    {id:'jh020', brand:'AWDis Just Hoods', name:'Street hoodie', sku:'JH020', tier:'premium', inkoop:16.50, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5c2f161e/6929708b5fa5b59a3a6c55e0/JH020_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "FNAV", "name": "French Navy*", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9338aaee/5c5c3e3deb9f5b1c75ce429e/b26c82ef/JH020_FrenchNavy_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey*", "hex": "#A1A9A7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9338aaee/5c5c3e3deb9f5b1c75ce429f/97541739/JH020_HeatherGrey_FT.jpg"}, {"code": "JBLA", "name": "Jet Black*", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9338aaee/5c5c3e3deb9f5b1c75ce42a2/25422269/JH020_JetBlack_FT.jpg"}]},
    {id:'jh06j', brand:'AWDis Just Hoods', name:'Kids sports polyester hoodie', sku:'JH06J', tier:'standaard', inkoop:10.05, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/76cf5e24/69296a695fa5b59a3a6b690a/JH06J_LS00_2026.jpg', sizes:["34", "56", "78", "911", "1213"], colors:[{"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b22b86d9/5c5c3e3deb9f5b1c75ce44bbca06a249.jpg"}, {"code": "OXNY", "name": "Oxford Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b22b86d9/5c5c3e3deb9f5b1c75ce44be9209ec00.jpg"}, {"code": "ROYA", "name": "Royal Blue", "hex": "#003BEF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b22b86d9/5c5c3e3deb9f5b1c75ce44bfe8a0a5bf.jpg"}]},
    {id:'jh011', brand:'AWDis Just Hoods', name:'Epic print hoodie', sku:'JH011', tier:'standaard', inkoop:11.45, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/006b8592/69296bf05fa5b59a3a6b9c84/JH011_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "CHAR", "name": "Charcoal", "hex": "#3F484F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41eebf89faeb.jpg"}, {"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce41f0a5df60fb.jpg"}]},
    {id:'jh150', brand:'AWDis Just Hoods', name:'Heavyweight zoodie', sku:'JH150', tier:'premium', inkoop:16.50, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/fccea9cf/692972025fa5b59a3a6c90c3/JH150_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4567c05c5fb4.jpg"}, {"code": "NFNA", "name": "New French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4571b97a1867.jpg"}]},
    {id:'jh180', brand:'AWDis Just Hoods', name:'Vintage Washed Hoodie', sku:'JH180', tier:'premium', inkoop:21.60, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/58d669e6/69296fed5fa5b59a3a6c3da9/3a40df45/JH180_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "WBLA", "name": "Washed Black", "hex": "#2E2B2E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c4100001a/JH180_WashedBlack_FT.jpg"}, {"code": "WGRY", "name": "Washed Grey", "hex": "#F2F2F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c4100001b/JH180_WashedGrey_FT.jpg"}]},
    {id:'jh014', brand:'AWDis Just Hoods', name:'Camo hoodie', sku:'JH014', tier:'premium', inkoop:17.10, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/37f1a9cc/692973d65fa5b59a3a6cd644/JH014_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "BKCA", "name": "Black Camo", "hex": "#3E3C42", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/de95887b/5dfabf876aa61163fd0004f9/06712bdf/JH014_BlackCamo_FT.jpg"}, {"code": "GNCA", "name": "Green Camo", "hex": "#717C6B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4218da1ede61.jpg"}]},
    {id:'lw02t', brand:'Larkwood', name:'Toddler hooded sweatshirt with kangaroo pocket', sku:'LW02T', tier:'standaard', inkoop:9.05, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/1c91f9a3/692972945fa5b59a3a6ca556/LW02T_LS00_2026.jpg', sizes:["612", "1218", "1824", "2436", "34", "56"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce52de420effa8.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce52e7333db3cf.jpg"}]},
    {id:'jh14j', brand:'AWDis Just Hoods', name:'Kids camo hoodie', sku:'JH14J', tier:'premium', inkoop:15.25, tags:["Kids"], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b489155e/69296b345fa5b59a3a6b892d/JH14J_LS00_2026.jpg', sizes:["34", "56", "78", "911", "1213"], colors:[{"code": "BKCA", "name": "Black Camo", "hex": "#2C2B32", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b22b86d9/5dfabf886aa61163fd00069d01a3f199.jpg"}, {"code": "GNCA", "name": "Green Camo", "hex": "#343F33", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b22b86d9/5dfabf886aa61163fd00069e8bd36c94.jpg"}]},
    {id:'jh185', brand:'AWDis Just Hoods', name:'Vintage Washed Zoodie', sku:'JH185', tier:'premium', inkoop:24.15, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/2d8b91af/69296ea75fa5b59a3a6c09b3/JH185_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "WBLA", "name": "Washed Black", "hex": "#2F2C2F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c4100001c/JH185_WashedBlack_FT.jpg"}]},
    {id:'jh015', brand:'AWDis Just Hoods', name:'Hoodie dress', sku:'JH015', tier:'standaard', inkoop:14.00, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0834e850/69296c365fa5b59a3a6ba77f/JH015_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL"], colors:[{"code": "JBLA", "name": "Jet Black", "hex": "#1A1D1E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce4222abe60d87.jpg"}]},
  ],
  polo:[
    {id:'aq010', brand:'Asquith & Fox', name:'Men\'s Classic fit polo', sku:'AQ010', tier:'standaard', inkoop:5.55, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4e11788f/692973f05fa5b59a3a6cda31/AQ010_LS00_2026.jpg', sizes:["S (Cla)", "M (Cla)", "L (Cla)", "XL (Cla)", "2XL (Cla)", "3XL (Cla)", "4XL (Cla)", "5XL (Cla)", "S (Rel)", "M (Rel)"], colors:[{"code": "BKHE", "name": "Heather Black", "hex": "#050506", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5cc054c3ace8be659900007c186944a1.jpg"}, {"code": "BLAC", "name": "Black*†", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf23e43e072f8.jpg"}, {"code": "BOCE", "name": "Bright Ocean", "hex": "#66FFF7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2404bdb1603.jpg"}, {"code": "BOTT", "name": "Bottle*†", "hex": "#041004", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf23f70537897.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#004BF4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2413ec2534e.jpg"}, {"code": "BSTE", "name": "Blue Steel†", "hex": "#888888", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/78218287/69b3e875ba355a39ab000010/AQ010_BlueSteel_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#300C26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2426d943e16.jpg"}, {"code": "CDRD", "name": "Cardinal Red", "hex": "#78002F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf244c1c16c77.jpg"}, {"code": "CHAR", "name": "Charcoal*†", "hex": "#20262D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2452ebf1f58.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf246148076e6.jpg"}, {"code": "CORN", "name": "Cornflower", "hex": "#51A8FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf248699ad082.jpg"}, {"code": "DENI", "name": "Denim", "hex": "#13177F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b2fdd025/5c5c3e3deb9f5b1c75cdf249/779cf852/AQ010_Denim_FT.jpg"}, {"code": "EARG", "name": "Earth Green†", "hex": "#888888", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/78218287/69b3e875ba355a39ab000011/AQ010_EarthGreen_FT.jpg"}, {"code": "FERN", "name": "Fern", "hex": "#4C996A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/32845449/674084b63d2d8f5e48000028/AQ010_Fern_FT.jpg"}, {"code": "FNAV", "name": "French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf24a0e7eb498.jpg"}, {"code": "HGRE", "name": "Heather Grey*†", "hex": "#6A7377", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf24b48572a7e.jpg"}, {"code": "HOPK", "name": "Hot Pink", "hex": "#F700C0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf24cf6d22383.jpg"}, {"code": "JADE", "name": "Jade", "hex": "#00B279", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/32845449/674084b63d2d8f5e48000029/AQ010_Jade_FT.jpg"}, {"code": "KELL", "name": "Kelly", "hex": "#08DB00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf24e02aa2723.jpg"}, {"code": "KHAK", "name": "Khaki", "hex": "#A2995A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf24f251effc6.jpg"}, {"code": "LAVE", "name": "Lavender", "hex": "#A5C1FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/32845449/674084b63d2d8f5e4800002a/AQ010_Lavender_FT.jpg"}, {"code": "LIME", "name": "Lime", "hex": "#5EFF28", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf25d0448707c.jpg"}, {"code": "MINT", "name": "Mint", "hex": "#ADFFB2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf25f3e41c756.jpg"}, {"code": "MNBL", "name": "Midnight Blue†", "hex": "#888888", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/78218287/69b3e875ba355a39ab000012/AQ010_MidnightBlue_FT.jpg"}, {"code": "NATU", "name": "Natural", "hex": "#EAE0AE", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf261a6b3ab39.jpg"}, {"code": "NAVY", "name": "Navy*†", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf263f107de98.jpg"}, {"code": "NYEL", "name": "Neon Yellow", "hex": "#E8FF56", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf267d1fee4ec.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#2C3E0D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf26862adc66c.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF5900", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf26961c5145d.jpg"}, {"code": "ORCH", "name": "Orchid", "hex": "#8432FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf26abda4bd53.jpg"}]},
    {id:'aq015', brand:'Asquith & Fox', name:'Men’s polycotton blend polo', sku:'AQ015', tier:'standaard', inkoop:6.25, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/fed3d45e/69296c485fa5b59a3a6baa25/AQ015_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ab62ceb9/5c5c3e3deb9f5b1c75cdf2ba/22b9e763/AQ015_Black_FT.jpg"}, {"code": "BOTT", "name": "Bottle*", "hex": "#041004", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2bbce9f2579.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#300C26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2bc80300da1.jpg"}, {"code": "CHAR", "name": "Charcoal*", "hex": "#20262D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2bdf63daa77.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2be97cfd8aa.jpg"}, {"code": "CORN", "name": "Cornflower", "hex": "#51A8FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2bf7cdc6057.jpg"}, {"code": "HGRE", "name": "Heather Grey*", "hex": "#6A7377", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2c021a07132.jpg"}, {"code": "KELL", "name": "Kelly", "hex": "#08DB00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2c26f0bc216.jpg"}, {"code": "KHAK", "name": "Khaki", "hex": "#A2995A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2c3f3876a5a.jpg"}, {"code": "LIME", "name": "Lime", "hex": "#5EFF28", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2cbe4ec72ef.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2cc25569c5b.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF5900", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d14718d12b.jpg"}, {"code": "PURP", "name": "Purple*", "hex": "#2503B0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d272f5806c.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#EA0023", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d37d22c1ba.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#0034CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d407654323.jpg"}, {"code": "SAPP", "name": "Sapphire*", "hex": "#0082C3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d5cdc80c4d.jpg"}, {"code": "SLAT", "name": "Slate", "hex": "#1F332E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d64911973a.jpg"}, {"code": "SUNF", "name": "Sunflower", "hex": "#FFB200", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d7f24f365c.jpg"}, {"code": "TURQ", "name": "Turquoise", "hex": "#3FFFF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d80c90e523.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2d952514451.jpg"}]},
    {id:'aq020', brand:'Asquith & Fox', name:'Women\'s Classic fit polo', sku:'AQ020', tier:'standaard', inkoop:5.55, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b2f5f713/692970c15fa5b59a3a6c5eb6/AQ020_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf303feaa74e1.jpg"}, {"code": "BOTT", "name": "Bottle", "hex": "#041004", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf304488404e3.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#300C26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf305b70afcae.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#20262D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02aa8e7d/5c5c3e3deb9f5b1c75cdf306/529694a3/AQ020_Charcoal_FT.jpg"}, {"code": "HGRE", "name": "Heather", "hex": "#6A7377", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/02aa8e7d/5c5c3e3deb9f5b1c75cdf309/e16b8064/AQ020_Heather_FT.jpg"}, {"code": "HOPK", "name": "Hot Pink", "hex": "#F700C0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf30a3291a338.jpg"}, {"code": "KELL", "name": "Kelly", "hex": "#08DB00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf30bfa22231a.jpg"}, {"code": "KHAK", "name": "Khaki", "hex": "#A2995A", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf30c0e9ddd44.jpg"}, {"code": "LIME", "name": "Lime", "hex": "#5EFF28", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf31ad3f42461.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf31c81a09347.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF5900", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf31d3e1fe03f.jpg"}, {"code": "PCAR", "name": "Pink Carnation", "hex": "#FF68F2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf31e53a3d2b2.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2503B0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf31f327f695c.jpg"}, {"code": "REDD", "name": "Red", "hex": "#EA0023", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf320d34eb5b0.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#0034CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf321ef542f1f.jpg"}, {"code": "SAPP", "name": "Sapphire", "hex": "#0082C3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf3220d672ba4.jpg"}, {"code": "SKYY", "name": "Sky", "hex": "#8BD7EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf32367fadc0f.jpg"}, {"code": "SUNF", "name": "Sunflower", "hex": "#FFB200", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf3254b96a2b0.jpg"}, {"code": "TURQ", "name": "Turquoise", "hex": "#3FFFF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf3264ffaee01.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf327db4da0d0.jpg"}]},
    {id:'aq011', brand:'Asquith & Fox', name:'Men\'s classic fit tipped polo', sku:'AQ011', tier:'standaard', inkoop:8.60, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/14bf4471/69296be75fa5b59a3a6b9b39/AQ011_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BHCH", "name": "Heather Black/Charcoal", "hex": "#050526", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5cc054c3ace8be659900007d/3c53edea/AQ011_HeatherBlack_Charcoal_FT.jpg"}, {"code": "BKOR", "name": "Black/Orange", "hex": "#000059", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf27f/88856aa5/AQ011_Black_Orange_FT.jpg"}, {"code": "BKRD", "name": "Black/Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf280/27bb0ace/AQ011_Black_Red_FT.jpg"}, {"code": "BKTU", "name": "Black/Turquoise", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5dfaa8206aa61102730001ce/9c869de5/AQ011_Black_Turquoise_FT.jpg"}, {"code": "BKWH", "name": "Black/White", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf281/c1d75742/AQ011_Black_White_FT.jpg"}, {"code": "BKYE", "name": "Black/Yellow", "hex": "#1D1DB9", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf282/acf2923f/AQ011_Black_Yellow_FT.jpg"}, {"code": "BUSK", "name": "Burgundy/Sky", "hex": "#300CD7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf283/ce02d2f0/AQ011_Burgundy_Sky_FT.jpg"}, {"code": "CHWH", "name": "Charcoal/White", "hex": "#2026FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf284/1d5d72cb/AQ011_Charcoal_White_FT.jpg"}, {"code": "HGBK", "name": "Heather Grey/Black", "hex": "#6A7300", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf285/25fc8d35/AQ011_HeatherGrey_Black_FT.jpg"}, {"code": "NYCF", "name": "Navy/Cornflower", "hex": "#0012A8", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf291/a8716b98/AQ011_Navy_Cornflower_FT.jpg"}, {"code": "NYWH", "name": "Navy/White", "hex": "#0012FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf292/16455342/AQ011_Navy_White_FT.jpg"}, {"code": "RBWH", "name": "Royal/White", "hex": "#0034FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf296/a08ca1af/AQ011_Royal_White_FT.jpg"}, {"code": "RDWH", "name": "Red/White", "hex": "#EA00FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf295/5752e02c/AQ011_Red_White_FT.jpg"}, {"code": "WHBK", "name": "White/Black", "hex": "#FFFF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf299/af575025/AQ011_White_Black_FT.jpg"}, {"code": "WHNY", "name": "White/Navy", "hex": "#FFFF12", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf29a/7c0af0c2/AQ011_White_Navy_FT.jpg"}]},
    {id:'aq025', brand:'Asquith & Fox', name:'Women’s polycotton blend polo', sku:'AQ025', tier:'standaard', inkoop:6.25, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b6d05807/69296edf5fa5b59a3a6c12c9/AQ025_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf35c77c87224.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf360397aae28.jpg"}, {"code": "KELL", "name": "Kelly", "hex": "#08DB00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf364cfd100b8.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/48286f73/5c5c3e3deb9f5b1c75cdf36e/e7d06bcf/AQ025_Navy_FT.jpg"}, {"code": "NGRE", "name": "Neon Green", "hex": "#7CFF26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf36f2efdcbb0.jpg"}, {"code": "NORA", "name": "Neon Orange", "hex": "#FF965E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf37073c3c35c.jpg"}, {"code": "NYEL", "name": "Neon Yellow", "hex": "#E8FF56", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf37224aee3dc.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#FF5900", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf37308392fd2.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2503B0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf3740de7da02.jpg"}, {"code": "REDD", "name": "Red", "hex": "#EA0023", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf37541e0b96e.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#0034CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b367a86d/5c5c3e3deb9f5b1c75cdf376a9f68689.jpg"}, {"code": "SLAT", "name": "Slate", "hex": "#1F332E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf37809ff2be5.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ab62ceb9/5c5c3e3deb9f5b1c75cdf37b/cc67788b/AQ025_White_FT.jpg"}]},
    {id:'aq001', brand:'Asquith & Fox', name:'PRINTGUARD recycled polyester polo', sku:'AQ001', tier:'standaard', inkoop:5.95, tags:["Organic", "Recycled"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0501cdec/69296a735fa5b59a3a6b6add/AQ001_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000011/AQ001_Black_FT.jpg"}, {"code": "BOTT", "name": "Bottle", "hex": "#041004", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000012/AQ001_Bottle_FT.jpg"}, {"code": "FNAV", "name": "French Navy", "hex": "#00070F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000014/AQ001_FrenchNavy_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000015/AQ001_Navy_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2503B0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000016/AQ001_Purple_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#EA0023", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000017/AQ001_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#0034CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000018/AQ001_Royal_FT.jpg"}, {"code": "SAPP", "name": "Sapphire", "hex": "#0082C3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce6510000019/AQ001_Sapphire_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce651000001a/AQ001_White_FT.jpg"}]},
    {id:'aq002', brand:'Asquith & Fox', name:'Men\'s GlacierTech polo', sku:'AQ002', tier:'standaard', inkoop:8.80, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c1c1feab/69296c3f5fa5b59a3a6ba8a9/AQ002_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce651000001b/AQ002_Black_FT.jpg"}, {"code": "BOTT", "name": "Bottle", "hex": "#041004", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce651000001c/AQ002_Bottle_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#20262D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce651000001d/AQ002_Charcoal_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f7884db80ce651000001e/AQ002_Navy_FT.jpg"}, {"code": "PURP", "name": "Purple", "hex": "#2503B0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f78dfdb80ce6510000024/AQ002_Purple_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#0034CC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f78dfdb80ce6510000022/AQ002_Royal_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/17e4004c/661f78dfdb80ce6510000023/AQ002_White_FT.jpg"}]},
    {id:'gd017', brand:'Gildan', name:'Softstyle™ adult double piqué polo', sku:'GD017', tier:'standaard', inkoop:6.05, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4dbffeec/692973d85fa5b59a3a6cd6a6/GD017_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a73/1f16b13a/GD017_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a74/7adda9bf/GD017_Charcoal_FT.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#AF0021", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/6564b0d6d9e4aa3e83000008/ef990b4e/GD017_CherryRed_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a7e/3de9e38d/GD017_Navy_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a81/2338f9a5/GD017_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a83/6950ac31/GD017_Royal_FT.jpg"}, {"code": "SPGY", "name": "Ringspun Sport Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a82/e5fe82a4/GD017_RingspunSportGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2a86/f0085f6b/GD017_White_FT.jpg"}]},
    {id:'aq012', brand:'Asquith & Fox', name:'Men\'s classic fit contrast polo', sku:'AQ012', tier:'standaard', inkoop:8.60, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8b1a032d/69296a025fa5b59a3a6b59ec/AQ012_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BKLI", "name": "Black/Lime", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf29bc8e415ca.jpg"}, {"code": "BKOR", "name": "Black/Orange", "hex": "#000059", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf29cd9c612ca.jpg"}, {"code": "BKRD", "name": "Black/Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf29df3feafa9.jpg"}, {"code": "BKWH", "name": "Black/White", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf29ec186f1c9.jpg"}, {"code": "CHHG", "name": "Charcoal/Heather Grey", "hex": "#202673", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ab62ceb9/5c5c3e3deb9f5b1c75cdf2a0/9aaa5539/AQ012_Charcoal_HeatherGrey_FT.jpg"}, {"code": "NYRD", "name": "Navy/Red", "hex": "#001200", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2a9a2445c40.jpg"}, {"code": "NYWH", "name": "Navy/White", "hex": "#0012FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2aa1efbcb2e.jpg"}, {"code": "RBWH", "name": "Royal/White", "hex": "#0034FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2ae16ff1b50.jpg"}]},
    {id:'aq022', brand:'Asquith & Fox', name:'Women\'s contrast polo', sku:'AQ022', tier:'standaard', inkoop:8.60, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5fa4c7a4/69296cd85fa5b59a3a6bc1aa/AQ022_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "BKOR", "name": "Black/Orange", "hex": "#000059", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf33b9b2cb4ba.jpg"}, {"code": "BKRD", "name": "Black/Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf33c1f3196f6.jpg"}, {"code": "BKWH", "name": "Black/White", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ab62ceb9/5c5c3e3deb9f5b1c75cdf33d/5028a5a6/AQ022_Black_White_FT.jpg"}, {"code": "NYRD", "name": "Navy/Red", "hex": "#001200", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf34a988a8fb6.jpg"}, {"code": "NYWH", "name": "Navy/White", "hex": "#0012FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf34bbffec7c2.jpg"}, {"code": "PUPK", "name": "Purple/Pink", "hex": "#2503B0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf34cdeaf8e51.jpg"}, {"code": "RBWH", "name": "Royal/White", "hex": "#0034FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf34f34cc8c19.jpg"}, {"code": "TQRD", "name": "Turquoise/Red", "hex": "#3FFF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf350106b712f.jpg"}]},
    {id:'gd040', brand:'Gildan', name:'DryBlend® Jersey knit polo', sku:'GD040', tier:'standaard', inkoop:6.35, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6957d88c/69296fdc5fa5b59a3a6c3af8/GD040_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ad2/c88cb6ce/GD040_Black_FT.jpg"}, {"code": "DHEA", "name": "Dark Heather", "hex": "#314F60", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ad4/2236de40/GD040_DarkHeather_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ae0/f84fa034/GD040_Navy_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ae3/6b22204b/GD040_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ae4/f22232af/GD040_Royal_FT.jpg"}, {"code": "SPGY", "name": "Sports Grey", "hex": "#7A8386", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c669c810d4a323c38000050/e73edd8a/GD040_SportsGrey_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2ae9/cc18de96/GD040_White_FT.jpg"}]},
    {id:'gd042', brand:'Gildan', name:'Hammer® piqué sport shirt', sku:'GD042', tier:'standaard', inkoop:8.25, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/61fa8ffd/69296d985fa5b59a3a6be17b/GD042_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aea/8668f6bc/GD042_Black_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aec/57c51385/GD042_Charcoal_FT.jpg"}, {"code": "NAVY", "name": "Navy*", "hex": "#041747", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2aff/e8c83e68/GD042_Navy_FT.jpg"}, {"code": "REDD", "name": "Red*", "hex": "#FF0047", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b02/b68f8aaa/GD042_Red_FT.jpg"}, {"code": "ROYA", "name": "Royal*", "hex": "#003DE5", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b03/5df0ea60/GD042_Royal_FT.jpg"}, {"code": "SPGY", "name": "RS Sport Grey*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b01/03ab07eb/GD042_RSSportGrey_FT.jpg"}, {"code": "WHIT", "name": "White*", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/13bdc30c/5c5c3e3deb9f5b1c75ce2b07/08f214db/GD042_White_FT.jpg"}]},
    {id:'aq082', brand:'Asquith & Fox', name:'Men\'s organic polo', sku:'AQ082', tier:'standaard', inkoop:7.35, tags:["Organic"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/63a01f15/69aa94e1ba355a0b9500056a/AQ082_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/be5a97d3/5dfaae756aa61102730005426fdb6e74.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#300C26", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/be5a97d3/5dfaae756aa6110273000544d91924de.jpg"}, {"code": "CHRD", "name": "Cherry Red", "hex": "#D90043", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/be5a97d3/5dfaae756aa611027300054511e0d510.jpg"}, {"code": "GRAP", "name": "Graphite", "hex": "#343C45", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/be5a97d3/5dfaae756aa61102730005463009ccef.jpg"}, {"code": "SLAT", "name": "Slate", "hex": "#1F332E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/be5a97d3/5dfaae756aa611027300054971d5e152.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/be5a97d3/5dfaae756aa611027300054bec92c76a.jpg"}]},
    {id:'aq030', brand:'Asquith & Fox', name:'Men\'s classic fit long sleeved polo', sku:'AQ030', tier:'standaard', inkoop:9.40, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5e03e52c/692971c75fa5b59a3a6c8778/AQ030_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf37c1f8a5f22.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#20262D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/32845449/674084b63d2d8f5e4800001a/AQ030_Charcoal_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf3859b35417a.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ab62ceb9/5c5c3e3deb9f5b1c75cdf386/0503d520/AQ030_White_FT.jpg"}]},
    {id:'by421', brand:'Build Your Brand', name:'Oversized polo shirt', sku:'BY421', tier:'standaard', inkoop:10.55, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c007ceae/69296cf75fa5b59a3a6bc6d4/BY421_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#262429", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00018b/BY421_Black_FT.jpg"}, {"code": "CLOU", "name": "Cloud", "hex": "#D5D0CA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00018c/BY421_Cloud_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#313343", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00018d/BY421_Navy_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#E7E6EC", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f00018e/BY421_White_FT.jpg"}]},
    {id:'aq005', brand:'Asquith & Fox', name:'Men\'s super smooth knit polo', sku:'AQ005', tier:'standaard', inkoop:6.75, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7bf20c14/692969f95fa5b59a3a6b586a/AQ005_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2123f44186b.jpg"}, {"code": "BROY", "name": "Bright Royal", "hex": "#004BF4", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf2147a02fe68.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#001243", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf21e5259347c.jpg"}, {"code": "TURQ", "name": "Turquoise", "hex": "#3FFFF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf221ae1ebce8.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c6199845/5c5c3e3deb9f5b1c75cdf222e574a059.jpg"}]},
    {id:'by368', brand:'Build Your Brand', name:'Women’s Sorona polo tee', sku:'BY368', tier:'standaard', inkoop:12.35, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c2851967/69296e2c5fa5b59a3a6bf7d8/BY368_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000015/fd29169e/BY368_Black_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#2E2E38", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f026500007c/2c87d0ac/BY368_Navy_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000053/248b2f4c/BY368_White_FT.jpg"}]},
    {id:'aq021', brand:'Asquith & Fox', name:'Women\'s classic fit tipped polo', sku:'AQ021', tier:'standaard', inkoop:8.55, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4eadaefa/69296f285fa5b59a3a6c1ec4/AQ021_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL"], colors:[{"code": "BKRD", "name": "Black/Red", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf328/e1979692/AQ021_Black_Red_FT.jpg"}, {"code": "BKWH", "name": "Black/White", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf329/dcbf7a32/AQ021_Black_White_FT.jpg"}, {"code": "NYWH", "name": "Navy/White", "hex": "#0012FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf336/03ddda65/AQ021_Navy_White_FT.jpg"}, {"code": "WHNY", "name": "White/Navy", "hex": "#FFFF12", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b73b91bb/5c5c3e3deb9f5b1c75cdf339/671def66/AQ021_White_Navy_FT.jpg"}]},
    {id:'by363', brand:'Build Your Brand', name:'Sorona polo tee', sku:'BY363', tier:'standaard', inkoop:12.70, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9df29426/69296f305fa5b59a3a6c200c/BY363_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f026500003f/57290012/BY363_Black_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#292935", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000028/c17fcbb0/BY363_Navy_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/674085a63d2d8f0265000068/0a0f24f5/BY363_White_FT.jpg"}]},
    {id:'by008', brand:'Build Your Brand', name:'Piqué polo shirt', sku:'BY008', tier:'standaard', inkoop:7.65, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4d93e82a/692972b35fa5b59a3a6ca9b6/BY008_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce18dbfc2ebde6.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce18eb12f97297.jpg"}]},
  ],
  cap:[
    {id:'lw90t', brand:'Larkwood', name:'Baby/toddler cap', sku:'LW90T', tier:'budget', inkoop:3.30, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9cc90df4/692970125fa5b59a3a6c4341/LW90T_LS00_2026.jpg', sizes:["612", "12", "35"], colors:[{"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53d94e8957f2.jpg"}, {"code": "PABL", "name": "Pale Blue", "hex": "#C6EDF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53dad2f7c6fd.jpg"}, {"code": "PAPK", "name": "Pale Pink", "hex": "#EFD3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53db1c241ed5.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53dc5f8507e6.jpg"}]},
    {id:'by001', brand:'Build Your Brand', name:'Heavy knit beanie', sku:'BY001', tier:'budget', inkoop:3.15, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/eab2701e/692971a05fa5b59a3a6c80ef/BY001_LS00_2026.jpg', sizes:["One Size", "One size"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5c5c3e3deb9f5b1c75ce1885/36e7a18e/BY001_Black_FT.jpg"}, {"code": "BURG", "name": "Burgundy", "hex": "#B73750", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5c5c3e3deb9f5b1c75ce1886/94e9f505/BY001_Burgundy_FT.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#232323", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5c5c3e3deb9f5b1c75ce1887/ea623940/BY001_Charcoal_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#9E9E9E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5c5c3e3deb9f5b1c75ce1888/44ae1fd0/BY001_HeatherGrey_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#2B2E32", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/5c5c3e3deb9f5b1c75ce1893/30b3237e/BY001_Navy_FT.jpg"}, {"code": "ORAN", "name": "Orange", "hex": "#E86A00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000007/57dabb34/BY001_Orange_FT.jpg"}, {"code": "REDD", "name": "Red", "hex": "#AA0003", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000008/c3d3a0fb/BY001_Red_FT.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/e2182403/63874d2248718b5b23000009/76f88d43/BY001_White_FT.jpg"}]},
    {id:'by002', brand:'Build Your Brand', name:'Jersey beanie', sku:'BY002', tier:'budget', inkoop:3.75, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c1fd9741/692970de5fa5b59a3a6c6368/BY002_LS00_2026.jpg', sizes:["One Size"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce18940914dffd.jpg"}, {"code": "CHAR", "name": "Charcoal", "hex": "#3D3D3D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a35e58e9/5c5c3e3deb9f5b1c75ce1895/dfb1a9d3/BY002_Charcoal_FT.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#A3A3A3", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce1896dfca4a0b.jpg"}, {"code": "MIGY", "name": "Mid Grey", "hex": "#666666", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1899ae169d6d.jpg"}]},
    {id:'by154', brand:'Build Your Brand', name:'Recycled yarn fisherman beanie', sku:'BY154', tier:'standaard', inkoop:5.10, tags:["Organic", "Recycled"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/4acd09bb/69296cf75fa5b59a3a6bc6ff/BY154_LS00_2026.jpg', sizes:["One Size"], colors:[{"code": "ASPH", "name": "Asphalt", "hex": "#76747F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8dac2995/5fcf5c469713820018736102/BY154_Asphalt_FT.jpg"}, {"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8dac2995/5fcf5c469713820018736103/BY154_Black_FT.jpg"}]},
  ],
  tas:[
    {id:'by059', brand:'Build Your Brand', name:'Hip bag', sku:'BY059', tier:'standaard', inkoop:5.60, tags:[], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/54079519/69296e815fa5b59a3a6c035b/BY059_LS00_2026.jpg', sizes:["One Size", "One size"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1ad70bc9815b.jpg"}, {"code": "GREY", "name": "Grey", "hex": "#605D5C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b2300004b/BY059_Grey_FT.jpg"}]},
  ],
  jack:[
    {id:'by016', brand:'Build Your Brand', name:'Wind runner', sku:'BY016', tier:'premium', inkoop:21.00, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/ddebad91/692971b45fa5b59a3a6c8432/BY016_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BKBK", "name": "Black/Black", "hex": "#41411E", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce1943c669dbc3.jpg"}, {"code": "BKRD", "name": "Black/Red", "hex": "#2F2F3F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5226e114/5c5c3e3deb9f5b1c75ce1944b1480a64.jpg"}, {"code": "BKWH", "name": "Black/White", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/348de6cd/5c5c3e3deb9f5b1c75ce1945427f471c.jpg"}, {"code": "BUBK", "name": "Burgundy/Black", "hex": "#AA3400", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000005/BY016_Burgundy_Black_FT.jpg"}, {"code": "DSDS", "name": "Dark Shadow/Dark Shadow", "hex": "#746F40", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/661267df/61a7ce3cfe9cd9426c000006/BY016_DarkShadow_DarkShadow_FT.jpg"}, {"code": "NYNY", "name": "Navy/Navy", "hex": "#2E2E00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/5c5c3e3deb9f5b1c75ce194e/f41e9f8a/BY016_Navy_Navy_FT.jpg"}]},
    {id:'by406', brand:'Build Your Brand', name:'Bonded sherpa jacket', sku:'BY406', tier:'premium', inkoop:35.35, tags:["Organic"], eco:true, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/d801e2af/692969db5fa5b59a3a6b5398/BY406_LS00_2026.jpg', sizes:["2XS", "XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#22201F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/afa5ee9f/68b01ed4ba355a0a92000030/BY406_Black_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#595878", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/afa5ee9f/68b01ed4ba355a0a92000031/BY406_Navy_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#C5BFAD", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/afa5ee9f/68b01ed4ba355a0a92000032/BY406_PaleOlive_FT.jpg"}, {"code": "WHSA", "name": "White Sand", "hex": "#F2EEE2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/afa5ee9f/68b01ed4ba355a0a92000033/BY406_WhiteSand_FT.jpg"}]},
    {id:'bb004', brand:'Build Your Brand Basic', name:'Basic college jacket', sku:'BB004', tier:'premium', inkoop:18.45, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/50ef67fc/69296a265fa5b59a3a6b5f70/BB004_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BKHG", "name": "Black/Heather Grey", "hex": "#2927C0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fcf/d0e01d47/BB004_Black_HeatherGrey_FT.jpg"}, {"code": "BKWH", "name": "Black/White", "hex": "#0000FF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/92fb23d0/5fd346e0647a2d5177000010/2495221c/BB004_Black_White_FT.jpg"}, {"code": "HGWH", "name": "Heather Grey/White", "hex": "#CBCAA7", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fd0/45e77cf9/BB004_HeatherGrey_White_FT.jpg"}, {"code": "NYWH", "name": "Navy/White", "hex": "#3634E2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b753e826/5fcf5c3b9713820018735fd1/a46972c8/BB004_Navy_White_FT.jpg"}]},
    {id:'lw31t', brand:'Larkwood', name:'1/2-zip lightweight fleece', sku:'LW31T', tier:'standaard', inkoop:6.30, tags:["Organic", "Recycled", "Kids"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/c2468ef5/69296db15fa5b59a3a6be5a2/LW31T_LS00_2026.jpg', sizes:["0/6", "6/12", "12/18", "18/24", "24/36", "3/4", "5/6"], colors:[{"code": "LSTO", "name": "Light Stone", "hex": "#D0BEB0", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000044/LW31T_LightStone_FT.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#333754", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000045/LW31T_Navy_FT.jpg"}, {"code": "SBLU", "name": "Stone Blue", "hex": "#676F82", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000047/LW31T_StoneBlue_FT.jpg"}, {"code": "SOPK", "name": "Soft Pink", "hex": "#BE8B88", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/68ca385a/692833529eee9a2c41000046/LW31T_SoftPink_FT.jpg"}]},
    {id:'by312', brand:'Build Your Brand', name:'Heavy ounce boxy denim jacket', sku:'BY312', tier:'premium', inkoop:32.55, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/39e2f066/69296a665fa5b59a3a6b687e/BY312_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BKWA", "name": "Black Washed", "hex": "#5B5B5B", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cf5b7d17/674085a63d2d8f0265000076/392292a0/BY312_BlackWashed_FT.jpg"}, {"code": "LBLW", "name": "New Light Blue Washed", "hex": "#85A2C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cf5b7d17/674085a63d2d8f0265000026/8b336c28/BY312_NewLightBlueWashed_FT.jpg"}, {"code": "MBLW", "name": "New Mid Blue Washed", "hex": "#334870", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cf5b7d17/674085a63d2d8f0265000027/b22ca6ce/BY312_NewMidBlueWashed_FT.jpg"}]},
    {id:'by030', brand:'Build Your Brand', name:'Bomber jacket', sku:'BY030', tier:'premium', inkoop:32.40, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8bbcf29d/69296a475fa5b59a3a6b6437/BY030_LS00_2026.jpg', sizes:["XS", "S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black*", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b3e77009/5c5c3e3deb9f5b1c75ce19cdf8b0eca4.jpg"}, {"code": "UNBE", "name": "Union Beige", "hex": "#D8C0A6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/5b1ecc0f/63874d2248718b5b23000040/BY030_UnionBeige_FT.jpg"}]},
    {id:'by096', brand:'Build Your Brand', name:'Basic pullover jacket', sku:'BY096', tier:'premium', inkoop:26.95, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/65e7cad0/6929715d5fa5b59a3a6c7738/BY096_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/65ec2e45/5dfb3d466aa6110ae20009b660f4fbcd.jpg"}, {"code": "OLIV", "name": "Olive", "hex": "#827143", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/65ec2e45/5dfb3d466aa6110ae20009b7ca875591.jpg"}]},
    {id:'by309', brand:'Build Your Brand', name:'Oversized puffer jacket', sku:'BY309', tier:'premium', inkoop:46.50, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/048ee70d/692971535fa5b59a3a6c7623/BY309_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b1434788700010d/BY309_Black_FT.jpg"}, {"code": "POLI", "name": "Pale Olive", "hex": "#938974", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/a0a0ee38/67b74e346b1434788700010e/BY309_PaleOlive_FT.jpg"}]},
    {id:'by428', brand:'Build Your Brand', name:'Basic workwear jacket', sku:'BY428', tier:'premium', inkoop:36.80, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/210522f6/692969f65fa5b59a3a6b57da/BY428_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL", "3XL", "4XL", "5XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#1F1A1C", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000193/BY428_Black_FT.jpg"}, {"code": "UNBE", "name": "Union Beige", "hex": "#C9A680", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/6ddfeedb/692832a89eee9a377f000194/BY428_UnionBeige_FT.jpg"}]},
    {id:'lw35t', brand:'Larkwood', name:'Rain jacket', sku:'LW35T', tier:'premium', inkoop:15.25, tags:["Kids"], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/44e4e1a6/692969d05fa5b59a3a6b519d/LW35T_LS00_2026.jpg', sizes:["612", "1218", "1824", "2436", "34"], colors:[{"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce534265b4e9b9.JPG"}, {"code": "PINK", "name": "Pink", "hex": "#EFD3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/768f7fcb/610283d53f66332d09000003/LW35T_Pink_FT.jpg"}, {"code": "YELL", "name": "Yellow", "hex": "#FFD83F", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce534443afabd4.JPG"}]},
    {id:'aq200', brand:'Asquith & Fox', name:'Men\'s Harrington jacket', sku:'AQ200', tier:'premium', inkoop:22.15, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/29188c0d/69a99d7eba355a5ac3000568/AQ200_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf5414225e59b.jpg"}]},
    {id:'aq203', brand:'Asquith & Fox', name:'Men\'s padded wind jacket', sku:'AQ203', tier:'premium', inkoop:33.25, tags:[], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/27ad51c7/69a99d22ba355a7a5a000568/AQ203_LS00_2026.jpg', sizes:["S", "M", "L", "XL", "2XL"], colors:[{"code": "NYCH", "name": "Navy/Charcoal", "hex": "#001226", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7acf52b2/5c5c3e3deb9f5b1c75cdf555f440e0c7.jpg"}]},
  ],
  baby:[
    {id:'lw55t', brand:'Larkwood', name:'Short-sleeved bodysuit with envelope neck opening', sku:'LW55T', tier:'budget', inkoop:4.60, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/cafaf799/69296d855fa5b59a3a6bded1/LW55T_LS00_2026.jpg', sizes:["03", "36", "612", "1218"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce5385902649b0.JPG"}, {"code": "FUCH", "name": "Fuchsia", "hex": "#E00088", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce5386479b80c1.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#C1C1C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53877844d572.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce538aee73e8d2.jpg"}, {"code": "PABL", "name": "Pale Blue", "hex": "#C6EDF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce538b49d1c386.jpg"}, {"code": "PAPK", "name": "Pale Pink", "hex": "#EFD3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce538c371e97be.jpg"}, {"code": "REDD", "name": "Red", "hex": "#FF0032", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce538d2d944afd.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce5390ff3e6305.jpg"}]},
    {id:'lw500', brand:'Larkwood', name:'Essential short-sleeved bodysuit', sku:'LW500', tier:'budget', inkoop:3.80, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/8ac74c93/69296f145fa5b59a3a6c1ba9/LW500_LS00_2026.jpg', sizes:["03", "36", "612", "1218"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc0000154523b095.JPG"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#C1C1C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc0000171bfa3edd.JPG"}, {"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc000018a9e0cb48.JPG"}, {"code": "PABL", "name": "Pale Blue", "hex": "#C6EDF2", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc000019ff0bce35.JPG"}, {"code": "PAPK", "name": "Pale Pink", "hex": "#EFD3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc00001ad48573c2.JPG"}, {"code": "REDD", "name": "Red", "hex": "#FF0032", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc00001c6bb4ad9e.JPG"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc00001e0c5771a1.JPG"}]},
    {id:'lw655', brand:'Larkwood', name:'Organic bodysuit', sku:'LW655', tier:'standaard', inkoop:5.10, tags:["Organic", "Kids"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b79abc6b/69296da95fa5b59a3a6be45a/LW655_LS00_2026.jpg', sizes:["New Born", "03", "36", "612", "1218"], colors:[{"code": "NATU", "name": "Natural", "hex": "#FFF4D6", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000d0ab3215536.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000d0bee6c58d0.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000d0cd1e98cc8.jpg"}]},
    {id:'lw70t', brand:'Larkwood', name:'Fleece all-in-one', sku:'LW70T', tier:'standaard', inkoop:12.70, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/9fa420ca/69296e365fa5b59a3a6bf920/LW70T_LS00_2026.jpg', sizes:["612", "1218", "1824", "2436"], colors:[{"code": "BLAC", "name": "Black", "hex": "#000000", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5e6b62efce60dd615800005b.jpg"}, {"code": "HGRE", "name": "Heather Grey", "hex": "#C1C1C1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53a6279f1fd6.jpg"}, {"code": "NAVY", "name": "Navy", "hex": "#0C253D", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce53ab40d8ad46.jpg"}]},
    {id:'lw73t', brand:'Larkwood', name:'Rabbit all-in-one', sku:'LW73T', tier:'premium', inkoop:17.40, tags:["Kids"], eco:false, prem:true, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7991861b/69aaab73ba355a3e29000569/26d2def9/LW73T_LS00_2026.jpg', sizes:["06", "1218", "1824", "2436", "612"], colors:[{"code": "PINK", "name": "Pink", "hex": "#EFD3EA", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000d14135334e0.jpg"}, {"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/b5898dac/5dfabf896aa61163fd000d15b9859353.jpg"}]},
    {id:'lw502', brand:'Larkwood', name:'Essential short-sleeved baseball bodysuit', sku:'LW502', tier:'budget', inkoop:3.85, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/66e783d2/692971b05fa5b59a3a6c8392/LW502_LS00_2026.jpg', sizes:["03", "36", "612", "1218"], colors:[{"code": "WHHG", "name": "White/Heather Grey", "hex": "#FFFFC1", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc0000205e6fc922.JPG"}, {"code": "WHRD", "name": "White/Red", "hex": "#FFFF00", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c7e43da0d4a327bbc000022efec46e8.JPG"}]},
    {id:'lw650', brand:'Larkwood', name:'Organic sleepsuit', sku:'LW650', tier:'standaard', inkoop:7.65, tags:["Organic", "Kids"], eco:true, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/0903c7ce/692972b55fa5b59a3a6caa18/LW650_LS00_2026.jpg', sizes:["New Born", "03", "36", "612", "1218"], colors:[{"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/51c9ce67/5dfabf896aa61163fd000d071eb01197.jpg"}]},
    {id:'lw52t', brand:'Larkwood', name:'Long sleeve baby bodysuit', sku:'LW52T', tier:'standaard', inkoop:5.10, tags:["Kids"], eco:false, prem:false, img:'https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/588a71f2/69296cd55fa5b59a3a6bc140/LW52T_LS00_2026.jpg', sizes:["03", "36", "612", "1218"], colors:[{"code": "WHIT", "name": "White", "hex": "#FFFFFF", "img": "https://cdn.pimber.ly/public/asset/raw/571f95845f13380f0056d06a/7dac6704/5c5c3e3deb9f5b1c75ce537a39d05dac.jpg"}]},
  ],
};

// ── Markup per tier (aanpasbaar) ───────────────────────────────────────────────
// tier: 'budget' = 50%, 'standaard' = 65%, 'premium' = 80%
// inkoop = inkoopprijs ex BTW in euros
// Verkoopprijs ex BTW = inkoop * (1 + MARKUP[tier])
// Pas tier en inkoop per model aan naar wens
const MARKUP = {budget: 0.50, standaard: 0.65, premium: 0.80};
const TIER_LABEL = {budget:'Budget', standaard:'Standaard', premium:'Premium'};

// Textielkorting op verkoopprijs ex BTW
// 10% korting vanaf 10 stuks, 20% korting vanaf 100 stuks
function textielKorting(qty){
  if(qty>=100) return 0.20;
  if(qty>=10)  return 0.10;
  return 0;
}
function kortingLabel(qty){
  if(qty>=100) return '20% korting (100+ stuks)';
  if(qty>=10)  return '10% korting (10+ stuks)';
  return null;
}

function verkoopExBtw(mdl, qty=1){
  if(!mdl||!mdl.inkoop) return 0;
  const basis = mdl.inkoop * (1 + MARKUP[mdl.tier||'standaard']);
  return basis * (1 - textielKorting(qty));
}

// ── SVG Productillustraties ───────────────────────────────────────────────────
const CAT_ICONS={
  tshirt:`<svg viewBox="0 0 80 72" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M28 8 L14 18 L4 14 L10 36 L20 34 L20 64 L60 64 L60 34 L70 36 L76 14 L66 18 L52 8 Q44 16 40 16 Q36 16 28 8Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" fill="none"/>
  </svg>`,
  sweater:`<svg viewBox="0 0 80 72" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M28 8 L14 18 L4 14 L10 38 L20 36 L20 66 L60 66 L60 36 L70 38 L76 14 L66 18 L52 8 Q44 18 40 18 Q36 18 28 8Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" fill="none"/>
    <path d="M20 38 Q40 42 60 38" stroke="currentColor" stroke-width="2" fill="none"/>
    <path d="M20 44 Q40 48 60 44" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 2" fill="none"/>
  </svg>`,
  hoodie:`<svg viewBox="0 0 80 76" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M28 8 L14 20 L4 16 L10 40 L20 38 L20 68 L60 68 L60 38 L70 40 L76 16 L66 20 L52 8 Q48 20 40 22 Q32 20 28 8Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" fill="none"/>
    <path d="M33 8 Q36 14 40 14 Q44 14 47 8" stroke="currentColor" stroke-width="2" fill="none"/>
    <rect x="33" y="38" width="14" height="8" rx="2" stroke="currentColor" stroke-width="1.8" fill="none"/>
    <line x1="40" y1="38" x2="40" y2="46" stroke="currentColor" stroke-width="1.5"/>
  </svg>`,
  polo:`<svg viewBox="0 0 80 76" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M28 8 L14 20 L4 16 L10 40 L20 38 L20 68 L60 68 L60 38 L70 40 L76 16 L66 20 L52 8 L46 14 L40 16 L34 14 Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" fill="none"/>
    <path d="M34 14 L34 28 Q40 32 46 28 L46 14" stroke="currentColor" stroke-width="2" fill="none"/>
    <line x1="40" y1="16" x2="40" y2="26" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 2"/>
    <circle cx="40" cy="22" r="1.2" fill="currentColor"/>
    <circle cx="40" cy="18" r="1.2" fill="currentColor"/>
  </svg>`,
  cap:`<svg viewBox="0 0 80 64" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M16 42 Q16 18 40 14 Q64 18 64 42" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linejoin="round"/>
    <path d="M14 42 Q14 48 40 48 Q66 48 66 42" stroke="currentColor" stroke-width="2.5" fill="none"/>
    <path d="M14 42 Q8 44 6 50 Q14 52 24 48" stroke="currentColor" stroke-width="2" fill="none"/>
    <path d="M36 14 Q40 10 44 14" stroke="currentColor" stroke-width="2" fill="none"/>
    <line x1="40" y1="14" x2="40" y2="48" stroke="currentColor" stroke-width="1.2" stroke-dasharray="2 3"/>
  </svg>`,
  tas:`<svg viewBox="0 0 80 76" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="14" y="28" width="52" height="40" rx="4" stroke="currentColor" stroke-width="2.5" fill="none"/>
    <path d="M28 28 Q28 14 40 14 Q52 14 52 28" stroke="currentColor" stroke-width="2.5" fill="none"/>
    <line x1="14" y1="40" x2="66" y2="40" stroke="currentColor" stroke-width="1.8"/>
    <rect x="34" y="44" width="12" height="8" rx="2" stroke="currentColor" stroke-width="1.8" fill="none"/>
  </svg>`,
  jack:`<svg viewBox="0 0 80 76" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M28 8 L12 22 L4 18 L8 44 L20 42 L20 68 L60 68 L60 42 L72 44 L76 18 L68 22 L52 8 L46 12 L40 14 L34 12 Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" fill="none"/>
    <line x1="40" y1="14" x2="40" y2="68" stroke="currentColor" stroke-width="1.8"/>
    <circle cx="40" cy="22" r="1.5" fill="currentColor"/>
    <circle cx="40" cy="30" r="1.5" fill="currentColor"/>
    <circle cx="40" cy="38" r="1.5" fill="currentColor"/>
    <path d="M20 32 L28 32" stroke="currentColor" stroke-width="1.5"/>
    <path d="M52 32 L60 32" stroke="currentColor" stroke-width="1.5"/>
  </svg>`,
  baby:`<svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="40" cy="18" r="10" stroke="currentColor" stroke-width="2.5" fill="none"/>
    <path d="M22 36 Q22 28 40 28 Q58 28 58 36 L58 58 Q58 66 50 66 L30 66 Q22 66 22 58 Z" stroke="currentColor" stroke-width="2.5" fill="none"/>
    <path d="M22 40 L10 44 Q8 52 14 54 L22 52" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round"/>
    <path d="M58 40 L70 44 Q72 52 66 54 L58 52" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round"/>
    <path d="M32 66 L28 74" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
    <path d="M48 66 L52 74" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
    <circle cx="36" cy="42" r="1.5" fill="currentColor"/>
    <circle cx="44" cy="42" r="1.5" fill="currentColor"/>
    <path d="M36 50 Q40 54 44 50" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round"/>
  </svg>`,
};


// Pricing
const DP=[{min:1,max:4,u:9.50},{min:5,max:9,u:7.50},{min:10,max:24,u:5.50},{min:25,max:49,u:4.50},{min:50,max:99,u:3.75},{min:100,max:9999,u:3.00}];
const ZP=[
  {min:25,   max:49,   c:[4.41,7.25,9.35,11.57]},
  {min:50,   max:99,   c:[2.83,4.41,5.62,7.00]},
  {min:100,  max:249,  c:[1.77,2.62,3.37,4.24]},
  {min:250,  max:499,  c:[1.27,1.76,2.11,2.63]},
  {min:500,  max:999,  c:[0.96,1.27,1.58,1.89]},
  {min:1000, max:2499, c:[0.77,1.02,1.16,1.43]},
  {min:2500, max:4999, c:[0.68,0.83,0.96,1.17]},
  {min:5000, max:9999, c:[0.58,0.66,0.79,0.91]},
  {min:10000,max:99999,c:[0.50,0.61,0.70,0.82]},
];

// ── State ──────────────────────────────────────────────────────────────────────
const BTW = 0.21;

const S={
  cat:null, mdl:null, clrId:null, clrName:null, clrHex:null, clrImg:null,
  pos:null,            // 'front' | 'back' | 'both'
  techA:null,          // techniek kant A (voorkant / enkel)
  techB:null,          // techniek kant B (achterkant, alleen bij 'both')
  zcA:1, zcB:1,        // zeefdruk kleuren per kant
  configuring:'A',     // welke kant configureert stap 3 nu
  qty:0, korting:0,
  upA:0, upB:0,        // drukprijs per stuk ex BTW per kant
  textielEx:0,         // textielprijsprijs per stuk ex BTW
  shipEx:0,            // verzending ex BTW
  totEx:0,             // totaal ex BTW
  btwBedrag:0,         // 21% BTW bedrag
  totIncl:0,           // totaal incl. BTW (= wat PayPal ontvangt)
};

// ── Helpers ────────────────────────────────────────────────────────────────────
const fmt    = n => '€'+n.toFixed(2).replace('.',',');  // incl BTW label
const fmtEx  = n => '€'+n.toFixed(2).replace('.',',');  // ex BTW (zelfde opmaak, ander label)
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
    if(n===2) setupStep2();
    if(n===4) setupStep4();
    if(n===5) setupStep5();
    if(n===6) fillSum();
  }
  window.scrollTo({top:0,behavior:'smooth'});
}

// PROG_LABELS wordt dynamisch gebouwd via i18n (zie vertaalUI)
const PROG_LABELS_FN=()=>({
  1:t('prog')[1]||'Stap 1 van 6',
  2:t('prog')[2]||'Stap 2 van 6',
  3:t('prog')[3]||'Stap 3 van 6',
  4:t('prog')[4]||'Stap 4 van 6',
  5:t('prog')[5]||'Stap 5 van 6',
  6:t('prog')[6]||'Stap 6 van 6',
});
function updProg(n){
  for(let i=1;i<=6;i++){
    const el=e('pb'+i);if(!el)continue;
    el.classList.remove('done','active');
    if(i<n) el.classList.add('done');
    else if(i===n) el.classList.add('active');
  }
  const lbl=e('prog-lbl');
  if(lbl){ lbl.innerHTML=PROG_LABELS_FN()[n]||''; lbl.dataset.stap=n; }
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
    if(t.children.length){const sp=document.createElement('span');sp.className='crumb-sep';sp.textContent='›';t.appendChild(sp);}
    const sp=document.createElement('span');sp.className='crumb'+(cur?' cur':fn?' lnk':'');sp.textContent=lbl;
    if(fn) sp.onclick=fn;t.appendChild(sp);
  };
  add('Categorie',sub!=='cat'?()=>s1Show('cat'):null,sub==='cat');
  if(sub==='mdl'||sub==='clr') add(cat?catNaam(cat.id):'Model',sub!=='mdl'?()=>s1Show('mdl'):null,sub==='mdl');
  if(sub==='clr') add(S.mdl?(S.mdl.brand+' '+S.mdl.name):'Kleur',null,true);
}

function buildCatGrid(){
  const g=e('cat-grid');g.innerHTML='';
  CATS.forEach(c=>{
    const d=document.createElement('div');
    d.className='opt cat-opt';d.id='cc-'+c.id;
    const svgIcon=CAT_ICONS[c.icon]||'';
    // Goedkoopste verkoopprijs in deze categorie
    const modellen=MODELS[c.id]||[];
    const vanafPrijs=modellen.length>0
      ? Math.min(...modellen.map(m=>verkoopExBtw(m,1)).filter(p=>p>0))
      : 0;
    const vanafBadge=vanafPrijs>0
      ? `<div style="font-size:.68rem;color:var(--ink3);margin-top:4px;">vanaf <strong style="color:var(--accent);">${fmtEx(vanafPrijs)}</strong> p/stuk</div>`
      : '';
    d.innerHTML=`<div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div><div class="cat-svg">${svgIcon}</div><div class="cat-name">${catNaam(c.id)}</div>${vanafBadge}`;
    d.onclick=()=>selCat(c.id);g.appendChild(d);
  });
}

function selCat(id){
  S.cat=id;S.mdl=null;S.clrId=null;S.clrName=null;S.clrHex=null;
  document.querySelectorAll('.cat-opt').forEach(c=>c.classList.toggle('sel',c.id==='cc-'+id));
  buildMdlGrid(id);s1Show('mdl');
}

// Filter state
let filterState={merk:'', sort:'default', catId:''};

function buildMdlGrid(catId){
  filterState.catId=catId;
  filterState.merk='';
  filterState.sort='default';

  // Vul merkfilter met unieke merken in deze categorie
  const merken=[...new Set((MODELS[catId]||[]).map(m=>m.brand))].sort();
  const merkSel=e('filter-merk');
  if(merkSel){
    merkSel.innerHTML='<option value="">Alle merken</option>';
    merken.forEach(merk=>{
      const opt=document.createElement('option');
      opt.value=merk; opt.textContent=merk;
      merkSel.appendChild(opt);
    });
    merkSel.value='';
  }
  const sortSel=e('filter-sort');
  if(sortSel) sortSel.value='default';

  // Toon/verberg filterbalk (alleen als er meer dan 1 merk is)
  const bar=e('mdl-filter-bar');
  if(bar) bar.style.display=merken.length>1?'flex':'none';

  renderMdlGrid();
}

function applyFilter(){
  filterState.merk=e('filter-merk')?.value||'';
  filterState.sort=e('filter-sort')?.value||'default';
  renderMdlGrid();
}

function resetFilter(){
  filterState.merk=''; filterState.sort='default';
  const merkSel=e('filter-merk'); if(merkSel) merkSel.value='';
  const sortSel=e('filter-sort'); if(sortSel) sortSel.value='default';
  renderMdlGrid();
}

function renderMdlGrid(){
  const g=e('mdl-grid');g.innerHTML='';
  let modellen=[...(MODELS[filterState.catId]||[])];

  // Merk filter
  if(filterState.merk) modellen=modellen.filter(m=>m.brand===filterState.merk);

  // Sortering
  if(filterState.sort==='az') modellen.sort((a,b)=>a.name.localeCompare(b.name,'nl'));
  else if(filterState.sort==='za') modellen.sort((a,b)=>b.name.localeCompare(a.name,'nl'));
  else if(filterState.sort==='prijs-laag') modellen.sort((a,b)=>verkoopExBtw(a,1)-verkoopExBtw(b,1));
  else if(filterState.sort==='prijs-hoog') modellen.sort((a,b)=>verkoopExBtw(b,1)-verkoopExBtw(a,1));

  // Resultaatteller
  const totaal=(MODELS[filterState.catId]||[]).length;
  const resultEl=e('mdl-resultaat');
  if(resultEl){
    if(filterState.merk||filterState.sort!=='default'){
      resultEl.textContent=modellen.length+' van '+totaal+' modellen';
    } else {
      resultEl.textContent=totaal+' modellen';
    }
  }

  modellen.forEach(m=>{
    const d=document.createElement('div');d.className='opt mdl-card';d.id='mc-'+m.id;
    const tags=(m.tags||[]).map(t=>`<span class="mtag${m.eco?' eco':m.prem?' prem':''}">${t}</span>`).join('');
    const tierCls={budget:'mtag',standaard:'mtag',premium:'mtag prem'}[m.tier||'standaard'];
    const tierLbl=TIER_LABEL[m.tier||'standaard'];
    const priceEx=verkoopExBtw(m,1);
    const priceIncl=priceEx*(1+BTW);
    const priceBadge=priceEx>0?`<span class="mdl-price">${fmtEx(priceEx)} <span class="mdl-price-sub">ex BTW · ${fmt(priceIncl)} incl.</span></span>`:'';
    // Product image
    const imgHtml=m.img?`<div class="mdl-img-wrap"><img class="mdl-img" src="${m.img}" alt="${m.name}" loading="lazy" onerror="this.style.display='none'"></div>`:'';
    // Color swatches (max 8 preview)
    const swatches=(m.colors||[]).slice(0,8).map(c=>`<span class="mdl-swatch" style="background:${c.hex}" title="${c.name}"></span>`).join('');
    const moreColors=(m.colors||[]).length>8?`<span class="mdl-clr-more">+${(m.colors||[]).length-8}</span>`:'';
    d.innerHTML=`<div class="chk"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3"/></svg></div>
      ${imgHtml}
      <div class="mdl-brand">${m.brand}</div><div class="mdl-name">${m.name}</div>
      <div class="mdl-sku">${m.sku}</div>
      <div class="mdl-tags"><span class="${tierCls}">${tierLbl}</span>${tags}</div>
      <div class="mdl-swatches">${swatches}${moreColors}</div>
      ${priceBadge}`;
    d.onclick=()=>selMdl(m);g.appendChild(d);
  });
} // einde renderMdlGrid

function selMdl(m){
  S.mdl=m;S.clrId=null;S.clrName=null;S.clrHex=null;
  document.querySelectorAll('#mdl-grid .opt').forEach(c=>c.classList.toggle('sel',c.id==='mc-'+m.id));
  buildSwatches();s1Show('clr');
}

function buildSwatches(){
  const g=e('sw-grid');g.innerHTML='';
  // Gebruik kleuren van het geselecteerde model
  const modelColors=S.mdl&&S.mdl.colors?S.mdl.colors:[];
  modelColors.forEach(c=>{
    // Bepaal of kleur licht is (voor donkere rand)
    const hex=c.hex||'#888';
    const r=parseInt(hex.slice(1,3),16)||0,gv=parseInt(hex.slice(3,5),16)||0,b=parseInt(hex.slice(5,7),16)||0;
    const lum=(r*299+gv*587+b*114)/1000;
    const isLight=lum>200;
    const w=document.createElement('div');w.className='sw'+(isLight?' lc':'');w.id='sw-'+c.code;
    // Toon productafbeelding per kleur als hover/preview
    w.innerHTML=`<div class="sw-circle" style="background:${hex}" title="${c.name}"></div><div class="sw-nm">${c.name.replace(/[*†△]+$/,'').trim()}</div>`;
    w.onclick=()=>selColor({id:c.code,code:c.code,name:c.name.replace(/[*†△]+$/,'').trim(),hex:hex,img:c.img});
    g.appendChild(w);
  });
  e('custom-row').classList.remove('sel');
  e('custom-field').classList.add('hidden');
  e('custom-inp').value='';
  e('chosen-bar').classList.add('hidden');
  const nb=e('btn-kleur-next');if(nb)nb.classList.add('hidden');
}

function selColor(c){
  S.clrId=c.code||c.id;S.clrName=c.name;S.clrHex=c.hex;S.clrImg=c.img||null;
  document.querySelectorAll('.sw').forEach(s=>s.classList.remove('sel'));
  const sw=e('sw-'+(c.code||c.id));if(sw)sw.classList.add('sel');
  e('custom-row').classList.remove('sel');
  e('custom-field').classList.add('hidden');
  showChosenBar(c.hex,c.name,c.img);
  e('btn-kleur-next').classList.remove('hidden');
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
  S.clrName=e('custom-inp').value.trim();gS(2);
}
function showChosenBar(hex,name,img){
  e('chosen-dot').style.background=hex||'conic-gradient(red,orange,yellow,green,blue,violet,red)';
  e('chosen-nm').textContent='Gekozen: '+name;
  // Toon kleurspecifieke productafbeelding indien beschikbaar
  const imgEl=e('chosen-img');
  if(imgEl){if(img){imgEl.src=img;imgEl.style.display='block';}else{imgEl.style.display='none';}}
  e('chosen-bar').classList.remove('hidden');
}

// ── STAP 2: Positie ────────────────────────────────────────────────────────────
// Alle geldige positie-IDs
const POS_IDS=['front','back','lborst','rborst','both','lborst-back','rborst-back','borst-both'];

// Posities per categorie — caps/beanies alleen voorkant
const CAT_POS={
  cap:     ['front'],
  baby:    ['front','back','both'],
  tas:     ['front','back','both'],
  default: POS_IDS,
};

function setupStep2(){
  const allowed = CAT_POS[S.cat] || CAT_POS.default;
  POS_IDS.forEach(pid=>{
    const el=e('pos-'+pid);
    if(!el) return;
    const ok=allowed.includes(pid);
    el.classList.toggle('hidden', !ok);
    el.classList.toggle('disabled', !ok);
  });

  // Als huidige positie niet meer toegestaan is, reset
  if(S.pos && !allowed.includes(S.pos)){
    S.pos=null; S.techA=null; S.techB=null;
    POS_IDS.forEach(x=>{const el=e('pos-'+x);if(el)el.classList.remove('sel');});
    e('btn2').disabled=true;
  }

  // Sectielabels tonen/verbergen
  // Als alleen voorkant: verberg "Combinatie" sectie
  const onlyFront = allowed.length===1 && allowed[0]==='front';
  const combiSection=document.querySelector('#step2 .sub-lbl:last-of-type');
  const combiGrid=document.querySelector('#step2 .opt-grid.g4:last-of-type');

  // Verberg combinatie rij als niet relevant
  const hasCombis = allowed.some(p=>POS_IS_DUAL[p]);
  const combiSections=document.querySelectorAll('#step2 .opt-grid.g4');
  if(combiSections.length>1){
    combiSections[1].classList.toggle('hidden', !hasCombis);
    const combiLabel=combiSections[1].previousElementSibling;
    if(combiLabel) combiLabel.classList.toggle('hidden', !hasCombis);
  }

  // Auto-select voorkant als dat de enige optie is
  if(onlyFront && !S.pos){
    selPos('front');
    e('btn2').disabled=false;
  }
}
// Welke posities hebben een "B" kant (= 2 technieken nodig)?
const POS_IS_DUAL={'both':true,'lborst-back':true,'rborst-back':true,'borst-both':true};
// Label per positie
function POS_LABEL_OBJ(){return {
  front:t('pos_front'), back:t('pos_back'),
  lborst:t('pos_lborst'), rborst:t('pos_rborst'),
  both:t('pos_front')+' + '+t('pos_back'),
  'lborst-back':t('pos_lborst')+' + '+t('pos_back'),
  'rborst-back':t('pos_rborst')+' + '+t('pos_back'),
  'borst-both':t('pos_lborst')+' + '+t('pos_rborst'),
};}
// Alias voor bestaande code
const POS_LABEL=new Proxy({},{get:(_,k)=>POS_LABEL_OBJ()[k]||k});

function selPos(p){
  S.pos=p; S.techA=null; S.techB=null; S.configuring='A';
  POS_IDS.forEach(x=>{const el=e('pos-'+x);if(el)el.classList.toggle('sel',x===p);});
  e('btn2').disabled=false;
  updatePosVisual(p);
}

function updatePosVisual(p){
  // Reset alle zones
  ['vis-front','vis-back','vis-lborst','vis-rborst'].forEach(id=>{
    const el=e(id); if(el) el.style.display='none';
  });
  // Toon juiste zones
  if(p==='front')       { showVis('vis-front'); }
  else if(p==='back')   { showVis('vis-back'); }
  else if(p==='lborst') { showVis('vis-lborst'); }
  else if(p==='rborst') { showVis('vis-rborst'); }
  else if(p==='both')   { showVis('vis-front'); showVis('vis-back'); }
  else if(p==='lborst-back') { showVis('vis-lborst'); showVis('vis-back'); }
  else if(p==='rborst-back') { showVis('vis-rborst'); showVis('vis-back'); }
  else if(p==='borst-both')  { showVis('vis-lborst'); showVis('vis-rborst'); }
}
function showVis(id){ const el=e(id); if(el) el.style.display='block'; }

// ── STAP 3: Techniek ───────────────────────────────────────────────────────────
function setupStep3ForKant(kant){
  S.configuring=kant;
  const dual=isDual();
  // Bepaal leesbare naam per kant
  const posLabel=POS_LABEL[S.pos]||S.pos;
  const parts=posLabel.split(' + ');
  const kantNameA=parts[0]||'Kant A';
  const kantNameB=parts[1]||'Kant B';
  const kantName=kant==='A'?kantNameA:kantNameB;
  const alreadyA=S.techA?`${kantNameA}: ${techName(S.techA)}`:'';

  e('kant-ind').classList.toggle('hidden',!dual);
  if(dual){
    e('kant-lbl-txt').textContent=kantName;
    e('kant-sub-txt').textContent=kant==='B'&&alreadyA?alreadyA:'';
  }
  e('s3-ttl').textContent=dual?`Techniek — ${kantName}`:'Kies een druktechniek';

  // Zeefdruk niet beschikbaar voor caps
  const isCap=S.cat==='cap';
  e('tc-zeef').classList.toggle('disabled',isCap);
  if(isCap){
    // Zet uitleg tekst onder de zeefdruk kaart
    const zeefDesc=e('tc-zeef').querySelector('.tc-desc');
    if(zeefDesc) zeefDesc.textContent='Niet beschikbaar voor caps. Kies DTF of borduren.';
    if(kant==='A'&&S.techA==='zeef') S.techA=null;
    if(kant==='B'&&S.techB==='zeef') S.techB=null;
  } else {
    const zeefDesc=e('tc-zeef').querySelector('.tc-desc');
    if(zeefDesc) zeefDesc.textContent='Traditionele techniek, levendig en duurzaam. Grote oplages.';
  }

  ['dtf','zeef'].forEach(x=>e('tc-'+x).classList.remove('sel'));
  ['ti-dtf','ti-zeef'].forEach(x=>e(x).classList.add('hidden'));
  e('btn3').disabled=true;
}

function selTech(t){
  if(S.configuring==='A') S.techA=t; else S.techB=t;
  ['dtf','zeef','bord'].forEach(x=>e('tc-'+x)?.classList.toggle('sel',x===t));
  ['ti-dtf','ti-zeef'].forEach(x=>e(x)?.classList.add('hidden'));
  if(t!=='bord') e('ti-'+t)?.classList.remove('hidden');
  e('btn3').disabled=false;
}

function techName(t){return t==='dtf'?'DTF druk':t==='zeef'?'Zeefdruk':t==='bord'?'Borduren':'–';}
function isDual(){return !!POS_IS_DUAL[S.pos];}
function isBorduren(){return S.techA==='bord'||(isDual()&&S.techB==='bord');}

function afterTech(){
  if(isDual()&&S.configuring==='A'){
    setupStep3ForKant('B');
  } else {
    gS(4);
  }
}

function goBackFromStep4(){
  if(isDual()){
    S.configuring='B';
    setupStep3ForKant('B');
    gS(3);
  } else {
    gS(3);
  }
}

// Override gS(3) entry point to always reset to kant A
const _origGS=gS;
function enterStep3(){
  S.techA=null;S.techB=null;S.configuring='A';
  setupStep3ForKant('A');
  gS(3);
}
// Patch btn2 onclick
e('btn2').onclick=()=>enterStep3();

// ── STAP 4: Maten ──────────────────────────────────────────────────────────────
function setupStep4(){
  const isBoth=isDual();
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
  const cat=CATS.find(c=>c.id===S.cat);
  const sizes=cat?.sizes||['XS','S','M','L','XL','2XL'];
  const container=e('sz-container');
  container.innerHTML='';
  // Responsief grid — werkt op desktop en mobiel
  const grid=document.createElement('div');
  grid.className='sz-grid';
  sizes.forEach(sz=>{
    const item=document.createElement('div');
    item.className='sz-grid-item';
    item.innerHTML=`<label>${sz}</label><input class="sz-inp" type="number" min="0" value="0" data-size="${sz}" oninput="updQ()" inputmode="numeric" pattern="[0-9]*">`;
    grid.appendChild(item);
  });
  container.appendChild(grid);
  S.qty=0;
}

function selZC(kant,n){
  if(kant==='front') S.zcA=n; else S.zcB=n;
  document.querySelectorAll('#zc-'+kant+' .zc-btn').forEach(b=>b.classList.toggle('sel',parseInt(b.dataset.n)===n));
  updQ();
}

function updQ(){
  let t=0;document.querySelectorAll('.sz-inp').forEach(i=>t+=Math.max(0,parseInt(i.value)||0));
  S.qty=t;calcQ();
}

function calcQ(){
  const q=S.qty;const w=e('qty-warn');w.classList.add('hidden');
  if(q===0){e('quote-box').style.display='none';e('btn4').disabled=true;return;}

  const isBoth=isDual();
  let upA=0,upB=0;

  // Drukprijs kant A (ex BTW) — borduren = 0 (op aanvraag)
  const tA=S.techA;
  if(tA==='dtf'){
    const t=DP.find(x=>q>=x.min&&q<=x.max);
    if(!t){e('quote-box').style.display='none';return;}upA=t.u;
  } else if(tA==='zeef'){
    if(q<25){w.textContent=t('err_zeef_min');w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
    const t=ZP.find(x=>q>=x.min&&q<=x.max);upA=t?t.c[S.zcA-1]:0.82;
  } else if(tA==='bord'){
    upA=0; // prijs op aanvraag
  }

  // Drukprijs kant B (ex BTW)
  if(isBoth){
    const tB=S.techB;
    if(tB==='dtf'){
      const t=DP.find(x=>q>=x.min&&q<=x.max);if(!t){e('quote-box').style.display='none';return;}upB=t.u;
    } else if(tB==='zeef'){
      if(q<25){w.textContent=t('err_zeef_min_b');w.classList.remove('hidden');e('quote-box').style.display='none';e('btn4').disabled=true;return;}
      const t=ZP.find(x=>q>=x.min&&q<=x.max);upB=t?t.c[S.zcB-1]:0.82;
    } else if(tB==='bord'){
      upB=0;
    }
  }

  // Textielprijzen ex BTW (incl. volumekorting)
  const textielEx = verkoopExBtw(S.mdl, q);
  const korting   = textielKorting(q);
  const kortingTxt = kortingLabel(q);

  // Verzending ex BTW
  const shipEx = q>=12 ? (13.95/1.21) : (6.95/1.21);

  // Totalen ex BTW
  const drukAEx   = upA * q;
  const drukBEx   = isBoth ? upB * q : 0;
  const textTotEx = textielEx * q;
  const totEx     = drukAEx + drukBEx + textTotEx + shipEx;
  const btwBedrag = totEx * BTW;
  const totIncl   = totEx + btwBedrag;

  // Sla op in state
  S.upA=upA; S.upB=upB; S.textielEx=textielEx; S.korting=korting;
  S.shipEx=shipEx; S.totEx=totEx; S.btwBedrag=btwBedrag; S.totIncl=totIncl;

  // Build quote breakdown (alles ex BTW)
  const posNm=POS_LABEL;
  let bdHtml='';
  bdHtml+=`<div class="qr"><span class="k">Stuks</span><span class="v">${q}</span></div>`;
  if(kortingTxt){
    bdHtml+=`<div class="qr" style="color:var(--success)"><span class="k">🎉 ${kortingTxt}</span><span class="v"></span></div>`;
  }
  bdHtml+=`<div class="qr"><span class="k">Textiel p/stuk</span><span class="v">${fmtEx(textielEx)}${korting?` <span style="color:var(--success);font-size:.7rem">(${korting*100}% korting)</span>`:''}</span></div>`;
  if(isBoth){
    bdHtml+=`<hr class="qdiv"><div class="q-kant-sep">Voorkant — ${techName(S.techA)}</div>`;
    bdHtml+=`<div class="qr"><span class="k">Druk p/stuk</span><span class="v">${S.techA==='bord'?'<span style="color:var(--accent2);font-weight:600">Op aanvraag</span>':fmtEx(upA)}</span></div>`;
    bdHtml+=`<hr class="qdiv"><div class="q-kant-sep">Achterkant — ${techName(S.techB)}</div>`;
    bdHtml+=`<div class="qr"><span class="k">Druk p/stuk</span><span class="v">${S.techB==='bord'?'<span style="color:var(--accent2);font-weight:600">Op aanvraag</span>':fmtEx(upB)}</span></div>`;
  } else {
    const drukVal=S.techA==='bord'?'<span style="color:var(--accent2);font-weight:600">Op aanvraag</span>':fmtEx(upA);
    bdHtml+=`<div class="qr"><span class="k">${techName(S.techA)} p/stuk</span><span class="v">${drukVal}</span></div>`;
  }
  bdHtml+=`<hr class="qdiv">`;
  bdHtml+=`<div class="qr"><span class="k">Subtotaal ex BTW</span><span class="v">${fmtEx(totEx)}</span></div>`;
  bdHtml+=`<div class="qr"><span class="k">BTW 21%</span><span class="v">${fmtEx(btwBedrag)}</span></div>`;
  bdHtml+=`<div class="qr"><span class="k">Verzending incl.</span><span class="v">${fmt(q>=12?13.95:6.95)}</span></div>`;

  e('q-total').textContent=fmt(totIncl);
  e('q-sub').textContent=fmtEx(totEx)+' ex BTW';
  e('q-bd').innerHTML=bdHtml;
  e('quote-box').style.display='flex';
  e('btn4').disabled=false;
}

// ── STAP 5: Ontwerp ────────────────────────────────────────────────────────────
function setupStep5(){
  const isBoth=isDual();
  const isBack=S.pos==='back';
  // Upload secties per positie tonen
  const pos=S.pos;
  e('upload-front-lbl').textContent='Logo / ontwerp voorkant';
  e('upload-front-wrap').classList.toggle('hidden', pos==='back'||pos==='lborst'||pos==='rborst'||pos==='lborst-back'||pos==='rborst-back');
  e('upload-back-wrap').classList.toggle('hidden',  !(pos==='back'||pos==='both'||pos==='lborst-back'||pos==='rborst-back'));
  e('upload-lborst-wrap').classList.toggle('hidden',!(pos==='lborst'||pos==='lborst-back'||pos==='borst-both'));
  e('upload-rborst-wrap').classList.toggle('hidden',!(pos==='rborst'||pos==='rborst-back'||pos==='borst-both'));
  // Voeg automatisch eerste upload slot toe als de sectie net zichtbaar wordt
  ['front','back','lborst','rborst'].forEach(side=>{
    const listEl=e('upload-'+side+'-list');
    if(listEl&&listEl.children.length===0){
      const wrap=e('upload-'+side+'-wrap');
      if(wrap&&!wrap.classList.contains('hidden')) addUploadSlot(side);
    }
  });
}

// Bewaar file references voor Cloudinary upload later
// ══════════════════════════════════════════════════════════════════════════════
// WINKELWAGEN
// ══════════════════════════════════════════════════════════════════════════════
const WAGEN = []; // Array van geconfigureerde producten

function voegToeAanWagen(){
  // Bouw regel object van huidige configuratie
  const sz = {};
  document.querySelectorAll('.sz-inp').forEach(inp=>{
    const v=parseInt(inp.value)||0;
    if(v>0) sz[inp.dataset.size]=v;
  });
  const dual = isDual();
  const regel = {
    id:        Date.now(),
    mdl:       {...S.mdl},
    cat:       S.cat,
    kleur:     S.clrName,
    kleurHex:  S.clrHex,
    kleurImg:  S.clrImg,
    pos:       S.pos,
    posLabel:  POS_LABEL[S.pos]||S.pos,
    techA:     S.techA,
    techB:     S.techB,
    dual:      dual,
    zcA:       S.zcA,
    zcB:       S.zcB,
    maten:     sz,
    qty:       S.qty,
    textielEx: S.textielEx,
    upA:       S.upA,
    upB:       S.upB,
    shipEx:    S.shipEx,
    totEx:     S.totEx,
    totIncl:   S.totIncl,
    uploads:   {
      front:  [...(UPLOADS.front||[])],
      back:   [...(UPLOADS.back||[])],
      lborst: [...(UPLOADS.lborst||[])],
      rborst: [...(UPLOADS.rborst||[])],
    },
    notes:     e('notes')?.value.trim()||'',
  };
  WAGEN.push(regel);
  updateCartUI();
  // Reset configuratie voor eventueel volgend product
  resetVoorNieuwProduct();
  // Toon winkelwagen en ga terug naar stap 1
  e('cart-bar').style.display='flex';
  toggleCartPanel(true);
  gS(1);
}

function resetVoorNieuwProduct(){
  Object.assign(S,{cat:null,mdl:null,clrId:null,clrName:null,clrHex:null,clrImg:null,
    pos:null,techA:null,techB:null,zcA:1,zcB:1,configuring:'A',
    qty:0,korting:0,upA:0,upB:0,textielEx:0,shipEx:0,totEx:0,btwBedrag:0,totIncl:0});
  UPLOADS.front=[];UPLOADS.back=[];UPLOADS.lborst=[];UPLOADS.rborst=[];
  ['front','back','lborst','rborst'].forEach(side=>{
    const listEl=e('upload-'+side+'-list');
    if(listEl) listEl.innerHTML='';
  });
  e('notes').value='';
  POS_IDS.forEach(x=>{const el=e('pos-'+x);if(el)el.classList.remove('sel');});
  if(e('btn2')) e('btn2').disabled=true;
  s1Show('cat');
}

function verwijderUitWagen(id){
  const idx=WAGEN.findIndex(r=>r.id===id);
  if(idx>=0) WAGEN.splice(idx,1);
  updateCartUI();
  if(WAGEN.length===0){
    e('cart-bar').style.display='none';
    e('cart-panel').style.display='none';
  }
}

function updateCartUI(){
  const count=WAGEN.length;
  const totaal=cartTotaalIncl();
  e('cart-count').textContent=count;
  e('cart-total-lbl').textContent=fmt(totaal);

  // Items
  const itemsEl=e('cart-items');
  itemsEl.innerHTML=WAGEN.map(r=>`
    <div class="cart-item">
      <div>
        <div class="cart-item-naam">${r.mdl.brand} ${r.mdl.name}</div>
        <div class="cart-item-detail">
          ${r.kleur} · ${r.posLabel} · ${techName(r.techA)}${r.dual?' + '+techName(r.techB):''}<br>
          ${Object.entries(r.maten).map(([m,n])=>m+': '+n).join(' | ')} · ${r.qty} stuks
        </div>
      </div>
      <div>
        <div class="cart-item-prijs">${fmt(r.totIncl)}</div>
        <span class="cart-item-del" onclick="verwijderUitWagen(${r.id})">✕ verwijderen</span>
      </div>
    </div>`).join('');

  // Totaalregel
  const shipTotaal=cartShip();
  const totEx=WAGEN.reduce((s,r)=>s+r.totEx,0)+shipTotaal;
  const btw=totEx*0.21;
  e('cart-totaalregel').innerHTML=
    `<div style="display:flex;justify-content:space-between;margin-bottom:4px;"><span>Subtotaal ex BTW</span><span>${fmtEx(WAGEN.reduce((s,r)=>s+r.totEx-r.shipEx,0))}</span></div>`+
    `<div style="display:flex;justify-content:space-between;margin-bottom:4px;"><span>Verzending</span><span>${fmtEx(shipTotaal)}</span></div>`+
    `<div style="display:flex;justify-content:space-between;margin-bottom:4px;"><span>BTW 21%</span><span>${fmtEx(btw)}</span></div>`+
    `<div style="display:flex;justify-content:space-between;font-weight:700;font-size:.95rem;"><span>Totaal incl. BTW</span><span style="color:var(--accent)">${fmt(totaal)}</span></div>`;
}

function cartTotaalIncl(){
  const totEx=WAGEN.reduce((s,r)=>s+r.totEx-r.shipEx,0)+cartShip();
  return totEx*1.21;
}

function cartShip(){
  // Verzending eenmalig op totaal aantal stuks
  const totalQty=WAGEN.reduce((s,r)=>s+r.qty,0);
  return totalQty>=12?13.95:6.95;
}

function toggleCartPanel(forceOpen=false){
  const panel=e('cart-panel');
  panel.style.display=(forceOpen||panel.style.display==='none')?'block':'none';
}

function nieuweRegel(){
  e('cart-panel').style.display='none';
  gS(1);
}

function naarAfrekenen(){
  if(WAGEN.length===0) return;
  e('cart-panel').style.display='none';
  gS(6);
  fillSumMulti();
}

function fillSumMulti(){
  // Overschrijf stap 6 samenvatting met multi-regel versie
  const sumCard=document.querySelector('#step6 .sum-card');
  if(!sumCard) return;
  const shipTotaal=cartShip();
  const totEx=WAGEN.reduce((s,r)=>s+r.totEx-r.shipEx,0)+shipTotaal;
  const btw=totEx*0.21;
  const totIncl=totEx*1.21;

  const regelsHtml=WAGEN.map((r,i)=>`
    <div style="padding:.75rem 0;border-bottom:1px solid var(--border);">
      <div style="font-size:.75rem;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Product ${i+1}</div>
      <div style="font-size:.9rem;font-weight:700;">${r.mdl.brand} ${r.mdl.name}</div>
      <div style="font-size:.78rem;color:var(--ink3);margin-top:2px;">${r.kleur} · ${r.posLabel} · ${techName(r.techA)}${r.dual?' + '+techName(r.techB):''}</div>
      <div style="font-size:.78rem;color:var(--ink3);">${Object.entries(r.maten).map(([m,n])=>m+': '+n).join(' | ')} · ${r.qty} stuks</div>
      <div style="font-size:.85rem;font-weight:700;margin-top:4px;">${fmt(r.totIncl)}</div>
    </div>`).join('');

  sumCard.innerHTML=`
    <div class="sum-hd">Jouw bestelling (${WAGEN.length} product${WAGEN.length>1?'en':''})</div>
    ${regelsHtml}
    <hr style="border:none;border-top:1px solid var(--border);margin:.75rem 0;">
    <div class="sum-row"><span class="k">Subtotaal ex BTW</span><span class="v">${fmtEx(WAGEN.reduce((s,r)=>s+r.totEx-r.shipEx,0))}</span></div>
    <div class="sum-row"><span class="k">Verzending</span><span class="v">${fmtEx(shipTotaal)}</span></div>
    <div class="sum-row"><span class="k">BTW 21%</span><span class="v">${fmtEx(btw)}</span></div>
    <div class="sum-total"><span class="lbl">Totaal incl. BTW</span><span class="prc">${fmt(totIncl)}</span></div>`;

  // Update PayPal bedrag
  S.totIncl=totIncl; S.totEx=totEx; S.btwBedrag=btw;
  // Levertijd dynamisch op basis van techniek
  const heeftBorduur=WAGEN.some(r=>r.techA==='bord'||r.techB==='bord');
  const heeftZeef=WAGEN.some(r=>r.techA==='zeef'||r.techB==='zeef');
  const ltEl=e('levertijd-tekst');
  if(ltEl){
    if(heeftBorduur) ltEl.textContent='7–12 werkdagen';
    else if(heeftZeef) ltEl.textContent='6–10 werkdagen';
    else ltEl.textContent='5–8 werkdagen';
  }
  initPP();
}

// ══════════════════════════════════════════════════════════════════════════════
// UPLOADS: per positie een array van bestanden
const UPLOADS={front:[], back:[], lborst:[], rborst:[], bord:[]};
let uploadCounter=0;

function addUploadSlot(side){
  const listId='upload-'+side+'-list';
  const listEl=e(listId); if(!listEl) return;
  const uid='upl-'+side+'-'+(++uploadCounter);
  const wrap=document.createElement('div');
  wrap.id=uid+'-wrap';
  wrap.style.cssText='margin-bottom:.75rem;';

  wrap.innerHTML=`
    <div class="upload-area" id="${uid}-area" onclick="document.getElementById('${uid}').click()">
      <div class="upload-icon-wrap">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <div class="upload-text" style="font-size:.8rem;"><strong>${t('upload_klik')}</strong></div>
      <div class="upload-text" style="font-size:.7rem;margin-top:2px;">${t('upload_of')}</div>
      <div style="font-size:.65rem;color:var(--ink3);margin-top:4px;">AI, EPS, PDF, PNG, SVG – max. 50 MB</div>
    </div>
    <input type="file" id="${uid}" accept=".ai,.eps,.pdf,.png,.svg,.jpg,.jpeg" style="display:none"
           onchange="handleUpload('${side}',this,'${uid}')">`;
  listEl.appendChild(wrap);

  // Drag-drop events
  const area=wrap.querySelector('.upload-area');
  area.addEventListener('dragover',ev=>{ev.preventDefault();area.classList.add('dragging');});
  area.addEventListener('dragleave',ev=>{if(!area.contains(ev.relatedTarget))area.classList.remove('dragging');});
  area.addEventListener('drop',ev=>{
    ev.preventDefault();area.classList.remove('dragging');
    const f=ev.dataTransfer.files[0];if(!f)return;
    handleUpload(side,{files:[f]},uid);
  });
}

function handleUpload(side, inp, uid){
  if(!inp.files||!inp.files[0]) return;
  const f=inp.files[0];
  if(!UPLOADS[side]) UPLOADS[side]=[];
  const existing=UPLOADS[side].find(u=>u.uid===uid);
  if(existing) existing.file=f;
  else UPLOADS[side].push({uid, file:f});

  const area=e(uid+'-area');
  if(!area) return;

  const isImg=/\.(png|jpg|jpeg|gif|webp|svg)$/i.test(f.name);
  const sizeStr=f.size>1048576?(f.size/1048576).toFixed(1)+' MB':Math.round(f.size/1024)+' KB';

  if(isImg){
    // Toon preview
    const reader=new FileReader();
    reader.onload=ev=>{
      area.classList.add('has-file');
      area.onclick=null;
      area.innerHTML=`
        <div class="upload-preview-wrap">
          <img class="upload-preview" src="${ev.target.result}" alt="${f.name}">
          <div class="upload-preview-overlay">
            <button class="upload-overlay-btn rep" onclick="replaceUpload('${uid}','${side}')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              Vervangen
            </button>
            <button class="upload-overlay-btn del" onclick="removeUploadSlot('${uid}',null,'${side}')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
              Verwijder
            </button>
          </div>
        </div>
        <div class="upload-file-info">
          <span class="upload-file-naam">✓ ${f.name}</span>
          <span class="upload-file-size">${sizeStr}</span>
        </div>`;
    };
    reader.readAsDataURL(f);
  } else {
    // Niet-afbeelding: toon bestandsnaam met icoon
    area.classList.add('has-file');
    area.onclick=null;
    area.style.padding='.75rem';
    area.innerHTML=`
      <div style="display:flex;align-items:center;gap:.65rem;">
        <div style="width:36px;height:36px;background:rgba(26,122,69,.1);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div style="flex:1;overflow:hidden;">
          <div style="font-size:.78rem;font-weight:700;color:var(--success);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">✓ ${f.name}</div>
          <div style="font-size:.68rem;color:var(--ink3);">${sizeStr}</div>
        </div>
        <button onclick="removeUploadSlot('${uid}',null,'${side}')"
                style="background:none;border:none;cursor:pointer;color:var(--ink3);font-size:1rem;padding:.2rem;flex-shrink:0;" title="Verwijder">✕</button>
      </div>`;
  }
}

function replaceUpload(uid, side){
  // Reset slot naar lege staat en open bestandskiezer
  const area=e(uid+'-area');
  if(area){
    area.classList.remove('has-file');
    area.style.padding='';
    area.innerHTML=`
      <div class="upload-icon-wrap">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <div class="upload-text" style="font-size:.8rem;"><strong>${t('upload_klik')}</strong></div>
      <div class="upload-text" style="font-size:.7rem;margin-top:2px;">${t('upload_of')}</div>
      <div style="font-size:.65rem;color:var(--ink3);margin-top:4px;">AI, EPS, PDF, PNG, SVG – max. 50 MB</div>`;
    area.onclick=()=>document.getElementById(uid)?.click();
    const inp=document.getElementById(uid); if(inp){inp.value='';inp.click();}
    UPLOADS[side]=(UPLOADS[side]||[]).filter(u=>u.uid!==uid);
  }
}

function removeUploadSlot(uid, btn, side){
  UPLOADS[side]=(UPLOADS[side]||[]).filter(u=>u.uid!==uid);
  const wrap=e(uid+'-wrap');
  if(wrap){
    wrap.style.transition='opacity .2s,transform .2s';
    wrap.style.opacity='0'; wrap.style.transform='scale(.95)';
    setTimeout(()=>wrap.remove(), 200);
  }
}

function toggleBizFields(){
  const hasCompany=e('company').value.trim().length>0;
  e('biz-fields').classList.toggle('hidden',!hasCompany);
  if(!hasCompany){e('kvk').value='';e('btwnr').value='';}
}

function chk5(){
  const ok=['fname','lname','email','street','zip','city'].every(id=>e(id).value.trim().length>0);
  const zipValid=validatePostcode();
  const geldig=ok&&zipValid;
  e('btn5').disabled=!geldig;
  const btnSave=e('btn-offerte-opslaan');
  if(btnSave) btnSave.disabled=!geldig;
}

function validatePostcode(){
  const zip=e('zip').value.trim();
  const country=e('country').value;
  const warn=e('zip-warn');
  if(!zip){if(warn)warn.classList.add('hidden');return false;}
  const patterns={
    NL:/^\d{4}\s?[A-Za-z]{2}$/,  // met of zonder spatie: 1234AB of 1234 AB
    BE:/^\d{4}$/,
    DE:/^\d{5}$/,
  };
  const pat=patterns[country];
  if(!pat){if(warn)warn.classList.add('hidden');return true;}
  const valid=pat.test(zip);
  if(warn){
    if(valid){warn.classList.add('hidden');}
    else{
      const examples={NL:'bijv. 1234 AB',BE:'bijv. 2000',DE:'bijv. 10115'};
      const pcErrKey='err_postcode_'+(country||'other').toLowerCase();
      warn.textContent=t(pcErrKey)||t('err_postcode_other');
      warn.classList.remove('hidden');
    }
  }
  return valid;
}

// ── STAP 6: Samenvatting ───────────────────────────────────────────────────────
function fillSum(){
  const cat=CATS.find(c=>c.id===S.cat);
  const isBoth=isDual();
  const posNm=POS_LABEL;
  const kantNmA=S.pos==='back'?'Achterkant':'Voorkant';

  e('s-textiel').textContent=(S.mdl?S.mdl.brand+' '+S.mdl.name:cat?catNaam(cat.id):'–');
  e('s-kleur').textContent=S.clrName||'–';
  e('s-positie').textContent=posNm[S.pos]||'–';
  e('s-qty').textContent=S.qty+' stuks';
  const sz={};document.querySelectorAll('.sz-inp').forEach(i=>{if(parseInt(i.value)>0)sz[i.dataset.size]=i.value;});
  e('s-maten').textContent=Object.entries(sz).map(([k,v])=>k+': '+v).join(', ')||'–';

  // Kant A
  e('sum-sep-a').textContent=isBoth?kantNmA:'Bedrukking';
  e('s-tech-a').textContent=techName(S.techA);
  if(S.techA==='zeef'){e('sum-kleur-a-row').classList.remove('hidden');e('s-kleur-a').textContent=S.zcA+' kleur'+(S.zcA>1?'en':'');}
  else e('sum-kleur-a-row').classList.add('hidden');
  e('s-up-a').textContent=S.techA==='bord'?'Op aanvraag':fmtEx(S.upA)+' ex BTW per stuk';

  // Kant B
  ['sum-sep-b','sum-tech-b-row','sum-up-b-row'].forEach(id=>e(id).classList.toggle('hidden',!isBoth));
  if(isBoth){
    e('s-tech-b').textContent=techName(S.techB);
    if(S.techB==='zeef'){e('sum-kleur-b-row').classList.remove('hidden');e('s-kleur-b').textContent=S.zcB+' kleur'+(S.zcB>1?'en':'');}
    else e('sum-kleur-b-row').classList.add('hidden');
    e('s-up-b').textContent=S.techB==='bord'?'Op aanvraag':fmtEx(S.upB)+' ex BTW per stuk';
  }

  const drukTotEx=isBoth?(S.upA+S.upB)*S.qty:S.upA*S.qty;
  const textTotEx=S.textielEx*S.qty;
  const kortingStr = S.korting>0 ? ` (incl. ${S.korting*100}% korting)` : '';
  e('s-textiel-prijs').textContent=fmtEx(S.textielEx)+' ex BTW'+kortingStr;
  const hasBord=isBorduren();
  if(hasBord){
    e('s-druk-label').textContent='Borduren';
    e('s-druk').innerHTML='<span style="color:var(--accent);font-weight:700">Op aanvraag</span>';
    e('s-bord-note').classList.remove('hidden');
  } else {
    e('s-druk-label').textContent='Bedrukking totaal';
    e('s-druk').textContent=fmtEx(drukTotEx);
    e('s-bord-note').classList.add('hidden');
  }
  e('s-textiel-tot').textContent=fmtEx(textTotEx);
  e('s-ship').textContent=fmt(S.qty>=12?13.95:6.95)+(S.qty>=12?' (12+ stuks)':' (1–11 stuks)');
  e('s-totex').textContent=hasBord ? fmtEx(S.totEx)+' (excl. borduurkosten)' : fmtEx(S.totEx);
  e('s-btw').textContent=fmtEx(S.btwBedrag);
  e('s-total').textContent=hasBord ? fmt(S.totIncl)+' *' : fmt(S.totIncl);
  // Voetnoot bij totaal als borduren
  const totalNote=e('s-total-note');
  if(totalNote) totalNote.classList.toggle('hidden',!hasBord);

  e('s-naam').textContent=e('fname').value.trim()+' '+e('lname').value.trim();
  e('s-adres').textContent=e('street').value.trim()+', '+e('zip').value.trim()+' '+e('city').value.trim();
  e('s-email-sum').textContent=e('email').value.trim();

  // Toon juiste blok: betalen of borduur aanvraag
  e('bord-aanvraag-block').classList.toggle('hidden',!hasBord);
  e('betaal-block').classList.toggle('hidden',hasBord);
  if(!hasBord) initPP();
}

// ── PayPal ─────────────────────────────────────────────────────────────────────
function initPP(){
  const c=e('pp-container');c.innerHTML='';
  if(typeof paypal==='undefined')return;
  e('fallback-pay').style.display='none'; // PayPal geladen, verberg fallback
  paypal.Buttons({
    style:{layout:'vertical',color:'blue',shape:'rect',label:'pay',height:46},
    createOrder:(d,a)=>a.order.create({
      purchase_units:[{
        description:'Merch Master – '+S.qty+' stuks',
        amount:{currency_code:'EUR',value:S.totIncl.toFixed(2),
          breakdown:{item_total:{currency_code:'EUR',value:(S.totEx).toFixed(2)},
            shipping:{currency_code:'EUR',value:'0.00'},
            tax_total:{currency_code:'EUR',value:S.btwBedrag.toFixed(2)}}}
      }],
      application_context:{shipping_preference:'NO_SHIPPING'}
    }),
    onApprove:(d,a)=>a.order.capture().then(details=>sendOrderEmail(details)).catch(err=>{
      toonBetaalFout('Betaling kon niet worden verwerkt. Probeer opnieuw of neem contact op via WhatsApp.');
      console.error('Capture fout:',err);
    }),
    onCancel:()=>{ toonBetaalFout('Betaling geannuleerd. Je kunt het opnieuw proberen wanneer je wilt.'); },
    onError:err=>{ toonBetaalFout('Er is iets misgegaan met PayPal. Probeer opnieuw of betaal via de knop hieronder.'); console.error(err); }
  }).render('#pp-container');
}

function toonBetaalFout(msg){
  // Verwijder bestaande foutmelding
  const oud=e('betaal-fout-msg');
  if(oud) oud.remove();
  // Maak nieuwe aan boven de PayPal knop
  const div=document.createElement('div');
  div.id='betaal-fout-msg';
  div.style.cssText='background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:.75rem 1rem;margin-bottom:.75rem;font-size:.83rem;color:#991b1b;display:flex;align-items:flex-start;gap:.5rem;';
  div.innerHTML=`<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>${msg} <a href="https://wa.me/31617255170" style="color:#991b1b;font-weight:700;">WhatsApp →</a></span>`;
  const ppContainer=e('pp-container');
  if(ppContainer) ppContainer.parentNode.insertBefore(div, ppContainer);
  // Scroll naar fout
  div.scrollIntoView({behavior:'smooth',block:'center'});
}

function simPay(){
  const btn=e('sim-pay-btn');
  if(btn){btn.innerHTML='<span class="spinner"></span> Bezig...';btn.disabled=true;}
  sendOrderEmail({id:'SIM-'+Date.now()}).finally(()=>{
    if(btn){btn.innerHTML='🧪 Simuleer betaling (alleen voor testen)';btn.disabled=false;}
  });
}

// ── EmailJS: order email ────────────────────────────────────────────────────────
async function sendOrderEmail(paypalDetails){
  // Als WAGEN leeg is (enkelvoudige bestelling), voeg huidige config toe
  if(WAGEN.length===0) voegToeAanWagen();
  const isBoth=isDual();
  const posNm=POS_LABEL;
  const sz={};document.querySelectorAll('.sz-inp').forEach(i=>{if(parseInt(i.value)>0)sz[i.dataset.size]=i.value;});

  // Toon uploadstatus aan gebruiker (alleen als knop zichtbaar is)
  const fpBtn=e('fallback-pay');
  if(fpBtn&&fpBtn.style.display!=='none'){fpBtn.innerHTML='<span class="spinner"></span> Logo uploaden...';fpBtn.disabled=true;}

  // Upload logo's naar Cloudinary
  const orderId=paypalDetails.id||('ORD-'+Date.now());
  const folder='merch-master/bestellingen/'+orderId;
  const pos=S.pos;
  const dual=isDual();
  // Upload alle bestanden per positie en sla URL op in het UPLOADS object
  async function uploadAll(files, folder){
    if(!files||files.length===0) return [];
    return Promise.all(files.map(async u=>{
      const url=await uploadToCloudinary(u.file, folder);
      u._url=url; // bewaar voor WAGEN referentie
      return url;
    }));
  }
  const [urlFront, urlBack, urlLborst, urlRborst] = await Promise.all([
    uploadAll(UPLOADS.front,  folder),
    uploadAll(UPLOADS.back,   folder),
    uploadAll(UPLOADS.lborst, folder),
    uploadAll(UPLOADS.rborst, folder),
  ]);

  // Bouw logo overzicht per positie
  function fmtUploads(urls, side){
    if(!urls||urls.length===0) return 'Niet geüpload';
    return urls.map((u,i)=>u?`⬇ Logo ${i+1}: ${u}`:`Upload mislukt`).join(' | ');
  }
  const logoFrontTxt   = fmtUploads(urlFront,   'voorkant');
  const logoBackTxt    = fmtUploads(urlBack,     'achterkant');
  const logoLborstTxt  = fmtUploads(urlLborst,   'linkerborst');
  const logoRborstTxt  = fmtUploads(urlRborst,   'rechterborst');

  const params={
    order_id:         orderId,
    naam:             e('fname').value.trim()+' '+e('lname').value.trim(),
    email:            e('email').value.trim(),
    telefoon:         e('phone').value.trim()||'–',
    bedrijf:          e('company').value.trim()||'–',
    kvk:              e('kvk').value.trim()||'–',
    btwnr:            e('btwnr').value.trim()||'–',
    adres:            e('street').value.trim()+', '+e('zip').value.trim()+' '+e('city').value.trim()+', '+e('country').value,
    textiel:          (S.mdl?S.mdl.brand+' '+S.mdl.name:'–')+' | SKU: '+(S.mdl?.sku||'–'),
    textiel_segment:  TIER_LABEL[S.mdl?.tier||'standaard'],
    textiel_prijs:    fmtEx(S.textielEx)+' ex BTW p/stuk'+(S.korting>0?' ('+S.korting*100+'% volumekorting)':''),
    kleur:            S.clrName||'–',
    positie:          posNm[S.pos]||'–',
    techniek_a:       techName(S.techA)+(S.techA==='zeef'?' ('+S.zcA+' kleuren)':''),
    techniek_b:       isBoth?(techName(S.techB)+(S.techB==='zeef'?' ('+S.zcB+' kleuren)':'')):'-',
    maten:            Object.entries(sz).map(([k,v])=>k+': '+v).join(' | ')||'–',
    totaal_stuks:     S.qty+' stuks',
    prijs_per_stuk_a: fmtEx(S.upA)+' ex BTW',
    prijs_per_stuk_b: isBoth?fmtEx(S.upB)+' ex BTW':'-',
    totaal_ex_btw:    fmtEx(S.totEx),
    btw_bedrag:       fmtEx(S.btwBedrag),
    verzending:       fmt(S.qty>=12?13.95:6.95)+(S.qty>=12?' (12+ stuks)':' (1–11 stuks)'),
    totaal:           fmt(S.totIncl)+' incl. BTW',
    opmerkingen:      e('notes').value.trim()||'–',
    logo_voorkant:    logoFrontTxt,
    logo_achterkant:  logoBackTxt,
    logo_linkerborst: logoLborstTxt||'–',
    logo_rechterborst:logoRborstTxt||'–',
    bcc_email:        e('email').value.trim(), // klant ontvangt bevestiging via BCC
  };

  try {
    await fetch(HANDLER_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        action:       'bestelling',
        taal:         TAAL,
        order_id:     params.order_id,
        naam:         params.naam,
        email:        params.email,
        telefoon:     params.telefoon,
        bedrijf:      params.bedrijf,
        kvk:          params.kvk,
        btwnr:        params.btwnr,
        adres:        params.adres,
        opmerkingen:  params.opmerkingen,
        totaal_incl:  cartTotaalIncl(),
        totaal_ex:    WAGEN.reduce((s,r)=>s+r.totEx-r.shipEx,0)+cartShip(),
        btw:          (WAGEN.reduce((s,r)=>s+r.totEx-r.shipEx,0)+cartShip())*0.21,
        verzending_ex:cartShip(),
        regels: WAGEN.map(r=>({
          sku:        r.mdl.sku,
          naam:       r.mdl.name,
          merk:       r.mdl.brand,
          kleur_naam: r.kleur,
          kleur_code: r.kleurHex,
          positie:    r.posLabel,
          techniek_a: techName(r.techA),
          techniek_b: r.dual?techName(r.techB):null,
          is_dual:    r.dual,
          maten:      r.maten,
          aantal:     r.qty,
          prijs_ex:   r.textielEx,
          druk_ex:    r.upA+(r.dual?r.upB:0),
          regel_ex:   r.totIncl,
          uploads:{
            front:  (r.uploads.front||[]).map(u=>u._url||null).filter(Boolean),
            back:   (r.uploads.back||[]).map(u=>u._url||null).filter(Boolean),
            lborst: (r.uploads.lborst||[]).map(u=>u._url||null).filter(Boolean),
            rborst: (r.uploads.rborst||[]).map(u=>u._url||null).filter(Boolean),
          },
          opmerkingen: r.notes,
        })),
      })
    });
  } catch(err){console.warn('Handler fout:',err);}

  const emailVal=e('email')?.value.trim()||'';
  const naamVal=(e('fname')?.value.trim()||'')+' '+(e('lname')?.value.trim()||'');
  if(e('confirm-email')) e('confirm-email').textContent=emailVal;
  if(e('ok-ordernr')) e('ok-ordernr').textContent=orderId;
  // WhatsApp deeplink met ordernummer vooringevuld
  const waLink=e('ok-whatsapp');
  if(waLink){
    const waMsg=encodeURIComponent('Hallo, ik heb zojuist bestelling #'+orderId+' geplaatst. Ik heb een vraag over mijn order.');
    waLink.href='https://wa.me/31617255170?text='+waMsg;
  }
  WAGEN.length=0; e('cart-bar').style.display='none'; e('cart-panel').style.display='none';
  gS('success');
}

// ── Borduren aanvraag via flow ────────────────────────────────────────────────
async function sendBordAanvraagViaFlow(){
  const btn=e('btn-bord-aanvraag');
  btn.innerHTML='<span class="spinner"></span> Versturen...';
  btn.disabled=true;

  const sz={};document.querySelectorAll('.sz-inp').forEach(i=>{if(parseInt(i.value)>0)sz[i.dataset.size]=i.value;});
  const isBoth=isDual();

  // Upload logo via Cloudinary
  const aanvraagId='BORD-FLOW-'+Date.now();
  const url = await uploadToCloudinary(UPLOADS.front, 'merch-master/borduren/'+aanvraagId);
  const logoTxt = url ? '⬇ Download logo: '+url : (UPLOADS.front ? 'Upload mislukt — bestandsnaam: '+UPLOADS.front.name : 'Geen logo geüpload');

  const params={
    naam:        e('fname').value.trim()+' '+e('lname').value.trim(),
    email:       e('email').value.trim(),
    telefoon:    e('phone').value.trim()||'–',
    bedrijf:     e('company').value.trim()||'–',
    kvk:         e('kvk').value.trim()||'–',
    btwnr:       e('btwnr').value.trim()||'–',
    opmerkingen: e('notes').value.trim()||'–',
    logo:        logoTxt,
    // Extra bestelinfo meesturen
    textiel:     (S.mdl?S.mdl.brand+' '+S.mdl.name:'–')+' | SKU: '+(S.mdl?.sku||'–'),
    kleur:       S.clrName||'–',
    positie:     {front:'Voorkant',back:'Achterkant',both:'Beide kanten'}[S.pos]||'–',
    maten:       Object.entries(sz).map(([k,v])=>k+': '+v).join(' | ')||'–',
    totaal_stuks: S.qty+' stuks',
  };

  try {
    await fetch(HANDLER_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({action: 'borduur', ...params})
    });
  } catch(err){console.warn('Handler borduur fout:',err);}

  e('confirm-email').textContent=e('email').value.trim();
  e('ok-title').textContent='Borduurwens ontvangen! 🧵';
  e('ok-msg').innerHTML=`Bedankt! We sturen binnen 1–2 werkdagen een offerte naar <strong>${e('email').value.trim()}</strong>.<br><br>Vragen? WhatsApp: <a href="https://wa.me/31617255170" style="color:var(--accent)">+31 6 17 25 51 70</a>`;
  gS('success');
}

// ── Borduren aanvraag ──────────────────────────────────────────────────────────
function chkBord(){
  const ok=['bfname','blname','bemail'].every(id=>e(id).value.trim().length>0);
  e('btn-bord').disabled=!ok;
}

async function sendBordAanvraag(){
  const btn=e('btn-bord');
  btn.innerHTML='<span class="spinner"></span> Logo uploaden...';
  btn.disabled=true;

  // Upload logo naar Cloudinary
  const aanvraagId='BORD-'+Date.now();
  const url = await uploadToCloudinary(UPLOADS.bord, 'merch-master/borduren/'+aanvraagId);
  const logoTxt = url
    ? '⬇ Download logo: '+url
    : (UPLOADS.bord ? 'Upload mislukt — bestandsnaam: '+UPLOADS.bord.name : 'Geen logo geüpload');

  const params={
    naam:        e('bfname').value.trim()+' '+e('blname').value.trim(),
    email:       e('bemail').value.trim(),
    telefoon:    e('bphone').value.trim()||'–',
    bedrijf:     e('bcompany').value.trim()||'–',
    opmerkingen: e('bord-notes').value.trim()||'–',
    logo:        logoTxt,
  };

  try {
    await fetch(HANDLER_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({action: 'borduur', ...params})
    });
  } catch(err){console.warn('Handler borduur fout:',err);}

  e('confirm-email').textContent=e('bemail').value.trim();
  e('ok-title').textContent='Aanvraag ontvangen! 🧵';
  e('ok-msg').innerHTML=`Bedankt voor je borduurwens. We nemen binnen 1–2 werkdagen contact op via <strong>${e('bemail').value.trim()}</strong> met een offerte.<br><br>Vragen? WhatsApp: <a href="https://wa.me/31617255170" style="color:var(--accent)">+31 6 17 25 51 70</a>`;
  gS('success');
}

// ── Offerte opslaan ──────────────────────────────────────────────────────────
function slaOfferteOp(){
  const modal=e('offerte-modal');
  if(modal){ modal.style.display='flex'; }
  const naamInp=e('offerte-naam');
  if(naamInp){
    // Suggereer naam op basis van configuratie
    const mdlNaam=S.mdl?S.mdl.brand+' '+S.mdl.name:'';
    const qty=S.qty||0;
    naamInp.value=mdlNaam+(qty?' ('+qty+' stuks)':'');
    naamInp.select();
  }
  const foutEl=e('offerte-modal-fout');
  if(foutEl) foutEl.style.display='none';
}

function sluitOfferteModal(){
  const modal=e('offerte-modal');
  if(modal) modal.style.display='none';
}

async function bevestigOfferteOpslaan(){
  const naam=(e('offerte-naam')?.value.trim())||'Mijn offerte';
  const foutEl=e('offerte-modal-fout');

  // Bouw configuratie object
  const sz={};
  document.querySelectorAll('.sz-inp').forEach(i=>{
    if(parseInt(i.value)>0) sz[i.dataset.size]=i.value;
  });
  const config={
    cat:S.cat, mdlSku:S.mdl?.sku, mdlNaam:S.mdl?.name, mdlMerk:S.mdl?.brand,
    kleurId:S.clrId, kleurNaam:S.clrName, kleurHex:S.clrHex,
    pos:S.pos, techA:S.techA, techB:S.techB, zcA:S.zcA, zcB:S.zcB,
    maten:sz, qty:S.qty,
    fname:e('fname')?.value.trim(), lname:e('lname')?.value.trim(),
    email:e('email')?.value.trim(), telefoon:e('phone')?.value.trim(),
    bedrijf:e('company')?.value.trim(), adres:e('street')?.value.trim(),
    postcode:e('zip')?.value.trim(), stad:e('city')?.value.trim(),
    land:e('country')?.value,
  };

  try {
    const res=await fetch(HANDLER_URL,{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({action:'concept-opslaan', naam, configuratie:config})
    });
    const data=await res.json();
    if(data.ok){
      sluitOfferteModal();
      // Toon bevestiging
      const bevestig=document.createElement('div');
      bevestig.style.cssText='position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);background:#1a7a45;color:#fff;padding:.75rem 1.25rem;border-radius:8px;font-size:.85rem;font-weight:700;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,.2);';
      bevestig.textContent=t('offerte_opgeslagen');
      document.body.appendChild(bevestig);
      setTimeout(()=>bevestig.remove(), 3500);
    } else if(data.fout==='Niet ingelogd'||data.fout==='Sessie verlopen'){
      // Niet ingelogd — stuur naar portaal
      if(foutEl){
        foutEl.innerHTML='Je bent niet ingelogd. <a href="/bestellen/portaal" style="color:#991b1b;font-weight:700;">Log in via het portaal →</a>';
        foutEl.style.display='block';
      }
    } else {
      if(foutEl){ foutEl.textContent=data.fout||'Opslaan mislukt'; foutEl.style.display='block'; }
    }
  } catch(err){
    if(foutEl){ foutEl.textContent='Verbindingsfout — probeer opnieuw'; foutEl.style.display='block'; }
  }
}

// Sluit modal bij klik buiten
document.addEventListener('click', ev=>{
  const modal=e('offerte-modal');
  if(modal&&modal.style.display==='flex'&&ev.target===modal) sluitOfferteModal();
});

// ── Reset ──────────────────────────────────────────────────────────────────────
function doReset(){
  Object.assign(S,{cat:null,mdl:null,clrId:null,clrName:null,clrHex:null,clrImg:null,pos:null,techA:null,techB:null,zcA:1,zcB:1,configuring:'A',qty:0,korting:0,upA:0,upB:0,textielEx:0,shipEx:0,totEx:0,btwBedrag:0,totIncl:0});
  ['fname','lname','email','phone','company','kvk','btwnr','street','zip','city','notes',
   'bfname','blname','bemail','bphone','bcompany','bord-notes'].forEach(id=>{const el=e(id);if(el)el.value='';});
  e('biz-fields').classList.add('hidden');
  // Reset upload lijsten
  ['front','back','lborst','rborst'].forEach(side=>{
    const listEl=e('upload-'+side+'-list');
    if(listEl) listEl.innerHTML='';
  });
  const bordEl=e('upload-bord');
  if(bordEl) bordEl.classList.remove('has-file');
  const bordNm=e('upload-bord-name');
  if(bordNm) bordNm.textContent='';
  const fileBord=e('file-bord');
  if(fileBord) fileBord.value='';
  UPLOADS.front=[];UPLOADS.back=[];UPLOADS.lborst=[];UPLOADS.rborst=[];UPLOADS.bord=[];
  WAGEN.length=0;
  e('cart-bar').style.display='none';
  e('cart-panel').style.display='none';
  e('btn2').disabled=true;
  ['front','back','both'].forEach(x=>e('pos-'+x).classList.remove('sel'));
  buildCatGrid();s1Show('cat');gS(1);
}

// ── Init ───────────────────────────────────────────────────────────────────────
setTaal(localStorage.getItem('mm_taal')||'nl');
buildCatGrid();

// Borduur upload: drag & drop + preview
(function setupBordUpload(){
  const area = e('upload-bord');
  const inp  = e('file-bord');
  if(!area || !inp) return;

  area.addEventListener('dragover', ev => { ev.preventDefault(); area.classList.add('dragging'); });
  area.addEventListener('dragleave', ev => { if(!area.contains(ev.relatedTarget)) area.classList.remove('dragging'); });
  area.addEventListener('drop', ev => {
    ev.preventDefault(); area.classList.remove('dragging');
    const f = ev.dataTransfer.files[0]; if(!f) return;
    // Simuleer file input change
    const dt = new DataTransfer(); dt.items.add(f);
    inp.files = dt.files;
    inp.dispatchEvent(new Event('change'));
  });

  // Override handleUpload voor borduur om ook preview te tonen
  inp.addEventListener('change', function(){
    const f = this.files[0]; if(!f) return;
    const isImg = /\.(png|jpg|jpeg|gif|webp|svg)$/i.test(f.name);
    const sizeStr = f.size>1048576?(f.size/1048576).toFixed(1)+' MB':Math.round(f.size/1024)+' KB';
    UPLOADS.bord = [{uid:'bord-main', file:f}];
    area.classList.add('has-file');
    area.onclick = null;
    if(isImg){
      const reader = new FileReader();
      reader.onload = ev => {
        area.innerHTML = `
          <div class="upload-preview-wrap">
            <img class="upload-preview" src="${ev.target.result}" alt="${f.name}" style="height:100px;">
            <div class="upload-preview-overlay">
              <button class="upload-overlay-btn rep" onclick="resetBordUpload()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Vervangen
              </button>
            </div>
          </div>
          <div class="upload-file-info">
            <span class="upload-file-naam">✓ ${f.name}</span>
            <span class="upload-file-size">${sizeStr}</span>
          </div>`;
      };
      reader.readAsDataURL(f);
    } else {
      area.style.padding = '.75rem';
      area.innerHTML = `
        <div style="display:flex;align-items:center;gap:.65rem;">
          <div style="width:36px;height:36px;background:rgba(26,122,69,.1);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div style="flex:1;overflow:hidden;">
            <div style="font-size:.78rem;font-weight:700;color:var(--success);">✓ ${f.name}</div>
            <div style="font-size:.68rem;color:var(--ink3);">${sizeStr}</div>
          </div>
          <button onclick="resetBordUpload()" style="background:none;border:none;cursor:pointer;color:var(--ink3);font-size:1rem;padding:.2rem;">✕</button>
        </div>`;
    }
  });
})();

function resetBordUpload(){
  const area = e('upload-bord');
  const inp  = e('file-bord');
  if(!area) return;
  UPLOADS.bord = [];
  area.classList.remove('has-file');
  area.style.padding = '';
  area.onclick = () => inp?.click();
  area.innerHTML = `
    <div class="upload-icon-wrap">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </div>
    <div class="upload-text"><strong>Klik om te uploaden</strong> of sleep hier naartoe</div>
    <div class="upload-text" style="margin-top:3px;font-size:.7rem;">AI, EPS, PDF, PNG, SVG – max. 50 MB</div>
    <div class="upload-name" id="upload-bord-name"></div>`;
  if(inp) { inp.value = ''; }
}

// Pre-invullen vanuit portaal sessie (localStorage)
try {
  const savedKlant = JSON.parse(localStorage.getItem('mm_klant')||'null');
  if(savedKlant){
    ['fname','lname','email','phone','company','kvk','btwnr','street','zip','city'].forEach(id=>{
      const el=document.getElementById(id);
      if(el && savedKlant[id]) el.value=savedKlant[id];
    });
    const country=document.getElementById('country');
    if(country && savedKlant.country) country.value=savedKlant.country;
    if(savedKlant.company) { const biz=document.getElementById('biz-fields'); if(biz) biz.classList.remove('hidden'); }
  }
} catch(e){}

// ── Herplaats: laad vorige bestelling terug in de tool ─────────────────────────
try {
  const herplaatsRaw = sessionStorage.getItem('mm_herplaats');
  if(herplaatsRaw){
    sessionStorage.removeItem('mm_herplaats'); // eenmalig gebruiken
    const hp = JSON.parse(herplaatsRaw);
    if(hp?.regels?.length) laadHerplaats(hp);
    else s1Show('cat');
  } else {
    s1Show('cat');
  }
} catch(e){ s1Show('cat'); }

async function laadHerplaats(hp){
  // Toon herplaats-melding
  const melding = document.createElement('div');
  melding.style.cssText='position:fixed;top:1rem;left:50%;transform:translateX(-50%);background:#1a7a45;color:#fff;padding:.65rem 1.2rem;border-radius:8px;font-size:.83rem;font-weight:700;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.2);';
  melding.textContent='↩ Vorige bestelling laden…';
  document.body.appendChild(melding);

  // Klantgegevens pre-invullen vanuit herplaats
  if(hp.klant){
    const k = hp.klant;
    ['fname','lname','email','phone','company','street','zip','city'].forEach(id=>{
      const el=e(id); if(el && k[id]) el.value=k[id];
    });
    const country=e('country'); if(country&&k.country) country.value=k.country;
    if(k.company){ const biz=e('biz-fields'); if(biz) biz.classList.remove('hidden'); }
  }

  // Laad regels één voor één in de winkelwagen
  const WAGEN_BACKUP = [];
  for(const regel of hp.regels){
    const ok = await laadRegelHerplaats(regel);
    if(ok) WAGEN_BACKUP.push(ok);
  }

  // Als er meerdere regels zijn, zet ze in de wagen
  if(WAGEN_BACKUP.length > 1){
    WAGEN.push(...WAGEN_BACKUP);
    updCartBar();
    melding.textContent=`↩ ${WAGEN_BACKUP.length} producten herladen — controleer je bestelling`;
    setTimeout(()=>melding.remove(),4000);
    gS(6); // Ga naar samenvatting
  } else if(WAGEN_BACKUP.length === 1){
    melding.textContent='↩ Product herladen — pas aan en bestel opnieuw';
    setTimeout(()=>melding.remove(),3500);
    // Ga door naar stap 4 (maten) zodat klant kan aanpassen
  } else {
    melding.style.background='#e84c1e';
    melding.textContent='Herlaad mislukt — sommige producten zijn niet meer beschikbaar';
    setTimeout(()=>melding.remove(),3500);
    s1Show('cat');
  }
}

async function laadRegelHerplaats(regel){
  // Zoek model op SKU
  let gevondenMdl = null;
  let gevondenCat = null;
  for(const catId of Object.keys(MODELS)){
    const mdl = MODELS[catId].find(m => m.sku === regel.textiel_sku);
    if(mdl){ gevondenMdl=mdl; gevondenCat=catId; break; }
  }

  if(!gevondenMdl){
    console.warn('Model niet meer beschikbaar:', regel.textiel_sku);
    return null;
  }

  // Selecteer categorie en model
  S.cat = gevondenCat;
  S.mdl = gevondenMdl;

  // Zoek kleur op code
  const kleur = gevondenMdl.colors?.find(c=>c.code===regel.kleur_code);
  if(kleur){
    S.clrId=kleur.code; S.clrName=kleur.name; S.clrHex=kleur.hex; S.clrImg=kleur.img||null;
  } else if(regel.kleur_naam){
    S.clrId='custom'; S.clrName=regel.kleur_naam; S.clrHex=null;
  }

  // Positie
  S.pos = regel.positie || null;

  // Techniek
  const isDualRegel = !!regel.is_dual;
  S.techA = regel.techniek_a || null;
  S.techB = isDualRegel ? (regel.techniek_b || regel.techniek_a) : null;

  // Maten — bouw de sz-grid op en vul maten in
  buildSzTable();
  if(regel.maten && typeof regel.maten==='object'){
    // Wacht even tot de grid gebouwd is
    await new Promise(r=>setTimeout(r,50));
    document.querySelectorAll('.sz-inp').forEach(inp=>{
      const maat=inp.dataset.size;
      if(maat && regel.maten[maat]) inp.value=regel.maten[maat];
    });
    updQ();
  }

  // Maak een wagen-object terug
  if(WAGEN.length === 0){
    // Eerste regel: toon in de UI
    gS(4); // Stap 4 zodat klant de maten ziet
    await new Promise(r=>setTimeout(r,100));
    calcQ();
  }

  // Geef de wagen-entry terug (voor meerdere regels)
  const sz={};
  document.querySelectorAll('.sz-inp').forEach(i=>{if(parseInt(i.value)>0)sz[i.dataset.size]=i.value;});
  return {
    naam:       gevondenMdl.name,
    merk:       gevondenMdl.brand,
    sku:        gevondenMdl.sku,
    kleurId:    S.clrId,
    kleurNaam:  S.clrName,
    kleurHex:   S.clrHex,
    pos:        S.pos,
    techA:      S.techA,
    techB:      S.techB,
    maten:      sz,
    qty:        S.qty,
    totExcl:    S.totEx||0,
    totIncl:    S.totIncl||0,
  };
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
