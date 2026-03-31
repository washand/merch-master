<?php
$PAGE_TITLE = 'Print & Borduur Service';
$PAGE_DESC  = 'Print- en borduurservice voor festivals, evenementen en duurzame merken. DTF vanaf 1 stuk, zeefdruk en borduren.';
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section style="background:var(--donkergroen);padding:5rem 2.5rem 4rem;padding-top:calc(68px + 4rem);position:relative;overflow:hidden;">
  <div style="position:absolute;top:-80px;right:-80px;width:400px;height:400px;border-radius:50%;background:rgba(196,98,45,.12);pointer-events:none;"></div>
  <div style="position:absolute;bottom:-60px;left:-60px;width:280px;height:280px;border-radius:50%;background:rgba(90,122,96,.2);pointer-events:none;"></div>
  <div style="max-width:800px;margin:0 auto;text-align:center;position:relative;z-index:1;">
    <div class="hero-eyebrow" style="justify-content:center;"><?= t('hero_eyebrow') ?></div>
    <h1 style="font-family:var(--display);font-size:clamp(3rem,7vw,5rem);font-weight:900;line-height:1;color:var(--wit);margin-bottom:1.25rem;">
      <?= t('hero_h1a') ?><br>
      <?= t('hero_h1b') ?>
    </h1>
    <p style="font-size:1.1rem;color:rgba(250,247,242,.7);line-height:1.7;max-width:580px;margin:0 auto 2.5rem;"><?= t('hero_sub') ?></p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="/bestellen/" class="btn-arrow">
        <span class="btn-arrow-txt"><?= t('hero_btn1') ?></span>
        <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
      </a>
      <a href="/contact.php" class="btn-ghost"><?= t('hero_btn2') ?></a>
    </div>
    <div style="display:flex;justify-content:center;gap:2rem;margin-top:3rem;flex-wrap:wrap;border-top:1px solid rgba(250,247,242,.1);padding-top:2rem;">
      <div style="text-align:center;"><div style="font-family:var(--display);font-size:2rem;font-weight:700;color:var(--terracotta);">1</div><div style="font-size:.7rem;color:rgba(250,247,242,.45);text-transform:uppercase;letter-spacing:.08em;"><?= t('hero_s1') ?></div></div>
      <div style="text-align:center;"><div style="font-family:var(--display);font-size:2rem;font-weight:700;color:var(--terracotta);">100+</div><div style="font-size:.7rem;color:rgba(250,247,242,.45);text-transform:uppercase;letter-spacing:.08em;"><?= t('hero_s2') ?></div></div>
      <div style="text-align:center;"><div style="font-family:var(--display);font-size:2rem;font-weight:700;color:var(--terracotta);">3</div><div style="font-size:.7rem;color:rgba(250,247,242,.45);text-transform:uppercase;letter-spacing:.08em;"><?= t('hero_s3') ?></div></div>
    </div>
  </div>
</section>

<!-- HOE WERKT HET -->
<section class="hoe" id="hoe">
  <div class="container">
    <div class="sec-kop fade-in">
      <div class="sec-oogje"><?= t('hoe_oogje') ?></div>
      <h2><?= t('hoe_h2') ?></h2>
    </div>
    <div class="hoe-grid fade-in">
      <div class="hoe-stap"><div class="hoe-nr">1</div><div class="hoe-ttl"><?= t('hoe_1t') ?></div><p class="hoe-txt"><?= t('hoe_1b') ?></p></div>
      <div class="hoe-stap"><div class="hoe-nr">2</div><div class="hoe-ttl"><?= t('hoe_2t') ?></div><p class="hoe-txt"><?= t('hoe_2b') ?></p></div>
      <div class="hoe-stap"><div class="hoe-nr">3</div><div class="hoe-ttl"><?= t('hoe_3t') ?></div><p class="hoe-txt"><?= t('hoe_3b') ?></p></div>
      <div class="hoe-stap"><div class="hoe-nr">4</div><div class="hoe-ttl"><?= t('hoe_4t') ?></div><p class="hoe-txt"><?= t('hoe_4b') ?></p></div>
    </div>
    <div style="text-align:center;margin-top:3rem;">
      <a href="/bestellen/" class="btn-arrow" style="display:inline-flex;">
        <span class="btn-arrow-txt"><?= t('hoe_btn') ?></span>
        <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
      </a>
    </div>
  </div>
</section>

