<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bestellen — Merch Master</title>
<meta name="description" content="Bestel direct online bij Merch Master. DTF, zeefdruk en borduren vanaf 1 stuk.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<style>
/* ── Reset & base ──────────────────────────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --terracotta:#c4622d;
  --terracotta-l:#e07645;
  --donkergroen:#1e3a2f;
  --groen:#3a6b4a;
  --creme:#faf7f2;
  --wit:#faf7f2;
  --ink:#1a1a1a;
  --ink2:#3a3832;
  --ink3:#7a7670;
  --border:#e8e4dc;
  --kaart:#fff;
  --zand:#f0ece4;
  --r:12px;
  --shadow:0 2px 16px rgba(0,0,0,.07);
  --shadow-lg:0 8px 48px rgba(0,0,0,.12);
  --display:'Syne',sans-serif;
  --body:'DM Sans',system-ui,sans-serif;
}
html{scroll-behavior:smooth;}
body{font-family:var(--body);background:var(--creme);color:var(--ink);min-height:100vh;font-size:15px;line-height:1.6;}

/* ── Topbar ────────────────────────────────────────────────────────────────── */
.topbar{background:var(--donkergroen);padding:.9rem 2rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;border-bottom:1px solid rgba(255,255,255,.08);}
.topbar-logo{font-family:var(--display);font-size:1.2rem;font-weight:900;color:var(--wit);text-decoration:none;letter-spacing:-.02em;}
.topbar-logo em{color:var(--terracotta);font-style:normal;}
.topbar-back{font-size:.78rem;color:rgba(250,247,242,.55);text-decoration:none;display:flex;align-items:center;gap:.35rem;transition:color .2s;}
.topbar-back:hover{color:var(--wit);}
.topbar-wagen{display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:rgba(250,247,242,.7);cursor:pointer;background:rgba(255,255,255,.08);padding:.4rem .85rem;border-radius:50px;border:1px solid rgba(255,255,255,.12);transition:background .2s;}
.topbar-wagen:hover{background:rgba(255,255,255,.14);}
.wagen-badge{background:var(--terracotta);color:#fff;font-size:.65rem;font-weight:700;min-width:18px;height:18px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;}

/* ── Progress stepper ──────────────────────────────────────────────────────── */
.stepper-wrap{background:var(--kaart);border-bottom:1px solid var(--border);padding:.75rem 2rem;overflow-x:auto;}
.stepper{display:flex;align-items:center;gap:0;max-width:900px;margin:0 auto;min-width:600px;}
.step{display:flex;align-items:center;gap:.5rem;flex:1;position:relative;}
.step:last-child{flex:0;}
.step-dot{width:28px;height:28px;border-radius:50%;border:2px solid var(--border);background:var(--kaart);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:var(--ink3);flex-shrink:0;transition:all .25s;position:relative;z-index:1;}
.step.done .step-dot{background:var(--donkergroen);border-color:var(--donkergroen);color:#fff;}
.step.actief .step-dot{background:var(--terracotta);border-color:var(--terracotta);color:#fff;box-shadow:0 0 0 4px rgba(196,98,45,.18);}
.step-lijn{flex:1;height:2px;background:var(--border);margin:0 .25rem;transition:background .25s;}
.step.done .step-lijn{background:var(--donkergroen);}
.step-lbl{font-size:.65rem;font-weight:600;color:var(--ink3);white-space:nowrap;position:absolute;top:100%;left:50%;transform:translateX(-50%);margin-top:.35rem;transition:color .2s;}
.step.actief .step-lbl{color:var(--terracotta);}
.step.done .step-lbl{color:var(--donkergroen);}

/* ── Wizard layout ─────────────────────────────────────────────────────────── */
.wizard-wrap{max-width:1100px;margin:0 auto;padding:2rem 1.5rem 4rem;display:grid;grid-template-columns:1fr 340px;gap:1.75rem;align-items:start;}
@media(max-width:900px){.wizard-wrap{grid-template-columns:1fr;}}
.wizard-hoofd{min-width:0;}
.wizard-zij{position:sticky;top:72px;}

/* ── Stap container ────────────────────────────────────────────────────────── */
.stap{display:none;animation:stapIn .28s ease;}
.stap.actief{display:block;}
@keyframes stapIn{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}

.stap-hdr{margin-bottom:1.5rem;}
.stap-nr{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--terracotta);margin-bottom:.3rem;}
.stap-ttl{font-family:var(--display);font-size:clamp(1.4rem,3vw,2rem);font-weight:800;line-height:1.1;color:var(--ink);}
.stap-sub{font-size:.88rem;color:var(--ink3);margin-top:.4rem;line-height:1.6;}

/* ── Categorie grid ────────────────────────────────────────────────────────── */
.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.75rem;}
.cat-kaart{background:var(--kaart);border:2px solid var(--border);border-radius:var(--r);padding:1.25rem 1rem;text-align:center;cursor:pointer;transition:all .2s;user-select:none;}
.cat-kaart:hover{border-color:var(--terracotta);transform:translateY(-2px);box-shadow:var(--shadow);}
.cat-kaart.geselecteerd{border-color:var(--terracotta);background:#fff9f7;box-shadow:0 0 0 3px rgba(196,98,45,.12);}
.cat-kaart.cat-leeg{opacity:.45;cursor:default;}
.cat-kaart.cat-leeg:hover{transform:none;box-shadow:none;border-color:var(--border);}
.cat-icon{font-size:2rem;margin-bottom:.6rem;display:block;}
.cat-naam{font-size:.82rem;font-weight:700;color:var(--ink);}
.cat-count{font-size:.7rem;color:var(--ink3);margin-top:.2rem;}

/* ── Product grid ──────────────────────────────────────────────────────────── */
.prod-zoek{width:100%;padding:.7rem 1rem;border:1.5px solid var(--border);border-radius:50px;font-size:.88rem;font-family:var(--body);background:var(--kaart);margin-bottom:1rem;transition:border-color .2s;}
.prod-zoek:focus{outline:none;border-color:var(--terracotta);}
.prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:.75rem;max-height:480px;overflow-y:auto;padding-right:.25rem;}
.prod-grid::-webkit-scrollbar{width:4px;}
.prod-grid::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}
.prod-kaart{background:var(--kaart);border:2px solid var(--border);border-radius:var(--r);padding:0;cursor:pointer;transition:all .2s;overflow:hidden;}
.prod-kaart:hover{border-color:var(--terracotta);transform:translateY(-2px);box-shadow:var(--shadow);}
.prod-kaart.geselecteerd{border-color:var(--terracotta);box-shadow:0 0 0 3px rgba(196,98,45,.12);}
.prod-img{width:100%;height:120px;object-fit:cover;background:var(--zand);display:block;}
.prod-img-ph{width:100%;height:120px;background:linear-gradient(135deg,var(--zand),var(--border));display:flex;align-items:center;justify-content:center;font-size:2rem;}
.prod-info{padding:.75rem;}
.prod-merk{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink3);}
.prod-naam{font-size:.82rem;font-weight:700;color:var(--ink);margin:.2rem 0;}
.prod-prijs{font-size:.78rem;color:var(--terracotta);font-weight:600;}

/* ── Kleur + maat stap ─────────────────────────────────────────────────────── */
.kleur-grid{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.5rem;}
.kleur-swatch{width:36px;height:36px;border-radius:50%;border:3px solid transparent;cursor:pointer;transition:all .18s;position:relative;}
.kleur-swatch:hover{transform:scale(1.12);}
.kleur-swatch.geselecteerd{border-color:var(--terracotta);box-shadow:0 0 0 2px var(--kaart),0 0 0 4px var(--terracotta);}
.kleur-naam-display{font-size:.82rem;color:var(--ink2);margin-bottom:1.25rem;min-height:20px;}

