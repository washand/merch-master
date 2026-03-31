<?php
$PAGE_TITLE = 'Verzendkosten';
$PAGE_DESC  = 'Verzendkosten Merch Master — klein pakket 6,95, groot pakket 13,95, pallet 70 ex btw.';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding:4rem 2.5rem 5rem;">
  <div class="container" style="max-width:720px;">
    <div class="sec-kop" style="text-align:left;margin-bottom:2.5rem;">
      <div class="sec-oogje"><?= t('verzend_oogje') ?></div>
      <h2 style="font-size:clamp(2rem,4vw,2.8rem);"><?= t('verzend_h1') ?></h2>
      <p style="color:var(--ink2);line-height:1.8;margin-top:.75rem;"><?= t('verzend_intro') ?></p>
    </div>

    <!-- Nederland -->
    <div style="margin-bottom:2.5rem;">
      <h3 style="font-family:var(--display);font-size:1.2rem;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:2px solid rgba(196,98,45,.15);"><?= t('verzend_nl_h') ?></h3>
      <div style="display:flex;flex-direction:column;gap:.75rem;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;">
          <div>
            <div style="font-weight:600;font-size:.9rem;"><?= t('verzend_klein_l') ?></div>
            
          </div>
          <div style="font-family:var(--display);font-size:1.3rem;font-weight:700;color:var(--terracotta);">&euro;6,95</div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;">
          <div>
            <div style="font-weight:600;font-size:.9rem;"><?= t('verzend_groot_l') ?></div>
            
          </div>
          <div style="font-family:var(--display);font-size:1.3rem;font-weight:700;color:var(--terracotta);">&euro;13,95</div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;">
          <div>
            <div style="font-weight:600;font-size:.9rem;"><?= t('verzend_pallet_l') ?></div>
            <div style="font-size:.78rem;color:var(--ink3);margin-top:.2rem;"><?= t('verzend_pallet_p') ?></div>
          </div>
          <div style="font-family:var(--display);font-size:1.3rem;font-weight:700;color:var(--terracotta);">&euro;70</div>
        </div>

      </div>
    </div>

    <!-- Internationaal -->
    <div style="margin-bottom:2.5rem;">
      <h3 style="font-family:var(--display);font-size:1.2rem;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:2px solid rgba(196,98,45,.15);"><?= t('verzend_buit_h') ?></h3>
      <div style="padding:1.25rem;background:var(--zand);border-radius:10px;border:1px solid rgba(196,98,45,.12);">
        <p style="color:var(--ink2);line-height:1.8;font-size:.9rem;"><?= t('verzend_buit_p') ?></p>
        <div style="display:flex;justify-content:center;margin-top:1.25rem;">
          <a href="/contact.php" class="btn-arrow">
            <span class="btn-arrow-txt"><?= t('faq_btn') ?></span>
            <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
          </a>
        </div>
      </div>
    </div>

    <!-- Opmerking -->
    <div style="background:rgba(196,98,45,.06);border-left:3px solid var(--terracotta);padding:1rem 1.25rem;border-radius:0 8px 8px 0;font-size:.82rem;color:var(--ink2);line-height:1.75;">
      <?= t('verzend_opmerking') ?>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
