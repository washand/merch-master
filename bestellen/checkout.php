<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/../includes/taal.php';

// Get wagen_token from session (set by bestellen.php via wagen.php)
$wagen_token = $_SESSION['mm_wagen_token'] ?? $_GET['wagen_token'] ?? '';
if(empty($wagen_token)){
  // Redirect back to tool if no cart
  header('Location: /bestellen.php');
  exit;
}
$cart = []; // Will be loaded via JavaScript/wagen.php

$klantType = $_SESSION['mm_klantType'] ?? 'particulier';
$opmerkingen = $_SESSION['mm_opmerkingen'] ?? '';
$klant = $_SESSION['mm_klant'] ?? [];
$isLoggedIn = !empty($_SESSION['mm_klant_id']);

// Helper function: format price
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
  <style>
    /* Tom Select aanpassen aan form stijl */
    .ts-wrapper .ts-control {
      border: 1px solid var(--border, #ddd);
      border-radius: 3px;
      padding: 0.5rem 0.7rem;
      font-size: 0.9rem;
      font-family: inherit;
      box-shadow: none;
    }
    .ts-wrapper.focus .ts-control { border-color: var(--accent, #e84c1e); box-shadow: 0 0 0 2px rgba(232,76,30,.15); }
    .ts-dropdown { font-size: 0.9rem; font-family: inherit; border-color: var(--border, #ddd); }
    .ts-dropdown .option.selected { background: var(--accent, #e84c1e); }
    .ts-dropdown .option:hover, .ts-dropdown .option.active { background: #fdf1ee; color: var(--ink, #1a1816); }
  </style>
  <style>
    .checkout-main {
      max-width: 900px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    .checkout-main h1 {
      font-size: 2rem;
      margin-bottom: 2rem;
      color: var(--ink);
    }
    .section {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .section h2 {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--ink);
      margin-bottom: 1rem;
      border-bottom: 1px solid var(--border);
      padding-bottom: 0.75rem;
    }
    .summary-item {
      padding: 0.75rem 0;
      border-bottom: 1px solid #f0ede5;
    }
    .summary-item:last-child {
      border-bottom: none;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
    }
    .summary-row .label {
      color: var(--ink3);
      font-size: 0.9rem;
    }
    .summary-row .value {
      font-weight: 500;
      color: var(--ink);
    }
    .price-section {
      background: #f9f8f6;
      padding: 1rem;
      border-radius: 3px;
      margin-top: 1rem;
    }
    .price-row {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 0;
      font-size: 0.95rem;
    }
    .price-row.total {
      border-top: 2px solid var(--border);
      padding-top: 1rem;
      margin-top: 1rem;
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--accent);
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 500;
      color: var(--ink);
      margin-bottom: 0.4rem;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.6rem 0.8rem;
      border: 1px solid var(--border);
      border-radius: 3px;
      font-size: 0.9rem;
      font-family: inherit;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }
    .payment-buttons {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      margin-top: 1.5rem;
    }
    .btn {
      padding: 0.75rem 1rem;
      border: none;
      border-radius: 3px;
      font-size: 0.95rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .btn-primary {
      background: var(--accent);
      color: #fff;
      width: 100%;
    }
    .btn-primary:hover {
      background: #d43b0e;
    }
    #pp-container {
      margin: 1rem 0;
    }
    .error {
      color: #e74c3c;
      font-size: 0.85rem;
      margin-top: 0.3rem;
    }
    .notes-display {
      background: #f9f8f6;
      padding: 0.75rem;
      border-left: 3px solid var(--accent);
      font-style: italic;
      color: var(--ink3);
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <main class="checkout-main">
    <h1>Afrekenen</h1>

    <!-- Order Summary -->
    <div class="section">
      <h2>Jouw bestelling</h2>
      <?php if(!empty($cart)): ?>
        <?php foreach($cart as $item): ?>
          <div class="summary-item">
            <div class="summary-row">
              <span class="label"><strong><?php echo htmlspecialchars($item['mdl']['brand'] . ' ' . $item['mdl']['name']); ?></strong></span>
              <span class="value">€<?php echo number_format($item['prijs_ex'], 2, ',', '.'); ?> ex BTW</span>
            </div>
            <div class="summary-row">
              <span class="label"><?php echo htmlspecialchars($item['clrName']); ?> • <?php echo htmlspecialchars($item['pos']); ?> • <?php echo intval($item['qty']); ?> stuks</span>
              <span class="value"><?php echo fmt($item['qty'] * $item['prijs_ex']); ?> ex</span>
            </div>
            <?php if($item['techA']): ?>
              <div class="summary-row">
                <span class="label"><?php echo ucfirst($item['techA']); ?> voorkant</span>
                <span class="value"><?php echo fmt($item['drukA']); ?></span>
              </div>
            <?php endif; ?>
            <?php if($item['techB'] && $item['pos'] === 'both'): ?>
              <div class="summary-row">
                <span class="label"><?php echo ucfirst($item['techB']); ?> achterkant</span>
                <span class="value"><?php echo fmt($item['drukB']); ?></span>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Price Display -->
      <div class="price-section">
        <div class="price-row">
          <span>Subtotaal (ex BTW):</span>
          <span id="total-ex">€0,00</span>
        </div>
        <div class="price-row">
          <span>BTW (21%):</span>
          <span id="total-btw">€0,00</span>
        </div>
        <div class="price-row total">
          <span>Totaal incl. BTW:</span>
          <span id="total-incl">€0,00</span>
        </div>
      </div>

      <!-- Notes (if any) -->
      <?php if(!empty($opmerkingen)): ?>
        <div style="margin-top: 1rem;">
          <strong style="font-size: 0.9rem; color: var(--ink);">Opmerkingen:</strong>
          <div class="notes-display"><?php echo htmlspecialchars($opmerkingen); ?></div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Customer Data Form -->
    <form id="checkout-form" class="section" method="POST" action="/bestellen/handler.php">
      <h2>Jouw gegevens</h2>

      <input type="hidden" name="action" value="bestelling">
      <input type="hidden" id="cart-data" name="cart_data" value="">

      <div class="form-row">
        <div class="form-group">
          <label>Voornaam *</label>
          <input type="text" name="voornaam" id="voornaam" required value="<?php echo htmlspecialchars($klant['voornaam'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>Achternaam *</label>
          <input type="text" name="achternaam" id="achternaam" required value="<?php echo htmlspecialchars($klant['achternaam'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($klant['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>Telefoon</label>
          <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
            <div style="flex: 0 0 160px;">
              <select name="telefoon_landcode" id="telefoon_landcode"></select>
            </div>
            <input type="tel" name="telefoon" id="telefoon" placeholder="6 12345678" value="<?php echo htmlspecialchars(preg_replace('/^\+\d+/', '', $klant['telefoon'] ?? '')); ?>" style="flex: 1; padding: 0.6rem 0.8rem; border: 1px solid var(--border); border-radius: 3px; font-size: 0.9rem; font-family: inherit;">
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Straat en huisnummer *</label>
          <input type="text" name="straat" id="straat" required value="<?php echo htmlspecialchars($klant['straat'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>Postcode *</label>
          <input type="text" name="postcode" id="postcode" required value="<?php echo htmlspecialchars($klant['postcode'] ?? ''); ?>" placeholder="1234 AB" autocomplete="postal-code">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Plaats *</label>
          <input type="text" name="stad" id="stad" required value="<?php echo htmlspecialchars($klant['stad'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>Land</label>
          <select name="land" id="land"></select>
        </div>
      </div>

      <!-- Optional business fields -->
      <div class="form-row">
        <div class="form-group">
          <label>Bedrijfsnaam (optioneel)</label>
          <input type="text" name="bedrijf" id="bedrijf" value="<?php echo htmlspecialchars($klant['bedrijf'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>BTW-nummer (optioneel)</label>
          <input type="text" name="btw_nummer" id="btw_nummer" value="<?php echo htmlspecialchars($klant['btw_nummer'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <label>KvK-nummer (optioneel)</label>
        <input type="text" name="kvk" id="kvk" value="<?php echo htmlspecialchars($klant['kvk'] ?? ''); ?>">
      </div>
    </form>

    <!-- Payment Section -->
    <div class="section">
      <h2>Betaalmethode</h2>

      <!-- PayPal Container -->
      <div id="pp-container"></div>

      <!-- Fallback payment button for testing -->
      <button class="btn btn-primary" id="submit-btn" style="background: #6c5ce7; margin-top: 1rem;">
        💳 Test Payment
      </button>

      <div style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: var(--ink3);">
        🔒 Beveiligde betaling via PayPal • 21% BTW inbegrepen
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>

  <!-- PayPal SDK -->
  <script src="https://www.paypal.com/sdk/js?client-id=ASLap52V7_VjYsq3D5k1W9a9RLG7854wBRs9TQ0m0PHhLXALJwrG3i-r4nrQOMuUr0d_Dqr5BSMv4ebk&currency=EUR"></script>

  <script>
    let CART = [];
    let TOTALEN = null;
    const WAGEN_TOKEN = '<?php echo htmlspecialchars($wagen_token); ?>';
    const KLANT_TYPE = '<?php echo $klantType; ?>';

    // Format price with € and Dutch decimal
    function formatPrice(val) {
      return '€' + Number(val).toFixed(2).replace('.', ',');
    }

    // Load cart from wagen.php (ASYNC - returns promise)
    async function loadCart() {
      try {
        const response = await fetch('/bestellen/wagen.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ actie: 'laden', wagen_token: WAGEN_TOKEN })
        });
        const data = await response.json();
        console.log('✓ loadCart() response:', data);

        if(data.ok && data.regels) {
          CART = data.regels;
          TOTALEN = data.totalen; // Store totals from server
          console.log('✓ TOTALEN loaded:', TOTALEN);
          updateSummary();
          calcTotals(); // Display totals
          return true; // Signal success
        } else {
          console.error('✗ Failed to load cart:', data);
          return false;
        }
      } catch(err) {
        console.error('✗ Cart load error:', err);
        return false;
      }
    }

    // Update summary display
    function updateSummary() {
      // Find the section containing order summary
      const summarySection = document.querySelector('.section');
      if(!summarySection) return;

      // Build items HTML
      let itemsHtml = '';
      CART.forEach((item, idx) => {
        const itemName = item.notitie ? item.notitie.substring(0, 60) : (item.mdl ? `${item.mdl.brand} ${item.mdl.name}` : item.sku);
        itemsHtml += `<div class="summary-item">
          <div class="summary-row">
            <span class="label"><strong>${itemName}</strong></span>
            <span class="value">${item.aantal || item.qty || 1} stuks</span>
          </div>
          ${item.kleur ? `<div class="summary-row"><span class="label">${item.kleur}</span></div>` : ''}
        </div>`;
      });

      // Rebuild the entire order summary section
      const priceHtml = `
        <div class="price-section">
          <div class="price-row">
            <span>Subtotaal (ex BTW):</span>
            <span id="total-ex">€0,00</span>
          </div>
          <div class="price-row">
            <span>BTW (21%):</span>
            <span id="total-btw">€0,00</span>
          </div>
          <div class="price-row">
            <span>Totaal incl. BTW:</span>
            <span id="total-incl">€0,00</span>
          </div>
          <div class="price-row">
            <span id="verzend-label">Verzending:</span>
            <span id="total-verzend">€0,00</span>
          </div>
          <div class="price-row total">
            <span>Totaal incl. verzending:</span>
            <span id="total-met-verzend">€0,00</span>
          </div>
        </div>
      `;

      summarySection.innerHTML = '<h2>Jouw bestelling</h2>' + itemsHtml + priceHtml;
    }

    // Calculate and display totals - use exact values from wagen.php
    function calcTotals() {
      if(!TOTALEN) {
        console.warn('⚠ calcTotals() called but TOTALEN is null');
        return { totalEx: 0, btw: 0, totalIncl: 0, shipExcl: 0, shipIncl: 0 };
      }

      // Use exact totals from wagen.php
      const totalEx = Number(TOTALEN.totaal_excl) || 0;
      const btw = Number(TOTALEN.btw) || 0;
      const totalIncl = Number(TOTALEN.totaal_incl) || 0;

      // Shipping: always get BOTH incl and excl, with fallback
      let shipIncl = 0;
      let shipExcl = 0;

      if(!TOTALEN.verzend_achteraf && TOTALEN.verzend_incl !== null && TOTALEN.verzend_incl !== undefined) {
        shipIncl = Number(TOTALEN.verzend_incl) || 0;
        shipExcl = Number(TOTALEN.verzend_excl) || 0;
      }

      // Final total: use totaal_met_verzend if available, else calculate
      let finalTotal = totalIncl;
      if(!TOTALEN.verzend_achteraf && shipIncl > 0) {
        finalTotal = Number(TOTALEN.totaal_met_verzend) || (totalIncl + shipIncl);
      }

      console.log('calcTotals():', { totalEx, btw, totalIncl, shipIncl, shipExcl, finalTotal });

      // Update display
      const exEl         = document.getElementById('total-ex');
      const btwEl        = document.getElementById('total-btw');
      const inclEl       = document.getElementById('total-incl');
      const verzendEl    = document.getElementById('total-verzend');
      const verzendLblEl = document.getElementById('verzend-label');
      const metVerzendEl = document.getElementById('total-met-verzend');

      if(exEl)         exEl.textContent         = formatPrice(totalEx);
      if(btwEl)        btwEl.textContent         = formatPrice(btw);
      if(inclEl)       inclEl.textContent         = formatPrice(totalIncl);
      if(verzendEl)    verzendEl.textContent      = formatPrice(shipIncl);
      if(verzendLblEl) verzendLblEl.textContent   = 'Verzending (' + (TOTALEN.verzend_label || '') + '):';
      if(metVerzendEl) metVerzendEl.textContent   = formatPrice(finalTotal);

      return {
        totalEx: totalEx,
        btw: btw,
        totalIncl: finalTotal,
        shipExcl: shipExcl,
        shipIncl: shipIncl
      };
    }

    // Initialize PayPal (CALLED AFTER loadCart() succeeds)
    function initPayPal() {
      console.log('initPayPal() called');

      if(typeof paypal === 'undefined') {
        console.error('✗ PayPal SDK not loaded');
        const container = document.getElementById('pp-container');
        if(container) container.innerHTML = '<p style="color: #e74c3c;">PayPal kon niet laden. Gebruik de testknop of probeer opnieuw.</p>';
        return;
      }

      if(!TOTALEN) {
        console.error('✗ TOTALEN still null at initPayPal()!');
        const container = document.getElementById('pp-container');
        if(container) container.innerHTML = '<p style="color: #e74c3c;">Fout: wagen gegevens niet geladen.</p>';
        return;
      }

      try {
        console.log('✓ Creating PayPal buttons with TOTALEN:', TOTALEN);

        paypal.Buttons({
          style: { layout: 'vertical', color: 'blue', height: 45 },
          createOrder: (data, actions) => {
            // CALCULATE FRESH totals here (don't rely on closure)
            const freshTotals = calcTotals();
            console.log('createOrder - freshTotals:', freshTotals);

            if(!freshTotals.shipIncl && freshTotals.shipIncl !== 0) {
              console.error('✗ shipIncl is undefined:', freshTotals);
              throw new Error('Verzending waarde niet geladen');
            }

            return actions.order.create({
              purchase_units: [{
                amount: {
                  currency_code: 'EUR',
                  value: Number(freshTotals.totalIncl).toFixed(2),
                  breakdown: {
                    item_total: { currency_code: 'EUR', value: Number(freshTotals.totalIncl - (freshTotals.shipIncl || 0)).toFixed(2) },
                    shipping: { currency_code: 'EUR', value: Number(freshTotals.shipIncl || 0).toFixed(2) },
                    tax_total: { currency_code: 'EUR', value: '0.00' }
                  }
                }
              }]
            });
          },
          onApprove: (data, actions) => {
            console.log('✓ PayPal onApprove');
            return actions.order.capture().then(details => {
              submitPayment(details);
            });
          },
          onError: (err) => {
            console.error('✗ PayPal error:', err);
            alert('Betaling mislukt: ' + (err.message || 'Onbekende fout'));
          }
        }).render('#pp-container');
        console.log('✓ PayPal buttons rendered');
      } catch(err) {
        console.error('✗ PayPal init error:', err);
        const container = document.getElementById('pp-container');
        if(container) container.innerHTML = '<p style="color: #e74c3c;">PayPal fout: ' + err.message + '</p>';
      }
    }

    // Submit payment (called from PayPal or test button)
    async function submitPayment(paypalDetails) {
      console.log('submitPayment() called', { paypalDetails });

      // Get form data
      const form = document.getElementById('checkout-form');
      if(!form) {
        alert('Fout: formulier niet gevonden');
        return;
      }
      if(!form.checkValidity()) {
        form.reportValidity();
        return;
      }
      const formData = new FormData(form);

      // Combine telefoon_landcode + telefoon
      const landcodeEl = document.getElementById('telefoon_landcode');
      const telefoonEl = document.getElementById('telefoon');
      if(landcodeEl && telefoonEl) {
        const landcode = landcodeEl.value || '+31';
        const telefoon_raw = telefoonEl.value || '';
        if(telefoon_raw) {
          const telefoon_clean = telefoon_raw.replace(/^[\+\s]+/, '');
          formData.set('telefoon', landcode + telefoon_clean);
        }
        formData.delete('telefoon_landcode');
      }

      // Calculate totals FRESH here
      console.log('submitPayment - calculating totals');
      const totals = calcTotals();
      if(!totals || totals.totalIncl <= 0) {
        console.error('✗ Invalid totals:', totals);
        alert('Fout: prijzen kunnen niet berekend worden');
        return;
      }
      console.log('✓ submitPayment totals:', totals);

      // Extract cart regels for handler validation
      if(!CART || CART.length === 0) {
        alert('Fout: winkelwagen is leeg');
        return;
      }

      const regels = CART.map(item => {
        const prijs_obj = item.prijs || {};
        const prijs_ex = Number(prijs_obj.prijs_excl) || 0;
        const druk_ex = Number(prijs_obj.druk_excl) || 0;
        const korting_pct = Number(prijs_obj.volumekorting_pct) || 0;
        const aantal = Number(item.aantal) || 1;

        return {
          sku: item.sku,
          prijs_ex: prijs_ex.toFixed(2),
          druk_ex: druk_ex.toFixed(2),
          aantal: aantal,
          korting_pct: korting_pct.toFixed(2)
        };
      });

      console.log('submitPayment - regels:', regels);

      // Add handler-required fields
      formData.set('regels', JSON.stringify(regels));
      formData.set('verzending_ex', (totals.shipExcl || 0).toFixed(2));
      formData.set('totaal_incl', (totals.totalIncl || 0).toFixed(2));
      formData.set('taal', 'nl');

      if(paypalDetails) {
        formData.set('paypal_id', paypalDetails.id);
        console.log('PayPal ID:', paypalDetails.id);
      }

      console.log('submitPayment - sending to handler');

      try {
        const response = await fetch('/bestellen/handler.php', {
          method: 'POST',
          body: formData
        });

        if(!response.ok) {
          console.error('✗ Handler returned status:', response.status);
        }

        const result = await response.json();
        console.log('✓ Handler response:', result);

        if(result.success) {
          console.log('✓ Order success! Redirecting...');
          window.location.href = '/bestellen.php?success=' + (paypalDetails?.id || 'test');
        } else {
          const errorMsg = result.error || result.fout || JSON.stringify(result);
          console.error('✗ Handler error:', errorMsg, result);
          alert('Bestelling mislukt: ' + errorMsg);
        }
      } catch(err) {
        console.error('✗ Fetch error:', err);
        alert('Fout bij versturen: ' + err.message);
      }
    }

    // Test payment button
    document.addEventListener('DOMContentLoaded', () => {
      const submitBtn = document.getElementById('submit-btn');
      if(submitBtn) {
        submitBtn.addEventListener('click', (e) => {
          e.preventDefault();
          console.log('Test payment button clicked');
          submitPayment(null);
        });
      }
    });

    // Initialize on load - WAIT for loadCart() BEFORE initPayPal()
    window.addEventListener('load', async () => {
      console.log('🚀 Page loaded - starting initialization');
      const loadSuccess = await loadCart(); // WAIT for this to complete
      if(loadSuccess) {
        console.log('✓ loadCart succeeded, now initializing PayPal');
        initPayPal();
      } else {
        console.error('✗ loadCart failed, skipping PayPal init');
        const container = document.getElementById('pp-container');
        if(container) container.innerHTML = '<p style="color: #e74c3c;">Fout bij laden wagen - vernieuw pagina.</p>';
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
  <script>
  // ── Landen data (alle UN-landen, Nederlandse namen) ─────────────────────────
  const LANDEN = [
    {v:'AF',n:'Afghanistan',t:'+93'},{v:'AL',n:'Albanië',t:'+355'},
    {v:'DZ',n:'Algerije',t:'+213'},{v:'AD',n:'Andorra',t:'+376'},
    {v:'AO',n:'Angola',t:'+244'},{v:'AG',n:'Antigua en Barbuda',t:'+1268'},
    {v:'AR',n:'Argentinië',t:'+54'},{v:'AM',n:'Armenië',t:'+374'},
    {v:'AU',n:'Australië',t:'+61'},{v:'AT',n:'Oostenrijk',t:'+43'},
    {v:'AZ',n:'Azerbeidzjan',t:'+994'},{v:'BS',n:"Bahama's",t:'+1242'},
    {v:'BH',n:'Bahrein',t:'+973'},{v:'BD',n:'Bangladesh',t:'+880'},
    {v:'BB',n:'Barbados',t:'+1246'},{v:'BY',n:'Belarus',t:'+375'},
    {v:'BE',n:'België',t:'+32'},{v:'BZ',n:'Belize',t:'+501'},
    {v:'BJ',n:'Benin',t:'+229'},{v:'BT',n:'Bhutan',t:'+975'},
    {v:'BO',n:'Bolivia',t:'+591'},{v:'BA',n:'Bosnië-Herzegovina',t:'+387'},
    {v:'BW',n:'Botswana',t:'+267'},{v:'BR',n:'Brazilië',t:'+55'},
    {v:'BN',n:'Brunei',t:'+673'},{v:'BG',n:'Bulgarije',t:'+359'},
    {v:'BF',n:'Burkina Faso',t:'+226'},{v:'BI',n:'Burundi',t:'+257'},
    {v:'CV',n:'Kaapverdië',t:'+238'},{v:'KH',n:'Cambodja',t:'+855'},
    {v:'CM',n:'Kameroen',t:'+237'},{v:'CA',n:'Canada',t:'+1'},
    {v:'CF',n:'Centraal-Afrikaanse Republiek',t:'+236'},{v:'TD',n:'Tsjaad',t:'+235'},
    {v:'CL',n:'Chili',t:'+56'},{v:'CN',n:'China',t:'+86'},
    {v:'CO',n:'Colombia',t:'+57'},{v:'KM',n:'Comoren',t:'+269'},
    {v:'CD',n:'Congo (DRC)',t:'+243'},{v:'CG',n:'Congo (Rep.)',t:'+242'},
    {v:'CR',n:'Costa Rica',t:'+506'},{v:'HR',n:'Kroatië',t:'+385'},
    {v:'CU',n:'Cuba',t:'+53'},{v:'CY',n:'Cyprus',t:'+357'},
    {v:'CZ',n:'Tsjechië',t:'+420'},{v:'DK',n:'Denemarken',t:'+45'},
    {v:'DJ',n:'Djibouti',t:'+253'},{v:'DM',n:'Dominica',t:'+1767'},
    {v:'DO',n:'Dominicaanse Republiek',t:'+1809'},{v:'EC',n:'Ecuador',t:'+593'},
    {v:'EG',n:'Egypte',t:'+20'},{v:'SV',n:'El Salvador',t:'+503'},
    {v:'GQ',n:'Equatoriaal-Guinea',t:'+240'},{v:'ER',n:'Eritrea',t:'+291'},
    {v:'EE',n:'Estland',t:'+372'},{v:'SZ',n:'Eswatini',t:'+268'},
    {v:'ET',n:'Ethiopië',t:'+251'},{v:'FJ',n:'Fiji',t:'+679'},
    {v:'FI',n:'Finland',t:'+358'},{v:'FR',n:'Frankrijk',t:'+33'},
    {v:'GA',n:'Gabon',t:'+241'},{v:'GM',n:'Gambia',t:'+220'},
    {v:'GE',n:'Georgië',t:'+995'},{v:'DE',n:'Duitsland',t:'+49'},
    {v:'GH',n:'Ghana',t:'+233'},{v:'GR',n:'Griekenland',t:'+30'},
    {v:'GD',n:'Grenada',t:'+1473'},{v:'GT',n:'Guatemala',t:'+502'},
    {v:'GN',n:'Guinee',t:'+224'},{v:'GW',n:'Guinee-Bissau',t:'+245'},
    {v:'GY',n:'Guyana',t:'+592'},{v:'HT',n:'Haïti',t:'+509'},
    {v:'HN',n:'Honduras',t:'+504'},{v:'HU',n:'Hongarije',t:'+36'},
    {v:'IS',n:'IJsland',t:'+354'},{v:'IN',n:'India',t:'+91'},
    {v:'ID',n:'Indonesië',t:'+62'},{v:'IR',n:'Iran',t:'+98'},
    {v:'IQ',n:'Irak',t:'+964'},{v:'IE',n:'Ierland',t:'+353'},
    {v:'IL',n:'Israël',t:'+972'},{v:'IT',n:'Italië',t:'+39'},
    {v:'JM',n:'Jamaica',t:'+1876'},{v:'JP',n:'Japan',t:'+81'},
    {v:'JO',n:'Jordanië',t:'+962'},{v:'KZ',n:'Kazachstan',t:'+7'},
    {v:'KE',n:'Kenia',t:'+254'},{v:'KI',n:'Kiribati',t:'+686'},
    {v:'KW',n:'Koeweit',t:'+965'},{v:'KG',n:'Kirgizstan',t:'+996'},
    {v:'LA',n:'Laos',t:'+856'},{v:'LV',n:'Letland',t:'+371'},
    {v:'LB',n:'Libanon',t:'+961'},{v:'LS',n:'Lesotho',t:'+266'},
    {v:'LR',n:'Liberia',t:'+231'},{v:'LY',n:'Libië',t:'+218'},
    {v:'LI',n:'Liechtenstein',t:'+423'},{v:'LT',n:'Litouwen',t:'+370'},
    {v:'LU',n:'Luxemburg',t:'+352'},{v:'MG',n:'Madagaskar',t:'+261'},
    {v:'MW',n:'Malawi',t:'+265'},{v:'MY',n:'Maleisië',t:'+60'},
    {v:'MV',n:'Maldiven',t:'+960'},{v:'ML',n:'Mali',t:'+223'},
    {v:'MT',n:'Malta',t:'+356'},{v:'MH',n:'Marshalleilanden',t:'+692'},
    {v:'MR',n:'Mauritanië',t:'+222'},{v:'MU',n:'Mauritius',t:'+230'},
    {v:'MX',n:'Mexico',t:'+52'},{v:'FM',n:'Micronesië',t:'+691'},
    {v:'MD',n:'Moldavië',t:'+373'},{v:'MC',n:'Monaco',t:'+377'},
    {v:'MN',n:'Mongolië',t:'+976'},{v:'ME',n:'Montenegro',t:'+382'},
    {v:'MA',n:'Marokko',t:'+212'},{v:'MZ',n:'Mozambique',t:'+258'},
    {v:'MM',n:'Myanmar',t:'+95'},{v:'NA',n:'Namibië',t:'+264'},
    {v:'NR',n:'Nauru',t:'+674'},{v:'NP',n:'Nepal',t:'+977'},
    {v:'NL',n:'Nederland',t:'+31'},{v:'NZ',n:'Nieuw-Zeeland',t:'+64'},
    {v:'NI',n:'Nicaragua',t:'+505'},{v:'NE',n:'Niger',t:'+227'},
    {v:'NG',n:'Nigeria',t:'+234'},{v:'KP',n:'Noord-Korea',t:'+850'},
    {v:'MK',n:'Noord-Macedonië',t:'+389'},{v:'NO',n:'Noorwegen',t:'+47'},
    {v:'OM',n:'Oman',t:'+968'},{v:'PK',n:'Pakistan',t:'+92'},
    {v:'PW',n:'Palau',t:'+680'},{v:'PS',n:'Palestina',t:'+970'},
    {v:'PA',n:'Panama',t:'+507'},{v:'PG',n:'Papoea-Nieuw-Guinea',t:'+675'},
    {v:'PY',n:'Paraguay',t:'+595'},{v:'PE',n:'Peru',t:'+51'},
    {v:'PH',n:'Filipijnen',t:'+63'},{v:'PL',n:'Polen',t:'+48'},
    {v:'PT',n:'Portugal',t:'+351'},{v:'QA',n:'Qatar',t:'+974'},
    {v:'RO',n:'Roemenië',t:'+40'},{v:'RU',n:'Rusland',t:'+7'},
    {v:'RW',n:'Rwanda',t:'+250'},{v:'KN',n:'Saint Kitts en Nevis',t:'+1869'},
    {v:'LC',n:'Saint Lucia',t:'+1758'},{v:'VC',n:'Saint Vincent',t:'+1784'},
    {v:'WS',n:'Samoa',t:'+685'},{v:'SM',n:'San Marino',t:'+378'},
    {v:'ST',n:'Sao Tomé en Príncipe',t:'+239'},{v:'SA',n:'Saoedi-Arabië',t:'+966'},
    {v:'SN',n:'Senegal',t:'+221'},{v:'RS',n:'Servië',t:'+381'},
    {v:'SC',n:'Seychellen',t:'+248'},{v:'SL',n:'Sierra Leone',t:'+232'},
    {v:'SG',n:'Singapore',t:'+65'},{v:'SK',n:'Slowakije',t:'+421'},
    {v:'SI',n:'Slovenië',t:'+386'},{v:'SB',n:'Salomonseilanden',t:'+677'},
    {v:'SO',n:'Somalië',t:'+252'},{v:'ZA',n:'Zuid-Afrika',t:'+27'},
    {v:'KR',n:'Zuid-Korea',t:'+82'},{v:'SS',n:'Zuid-Soedan',t:'+211'},
    {v:'ES',n:'Spanje',t:'+34'},{v:'LK',n:'Sri Lanka',t:'+94'},
    {v:'SD',n:'Soedan',t:'+249'},{v:'SR',n:'Suriname',t:'+597'},
    {v:'SE',n:'Zweden',t:'+46'},{v:'CH',n:'Zwitserland',t:'+41'},
    {v:'SY',n:'Syrië',t:'+963'},{v:'TW',n:'Taiwan',t:'+886'},
    {v:'TJ',n:'Tadzjikistan',t:'+992'},{v:'TZ',n:'Tanzania',t:'+255'},
    {v:'TH',n:'Thailand',t:'+66'},{v:'TL',n:'Oost-Timor',t:'+670'},
    {v:'TG',n:'Togo',t:'+228'},{v:'TO',n:'Tonga',t:'+676'},
    {v:'TT',n:'Trinidad en Tobago',t:'+1868'},{v:'TN',n:'Tunesië',t:'+216'},
    {v:'TR',n:'Turkije',t:'+90'},{v:'TM',n:'Turkmenistan',t:'+993'},
    {v:'TV',n:'Tuvalu',t:'+688'},{v:'UG',n:'Oeganda',t:'+256'},
    {v:'UA',n:'Oekraïne',t:'+380'},{v:'AE',n:'Ver. Arabische Emiraten',t:'+971'},
    {v:'GB',n:'Verenigd Koninkrijk',t:'+44'},{v:'US',n:'Verenigde Staten',t:'+1'},
    {v:'UY',n:'Uruguay',t:'+598'},{v:'UZ',n:'Oezbekistan',t:'+998'},
    {v:'VU',n:'Vanuatu',t:'+678'},{v:'VA',n:'Vaticaanstad',t:'+379'},
    {v:'VE',n:'Venezuela',t:'+58'},{v:'VN',n:'Vietnam',t:'+84'},
    {v:'YE',n:'Jemen',t:'+967'},{v:'ZM',n:'Zambia',t:'+260'},
    {v:'ZW',n:'Zimbabwe',t:'+263'},
  ];

  // ── Bouw opties + init Tom Select ─────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    const savedLand  = '<?php echo htmlspecialchars($klant['land'] ?? 'NL'); ?>';
    const savedDial  = '<?php echo htmlspecialchars($klant['telefoon_landcode'] ?? '+31'); ?>';

    // Landenkeuze
    const landSel = document.getElementById('land');
    LANDEN.forEach(l => {
      const o = document.createElement('option');
      o.value = l.v; o.textContent = l.n;
      if(l.v === (savedLand || 'NL')) o.selected = true;
      landSel.appendChild(o);
    });
    new TomSelect('#land', { maxOptions: 300, placeholder: 'Zoek land…' });

    // Landcode telefoon
    const dialSel = document.getElementById('telefoon_landcode');
    LANDEN.forEach(l => {
      const o = document.createElement('option');
      o.value = l.t; o.textContent = l.t + ' ' + l.n;
      if(l.t === (savedDial || '+31') && l.v === (savedLand || 'NL')) o.selected = true;
      dialSel.appendChild(o);
    });
    new TomSelect('#telefoon_landcode', {
      maxOptions: 300,
      placeholder: 'Zoek…',
      render: {
        option: (d) => `<div><strong>${d.value.split(' ')[0]}</strong> ${d.text.substring(d.text.indexOf(' ')+1)}</div>`,
        item:   (d) => `<div>${d.value.split(' ')[0]}</div>`,
      }
    });

    // ── Postcode: valideer NL-formaat + normaliseer naar "1234 AB" ───────────────
    const postcodeEl = document.getElementById('postcode');
    const landTsEl   = document.getElementById('land');
    const NL_RE = /^([1-9][0-9]{3})\s?([A-Za-z]{2})$/;

    function isNL() {
      const ts = landTsEl.tomselect;
      return (ts ? ts.getValue() : landTsEl.value) === 'NL';
    }

    postcodeEl.addEventListener('blur', () => {
      if(!isNL()) return;
      const m = postcodeEl.value.trim().match(NL_RE);
      if(m) {
        postcodeEl.value = m[1] + ' ' + m[2].toUpperCase();
        postcodeEl.setCustomValidity('');
      } else if(postcodeEl.value) {
        postcodeEl.setCustomValidity('Vul een geldige Nederlandse postcode in (bijv. 1234 AB)');
        postcodeEl.reportValidity();
      }
    });
    postcodeEl.addEventListener('input', () => postcodeEl.setCustomValidity(''));
  });
  </script>
</body>
</html>