.maten-sectie h4{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);margin-bottom:.75rem;}
.maten-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-bottom:.5rem;}
@media(max-width:480px){.maten-grid{grid-template-columns:repeat(3,1fr);}}
.maat-item{display:flex;flex-direction:column;align-items:center;gap:.3rem;}
.maat-lbl{font-size:.72rem;font-weight:700;color:var(--ink2);}
.maat-input{width:100%;padding:.5rem .35rem;border:1.5px solid var(--border);border-radius:8px;font-size:.9rem;text-align:center;font-family:var(--body);transition:border-color .2s;background:var(--kaart);}
.maat-input:focus{outline:none;border-color:var(--terracotta);}
.maat-input.heeft-waarde{border-color:var(--donkergroen);background:#f0fdf4;}
.maten-totaal{font-size:.82rem;color:var(--ink3);text-align:right;margin-top:.35rem;}
.maten-totaal strong{color:var(--ink);font-weight:700;}

/* ── Techniek stap ─────────────────────────────────────────────────────────── */
.tech-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;}
@media(max-width:600px){.tech-grid{grid-template-columns:1fr;}}
.tech-kaart{background:var(--kaart);border:2px solid var(--border);border-radius:var(--r);padding:1.25rem;cursor:pointer;transition:all .2s;position:relative;}
.tech-kaart:hover:not(.geblokkeerd){border-color:var(--terracotta);transform:translateY(-2px);box-shadow:var(--shadow);}
.tech-kaart.geselecteerd{border-color:var(--terracotta);background:#fff9f7;box-shadow:0 0 0 3px rgba(196,98,45,.12);}
.tech-kaart.geblokkeerd{opacity:.55;cursor:not-allowed;}
.tech-icon{font-size:1.75rem;margin-bottom:.6rem;}
.tech-naam{font-family:var(--display);font-size:.95rem;font-weight:800;color:var(--ink);}
.tech-sub{font-size:.75rem;color:var(--ink3);margin-top:.3rem;line-height:1.5;}
.tech-badge{position:absolute;top:.6rem;right:.6rem;font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:.2rem .5rem;border-radius:4px;}
.tech-badge-min{background:#fff3cd;color:#92400e;}
.tech-badge-aanvraag{background:#f3e8ff;color:#6b21a8;}
.tech-badge-ok{background:#dcfce7;color:#166534;}
.tech-min-waarsch{background:#fff3cd;border:1px solid #f59e0b;border-radius:8px;padding:.6rem .85rem;font-size:.78rem;color:#92400e;margin-top:.75rem;line-height:1.6;display:none;}
.tech-min-waarsch.toon{display:block;}

/* ── Posities stap ─────────────────────────────────────────────────────────── */
.pos-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
.pos-kaart{background:var(--kaart);border:2px solid var(--border);border-radius:var(--r);padding:1rem;cursor:pointer;transition:all .2s;display:flex;align-items:flex-start;gap:.75rem;}
.pos-kaart:hover:not(.geblokkeerd){border-color:var(--terracotta);}
.pos-kaart.geselecteerd{border-color:var(--terracotta);background:#fff9f7;}
.pos-kaart.geblokkeerd{opacity:.4;cursor:not-allowed;pointer-events:none;}
.pos-checkbox{width:20px;height:20px;border:2px solid var(--border);border-radius:4px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .2s;margin-top:.1rem;}
.pos-kaart.geselecteerd .pos-checkbox{background:var(--terracotta);border-color:var(--terracotta);}
.pos-check-icon{display:none;}
.pos-kaart.geselecteerd .pos-check-icon{display:block;}
.pos-info h4{font-size:.85rem;font-weight:700;color:var(--ink);}
.pos-info p{font-size:.74rem;color:var(--ink3);margin-top:.15rem;line-height:1.4;}
.pos-regel{background:rgba(196,98,45,.06);border:1px solid rgba(196,98,45,.15);border-radius:8px;padding:.6rem .85rem;font-size:.78rem;color:var(--ink2);margin-top:.75rem;line-height:1.6;}

/* Kleuren per positie (zeefdruk) */
.kl-select-wrap{margin-top:.75rem;}
.kl-select-wrap label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink3);display:block;margin-bottom:.35rem;}
.kl-rij{display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem;font-size:.82rem;}
.kl-rij span{flex:1;color:var(--ink2);}
.kl-sel{padding:.35rem .6rem;border:1.5px solid var(--border);border-radius:6px;font-size:.82rem;font-family:var(--body);}

/* ── Upload stap ───────────────────────────────────────────────────────────── */
.upload-zone{border:2px dashed var(--border);border-radius:var(--r);padding:2.5rem;text-align:center;cursor:pointer;transition:all .25s;background:var(--kaart);position:relative;}
.upload-zone:hover,.upload-zone.dragover{border-color:var(--terracotta);background:#fff9f7;}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-icon{font-size:2.5rem;margin-bottom:.75rem;display:block;}
.upload-ttl{font-size:.95rem;font-weight:700;color:var(--ink);margin-bottom:.3rem;}
.upload-sub{font-size:.78rem;color:var(--ink3);line-height:1.6;}
.upload-result{background:var(--kaart);border:1.5px solid var(--donkergroen);border-radius:var(--r);padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;margin-top:1rem;}
.upload-result-icon{font-size:1.5rem;flex-shrink:0;}
.upload-result-info{flex:1;}
.upload-result-naam{font-size:.88rem;font-weight:700;color:var(--donkergroen);}
.upload-result-meta{font-size:.75rem;color:var(--ink3);margin-top:.15rem;}
.upload-result-del{background:none;border:none;color:var(--ink3);cursor:pointer;font-size:1.1rem;padding:.25rem;transition:color .2s;}
.upload-result-del:hover{color:#991b1b;}
.upload-later{background:var(--zand);border-radius:var(--r);padding:1rem 1.25rem;margin-top:.75rem;font-size:.8rem;color:var(--ink2);line-height:1.6;}

/* ── Wagen zijpaneel ───────────────────────────────────────────────────────── */
.zij-kaart{background:var(--kaart);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow);overflow:hidden;}
.zij-kaart-hdr{padding:1rem 1.25rem;border-bottom:1px solid var(--border);font-family:var(--display);font-size:.9rem;font-weight:800;display:flex;justify-content:space-between;align-items:center;}
.zij-prijs-preview{padding:1rem 1.25rem;border-bottom:1px solid var(--border);}
.prijs-rij{display:flex;justify-content:space-between;font-size:.82rem;padding:.2rem 0;color:var(--ink2);}
.prijs-rij.totaal{font-weight:700;font-size:.95rem;color:var(--ink);padding-top:.4rem;margin-top:.2rem;border-top:1.5px solid var(--border);}
.prijs-laden{font-size:.78rem;color:var(--ink3);text-align:center;padding:.5rem;}
.wagen-regels-mini{padding:.75rem 1.25rem;}
.wagen-regel-mini{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--border);font-size:.8rem;}
.wagen-regel-mini:last-child{border-bottom:none;}
.wagen-regel-mini-naam{font-weight:600;color:var(--ink);}
.wagen-regel-mini-sub{font-size:.72rem;color:var(--ink3);}
.wagen-regel-mini-prijs{font-weight:700;color:var(--terracotta);white-space:nowrap;margin-left:.5rem;}
.wagen-leeg-mini{padding:.75rem 1.25rem;font-size:.8rem;color:var(--ink3);text-align:center;}

/* ── Checkout stap ─────────────────────────────────────────────────────────── */
.checkout-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
@media(max-width:540px){.checkout-grid{grid-template-columns:1fr;}}
.field label{display:block;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink3);margin-bottom:.35rem;}
.field input,.field select{width:100%;padding:.7rem .9rem;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:var(--body);background:var(--kaart);transition:border-color .2s;}
.field input:focus,.field select:focus{outline:none;border-color:var(--terracotta);}
.field{margin-bottom:.75rem;}
.account-keuze{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem;}
.acc-optie{background:var(--kaart);border:2px solid var(--border);border-radius:var(--r);padding:1.1rem;cursor:pointer;transition:all .2s;text-align:center;}
.acc-optie:hover{border-color:var(--terracotta);}
.acc-optie.geselecteerd{border-color:var(--terracotta);background:#fff9f7;}
.acc-optie h4{font-size:.88rem;font-weight:700;margin-bottom:.25rem;}
.acc-optie p{font-size:.75rem;color:var(--ink3);line-height:1.4;}

/* ── Betaal opties ─────────────────────────────────────────────────────────── */
.betaal-opties{display:flex;flex-direction:column;gap:.6rem;margin-bottom:1.5rem;}
.betaal-optie{background:var(--kaart);border:2px solid var(--border);border-radius:var(--r);padding:1rem 1.25rem;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:1rem;}
.betaal-optie:hover{border-color:var(--terracotta);}
.betaal-optie.geselecteerd{border-color:var(--terracotta);background:#fff9f7;}
.betaal-logo{height:28px;object-fit:contain;}
.betaal-info h4{font-size:.88rem;font-weight:700;}
.betaal-info p{font-size:.75rem;color:var(--ink3);}
.paypal-btn{width:100%;background:#0070ba;color:#fff;border:none;border-radius:50px;padding:1rem;font-size:1rem;font-weight:700;cursor:pointer;font-family:var(--body);display:flex;align-items:center;justify-content:center;gap:.6rem;transition:background .2s;}
.paypal-btn:hover{background:#005ea6;}
.offerte-btn{width:100%;background:var(--kaart);color:var(--ink);border:2px solid var(--border);border-radius:50px;padding:.9rem;font-size:.9rem;font-weight:700;cursor:pointer;font-family:var(--body);transition:all .2s;margin-top:.6rem;}
.offerte-btn:hover{border-color:var(--terracotta);color:var(--terracotta);}

/* ── Navigatie knoppen ─────────────────────────────────────────────────────── */
.stap-nav{display:flex;justify-content:space-between;align-items:center;margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);}
.btn-volgende{background:var(--terracotta);color:#fff;border:none;border-radius:50px;padding:.8rem 2rem;font-size:.92rem;font-weight:700;cursor:pointer;font-family:var(--body);display:inline-flex;align-items:center;gap:.5rem;transition:all .2s;}
.btn-volgende:hover{background:var(--terracotta-l);transform:translateY(-1px);}
.btn-volgende:disabled{background:#c4bdb4;cursor:not-allowed;transform:none;}
.btn-terug{background:none;border:1.5px solid var(--border);color:var(--ink3);border-radius:50px;padding:.75rem 1.5rem;font-size:.85rem;font-weight:600;cursor:pointer;font-family:var(--body);transition:all .2s;}
.btn-terug:hover{border-color:var(--ink3);color:var(--ink);}
.btn-wagen{background:var(--donkergroen);color:#fff;border:none;border-radius:50px;padding:.8rem 1.75rem;font-size:.88rem;font-weight:700;cursor:pointer;font-family:var(--body);display:inline-flex;align-items:center;gap:.5rem;transition:background .2s;}
.btn-wagen:hover{background:#2a4f3e;}

/* ── Bevestiging stap ──────────────────────────────────────────────────────── */
.bevestiging-wrap{text-align:center;padding:2rem 1rem;}
.bevestiging-icon{font-size:4rem;margin-bottom:1rem;}
.bevestiging-ttl{font-family:var(--display);font-size:1.8rem;font-weight:900;color:var(--donkergroen);margin-bottom:.75rem;}
.bevestiging-sub{font-size:.95rem;color:var(--ink2);line-height:1.8;max-width:480px;margin:0 auto 2rem;}
.bevestiging-acties{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;}

/* ── Fout / info ───────────────────────────────────────────────────────────── */
.inline-fout{background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:.6rem .9rem;font-size:.8rem;color:#991b1b;margin-top:.5rem;}
.inline-info{background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.6rem .9rem;font-size:.8rem;color:#166534;}

/* ── Spinner ───────────────────────────────────────────────────────────────── */
.spinner{display:inline-block;width:16px;height:16px;border:2.5px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}

/* ── Prijspreview live ─────────────────────────────────────────────────────── */
.live-prijs{font-family:var(--display);font-size:1.5rem;font-weight:800;color:var(--terracotta);}
.live-prijs-sub{font-size:.75rem;color:var(--ink3);}

/* ── Responsive ────────────────────────────────────────────────────────────── */
@media(max-width:640px){
  .topbar{padding:.75rem 1rem;}
  .stepper-wrap{padding:.6rem 1rem;}
  .wizard-wrap{padding:1.25rem 1rem 3rem;}
  .pos-grid{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<!-- Topbar -->
<header class="topbar">
  <a href="/" class="topbar-back">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
    Terug naar site
  </a>
  <a href="/" class="topbar-logo">Merch<em>Master</em></a>
  <div class="topbar-wagen" id="topbar-wagen" onclick="toonWagenPanel()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/></svg>
    Wagen
    <span class="wagen-badge" id="wagen-count">0</span>
  </div>
</header>

<!-- Stepper -->
<div class="stepper-wrap">
  <div class="stepper" id="stepper">
    <div class="step actief" data-step="1">
      <div class="step-dot">1</div>
      <span class="step-lbl">Categorie</span>
      <div class="step-lijn"></div>
    </div>
    <div class="step" data-step="2">
      <div class="step-dot">2</div>
      <span class="step-lbl">Product</span>
      <div class="step-lijn"></div>
    </div>
    <div class="step" data-step="3">
      <div class="step-dot">3</div>
      <span class="step-lbl">Kleur & maat</span>
      <div class="step-lijn"></div>
    </div>
    <div class="step" data-step="4">
      <div class="step-dot">4</div>
      <span class="step-lbl">Techniek</span>
      <div class="step-lijn"></div>
    </div>
    <div class="step" data-step="5">
      <div class="step-dot">5</div>
      <span class="step-lbl">Drukplekken</span>
      <div class="step-lijn"></div>
    </div>
    <div class="step" data-step="6">
      <div class="step-dot">6</div>
      <span class="step-lbl">Ontwerp</span>
      <div class="step-lijn"></div>
    </div>
    <div class="step" data-step="7">
      <div class="step-dot">7</div>
      <span class="step-lbl">Afrekenen</span>
    </div>
  </div>
</div>

<!-- Wizard -->
<div class="wizard-wrap">
  <div class="wizard-hoofd">

    <!-- STAP 1: Categorie -->
    <div class="stap actief" id="stap-1">
      <div class="stap-hdr">
        <div class="stap-nr">Stap 1 van 7</div>
        <h1 class="stap-ttl">Wat wil je laten bedrukken?</h1>
        <p class="stap-sub">Kies de productcategorie die bij jouw wens past.</p>
      </div>
      <div class="cat-grid" id="cat-grid">
        <div class="loading-pulse" style="grid-column:1/-1;padding:2rem;text-align:center;color:var(--ink3);font-size:.85rem;">Categorieën laden...</div>
      </div>
      <div class="stap-nav">
        <div></div>
        <button class="btn-volgende" id="btn-1" onclick="naarStap(2)" disabled>
          Verder <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>

    <!-- STAP 2: Product -->
    <div class="stap" id="stap-2">
      <div class="stap-hdr">
        <div class="stap-nr">Stap 2 van 7</div>
        <h2 class="stap-ttl">Kies je product</h2>
        <p class="stap-sub" id="stap2-sub">Selecteer het gewenste product.</p>
      </div>
      <input type="search" class="prod-zoek" placeholder="Product zoeken..." id="prod-zoek" oninput="filterProducten(this.value)">
      <div class="prod-grid" id="prod-grid">
        <div style="grid-column:1/-1;padding:2rem;text-align:center;color:var(--ink3);font-size:.85rem;">Selecteer eerst een categorie.</div>
      </div>
      <div class="stap-nav">
        <button class="btn-terug" onclick="naarStap(1)">← Terug</button>
        <button class="btn-volgende" id="btn-2" onclick="naarStap(3)" disabled>Verder →</button>
      </div>
    </div>

    <!-- STAP 3: Kleur & maat -->
    <div class="stap" id="stap-3">
      <div class="stap-hdr">
        <div class="stap-nr">Stap 3 van 7</div>
        <h2 class="stap-ttl">Kleur & aantallen</h2>
        <p class="stap-sub">Kies een kleur en vul per maat het gewenste aantal in.</p>
      </div>
      <div id="kleur-sectie">
        <h3 style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);margin-bottom:.75rem;">Kleur</h3>
        <div class="kleur-grid" id="kleur-grid"></div>
        <div class="kleur-naam-display" id="kleur-naam-display">Selecteer een kleur</div>
      </div>
      <div class="maten-sectie">
        <h3>Aantallen per maat</h3>
        <div class="maten-grid" id="maten-grid"></div>
        <div class="maten-totaal">Totaal: <strong id="maten-totaal">0</strong> stuks</div>
      </div>
      <div class="stap-nav">
        <button class="btn-terug" onclick="naarStap(2)">← Terug</button>
        <button class="btn-volgende" id="btn-3" onclick="naarStap(4)" disabled>Verder →</button>
      </div>
    </div>

    <!-- STAP 4: Techniek -->
    <div class="stap" id="stap-4">
      <div class="stap-hdr">
        <div class="stap-nr">Stap 4 van 7</div>
        <h2 class="stap-ttl">Decoratiemethode</h2>
        <p class="stap-sub">Kies hoe je product wordt versierd.</p>
      </div>
      <div class="tech-grid">
        <div class="tech-kaart" id="tech-dtf" onclick="kiesTechniek('dtf')">
          <span class="tech-badge tech-badge-ok">Vanaf 1 stuk</span>
          <div class="tech-icon">🖨️</div>
          <div class="tech-naam">DTF Transferdruk</div>
          <div class="tech-sub">Full colour, fotokwaliteit. Geschikt voor alle stofsoorten. Geen minimale oplage.</div>
        </div>
        <div class="tech-kaart" id="tech-zeefdruk" onclick="kiesTechniek('zeefdruk')">
          <span class="tech-badge tech-badge-min">Min. 25 stuks</span>
          <div class="tech-icon">🎨</div>
          <div class="tech-naam">Zeefdruk</div>
          <div class="tech-sub">Scherpe kleuren, ideaal voor grote oplages. Vanaf 25 stuks.</div>
        </div>
        <div class="tech-kaart" id="tech-borduren" onclick="kiesTechniek('borduren')">
          <span class="tech-badge tech-badge-aanvraag">Op aanvraag</span>
          <div class="tech-icon">🧵</div>
          <div class="tech-naam">Borduren</div>
          <div class="tech-sub">Premium afwerking. Luxueus en duurzaam voor caps, jassen en bedrijfskleding.</div>
        </div>
      </div>
      <div class="tech-min-waarsch" id="tech-min-waarsch">
        <strong>Let op:</strong> zeefdruk heeft een minimale oplage van 25 stuks. U heeft op dit moment <strong id="tech-min-aantal">0</strong> stuks geselecteerd. U kunt een zeefdruk offerte aanvragen, maar de order gaat pas in productie bij minimaal 25 stuks.
      </div>
      <div class="tech-min-waarsch" id="tech-borduur-info" style="background:#f3e8ff;border-color:#c084fc;">
        <strong>Borduren is op aanvraag.</strong> U kunt een offerte aanvragen en wij nemen contact met u op voor een prijsopgave op maat. Direct betalen is niet mogelijk.
      </div>
      <div class="stap-nav">
        <button class="btn-terug" onclick="naarStap(3)">← Terug</button>
        <button class="btn-volgende" id="btn-4" onclick="naarStap(5)" disabled>Verder →</button>
      </div>
    </div>

    <!-- STAP 5: Drukplekken -->
    <div class="stap" id="stap-5">
      <div class="stap-hdr">
        <div class="stap-nr">Stap 5 van 7</div>
        <h2 class="stap-ttl">Drukplekken</h2>
        <p class="stap-sub">Kies waar het ontwerp geplaatst wordt. Meerdere plekken mogelijk.</p>
      </div>
      <div class="pos-grid">
        <div class="pos-kaart" id="pos-voorkant" onclick="togglePositie('voorkant')">
          <div class="pos-checkbox"><svg class="pos-check-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></div>
          <div class="pos-info">
            <h4>Voorkant</h4>
            <p>Middenvoor op de borst of grote print over de voorkant.</p>
          </div>
        </div>
        <div class="pos-kaart" id="pos-achterkant" onclick="togglePositie('achterkant')">
          <div class="pos-checkbox"><svg class="pos-check-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></div>
          <div class="pos-info">
            <h4>Achterkant</h4>
            <p>Groot vlak voor logo's, tekst of volledige prints.</p>
          </div>
        </div>
        <div class="pos-kaart" id="pos-linkerborst" onclick="togglePositie('linkerborst')">
          <div class="pos-checkbox"><svg class="pos-check-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></div>
          <div class="pos-info">
            <h4>Linkerborst</h4>
            <p>Klein logo of embleem. Alleen combineerbaar met achterkant.</p>
          </div>
        </div>
        <div class="pos-kaart" id="pos-rechterborst" onclick="togglePositie('rechterborst')">
          <div class="pos-checkbox"><svg class="pos-check-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></div>
          <div class="pos-info">
            <h4>Rechterborst</h4>
            <p>Klein logo of embleem. Alleen combineerbaar met achterkant.</p>
          </div>
        </div>
      </div>
      <div class="pos-regel" id="pos-combinatie-regel" style="display:none;"></div>

      <!-- Kleuren per positie (zeefdruk) -->
      <div class="kl-select-wrap" id="kl-select-wrap" style="display:none;">
        <label>Aantal kleuren per drukplek (zeefdruk)</label>
        <div id="kl-select-rijen"></div>
      </div>

      <div class="stap-nav">
        <button class="btn-terug" onclick="naarStap(4)">← Terug</button>
        <button class="btn-volgende" id="btn-5" onclick="naarStap(6)" disabled>Verder →</button>
      </div>
    </div>

    <!-- STAP 6: Upload -->
    <div class="stap" id="stap-6">
      <div class="stap-hdr">
        <div class="stap-nr">Stap 6 van 7</div>
        <h2 class="stap-ttl">Upload je ontwerp</h2>
        <p class="stap-sub">Bestandsformaten: JPG, PNG, SVG, PDF, AI of PSD. Maximaal 20MB.</p>
      </div>
      <div class="upload-zone" id="upload-zone">
        <input type="file" id="upload-input" accept=".jpg,.jpeg,.png,.svg,.pdf,.ai,.eps,.psd" onchange="handleUpload(this.files[0])">
        <span class="upload-icon">📁</span>
        <div class="upload-ttl">Sleep je bestand hierheen</div>
        <div class="upload-sub">of klik om een bestand te kiezen</div>
      </div>
      <div id="upload-result" style="display:none;"></div>
      <div class="upload-later">
        <strong>Nog geen bestand bij de hand?</strong> Geen probleem — u kunt de bestelling of offerte afronden en uw ontwerp naderhand sturen naar
        <a href="mailto:info@merch-master.com" style="color:var(--terracotta);">info@merch-master.com</a>.
        Vermeld daarbij uw ordernummer. De productie start pas na ontvangst van het bestand.
      </div>
      <div class="stap-nav">
        <button class="btn-terug" onclick="naarStap(5)">← Terug</button>
        <div style="display:flex;gap:.75rem;">
          <button class="btn-wagen" onclick="voegToeAanWagen()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Toevoegen aan wagen
          </button>
        </div>
      </div>
    </div>

    <!-- STAP 7: Afrekenen -->
    <div class="stap" id="stap-7">
      <div class="stap-hdr">
        <div class="stap-nr">Stap 7 van 7</div>
        <h2 class="stap-ttl">Afrekenen</h2>
        <p class="stap-sub">Controleer uw wagen en kies hoe u verder wilt.</p>
      </div>

      <!-- Account keuze -->
      <h3 style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);margin-bottom:.75rem;">Account</h3>
      <div class="account-keuze" id="account-keuze">
        <div class="acc-optie geselecteerd" id="acc-gast" onclick="kiesAccount('gast')">
          <h4>Uitchecken als gast</h4>
          <p>Snel afrekenen zonder account. U ontvangt een bevestigingsmail.</p>
        </div>
        <div class="acc-optie" id="acc-account" onclick="kiesAccount('account')">
          <h4>Account aanmaken / inloggen</h4>
          <p>Bestellingen opvolgen, herbestellen en offertes opslaan.</p>
        </div>
      </div>

      <!-- Klantgegevens -->
      <h3 style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);margin-bottom:.75rem;">Uw gegevens</h3>
      <div class="checkout-grid">
        <div class="field"><label>Voornaam *</label><input type="text" id="co-vnaam" placeholder="Jan"></div>
        <div class="field"><label>Achternaam *</label><input type="text" id="co-anaam" placeholder="de Vries"></div>
      </div>
      <div class="field"><label>E-mailadres *</label><input type="email" id="co-email" placeholder="jan@bedrijf.nl"></div>
      <div class="checkout-grid">
        <div class="field"><label>Telefoonnummer</label><input type="tel" id="co-tel" placeholder="+31 6 ..."></div>
        <div class="field"><label>Bedrijfsnaam</label><input type="text" id="co-bedrijf"></div>
      </div>
      <div id="acc-wachtwoord-wrap" style="display:none;">
        <div class="field"><label>Wachtwoord kiezen *</label><input type="password" id="co-pw" placeholder="Min. 8 tekens"></div>
      </div>

      <!-- Betaalopties -->
      <h3 style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);margin:.75rem 0;">Hoe wilt u verdergaan?</h3>
      <div id="checkout-fout" class="inline-fout" style="display:none;"></div>

      <div id="betaal-wrap">
        <!-- Offerte aanvragen altijd beschikbaar -->
        <button class="offerte-btn" onclick="vraagOfferte()" id="btn-offerte">
          📄 Offerte aanvragen (gratis)
        </button>
        <!-- Spoed melding -->
        <div id="spoed-info" style="display:none;background:#fff3cd;border:1px solid #f59e0b;border-radius:8px;padding:.75rem 1rem;font-size:.8rem;color:#92400e;margin-top:.75rem;line-height:1.6;">
          <strong>Spoedorder geselecteerd.</strong> U kunt niet direct online betalen bij een spoedorder. Vraag een offerte aan — wij nemen dan contact op voor bevestiging en betaling.
        </div>
        <div id="paypal-wrap" style="margin-top:1rem;">
          <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
            <div style="flex:1;height:1px;background:var(--border);"></div>
            <span style="font-size:.75rem;color:var(--ink3);">of direct betalen</span>
            <div style="flex:1;height:1px;background:var(--border);"></div>
          </div>
          <div id="paypal-button-container"></div>
        </div>
      </div>

      <div class="stap-nav">
        <button class="btn-terug" onclick="naarStap(6)">← Terug</button>
      </div>
    </div>

    <!-- STAP 8: Bevestiging -->
    <div class="stap" id="stap-8">
      <div class="bevestiging-wrap">
        <div class="bevestiging-icon" id="bev-icon">✅</div>
        <h2 class="bevestiging-ttl" id="bev-ttl">Bedankt voor uw bestelling!</h2>
        <p class="bevestiging-sub" id="bev-sub">U ontvangt een bevestiging per e-mail. Zodra uw ontwerp is ontvangen gaat de productie van start.</p>
        <div class="bevestiging-acties">
          <a href="/" class="btn-volgende" style="text-decoration:none;">Terug naar home</a>
          <a href="/portaal" class="btn-wagen" style="text-decoration:none;" id="bev-portaal">Mijn account</a>
        </div>
      </div>
    </div>

  </div><!-- /wizard-hoofd -->

  <!-- Zijpaneel -->
  <aside class="wizard-zij">
    <!-- Live prijspreview -->
    <div class="zij-kaart" style="margin-bottom:1rem;">
      <div class="zij-kaart-hdr">
        Prijsindicatie
        <div style="display:flex;border:1.5px solid var(--border);border-radius:6px;overflow:hidden;">
          <button onclick="setBtw('incl')" id="btw-incl" style="padding:.25rem .6rem;font-size:.7rem;font-weight:600;border:none;background:#1a1a1a;color:#fff;cursor:pointer;font-family:var(--body);">Incl.</button>
          <button onclick="setBtw('excl')" id="btw-excl" style="padding:.25rem .6rem;font-size:.7rem;font-weight:600;border:none;background:#fff;color:#7a7670;cursor:pointer;font-family:var(--body);">Excl.</button>
        </div>
      </div>
      <div class="zij-prijs-preview" id="zij-prijs">
        <div class="prijs-laden">Configureer uw product om een prijsindicatie te zien.</div>
      </div>
    </div>
    <!-- Wagen mini -->
    <div class="zij-kaart">
      <div class="zij-kaart-hdr">
        Winkelwagen
        <span class="wagen-badge" id="zij-wagen-count">0</span>
      </div>
      <div id="zij-wagen-inhoud">
        <div class="wagen-leeg-mini">Uw wagen is leeg.</div>
      </div>
      <div id="zij-wagen-acties" style="display:none;padding:.75rem 1.25rem;border-top:1px solid var(--border);">
        <button onclick="naarStap(7)" style="width:100%;background:var(--terracotta);color:#fff;border:none;border-radius:50px;padding:.7rem;font-size:.85rem;font-weight:700;cursor:pointer;font-family:var(--body);">
          Naar afrekenen →
        </button>
      </div>
    </div>
  </aside>

</div><!-- /wizard-wrap -->

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=EUR" data-sdk-integration-source="button-factory"></script>

<script>
// ── Configuratie & state ──────────────────────────────────────────────────────
const API_CAT    = '/bestellen/catalogus.php';
const API_PRIJS  = '/bestellen/prijs.php';
const API_WAGEN  = '/bestellen/wagen.php';
const API_MAIL   = '/bestellen/mail.php';

let config = {
  stap:         1,
  categorie:    null,
  product:      null,
  kleur:        null,
  maten:        {},
  techniek:     null,
  posities:     [],
  uploadToken:  null,
  uploadNaam:   null,
  accountKeuze: 'gast',
  spoed:        false,
  btwKeuze:     'incl',  // 'incl' | 'excl'
};

let catalogusData   = { categorieen: [], producten: [] };
let prodFilter      = '';
// Upload fix: zorg dat wagenToken altijd bestaat vóór upload
let wagenToken      = localStorage.getItem('mm_wagen_token') || null;
let wagenRegels     = [];
let wagenTotalen    = null;
let prijsTimeout    = null;
let ingelogdeKlant  = null; // wordt gevuld bij sessie-check

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  // Zorg altijd voor een wagen token zodat upload direct werkt
  if (!wagenToken) {
    wagenToken = crypto.randomUUID
      ? crypto.randomUUID().replace(/-/g,'')
      : Array.from({length:32},()=>Math.floor(Math.random()*16).toString(16)).join('');
    localStorage.setItem('mm_wagen_token', wagenToken);
  }
  await Promise.all([laadCatalogus(), laadWagen(), checkIngelogd()]);
  setupUploadDrop();
  // Herstel BTW keuze
  const opgeslagenBtw = localStorage.getItem('mm_btw_keuze');
  if (opgeslagenBtw) setBtw(opgeslagenBtw);
});

// ── Account sessie check ──────────────────────────────────────────────────────
async function checkIngelogd() {
  try {
    const r = await fetch('/bestellen/handler.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'klant' }),
    });
    const d = await r.json();
    if (d.ok && d.klant) {
      ingelogdeKlant = d.klant;
      // Pre-fill klantgegevens
      const vnEl = document.getElementById('co-vnaam');
      const anEl = document.getElementById('co-anaam');
      const emEl = document.getElementById('co-email');
      const tlEl = document.getElementById('co-tel');
      const bdEl = document.getElementById('co-bedrijf');
      if (vnEl) vnEl.value = d.klant.voornaam || '';
      if (anEl) anEl.value = d.klant.achternaam || '';
      if (emEl) emEl.value = d.klant.email || '';
      if (tlEl) tlEl.value = d.klant.telefoon || '';
      if (bdEl) bdEl.value = d.klant.bedrijf || '';
      // Toon ingelogd-status in account keuze
      const gastEl = document.getElementById('acc-gast');
      const accEl  = document.getElementById('acc-account');
      if (gastEl && accEl) {
        accEl.classList.add('geselecteerd');
        gastEl.classList.remove('geselecteerd');
        accEl.querySelector('h4').textContent = `Ingelogd als ${d.klant.voornaam}`;
        accEl.querySelector('p').textContent  = d.klant.email;
        config.accountKeuze = 'account';
      }
    }
  } catch(e) { /* niet ingelogd — geen probleem */ }
}
async function laadCatalogus() {
  try {
    const r = await fetch(API_CAT);
    const d = await r.json();
    if (!d.ok) return;
    catalogusData = d;
    renderCategorieen();
  } catch(e) {
    document.getElementById('cat-grid').innerHTML =
      '<div class="inline-fout" style="grid-column:1/-1">Catalogus kon niet geladen worden. Herlaad de pagina.</div>';
  }
}

function renderCategorieen() {
  const el   = document.getElementById('cat-grid');
  const cats = catalogusData.categorieen || [];
  if (!cats.length) {
    el.innerHTML = '<div class="inline-fout" style="grid-column:1/-1;">Geen categorieën gevonden. Herlaad de pagina.</div>';
    return;
  }
  el.innerHTML = cats.map(c => `
    <div class="cat-kaart ${c.aantal === 0 ? 'cat-leeg' : ''}" id="cat-${c.slug}"
         onclick="${c.aantal > 0 ? `kiesCategorie('${c.slug}','${esc(c.naam)}')` : ''}">
      <span class="cat-icon">${c.icon || '👕'}</span>
      <div class="cat-naam">${esc(c.naam)}</div>
      <div class="cat-count">${c.aantal > 0 ? c.aantal + ' producten' : 'Binnenkort'}</div>
    </div>`).join('');
}

function kiesCategorie(slug, naam) {
  config.categorie = slug;
  config.product   = null;
  document.querySelectorAll('.cat-kaart').forEach(el => el.classList.remove('geselecteerd'));
  document.getElementById('cat-'+slug)?.classList.add('geselecteerd');
  document.getElementById('btn-1').disabled = false;
  document.getElementById('stap2-sub').textContent = `Producten in: ${naam}`;
  renderProducten(slug);
}

function renderProducten(slug) {
  const el   = document.getElementById('prod-grid');
  const prod = (catalogusData.producten || []).filter(p => p.categorie_slug === slug);
  if (!prod.length) {
    el.innerHTML = '<div style="grid-column:1/-1;color:var(--ink3);font-size:.85rem;">Geen producten in deze categorie.</div>';
    return;
  }
  el.innerHTML = prod.map(p => `
    <div class="prod-kaart" id="prod-${p.id}" onclick="kiesProduct(${p.id})">
      <div class="prod-img-ph">👕</div>
      <div class="prod-info">
        <div class="prod-merk">${esc(p.merk||'')}</div>
        <div class="prod-naam">${esc(p.naam)}</div>
        <div class="prod-prijs">v.a. €${parseFloat(p.inkoop||0).toFixed(2).replace('.',',')}</div>
      </div>
    </div>`).join('');
}

function filterProducten(q) {
  prodFilter = q.toLowerCase();
  const slug = config.categorie;
  if (!slug) return;
  const prod = (catalogusData.producten || [])
    .filter(p => p.categorie_slug === slug)
    .filter(p => !prodFilter || (p.naam+' '+p.merk).toLowerCase().includes(prodFilter));
  const el = document.getElementById('prod-grid');
  if (!prod.length) { el.innerHTML = '<div style="grid-column:1/-1;color:var(--ink3);">Geen producten gevonden.</div>'; return; }
  el.innerHTML = prod.map(p => `
    <div class="prod-kaart ${config.product?.id == p.id ? 'geselecteerd' : ''}" id="prod-${p.id}" onclick="kiesProduct(${p.id})">
      <div class="prod-img-ph">👕</div>
      <div class="prod-info">
        <div class="prod-merk">${esc(p.merk||'')}</div>
        <div class="prod-naam">${esc(p.naam)}</div>
        <div class="prod-prijs">v.a. €${parseFloat(p.inkoop||0).toFixed(2).replace('.',',')}</div>
      </div>
    </div>`).join('');
}

function kiesProduct(id) {
  const p = (catalogusData.producten || []).find(p => p.id == id);
  if (!p) return;
  config.product = p;
  config.kleur   = null;
  config.maten   = {};
  document.querySelectorAll('.prod-kaart').forEach(el => el.classList.remove('geselecteerd'));
  document.getElementById('prod-'+id)?.classList.add('geselecteerd');
  document.getElementById('btn-2').disabled = false;
}

// ── Kleuren & maten ───────────────────────────────────────────────────────────
function renderKleuren() {
  const el  = document.getElementById('kleur-grid');
  const kl  = (config.product?.kleuren || []);
  if (!kl.length) {
    el.innerHTML = '<div style="color:var(--ink3);font-size:.85rem;">Geen kleurinformatie beschikbaar.</div>';
    return;
  }
  el.innerHTML = kl.map(k => `
    <div class="kleur-swatch" title="${esc(k.naam)}"
         style="background:${k.hex||'#ccc'}"
         onclick="kiesKleur('${k.code}','${esc(k.naam)}','${k.hex||'#ccc'}')">
    </div>`).join('');
}

function kiesKleur(code, naam, hex) {
  config.kleur = { code, naam, hex };
  document.querySelectorAll('.kleur-swatch').forEach(el => el.classList.remove('geselecteerd'));
  // Vind swatch op hex
  document.querySelectorAll('.kleur-swatch').forEach(el => {
    if (el.style.background === hex || el.getAttribute('title') === naam) el.classList.add('geselecteerd');
  });
  document.getElementById('kleur-naam-display').textContent = naam;
  checkStap3();
}

function renderMaten() {
  const el    = document.getElementById('maten-grid');
  const maten = config.product?.maten || ['XS','S','M','L','XL','XXL'];
  el.innerHTML = maten.map(m => `
    <div class="maat-item">
      <label class="maat-lbl">${m}</label>
      <input type="number" class="maat-input" min="0" max="9999"
             data-maat="${m}" value="${config.maten[m]||0}"
             oninput="updateMaat('${m}',this.value)">
    </div>`).join('');
}

function updateMaat(maat, val) {
  const v = parseInt(val) || 0;
  if (v > 0) config.maten[maat] = v; else delete config.maten[maat];
  const totaal = Object.values(config.maten).reduce((s,v)=>s+v,0);
  document.getElementById('maten-totaal').textContent = totaal;
  // Markeer ingevulde velden
  document.querySelectorAll('.maat-input').forEach(inp => {
    const v = parseInt(inp.value)||0;
    inp.classList.toggle('heeft-waarde', v > 0);
  });
  checkStap3();
  updateZeefWaarsch();
  schedulePrijsUpdate();
}

function checkStap3() {
  const totaal = Object.values(config.maten).reduce((s,v)=>s+v,0);
  document.getElementById('btn-3').disabled = !(config.kleur && totaal > 0);
}

function updateZeefWaarsch() {
  const totaal = Object.values(config.maten).reduce((s,v)=>s+v,0);
  document.getElementById('tech-min-aantal').textContent = totaal;
  if (config.techniek === 'zeefdruk' && totaal < 25) {
    document.getElementById('tech-min-waarsch').classList.add('toon');
  } else {
    document.getElementById('tech-min-waarsch').classList.remove('toon');
  }
}

// ── Techniek ──────────────────────────────────────────────────────────────────
function kiesTechniek(tech) {
  config.techniek = tech;
  config.posities = []; // reset bij techniek wissel
  document.querySelectorAll('.tech-kaart').forEach(el => el.classList.remove('geselecteerd'));
  document.getElementById('tech-'+tech)?.classList.add('geselecteerd');

  const totaal = Object.values(config.maten).reduce((s,v)=>s+v,0);
  document.getElementById('tech-min-waarsch').classList.toggle('toon', tech==='zeefdruk' && totaal < 25);
  document.getElementById('tech-borduur-info').classList.toggle('toon', tech==='borduren');
  document.getElementById('btn-4').disabled = false;

  // Posities aanpassen voor borduren (geen voorkant grote print)
  renderPosities();
  schedulePrijsUpdate();
}

// ── Posities ──────────────────────────────────────────────────────────────────
function renderPosities() {
  // Reset alles
  ['voorkant','achterkant','linkerborst','rechterborst'].forEach(p => {
    const el = document.getElementById('pos-'+p);
    if (!el) return;
    el.classList.remove('geselecteerd','geblokkeerd');
  });
  // Borduren: geen voorkant grote print
  if (config.techniek === 'borduren') {
    const el = document.getElementById('pos-voorkant');
    if (el) { el.classList.add('geblokkeerd'); }
  }
}

function togglePositie(naam) {
  const el = document.getElementById('pos-'+naam);
  if (el?.classList.contains('geblokkeerd')) return;

  const idx = config.posities.findIndex(p => p.positie === naam);
  if (idx >= 0) {
    config.posities.splice(idx, 1);
    el?.classList.remove('geselecteerd');
  } else {
    // Combinatievalidatie
    const heeftVoor  = config.posities.some(p => p.positie === 'voorkant');
    const heeftAchter = config.posities.some(p => p.positie === 'achterkant');
    if ((naam === 'linkerborst' || naam === 'rechterborst') && heeftVoor) {
      toonPosRegel('Linker/rechterborst kan niet gecombineerd worden met voorkant. Kies achterkant.');
      return;
    }
    if (naam === 'voorkant' && (config.posities.some(p=>p.positie==='linkerborst')||config.posities.some(p=>p.positie==='rechterborst'))) {
      toonPosRegel('Voorkant kan niet gecombineerd worden met linker/rechterborst.');
      return;
    }
    config.posities.push({ positie: naam, kleuren: 1 });
    el?.classList.add('geselecteerd');
  }

  // Combinatieregel verbergen als alles ok
  document.getElementById('pos-combinatie-regel').style.display = 'none';

  // Zeefdruk: toon kleurenselectie per positie
  renderKleurenSelectie();
  document.getElementById('btn-5').disabled = config.posities.length === 0;
  schedulePrijsUpdate();
}

function toonPosRegel(tekst) {
  const el = document.getElementById('pos-combinatie-regel');
  el.textContent = tekst;
  el.style.display = 'block';
  setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function renderKleurenSelectie() {
  const wrap = document.getElementById('kl-select-wrap');
  const rijen = document.getElementById('kl-select-rijen');
  if (config.techniek !== 'zeefdruk' || !config.posities.length) {
    wrap.style.display = 'none';
    return;
  }
  wrap.style.display = 'block';
  rijen.innerHTML = config.posities.map((p,i) => `
    <div class="kl-rij">
      <span>${p.positie}</span>
      <select class="kl-sel" onchange="updateKleuren(${i},this.value)">
        ${[1,2,3,4].map(k=>`<option value="${k}" ${k===p.kleuren?'selected':''}>${k} kleur${k>1?'en':''}</option>`).join('')}
      </select>
    </div>`).join('');
}

function updateKleuren(idx, val) {
  if (config.posities[idx]) {
    config.posities[idx].kleuren = parseInt(val);
    schedulePrijsUpdate();
  }
}

// ── Upload ────────────────────────────────────────────────────────────────────
function setupUploadDrop() {
  const zone = document.getElementById('upload-zone');
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('dragover');
    const f = e.dataTransfer.files[0];
    if (f) handleUpload(f);
  });
}

async function handleUpload(bestand) {
  if (!bestand) return;
  const MAX = 20 * 1024 * 1024;
  if (bestand.size > MAX) { toonUploadFout('Bestand is te groot (max. 20MB)'); return; }
  const okTypes = ['image/jpeg','image/png','image/svg+xml','application/pdf','application/postscript'];

  // Toon laden
  const resultEl = document.getElementById('upload-result');
  resultEl.style.display = 'block';
  resultEl.innerHTML = `<div class="upload-result"><span class="upload-result-icon"><div class="spinner" style="border-top-color:var(--terracotta);border-color:var(--border);"></div></span><div class="upload-result-info"><div class="upload-result-naam">Uploaden...</div></div></div>`;

  // Upload naar server (wagen token nodig)
  if (!wagenToken) wagenToken = Array.from({length:32},()=>Math.floor(Math.random()*16).toString(16)).join('');

  const fd = new FormData();
  fd.append('actie', 'upload');
  fd.append('wagen_token', wagenToken);
  fd.append('regel_id', 'nieuw');
  fd.append('ontwerp', bestand);

  try {
    const r  = await fetch(API_WAGEN, { method:'POST', body: fd });
    const d  = await r.json();
    if (d.ok) {
      config.uploadToken = d.upload_token;
      config.uploadNaam  = bestand.name;
      resultEl.innerHTML = `<div class="upload-result">
        <span class="upload-result-icon">✅</span>
        <div class="upload-result-info">
          <div class="upload-result-naam">${esc(bestand.name)}</div>
          <div class="upload-result-meta">${(bestand.size/1024).toFixed(0)} KB geüpload</div>
        </div>
        <button class="upload-result-del" onclick="verwijderUpload()">✕</button>
      </div>`;
    } else {
      toonUploadFout(d.fout || 'Upload mislukt');
    }
  } catch(e) {
    // Upload later — bestand lokaal bijhouden
    config.uploadToken = null;
    config.uploadNaam  = bestand.name;
    resultEl.innerHTML = `<div class="upload-result">
      <span class="upload-result-icon">📎</span>
      <div class="upload-result-info">
        <div class="upload-result-naam">${esc(bestand.name)}</div>
        <div class="upload-result-meta" style="color:var(--ink3);">Bestand wordt meegestuurd bij bestelling</div>
      </div>
      <button class="upload-result-del" onclick="verwijderUpload()">✕</button>
    </div>`;
  }
}

function verwijderUpload() {
  config.uploadToken = null;
  config.uploadNaam  = null;
  document.getElementById('upload-result').style.display = 'none';
  document.getElementById('upload-input').value = '';
}

function toonUploadFout(msg) {
  const el = document.getElementById('upload-result');
  el.style.display = 'block';
  el.innerHTML = `<div class="inline-fout">${esc(msg)}</div>`;
}

// ── BTW toggle ────────────────────────────────────────────────────────────────
function setBtw(keuze) {
  config.btwKeuze = keuze;
  localStorage.setItem('mm_btw_keuze', keuze);
  document.getElementById('btw-incl').style.background = keuze==='incl' ? '#1a1a1a' : '#fff';
  document.getElementById('btw-incl').style.color      = keuze==='incl' ? '#fff'    : '#7a7670';
  document.getElementById('btw-excl').style.background = keuze==='excl' ? '#1a1a1a' : '#fff';
  document.getElementById('btw-excl').style.color      = keuze==='excl' ? '#fff'    : '#7a7670';
  schedulePrijsUpdate();
  renderWagenMini();
}

// ── Live prijspreview ─────────────────────────────────────────────────────────
function schedulePrijsUpdate() {
  clearTimeout(prijsTimeout);
  prijsTimeout = setTimeout(updatePrijsPreview, 400);
}

async function updatePrijsPreview() {
  const totaal = Object.values(config.maten).reduce((s,v)=>s+v,0);
  const el     = document.getElementById('zij-prijs');

  if (!config.product || totaal === 0 || !config.techniek || !config.posities.length) {
    el.innerHTML = '<div class="prijs-laden">Vul product, aantal en techniek in voor een prijsindicatie.</div>';
    return;
  }

  el.innerHTML = '<div class="prijs-laden">Prijs berekenen...</div>';

  try {
    const r = await fetch(API_PRIJS, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        sku:         config.product.sku,
        techniek:    config.techniek,
        aantal:      totaal,
        kleuren:     config.posities[0]?.kleuren || 1,
        order_totaal_stuks: totaal + wagenRegels.reduce((s,r)=>s+(r.aantal||0),0),
      }),
    });
    const d = await r.json();
    if (!d.ok) { el.innerHTML = `<div class="prijs-laden" style="color:#991b1b;">${esc(d.fout)}</div>`; return; }
    if (d.op_aanvraag) { el.innerHTML = '<div class="prijs-laden">Borduren: prijs op aanvraag.</div>'; return; }

    const incl      = config.btwKeuze !== 'excl';
    const pStuk     = incl ? d.prijs_incl   : d.prijs_excl;
    const pTotaal   = incl ? d.totaal_incl  : d.totaal_excl;
    const btwLabel  = incl ? 'incl. BTW'    : 'excl. BTW';

    el.innerHTML = `
      <div class="prijs-rij"><span>${totaal} stuks × ${esc(config.techniek)}</span><span></span></div>
      <div class="prijs-rij"><span>Textiel</span><span>€ ${n(d.textiel_excl)}</span></div>
      <div class="prijs-rij"><span>Druk</span><span>€ ${n(d.druk_excl)}</span></div>
      ${d.volumekorting_pct > 0 ? `<div class="prijs-rij" style="color:#166534;"><span>Volumekorting ${d.volumekorting_pct}%</span><span>– € ${n(d.korting_per_stuk)}</span></div>` : ''}
      <div class="prijs-rij"><span>Per stuk ${btwLabel}</span><span>€ ${n(pStuk)}</span></div>
      <div class="prijs-rij totaal"><span>Totaal ${btwLabel}</span><span>€ ${n(pTotaal)}</span></div>
      ${!incl ? `<div class="prijs-rij" style="font-size:.75rem;color:var(--ink3);"><span>BTW (21%)</span><span>€ ${n(d.btw)}</span></div>` : ''}
      ${d.verzend_achteraf ? '<div style="font-size:.72rem;color:var(--ink3);margin-top:.35rem;">Verzendkosten worden achteraf berekend.</div>'
        : `<div class="prijs-rij" style="font-size:.78rem;color:var(--ink3);"><span>Verzending</span><span>€ ${n(d.verzend_excl)}</span></div>`}`;
  } catch(e) {
    el.innerHTML = '<div class="prijs-laden">Prijs momenteel niet beschikbaar.</div>';
  }
}

// ── Wagen ─────────────────────────────────────────────────────────────────────
async function laadWagen() {
  if (!wagenToken) { renderWagenMini(); return; }
  try {
    const r = await fetch(API_WAGEN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ actie: 'laden', wagen_token: wagenToken }),
    });
    const d = await r.json();
    if (d.ok) {
      wagenRegels  = d.regels  || [];
      wagenTotalen = d.totalen || null;
    }
  } catch(e) {}
  renderWagenMini();
}

