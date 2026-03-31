<?php
$PAGE_TITLE = 'Levertijden';
$PAGE_DESC  = 'Levertijden Merch Master — DTF, zeefdruk en borduren.';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding:4rem 2.5rem 5rem;">
  <div class="container" style="max-width:720px;">
    <div class="sec-kop" style="text-align:left;margin-bottom:2.5rem;">
      <div class="sec-oogje"><?= t('levert_oogje') ?></div>
      <h2 style="font-size:clamp(2rem,4vw,2.8rem);"><?= t('levert_h1') ?></h2>
      <p style="color:var(--ink2);line-height:1.8;margin-top:.75rem;"><?= t('levert_intro') ?></p>
    </div>

    <!-- Technieken -->
    <div style="display:flex;flex-direction:column;gap:.75rem;margin-bottom:2.5rem;">

      <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.25rem;background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;">
        <div style="font-weight:600;font-size:.95rem;"><?= t('levert_dtf_l') ?></div>
        <div style="font-family:var(--display);font-size:1.2rem;font-weight:700;color:var(--terracotta);"><?= t_lt('{lt_dtf}') ?: '5&ndash;8' ?> <span style="font-size:.75rem;font-weight:400;color:var(--ink3);"><?= t('levert_werkdag') ?></span></div>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.25rem;background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;">
        <div style="font-weight:600;font-size:.95rem;"><?= t('levert_zeef_l') ?></div>
        <div style="font-family:var(--display);font-size:1.2rem;font-weight:700;color:var(--terracotta);"><?= t_lt('{lt_zeef}') ?: '6&ndash;10' ?> <span style="font-size:.75rem;font-weight:400;color:var(--ink3);"><?= t('levert_werkdag') ?></span></div>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.25rem;background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;">
        <div style="font-weight:600;font-size:.95rem;"><?= t('levert_bord_l') ?></div>
        <div style="font-family:var(--display);font-size:1.2rem;font-weight:700;color:var(--terracotta);"><?= t_lt('{lt_bord}') ?: '7&ndash;12' ?> <span style="font-size:.75rem;font-weight:400;color:var(--ink3);"><?= t('levert_werkdag') ?></span></div>
      </div>

    </div>

    <!-- Info -->
    <div style="background:rgba(196,98,45,.06);border-left:3px solid var(--terracotta);padding:1rem 1.25rem;border-radius:0 8px 8px 0;font-size:.82rem;color:var(--ink2);line-height:1.75;margin-bottom:1.5rem;">
      <?= t('levert_info') ?>
    </div>

    <!-- Spoed -->
    <div style="background:var(--zand);border-radius:10px;padding:1.25rem;border:1px solid rgba(196,98,45,.12);">
      <div style="font-weight:700;font-size:.95rem;margin-bottom:.5rem;"><?= t('levert_spoed') ?></div>
      <p style="color:var(--ink2);font-size:.85rem;line-height:1.75;margin-bottom:1rem;"><?= t('levert_spoed_p') ?></p>
      <a href="/contact.php" class="btn-arrow" style="display:inline-flex;font-size:.82rem;">
        <span class="btn-arrow-txt"><?= t('faq_btn') ?></span>
        <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
      </a>
    </div>

  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
