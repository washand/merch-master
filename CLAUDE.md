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

### 5-stappen wizard
1. Categorie → 2. Product → 3. Kleur & Maat → 4. Techniek → 5. Ontwerp/Upload
**Opmerking:** Stap 6 (Betaling) verplaatst naar standalone `bestellen/checkout.php`

### Prijsberekening
- **Textiel:** `inkoop × margin (budget 1.50 / standaard 1.65 / premium 1.80) / 1.21` = excl. BTW → ×1.21 voor klantprijs
- **DTF & Zeefdruk:** laden uit admin DB (`mm_instellingen` key `drukkosten`) — geen fallback hardcoded
- **Staffelkorting textiel** (alleen DTF/zeefdruk, geen offerte):
  - 50–99 stuks: 5% | 100–199 stuks: 10% | 200+ stuks: 20%
- **Verzending:** €6,95 (1–5 stuks) / €13,95 (6–14 stuks) / **ALTIJD BETALEN**, geen gratis verzending voor grote orders
- **Rush toeslag:** 40%

### ⚠️ VERZENDING — KRITIEK
- **MERCH MASTER WILT NOOIT GRATIS VERZENDING**
- Verzending is ALTIJD betaald (€6,95 of €13,95)
- `verzend_achteraf` mag NIET gebruikt worden voor gratis verzending
- Bij 15+ stuks: verzending blijft €13,95 (of opnieuw bepalen met klant)
- wagen.php regel 536 zet `verzend_incl=0` voor 15+ stuks → DIT IS FOUT

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
**pos values:** `'front'` | `'back'` | `'both'` | `'left'` | `'right'` | `'left-back'` | `'right-back'`

### Drukkosten laden (server-side PHP inject)
`bestellen.php` laadt bij openen drukkosten uit DB en injecteert als `const _DK = {...}` in JS.
Volgorde: `mm_instellingen` → `instellingen` → lege array (geen fallback hardcode).

### Printposities (7 opties)
**Solo posities:**
- `'front'` — **Voorkant** — zowel DTF als Zeefdruk
- `'back'` — **Achterkant** — zowel DTF als Zeefdruk
- `'left'` — **Linkerborst** — DTF: €4,50 | Zeefdruk: gratis
- `'right'` — **Rechterborst** — DTF: €4,50 | Zeefdruk: gratis

**Combinaties (twee-techniek):**
- `'both'` — **Beide kanten** (Voorkant + Achterkant)
- `'left-back'` — **Linkerborst + Achterkant** — kan verschillende technieken gebruiken per positie
- `'right-back'` — **Rechterborst + Achterkant** — kan verschillende technieken gebruiken per positie

**Constraints & Pricing:**
- Linkerborst & Rechterborst kunnen NOOIT met Voorkant gecombineerd worden
- Linkerborst & Rechterborst kunnen NIET met elkaar gecombineerd worden
- DTF extra kosten: €4,50 per borst-positie (admin-settable, `drukkosten.dtf_borst`)
- Zeefdruk op borst: **gratis** (gebruikt Voorkant zeefdruk pricing matrix, geen extra cost)
- Elk positie in combinatie kan eigen techniek hebben (bijv. linkerborst=DTF + achterkant=zeefdruk)
- Zeefdruk color dropdowns tonen ALLEEN voor borst-posities (id="zc-left-sel", id="zc-right-sel")

**Validatie op 3 niveaus:**
1. UI live — `selPos()` toggles position classes, `calcQ()` valideert combinaties
2. On save — `toevoegenAanWagen()` bouwt posities array met validatie
3. Server PHP — `wagen.php` validates rules op backend

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
- **Kortingscodes** (`kortingscodes`) — aanmaken, activeren/deactiveren, verwijderen
- Auth: session-based (`$_SESSION['mm_admin']`)
- Settings opgeslagen in `mm_instellingen` tabel (key/value JSON)

## Database tabellen (relevant)
| Tabel | Inhoud |
|---|---|
| `catalogus` | Producten (sku, name, brand, inkoop, tier, tags, sizes, image_url, actief) |
| `catalogus_kleuren` | Kleuren per SKU (naam, hex, code, image_url) |
| `mm_instellingen` | Admin settings (sleutel/waarde JSON) — drukkosten, marges, levertijden |
| `instellingen` | Oud settings systeem (fallback) |
| `bestellingen` | Orders — incl. `korting_code` + `korting_pct` kolommen |
| `klanten` | Klantgegevens |
| `mm_kortingscodes` | Kortingscodes — code, pct, actief, gebruikt, vervaldatum |

---

