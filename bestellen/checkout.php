<?php
session_start();
require_once __DIR__ . '/includes/db-config.php';
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

// Load drukkosten from admin
$drukkosten = ['dtf' => [], 'zeef' => []];
try {
  $hasil = Db::exec("SELECT value FROM mm_instellingen WHERE `key` = 'drukkosten' LIMIT 1");
  if($hasil && count($hasil) > 0) {
    $dk = json_decode($hasil[0]['value'], true);
    if($dk && is_array($dk)){
      $drukkosten = $dk;
    }
  }
} catch(Exception $e) {}

// Helper function: format price
function fmt($val) {
  return '€' . number_format((float)$val, 2, ',', '.');
}

// Helper function: translate
function t($key) {
  $trans = [
    'checkout' => ['nl' => 'Afrekenen', 'en' => 'Checkout', 'de' => 'Kasse'],
    'order_summary' => ['nl' => 'Jouw bestelling', 'en' => 'Your order', 'de' => 'Deine Bestellung'],
    'customer_data' => ['nl' => 'Jouw gegevens', 'en' => 'Your details', 'de' => 'Deine Daten'],
    'firstname' => ['nl' => 'Voornaam', 'en' => 'First name', 'de' => 'Vorname'],
    'lastname' => ['nl' => 'Achternaam', 'en' => 'Last name', 'de' => 'Nachname'],
    'email' => ['nl' => 'E-mailadres', 'en' => 'Email address', 'de' => 'E-Mailadresse'],
    'phone' => ['nl' => 'Telefoon', 'en' => 'Phone', 'de' => 'Telefon'],
    'street' => ['nl' => 'Straat + huisnummer', 'en' => 'Street + number', 'de' => 'Straße + Nummer'],
    'zip' => ['nl' => 'Postcode', 'en' => 'Zip code', 'de' => 'Postleitzahl'],
    'city' => ['nl' => 'Plaats', 'en' => 'City', 'de' => 'Stadt'],
    'country' => ['nl' => 'Land', 'en' => 'Country', 'de' => 'Land'],
    'company' => ['nl' => 'Bedrijfsnaam', 'en' => 'Company', 'de' => 'Unternehmen'],
    'btw_number' => ['nl' => 'BTW-nummer', 'en' => 'VAT number', 'de' => 'MwSt-Nummer'],
    'kvk' => ['nl' => 'KVK-nummer', 'en' => 'KVK number', 'de' => 'KVK-Nummer'],
    'notes' => ['nl' => 'Opmerkingen', 'en' => 'Notes', 'de' => 'Hinweise'],
    'payment_method' => ['nl' => 'Betaalmethode', 'en' => 'Payment method', 'de' => 'Zahlungsart'],
  ];
  $lang = $_SESSION['mm_lang'] ?? 'nl';
  return $trans[$key][$lang] ?? $key;
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo t('checkout'); ?> - Merch Master</title>
  <link rel="stylesheet" href="/includes/style.css">
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
    <h1><?php echo t('checkout'); ?></h1>

    <!-- Order Summary -->
    <div class="section">
      <h2><?php echo t('order_summary'); ?></h2>
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
      <h2><?php echo t('customer_data'); ?></h2>

      <input type="hidden" name="action" value="bestelling">
      <input type="hidden" id="cart-data" name="cart_data" value="">

      <div class="form-row">
        <div class="form-group">
          <label><?php echo t('firstname'); ?> *</label>
          <input type="text" name="fname" id="fname" required value="<?php echo htmlspecialchars($klant['voornaam'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label><?php echo t('lastname'); ?> *</label>
          <input type="text" name="lname" id="lname" required value="<?php echo htmlspecialchars($klant['achternaam'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><?php echo t('email'); ?> *</label>
          <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($klant['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label><?php echo t('phone'); ?></label>
          <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($klant['telefoon'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><?php echo t('street'); ?> *</label>
          <input type="text" name="street" id="street" required value="<?php echo htmlspecialchars($klant['straat'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label><?php echo t('zip'); ?> *</label>
          <input type="text" name="zip" id="zip" required value="<?php echo htmlspecialchars($klant['postcode'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><?php echo t('city'); ?> *</label>
          <input type="text" name="city" id="city" required value="<?php echo htmlspecialchars($klant['stad'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label><?php echo t('country'); ?></label>
          <select name="country" id="country">
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
          <label><?php echo t('company'); ?> (optioneel)</label>
          <input type="text" name="company" id="company" value="<?php echo htmlspecialchars($klant['bedrijf'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label><?php echo t('btw_number'); ?> (optioneel)</label>
          <input type="text" name="btw_num" id="btw_num" value="<?php echo htmlspecialchars($klant['btw_nummer'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <label><?php echo t('kvk'); ?> (optioneel)</label>
        <input type="text" name="kvk" id="kvk" value="<?php echo htmlspecialchars($klant['kvk'] ?? ''); ?>">
      </div>
    </form>

    <!-- Payment Section -->
    <div class="section">
      <h2><?php echo t('payment_method'); ?></h2>

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
  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo getenv('PAYPAL_CLIENT_ID') ?: 'sb-nxgbn28402656@personal.example.com'; ?>&currency=EUR"></script>

  <script>
    let CART = [];
    const WAGEN_TOKEN = '<?php echo htmlspecialchars($wagen_token); ?>';
    const KLANT_TYPE = '<?php echo $klantType; ?>';

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
          updateSummary();
          calcTotals();
        } else {
          console.error('Failed to load cart:', data);
          document.body.innerHTML = '<p>Fout bij laden cart. <a href="/bestellen.php">Terug</a></p>';
        }
      } catch(err) {
        console.error('Cart load error:', err);
      }
    }

    // Update summary display
    function updateSummary() {
      const summaryDiv = document.querySelector('.summary-item');
      if(!summaryDiv) return;

      let html = '';
      CART.forEach((item, idx) => {
        html += `<div class="summary-item">
          <div class="summary-row">
            <span class="label"><strong>${item.notitie || item.sku}</strong></span>
            <span class="value">${item.aantal} stuks</span>
          </div>
        </div>`;
      });
      document.querySelector('.section').innerHTML = '<h2><?php echo t("order_summary"); ?></h2>' + html + '<div class="price-section" id="prices"></div>';
    }

    // Calculate and display totals
    function calcTotals() {
      let totalEx = 0;

      CART.forEach(item => {
        // Simplified: assume prices stored in item
        // Real calculation would come from wagen.php
        totalEx += (item.prijs_ex || 0) * (item.aantal || 0);
      });

      // Add shipping
      let qty = 0;
      CART.forEach(item => qty += item.aantal || 0);
      const ship = qty >= 12 ? 13.95 : 6.95;
      const shipEx = ship / 1.21;

      totalEx += shipEx;
      const btw = totalEx * 0.21 / 1.21;
      const totalIncl = totalEx + btw;

      document.getElementById('total-ex').textContent = '€' + totalEx.toFixed(2).replace('.', ',');
      document.getElementById('total-btw').textContent = '€' + btw.toFixed(2).replace('.', ',');
      document.getElementById('total-incl').textContent = '€' + totalIncl.toFixed(2).replace('.', ',');

      return { totalEx: Math.round(totalEx * 100) / 100, btw, totalIncl: Math.round(totalIncl * 100) / 100, ship };
    }

    // Initialize PayPal
    function initPayPal() {
      const totals = calcTotals();

      paypal.Buttons({
        style: { layout: 'vertical', color: 'blue', height: 45 },
        createOrder: (data, actions) => {
          return actions.order.create({
            purchase_units: [{
              amount: {
                currency_code: 'EUR',
                value: totals.totalIncl.toFixed(2),
                breakdown: {
                  item_total: { currency_code: 'EUR', value: (totals.totalIncl - totals.ship).toFixed(2) },
                  shipping: { currency_code: 'EUR', value: totals.ship.toFixed(2) },
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
    }

    // Submit payment (called from PayPal or test button)
    async function submitPayment(paypalDetails) {
      const formData = new FormData(document.getElementById('checkout-form'));

      // Add cart data
      formData.append('cart_json', JSON.stringify(CART));
      if(paypalDetails) {
        formData.append('paypal_id', paypalDetails.id);
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
