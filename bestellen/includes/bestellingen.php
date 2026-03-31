<?php
require_once __DIR__ . '/db.php';

class Bestellingen {

    // ── Bestelling opslaan na betaling ────────────────────────────────────────
    public static function opslaan($d, $klantId = null) {
        $orderId = $d['order_id'] ?? ('ORD-' . date('YmdHis') . '-' . rand(100,999));

        $bestelId = DB::insert(
            'INSERT INTO bestellingen (order_id,klant_id,status,totaal_incl,totaal_ex,btw,verzending_ex,paypal_id,opmerkingen)
             VALUES (?,?,?,?,?,?,?,?,?)',
            [
                $orderId,
                $klantId,
                'betaald',
                round((float)($d['totaal_incl'] ?? 0), 2),
                round((float)($d['totaal_ex'] ?? 0), 2),
                round((float)($d['btw'] ?? 0), 2),
                round((float)($d['verzending_ex'] ?? 0), 2),
                $d['paypal_id'] ?? null,
                $d['opmerkingen'] ?? null,
            ]
        );

        // Sla bestelregels op
        foreach (($d['regels'] ?? []) as $regel) {
            self::slaRegelOp($bestelId, $regel);
        }

        return $bestelId;
    }

    private static function slaRegelOp($bestelId, $r) {
        $regelId = DB::insert(
            'INSERT INTO bestelregels (bestelling_id,textiel_sku,textiel_naam,textiel_merk,kleur_code,kleur_naam,
             positie,techniek_a,techniek_b,is_dual,maten,aantal,prijs_ex,druk_ex,regel_ex)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $bestelId,
                $r['sku'] ?? null, $r['naam'] ?? null, $r['merk'] ?? null,
                $r['kleur_code'] ?? null, $r['kleur_naam'] ?? null,
                $r['positie'] ?? null, $r['techniek_a'] ?? null, $r['techniek_b'] ?? null,
                $r['is_dual'] ? 1 : 0,
                json_encode($r['maten'] ?? []),
                (int)($r['aantal'] ?? 0),
                round((float)($r['prijs_ex'] ?? 0), 4),
                round((float)($r['druk_ex'] ?? 0), 4),
                round((float)($r['regel_ex'] ?? 0), 2),
            ]
        );

        // Uploads koppelen
        foreach (($r['uploads'] ?? []) as $pos => $urls) {
            foreach ((array)$urls as $i => $url) {
                if (!$url) continue;
                DB::run(
                    'INSERT INTO uploads (regel_id,positie,bestandsnaam,url,volgorde) VALUES (?,?,?,?,?)',
                    [$regelId, $pos, basename($url), $url, $i]
                );
            }
        }
    }

    // ── Bestellingen van klant ophalen ────────────────────────────────────────
    public static function vanKlant($klantId) {
        $bestellingen = DB::fetchAll(
            'SELECT * FROM bestellingen WHERE klant_id = ? ORDER BY aangemaakt DESC',
            [$klantId]
        );
        foreach ($bestellingen as &$b) {
            $b['regels'] = self::regelsVanBestelling($b['id']);
        }
        return $bestellingen;
    }

    // ── Bestelregels ophalen ──────────────────────────────────────────────────
    public static function regelsVanBestelling($bestelId) {
        $regels = DB::fetchAll(
            'SELECT * FROM bestelregels WHERE bestelling_id = ?',
            [$bestelId]
        );
        foreach ($regels as &$r) {
            $r['maten'] = json_decode($r['maten'], true) ?? [];
            $r['uploads'] = DB::fetchAll('SELECT * FROM uploads WHERE regel_id = ? ORDER BY positie,volgorde', [$r['id']]);
        }
        return $regels;
    }

    // ── Statuslabels ──────────────────────────────────────────────────────────
    public static function statusLabel($status) {
        return [
            'concept'        => 'Concept',
            'betaald'        => 'Betaald ',
            'in_behandeling' => 'In behandeling',
            'geleverd'       => 'Geleverd ',
            'geannuleerd'    => 'Geannuleerd',
        ][$status] ?? $status;
    }
}