## File Structure
```
merch-master/
├── .env                          (SECRETS — niet in git)
├── .env.example
├── bestellen.php                 (5-stappen wizard — hoofdbestand)
├── bestellen/
│   ├── checkout.php              (Afrekenen — Proto A design standalone pagina)
│   ├── proto-a.html              (Proto A design template — basis voor checkout.php styling)
│   ├── catalogus.php             (API — producten + kleuren)
│   ├── ralawise_sync.php         (cron — sync images/stock)
│   ├── db_migratie_kortingscodes.sql  (eenmalig uitvoeren op live DB)
│   ├── admin/
│   │   ├── handler.php           (admin API endpoints)
│   │   └── index.php             (admin dashboard)
│   └── includes/
│       ├── db.php                (DB wrapper)
│       ├── db-config.php
│       └── bestellingen.php      (Bestellingen class — opslaan, regels, uploads)
├── includes/
│   ├── vertalingen.json
│   └── header.php / footer.php
├── winkelwagen.php               (DEPRECATED — checkout moved to bestellen/checkout.php)
├── wagen.php / wagen.js          (winkelwagen API/JS — basis winkelwagen functies)
├── mail.php                      (bevestigingsmails)
└── admin.php                     (hoofd admin)
```

---

## 📋 Todo

### 🟢 Afgerond — Checkout Bug Fixes (CSS, PayPal, Prijzen)
**Fixed:**
- [x] CSS MIME type error: `/includes/style.css` niet op server → inline styles gebruikt
- [x] PayPal SDK niet laden: sandbox client-id → real client-id ingesteld
- [x] Prijzen mismatch winkelwagen ≠ checkout: herberekening → server totals gebruiken
- [x] Error handling voor PayPal SDK fallback
- [x] Fetch paths absolute gemaakt voor consistency

**Implementation:**
- checkout.php geen externe CSS meer (alles inline)
- PayPal client-id = `ASLap52V7_VjYsq3D5k1W9a9RLG7854wBRs9TQ0m0PHhLXALJwrG3i-r4nrQOMuUr0d_Dqr5BSMv4ebk`
- TOTALEN object van wagen.php gebruikt voor exacte prijzen
- PayPal button graceful fallback bij load failure

### 🟢 Afgerond — Consolidatie winkelwagen → bestellen.php stap 6
**Done:**
- [x] Cart panel "Betalen →" button nu navigeert naar stap 6 via `gS(6);` (ipv `/winkelwagen.php`)
- [x] stap 6 initialiseert volledig: `tryAutoFill()` + `fillSum()`
- [x] `/winkelwagen.php` verdwijnt — alle checkout flow nu in `bestellen.php`

### 🟢 Afgerond — Kortingscodes feature
- [x] `mm_kortingscodes` tabel aangemaakt (SQL migratie in `bestellen/db_migratie_kortingscodes.sql`)
- [x] `korting_code` + `korting_pct` kolommen toegevoegd aan `bestellingen` tabel
- [x] `Bestellingen::opslaan()` slaat kortingscode + percentage op
- [x] handler.php: `korting-valideer`, `admin-kortingscodes`, `admin-korting-aanmaken`, `admin-korting-toggle`, `admin-korting-verwijder`
- [x] Admin UI: kortingscodes sectie in admin/index.php — tabel, aanmaken modal, toggle, verwijder
- [x] checkout.php: kortingscode invoerveld met AJAX validatie, `KORTING` state, korting-rij in prijsoverzicht
- [x] Kortingscode gemarkeerd als `gebruikt=1` bij plaatsen bestelling
- [x] Kortingberekening: over `totaal_excl`, BTW (21%) herberekend, daarna verzending opgeteld

**⚠️ TODO: SQL migratie nog uitvoeren op live server** (`bestellen/db_migratie_kortingscodes.sql` via phpMyAdmin)

### 🟢 Afgerond — Checkout Proto A redesign
- [x] checkout.php volledig hergebouwd naar Proto A design
- [x] Standalone pagina — geen site header/footer (minder afleiding, hogere conversie)
- [x] Two-column grid: form links (1fr), order summary sidebar rechts (360px)
- [x] Card-based componenten: `.card` / `.ch` / `.cb` / `.cf`
- [x] CSS design tokens: `--ac:#e84c1e`, `--ink`, `--bg:#f5f3f0`, `--sur:#fff`, etc.
- [x] Google Fonts Inter geladen
- [x] `updateSummary()` herbouwt `#order-summary` volledig via JS (`.si`, `.pt`, `.pr`, `.korting-wrap`)
- [x] Trust icons (SSL, retour, veilig betalen) in sidebar
- [x] Steps indicator in header

### 🟢 Afgerond — Linkerborst & Rechterborst positions (incl. Zeefdruk colors dropdown)
**Features (now part of 7-position implementation):**
- [x] Linkerborst & Rechterborst als solo selecteerbare posities in stap 2
- [x] Validatie: kunnen niet met voorkant of elkaar gecombineerd worden
- [x] Kunnen alleen gecombineerd worden met achterkant (left-back, right-back)
- [x] DTF pricing: €4,50 per positie (admin-settable, `drukkosten.dtf_borst` matrix)
- [x] Zeefdruk: gratis (uses voorkant pricing, geen separate costs)
- [x] Twee-techniek support: borst+achterkant kunnen beide eigen techniek hebben
- [x] Zeefdruk color dropdown (1-4 kleuren selectie) voor beide breast posities
- [x] Admin panel split DTF matrix: "Voorkant/Achterkant" vs "Linkerborst/Rechterborst"
- [x] wagen.php: berekenRegelPrijs() checks positie type om correct DTF matrix te gebruiken

