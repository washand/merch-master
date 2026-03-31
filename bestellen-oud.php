<?php
$PAGE_TITLE = 'Bestellen (oud)';
$PAGE_DESC  = 'Bestel direct online bij Merch Master. Kies je textiel, techniek en upload je ontwerp.';
require_once __DIR__ . '/includes/header.php';
?>
<style>
.besteltool-frame {
  width: 100%;
  min-height: calc(100vh - 68px);
  border: none;
  display: block;
}
</style>

<iframe
  src="/bestellen/"
  class="besteltool-frame"
  id="besteltool-iframe"
  scrolling="yes"
  allowfullscreen>
</iframe>

<script>
// Auto-resize iframe mee met content
(function(){
  var iframe = document.getElementById('besteltool-iframe');
  function resize(){
    try {
      var h = iframe.contentWindow.document.body.scrollHeight;
      if(h > 200) iframe.style.height = h + 'px';
    } catch(e) {}
  }
  iframe.addEventListener('load', function(){
    resize();
    setInterval(resize, 500);
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
