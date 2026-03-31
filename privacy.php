<?php
$PAGE_TITLE = 'Privacybeleid';
$PAGE_DESC  = 'Lees hoe Merch Master omgaat met uw persoonsgegevens.';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding:4rem 2.5rem 5rem;">
  <div class="container" style="max-width:800px;">
    <div class="sec-kop" style="text-align:left;margin-bottom:2.5rem;">
      <div class="sec-oogje"><?= t('privacy_title') ?></div>
      <h2 style="font-size:clamp(2rem,4vw,2.8rem);"><?= t('privacy_title') ?></h2>
      <p style="color:var(--ink2);font-size:.9rem;margin-top:.5rem;"><?php
        $last = ['nl'=>'Laatst bijgewerkt: maart 2026','en'=>'Last updated: March 2026','de'=>'Zuletzt aktualisiert: März 2026','no'=>'Sist oppdatert: mars 2026'];
        echo $last[$TAAL] ?? $last['nl'];
      ?></p>
    </div>

    <?php
    $sections = [
      ['nl'=>'1. Wie zijn wij','en'=>'1. Who we are','de'=>'1. Wer wir sind','no'=>'1. Hvem vi er'],
      ['nl'=>'Merch Master is een print- en borduurservice gevestigd in Nederland. Wij zijn bereikbaar via info@merch-master.com of +31 6 17 25 51 70. Merch Master is verantwoordelijk voor de verwerking van persoonsgegevens zoals beschreven in dit privacybeleid.','en'=>'Merch Master is a print and embroidery service based in the Netherlands. You can reach us at info@merch-master.com or +31 6 17 25 51 70. Merch Master is responsible for processing personal data as described in this privacy policy.','de'=>'Merch Master ist ein Druck- und Stickservice mit Sitz in den Niederlanden. Sie können uns unter info@merch-master.com oder +31 6 17 25 51 70 erreichen.','no'=>'Merch Master er en trykk- og broderingstjeneste basert i Nederland. Du kan nå oss på info@merch-master.com eller +31 6 17 25 51 70.'],

      ['nl'=>'2. Welke gegevens verzamelen wij','en'=>'2. What data we collect','de'=>'2. Welche Daten wir erheben','no'=>'2. Hvilke data vi samler inn'],
      ['nl'=>'Bij het plaatsen van een bestelling of het invullen van het contactformulier verzamelen wij: naam en e-mailadres, bezorgadres, telefoonnummer (optioneel), betaalgegevens (via PayPal — wij slaan geen betaalgegevens op), uploadbestanden (logo\'s en ontwerpen).','en'=>'When placing an order or filling in the contact form we collect: name and email address, delivery address, phone number (optional), payment details (via PayPal — we do not store payment data), uploaded files (logos and designs).','de'=>'Bei der Bestellung oder beim Ausfüllen des Kontaktformulars erheben wir: Name und E-Mail-Adresse, Lieferadresse, Telefonnummer (optional), Zahlungsdaten (über PayPal — wir speichern keine Zahlungsdaten), hochgeladene Dateien (Logos und Designs).','no'=>'Ved bestilling eller utfylling av kontaktskjemaet samler vi inn: navn og e-postadresse, leveringsadresse, telefonnummer (valgfritt), betalingsdata (via PayPal — vi lagrer ingen betalingsdata), opplastede filer (logoer og design).'],

      ['nl'=>'3. Waarvoor gebruiken wij uw gegevens','en'=>'3. How we use your data','de'=>'3. Wofür wir Ihre Daten verwenden','no'=>'3. Hvordan vi bruker dataene dine'],
      ['nl'=>'Wij gebruiken uw gegevens uitsluitend voor: het verwerken en leveren van uw bestelling, communicatie over uw bestelling, het sturen van een factuur via Jortt, en het beantwoorden van uw vragen. Wij verkopen uw gegevens nooit aan derden.','en'=>'We use your data exclusively for: processing and delivering your order, communication about your order, sending an invoice via Jortt, and answering your questions. We never sell your data to third parties.','de'=>'Wir verwenden Ihre Daten ausschließlich für: die Verarbeitung und Lieferung Ihrer Bestellung, die Kommunikation über Ihre Bestellung, das Senden einer Rechnung über Jortt und die Beantwortung Ihrer Fragen. Wir verkaufen Ihre Daten niemals an Dritte.','no'=>'Vi bruker dataene dine utelukkende for: behandling og levering av bestillingen din, kommunikasjon om bestillingen din, sending av faktura via Jortt og besvaring av spørsmålene dine. Vi selger aldri dataene dine til tredjeparter.'],

      ['nl'=>'4. Bewaartermijn','en'=>'4. Retention period','de'=>'4. Aufbewahrungsfrist','no'=>'4. Oppbevaringsperiode'],
      ['nl'=>'Wij bewaren uw persoonsgegevens niet langer dan noodzakelijk. Bestelgegevens worden conform de wettelijke bewaarplicht 7 jaar bewaard. Contactformuliergegevens worden na 1 jaar verwijderd.','en'=>'We do not retain your personal data longer than necessary. Order data is retained for 7 years in accordance with legal requirements. Contact form data is deleted after 1 year.','de'=>'Wir speichern Ihre personenbezogenen Daten nicht länger als notwendig. Bestelldaten werden gemäß der gesetzlichen Aufbewahrungspflicht 7 Jahre aufbewahrt. Kontaktformulardaten werden nach 1 Jahr gelöscht.','no'=>'Vi beholder ikke personopplysningene dine lenger enn nødvendig. Bestillingsdata oppbevares i 7 år i henhold til lovkrav. Kontaktskjemadata slettes etter 1 år.'],

      ['nl'=>'5. Uw rechten','en'=>'5. Your rights','de'=>'5. Ihre Rechte','no'=>'5. Dine rettigheter'],
      ['nl'=>'U heeft het recht om uw persoonsgegevens in te zien, te corrigeren of te laten verwijderen. Ook kunt u bezwaar maken tegen de verwerking. Stuur hiervoor een e-mail naar info@merch-master.com. Wij reageren binnen 30 dagen.','en'=>'You have the right to access, correct or delete your personal data. You can also object to the processing. Send an email to info@merch-master.com for this. We respond within 30 days.','de'=>'Sie haben das Recht, Ihre personenbezogenen Daten einzusehen, zu korrigieren oder löschen zu lassen. Sie können auch der Verarbeitung widersprechen. Senden Sie dazu eine E-Mail an info@merch-master.com. Wir antworten innerhalb von 30 Tagen.','no'=>'Du har rett til å få tilgang til, korrigere eller slette personopplysningene dine. Du kan også protestere mot behandlingen. Send en e-post til info@merch-master.com. Vi svarer innen 30 dager.'],

      ['nl'=>'6. Beveiliging','en'=>'6. Security','de'=>'6. Sicherheit','no'=>'6. Sikkerhet'],
      ['nl'=>'Wij nemen passende technische en organisatorische maatregelen om uw persoonsgegevens te beschermen tegen verlies of ongeoorloofde toegang. Onze website maakt gebruik van een beveiligde HTTPS-verbinding.','en'=>'We take appropriate technical and organisational measures to protect your personal data against loss or unauthorised access. Our website uses a secure HTTPS connection.','de'=>'Wir treffen geeignete technische und organisatorische Maßnahmen, um Ihre personenbezogenen Daten vor Verlust oder unbefugtem Zugriff zu schützen. Unsere Website verwendet eine sichere HTTPS-Verbindung.','no'=>'Vi tar passende tekniske og organisatoriske tiltak for å beskytte personopplysningene dine mot tap eller uautorisert tilgang. Nettstedet vårt bruker en sikker HTTPS-tilkobling.'],

      ['nl'=>'7. Contact','en'=>'7. Contact','de'=>'7. Kontakt','no'=>'7. Kontakt'],
      ['nl'=>'Voor vragen over dit privacybeleid kunt u contact opnemen via info@merch-master.com of via WhatsApp: +31 6 17 25 51 70.','en'=>'For questions about this privacy policy you can contact us at info@merch-master.com or via WhatsApp: +31 6 17 25 51 70.','de'=>'Für Fragen zu dieser Datenschutzrichtlinie können Sie uns unter info@merch-master.com oder per WhatsApp: +31 6 17 25 51 70 kontaktieren.','no'=>'For spørsmål om denne personvernpolicyen kan du kontakte oss på info@merch-master.com eller via WhatsApp: +31 6 17 25 51 70.'],
    ];
    for($i = 0; $i < count($sections); $i += 2):
      $titel = $sections[$i][$TAAL] ?? $sections[$i]['nl'];
      $tekst = $sections[$i+1][$TAAL] ?? $sections[$i+1]['nl'];
    ?>
    <div style="margin-bottom:2.5rem;">
      <h3 style="font-family:var(--display);font-size:1.3rem;margin-bottom:.75rem;color:var(--ink);"><?= htmlspecialchars($titel) ?></h3>
      <p style="color:var(--ink2);line-height:1.85;font-size:.95rem;"><?= htmlspecialchars($tekst) ?></p>
    </div>
    <?php endfor; ?>

  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