function renderWagenMini() {
  const countEls = [document.getElementById('wagen-count'), document.getElementById('zij-wagen-count')];
  const count    = wagenRegels.length;
  countEls.forEach(el => { if (el) el.textContent = count; });

  const el      = document.getElementById('zij-wagen-inhoud');
  const acties  = document.getElementById('zij-wagen-acties');

  if (!count) {
    el.innerHTML = '<div class="wagen-leeg-mini">Uw wagen is leeg.</div>';
    if (acties) acties.style.display = 'none';
    return;
  }
  el.innerHTML = `<div class="wagen-regels-mini">
    ${wagenRegels.slice(0,4).map(r => `
      <div class="wagen-regel-mini">
        <div>
          <div class="wagen-regel-mini-naam">${esc(r.product_naam||r.sku)}</div>
          <div class="wagen-regel-mini-sub">${r.aantal} stuks · ${esc(r.techniek)}</div>
        </div>
        <div class="wagen-regel-mini-prijs">€ ${n(r.prijs?.totaal_incl||0)}</div>
      </div>`).join('')}
    ${wagenRegels.length > 4 ? `<div style="font-size:.74rem;color:var(--ink3);padding:.4rem 0;">+${wagenRegels.length-4} meer...</div>` : ''}
  </div>`;
  if (wagenTotalen) {
    el.innerHTML += `<div style="padding:.5rem 1.25rem 0;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:.82rem;font-weight:700;padding-bottom:.5rem;">
      <span>Totaal incl. BTW</span><span style="color:var(--terracotta);">€ ${n(wagenTotalen.totaal_incl)}</span>
    </div>`;
  }
  if (acties) acties.style.display = 'block';
}

