# Merch Master — Website & Besteltool

## Project Overview
Ik ben bezig met het bouwen van een volledige website en besteltool voor **Merch Master** (merch-master.com), een Nederlandse printshop.

## Stack
- **Backend:** PHP + MySQL
- **Hosting:** Hostinger shared hosting
- **Local workspace:** `/home/claude/site_v2/`
- **Deployment:** Zip naar `/home/claude/mm-clean.zip` en presenteren via present_files

## Credentials
All credentials are stored in `.env` file (NOT tracked in git). See `.env.example` for template.
- **Database:** `u204320941_merchmaster`
- **Site:** https://merch-master.com
- **Admin password:** See `.env` (ADMIN_PASSWORD)

## Design Rules

### No emojis in PHP
- Always use SVG icons instead of emojis

### Multilingual Support (4 languages)
All customer-visible text must be in: **NL / EN / DE / NO**
- Always use `t()` and `t_lt()` functions for translations
- Translations stored in `vertalingen.json`

### Credentials in Deployment
- Credentials are always embedded in deployment zips
- Use `.env` file locally for development
- Update `.env` before building zips for deployment

## ✅ Completed Features

### Admin Panel (`/bestellen/admin/`)
- Full dashboard with orders + profit overview
- Customer management
- Price margins per tier (budget/standard/premium)
- Print cost matrix (DTF, screen print, embroidery on request)
- Delivery times management
- Volume discount tiers
- Quotations management

### Shopping Cart System (`wagen.php` + `wagen.js`)
- CSRF-protected
- Error handling & session coupling
- Auto-cleanup after 30 days
- Upload storage with MIME checking
- VAT toggle

### Quotations Engine
- Customers request quotes → profit calculation stored (`winst_excl`)
- Admin sets status via modal: concept → sent → accepted → paid → expired
- No more prompt() dialogs

### 7-Step Order Wizard (`/bestellen/index.php`)
1. Category → 2. Product → 3. Color & Size → 4. Technique → 5. Print Positions → 6. Upload → 7. Checkout
- Live price preview
- VAT toggle
- PayPal integration
- Confirmation email

### Mail Handler (`mail.php`)
- HTML emails to customer & admin after quote and payment
- Rush orders marked with 🚨 in subject

### Catalogus API (`catalogus.php`)
- 7 fixed categories: T-shirts, Polo's, Sweaters, Hoodies, Caps, Jackets, Bags
- Products linked via keyword detection (name/tags)
- 133 products in database

## 🔧 Important Technical Decisions

### Print Positions
- Front / Back / Left Breast / Right Breast
- Left & Right Breast only combinable with Back, NEVER with Front
- Validation at 3 levels: UI live check → on save → server-side PHP

### Price Logic
- Textiles: purchase price × margin per tier
- DTF: print costs in 3 price bands
- Screen print: matrix per run × colors
- Volume discounts: configurable tiers
- Rush surcharge: 40%
- Shipping: based on quantity

### Product Display
- Customer sees: product name + color
- SKU used internally for price calculation

## 🐛 Known Issues

### Color Display Bug (OPEN)
- `catalogus_kleuren` table links via `product_sku` (varchar), not integer ID
- Color code column is `hex` (not `hex_code`)
- Despite fixes: `kleur_count: 0` and `kleuren: []` for all products
- Colors table appears empty OR SKU matching isn't working
- Debug block missing from latest JSON response → latest `catalogus.php` may not be uploaded correctly
- **Action:** Debug colors and restore color display in wizard

## 📋 Todo List

### Critical
- [ ] Debug & fix color display in wizard
- [ ] Test confirmation emails on live server
- [ ] PayPal live client-id (currently sandbox `sb`)

### Important
- [ ] Ralawise API sync via cron job
- [ ] Jortt server-side invoicing
- [ ] L-shop API integration
- [ ] Remove admin-test.php and phpcheck.php from server

### Deferred
- [ ] iDEAL via Mollie

## File Structure
```
merch-master/
├── .env (SECRETS — not in git)
├── .env.example (template for credentials)
├── .gitignore (excludes .env)
├── index.php (main site)
├── bestellen.php (new ordering tool in PHP)
├── bestellen/
│   ├── catalogus.php (API — 133 products)
│   ├── index.html (old standalone JS tool — fallback)
│   └── includes/
│       └── config.php (reads from .env)
├── includes/
│   ├── vertalingen.json (4-language strings)
│   └── ... (shared PHP includes)
├── admin.php (admin dashboard)
├── [product pages].php (zeefdruk.php, dtf.php, duurzaam.php, etc.)
└── [content pages].php (over-ons.php, contact.php, faq.php, etc.)
```

## How to Use This Project in Claude Code

### Workflow
1. Copy `.env.example` to `.env` and fill in your local database credentials
2. `git pull` latest changes
3. Work on files as needed
4. **Commit & push changes regularly** (see Git Workflow below)
5. Build & deploy zips with updated credentials

### Git Workflow — IMPORTANT
**Always commit and push regularly to prevent data loss.** Follow these practices:

#### Commit Messages
Use clear, imperative commit messages following this format:
- **Feature:** `feat: Add color display to product wizard`
- **Bug fix:** `fix: Debug color SKU matching in catalogus API`
- **Docs:** `docs: Update project status in CLAUDE.md`
- **Refactor:** `refactor: Simplify price calculation logic`
- **Security:** `security: Move credentials to .env file`

Example:
```bash
git add .
git commit -m "feat: Implement Ralawise API sync via cron job"
git push origin main
```

#### Commit Frequency
- Commit after **each completed feature or bug fix**
- Never leave work uncommitted at end of day
- Push to GitHub **at least once per work session**
- Use atomic commits (one logical change per commit)

#### Status Check Before Pushing
```bash
git status              # Check what's staged
git log --oneline -5   # See recent commits
git push origin main   # Push to GitHub
```

### Why This Matters
- **No data loss:** Every commit is backed up on GitHub
- **Track progress:** Clear commit history shows what's been done
- **Easy rollback:** Can revert to previous versions if needed
- **Team collaboration:** Easy to see who changed what and when
- **Deployment safety:** Clean history makes deployment zips more reliable
