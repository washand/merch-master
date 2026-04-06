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
.si{display:flex;gap:.7rem;padding:.7rem 0;border-bottom:1px solid var(--brd2);}
.si:last-child{border-bottom:none;}
.simg{width:50px;height:50px;border-radius:4px;background:var(--bg);border:1px solid var(--brd);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.2rem;}
.sinfo{flex:1;}
.sname{font-size:.82rem;font-weight:500;}
.sdet{font-size:.73rem;color:var(--ink3);margin-top:.15rem;}
.sprice{font-size:.85rem;font-weight:600;white-space:nowrap;}

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
.ts-dropdown{font-size:.85rem;border-color:var(--brd);border-radius:var(--r);}
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
        </form>
        <div class="cf">
          <button type="submit" form="checkout-form" class="btn btn-p">
            Doorgaan naar betaling
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
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
    const resp = await fetch('/wagen.php?action=laden&token=' + wagen_token);
    const data = await resp.json();
    if (data.ok && data.regels) {
      renderCart(data.regels, data.totalen);
    }
  } catch (e) {
    console.error('Cart load error:', e);
  }
}

function renderCart(regels, totalen) {
  // Items
  let html = '';
  regels.forEach(r => {
    const posMap = {'front':'Voorkant', 'back':'Achterkant', 'both':'Beide kanten', 'left':'Linkerborst', 'right':'Rechterborst', 'left-back':'Linkerborst + Achterkant', 'right-back':'Rechterborst + Achterkant'};
    html += `<div class="si">
      <div class="simg">👕</div>
      <div class="sinfo">
        <div class="sname">${r.mdl.brand} ${r.mdl.name}</div>
        <div class="sdet">${r.clrName} · ${r.qty}× · ${posMap[r.pos] || r.pos}</div>
      </div>
      <div class="sprice">${fmt(r.qty * r.prijs_ex)}</div>
    </div>`;
  });
  document.getElementById('order-items').innerHTML = html;

  // Totals
  let totals = `
    <div class="pr"><span>Subtotaal (ex BTW)</span><span>${fmt(totalen.totaal_ex)}</span></div>
    <div class="pr"><span>BTW 21%</span><span>${fmt(totalen.btw)}</span></div>
    <div class="pr"><span>Totaal incl. BTW</span><span>${fmt(totalen.totaal_incl)}</span></div>
    <div class="pr"><span>Verzending</span><span>+ ${fmt(totalen.verzending)}</span></div>
    <div class="pr div tot"><span>Totaal incl. verzending</span><span>${fmt(totalen.totaal_incl + totalen.verzending)}</span></div>
  `;
  document.getElementById('order-totals').innerHTML = totals;

  // Set cart data
  document.getElementById('cart-data').value = JSON.stringify(regels);

  // PayPal
  if (window.paypal) {
    paypal.Buttons({
      createOrder(data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: { value: (totalen.totaal_incl + totalen.verzending).toFixed(2) }
          }]
        });
      },
      onApprove(data, actions) {
        return actions.order.capture().then(() => {
          document.getElementById('checkout-form').submit();
        });
      }
    }).render('#pp-container');
  }
}

// Init selects
new TomSelect('#dial_ts', {
  options: [
    {text: '🇳🇱 +31', value: '+31'},
    {text: '🇧🇪 +32', value: '+32'},
    {text: '🇩🇪 +49', value: '+49'},
    {text: '🇫🇷 +33', value: '+33'}
  ],
  items: ['+31']
});

new TomSelect('#land', {
  options: [
    {text: '🇳🇱 Nederland', value: 'NL'},
    {text: '🇧🇪 België', value: 'BE'},
    {text: '🇩🇪 Duitsland', value: 'DE'},
    {text: '🇫🇷 Frankrijk', value: 'FR'}
  ],
  items: ['NL']
});

// Load PayPal
const script = document.createElement('script');
script.src = 'https://www.paypal.com/sdk/js?client-id=ASLap52V7_VjYsq3D5k1W9a9RLG7854wBRs9TQ0m0PHhLXALJwrG3i-r4nrQOMuUr0d_Dqr5BSMv4ebk';
document.head.appendChild(script);

loadCart();
</script>
</body>
</html>