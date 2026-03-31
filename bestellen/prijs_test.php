<?php
// Wachtwoord beveiliging — verwijder dit bestand na go-live
if (($_GET['key'] ?? '') !== 'Klaas99') {
    http_response_code(403);
    die('403 Forbidden');
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Prijsberekening test</title>
<style>
body{font-family:monospace;padding:2rem;background:#f5f3ef;font-size:13px;}
.kaart{background:#fff;border:1px solid #e8e4dc;border-radius:8px;padding:1.25rem;margin-bottom:1rem;}
h2{font-size:.9rem;margin-bottom:.75rem;color:#e84c1e;}
.rij{display:flex;gap:.5rem;align-items:center;margin-bottom:.5rem;flex-wrap:wrap;}
label{width:140px;color:#7a7670;flex-shrink:0;}
input,select{padding:.35rem .6rem;border:1.5px solid #e8e4dc;border-radius:6px;font-family:monospace;font-size:12px;}
button{padding:.4rem 1rem;background:#e84c1e;color:#fff;border:none;border-radius:6px;cursor:pointer;font-family:monospace;}
pre{background:#1a1a1a;color:#86efac;padding:1rem;border-radius:6px;font-size:11px;overflow-x:auto;margin-top:.75rem;white-space:pre-wrap;}
.ok{color:#166534;} .err{color:#991b1b;}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.warn{background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.82rem;}
</style>
</head>
<body>
<h1 style="margin-bottom:.5rem;">Merch Master — Prijsberekening test</h1>
<div class="warn">⚠ Dit bestand is alleen toegankelijk met de juiste key. Verwijder het voor go-live.</div>

<div class="kaart">
  <h2>Handmatige test (SKU-based)</h2>
  <div class="rij"><label>SKU</label>
    <input type="text" id="sku" value="" placeholder="bijv. BC150" style="width:180px;">
    <button onclick="zoekSkus()">Laad eerste SKUs uit DB</button>
  </div>
  <div class="rij"><label>Techniek</label>
    <select id="techniek">
      <option value="dtf">DTF</option>
      <option value="zeefdruk">Zeefdruk</option>
      <option value="borduren">Borduren</option>
    </select>
  </div>
  <div class="rij"><label>Aantal</label><input type="number" id="aantal" value="10" min="1"></div>
  <div class="rij"><label>Kleuren (zeefdruk)</label>
    <select id="kleuren"><option>1</option><option>2</option><option>3</option><option>4</option></select>
  </div>
  <button onclick="testHandmatig()">Berekenen</button>
  <pre id="result-handmatig">–</pre>
</div>

<div class="kaart">
  <h2>Beveiligingstest — deze calls moeten FALEN</h2>
  <button onclick="testBeveiliging()">Run beveiligingstests</button>
  <pre id="result-beveiliging">–</pre>
</div>

<h2 style="margin:1rem 0 .5rem;">Automatische scenario tests</h2>
<div class="grid" id="tests"></div>

<script>
const API = '/bestellen/prijs.php';
let eersteSkus = [];

async function zoekSkus() {
  const r = await fetch('/bestellen/admin/handler.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({action:'catalogus-lijst', admin:true})
  });
  const d = await r.json();
  if (d.ok && d.producten?.length) {
    eersteSkus = d.producten.slice(0,8).map(p => p.sku);
    document.getElementById('sku').value = eersteSkus[0] || '';
    alert('SKUs geladen: ' + eersteSkus.join(', '));
  }
}

async function roep(body) {
  const r = await fetch(API, {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(body)
  });
  return await r.json();
}

function fmt(data) {
  if (!data.ok && !data.op_aanvraag) {
    return `<span class="err">FOUT: ${data.fout}${data.suggestie?' → suggestie: '+data.suggestie:''}</span>`;
  }
  if (data.op_aanvraag) return `<span class="ok">Op aanvraag ✓</span>`;
  const v = data.verzend_achteraf ? '⚠ verzending achteraf' : `verzending: €${data.verzend_excl} (${data.verzend_label})`;
  return `<span class="ok">✓ ${data.product_naam}
  Per stuk: €${data.prijs_excl} excl. / €${data.prijs_incl} incl.
  Textiel: €${data.textiel_excl} | Druk: €${data.druk_excl}
  Totaal ${data.aantal}st: €${data.totaal_incl} incl. BTW
  ${v}${data.totaal_incl_verzend ? '\n  Totaal incl. verzend: €'+data.totaal_incl_verzend : ''}
  Tier: ${data.tier} | Oplage: ${data.oplage_label}</span>`;
}

async function testHandmatig() {
  const el = document.getElementById('result-handmatig');
  el.textContent = 'Bezig...';
  const data = await roep({
    techniek: document.getElementById('techniek').value,
    aantal:   parseInt(document.getElementById('aantal').value),
    kleuren:  parseInt(document.getElementById('kleuren').value),
    sku:      document.getElementById('sku').value.trim(),
  });
  el.innerHTML = fmt(data) + '\n\nRaw:\n' + JSON.stringify(data, null, 2);
}

async function testBeveiliging() {
  const el = document.getElementById('result-beveiliging');
  el.textContent = 'Testen...';
  const tests = [
    { label: 'Inkoopprijs meesturen (moet genegeerd worden)',
      body: {techniek:'dtf', aantal:10, sku:'TESTSKU', inkoopprijs: 0.01} },
    { label: 'Tier meesturen als override (moet genegeerd worden)',
      body: {techniek:'dtf', aantal:10, sku:'TESTSKU', tier:'budget'} },
    { label: 'SQL injection in SKU',
      body: {techniek:'dtf', aantal:10, sku:"' OR 1=1--"} },
    { label: 'Lege SKU',
      body: {techniek:'dtf', aantal:10, sku:''} },
    { label: 'Negatief aantal',
      body: {techniek:'dtf', aantal:-5, sku:'TESTSKU'} },
    { label: 'Onbekende techniek',
      body: {techniek:'laser', aantal:10, sku:'TESTSKU'} },
  ];
  let out = '';
  for (const t of tests) {
    const data = await roep(t.body);
    const status = !data.ok ? '✓ Correct geweigerd' : '⚠ ONVERWACHT GESLAAGD';
    out += `${status}: ${t.label}\n  → ${data.fout || JSON.stringify(data)}\n\n`;
  }
  el.textContent = out;
}

// Scenario tests met placeholder SKU (pas aan als je echte SKUs hebt)
const scenarios = [
  { label: 'DTF 1 stuk',          body: {techniek:'dtf',      aantal:1,   sku:'__SKU__'} },
  { label: 'DTF 10 stuks',        body: {techniek:'dtf',      aantal:10,  sku:'__SKU__'} },
  { label: 'DTF 51 stuks',        body: {techniek:'dtf',      aantal:51,  sku:'__SKU__'} },
  { label: 'Zeefdruk 25st 1kleur',body: {techniek:'zeefdruk', aantal:25,  kleuren:1, sku:'__SKU__'} },
  { label: 'Zeefdruk 100st 3kl',  body: {techniek:'zeefdruk', aantal:100, kleuren:3, sku:'__SKU__'} },
  { label: 'Zeefdruk 15st → fout',body: {techniek:'zeefdruk', aantal:15,  kleuren:1, sku:'__SKU__'} },
  { label: 'Borduren → aanvraag', body: {techniek:'borduren', aantal:50,  sku:'__SKU__'} },
  { label: 'SKU bestaat niet',    body: {techniek:'dtf',      aantal:10,  sku:'BESTAAT_NIET_XYZ'} },
];

async function runTests(sku) {
  const wrap = document.getElementById('tests');
  wrap.innerHTML = '';
  for (const s of scenarios) {
    const div = document.createElement('div');
    div.className = 'kaart';
    div.innerHTML = `<h2>${s.label}</h2><pre>Laden...</pre>`;
    wrap.appendChild(div);
    const body = {...s.body, sku: s.body.sku === '__SKU__' ? sku : s.body.sku};
    const data = await roep(body);
    div.querySelector('pre').innerHTML = fmt(data);
  }
}

// Laad SKUs en run tests zodra pagina laadt
window.addEventListener('load', async () => {
  await zoekSkus();
  if (eersteSkus.length) runTests(eersteSkus[0]);
});
</script>
</body>
</html>
