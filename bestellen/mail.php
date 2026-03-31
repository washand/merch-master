<?php
/**
 * Merch Master — Mail handler
 * POST /bestellen/mail.php
 *
 * Verstuurt bevestigingsmails via PHP mail() (Hostinger SMTP)
 * Acties:
 *   offerte_bevestiging  — klant + admin na offerte aanvraag
 *   betaling_bevestiging — klant + admin na betaling
 */

header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok'=>false]); exit; }

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$actie  = $input['actie'] ?? '';

define('ADMIN_MAIL',   'info@merch-master.com');
define('ADMIN_NAAM',   'Merch Master');
define('FROM_MAIL',    'noreply@merch-master.com');
define('SITE_URL',     'https://merch-master.com');

function stuurMail(string $naar, string $naar_naam, string $onderwerp, string $body_html): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . ADMIN_NAAM . " <" . FROM_MAIL . ">\r\n";
    $headers .= "Reply-To: " . ADMIN_MAIL . "\r\n";
    $headers .= "X-Mailer: MerchMaster/1.0\r\n";

    $to = $naar_naam ? "$naar_naam <$naar>" : $naar;
    return mail($to, $onderwerp, $body_html, $headers);
}

function mailTemplate(string $titel, string $inhoud, bool $spoed = false): string {
    $spoed_banner = $spoed ? '
      <div style="background:#fff3cd;border:2px solid #f59e0b;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#92400e;line-height:1.6;">
        <strong>⚠ Spoedorder</strong> — Neem contact op via <a href="mailto:info@merch-master.com" style="color:#92400e;">info@merch-master.com</a> voor bevestiging. Online betaling is niet mogelijk bij spoedorders.
      </div>' : '';

    return '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f5f3ef;font-family:\'DM Sans\',Arial,sans-serif;">
    <div style="max-width:580px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
      <!-- Header -->
      <div style="background:#1e3a2f;padding:28px 32px;">
        <div style="font-family:Georgia,serif;font-size:22px;font-weight:900;color:#faf7f2;letter-spacing:-.5px;">
          Merch<span style="color:#c4622d;">Master</span>
        </div>
      </div>
      <!-- Inhoud -->
      <div style="padding:32px;">
        ' . $spoed_banner . '
        <h1 style="font-family:Georgia,serif;font-size:22px;font-weight:900;color:#1e3a2f;margin:0 0 16px;">' . $titel . '</h1>
        ' . $inhoud . '
      </div>
      <!-- Footer -->
      <div style="background:#f5f3ef;padding:20px 32px;border-top:1px solid #e8e4dc;">
        <p style="font-size:12px;color:#7a7670;margin:0;line-height:1.7;">
          Merch Master &nbsp;·&nbsp; <a href="mailto:info@merch-master.com" style="color:#c4622d;">info@merch-master.com</a><br>
          <a href="' . SITE_URL . '" style="color:#7a7670;">' . SITE_URL . '</a>
        </p>
      </div>
    </div>
    </body></html>';
}

