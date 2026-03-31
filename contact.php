<?php
$PAGE_TITLE = 'Contact';
$PAGE_DESC  = 'Neem contact op met Merch Master voor print- en borduurservice.';
require_once __DIR__ . '/includes/header.php';
?>
<section class="contact" style="padding-top:4rem;padding-bottom:5rem;">
  <div class="container">
    <div class="sec-kop fade-in">
      <div class="sec-oogje"><?= t('cont_oogje') ?></div>
      <h2><?= t('cont_h2') ?></h2>
    </div>
    <div class="contact-grid fade-in">
      <div>
        <p class="contact-info" style="font-size:.95rem;color:var(--ink2);line-height:1.8;margin-bottom:2rem;"><?= t('cont_p') ?></p>
        <div class="contact-methods">
          <a href="https://wa.me/31617255170" class="contact-method" target="_blank">
            <div class="cm-icon cm-wa"><?= $ICON_WA ?></div>
            <div class="cm-info"><strong>WhatsApp</strong><span>+31 6 17 25 51 70 — <?= t('cont_wa') ?></span></div>
          </a>
          <a href="mailto:info@merch-master.com" class="contact-method">
            <div class="cm-icon cm-mail"><?= $ICON_MAIL ?></div>
            <div class="cm-info"><strong>E-mail</strong><span>info@merch-master.com</span></div>
          </a>
          <a href="https://instagram.com/merchmastercom" class="contact-method" target="_blank">
            <div class="cm-icon cm-ig"><?= $ICON_IG ?></div>
            <div class="cm-info"><strong><?= t('soc_instagram') ?></strong><span>@merchmastercom</span></div>
          </a>
        </div>
      </div>
      <div class="contact-form">
        <div class="form-ttl"><?= t('cont_form_ttl') ?></div>
        <div class="form-sub"><?= t('cont_form_sub') ?></div>
        <div id="form-ok" style="display:none;background:var(--groen);color:#fff;padding:1rem;border-radius:8px;margin-bottom:1rem;"><?= t('cont_ok') ?></div>
        <div id="form-err" style="display:none;background:#c0392b;color:#fff;padding:1rem;border-radius:8px;margin-bottom:1rem;"></div>
        <form id="cf" onsubmit="verstuurContact(event)">
          <input type="text" name="website" style="display:none"> <!-- honeypot -->
          <div class="f-row">
            <div class="f-group"><label><?= t('cont_vnaam') ?></label><input type="text" name="naam"></div>
            <div class="f-group"><label><?= t('cont_email') ?> *</label><input type="email" name="email" required></div>
          </div>
          <div class="f-group">
            <label><?= t('cont_onderwerp') ?></label>
            <select name="onderwerp">
              <option value=""><?= t('cont_ph_sub') ?></option>
              <option><?= t('cont_opt4') ?></option>
              <option><?= t('cont_opt5') ?></option>
              <option><?= t('cont_opt3') ?></option>
              <option><?= t('cont_opt6') ?></option>
            </select>
          </div>
          <div class="f-group">
            <label><?= t('cont_bericht') ?> *</label>
            <textarea name="bericht" required placeholder="<?= htmlspecialchars(t('cont_ph_bericht')) ?>"></textarea>
          </div>
          <button type="submit" class="f-send" id="cf-btn"><?= t('cont_verzend') ?></button>
        </form>
      </div>
    </div>
  </div>
</section>
<script>
async function verstuurContact(e) {
  e.preventDefault();
  const btn = document.getElementById('cf-btn');
  btn.disabled = true;
  btn.textContent = '...';
  const data = new FormData(document.getElementById('cf'));
  try {
    const r = await fetch('/includes/contact_handler.php', {method:'POST', body: data});
    const j = await r.json();
    if (j.ok) {
      document.getElementById('form-ok').style.display = 'block';
      document.getElementById('cf').reset();
    } else {
      const err = document.getElementById('form-err');
      err.textContent = j.msg;
      err.style.display = 'block';
    }
  } catch(err) {
    document.getElementById('form-err').textContent = '<?= addslashes(t("cont_err_wa")) ?>';
    document.getElementById('form-err').style.display = 'block';
  }
  btn.disabled = false;
  btn.textContent = '<?= t('cont_verzend') ?>';
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