<!-- TECHNIEKEN -->
<section class="tech" id="technieken" style="background:var(--wit);">
  <div class="container">
    <div class="sec-kop fade-in">
      <div class="sec-oogje"><?= t('tech_oogje') ?></div>
      <h2><?= t('tech_h2') ?></h2>
      <p class="sec-sub"><?= t('tech_sub') ?></p>
    </div>
    <div class="tech-grid fade-in">
      <?php
      $technieken = [
        ['img'=>'dtf_machine.jpg','naam'=>t('dtf_naam'),'sub'=>t('dtf_sub'),'txt'=>t('dtf_txt'),'ideal'=>t('dtf_ideal_txt'),'letop'=>t('dtf_letop_txt'),'meer'=>t('dtf_meer'),'link'=>'/dtf.php'],
        ['img'=>'zeefdruk.jpg','naam'=>t('zeef_naam'),'sub'=>t('zeef_sub'),'txt'=>t('zeef_txt'),'ideal'=>t('zeef_ideal_txt'),'letop'=>t('zeef_letop_txt'),'meer'=>t('zeef_meer'),'link'=>'/zeefdruk.php'],
        ['img'=>'borduren.jpg','naam'=>t('bord_naam'),'sub'=>t('bord_sub'),'txt'=>t('bord_txt'),'ideal'=>t('bord_ideal_txt'),'letop'=>t('bord_letop_txt'),'meer'=>t('bord_meer'),'link'=>'/borduren.php'],
      ];
      foreach($technieken as $tech): ?>
      <div class="tech-card">
        <div class="tech-top">
          <img src="/img/<?= $tech['img'] ?>" alt="<?= $tech['naam'] ?>" style="width:calc(100% + 3.5rem);margin:-2rem -1.75rem 1.25rem;height:160px;object-fit:cover;border-radius:12px 12px 0 0;">
          <div class="tech-naam"><?= $tech['naam'] ?></div>
          <div class="tech-sub"><?= $tech['sub'] ?></div>
        </div>
        <div style="padding:0 1.75rem 1.25rem;font-size:.83rem;color:var(--ink2);line-height:1.6;flex:1;"><?= $tech['txt'] ?></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;border-top:1px solid rgba(196,98,45,.1);">
          <div style="padding:.85rem 1.75rem;border-right:1px solid rgba(196,98,45,.1);">
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--groen);margin-bottom:.4rem;"><?= t('dtf_ideal') ?></div>
            <div style="font-size:.75rem;color:var(--ink2);line-height:1.5;"><?= $tech['ideal'] ?></div>
          </div>
          <div style="padding:.85rem 1.75rem;">
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#c0392b;margin-bottom:.4rem;"><?= t('dtf_letop') ?></div>
            <div style="font-size:.75rem;color:var(--ink2);line-height:1.5;"><?= $tech['letop'] ?></div>
          </div>
        </div>
        <div style="padding:1.25rem 1.75rem;border-top:1px solid rgba(196,98,45,.1);display:flex;justify-content:center;">
          <a href="<?= $tech['link'] ?>" class="btn-arrow" style="min-width:auto;font-size:.82rem;">
            <span class="btn-arrow-txt"><?= $tech['meer'] ?></span>
            <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA MIDDEN -->
<div class="cta-mid">
  <h2><?= t('cta_h2') ?></h2>
  <p><?= t('cta_p') ?></p>
  <div class="cta-btns">
    <a href="/bestellen/" class="btn-groot">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      <span><?= t('cta_btn1') ?></span>
    </a>
    <a href="https://wa.me/31617255170" class="btn-lijn" target="_blank" style="display:inline-flex;align-items:center;gap:.5rem;"><?= $ICON_WA ?><?= t('cta_btn2') ?></a>
  </div>
</div>

<!-- REVIEWS -->
<section style="padding:4rem 2.5rem;background:var(--creme);">
  <div class="container">
    <div class="sec-kop fade-in">
      <div class="sec-oogje"><?= t('reviews_oogje') ?></div>
      <h2><?= t('reviews_h2') ?></h2>
    </div>
    <div class="reviews-scroll-wrap fade-in">
      <div class="reviews-cols">
        <?php
        $reviews = [
          ["Sophie L.","Utrecht","De printservice van Merch-Master.com is fantastisch! Ze hebben mijn verwachtingen overtroffen.","women/44"],
          ["Tim V.","Rotterdam","Super blij met het resultaat! Onze festival shirts zagen er geweldig uit.","men/32"],
          ["Lisa M.","Leiden","Snel geleverd en precies zoals ik het wilde. De kwaliteit is echt top.","women/68"],
          ["Jasper K.","Amsterdam","Geweldige service en kwaliteit! Mijn shirts zijn perfect bedrukt en snel geleverd. Alleen de levertijd had iets sneller gemogen.","men/22","4"],
          ["Sarah B.","Den Haag","Voor onze horeca uniforms precies wat we zochten. Professioneel geborduurde logo's.","women/55"],
          ["Marco D.","Amstelveen","Besteld voor ons voetbalteam. Resultaat was perfect en communicatie verliep soepel.","men/41"],
          ["Emma V.","Delft","Voor ons biologisch voedingsmerk prachtige eco-vriendelijke tote bags gedrukt.","women/29"],
          ["Antony R.","Zoetermeer","Onze groepsuitje shirts waren een hit. Snel besteld, snel ontvangen.","men/67"],
          ["Nora K.","Hoofddorp","Bij Merch Master ben ik eindelijk gevonden wat ik zocht.","women/12"],
        ];
        // Engelstalige reviews apart weergeven in kolom 2
        $reviews_en = [
          ["James R.","London, UK", null, "men/45", "review_en1"],
          ["Claire M.","Brussels, BE", null, "women/33", "review_en2"],
          ["Alex K.","Oslo, NO", null, "men/58", "review_en3"],
        ];
        $cols = array_chunk($reviews, 3);
        $col_idx = 0;
        foreach($cols as $col):
          $extra = ($col_idx === 1) ? $reviews_en : [];
          $col_idx++;
        ?>
        <div class="reviews-col"><div class="reviews-col-inner">
          <?php foreach(array_merge($col,$col) as $r): ?>
          <div class="review-scroll-card">
            <div class="review-scroll-stars"><?php $stars=isset($r[4])?(int)$r[4]:5; for($s=0;$s<$stars;$s++) echo "&#9733;"; for($s=$stars;$s<5;$s++) echo "<span style='color:var(--zand)'>&#9733;</span>"; ?></div>
            <p class="review-scroll-txt">"<?= htmlspecialchars($r[2]) ?>"</p>
            <div class="review-scroll-auteur">
              <img class="review-scroll-avatar" src="https://randomuser.me/api/portraits/<?= $r[3] ?>.jpg" alt="<?= $r[0] ?>">
              <div><div class="review-scroll-naam"><?= $r[0] ?></div><div class="review-scroll-stad"><?= $r[1] ?></div></div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php foreach(array_merge($extra,$extra) as $r): ?>
          <div class="review-scroll-card">
            <div class="review-scroll-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <p class="review-scroll-txt"><?= t($r[4]) ?></p>
            <div class="review-scroll-auteur">
              <img class="review-scroll-avatar" src="https://randomuser.me/api/portraits/<?= $r[3] ?>.jpg" alt="<?= $r[0] ?>">
              <div><div class="review-scroll-naam"><?= $r[0] ?></div><div class="review-scroll-stad"><?= $r[1] ?></div></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>


