<?php
$PAGE_TITLE = htmlspecialchars('FAQ');
$PAGE_DESC  = 'Veelgestelde vragen over bedrukken, levertijden en bestellingen bij Merch Master.';
require_once __DIR__ . '/includes/header.php';
?>
<section class="faq" style="padding-top:4rem;padding-bottom:5rem;">
  <div class="container">
    <div class="sec-kop fade-in">
      <div class="sec-oogje"><?= t('faq_oogje') ?></div>
      <h2><?= t('faq_h2') ?></h2>
      <p class="sec-sub"><?= t('faq_sub') ?></p>
    </div>
    <div class="faq-lijst fade-in">
      <div class="faq-item">
        <div class="faq-nr">1</div>
        <div class="faq-body"><h3><?= t('faq_q1') ?></h3><p><?= t('faq_a1') ?></p></div>
      </div>
      <div class="faq-item">
        <div class="faq-nr">2</div>
        <div class="faq-body"><h3><?= t('faq_q2') ?></h3><p><?= t('faq_a2') ?></p></div>
      </div>
      <div class="faq-item">
        <div class="faq-nr">3</div>
        <div class="faq-body"><h3><?= t('faq_q3') ?></h3><p><?= t_lt('faq_a3') ?></p></div>
      </div>
      <div class="faq-item">
        <div class="faq-nr">4</div>
        <div class="faq-body"><h3><?= t('faq_q4') ?></h3><p><?= t('faq_a4') ?></p></div>
      </div>
      <div class="faq-item">
        <div class="faq-nr">5</div>
        <div class="faq-body"><h3><?= t('faq_q5') ?></h3><p><?= t('faq_a5') ?></p></div>
      </div>
      <div class="faq-item">
        <div class="faq-nr">6</div>
        <div class="faq-body"><h3><?= t('faq_q6') ?></h3><p><?= t('faq_a6') ?></p></div>
      </div>
      <div class="faq-item">
        <div class="faq-nr">7</div>
        <div class="faq-body"><h3><?= t('faq_q7') ?></h3><p><?= t('faq_a7') ?></p></div>
      </div>
      <div class="faq-item">
        <div class="faq-nr">8</div>
        <div class="faq-body"><h3><?= t('faq_q8') ?></h3><p><?= t('faq_a8') ?></p></div>
      </div></div>
    <div style="text-align:center;margin-top:3rem;">
      <a href="/contact.php" class="btn-arrow" style="display:inline-flex;">
        <span class="btn-arrow-txt"><?= t('faq_btn') ?></span>
        <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
      </a>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
