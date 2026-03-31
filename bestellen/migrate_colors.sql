-- ─────────────────────────────────────────────────────────────────────────────
-- Merch Master Color Data Migration
-- Voegt standaard kleuren toe voor alle producten in catalogus_kleuren
-- ─────────────────────────────────────────────────────────────────────────────

-- Controleer of tabel leeg is
SELECT COUNT(*) as bestaande_kleuren FROM catalogus_kleuren;

-- Voeg standaard kleuren toe voor elk product
INSERT INTO catalogus_kleuren (product_sku, code, naam, hex)
SELECT DISTINCT
    c.sku,
    kleur.code,
    kleur.naam,
    kleur.hex
FROM catalogus c
CROSS JOIN (
    SELECT 'BK' as code, 'Zwart' as naam, '#000000' as hex
    UNION ALL SELECT 'WH', 'Wit', '#FFFFFF'
    UNION ALL SELECT 'NV', 'Navy', '#001F3F'
    UNION ALL SELECT 'RD', 'Rood', '#FF4136'
    UNION ALL SELECT 'GR', 'Grijs', '#AAAAAA'
    UNION ALL SELECT 'BL', 'Blauw', '#0074D9'
    UNION ALL SELECT 'GN', 'Groen', '#2ECC40'
) kleur
WHERE c.actief = 1
AND NOT EXISTS (
    -- Voorkom duplicates
    SELECT 1 FROM catalogus_kleuren ck
    WHERE ck.product_sku = c.sku
    AND ck.code = kleur.code
);

-- Controleer resultaat
SELECT COUNT(*) as totaal_kleuren FROM catalogus_kleuren;
SELECT
    COUNT(DISTINCT product_sku) as producten_met_kleuren,
    COUNT(*) as totaal_kleur_rijen,
    COUNT(*) / COUNT(DISTINCT product_sku) as kleuren_per_product
FROM catalogus_kleuren;

-- Sample check
SELECT * FROM catalogus_kleuren LIMIT 10;
