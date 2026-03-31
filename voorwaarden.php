<?php
$PAGE_TITLE = 'Algemene Voorwaarden';
$PAGE_DESC  = 'Algemene voorwaarden van Merch Master voor print- en borduurbestellingen.';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding:4rem 2.5rem 5rem;">
  <div class="container" style="max-width:800px;">
    <div class="sec-kop" style="text-align:left;margin-bottom:2.5rem;">
      <div class="sec-oogje"><?= t('voorwaarden_title') ?></div>
      <h2 style="font-size:clamp(2rem,4vw,2.8rem);"><?= t('voorwaarden_title') ?></h2>
      <p style="color:var(--ink2);font-size:.9rem;margin-top:.5rem;"><?php
        $last = ['nl'=>'Versie 1.0 — maart 2026','en'=>'Version 1.0 — March 2026','de'=>'Version 1.0 — März 2026','no'=>'Versjon 1.0 — mars 2026'];
        echo $last[$TAAL] ?? $last['nl'];
      ?></p>
    </div>

    <?php
    $sections = [
      ['nl'=>'Artikel 1 — Definities','en'=>'Article 1 — Definitions','de'=>'Artikel 1 — Definitionen','no'=>'Artikkel 1 — Definisjoner'],
      ['nl'=>'In deze algemene voorwaarden wordt verstaan onder: Merch Master: de eenmanszaak die print- en borduurservices aanbiedt; Klant: de natuurlijke of rechtspersoon die een bestelling plaatst; Bestelling: de opdracht van de klant aan Merch Master voor het bedrukken of borduren van textiel.','en'=>'In these terms and conditions: Merch Master: the sole trader offering print and embroidery services; Customer: the natural or legal person placing an order; Order: the customer\'s assignment to Merch Master for printing or embroidering textile.','de'=>'In diesen AGB bezeichnet: Merch Master: das Einzelunternehmen, das Druck- und Stickdienstleistungen anbietet; Kunde: die natürliche oder juristische Person, die eine Bestellung aufgibt; Bestellung: der Auftrag des Kunden an Merch Master zum Bedrucken oder Besticken von Textilien.','no'=>'I disse vilkårene: Merch Master: enkeltpersonsforetaket som tilbyr trykk- og broderingstjenester; Kunde: den fysiske eller juridiske personen som legger inn en bestilling; Bestilling: kundens oppdrag til Merch Master for trykking eller broderi av tekstil.'],

      ['nl'=>'Artikel 2 — Toepasselijkheid','en'=>'Article 2 — Applicability','de'=>'Artikel 2 — Anwendbarkeit','no'=>'Artikkel 2 — Anvendelse'],
      ['nl'=>'Deze algemene voorwaarden zijn van toepassing op alle aanbiedingen, offertes en overeenkomsten van Merch Master. Afwijkingen zijn alleen geldig indien schriftelijk overeengekomen.','en'=>'These terms and conditions apply to all offers, quotations and agreements of Merch Master. Deviations are only valid if agreed in writing.','de'=>'Diese AGB gelten für alle Angebote, Kostenvoranschläge und Vereinbarungen von Merch Master. Abweichungen sind nur gültig, wenn sie schriftlich vereinbart wurden.','no'=>'Disse vilkårene gjelder for alle tilbud, pristilbud og avtaler fra Merch Master. Avvik er bare gyldige hvis de er avtalt skriftlig.'],

      ['nl'=>'Artikel 3 — Bestelling & Bevestiging','en'=>'Article 3 — Order & Confirmation','de'=>'Artikel 3 — Bestellung & Bestätigung','no'=>'Artikkel 3 — Bestilling & Bekreftelse'],
      ['nl'=>'Een bestelling komt tot stand na schriftelijke of digitale bevestiging door Merch Master. Na akkoord op de digitale proef (mock-up) is de bestelling definitief en kan deze niet meer worden gewijzigd of geannuleerd.','en'=>'An order is established after written or digital confirmation by Merch Master. After approval of the digital proof (mock-up) the order is final and can no longer be changed or cancelled.','de'=>'Eine Bestellung kommt nach schriftlicher oder digitaler Bestätigung durch Merch Master zustande. Nach Genehmigung des digitalen Entwurfs (Mock-up) ist die Bestellung endgültig und kann nicht mehr geändert oder storniert werden.','no'=>'En bestilling etableres etter skriftlig eller digital bekreftelse fra Merch Master. Etter godkjenning av det digitale beviset (mock-up) er bestillingen endelig og kan ikke lenger endres eller kanselleres.'],

      ['nl'=>'Artikel 4 — Prijzen & Betaling','en'=>'Article 4 — Prices & Payment','de'=>'Artikel 4 — Preise & Zahlung','no'=>'Artikkel 4 — Priser & Betaling'],
      ['nl'=>'Alle prijzen zijn in euro\'s en inclusief BTW (tenzij anders vermeld). Betaling geschiedt via PayPal of een andere overeengekomen betaalmethode. Bij niet-tijdige betaling behoudt Merch Master het recht de bestelling te staken.','en'=>'All prices are in euros and include VAT (unless stated otherwise). Payment is made via PayPal or another agreed payment method. In case of late payment Merch Master reserves the right to suspend the order.','de'=>'Alle Preise sind in Euro und inklusive MwSt. (sofern nicht anders angegeben). Die Zahlung erfolgt über PayPal oder eine andere vereinbarte Zahlungsmethode. Bei nicht rechtzeitiger Zahlung behält sich Merch Master das Recht vor, die Bestellung einzustellen.','no'=>'Alle priser er i euro og inkluderer moms (med mindre annet er angitt). Betaling skjer via PayPal eller en annen avtalt betalingsmetode. Ved sen betaling forbeholder Merch Master seg retten til å stanse bestillingen.'],

      ['nl'=>'Artikel 5 — Levertijd','en'=>'Article 5 — Delivery time','de'=>'Artikel 5 — Lieferzeit','no'=>'Artikkel 5 — Leveringstid'],
      ['nl'=>'De opgegeven levertijden zijn indicatief. DTF-druk: 5–8 werkdagen, zeefdruk: 6–10 werkdagen, borduren: 7–12 werkdagen na akkoord op de proef. Merch Master is niet aansprakelijk voor vertragingen door overmacht of toeleveranciers.','en'=>'The stated delivery times are indicative. DTF printing: 5–8 working days, screen printing: 6–10 working days, embroidery: 7–12 working days after approval of the proof. Merch Master is not liable for delays due to force majeure or suppliers.','de'=>'Die angegebenen Lieferzeiten sind Richtwerte. DTF-Druck: 5–8 Werktage, Siebdruck: 6–10 Werktage, Stickerei: 7–12 Werktage nach Genehmigung des Entwurfs. Merch Master haftet nicht für Verzögerungen aufgrund höherer Gewalt oder Lieferanten.','no'=>'De oppgitte leveringstidene er veiledende. DTF-trykk: 5–8 arbeidsdager, silketrykk: 6–10 arbeidsdager, broderi: 7–12 arbeidsdager etter godkjenning av beviset. Merch Master er ikke ansvarlig for forsinkelser på grunn av force majeure eller leverandører.'],

      ['nl'=>'Artikel 6 — Auteursrecht & Aansprakelijkheid','en'=>'Article 6 — Copyright & Liability','de'=>'Artikel 6 — Urheberrecht & Haftung','no'=>'Artikkel 6 — Opphavsrett & Ansvar'],
      ['nl'=>'De klant garandeert dat het aangeleverde ontwerp vrij is van rechten van derden. Merch Master aanvaardt geen aansprakelijkheid voor inbreuk op intellectuele eigendomsrechten door door de klant aangeleverd materiaal. Merch Master is niet aansprakelijk voor indirecte schade of gevolgschade.','en'=>'The customer guarantees that the supplied design is free of third-party rights. Merch Master accepts no liability for infringement of intellectual property rights by material supplied by the customer. Merch Master is not liable for indirect or consequential damage.','de'=>'Der Kunde garantiert, dass das gelieferte Design frei von Rechten Dritter ist. Merch Master übernimmt keine Haftung für die Verletzung von Rechten des geistigen Eigentums durch vom Kunden geliefertes Material. Merch Master haftet nicht für indirekte Schäden oder Folgeschäden.','no'=>'Kunden garanterer at det leverte designet er fritt for tredjeparts rettigheter. Merch Master aksepterer intet ansvar for krenkelse av immaterielle rettigheter ved materiale levert av kunden. Merch Master er ikke ansvarlig for indirekte skade eller følgeskade.'],

      ['nl'=>'Artikel 7 — Klachten','en'=>'Article 7 — Complaints','de'=>'Artikel 7 — Beschwerden','no'=>'Artikkel 7 — Klager'],
      ['nl'=>'Klachten over de uitvoering van de bestelling dienen binnen 7 dagen na ontvangst schriftelijk te worden gemeld via info@merch-master.com. Merch Master zal klachten zo snel mogelijk afhandelen.','en'=>'Complaints about the execution of the order must be reported in writing within 7 days of receipt via info@merch-master.com. Merch Master will handle complaints as quickly as possible.','de'=>'Beschwerden über die Ausführung der Bestellung müssen innerhalb von 7 Tagen nach Erhalt schriftlich unter info@merch-master.com gemeldet werden. Merch Master wird Beschwerden so schnell wie möglich bearbeiten.','no'=>'Klager på gjennomføringen av bestillingen må meldes skriftlig innen 7 dager etter mottak via info@merch-master.com. Merch Master vil behandle klager så raskt som mulig.'],

      ['nl'=>'Artikel 8 — Toepasselijk recht','en'=>'Article 8 — Applicable law','de'=>'Artikel 8 — Anwendbares Recht','no'=>'Artikkel 8 — Gjeldende lov'],
      ['nl'=>'Op alle overeenkomsten met Merch Master is Nederlands recht van toepassing. Geschillen worden voorgelegd aan de bevoegde rechter in Nederland.','en'=>'Dutch law applies to all agreements with Merch Master. Disputes are submitted to the competent court in the Netherlands.','de'=>'Auf alle Vereinbarungen mit Merch Master ist niederländisches Recht anwendbar. Streitigkeiten werden dem zuständigen Gericht in den Niederlanden vorgelegt.','no'=>'Nederlandsk lov gjelder for alle avtaler med Merch Master. Tvister fremlegges for den kompetente domstolen i Nederland.'],
    ];
    for($i = 0; $i < count($sections); $i += 2):
      $titel = $sections[$i][$TAAL] ?? $sections[$i]['nl'];
      $tekst = $sections[$i+1][$TAAL] ?? $sections[$i+1]['nl'];
    ?>
    <div style="margin-bottom:2.5rem;padding-bottom:2.5rem;border-bottom:1px solid rgba(196,98,45,.1);">
      <h3 style="font-family:var(--display);font-size:1.3rem;margin-bottom:.75rem;color:var(--ink);"><?= htmlspecialchars($titel) ?></h3>
      <p style="color:var(--ink2);line-height:1.85;font-size:.95rem;"><?= htmlspecialchars($tekst) ?></p>
    </div>
    <?php endfor; ?>

  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