*(See "🟢 Afgerond — 7-Position Implementation" section for complete details on all position types)*

### 🟢 Afgerond — Success/Thank You page & Cart Confirmation
- [x] checkout.php: Success screen toont order details na betaling (geen redirect naar bestellen.php)
- [x] bestellen.php stap 5: Full-screen "Product toegevoegd aan wagen" confirmation met checkmark
- [x] Confirmation buttons: groene "Betalen" + oranje "Meer producten toevoegen"

### 🟢 Afgerond — Checkout herstructurering → standalone checkout.php
Stap 6 uit bestellen.php verwijderd. Nieuwe standalone bestellen/checkout.php pagina met winkelwagen-review + klantgegevens + betaling.

**Done:**
- [x] bestellen.php gereduceerd naar 5 stappen (stap 6 verwijderd)
- [x] Nieuwe bestellen/checkout.php gemaakt met order summary, customer form, payment
- [x] Klantgegevens velden: voornaam, achternaam, straat+nr, postcode, plaats, email, telefoon
- [x] Optionele bedrijfsvelden: bedrijfsnaam, btw-nummer, kvk (NIET in form, waren al optioneel)
- [x] Bedrijf/Particulier toggle: blijft ALLEEN in stap 4 (niet in checkout form)
- [x] Auto-invullen vanuit klantenportaal als ingelogd (via $_SESSION['mm_klant'])
- [x] Guest checkout mogelijk (gegevens in form)
- [x] Opmerkingen zichtbaar in checkout (readonly, uit stap 5)
- [x] Bevestigingspagina behouden — success screen na betaling
- [x] PayPal integratie in checkout.php (dezelfde SDK als bestellen.php)
- [x] "Betalen" button navigeert naar checkout.php met wagen_token in URL
- [x] URL parameter check — bij ?success=X automatisch success screen tonen

**Implementation Details:**
- Pricing validation server-side in handler.php (bestaande logica behouden)
- regels array geformatteerd voor handler: {sku, prijs_ex, druk_ex, aantal, korting_pct}
- Verzending calculated based on total quantity (6.95€ < 12, 13.95€ ≥ 12)
- Cart loaded via wagen.php 'laden' action — dynamic pricing always fresh
- Form field names match handler expectations (voornaam, achternaam, etc)

### 🟢 Afgerond — 7-Position Implementation & Position Display Fix
**Features:**
- [x] All 7 position options selectable in stap 2: front, back, both, left, right, left-back, right-back
- [x] Position names displayed correctly in cart, checkout, quote box, and email confirmations
- [x] Zeefdruk color dropdowns conditional on position type (borst-posities only)
- [x] DTF €4,50 pricing for breast positions (admin-settable via `drukkosten.dtf_borst`)
- [x] Zeefdruk pricing: gratis for breast positions (uses front pricing matrix)
- [x] Two-technique support: combination positions can have different techs per position
- [x] End-to-end testing: all 7 position types, all combos, pricing validation

**Implementation Details:**
- **Helper function:** `formatPosLabel(pos)` — Maps position codes to user-friendly Dutch labels
- **bestellen.php:**
  - posNm object: `{front:'Voorkant', back:'Achterkant', both:'Beide kanten', left:'Linkerborst', right:'Rechterborst', 'left-back':'Linkerborst + Achterkant', 'right-back':'Rechterborst + Achterkant'}`
  - calcQ() logic: `isBoth = S.pos==='both'||S.pos==='left-back'||S.pos==='right-back'`; `isLeft = S.pos==='left'||S.pos==='left-back'`; `isRight = S.pos==='right'||S.pos==='right-back'`
  - setupStep4() zeefdruk visibility: show dropdowns only for left/right positions with zeefdruk selected
  - Quote box display: posALabel correctly labeled for all 7 position types
- **checkout.php:**
  - positieMap constant: Maps all 7 position values to display names
  - Position labels in sidebar "Jouw bestelling" section
- **mail.php:**
  - formatRegels() function enhanced with position column
  - Positions displayed as user-friendly labels in email tables
- **wagen.php:**
  - Position validation for DTF pricing matrix selection (dtf vs dtf_borst)
  - Two-technique support: checks S.configuring flag for combination positions

### Kritiek
- [ ] **SQL migratie uitvoeren op live server** — `bestellen/db_migratie_kortingscodes.sql` via phpMyAdmin
- [ ] Test kortingscode flow end-to-end op live (aanmaken in admin → gebruiken in checkout → gebruikt=1 in DB)
- [ ] Test bevestigingsmails op live server (incl. position names)
- [x] PayPal live client-id (nu ingesteld: `ASLap52V7_VjYsq3D5k1W9a9RLG7854wBRs9TQ0m0PHhLXALJwrG3i-r4nrQOMuUr0d_Dqr5BSMv4ebk`)

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