function formatRegels(array $regels): string {
    if (empty($regels)) return '<p style="color:#7a7670;font-size:13px;">Geen regels.</p>';
    $html = '<table style="width:100%;border-collapse:collapse;font-size:13px;margin:16px 0;">';
    $html .= '<thead><tr style="background:#f5f3ef;">
        <th style="padding:8px 10px;text-align:left;border-bottom:2px solid #e8e4dc;">Product</th>
        <th style="padding:8px 10px;text-align:left;border-bottom:2px solid #e8e4dc;">Techniek</th>
        <th style="padding:8px 10px;text-align:center;border-bottom:2px solid #e8e4dc;">Stuks</th>
        <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e8e4dc;">Subtotaal</th>
    </tr></thead><tbody>';

    foreach ($regels as $r) {
        $naam     = htmlspecialchars(($r['product_naam'] ?? $r['sku'] ?? '–') . ($r['kleur_naam'] ? ' — '.$r['kleur_naam'] : ''));
        $techniek = htmlspecialchars(ucfirst($r['techniek'] ?? '–'));
        $stuks    = (int)($r['aantal'] ?? 0);
        $subtot   = '€ '.number_format((float)($r['prijs']['totaal_incl'] ?? 0), 2, ',', '.');
        $ontwerp  = $r['upload_naam'] ? '<br><small style="color:#3a6b4a;">✓ '.htmlspecialchars($r['upload_naam']).'</small>' : '<br><small style="color:#c4622d;">Ontwerp nog niet ontvangen</small>';
        $notitie  = $r['notitie'] ? '<br><small style="color:#7a7670;">'.htmlspecialchars($r['notitie']).'</small>' : '';

        $html .= "<tr>
            <td style='padding:8px 10px;border-bottom:1px solid #f0ece4;'>{$naam}{$ontwerp}{$notitie}</td>
            <td style='padding:8px 10px;border-bottom:1px solid #f0ece4;'>{$techniek}</td>
            <td style='padding:8px 10px;border-bottom:1px solid #f0ece4;text-align:center;'>{$stuks}</td>
            <td style='padding:8px 10px;border-bottom:1px solid #f0ece4;text-align:right;font-weight:600;'>{$subtot}</td>
        </tr>";
    }
    $html .= '</tbody></table>';
    return $html;
}

function formatTotalen(array $totalen, bool $spoed, float $spoed_toeslag): string {
    $html = '<table style="width:100%;font-size:13px;margin-top:8px;">';
    $html .= '<tr><td style="padding:4px 0;color:#7a7670;">Subtotaal excl. BTW</td><td style="text-align:right;">€ '.number_format((float)($totalen['totaal_excl']??0),2,',','.').'</td></tr>';
    if (($totalen['vol_pct']??0) > 0) {
        $html .= '<tr><td style="padding:4px 0;color:#166534;">Volumekorting ('.$totalen['vol_pct'].'%)</td><td style="text-align:right;color:#166534;">– € '.number_format((float)($totalen['vol_korting']??0),2,',','.').'</td></tr>';
    }
    $html .= '<tr><td style="padding:4px 0;color:#7a7670;">BTW (21%)</td><td style="text-align:right;color:#7a7670;">€ '.number_format((float)($totalen['btw']??0),2,',','.').'</td></tr>';
    $html .= '<tr style="border-top:2px solid #1a1a1a;"><td style="padding:8px 0 4px;font-weight:700;font-size:15px;">Totaal incl. BTW</td><td style="text-align:right;font-weight:700;font-size:15px;color:#c4622d;">€ '.number_format((float)($totalen['totaal_incl']??0),2,',','.').'</td></tr>';
    if ($spoed && $spoed_toeslag > 0) {
        $html .= '<tr><td style="padding:4px 0;color:#92400e;">Spoedtoeslag (40%)</td><td style="text-align:right;color:#92400e;">+ € '.number_format($spoed_toeslag,2,',','.').'</td></tr>';
        $html .= '<tr><td style="padding:4px 0;font-weight:700;">Te betalen (spoed)</td><td style="text-align:right;font-weight:700;color:#c4622d;">€ '.number_format((float)($totalen['totaal_incl']??0)+$spoed_toeslag,2,',','.').'</td></tr>';
    }
    $html .= '</table>';
    return $html;
}

