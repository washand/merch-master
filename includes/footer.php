<?php // footer — taal al geladen via header ?>
</div>
<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div class="footer-brand">
        <a href="/" class="footer-logo">
          <div class="footer-logo-mark"><svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
          <span style="margin-left:.4rem;">Merch<span style="color:var(--terracotta)">Master</span></span>
        </a>
        <p><?= t('footer_tagline') ?></p>
      </div>
      <div class="footer-col">
        <h4><?= t('footer_diensten') ?></h4>
        <a href="/dtf.php"><?= t('footer_dtf') ?></a>
        <a href="/zeefdruk.php"><?= t('footer_zeef') ?></a>
        <a href="/borduren.php"><?= t('footer_bord') ?></a>
        <a href="/bestellen/"><?= t('footer_bestel') ?></a>
        <a href="/duurzaam.php"><?= t('footer_duurzaam') ?></a>
      </div>
      <div class="footer-col">
        <h4><?= t('footer_service') ?></h4>
        <a href="/portaal.php"><?= t('footer_account') ?></a>
        <a href="/contact.php"><?= t('footer_contact') ?></a>
        <a href="/faq.php"><?= t('footer_faq') ?></a>
        <a href="/verzendkosten.php"><?= t('footer_verzend') ?></a>
      </div>
      <div class="footer-col">
        <h4><?= t('footer_cont_ttl') ?></h4>
        <a href="mailto:info@merch-master.com" style="display:flex;align-items:center;gap:.4rem;"><?= $ICON_MAIL ?>info@merch-master.com</a>
        <a href="https://wa.me/31617255170" style="display:flex;align-items:center;gap:.4rem;"><?= $ICON_WA ?><?= t('footer_wa') ?></a>
        <a href="https://instagram.com/merchmastercom" target="_blank" style="display:flex;align-items:center;gap:.4rem;"><?= $ICON_IG ?><?= t('soc_instagram') ?></a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> Merch Master. <?= t('footer_rechten') ?></span>
      <div style="display:flex;gap:1.5rem;">
        <a href="/privacy.php"><?= t('privacy') ?></a>
        <a href="/voorwaarden.php"><?= t('voorwaarden') ?></a>
      </div>
    </div>
  </div>
</footer>
<a href="https://wa.me/31617255170" class="wa-float" target="_blank" aria-label="WhatsApp">
  <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/><path d="M11.5 0C5.159 0 0 5.159 0 11.5c0 2.065.549 4.002 1.508 5.674L0 23l5.974-1.484A11.45 11.45 0 0011.5 23C17.841 23 23 17.841 23 11.5S17.841 0 11.5 0zm0 21.077a9.54 9.54 0 01-4.976-1.395l-.356-.214-3.695.917.944-3.606-.235-.372A9.522 9.522 0 011.923 11.5C1.923 6.216 6.216 1.923 11.5 1.923S21.077 6.216 21.077 11.5 16.784 21.077 11.5 21.077z"/></svg>
</a>
<script>
function toggleMenu(){ document.getElementById('mob-menu').classList.toggle('open'); }
function toggleLangMenu(){ document.getElementById('lang-menu').classList.toggle('open'); }
document.addEventListener('click',e=>{
  const w=document.querySelector('.lang-wrap');
  if(w&&!w.contains(e.target)){const m=document.getElementById('lang-menu');if(m)m.classList.remove('open');}
});
const obs=new IntersectionObserver(els=>{els.forEach(e=>{if(e.isIntersecting)e.target.classList.add('zichtbaar');});},{threshold:.1});
document.querySelectorAll('.fade-in').forEach(el=>obs.observe(el));
</script>
</body>
</html>
