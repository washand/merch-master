<?php
$PAGE_TITLE = 'Pagina niet gevonden';
$PAGE_DESC  = 'Deze pagina bestaat niet.';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding:8rem 2.5rem;text-align:center;min-height:60vh;display:flex;align-items:center;justify-content:center;">
  <div>
    <div style="font-family:var(--display);font-size:8rem;font-weight:900;color:var(--terracotta);line-height:1;margin-bottom:1rem;">404</div>
    <h1 style="font-size:2rem;margin-bottom:1rem;"><?php
      $msgs = ['nl'=>'Pagina niet gevonden','en'=>'Page not found','de'=>'Seite nicht gefunden','no'=>'Side ikke funnet'];
      echo $msgs[$TAAL] ?? $msgs['nl'];
    ?></h1>
    <p style="color:var(--ink2);margin-bottom:2.5rem;max-width:400px;margin-left:auto;margin-right:auto;"><?php
      $subs = ['nl'=>'De pagina die je zoekt bestaat niet of is verplaatst.','en'=>'The page you are looking for does not exist or has been moved.','de'=>'Die gesuchte Seite existiert nicht oder wurde verschoben.','no'=>'Siden du leter etter finnes ikke eller er flyttet.'];
      echo $subs[$TAAL] ?? $subs['nl'];
    ?></p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="/" class="btn-arrow">
        <span class="btn-arrow-txt"><?php
          $home = ['nl'=>'Terug naar home','en'=>'Back to home','de'=>'Zurück zur Startseite','no'=>'Tilbake til hjem'];
          echo $home[$TAAL] ?? $home['nl'];
        ?></span>
        <span class="btn-arrow-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
      </a>
      <a href="/contact.php" class="btn-ghost"><?php
        $cont = ['nl'=>'Contact opnemen','en'=>'Contact us','de'=>'Kontakt aufnehmen','no'=>'Kontakt oss'];
        echo $cont[$TAAL] ?? $cont['nl'];
      ?></a>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
