<?php
session_start();
$bestelling_id = $_GET['id'] ?? '';
$email = $_GET['email'] ?? $_SESSION['mm_klant_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bestelling bevestigd - Merch Master</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --ac: #e84c1e;
      --ink: #1a1816;
      --bg: #f5f3f0;
      --sur: #fff;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background: var(--bg);
      color: var(--ink);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    .container {
      max-width: 600px;
      background: var(--sur);
      border-radius: 12px;
      padding: 3rem 2rem;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .checkmark {
      width: 80px;
      height: 80px;
      margin: 0 auto 2rem;
      background: #f0fdf4;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .checkmark svg {
      width: 50px;
      height: 50px;
      stroke: #22c55e;
      stroke-width: 2;
      fill: none;
    }
    h1 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: var(--ink);
    }
    .message {
      font-size: 1rem;
      line-height: 1.6;
      color: #666;
      margin-bottom: 2rem;
    }
    .message strong {
      color: var(--ac);
      word-break: break-all;
    }
    .order-id {
      background: var(--bg);
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 2rem;
      font-size: 0.9rem;
      color: #666;
    }
    .order-id-label {
      color: #999;
      font-size: 0.85rem;
      margin-bottom: 0.3rem;
    }
    .order-id-value {
      font-weight: 600;
      color: var(--ink);
      font-size: 1.1rem;
    }
    .button {
      display: inline-block;
      padding: 0.75rem 2rem;
      background: var(--ac);
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 500;
      transition: 0.2s;
      border: none;
      cursor: pointer;
      font-size: 1rem;
      font-family: inherit;
    }
    .button:hover {
      background: #c73d14;
    }
    .whatsapp {
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 1px solid #e2ddd5;
      font-size: 0.9rem;
      color: #666;
    }
    .whatsapp a {
      color: var(--ac);
      text-decoration: none;
      font-weight: 500;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="checkmark">
    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
  </div>

  <h1>Bestelling ontvangen! 🎉</h1>

  <div class="message">
    Bedankt voor je bestelling bij <strong>Merch Master</strong>!<br>
    Je ontvangt een bevestiging op <strong><?php echo htmlspecialchars($email); ?></strong>
  </div>

  <?php if ($bestelling_id): ?>
  <div class="order-id">
    <div class="order-id-label">Bestelling #</div>
    <div class="order-id-value"><?php echo htmlspecialchars($bestelling_id); ?></div>
  </div>
  <?php endif; ?>

  <button class="button" onclick="window.location.href='/'">← Terug naar home</button>

  <div class="whatsapp">
    Vragen? <a href="https://wa.me/31617255170">WhatsApp: +31 6 17 25 51 70</a>
  </div>
</div>

</body>
</html>