async function voegToeAanWagen() {
  const totaal = Object.values(config.maten).reduce((s,v)=>s+v,0);
  if (!config.product || !config.kleur || totaal === 0 || !config.techniek || !config.posities.length) {
    alert('Vul alle stappen in voor u toevoegt aan de wagen.');
    return;
  }

  const btn = document.querySelector('[onclick="voegToeAanWagen()"]');
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner"></div> Toevoegen...';

  const regel = {
    sku:         config.product.sku,
    techniek:    config.techniek,
    kleur_code:  config.kleur.code,
    kleur_naam:  config.kleur.naam,
    posities:    config.posities,
    maten:       config.maten,
    notitie:     '',
    upload_token: config.uploadToken || null,
    upload_naam:  config.uploadNaam  || null,
  };

  try {
    const r = await fetch(API_WAGEN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ actie: 'toevoegen', wagen_token: wagenToken||'', regel }),
    });
    const d = await r.json();

    if (d.ok) {
      if (d.wagen_token) {
        wagenToken = d.wagen_token;
        localStorage.setItem('mm_wagen_token', d.wagen_token);
      }
      await laadWagen();

      // Reset configuratie voor nieuw product
      const tmpToken = wagenToken;
      config = { stap:1, categorie:null, product:null, kleur:null, maten:{}, techniek:null,
                 posities:[], uploadToken:null, uploadNaam:null, accountKeuze:'gast', spoed:false };
      wagenToken = tmpToken;

      // Vraag of klant nog een product wil toevoegen
      const nogeen = confirm('Product toegevoegd! Wilt u nog een product toevoegen?');
      if (nogeen) {
        naarStap(1);
      } else {
        naarStap(7);
      }
    } else {
      alert(d.fout || 'Kon product niet toevoegen.');
      btn.disabled = false;
      btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Toevoegen aan wagen`;
    }
  } catch(e) {
    alert('Verbindingsfout. Probeer opnieuw.');
    btn.disabled = false;
  }
}

// ── Checkout ──────────────────────────────────────────────────────────────────
function kiesAccount(keuze) {
  config.accountKeuze = keuze;
  document.getElementById('acc-gast').classList.toggle('geselecteerd', keuze==='gast');
  document.getElementById('acc-account').classList.toggle('geselecteerd', keuze==='account');
  document.getElementById('acc-wachtwoord-wrap').style.display = keuze==='account' ? 'block' : 'none';
}

function getKlantData() {
  return {
    naam:    (document.getElementById('co-vnaam')?.value||'').trim() + ' ' + (document.getElementById('co-anaam')?.value||'').trim(),
    email:   document.getElementById('co-email')?.value.trim() || '',
    tel:     document.getElementById('co-tel')?.value.trim() || '',
    bedrijf: document.getElementById('co-bedrijf')?.value.trim() || '',
  };
}

function valideerKlant() {
  const vnaam = document.getElementById('co-vnaam')?.value.trim();
  const anaam = document.getElementById('co-anaam')?.value.trim();
  const email = document.getElementById('co-email')?.value.trim();
  const foutEl = document.getElementById('checkout-fout');
  if (!vnaam || !anaam) { foutEl.textContent='Vul uw voor- en achternaam in.'; foutEl.style.display='block'; return false; }
  if (!email || !email.includes('@')) { foutEl.textContent='Vul een geldig e-mailadres in.'; foutEl.style.display='block'; return false; }
  foutEl.style.display = 'none';
  return true;
}

async function vraagOfferte() {
  if (!wagenRegels.length) { alert('Uw wagen is leeg.'); return; }
  if (!valideerKlant()) return;

  const btn = document.getElementById('btn-offerte');
  btn.disabled = true;
  btn.textContent = 'Bezig...';

  try {
    const r = await fetch(API_WAGEN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        actie:        'naar_offerte',
        wagen_token:  wagenToken,
        klant:        getKlantData(),
        spoed:        config.spoed,
      }),
    });
    const d = await r.json();

    if (d.ok) {
      wagenRegels  = [];
      wagenTotalen = null;
      localStorage.removeItem('mm_wagen_token');
      wagenToken = null;
      renderWagenMini();

      // Bevestigingsmail sturen
      fetch(API_MAIL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          actie:         'offerte_bevestiging',
          klant:         getKlantData(),
          regels:        d.regels || [],
          totalen:       d.totalen || {},
          offerte_nr:    d.offerte_nr || '–',
          pdf_url:       d.pdf_url || '',
          spoed:         config.spoed,
          spoed_toeslag: d.spoed_toeslag || 0,
          geldig_tot:    d.geldig_tot || '',
        }),
      }).catch(() => {}); // mail fout is niet kritiek voor UX

      document.getElementById('bev-icon').textContent    = '📄';
      document.getElementById('bev-ttl').textContent     = 'Offerte aangevraagd!';
      document.getElementById('bev-sub').textContent     = config.spoed
        ? 'Uw spoedofferte is aangemaakt. Neem contact op via info@merch-master.com voor bevestiging. U ontvangt zo snel mogelijk een reactie.'
        : 'Uw offerte is aangemaakt. U ontvangt een bevestiging per e-mail. Klik op de knop om uw offerte te bekijken.';

      if (d.pdf_url) {
        document.getElementById('bev-portaal').href = d.pdf_url;
        document.getElementById('bev-portaal').textContent = 'Offerte bekijken (PDF)';
      }

      naarStap(8);
    } else {
      document.getElementById('checkout-fout').textContent = d.fout || 'Fout bij aanvragen.';
      document.getElementById('checkout-fout').style.display = 'block';
    }
  } catch(e) {
    document.getElementById('checkout-fout').textContent = 'Verbindingsfout. Probeer opnieuw.';
    document.getElementById('checkout-fout').style.display = 'block';
  }

  btn.disabled = false;
  btn.textContent = '📄 Offerte aanvragen (gratis)';
}

// ── PayPal ────────────────────────────────────────────────────────────────────
function initPayPal() {
  const container = document.getElementById('paypal-button-container');
  if (!container || !window.paypal) return;
  container.innerHTML = '';

  paypal.Buttons({
    style: { layout:'vertical', color:'blue', shape:'pill', label:'paypal' },
    createOrder: (data, actions) => {
      if (!valideerKlant()) return;
      const totaal = wagenTotalen?.totaal_incl || 0;
      return actions.order.create({
        purchase_units: [{ amount: { value: totaal.toFixed(2), currency_code: 'EUR' },
                          description: 'Merch Master bestelling' }],
      });
    },
    onApprove: async (data, actions) => {
      const order = await actions.order.capture();
      await bevestigBetaling(order.id, 'paypal');
    },
    onError: (err) => {
      document.getElementById('checkout-fout').textContent = 'PayPal fout. Probeer opnieuw of kies "Offerte aanvragen".';
      document.getElementById('checkout-fout').style.display = 'block';
    },
  }).render('#paypal-button-container');
}

async function bevestigBetaling(paypalOrderId, methode) {
  // Sla op als offerte met status betaald
  const r = await fetch(API_WAGEN, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      actie: 'naar_offerte',
      wagen_token: wagenToken,
      klant: getKlantData(),
      spoed: false,
      betaling_methode: methode,
      betaling_id: paypalOrderId,
    }),
  });
  const d = await r.json();
  if (d.ok) {
    wagenRegels = []; wagenTotalen = null;
    localStorage.removeItem('mm_wagen_token'); wagenToken = null;
    renderWagenMini();

    // Bevestigingsmail betaling
    fetch(API_MAIL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        actie:       'betaling_bevestiging',
        klant:       getKlantData(),
        regels:      d.regels  || [],
        totalen:     d.totalen || {},
        offerte_nr:  d.offerte_nr || '–',
        betaling_id: paypalOrderId,
      }),
    }).catch(() => {});

    document.getElementById('bev-icon').textContent = '🎉';
    document.getElementById('bev-ttl').textContent  = 'Betaling ontvangen!';
    document.getElementById('bev-sub').textContent  = 'Uw betaling is bevestigd. U ontvangt een bevestigingsmail. De productie start na ontvangst van uw ontwerp.';
    naarStap(8);
  }
}

// ── Stap navigatie ────────────────────────────────────────────────────────────
function naarStap(nr) {
  // Onboarding bij stap 3
  if (nr === 3 && config.product) {
    renderKleuren();
    renderMaten();
  }
  // Techniek stap: reset waarschuwingen
  if (nr === 4) {
    document.getElementById('tech-min-waarsch').classList.remove('toon');
    document.getElementById('tech-borduur-info').classList.remove('toon');
    updateZeefWaarsch();
  }
  // Posities stap: render op basis van techniek
  if (nr === 5) {
    renderPosities();
    renderKleurenSelectie();
    document.getElementById('btn-5').disabled = config.posities.length === 0;
  }
  // Checkout stap: init PayPal
  if (nr === 7) {
    setTimeout(initPayPal, 300);
    document.getElementById('spoed-info').style.display = config.spoed ? 'block' : 'none';
    document.getElementById('paypal-wrap').style.display = config.spoed ? 'none' : 'block';
  }

  config.stap = nr;

  // Toon/verberg stappen
  document.querySelectorAll('.stap').forEach(el => el.classList.remove('actief'));
  document.getElementById('stap-'+nr)?.classList.add('actief');

  // Stepper updaten
  document.querySelectorAll('.step').forEach(el => {
    const s = parseInt(el.dataset.step);
    el.classList.remove('actief','done');
    if (s === nr) el.classList.add('actief');
    else if (s < nr) el.classList.add('done');
  });

  // Scroll naar top
  window.scrollTo({ top: 0, behavior: 'smooth' });
  schedulePrijsUpdate();
}

function toonWagenPanel() {
  if (wagenRegels.length) naarStap(7);
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function n(v)  { return parseFloat(v||0).toFixed(2).replace('.',','); }
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
</body>
</html>
