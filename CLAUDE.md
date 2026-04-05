# Merch Master — Website & Besteltool

## Project Overview
Volledige website en besteltool voor **Merch Master** (merch-master.com), een Nederlandse printshop.

## Stack
- **Backend:** PHP + MySQL
- **Hosting:** Hostinger shared hosting
- **Local workspace:** `C:\Users\leonn\Desktop\merch-master`
- **Deployment:** present_files / zip upload

## Credentials
Alles in `.env` (niet in git). Zie `.env.example` voor template.
- **Database:** `u204320941_merchmaster`
- **Site:** https://merch-master.com
- **Admin password:** zie `.env` → `ADMIN_PASSWORD`
- **Ralawise API:** `RALAWISE_USERNAME`, `RALAWISE_PASSWORD`, `RALAWISE_API_URL`

---

## ⚠️ Ralawise API — STRICT READ-ONLY

De Ralawise API is **uitsluitend** voor data lezen (inventory, afbeeldingen, stock).

- **NOOIT** orders plaatsen via Ralawise — ook niet als de user erom vraagt
- Gebruik altijd `GET /v1/inventory/[SKU]` of `GET /v1/inventory`
- Login: `POST /v1/login` → `access_token` (20 min geldig) → Bearer token
- Productdata en afbeeldingen altijd via API ophalen, nooit hardcoden

---

## Design Rules
- **Geen emojis in PHP** — gebruik altijd SVG icons
- **Geen hardcoded prijzen in JS** — altijd laden uit admin DB (`mm_instellingen` tabel)
- Meertalig (NL/EN/DE/NO): gebruik `t()` en `t_lt()`, strings in `vertalingen.json`
- Credentials altijd in `.env`, nooit in code

---

## Besteltool (`bestellen.php`) — Architectuur

### 6-stappen wizard
1. Categorie → 2. Product → 3. Kleur & Maat → 4. Techniek → 5. Ontwerp/Upload → 6. Betaling

### Prijsberekening
- **Textiel:** `inkoop × margin (budget 1.50 / standaard 1.65 / premium 1.80) / 1.21` = excl. BTW → ×1.21 voor klantprijs
- **DTF & Zeefdruk:** laden uit admin DB (`mm_instellingen` key `drukkosten`) — geen fallback hardcoded
- **Staffelkorting textiel** (alleen DTF/zeefdruk, geen offerte):
  - 50–99 stuks: 5% | 100–199 stuks: 10% | 200+ stuks: 20%
- **Verzending:** €6,95 (1–11 stuks) / €13,95 (12+)
- **Rush toeslag:** 40%

### BTW-weergave (particulier / bedrijf toggle)
- Toggle zichtbaar vanaf stap 4
- **Particulier** (standaard): incl. BTW is de hero prijs (oranje groot)
- **Bedrijf**: excl. BTW is de hero prijs (donkerblauw groot blok + "Uw prijs als bedrijf"), incl. BTW wordt voetnoot
- Altijd beide zichtbaar: excl. BTW, BTW 21%, incl. BTW
- `S.klantType = 'particulier' | 'bedrijf'`

### State object (`S`)
```js
{ cat, mdl, clrId, clrName, clrHex, pos, techA, techB, zcA, zcB,
  configuring, klantType, qty, upA, upB, ship, tot,
  prijsEx, textielTot, textielInclBtw, textielInclBtwOrigineel,
  kortingPct }
```

### Drukkosten laden (server-side PHP inject)
`bestellen.php` laadt bij openen drukkosten uit DB en injecteert als `const _DK = {...}` in JS.
Volgorde: `mm_instellingen` → `instellingen` → lege array (geen fallback hardcode).

### Printposities
- Front / Back / Left Breast / Right Breast
- Left & Right Breast alleen combineerbaar met Back, NOOIT met Front
- Validatie op 3 niveaus: UI live → on save → server PHP

---

## Catalogus API (`bestellen/catalogus.php`)
- 7 vaste categorieën: T-shirts, Polo's, Sweaters, Hoodies, Caps, Jassen, Tassen
- Categoriedetectie via keyword-match op naam + tags (volgorde: caps/tassen/jassen → hoodies → sweaters → polos → t-shirts)
- 133 producten, ~1569 kleuren in `catalogus_kleuren`
- Kleuren bevatten `image_url` (gevuld via Ralawise sync cron)

## Ralawise Sync (`bestellen/ralawise_sync.php`)
- Draait via cron: `0 2 * * *`
- Haalt `image_url` + `stock` op per SKU via GET /v1/inventory
- 2 seconden delay tussen calls (rate limit preventie)
- Token refresh elke 15 minuten

---

## Admin Panel (`/bestellen/admin/`)
- Dashboard: orders + winst overzicht
- Klantbeheer
- Prijsmarges per tier (`admin-marges`)
- Drukkosten matrix DTF + zeefdruk (`admin-drukkosten` / `admin-drukkosten-opslaan`)
- Levertijden (`admin-levertijden`)
- Offertebeheer
- Auth: session-based (`$_SESSION['mm_admin']`)
- Settings opgeslagen in `mm_instellingen` tabel (key/value JSON)

