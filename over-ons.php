<?php
$PAGE_TITLE = htmlspecialchars('Over ons');
$PAGE_DESC  = 'Merch Master — print- en borduurservice voor particulieren, festivals en ondernemers.';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding:4rem 2.5rem 5rem;">
  <div class="container">
    <div class="sec-kop" style="text-align:left;margin-bottom:2.5rem;">
      <div class="sec-oogje"><?= t('over_oogje') ?></div>
      <h2 style="font-size:clamp(2.2rem,5vw,3.2rem);"><?= t('over_h2') ?></h2>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:start;margin-bottom:3rem;" class="fade-in">
      <div>
        <p style="font-size:.95rem;color:var(--ink2);line-height:1.8;margin-bottom:1rem;"><?= t('over_p1') ?></p>
        <p style="font-size:.95rem;color:var(--ink2);line-height:1.8;margin-bottom:1rem;"><?= t('over_p2') ?></p>
        <p style="font-size:.95rem;color:var(--ink2);line-height:1.8;margin-bottom:2rem;"><?= t('over_p3') ?></p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem;margin-bottom:2rem;"><div class="over-val"><div class="over-val-icon"><?= $ICON_BOLT ?></div><div class="over-val-ttl"><?= t('over_v1t') ?></div><div class="over-val-txt"><?= t('over_v1b') ?></div></div><div class="over-val"><div class="over-val-icon"><?= $ICON_TARGET ?></div><div class="over-val-ttl"><?= t('over_v2t') ?></div><div class="over-val-txt"><?= t('over_v2b') ?></div></div><div class="over-val"><div class="over-val-icon"><?= $ICON_LEAF ?></div><div class="over-val-ttl"><?= t('over_v3t') ?></div><div class="over-val-txt"><?= t('over_v3b') ?></div></div><div class="over-val"><div class="over-val-icon"><?= $ICON_CHAT ?></div><div class="over-val-ttl"><?= t('over_v4t') ?></div><div class="over-val-txt"><?= t('over_v4b') ?></div></div></div>
        <a href="/bestellen/" class="btn-arrow" style="display:inline-flex;">
          <span class="btn-arrow-txt"><?= t('over_direct') ?></span>
          <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
        </a>
      </div>
      <div>
        <img src="/img/ramen.jpg" alt="Voorbeeld bedrukking" style="width:100%;border-radius:12px;margin-bottom:1rem;">
        <img src="/img/dtf2.jpg" alt="DTF voorbeeld" style="width:100%;border-radius:12px;">
      </div>
    </div>
  </div>
</section>

<!-- MERKEN -->
<div style="background:var(--creme);padding:3rem 2.5rem;border-top:1px solid rgba(196,98,45,.1);">
  <div style="max-width:1100px;margin:0 auto;">
    <div style="text-align:center;margin-bottom:2rem;">
      <div style="font-size:.7rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--ink3);"><?= t('merken_titel') ?></div>
    </div>
    <div style="display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:1.25rem;">

      <div class="merk-card">
        <img src="/img/logo_byb.png" alt="Build Your Brand" class="merk-logo">
      </div>
      <div class="merk-card">
        <img src="/img/logo_gildan.png" alt="Gildan" class="merk-logo">
      </div>
      <div class="merk-card">
        <img src="/img/logo_asquith.png" alt="Asquith & Fox" class="merk-logo">
      </div>
      <div class="merk-card">
        <img src="/img/logo_bc.png" alt="B&C Collection" class="merk-logo">
      </div>
      <div class="merk-card">
        <img src="/img/logo_flexfit.png" alt="Flexfit / Yupoong" class="merk-logo">
      </div>
      <div class="merk-card">
        <img src="/img/logo_anthem.png" alt="Anthem" class="merk-logo">
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
