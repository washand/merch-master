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
          <div style="display: flex; gap: 0.5rem;">
            <select name="telefoon_landcode" id="telefoon_landcode" style="flex: 0 0 auto; max-width: 90px;">
              <option value="+31" <?php echo ($klant['telefoon_landcode'] ?? '+31') === '+31' ? 'selected' : ''; ?>>🇳🇱 +31</option>
              <option value="+32" <?php echo ($klant['telefoon_landcode'] ?? '') === '+32' ? 'selected' : ''; ?>>🇧🇪 +32</option>
              <option value="+49" <?php echo ($klant['telefoon_landcode'] ?? '') === '+49' ? 'selected' : ''; ?>>🇩🇪 +49</option>
            </select>
            <input type="tel" name="telefoon" id="telefoon" placeholder="6 12345678" value="<?php echo htmlspecialchars(preg_replace('/^\+\d+/', '', $klant['telefoon'] ?? '')); ?>" style="flex: 1;">
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
          <input type="text" name="postcode" id="postcode" required value="<?php echo htmlspecialchars($klant['postcode'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Plaats *</label>
          <input type="text" name="stad" id="stad" required value="<?php echo htmlspecialchars($klant['stad'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>Land</label>
          <select name="land" id="land">
            <option value="NL" <?php echo ($klant['land'] ?? 'NL') === 'NL' ? 'selected' : ''; ?>>Nederland</option>
            <option value="BE" <?php echo ($klant['land'] ?? '') === 'BE' ? 'selected' : ''; ?>>België</option>
            <option value="DE" <?php echo ($klant['land'] ?? '') === 'DE' ? 'selected' : ''; ?>>Duitsland</option>
            <option value="other" <?php echo !in_array($klant['land'] ?? '', ['NL', 'BE', 'DE']) ? 'selected' : ''; ?>>Anders</option>
          </select>
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

    // Load cart from wagen.php
    async function loadCart() {
      try {
        const response = await fetch('/bestellen/wagen.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ actie: 'laden', wagen_token: WAGEN_TOKEN })
        });
        const data = await response.json();
        if(data.ok && data.regels) {
          CART = data.regels;
          TOTALEN = data.totalen; // Store totals from server for exact prices
          updateSummary();
          calcTotals();
        } else {
          console.error('Failed to load cart:', data);
        }
      } catch(err) {
        console.error('Cart load error:', err);
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
          <div class="price-row total">
            <span>Totaal incl. BTW:</span>
            <span id="total-incl">€0,00</span>
          </div>
        </div>
      `;

      summarySection.innerHTML = '<h2>Jouw bestelling</h2>' + itemsHtml + priceHtml;
    }

    // Calculate and display totals - use exact values from wagen.php
    function calcTotals() {
      if(!TOTALEN) {
        return { totalEx: 0, btw: 0, totalIncl: 0, ship: 0 };
      }

      // Use exact totals from wagen.php (matches winkelwagen display)
      const totalEx = TOTALEN.totaal_excl || 0;
      const btw = TOTALEN.btw || 0;
      const totalIncl = TOTALEN.totaal_incl || 0;

      // Shipping: use verzend_incl directly (NOT verzend_excl * 1.21)
      let shipIncl = 0;
      let shipExcl = 0;
      if(!TOTALEN.verzend_achteraf) {
        shipIncl = TOTALEN.verzend_incl || 0;  // Use incl value directly
        shipExcl = TOTALEN.verzend_excl || 0;  // For handler validation
      }

      // Add shipping to totals if not achteraf
      let totalWithShip = totalIncl;
      if(!TOTALEN.verzend_achteraf && shipIncl > 0) {
        totalWithShip = TOTALEN.totaal_met_verzend || (totalIncl + shipIncl);
      }

      document.getElementById('total-ex').textContent = formatPrice(totalEx);
      document.getElementById('total-btw').textContent = formatPrice(btw);
      document.getElementById('total-incl').textContent = formatPrice(totalWithShip);

      return {
        totalEx: totalEx,
        btw: btw,
        totalIncl: totalWithShip,
        shipExcl: shipExcl,
        shipIncl: shipIncl
      };
    }

    // Initialize PayPal
    function initPayPal() {
      if(typeof paypal === 'undefined') {
        console.error('PayPal SDK not loaded');
        document.getElementById('pp-container').innerHTML = '<p style="color: #e74c3c;">PayPal kon niet laden. Gebruik de testknop of probeer opnieuw.</p>';
        return;
      }

      const totals = calcTotals();

      try {
        paypal.Buttons({
          style: { layout: 'vertical', color: 'blue', height: 45 },
          createOrder: (data, actions) => {
            return actions.order.create({
              purchase_units: [{
                amount: {
                  currency_code: 'EUR',
                  value: totals.totalIncl.toFixed(2),
                  breakdown: {
                    item_total: { currency_code: 'EUR', value: (totals.totalIncl - totals.shipIncl).toFixed(2) },
                    shipping: { currency_code: 'EUR', value: totals.shipIncl.toFixed(2) },
                    tax_total: { currency_code: 'EUR', value: '0.00' }
                  }
                }
              }]
            });
          },
          onApprove: (data, actions) => {
            return actions.order.capture().then(details => {
              submitPayment(details);
            });
          },
          onError: (err) => {
            alert('Betaling mislukt. Probeer opnieuw.');
            console.error(err);
          }
        }).render('#pp-container');
      } catch(err) {
        console.error('PayPal init error:', err);
        document.getElementById('pp-container').innerHTML = '<p style="color: #e74c3c;">Er is een fout opgetreden bij het laden van PayPal.</p>';
      }
    }

    // Submit payment (called from PayPal or test button)
    async function submitPayment(paypalDetails) {
      // Get form data
      const form = document.getElementById('checkout-form');
      const formData = new FormData(form);

      // Combine telefoon_landcode + telefoon
      const landcode = document.getElementById('telefoon_landcode').value || '+31';
      const telefoon_raw = document.getElementById('telefoon').value || '';
      if(telefoon_raw) {
        // Remove any + or spaces at start, prepend landcode
        const telefoon_clean = telefoon_raw.replace(/^[\+\s]+/, '');
        formData.set('telefoon', landcode + telefoon_clean);
      }
      formData.delete('telefoon_landcode');  // Don't send landcode separately

      // Calculate totals
      const totals = calcTotals();

      // Extract cart regels for handler validation
      const regels = CART.map(item => {
        // Get item pricing from wagen.php response (nested in 'prijs' object)
        const prijs_obj = item.prijs || {};
        const prijs_ex = prijs_obj.prijs_excl || 0;
        const druk_ex = prijs_obj.druk_excl || 0;
        const korting_pct = prijs_obj.volumekorting_pct || 0;
        const aantal = item.aantal || 1;

        return {
          sku: item.sku,
          prijs_ex: Number(prijs_ex).toFixed(2),
          druk_ex: Number(druk_ex).toFixed(2),
          aantal: aantal,
          korting_pct: Number(korting_pct).toFixed(2)
        };
      });

      // Add handler-required fields using exact values from server
      formData.set('regels', JSON.stringify(regels));
      formData.set('verzending_ex', TOTALEN && !TOTALEN.verzend_achteraf ? (TOTALEN.verzend_excl || 0).toFixed(2) : '0.00');

      // Use totaal_met_verzend if available (includes shipping), otherwise calculate
      let finalTotal = 0;
      if(TOTALEN) {
        if(!TOTALEN.verzend_achteraf && TOTALEN.totaal_met_verzend) {
          finalTotal = TOTALEN.totaal_met_verzend;
        } else {
          finalTotal = TOTALEN.totaal_incl || 0;
        }
      }
      formData.set('totaal_incl', Number(finalTotal).toFixed(2));
      formData.set('taal', 'nl');

      if(paypalDetails) {
        formData.set('paypal_id', paypalDetails.id);
      }

      try {
        const response = await fetch('/bestellen/handler.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();

        if(result.success) {
          // Redirect to success page
          window.location.href = '/bestellen.php?success=' + (paypalDetails?.id || 'test');
        } else {
          alert('Bestelling mislukt: ' + (result.error || 'Onbekende fout'));
        }
      } catch(err) {
        console.error('Error:', err);
        alert('Fout bij versturen: ' + err.message);
      }
    }

    // Test payment button
    document.getElementById('submit-btn').addEventListener('click', (e) => {
      e.preventDefault();
      submitPayment(null);
    });

    // Initialize on load
    window.addEventListener('load', () => {
      loadCart();
      initPayPal();
    });
  </script>
</body>
</html>
