<?php
// Tijdelijk — achter wachtwoord
if (($_GET['key'] ?? '') !== 'Klaas99') { http_response_code(403); die('403'); }
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Winkelwagen demo — Merch Master</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', system-ui, sans-serif; background: #f5f3ef; padding: 2rem; color: #1a1a1a; }
.demo { display: grid; grid-template-columns: 1fr 380px; gap: 2rem; max-width: 1100px; margin: 0 auto; }
.kaart { background: #fff; border: 1px solid #e8e4dc; border-radius: 12px; padding: 1.5rem; }
h2 { font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: #c4622d; }
.field { margin-bottom: .75rem; }
.field label { display: block; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #7a7670; margin-bottom: .3rem; }
.field input, .field select { width: 100%; padding: .6rem .8rem; border: 1.5px solid #e8e4dc; border-radius: 8px; font-size: .88rem; font-family: inherit; }
.btn { padding: .7rem 1.4rem; background: #c4622d; color: #fff; border: none; border-radius: 50px; font-weight: 700; font-size: .88rem; cursor: pointer; font-family: inherit; }
.skus { font-size: .75rem; color: #7a7670; margin-top: .5rem; }
.log { background: #1a1a1a; color: #86efac; padding: 1rem; border-radius: 8px; font-size: .75rem; font-family: monospace; max-height: 200px; overflow-y: auto; margin-top: 1rem; }

/* Offerte modal */
.modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 100; align-items: center; justify-content: center; }
.modal-backdrop.open { display: flex; }
.modal { background: #fff; border-radius: 14px; padding: 2rem; width: 100%; max-width: 480px; }
.modal h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; }
.modal-acties { display: flex; gap: .5rem; margin-top: 1.25rem; }
.btn-s { padding: .7rem 1.2rem; background: #f5f3ef; color: #1a1a1a; border: 1.5px solid #e8e4dc; border-radius: 50px; font-weight: 600; font-size: .85rem; cursor: pointer; font-family: inherit; }
</style>
</head>
<body>
<h1 style="font-size:1.3rem;font-weight:800;margin-bottom:1.5rem;">Winkelwagen demo</h1>

<div class="demo">
  <!-- Links: product toevoegen -->
  <div>
    <div class="kaart" style="margin-bottom:1rem;">
      <h2>Product toevoegen aan wagen</h2>

      <div class="field">
        <label>SKU</label>
        <input type="text" id="sku" placeholder="bijv. BC150">
        <div class="skus" id="sku-hint">Laden...</div>
      </div>
      <div class="field">
        <label>Techniek</label>
        <select id="techniek">
          <option value="dtf">DTF Transferdruk</option>
          <option value="zeefdruk">Zeefdruk</option>
          <option value="borduren">Borduren (op aanvraag)</option>
        </select>
      </div>
      <div class="field">
        <label>Kleur</label>
        <input type="text" id="kleur_naam" placeholder="bijv. Wit">
      </div>

      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-bottom:1rem;">
        <div class="field" style="margin:0;"><label>S</label><input type="number" min="0" class="maat" data-maat="S" value="0"></div>
        <div class="field" style="margin:0;"><label>M</label><input type="number" min="0" class="maat" data-maat="M" value="0"></div>
        <div class="field" style="margin:0;"><label>L</label><input type="number" min="0" class="maat" data-maat="L" value="5"></div>
        <div class="field" style="margin:0;"><label>XL</label><input type="number" min="0" class="maat" data-maat="XL" value="5"></div>
      </div>

      <div id="posities-demo">
        <label style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7a7670;margin-bottom:.4rem;display:block;">Posities</label>
        <div style="font-size:.75rem;color:#7a7670;margin-bottom:.5rem;">Linker/rechterborst alleen combineerbaar met achterkant.</div>
        <div id="pos-fout-demo" style="display:none;background:#fee2e2;border:1px solid #fca5a5;border-radius:6px;padding:.5rem .75rem;font-size:.78rem;color:#991b1b;margin-bottom:.5rem;"></div>
        <div class="positie-rij" style="display:flex;gap:.5rem;margin-bottom:.4rem;">
          <select class="pos-sel" style="padding:.45rem .6rem;border:1.5px solid #e8e4dc;border-radius:6px;font-family:inherit;font-size:.82rem;" onchange="checkDemoPosCombo()">
            <option>voorkant</option><option>achterkant</option><option>linkerborst</option><option>rechterborst</option>
          </select>
          <select class="kl-sel" style="padding:.45rem .6rem;border:1.5px solid #e8e4dc;border-radius:6px;font-family:inherit;font-size:.82rem;">
            <option>1 kleur</option><option>2 kleuren</option><option>3 kleuren</option><option>4 kleuren</option>
          </select>
        </div>
      </div>
      <button class="btn-s" id="add-pos" style="margin-bottom:1rem;font-size:.78rem;">+ Positie</button>

      <button class="btn" onclick="voegToe()">Toevoegen aan wagen</button>

      <div class="log" id="log">API log...<br></div>
    </div>
  </div>

  <!-- Rechts: winkelwagen -->
  <div class="kaart">
    <div id="wagen-container"></div>
  </div>
</div>

<!-- Offerte modal -->
<div class="modal-backdrop" id="offerte-modal">
  <div class="modal">
    <h3>Offerte aanvragen</h3>
    <div class="field"><label>Naam</label><input type="text" id="klant-naam" placeholder="Jan de Vries"></div>
    <div class="field"><label>E-mail *</label><input type="email" id="klant-email" placeholder="jan@bedrijf.nl"></div>
    <div class="field"><label>Telefoonnummer</label><input type="tel" id="klant-tel" placeholder="+31 6 ..."></div>
    <div class="field"><label>Bedrijf</label><input type="text" id="klant-bedrijf"></div>
    <div id="offerte-spoed-info" style="display:none;background:#fff3cd;border:1px solid #f59e0b;border-radius:6px;padding:.75rem;font-size:.8rem;color:#92400e;margin-bottom:1rem;">
      ⚠ Dit is een spoedorder. Na het aanvragen neemt u contact op via info@merch-master.com voor bevestiging.
    </div>
    <div class="modal-acties">
      <button class="btn" id="btn-offerte-bevestig">Offerte aanvragen</button>
      <button class="btn-s" onclick="document.getElementById('offerte-modal').classList.remove('open')">Annuleren</button>
    </div>
  </div>
</div>

<script src="/bestellen/wagen.js"></script>
<script>
// Initialiseer wagen
MerchWagen.init({ container: '#wagen-container' });

// Luister naar offerte-aanvragen event
document.addEventListener('mm:offerte-aanvragen', (e) => {
    const modal = document.getElementById('offerte-modal');
    const spoedInfo = document.getElementById('offerte-spoed-info');
    spoedInfo.style.display = e.detail.spoed ? 'block' : 'none';
    modal.classList.add('open');
});

// Offerte bevestigen
document.getElementById('btn-offerte-bevestig').addEventListener('click', async () => {
    const klant = {
        naam:    document.getElementById('klant-naam').value,
        email:   document.getElementById('klant-email').value,
        tel:     document.getElementById('klant-tel').value,
        bedrijf: document.getElementById('klant-bedrijf').value,
    };
    const r = await MerchWagen.naarOfferte(klant);
    if (r?.ok) {
        document.getElementById('offerte-modal').classList.remove('open');
        log('Offerte aangemaakt: ' + JSON.stringify(r));
        // Optioneel: open PDF
        if (r.pdf_url) window.open(r.pdf_url, '_blank');
    } else {
        alert(r?.fout || 'Fout bij aanmaken offerte');
    }
});

// Laad SKUs voor demo
async function laadSkus() {
    const r = await fetch('/bestellen/admin/handler.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({action:'catalogus-lijst', admin:true})
    });
    const d = await r.json();
    if (d.ok && d.producten?.length) {
        const hints = d.producten.slice(0,5).map(p => p.sku).join(', ');
        document.getElementById('sku-hint').textContent = 'Eerste SKUs: ' + hints;
        document.getElementById('sku').value = d.producten[0].sku;
    }
}
laadSkus();

// Positie toevoegen
document.getElementById('add-pos').addEventListener('click', () => {
    const wrap = document.getElementById('posities-demo');
    const rijen = wrap.querySelectorAll('.positie-rij').length;
    if (rijen >= 4) { alert('Maximum 4 posities.'); return; }
    const div  = document.createElement('div');
    div.className = 'positie-rij';
    div.style.cssText = 'display:flex;gap:.5rem;margin-bottom:.4rem;';
    div.innerHTML = `
        <select class="pos-sel" style="padding:.45rem .6rem;border:1.5px solid #e8e4dc;border-radius:6px;font-family:inherit;font-size:.82rem;" onchange="checkDemoPosCombo()">
            <option>voorkant</option><option>achterkant</option><option>linkerborst</option><option>rechterborst</option>
        </select>
        <select class="kl-sel" style="padding:.45rem .6rem;border:1.5px solid #e8e4dc;border-radius:6px;font-family:inherit;font-size:.82rem;">
            <option>1 kleur</option><option>2 kleuren</option><option>3 kleuren</option>
        </select>
        <button onclick="this.parentElement.remove();checkDemoPosCombo();" style="padding:.35rem .6rem;border:1.5px solid #fca5a5;border-radius:6px;color:#991b1b;background:#fff;cursor:pointer;font-family:inherit;">–</button>`;
    wrap.insertBefore(div, document.getElementById('add-pos'));
    checkDemoPosCombo();
});

function checkDemoPosCombo() {
    const namen = Array.from(document.querySelectorAll('#posities-demo .pos-sel')).map(s => s.value);
    const foutEl = document.getElementById('pos-fout-demo');
    const heeftVoor  = namen.includes('voorkant');
    const heeftLinks = namen.includes('linkerborst');
    const heeftRechts= namen.includes('rechterborst');
    if (heeftVoor && (heeftLinks || heeftRechts)) {
        foutEl.textContent = 'Linkerborst/rechterborst kunnen niet gecombineerd worden met voorkant.';
        foutEl.style.display = 'block';
        return false;
    }
    foutEl.style.display = 'none';
    return true;
}

async function voegToe() {
    if (!checkDemoPosCombo()) { alert('Corrigeer de positiecombinatie eerst.'); return; }
    const maten = {};
    document.querySelectorAll('.maat').forEach(inp => {
        const v = parseInt(inp.value) || 0;
        if (v > 0) maten[inp.dataset.maat] = v;
    });

    const posities = [];
    document.querySelectorAll('.positie-rij').forEach(rij => {
        const pos = rij.querySelector('.pos-sel')?.value;
        const klStr = rij.querySelector('.kl-sel')?.value || '1';
        const kl = parseInt(klStr) || 1;
        if (pos) posities.push({ positie: pos, kleuren: kl });
    });

    const regel = {
        sku:        document.getElementById('sku').value.trim(),
        techniek:   document.getElementById('techniek').value,
        kleur_naam: document.getElementById('kleur_naam').value.trim(),
        kleur_code: '',
        maten,
        posities,
    };

    log('Toevoegen: ' + JSON.stringify(regel));
    const r = await MerchWagen.toevoegen(regel);
    log('Resultaat: ' + JSON.stringify(r));
}

function log(msg) {
    const el = document.getElementById('log');
    el.innerHTML += msg + '<br>';
    el.scrollTop = el.scrollHeight;
}
</script>
</body>
</html>