// ── Router ────────────────────────────────────────────────────────────────────
switch ($actie) {

    case 'offerte_bevestiging': {
        $klant         = $input['klant']          ?? [];
        $regels        = $input['regels']         ?? [];
        $totalen       = $input['totalen']        ?? [];
        $offerte_nr    = $input['offerte_nr']     ?? '–';
        $pdf_url       = $input['pdf_url']        ?? '';
        $spoed         = !empty($input['spoed']);
        $spoed_toeslag = (float)($input['spoed_toeslag'] ?? 0);
        $geldig_tot    = $input['geldig_tot']     ?? '';

        $klant_naam  = htmlspecialchars($klant['naam']    ?? '–');
        $klant_email = $klant['email'] ?? '';

        if (!$klant_email || !str_contains($klant_email, '@')) {
            echo json_encode(['ok'=>false,'fout'=>'Geen geldig e-mailadres']); exit;
        }

        $pdf_link = $pdf_url ? '<p style="margin:20px 0 0;"><a href="'.SITE_URL.$pdf_url.'" style="display:inline-block;background:#c4622d;color:#fff;padding:12px 24px;border-radius:50px;text-decoration:none;font-weight:700;font-size:14px;">Offerte bekijken (PDF)</a></p>' : '';
        $geldig_str = $geldig_tot ? date('d-m-Y', strtotime($geldig_tot)) : '30 dagen';

        // ── Mail naar klant ──────────────────────────────────────────────────
        $klant_inhoud = '
          <p style="font-size:14px;color:#3a3832;line-height:1.7;margin-bottom:16px;">
            Beste ' . $klant_naam . ',<br><br>
            Bedankt voor uw offerte-aanvraag bij Merch Master. Hieronder vindt u een overzicht van uw aanvraag.
            ' . ($spoed ? 'Dit is een <strong>spoedorder</strong> — wij nemen zo spoedig mogelijk contact met u op.' : 'Wij nemen contact met u op zodra de offerte is beoordeeld.') . '
          </p>
          <div style="background:#f5f3ef;border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:16px;">
            <strong>Offertenummer:</strong> ' . htmlspecialchars($offerte_nr) . '<br>
            <strong>Geldig tot:</strong> ' . $geldig_str . '
          </div>
          ' . formatRegels($regels) . '
          ' . formatTotalen($totalen, $spoed, $spoed_toeslag) . '
          ' . $pdf_link . '
          <p style="font-size:13px;color:#7a7670;margin-top:24px;line-height:1.7;">
            Heeft u vragen? Stuur een e-mail naar <a href="mailto:' . ADMIN_MAIL . '" style="color:#c4622d;">' . ADMIN_MAIL . '</a>.
          </p>';

        $ok1 = stuurMail(
            $klant_email, $klant['naam'] ?? '',
            'Offerte aangevraagd — ' . $offerte_nr . ' | Merch Master',
            mailTemplate('Uw offerte is ontvangen', $klant_inhoud, $spoed)
        );

        // ── Mail naar admin ──────────────────────────────────────────────────
        $admin_inhoud = '
          <p style="font-size:14px;color:#3a3832;line-height:1.7;margin-bottom:16px;">
            Nieuwe offerte-aanvraag ontvangen van <strong>' . $klant_naam . '</strong> (' . htmlspecialchars($klant_email) . ').
            ' . ($spoed ? '<strong style="color:#92400e;">SPOEDORDER</strong>' : '') . '
          </p>
          <div style="background:#f5f3ef;border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:16px;">
            <strong>Nr:</strong> ' . htmlspecialchars($offerte_nr) . '<br>
            <strong>Klant:</strong> ' . $klant_naam . '<br>
            <strong>E-mail:</strong> <a href="mailto:' . htmlspecialchars($klant_email) . '" style="color:#c4622d;">' . htmlspecialchars($klant_email) . '</a><br>
            ' . (!empty($klant['tel']) ? '<strong>Tel:</strong> '.htmlspecialchars($klant['tel']).'<br>' : '') . '
            ' . (!empty($klant['bedrijf']) ? '<strong>Bedrijf:</strong> '.htmlspecialchars($klant['bedrijf']).'<br>' : '') . '
          </div>
          ' . formatRegels($regels) . '
          ' . formatTotalen($totalen, $spoed, $spoed_toeslag) . '
          ' . ($pdf_url ? '<p style="margin-top:16px;"><a href="'.SITE_URL.$pdf_url.'" style="color:#c4622d;">Offerte PDF bekijken →</a></p>' : '') . '
          <p style="margin-top:16px;"><a href="' . SITE_URL . '/bestellen/admin/" style="display:inline-block;background:#1e3a2f;color:#fff;padding:10px 20px;border-radius:50px;text-decoration:none;font-weight:700;font-size:13px;">Naar admin dashboard →</a></p>';

        $ok2 = stuurMail(
            ADMIN_MAIL, ADMIN_NAAM,
            ($spoed ? '🚨 SPOED — ' : '') . 'Nieuwe offerte: ' . $offerte_nr . ' van ' . ($klant['naam'] ?? $klant_email),
            mailTemplate(($spoed ? '🚨 Spoedofferte: ' : 'Nieuwe offerte: ') . $offerte_nr, $admin_inhoud, $spoed)
        );

        echo json_encode(['ok' => true, 'klant_mail' => $ok1, 'admin_mail' => $ok2]);
        break;
    }

    case 'betaling_bevestiging': {
        $klant        = $input['klant']         ?? [];
        $regels       = $input['regels']        ?? [];
        $totalen      = $input['totalen']       ?? [];
        $offerte_nr   = $input['offerte_nr']    ?? '–';
        $betaling_id  = $input['betaling_id']   ?? '';
        $klant_email  = $klant['email'] ?? '';

        if (!$klant_email || !str_contains($klant_email, '@')) {
            echo json_encode(['ok'=>false,'fout'=>'Geen geldig e-mailadres']); exit;
        }

        $klant_naam = htmlspecialchars($klant['naam'] ?? '–');

        // ── Mail naar klant ──────────────────────────────────────────────────
        $klant_inhoud = '
          <p style="font-size:14px;color:#3a3832;line-height:1.7;margin-bottom:16px;">
            Beste ' . $klant_naam . ',<br><br>
            Uw betaling is ontvangen en bevestigd. De productie van uw bestelling start zodra uw ontwerp is ontvangen.
          </p>
          <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:16px;">
            <strong>✓ Betaling ontvangen</strong><br>
            Ordernummer: ' . htmlspecialchars($offerte_nr) . '<br>
            ' . ($betaling_id ? 'Transactie-ID: '.htmlspecialchars($betaling_id) : '') . '
          </div>
          ' . formatRegels($regels) . '
          ' . formatTotalen($totalen, false, 0) . '
          <p style="font-size:13px;color:#7a7670;margin-top:24px;line-height:1.7;">
            Nog geen ontwerp verstuurd? Stuur het naar <a href="mailto:' . ADMIN_MAIL . '" style="color:#c4622d;">' . ADMIN_MAIL . '</a> met vermelding van uw ordernummer.
          </p>';

        $ok1 = stuurMail(
            $klant_email, $klant['naam'] ?? '',
            'Betaling bevestigd — ' . $offerte_nr . ' | Merch Master',
            mailTemplate('Betaling ontvangen — bedankt!', $klant_inhoud)
        );

        // ── Mail naar admin ──────────────────────────────────────────────────
        $admin_inhoud = '
          <p style="font-size:14px;color:#3a3832;line-height:1.7;margin-bottom:16px;">
            Betaling ontvangen van <strong>' . $klant_naam . '</strong> (' . htmlspecialchars($klant_email) . ').
          </p>
          <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:16px;">
            <strong>Order:</strong> ' . htmlspecialchars($offerte_nr) . '<br>
            ' . ($betaling_id ? '<strong>PayPal ID:</strong> '.htmlspecialchars($betaling_id).'<br>' : '') . '
            <strong>Bedrag:</strong> € '.number_format((float)($totalen['totaal_incl']??0),2,',','.').'
          </div>
          ' . formatRegels($regels) . '
          <p style="margin-top:16px;"><a href="' . SITE_URL . '/bestellen/admin/" style="display:inline-block;background:#1e3a2f;color:#fff;padding:10px 20px;border-radius:50px;text-decoration:none;font-weight:700;font-size:13px;">Naar admin →</a></p>';

        $ok2 = stuurMail(
            ADMIN_MAIL, ADMIN_NAAM,
            '💰 Betaling ontvangen: ' . $offerte_nr,
            mailTemplate('Betaling ontvangen: ' . $offerte_nr, $admin_inhoud)
        );

        echo json_encode(['ok'=>true,'klant_mail'=>$ok1,'admin_mail'=>$ok2]);
        break;
    }

    default:
        echo json_encode(['ok'=>false,'fout'=>'Onbekende actie']);
}
