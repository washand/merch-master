<?php
$PAGE_TITLE = 'DTF Transferdruk';
$PAGE_DESC  = 'Alles over DTF transferdruk bij Merch Master. Full colour, vanaf 1 stuk, geen minimale oplage.';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding:4rem 2.5rem 5rem;">
  <div class="container">
    <div class="sec-kop" style="text-align:left;margin-bottom:2.5rem;">
      <div class="sec-oogje"><?= t('tech_pg_oogje') ?></div>
      <h2 style="font-size:clamp(2.2rem,5vw,3.2rem);">DTF <em><?= t('dtf_naam') ?></em></h2>
      <p style="font-size:1.05rem;color:var(--ink2);line-height:1.75;max-width:600px;margin-top:.75rem;"><?= t('dtf_txt') ?></p>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:3rem;" class="fade-in">
      <img src="/img/dtf_machine.jpg" alt="DTF printer" style="width:100%;height:300px;object-fit:cover;border-radius:12px;">
      <img src="/img/dtf2.jpg" alt="DTF resultaat" style="width:100%;height:300px;object-fit:cover;border-radius:12px;">
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:3rem;" class="fade-in">
      <div style="background:rgba(58,90,64,.08);border-radius:10px;padding:1.5rem;">
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--groen);margin-bottom:.85rem;"><?= t('tech_voordelen') ?></div>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:.5rem;">
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_v1') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_v2') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_v3') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_v4') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_v5') ?></li>
        </ul>
      </div>
      <div style="background:rgba(192,57,43,.06);border-radius:10px;padding:1.5rem;">
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#c0392b;margin-bottom:.85rem;"><?= t('tech_nadelen') ?></div>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:.5rem;">
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_n1') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_n2') ?></li>
        </ul>
      </div>
      <div style="background:var(--zand);border-radius:10px;padding:1.5rem;">
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--terracotta);margin-bottom:.85rem;"><?= t('tech_toepassing') ?></div>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:.5rem;">
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_t1') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_t2') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_t3') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_t4') ?></li>
          <li style="font-size:.85rem;color:var(--ink2);"><?= t('dtf_t5') ?></li>
        </ul>
      </div>
    </div>
    <div style="background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:12px;padding:2rem;margin-bottom:2rem;" class="fade-in">
      <h3 style="font-family:var(--display);font-size:1.4rem;margin-bottom:.75rem;"><?= t('dtf_hoe_h') ?></h3>
      <p style="color:var(--ink2);line-height:1.8;margin-bottom:1rem;"><?= t('dtf_hoe_1') ?></p>
      <p style="color:var(--ink2);line-height:1.8;"><?= t('dtf_hoe_2') ?></p>
    </div>
    <div style="background:var(--terracotta);padding:3.5rem 2.5rem;text-align:center;margin-top:3rem;border-radius:12px;position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 30px,rgba(0,0,0,.04) 30px,rgba(0,0,0,.04) 60px);"></div>
  <h2 style="color:#fff;font-size:1.8rem;margin-bottom:.75rem;position:relative;"><?= t('tech_cta_h2') ?></h2>
  <p style="color:rgba(255,255,255,.8);margin-bottom:1.75rem;position:relative;"><?= t('tech_cta_p') ?></p>
  <a href="/bestellen/" class="btn-arrow wit" style="position:relative;display:inline-flex;">
    <span class="btn-arrow-txt"><?= t('tech_cta_btn') ?></span>
    <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
  </a>
</div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
