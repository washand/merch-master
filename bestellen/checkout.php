<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/../includes/taal.php';

// Get wagen_token from session (set by bestellen.php via wagen.php)
$wagen_token = $_SESSION['mm_wagen_token'] ?? $_GET['wagen_token'] ?? '';
if(empty($wagen_token)){
  header('Location: /bestellen.php');
  exit;
}

$klantType = $_SESSION['mm_klantType'] ?? 'particulier';
$opmerkingen = $_SESSION['mm_opmerkingen'] ?? '';
$klant = $_SESSION['mm_klant'] ?? [];
$isLoggedIn = !empty($_SESSION['mm_klant_id']);

function fmt($val) {
  return '€' . number_format((float)$val, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Afrekenen - Merch Master</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
<style>
:root{
  --ac:#e84c1e; --ac2:#c73d14;
  --ink:#1a1816; --ink2:#3d3a36; --ink3:#7a7670;
  --bg:#f5f3f0; --sur:#fff; --brd:#e2ddd5; --brd2:#ede9e3;
  --r:6px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter',-apple-system,sans-serif;background:var(--bg);color:var(--ink);font-size:14px;line-height:1.5;}

/* ─── HEADER ─── */
.hdr{background:var(--sur);border-bottom:1px solid var(--brd);padding:0 2rem;height:60px;display:flex;align-items:center;justify-content:space-between;}
.logo{font-size:1.25rem;font-weight:700;letter-spacing:-.03em;}
.logo em{color:var(--ac);font-style:normal;}
.badge{display:flex;align-items:center;gap:.35rem;font-size:.72rem;color:#166534;background:#f0fdf4;border:1px solid #bbf7d0;padding:.25rem .65rem;border-radius:20px;}

/* ─── WRAP ─── */
.wrap{max-width:1060px;margin:0 auto;padding:1.75rem 1rem 4rem;}
.back{display:inline-flex;align-items:center;gap:.35rem;font-size:.8rem;color:var(--ink3);text-decoration:none;margin-bottom:1.5rem;}
.back:hover{color:var(--ink);}

/* ─── STEPS ─── */
.steps{display:flex;align-items:center;margin-bottom:2rem;}
.step{display:flex;align-items:center;gap:.5rem;}
.sc{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:600;border:2px solid var(--brd);color:var(--ink3);background:var(--sur);transition:.2s;}
.step.done .sc,.step.active .sc{background:var(--ac);border-color:var(--ac);color:#fff;}
.sl{font-size:.82rem;font-weight:500;color:var(--ink3);}
.step.active .sl,.step.done .sl{color:var(--ink);}
.sline{flex:1;height:2px;background:var(--brd);margin:0 .75rem;max-width:72px;}
.step.done ~ .sline{background:var(--ac);}

/* ─── GRID ─── */
.grid{display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start;}

/* ─── CARD ─── */
.card{background:var(--sur);border:1px solid var(--brd);border-radius:var(--r);overflow:hidden;}
.ch{padding:1rem 1.25rem;border-bottom:1px solid var(--brd2);display:flex;align-items:center;gap:.5rem;}
.ch h2{font-size:.95rem;font-weight:600;}
.ch svg{color:var(--ac);}
.cb{padding:1.25rem;}
.cf{padding:.9rem 1.25rem;border-top:1px solid var(--brd2);display:flex;justify-content:flex-end;gap:.75rem;}

/* ─── FORM ─── */
.fr2{display:grid;grid-template-columns:1fr 1fr;gap:.875rem;margin-bottom:.875rem;}
.fg{margin-bottom:.875rem;}
.fg:last-child{margin-bottom:0;}
label{display:block;font-size:.75rem;font-weight:500;color:var(--ink2);margin-bottom:.3rem;}
input,select,textarea{width:100%;padding:.5rem .75rem;border:1px solid var(--brd);border-radius:var(--r);font-size:.85rem;font-family:inherit;color:var(--ink);background:#fafaf9;outline:none;transition:.15s;}
input:focus,select:focus{border-color:var(--ac);box-shadow:0 0 0 3px rgba(232,76,30,.1);background:#fff;}
input.filled{background:#fff;}

/* ─── BUTTONS ─── */
.btn{padding:.55rem 1.1rem;border:none;border-radius:var(--r);font-size:.85rem;font-weight:500;font-family:inherit;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;transition:.15s;}
.btn-p{background:var(--ac);color:#fff;width:100%;justify-content:center;padding:.75rem;}
.btn-p:hover{background:var(--ac2);}
.btn-o{background:#fff;border:1px solid var(--brd);color:var(--ink);}
.btn-o:hover{background:var(--bg);}

/* ─── SUMMARY ITEMS ─── */
.si{display:flex;gap:1rem;padding:1rem 0;border-bottom:1px solid var(--brd2);}
.si:last-child{border-bottom:none;}
.simg{width:80px;height:100px;border-radius:6px;background:var(--bg);border:1px solid var(--brd);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.8rem;}
.sinfo{flex:1;}
.sname{font-size:.9rem;font-weight:600;color:var(--ink);}
.sdet{font-size:.75rem;color:var(--ink3);margin-top:.3rem;line-height:1.5;}
.sprice{font-size:.9rem;font-weight:700;color:var(--ac);white-space:nowrap;text-align:right;min-width:60px;}

/* ─── PRICE TABLE ─── */
.pt .pr{display:flex;justify-content:space-between;padding:.3rem 0;font-size:.85rem;color:var(--ink2);}
.pt .pr.div{border-top:1px solid var(--brd2);margin-top:.35rem;padding-top:.6rem;}
.pt .pr.tot{font-size:.95rem;font-weight:700;}
.pt .pr.tot span:first-child{color:var(--ink);}
.pt .pr.tot span:last-child{color:var(--ac);}

/* ─── TRUST ─── */
.trust{background:var(--sur);border:1px solid var(--brd);border-radius:var(--r);padding:.9rem 1.1rem;display:flex;flex-direction:column;gap:.5rem;}
.ti{display:flex;align-items:center;gap:.55rem;font-size:.76rem;color:var(--ink3);}
.tico{width:24px;height:24px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;flex-shrink:0;}

.ts-wrapper .ts-control{border:1px solid var(--brd);border-radius:var(--r);padding:.5rem .75rem;font-size:.85rem;background:#fafaf9;}
.ts-wrapper.focus .ts-control{border-color:var(--ac);box-shadow:0 0 0 3px rgba(232,76,30,.1);background:#fff;}
.ts-dropdown{font-size:.85rem;border-color:var(--brd);border-radius:var(--r);z-index:1000;}
.ts-dropdown .option.selected{background:var(--ac);color:#fff;}
.ts-dropdown .option:hover{background:var(--bg);}

@media(max-width:720px){
  .grid{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<header class="hdr">
  <div class="logo">merch<em>master</em></div>
  <div class="badge">
    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    SSL beveiligde checkout
  </div>
</header>

<div class="wrap">
  <a href="/bestellen.php" class="back">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
    Terug naar winkelwagen
  </a>

  <div class="steps">
    <div class="step active">
      <div class="sc">1</div>
      <span class="sl">Jouw gegevens</span>
    </div>
    <div class="sline"></div>
    <div class="step">
      <div class="sc">2</div>
      <span class="sl">Betaling</span>
    </div>
    <div class="sline"></div>
    <div class="step">
      <div class="sc">3</div>
      <span class="sl">Bevestiging</span>
    </div>
  </div>

  <div class="grid">
    <!-- LEFT: FORM -->
    <div>
      <div class="card">
        <div class="ch">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <h2>Jouw gegevens</h2>
        </div>
        <form id="checkout-form" class="cb" method="POST" action="/bestellen/handler.php">
          <input type="hidden" name="action" value="bestelling">
          <input type="hidden" id="cart-data" name="cart_data" value="">

          <div class="fr2">
            <div class="fg">
              <label>Voornaam *</label>
              <input type="text" name="voornaam" id="voornaam" required value="<?php echo htmlspecialchars($klant['voornaam'] ?? ''); ?>">
            </div>
            <div class="fg">
              <label>Achternaam *</label>
              <input type="text" name="achternaam" id="achternaam" required value="<?php echo htmlspecialchars($klant['achternaam'] ?? ''); ?>">
            </div>
          </div>

          <div class="fr2">
            <div class="fg">
              <label>E-mailadres *</label>
              <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($klant['email'] ?? ''); ?>">
            </div>
            <div class="fg">
              <label>Telefoon</label>
              <div style="display:flex;gap:.4rem;">
                <select id="dial_ts" style="width:85px;flex-shrink:0;padding-right:.3rem;"></select>
                <input type="hidden" name="telefoon_landcode" id="telefoon_landcode" value="+31">
                <input type="tel" name="telefoon" id="telefoon" value="<?php echo htmlspecialchars(preg_replace('/^\+\d+/', '', $klant['telefoon'] ?? '')); ?>" style="flex:1;">
              </div>
            </div>
          </div>

          <div class="fg">
            <label>Straat en huisnummer *</label>
            <input type="text" name="straat" id="straat" required value="<?php echo htmlspecialchars($klant['straat'] ?? ''); ?>">
          </div>

          <div class="fr2">
            <div class="fg">
              <label>Postcode *</label>
              <input type="text" name="postcode" id="postcode" required value="<?php echo htmlspecialchars($klant['postcode'] ?? ''); ?>" placeholder="1234 AB">
            </div>
            <div class="fg">
              <label>Plaatsnaam *</label>
              <input type="text" name="stad" id="stad" required value="<?php echo htmlspecialchars($klant['stad'] ?? ''); ?>">
            </div>
          </div>

          <div class="fg">
            <label>Land</label>
            <select name="land" id="land"></select>
          </div>

          <div class="fr2" style="margin-bottom:0;">
            <div class="fg">
              <label>Bedrijfsnaam (optioneel)</label>
              <input type="text" name="bedrijf" value="<?php echo htmlspecialchars($klant['bedrijf'] ?? ''); ?>">
            </div>
            <div class="fg">
              <label>KvK-nummer (optioneel)</label>
              <input type="text" name="kvk" value="<?php echo htmlspecialchars($klant['kvk'] ?? ''); ?>">
            </div>
          </div>

          <div id="pp-container"></div>
          <button class="btn btn-p" id="test-btn" style="margin-top:1rem;background:#6c5ce7;">
            Test Payment
          </button>

          <!-- Hidden fields for totals -->
          <input type="hidden" name="totaal_incl" id="totaal_incl" value="0">
          <input type="hidden" name="verzending_incl" id="verzending_incl" value="0">
        </form>
      </div>
    </div>

    <!-- RIGHT: SUMMARY -->
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <div class="card">
        <div class="ch">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <h2>Jouw bestelling</h2>
        </div>
        <div class="cb">
          <div id="order-items">Laden...</div>
          <div class="pt" id="order-totals" style="margin-top:.75rem;">Laden...</div>
        </div>
      </div>

      <div class="trust">
        <div class="ti"><div class="tico">🔒</div><span>256-bit SSL encryptie</span></div>
        <div class="ti"><div class="tico">💳</div><span>Veilig betalen via PayPal</span></div>
        <div class="ti"><div class="tico">🚚</div><span>Levering binnen 5–7 werkdagen</span></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
const wagen_token = '<?php echo htmlspecialchars($wagen_token); ?>';

async function loadCart() {
  try {
    console.log('loadCart: fetching with token:', wagen_token);
    const resp = await fetch('./wagen.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({actie: 'laden', wagen_token: wagen_token})
    });
    const data = await resp.json();
    console.log('loadCart: response:', data);
    if (data.ok && data.regels) {
      renderCart(data.regels, data.totalen);
    } else {
      console.error('loadCart: response not ok or missing regels', data);
      document.getElementById('order-items').innerHTML = '<div style="color:var(--ink3);font-size:.85rem;">Fout bij laden wagen</div>';
    }
  } catch (e) {
    console.error('Cart load error:', e);
    document.getElementById('order-items').innerHTML = '<div style="color:red;font-size:.85rem;">Error: ' + e.message + '</div>';
  }
}

function renderCart(regels, totalen) {
  try {
    console.log('renderCart called with:', {regels, totalen});

    if (!totalen) {
      document.getElementById('order-items').innerHTML = '<div style="color:var(--ink3);font-size:.85rem;">Wagen is leeg</div>';
      document.getElementById('order-totals').innerHTML = '';
      return;
    }

    // Store totals globally for form submission
    window.TOTALEN = totalen;
    console.log('window.TOTALEN set:', window.TOTALEN);

    // Items
    let html = '';
    regels.forEach(r => {
      try {
        // Build position label from posities array
        const posLabels = {
          'voorkant': 'Voorkant',
          'achterkant': 'Achterkant',
          'linkerborst': 'Linkerborst',
          'rechterborst': 'Rechterborst'
        };
        const posLabel = (r.posities || []).map(p => posLabels[p.positie] || p.positie).join(' + ') || 'Voorkant';
        const totalPrice = (r.prijs && r.prijs.prijs_excl) ? r.aantal * r.prijs.prijs_excl : 0;

        // Build techniek label per positie
        const posMap = {'voorkant': 'Voorkant', 'achterkant': 'Achterkant', 'linkerborst': 'Linkerborst', 'rechterborst': 'Rechterborst'};
        const techMap = {'dtf': 'DTF', 'zeefdruk': 'Zeefdruk', 'zeef': 'Zeefdruk', 'borduren': 'Borduren'};
        let techDetails = '';

        if (r.technieken && r.technieken.length > 0) {
          const techByPos = {};
          r.technieken.forEach(t => {
            const pos = t.positie || 'voorkant';
            const tech = techMap[t.techniek] || t.techniek;
            techByPos[pos] = tech;
          });
          const techLines = Object.entries(techByPos).map(([pos, tech]) => `${posMap[pos] || pos}: ${tech}`);
          techDetails = techLines.join('<br>');
        } else if (r.techniek) {
          techDetails = techMap[r.techniek] || r.techniek;
        }

        // Build maten display
        let matenLabel = '';
        if (r.maten && Object.keys(r.maten).length > 0) {
          const matenMap = {
            'xs': 'XS', 's': 'S', 'm': 'M', 'l': 'L', 'xl': 'XL', 'xxl': 'XXL', 'xxxl': '3XL',
            '0': 'XS', '1': 'S', '2': 'M', '3': 'L', '4': 'XL', '5': 'XXL', '6': '3XL'
          };
          const matenArray = [];
          Object.entries(r.maten).forEach(([size, qty]) => {
            if (qty > 0) {
              const sizeName = matenMap[size] || size;
              matenArray.push(`${qty}× ${sizeName}`);
            }
          });
          if (matenArray.length > 0) {
            matenLabel = matenArray.join(', ');
          }
        }

        // Product image (fallback if kleur_image_url is missing)
        const imgSrc = r.kleur_image_url || '/bestellen/img/placeholder.png';

        html += `<div class="si">
          <div class="simg" style="background-size:cover;background-position:center;background-image:url('${imgSrc}');"></div>
          <div class="sinfo">
            <div class="sname" style="font-size:.95rem;font-weight:600;color:var(--ink);">${r.aantal}× ${r.product_naam || 'Product'}</div>
            <div class="sdet" style="margin-top:.45rem;font-size:.75rem;color:var(--ink3);line-height:1.6;">
              <div style="margin-bottom:.2rem;"><strong>SKU:</strong> ${r.sku || '–'}</div>
              <div style="margin-bottom:.2rem;"><strong>Kleur:</strong> ${r.kleur_naam || '–'}</div>
              ${matenLabel ? `<div style="margin-bottom:.2rem;"><strong>Maten:</strong> ${matenLabel}</div>` : ''}
              <div style="margin-bottom:.2rem;"><strong>Positie:</strong> ${posLabel}</div>
              <div style="margin-bottom:.2rem;"><strong>Techniek:</strong><br>${techDetails}</div>
            </div>
          </div>
          <div class="sprice" style="white-space:nowrap;text-align:right;">${fmt(totalPrice)}</div>
        </div>`;
      } catch(e) {
        console.error('Error rendering regel:', r, e);
      }
    });
    document.getElementById('order-items').innerHTML = html;

    // Totals
    const verzending = totalen.verzend_incl || 0;
    const totalMet = totalen.totaal_incl + verzending;
    let totals = `
      <div class="pr"><span>Subtotaal (ex BTW)</span><span>${fmt(totalen.subtotaal_excl)}</span></div>
      <div class="pr"><span>BTW 21%</span><span>${fmt(totalen.btw)}</span></div>
      <div class="pr"><span>Totaal incl. BTW</span><span>${fmt(totalen.totaal_incl)}</span></div>
      <div class="pr"><span>Verzending</span><span>+ ${fmt(verzending)}</span></div>
      <div class="pr div tot"><span>Totaal incl. verzending</span><span>${fmt(totalMet)}</span></div>
    `;
    document.getElementById('order-totals').innerHTML = totals;

    // Set cart data
    document.getElementById('cart-data').value = JSON.stringify(regels);

    // PayPal
    console.log('Initializing PayPal, window.paypal:', !!window.paypal);
    if (window.paypal) {
      try {
        paypal.Buttons({
          createOrder(data, actions) {
            return actions.order.create({
              purchase_units: [{
                amount: { value: totalMet.toFixed(2) }
              }]
            });
          },
          onApprove(data, actions) {
            return actions.order.capture().then(() => {
              document.getElementById('checkout-form').submit();
            });
          },
          onError: (err) => {
            console.error('PayPal error:', err);
            document.getElementById('test-btn').style.display = 'block';
          }
        }).render('#pp-container');
        console.log('PayPal buttons rendered');
      } catch (err) {
        console.error('PayPal render error:', err);
        document.getElementById('test-btn').style.display = 'block';
      }
    } else {
      console.warn('PayPal SDK not available, showing test button');
      document.getElementById('test-btn').style.display = 'block';
    }
  } catch(e) {
    console.error('renderCart error:', e);
    document.getElementById('order-items').innerHTML = '<div style="color:red;font-size:.85rem;">Render error: ' + e.message + '</div>';
  }
}

// Format price (€ with Dutch decimals)
function fmt(val) {
  const num = parseFloat(val) || 0;
  return '€' + num.toFixed(2).replace('.', ',');
}

// Landen met codes en vlaggen
const LANDEN = [
  {iso:'NL',dial:'+31',name:'Nederland'},
  {iso:'BE',dial:'+32',name:'België'},
  {iso:'DE',dial:'+49',name:'Duitsland'},
  {iso:'FR',dial:'+33',name:'Frankrijk'},
  {iso:'AT',dial:'+43',name:'Oostenrijk'},
  {iso:'CH',dial:'+41',name:'Zwitserland'},
  {iso:'PL',dial:'+48',name:'Polen'},
  {iso:'CZ',dial:'+420',name:'Tsjechië'},
  {iso:'SE',dial:'+46',name:'Zweden'},
  {iso:'NO',dial:'+47',name:'Noorwegen'},
  {iso:'DK',dial:'+45',name:'Denemarken'},
  {iso:'FI',dial:'+358',name:'Finland'},
  {iso:'IT',dial:'+39',name:'Italië'},
  {iso:'ES',dial:'+34',name:'Spanje'},
  {iso:'PT',dial:'+351',name:'Portugal'},
  {iso:'GR',dial:'+30',name:'Griekenland'},
  {iso:'HU',dial:'+36',name:'Hongarije'},
  {iso:'RO',dial:'+40',name:'Roemenië'},
  {iso:'BG',dial:'+359',name:'Bulgarije'},
  {iso:'HR',dial:'+385',name:'Kroatië'},
  {iso:'IE',dial:'+353',name:'Ierland'},
  {iso:'GB',dial:'+44',name:'Groot-Brittannië'},
  {iso:'UA',dial:'+380',name:'Oekraïne'},
  {iso:'TR',dial:'+90',name:'Turkije'},
  {iso:'US',dial:'+1',name:'Verenigde Staten'},
  {iso:'CA',dial:'+1',name:'Canada'}
];

// Vlaggen-set (Europese landen + VS + Canada)
const MET_VLAG = new Set(['NL','BE','DE','FR','AT','CH','PL','CZ','SE','NO','DK','FI','IT','ES','PT','GR','HU','RO','BG','HR','IE','GB','UA','TR','US','CA']);

// Telefoon landcode dropdown
const dialOptions = LANDEN.map(l => ({ value: l.dial, iso: l.iso, name: l.name }));
new TomSelect('#dial_ts', {
  options: dialOptions,
  valueField: 'value',
  labelField: 'name',
  searchField: ['name', 'value'],
  items: ['+31'],
  maxOptions: 300,
  render: {
    option: (d) => {
      const flag = MET_VLAG.has(d.iso) ? [...d.iso].map(c => String.fromCodePoint(c.charCodeAt(0)+127397)).join('') + ' ' : '';
      return `<div>${flag}<strong>${d.value}</strong> ${d.name}</div>`;
    },
    item: (d) => {
      const flag = MET_VLAG.has(d.iso) ? [...d.iso].map(c => String.fromCodePoint(c.charCodeAt(0)+127397)).join('') + ' ' : '';
      return `<div>${flag}${d.value}</div>`;
    }
  },
  onChange: (val) => {
    document.getElementById('telefoon_landcode').value = val || '+31';
  }
});

// Land dropdown
const landOptions = LANDEN.map(l => ({ value: l.iso, name: l.name }));
new TomSelect('#land', {
  options: landOptions,
  valueField: 'value',
  labelField: 'name',
  searchField: ['name'],
  items: ['NL'],
  maxOptions: 300,
  render: {
    option: (d) => {
      const flag = MET_VLAG.has(d.value) ? [...d.value].map(c => String.fromCodePoint(c.charCodeAt(0)+127397)).join('') + ' ' : '';
      return `<div>${flag}${d.name}</div>`;
    },
    item: (d) => {
      const flag = MET_VLAG.has(d.value) ? [...d.value].map(c => String.fromCodePoint(c.charCodeAt(0)+127397)).join('') : '';
      return `<div>${flag}${d.name}</div>`;
    }
  }
});

// ── Postcode validatie (Nederlands format) ───────────────────────────────
const postcodeEl = document.getElementById('postcode');
const landEl = document.getElementById('land');
const NL_POSTCODE_RE = /^([1-9][0-9]{3})\s?([A-Za-z]{2})$/;

function isNederland() {
  const landTS = landEl.tomselect;
  const val = landTS ? landTS.getValue() : landEl.value;
  return val === 'NL';
}

postcodeEl.addEventListener('blur', () => {
  if (!isNederland()) return;
  const match = postcodeEl.value.trim().match(NL_POSTCODE_RE);
  if (match) {
    postcodeEl.value = match[1] + ' ' + match[2].toUpperCase();
  }
});

// ── Form validatie ───────────────────────────────────────────────────────
function validateForm() {
  const voornaam = document.getElementById('voornaam').value.trim();
  const achternaam = document.getElementById('achternaam').value.trim();
  const email = document.getElementById('email').value.trim();
  const straat = document.getElementById('straat').value.trim();
  const postcode = document.getElementById('postcode').value.trim();
  const stad = document.getElementById('stad').value.trim();

  // Check required velden
  if (!voornaam) { alert('Vul je voornaam in'); return false; }
  if (!achternaam) { alert('Vul je achternaam in'); return false; }
  if (!email) { alert('Vul je e-mailadres in'); return false; }
  if (!straat) { alert('Vul straat en huisnummer in'); return false; }
  if (!postcode) { alert('Vul je postcode in'); return false; }
  if (!stad) { alert('Vul je plaatsnaam in'); return false; }

  // Postcode validatie als Nederland
  if (isNederland()) {
    if (!NL_POSTCODE_RE.test(postcode)) {
      alert('Vul een geldige Nederlandse postcode in (bijv. 1234 AB)');
      return false;
    }
  }

  return true;
}

// Load PayPal SDK with onload callback
const script = document.createElement('script');
script.src = 'https://www.paypal.com/sdk/js?client-id=ASLap52V7_VjYsq3D5k1W9a9RLG7854wBRs9TQ0m0PHhLXALJwrG3i-r4nrQOMuUr0d_Dqr5BSMv4ebk';
script.onload = () => {
  console.log('PayPal SDK loaded, calling loadCart');
  loadCart();
};
script.onerror = () => {
  console.warn('PayPal SDK failed to load, showing test button and calling loadCart');
  document.getElementById('test-btn').style.display = 'block';
  loadCart();
};
document.head.appendChild(script);

// Fallback: call loadCart after 3 seconds if PayPal hasn't loaded
setTimeout(() => {
  if (!document.getElementById('pp-container').innerHTML) {
    console.warn('PayPal buttons not rendered after 3s, showing test button');
    document.getElementById('test-btn').style.display = 'block';
  }
}, 3000);

// Test button handler
document.getElementById('test-btn').addEventListener('click', async (e) => {
  e.preventDefault();
  console.log('Test button clicked');

  // Validate form first
  if (!validateForm()) {
    return;
  }

  // Populate totals before submit
  if (window.TOTALEN) {
    const verzending = window.TOTALEN.verzend_incl || 0;
    const totalMet = window.TOTALEN.totaal_incl + verzending;

    document.getElementById('totaal_incl').value = window.TOTALEN.totaal_incl.toFixed(2);
    document.getElementById('verzending_incl').value = verzending.toFixed(2);

    console.log('Form totals set:', {
      totaal_incl: window.TOTALEN.totaal_incl,
      verzending_incl: verzending,
      totalMet: totalMet
    });
  }

  // Submit form via fetch to handle response
  try {
    const formData = new FormData(document.getElementById('checkout-form'));
    const response = await fetch('/bestellen/handler.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();
    console.log('Handler response:', result);

    if (result.success) {
      // Redirect to success page with order ID and email
      const email = document.getElementById('email').value;
      const successUrl = '/bestellen/success.php?id=' + encodeURIComponent(result.bestelling_id || 'test') + '&email=' + encodeURIComponent(email);
      console.log('Success! Redirecting to:', successUrl);
      window.location.href = successUrl;
    } else {
      console.error('Order failed:', result.error);
      alert('Fout: ' + (result.error || 'Onbekende fout'));
    }
  } catch (err) {
    console.error('Form submission error:', err);
    alert('Fout bij verzenden: ' + err.message);
  }
});
</script>
</body>
</html>