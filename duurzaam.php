<?php
$PAGE_TITLE = 'Duurzame opties';
$PAGE_DESC  = 'Biologisch katoen, gerecyclede materialen en Fair Wear gecertificeerde merken bij Merch Master.';
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section style="background:var(--donkergroen);padding:5rem 2.5rem 4rem;padding-top:calc(68px + 3rem);position:relative;overflow:hidden;">
  <div style="position:absolute;top:-60px;right:-60px;width:350px;height:350px;border-radius:50%;background:rgba(90,122,96,.25);pointer-events:none;"></div>
  <div style="position:absolute;bottom:-40px;left:-40px;width:220px;height:220px;border-radius:50%;background:rgba(196,98,45,.1);pointer-events:none;"></div>
  <div style="max-width:720px;margin:0 auto;text-align:center;position:relative;z-index:1;">
    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(134,239,172,.15);color:#86efac;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:.4rem 1rem;border-radius:20px;margin-bottom:1.5rem;border:1px solid rgba(134,239,172,.2);"><?= $ICON_LEAF ?>&nbsp;<?= t('eco_page_title') ?></div>
    <h1 style="font-family:var(--display);font-size:clamp(2.5rem,6vw,4rem);font-weight:900;line-height:1.05;color:var(--wit);margin-bottom:1.25rem;"><?= t('eco_h1') ?></h1>
    <p style="font-size:1.05rem;color:rgba(250,247,242,.7);line-height:1.75;max-width:580px;margin:0 auto;"><?= t('eco_intro') ?></p>
  </div>
</section>

<!-- OPTIES -->
<section style="padding:5rem 2.5rem;">
  <div class="container">

    <?php
    $opties = [
      ['key_h'=>'eco_gots_h','key_p'=>'eco_gots_p','kleur'=>'rgba(58,90,64,.08)','rand'=>'rgba(58,90,64,.2)','label'=>'GOTS'],
      ['key_h'=>'eco_rpet_h','key_p'=>'eco_rpet_p','kleur'=>'rgba(26,94,138,.07)','rand'=>'rgba(26,94,138,.2)','label'=>'rPET'],
      ['key_h'=>'eco_fw_h','key_p'=>'eco_fw_p','kleur'=>'rgba(196,98,45,.07)','rand'=>'rgba(196,98,45,.2)','label'=>'Fair Wear'],
      ['key_h'=>'eco_vegan_h','key_p'=>'eco_vegan_p','kleur'=>'rgba(90,50,120,.07)','rand'=>'rgba(90,50,120,.2)','label'=>'Vegan'],
    ];
    foreach($opties as $opt): ?>
    <div style="display:grid;grid-template-columns:auto 1fr;gap:2rem;align-items:start;margin-bottom:3rem;padding:2rem;background:<?= $opt['kleur'] ?>;border:1px solid <?= $opt['rand'] ?>;border-radius:14px;" class="fade-in">
      <div style="background:<?= $opt['rand'] ?>;color:var(--donkergroen);font-size:.72rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;padding:.5rem 1rem;border-radius:8px;white-space:nowrap;margin-top:.2rem;"><?= $opt['label'] ?></div>
      <div>
        <h3 style="font-family:var(--display);font-size:1.4rem;margin-bottom:.65rem;"><?= t($opt['key_h']) ?></h3>
        <p style="color:var(--ink2);line-height:1.8;font-size:.95rem;"><?= t($opt['key_p']) ?></p>
      </div>
    </div>
    <?php endforeach; ?>



  </div>
</section>

<!-- CTA -->
<section style="padding:0 2.5rem 5rem;">
  <div class="container">
    <div style="background:var(--donkergroen);border-radius:16px;padding:3.5rem 2.5rem;text-align:center;position:relative;overflow:hidden;">
      <div style="position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 40px,rgba(255,255,255,.02) 40px,rgba(255,255,255,.02) 80px);pointer-events:none;"></div>
      <h2 style="color:var(--wit);font-size:clamp(1.6rem,4vw,2.4rem);margin-bottom:.85rem;position:relative;"><?= t('eco_cta_h') ?></h2>
      <p style="color:rgba(250,247,242,.65);line-height:1.75;max-width:520px;margin:0 auto 2rem;position:relative;"><?= t('eco_cta_p') ?></p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;position:relative;">
        <a href="/bestellen.php" class="btn-arrow">
          <span class="btn-arrow-txt"><?= t('eco_btn_bestel') ?></span>
          <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
        </a>
        <a href="/contact.php" class="btn-ghost" style="border-color:rgba(250,247,242,.3);color:var(--wit);"><?= t('eco_btn_contact') ?></a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