<!-- ECO BANNER -->
<div style="background:var(--donkergroen);padding:2.5rem 2.5rem;">
  <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem;">
    <div>
      <div style="font-size:.7rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:#86efac;margin-bottom:.5rem;"><?= t('eco_label') ?></div>
      <div style="font-family:var(--display);font-size:1.4rem;font-weight:700;color:var(--wit);margin-bottom:.3rem;"><?= t('eco_title') ?></div>
      <div style="font-size:.85rem;color:rgba(250,247,242,.55);"><?= t('eco_sub') ?></div>
    </div>
    <a href="/duurzaam.php" style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(250,247,242,.1);color:var(--wit);font-family:var(--body);font-size:.85rem;font-weight:600;padding:.75rem 1.5rem;border-radius:50px;text-decoration:none;border:1px solid rgba(250,247,242,.25);white-space:nowrap;transition:background .2s;" onmouseover="this.style.background='rgba(250,247,242,.18)'" onmouseout="this.style.background='rgba(250,247,242,.1)'"><?= t('eco_btn') ?></a>
  </div>
</div>

<!-- OVER ONS KORT -->
<section style="padding:4rem 2.5rem;background:var(--wit);">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center;" class="fade-in">
      <div>
        <div class="sec-oogje"><?= t('over_oogje') ?></div>
        <h2 style="margin-bottom:1rem;"><?= t('over_h2') ?></h2>
        <p style="font-size:.95rem;color:var(--ink2);line-height:1.8;margin-bottom:1.5rem;"><?= t('over_kort') ?></p>
        <a href="/over-ons.php" class="btn-arrow" style="display:inline-flex;">
          <span class="btn-arrow-txt"><?= t('over_lees_meer') ?></span>
          <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
        </a>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
        <div class="over-val"><div class="over-val-icon"><?= $ICON_BOLT ?></div><div class="over-val-ttl"><?= t('over_v1t') ?></div><div class="over-val-txt"><?= t('over_v1b') ?></div></div>
        <div class="over-val"><div class="over-val-icon"><?= $ICON_TARGET ?></div><div class="over-val-ttl"><?= t('over_v2t') ?></div><div class="over-val-txt"><?= t('over_v2b') ?></div></div>
        <div class="over-val"><div class="over-val-icon"><?= $ICON_LEAF ?></div><div class="over-val-ttl"><?= t('over_v3t') ?></div><div class="over-val-txt"><?= t('over_v3b') ?></div></div>
        <div class="over-val"><div class="over-val-icon"><?= $ICON_CHAT ?></div><div class="over-val-ttl"><?= t('over_v4t') ?></div><div class="over-val-txt"><?= t('over_v4b') ?></div></div>
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
      <div class="merk-card"><img src="/img/logo_byb.png" alt="Build Your Brand" class="merk-logo"></div>
      <div class="merk-card"><img src="/img/logo_gildan.png" alt="Gildan" class="merk-logo"></div>
      <div class="merk-card"><img src="/img/logo_asquith.png" alt="Asquith &amp; Fox" class="merk-logo"></div>
      <div class="merk-card"><img src="/img/logo_bc.png" alt="B&amp;C Collection" class="merk-logo"></div>
      <div class="merk-card"><img src="/img/logo_flexfit.png" alt="Flexfit / Yupoong" class="merk-logo"></div>
      <div class="merk-card"><img src="/img/logo_anthem.png" alt="Anthem" class="merk-logo"></div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>