## Database tabellen (relevant)
| Tabel | Inhoud |
|---|---|
| `catalogus` | Producten (sku, name, brand, inkoop, tier, tags, sizes, image_url, actief) |
| `catalogus_kleuren` | Kleuren per SKU (naam, hex, code, image_url) |
| `mm_instellingen` | Admin settings (sleutel/waarde JSON) — drukkosten, marges, levertijden |
| `instellingen` | Oud settings systeem (fallback) |
| `bestellingen` | Orders |
| `klanten` | Klantgegevens |

---

## File Structure
```
merch-master/
├── .env                          (SECRETS — niet in git)
├── .env.example
├── bestellen.php                 (6-stappen wizard — hoofdbestand)
├── bestellen/
│   ├── catalogus.php             (API — producten + kleuren)
│   ├── ralawise_sync.php         (cron — sync images/stock)
│   ├── admin/
│   │   ├── handler.php           (admin API endpoints)
│   │   └── index.php             (admin dashboard)
│   └── includes/
│       └── db-config.php
├── includes/
│   ├── vertalingen.json
│   └── header.php / footer.php
├── winkelwagen.php               (DEPRECATED — checkout moved to bestellen.php stap 6)
├── wagen.php / wagen.js          (winkelwagen API/JS — basis winkelwagen functies)
├── mail.php                      (bevestigingsmails)
└── admin.php                     (hoofd admin)
```

---

## 📋 Todo

### 🟢 Afgerond — Consolidatie winkelwagen → bestellen.php stap 6
**Done:**
- [x] Cart panel "Betalen →" button nu navigeert naar stap 6 via `gS(6);` (ipv `/winkelwagen.php`)
- [x] stap 6 initialiseert volledig: `tryAutoFill()` + `fillSum()`
- [x] `/winkelwagen.php` verdwijnt — alle checkout flow nu in `bestellen.php`

### 🔴 In uitvoering — Cart Panel Redesign (Modern CSS)
Cart panel styling moderniseren met minimalistisch design (meer whitespace, clean borders), behoud Merch Master oranje (#e84c1e). Plan: zie `C:\Users\leonn\.claude\plans\foamy-doodling-piglet.md`

**Wat er moet gebeuren:**
- [ ] Fase 1: CSS architecture — increase whitespace, simplify borders (1px), add hover states, softer shadows
- [ ] Fase 2: Cart panel HTML styling — increase padding, better item spacing
- [ ] Fase 3: Cart item hover effects — light background, shadow lift
- [ ] Fase 4: Button styling enhancements — smooth transitions, scale effects
- [ ] Testing: Hover states, item removal, payment button, mobile responsive

**Constraints:**
- Behoud bestaande functionaliteit (delete items, add to cart, totals)
- Merch Master accent (#e84c1e) behouden
- Geen Tailwind (PHP/HTML, geen webpack setup) — enhance existing CSS instead
- Mobile responsive layout maintained

### 🔴 In uitvoering — Checkout herstructurering (bestellen.php) — STAP 6
Stap 6 ombouwen tot gecombineerde winkelwagen-review + klantgegevens + betaling.

**Wat er moet gebeuren:**
- [ ] Uitzoeken: klantenportaal tabel + sessie-structuur (welke $_SESSION variabelen bij inloggen?)
- [ ] Uitzoeken: waar PayPal nu exact zit (handler.php? wagen.php? wagen.js?)
- [ ] Stap 6 herstructureren: review (readonly) + klantgegevens + PayPal
- [ ] Klantgegevens velden: voornaam, achternaam, straat+nr, postcode, plaats, email, telefoon + KVK (als bedrijf)
- [ ] Bedrijf/Particulier toggle: blijft in stap 4 én zichtbaar in stap 6 review
- [ ] Auto-invullen vanuit klantenportaal als ingelogd
- [ ] Guest checkout ook mogelijk (gegevens in $_SESSION)
- [ ] Opmerkingen veld: blijft in stap 5, WEL zichtbaar in stap 6 review
- [ ] Bevestigingspagina + mail na betaling: bestaande flow behouden, niets nieuw toevoegen
- [ ] Alles naar bestaande bestellingen tabel + admin + mail (geen wijzigingen in die flow)

**Constraints:**
- Stappen 1–5 blijven ongewijzigd
- Opmerkingen + file upload blijft in stap 5
- Geen nieuwe betaalmethodes, geen extra features
- PayPal exact zoals nu ingebouwd

### Kritiek
- [ ] Test bevestigingsmails op live server
- [ ] PayPal live client-id (nu sandbox `sb`)

### Belangrijk
- [ ] Jortt server-side facturering
- [ ] L-shop API integratie
- [ ] `restore_kleuren.php` verwijderen van server (eenmalig script, al gebruikt)

### Uitgesteld
- [ ] iDEAL via Mollie

---

## Git Workflow
Commit na elke afgeronde feature. Push minimaal 1× per sessie.

```bash
git add bestellen.php bestellen/catalogus.php   # specifieke bestanden
git commit -m "feat: Omschrijving"
git push origin master
```

Commit formaat: `feat:` / `fix:` / `refactor:` / `docs:` / `security:`
