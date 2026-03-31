<?php require_once __DIR__.'/taal.php';
$ICON_WA='<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>';
$ICON_MAIL='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="20" height="20"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
$ICON_IG='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="20" height="20"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>';
$ICON_BOLT='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
$ICON_TARGET='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>';
$ICON_LEAF='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M17 8C8 10 5.9 16.17 3.82 19.5c2.47.17 5.1-.29 7.18-1.5 2.5-1.45 4.14-3.79 4.64-6.23.5-2.44-.07-5.44-2.64-7.77C12.97 3.9 8 4 8 4s0 5 9 4z"/></svg>';
$ICON_CHAT='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>';
?>
<!DOCTYPE html>
<html lang="<?= $TAAL ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($PAGE_TITLE ?? 'Merch Master') ?> — Merch Master</title>
<meta name="description" content="<?= htmlspecialchars($PAGE_DESC ?? 'Print- en borduurservice voor festivals, evenementen en duurzame merken.') ?>">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --creme:#f5f0e8;--zand:#e8ddc8;--terracotta:#c4622d;--accent:#e84c1e;
  --groen:#3a5a40;--groen2:#5a7a60;--donkergroen:#243326;
  --ink:#1e1a16;--ink2:#5a5248;--ink3:#8a8278;
  --wit:#faf7f2;--kaart:#fff9f2;
  --r:8px;
  --display:'Playfair Display',serif;
  --body:'DM Sans',sans-serif;
}
html{scroll-behavior:smooth;}
body{font-family:var(--body);background:var(--creme);color:var(--ink);overflow-x:hidden;}

/* ── NAV ─────────────────────────────────────────────────────────────────────*/
nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 2.5rem;display:flex;align-items:center;justify-content:space-between;height:68px;background:rgba(245,240,232,.95);backdrop-filter:blur(12px);border-bottom:1px solid rgba(196,98,45,.15);}
.nav-logo{display:flex;align-items:center;gap:.7rem;text-decoration:none;}
.nav-logo-mark{width:38px;height:38px;background:var(--terracotta);border-radius:6px;display:flex;align-items:center;justify-content:center;}
.nav-logo-mark svg{width:20px;height:20px;fill:#fff;}
.nav-logo-txt{font-family:var(--display);font-size:1.25rem;font-weight:700;color:var(--ink);line-height:1.1;}
.nav-logo-txt em{font-style:normal;color:var(--terracotta);}
.nav-logo-sub{font-size:.58rem;color:var(--ink3);letter-spacing:.12em;text-transform:uppercase;display:block;font-family:var(--body);}
.nav-links{display:flex;align-items:center;gap:2.25rem;}
.nav-links a{font-size:.85rem;font-weight:500;color:var(--ink2);text-decoration:none;transition:color .15s;}
.nav-links a:hover{color:var(--terracotta);}
.nav-cta{background:var(--terracotta);color:#fff!important;padding:.55rem 1.2rem;border-radius:50px;font-weight:600!important;transition:background .15s!important;}
.nav-cta:hover{background:#a84e22!important;}
.nav-toggle{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:.5rem;}
.nav-toggle span{display:block;width:22px;height:2px;background:var(--ink);border-radius:2px;}
.mob-menu{display:none;position:fixed;inset:68px 0 0;background:var(--wit);z-index:99;padding:2rem;flex-direction:column;gap:1.5rem;border-top:1px solid var(--zand);}
.mob-menu.open{display:flex;}
.mob-menu a{font-size:1.05rem;font-weight:500;color:var(--ink2);text-decoration:none;}
.mob-menu .nav-cta{color:#fff!important;text-align:center;padding:.85rem;font-size:.95rem;border-radius:50px;}

/* ── HERO ─────────────────────────────────────────────────────────────────────*/
.hero{min-height:100vh;display:grid;grid-template-columns:1fr 1fr;position:relative;padding-top:68px;overflow:hidden;}
.hero-left{background:var(--donkergroen);display:flex;flex-direction:column;justify-content:center;padding:5rem 4rem 5rem 6rem;position:relative;overflow:hidden;}
.hero-left::before{content:'';position:absolute;bottom:-80px;left:-80px;width:320px;height:320px;border-radius:50%;background:rgba(90,122,96,.25);pointer-events:none;}
.hero-left::after{content:'';position:absolute;top:-40px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(196,98,45,.15);pointer-events:none;}
.hero-eyebrow{display:inline-flex;align-items:center;gap:.5rem;font-size:.7rem;font-weight:600;letter-spacing:.15em;text-transform:uppercase;color:rgba(250,247,242,.5);margin-bottom:1.5rem;}
.hero-eyebrow::before{content:'';width:24px;height:1px;background:var(--terracotta);}
h1{font-family:var(--display);font-size:clamp(2.8rem,5vw,4rem);font-weight:900;line-height:1.05;color:var(--wit);margin-bottom:1.5rem;}
h1 em{font-style:italic;color:var(--terracotta);}
.hero-sub{font-size:1rem;color:rgba(250,247,242,.65);line-height:1.75;margin-bottom:2.5rem;max-width:400px;}
.hero-btns{display:flex;flex-direction:column;gap:.75rem;max-width:280px;}
.btn-p{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;background:var(--terracotta);color:#fff;font-family:var(--body);font-size:.9rem;font-weight:700;padding:.9rem 1.8rem;border-radius:50px;text-decoration:none;transition:all .2s;border:none;cursor:pointer;width:100%;}
.btn-p:hover{background:#a84e22;transform:translateY(-1px);}
.btn-ghost{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;background:transparent;color:rgba(250,247,242,.7);font-family:var(--body);font-size:.88rem;font-weight:500;padding:.85rem 1.8rem;border-radius:50px;text-decoration:none;transition:all .2s;border:1px solid rgba(250,247,242,.2);min-width:220px;}
.btn-ghost:hover{border-color:rgba(250,247,242,.5);color:var(--wit);}
.hero-badges{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:2.5rem;}
.hero-badge{font-size:.65rem;font-weight:600;color:rgba(250,247,242,.5);background:rgba(250,247,242,.07);border:1px solid rgba(250,247,242,.12);border-radius:20px;padding:.25rem .7rem;letter-spacing:.05em;}

.hero-right{background:var(--zand);display:flex;flex-direction:column;padding:0;position:relative;}
.hero-right-top{flex:1;overflow:hidden;position:relative;}
.hero-right-top img,.hero-img-placeholder{width:100%;height:100%;object-fit:cover;}
.hero-img-placeholder{background:linear-gradient(135deg,#d4c9b0 0%,#c4b898 50%,#b8a880 100%);display:flex;align-items:center;justify-content:center;min-height:300px;}
.hero-img-placeholder span{font-size:5rem;opacity:.3;}
.hero-right-bottom{background:var(--kaart);border-top:1px solid rgba(196,98,45,.15);padding:1.75rem 2rem;display:grid;grid-template-columns:repeat(3,1fr);gap:1px;}
.hero-stat{text-align:center;padding:.75rem;}
.hero-stat-n{font-family:var(--display);font-size:1.8rem;font-weight:700;color:var(--terracotta);}
.hero-stat-l{font-size:.65rem;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em;margin-top:.15rem;}

/* ── Gemeenschappelijk ───────────────────────────────────────────────────────*/
.container{max-width:1100px;margin:0 auto;}
.sec-kop{text-align:center;margin-bottom:3.5rem;}
.sec-oogje{font-size:.7rem;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--terracotta);margin-bottom:.6rem;}
h2{font-family:var(--display);font-size:clamp(2rem,4vw,2.8rem);font-weight:700;line-height:1.1;color:var(--ink);}
h2 em{font-style:italic;color:var(--terracotta);}
.sec-sub{font-size:1rem;color:var(--ink2);line-height:1.7;max-width:540px;margin:.85rem auto 0;}

/* ── ECO SECTION ─────────────────────────────────────────────────────────────*/
.eco-band{background:var(--donkergroen);padding:4rem 2.5rem;}
.eco-inner{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;}
.eco-tekst .sec-oogje{color:rgba(90,122,96,1);}
.eco-tekst h2{color:var(--wit);}
.eco-tekst p{font-size:.95rem;color:rgba(250,247,242,.65);line-height:1.75;margin-top:.85rem;margin-bottom:1.75rem;}
.eco-punten{display:flex;flex-direction:column;gap:.85rem;}
.eco-punt{display:flex;align-items:flex-start;gap:.85rem;}
.eco-punt-icon{width:36px;height:36px;background:rgba(90,122,96,.3);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.eco-punt-info strong{display:block;font-size:.88rem;color:var(--wit);margin-bottom:.15rem;}
.eco-punt-info span{font-size:.78rem;color:rgba(250,247,242,.5);line-height:1.5;}
.eco-visual{background:rgba(250,247,242,.05);border:1px solid rgba(250,247,242,.1);border-radius:12px;padding:2rem;}
.eco-vis-ttl{font-family:var(--display);font-size:1.1rem;color:var(--wit);margin-bottom:1.25rem;}
.eco-stoffen{display:flex;flex-direction:column;gap:.6rem;}
.eco-stof{background:rgba(250,247,242,.06);border-radius:8px;padding:.75rem 1rem;display:flex;justify-content:space-between;align-items:center;}
.eco-stof-nm{font-size:.83rem;color:rgba(250,247,242,.8);}
.eco-stof-tag{font-size:.62rem;font-weight:700;background:rgba(90,122,96,.3);color:#86efac;border-radius:12px;padding:.2rem .6rem;letter-spacing:.06em;}

/* ── HOE WERKT HET ────────────────────────────────────────────────────────────*/
.hoe{padding:5rem 2.5rem;background:var(--creme);}
.hoe-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin-top:3rem;position:relative;}
.hoe-grid::before{content:'';position:absolute;top:2rem;left:12%;right:12%;height:1px;background:repeating-linear-gradient(90deg,var(--terracotta) 0,var(--terracotta) 8px,transparent 8px,transparent 20px);}
.hoe-stap{text-align:center;padding:0 1.25rem;position:relative;}
.hoe-nr{width:40px;height:40px;border-radius:50%;background:var(--terracotta);color:#fff;font-family:var(--display);font-size:1rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;position:relative;z-index:1;}
.hoe-ttl{font-family:var(--display);font-size:1rem;font-weight:700;margin-bottom:.4rem;}
.hoe-txt{font-size:.78rem;color:var(--ink2);line-height:1.6;}

/* ── TECHNIEKEN ──────────────────────────────────────────────────────────────*/
.tech{padding:5rem 2.5rem;background:var(--wit);}
.tech-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-top:3rem;align-items:stretch;}
.tech-card{border-radius:12px;border:1px solid rgba(196,98,45,.12);overflow:hidden;background:var(--kaart);transition:border-color .2s;display:flex;flex-direction:column;}
.tech-card:hover{border-color:rgba(196,98,45,.4);}
.tech-top{padding:2rem 1.75rem 0;}
.tech-icoon{font-size:2rem;margin-bottom:.85rem;}
.tech-naam{font-family:var(--display);font-size:1.4rem;font-weight:700;margin-bottom:.25rem;}
.tech-sub{font-size:.75rem;color:var(--ink3);font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.85rem;}
.tech-uitleg{font-size:.83rem;color:var(--ink2);line-height:1.65;padding:0 1.75rem;}
.tech-pros-cons{display:grid;grid-template-columns:1fr 1fr;gap:0;margin-top:1.5rem;border-top:1px solid rgba(196,98,45,.1);}
.tech-pros,.tech-cons{padding:1.25rem 1.75rem;}
.tech-pros{border-right:1px solid rgba(196,98,45,.1);}
.tech-col-ttl{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.6rem;}
.tech-pros .tech-col-ttl{color:var(--groen);}
.tech-cons .tech-col-ttl{color:#c0392b;}
.tech-li{font-size:.75rem;color:var(--ink2);line-height:1.55;padding:.15rem 0;display:flex;align-items:flex-start;gap:.4rem;}
.tech-li-pro::before{content:'';color:var(--groen);flex-shrink:0;font-weight:700;}
.tech-li-con::before{content:'·';color:#c0392b;flex-shrink:0;}
.tech-best{background:rgba(196,98,45,.05);padding:1.25rem 1.75rem;border-top:1px solid rgba(196,98,45,.1);}
.tech-best-ttl{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ink3);margin-bottom:.4rem;}
.tech-best-txt{font-size:.78rem;color:var(--ink2);line-height:1.5;}

/* ── CTA MIDDEN ──────────────────────────────────────────────────────────────*/
.cta-mid{padding:5rem 2.5rem;background:var(--zand);text-align:center;position:relative;overflow:hidden;}
.cta-mid::before{content:'';}
.cta-mid h2{font-size:clamp(2.2rem,5vw,3.5rem);margin-bottom:.85rem;position:relative;}
.cta-mid p{font-size:1.05rem;color:var(--ink2);line-height:1.7;max-width:520px;margin:0 auto 2.5rem;position:relative;}
.cta-btns{display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;position:relative;}
.btn-groot{display:inline-flex;align-items:center;gap:.6rem;background:var(--terracotta);color:#fff;font-family:var(--body);font-size:1rem;font-weight:700;padding:1rem 2.25rem;border-radius:50px;text-decoration:none;transition:all .2s;}
.btn-groot:hover{background:#a84e22;transform:translateY(-2px);box-shadow:0 8px 24px rgba(196,98,45,.35);}
.btn-lijn{display:inline-flex;align-items:center;gap:.6rem;background:transparent;color:var(--ink);font-family:var(--body);font-size:1rem;font-weight:600;padding:1rem 2.25rem;border-radius:50px;text-decoration:none;transition:all .2s;border:2px solid rgba(30,26,22,.25);}
.btn-lijn:hover{border-color:var(--terracotta);color:var(--terracotta);}

/* ── OVER ONS ────────────────────────────────────────────────────────────────*/
.over{padding:5rem 2.5rem;background:var(--wit);}
.over-grid{display:grid;grid-template-columns:1fr 1fr;gap:4.5rem;align-items:start;margin-top:3rem;}
.over-tekst p{font-size:.95rem;color:var(--ink2);line-height:1.8;margin-bottom:1rem;}
.over-vals{display:grid;grid-template-columns:1fr 1fr;gap:.85rem;margin-top:2rem;}
.over-val{background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;padding:1.1rem;}
.over-val-icon{font-size:1.3rem;margin-bottom:.4rem;}
.over-val-ttl{font-size:.85rem;font-weight:700;color:var(--ink);margin-bottom:.25rem;}
.over-val-txt{font-size:.75rem;color:var(--ink3);line-height:1.5;}
.over-reviews{display:flex;flex-direction:column;gap:1rem;}
.review{background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;padding:1.5rem;}
.stars{color:#f59e0b;font-size:.85rem;margin-bottom:.6rem;}
.review-txt{font-size:.88rem;color:var(--ink2);line-height:1.65;font-style:italic;}
.review-auteur{font-size:.75rem;font-weight:600;color:var(--ink3);margin-top:.75rem;display:flex;align-items:center;gap:.5rem;}
.review-auteur::before{content:'';width:20px;height:1px;background:var(--terracotta);}

/* ── CONTACT ─────────────────────────────────────────────────────────────────*/
.contact{padding:5rem 2.5rem;background:var(--creme);}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:4rem;margin-top:3rem;}
.contact-info p{font-size:.95rem;color:var(--ink2);line-height:1.8;margin-bottom:2rem;}
.contact-methods{display:flex;flex-direction:column;gap:.75rem;}
.contact-method{display:flex;align-items:center;gap:.85rem;padding:1rem 1.25rem;background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:10px;text-decoration:none;transition:all .18s;}
.contact-method:hover{border-color:var(--terracotta);transform:translateX(4px);}
.cm-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.cm-wa{background:rgba(37,211,102,.12);}
.cm-mail{background:rgba(196,98,45,.1);}
.cm-ig{background:rgba(193,53,132,.1);}
.cm-info strong{display:block;font-size:.85rem;font-weight:600;color:var(--ink);}
.cm-info span{font-size:.75rem;color:var(--ink3);}
.contact-form{background:var(--kaart);border:1px solid rgba(196,98,45,.15);border-radius:12px;padding:2.25rem;}
.form-ttl{font-family:var(--display);font-size:1.4rem;font-weight:700;margin-bottom:.35rem;}
.form-sub{font-size:.82rem;color:var(--ink3);margin-bottom:1.75rem;}
.f-group{margin-bottom:1rem;}
.f-group label{display:block;font-size:.7rem;font-weight:600;color:var(--ink3);margin-bottom:.35rem;text-transform:uppercase;letter-spacing:.07em;}
.f-group input,.f-group textarea,.f-group select{width:100%;background:var(--creme);border:1.5px solid rgba(196,98,45,.2);border-radius:8px;padding:.7rem .95rem;color:var(--ink);font-size:.88rem;font-family:var(--body);transition:border-color .15s;}
.f-group input:focus,.f-group textarea:focus,.f-group select:focus{outline:none;border-color:var(--terracotta);}
.f-group textarea{height:110px;resize:vertical;}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
.f-send{width:100%;background:var(--terracotta);color:#fff;font-family:var(--body);font-size:.9rem;font-weight:700;padding:.9rem;border:none;border-radius:50px;cursor:pointer;transition:background .15s;margin-top:.25rem;}
.f-send:hover{background:#a84e22;}
.f-ok{display:none;text-align:center;padding:.75rem;background:rgba(58,90,64,.1);border-radius:8px;font-size:.83rem;color:var(--groen);margin-top:.75rem;}

/* ── FOOTER ──────────────────────────────────────────────────────────────────*/
footer{background:var(--donkergroen);padding:3.5rem 2.5rem 1.5rem;}
.footer-inner{max-width:1100px;margin:0 auto;}
.footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:2.5rem;margin-bottom:3rem;}
.footer-brand p{font-size:.82rem;color:rgba(250,247,242,.45);line-height:1.7;max-width:260px;margin-top:.85rem;}
.footer-logo{font-family:var(--display);font-size:1.2rem;font-weight:700;color:var(--wit);text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;}
.footer-logo-mark{width:30px;height:30px;background:var(--terracotta);border-radius:5px;display:flex;align-items:center;justify-content:center;}
.footer-logo-mark svg{width:16px;height:16px;fill:#fff;}
.footer-col h4{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:rgba(250,247,242,.3);margin-bottom:.85rem;}
.footer-col a{display:block;font-size:.82rem;color:rgba(250,247,242,.5);text-decoration:none;margin-bottom:.4rem;transition:color .15s;}
.footer-col a:hover{color:var(--wit);}
.footer-bottom{border-top:1px solid rgba(250,247,242,.08);padding-top:1.5rem;display:flex;align-items:center;justify-content:space-between;font-size:.73rem;color:rgba(250,247,242,.3);}
.footer-bottom a{color:rgba(250,247,242,.3);text-decoration:none;}
.footer-bottom a:hover{color:rgba(250,247,242,.6);}

/* ── WA FLOAT ────────────────────────────────────────────────────────────────*/
.wa-float{position:fixed;bottom:1.5rem;right:1.5rem;width:52px;height:52px;background:#25D366;border-radius:50%;display:flex;align-items:center;justify-content:center;z-index:90;box-shadow:0 4px 20px rgba(0,0,0,.2);text-decoration:none;transition:transform .2s;}
.wa-float:hover{transform:scale(1.1);}
.wa-float svg{width:26px;height:26px;fill:#fff;}

/* ── ANIMATIES ───────────────────────────────────────────────────────────────*/
.fade-in{opacity:0;transform:translateY(20px);transition:opacity .55s ease,transform .55s ease;}
.fade-in.zichtbaar{opacity:1;transform:translateY(0);}
.fade-in:nth-child(2){transition-delay:.1s;}
.fade-in:nth-child(3){transition-delay:.2s;}

/* ── MOBILE ──────────────────────────────────────────────────────────────────*/
@media(max-width:960px){
  .hero{grid-template-columns:1fr;min-height:auto;}
  .hero-left{padding:4rem 2rem 3rem;}
  .hero-right-top{min-height:240px;}
  .niches-grid{grid-template-columns:1fr;}
  .eco-inner{grid-template-columns:1fr;}
  .tech-grid{grid-template-columns:1fr;}
  .over-grid{grid-template-columns:1fr;}
  .contact-grid{grid-template-columns:1fr;}
  .footer-top{grid-template-columns:1fr 1fr;}
  .hoe-grid{grid-template-columns:1fr 1fr;}
  .hoe-grid::before{display:none;}
  .nav-links{display:none;}
  .nav-toggle{display:flex;}
}
@media(max-width:560px){
  .hero-left{padding:3rem 1.5rem;}
  .hero-right-bottom{grid-template-columns:1fr 1fr 1fr;}
  .tech-pros-cons{grid-template-columns:1fr;}
  .tech-pros{border-right:none;border-bottom:1px solid rgba(196,98,45,.1);}
  .f-row{grid-template-columns:1fr;}
  .footer-top{grid-template-columns:1fr;}
  .over-vals{grid-template-columns:1fr;}
  .hoe-grid{grid-template-columns:1fr;}
  section,.niches,.eco-band,.hoe,.tech,.cta-mid,.over,.contact{padding-left:1.25rem;padding-right:1.25rem;}
}

/* ── Get Started Button (pijl-effect) ───────────────────────────────────────*/
.btn-arrow{position:relative;overflow:hidden;display:inline-flex;align-items:center;justify-content:center;background:var(--terracotta);color:#fff;font-family:var(--body);font-size:.95rem;font-weight:700;padding:.9rem 1.8rem;border-radius:50px;text-decoration:none;border:none;cursor:pointer;min-width:220px;transition:background .2s;}
.btn-arrow:hover{background:#a84e22;}
.btn-arrow-txt{margin-right:2rem;transition:opacity .4s;white-space:nowrap;}
.btn-arrow:hover .btn-arrow-txt{opacity:0;}
.btn-arrow-icon{position:absolute;right:6px;top:6px;bottom:6px;border-radius:40px;display:grid;place-items:center;width:20%;background:rgba(255,255,255,.18);transition:width .4s ease;z-index:1;}
.btn-arrow:hover .btn-arrow-icon{width:calc(100% - 12px);}
.btn-arrow-icon svg{width:16px;height:16px;stroke:#fff;flex-shrink:0;}
.btn-arrow.wit{background:#fff;color:var(--terracotta);}
.btn-arrow.wit:hover{background:#f5f0e8;}
.btn-arrow.wit .btn-arrow-icon{background:rgba(196,98,45,.15);}
.btn-arrow.wit .btn-arrow-icon svg{stroke:var(--terracotta);}
.btn-arrow.groen{background:var(--donkergroen);}
.btn-arrow.groen:hover{background:#1a2620;}

/* ── Scrollende reviews ──────────────────────────────────────────────────────*/
.reviews-scroll-wrap{overflow:hidden;max-height:600px;mask-image:linear-gradient(to bottom,transparent,black 15%,black 85%,transparent);-webkit-mask-image:linear-gradient(to bottom,transparent,black 15%,black 85%,transparent);margin-top:2rem;}
.reviews-cols{display:flex;gap:1.25rem;}
.reviews-col{flex:1;display:flex;flex-direction:column;gap:1.25rem;}
.reviews-col-inner{display:flex;flex-direction:column;gap:1.25rem;animation:scrollUp 20s linear infinite;}
.reviews-col:nth-child(2) .reviews-col-inner{animation-duration:26s;animation-delay:-8s;}
.reviews-col:nth-child(3) .reviews-col-inner{animation-duration:23s;animation-delay:-4s;}
@keyframes scrollUp{0%{transform:translateY(0);}100%{transform:translateY(-50%);}}
.review-scroll-card{background:var(--kaart);border:1px solid rgba(196,98,45,.12);border-radius:12px;padding:1.5rem;flex-shrink:0;}
.review-scroll-txt{font-size:.85rem;color:var(--ink2);line-height:1.7;font-style:italic;margin-bottom:1rem;}
.review-scroll-auteur{display:flex;align-items:center;gap:.65rem;}
.review-scroll-avatar{width:36px;height:36px;border-radius:50%;object-fit:cover;background:var(--zand);}
.review-scroll-naam{font-size:.8rem;font-weight:600;color:var(--ink);}
.review-scroll-stad{font-size:.72rem;color:var(--ink3);}
.review-scroll-stars{color:#f59e0b;font-size:.75rem;margin-bottom:.6rem;}
@media(max-width:768px){.reviews-col:nth-child(2),.reviews-col:nth-child(3){display:none;}}

/* ── FAQ ─────────────────────────────────────────────────────────────────────*/
.faq{padding:5rem 2.5rem;background:var(--zand);}
.faq-lijst{max-width:680px;margin:3rem auto 0;}
.faq-item{display:flex;gap:1.25rem;margin-bottom:2rem;}
.faq-nr{width:28px;height:28px;border-radius:6px;background:var(--terracotta);color:#fff;font-family:var(--display);font-size:.85rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;}
.faq-body h3{font-size:.95rem;font-weight:700;color:var(--ink);margin-bottom:.4rem;}
.faq-body p{font-size:.85rem;color:var(--ink2);line-height:1.75;}
@media(max-width:560px){.faq{padding:3.5rem 1.25rem;}}

/* ── Taalschakelaar ─────────────────────────────────────────────────────────*/
.lang-wrap{position:relative;margin-left:.5rem;}
.lang-btn{display:flex;align-items:center;gap:.4rem;background:transparent;border:1px solid rgba(196,98,45,.25);border-radius:20px;padding:.35rem .75rem;font-size:.78rem;font-weight:600;color:var(--ink2);cursor:pointer;font-family:var(--body);transition:all .15s;}
.lang-btn:hover{border-color:var(--terracotta);color:var(--terracotta);}
.lang-btn .vlag{font-size:.9rem;}
.lang-menu{position:absolute;top:calc(100% + .4rem);right:0;background:var(--wit);border:1px solid rgba(196,98,45,.15);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.1);overflow:hidden;display:none;z-index:200;min-width:130px;}
.lang-menu.open{display:block;}
.lang-opt{display:flex;align-items:center;gap:.6rem;padding:.6rem 1rem;font-size:.82rem;font-weight:500;color:var(--ink2);cursor:pointer;transition:background .12s;}
.lang-opt:hover{background:var(--zand);}
.lang-opt.actief{color:var(--terracotta);font-weight:700;}

/* ── Nav dropdown ────────────────────────────────────────────────────────────*/
.nav-dropdown{position:relative;}
.nav-dropdown > a{display:flex;align-items:center;gap:.3rem;cursor:pointer;}
.nav-dropdown > a svg{transition:transform .2s;}
.nav-dropdown.open > a svg,.nav-dropdown:hover > a svg{transform:rotate(180deg);}
/* Padding-top voorkomt gap tussen link en menu */
.nav-drop-menu{position:absolute;top:100%;left:0;padding-top:.5rem;background:transparent;display:none;z-index:200;min-width:200px;}
.nav-dropdown:hover .nav-drop-menu,.nav-dropdown.open .nav-drop-menu{display:block;}
.nav-drop-inner{background:var(--wit);border:1px solid rgba(196,98,45,.15);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.1);overflow:hidden;}
.nav-drop-item{display:flex;align-items:center;padding:.7rem 1.1rem;font-size:.83rem;font-weight:500;color:var(--ink2);text-decoration:none;transition:background .12s;border-bottom:1px solid rgba(196,98,45,.06);}
.nav-drop-item:last-child{border-bottom:none;}
.nav-drop-item:hover{background:var(--zand);color:var(--terracotta);}

.merk-card{background:var(--wit);border:1px solid rgba(196,98,45,.15);border-radius:10px;padding:1rem 1.5rem;display:flex;align-items:center;justify-content:center;min-width:140px;min-height:72px;transition:border-color .2s,box-shadow .2s;}
.merk-card:hover{border-color:rgba(196,98,45,.35);box-shadow:0 4px 12px rgba(196,98,45,.1);}
.merk-logo{max-width:110px;max-height:44px;object-fit:contain;filter:grayscale(100%) opacity(.55);transition:filter .2s;}
.merk-card:hover .merk-logo{filter:grayscale(0%) opacity(1);}
</style>
</head>
<body>
<nav>
  <a href="/" class="nav-logo">
    <div class="nav-logo-mark"><svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
    <div><div class="nav-logo-txt">Merch<em>Master</em></div><span class="nav-logo-sub"><?= t('nav_tagline') ?></span></div>
  </a>
  <div class="nav-links">
    <a href="/"><?= t('nav_home') ?></a>
    <div class="nav-dropdown">
      <a href="/#technieken"><?= t('nav_technieken') ?>
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
      </a>
      <div class="nav-drop-menu">
        <div class="nav-drop-inner">
          <a href="/zeefdruk.php" class="nav-drop-item"><?= t('nav_zeefdruk') ?></a>
          <a href="/dtf.php" class="nav-drop-item"><?= t('nav_dtf') ?></a>
          <a href="/borduren.php" class="nav-drop-item"><?= t('nav_borduren') ?></a>
        </div>
      </div>
    </div>
    <a href="/over-ons.php"><?= t('nav_over') ?></a>
    <a href="/duurzaam.php"><?= t('nav_duurzaam') ?></a>
    <a href="/faq.php"><?= t('nav_faq') ?></a>
    <a href="/contact.php"><?= t('nav_contact') ?></a>
    <a href="/bestellen.php" class="nav-cta"><?= t('nav_bestel') ?></a>
    <a href="/bestellen-oud.php" style="font-size:.75rem;color:var(--ink3);text-decoration:none;padding:.35rem .75rem;border:1px solid rgba(196,98,45,.2);border-radius:20px;white-space:nowrap;">Oud</a>
    <div class="lang-wrap">
      <button class="lang-btn" onclick="toggleLangMenu()">
        <span><?= $VLAG[$TAAL] ?></span>
        <span><?= $TAAL_LBL[$TAAL] ?></span>
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div class="lang-menu" id="lang-menu">
        <?php foreach(['nl'=>$VLAG['nl'].' NL — Nederlands','en'=>$VLAG['en'].' EN — English','de'=>$VLAG['de'].' DE — Deutsch','no'=>$VLAG['no'].' NO — Norsk'] as $code=>$label): ?>
        <a href="?lang=<?= $code ?>" class="lang-opt <?= $TAAL===$code?'actief':'' ?>"><?= $label ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="nav-toggle" onclick="toggleMenu()"><span></span><span></span><span></span></div>
</nav>
<div class="mob-menu" id="mob-menu">
  <a href="/" onclick="toggleMenu()"><?= t('nav_home') ?></a>
  <a href="/zeefdruk.php" onclick="toggleMenu()"><?= t('nav_zeefdruk') ?></a>
  <a href="/dtf.php" onclick="toggleMenu()"><?= t('nav_dtf') ?></a>
  <a href="/borduren.php" onclick="toggleMenu()"><?= t('nav_borduren') ?></a>
  <a href="/over-ons.php" onclick="toggleMenu()"><?= t('nav_over') ?></a>
  <a href="/duurzaam.php" onclick="toggleMenu()"><?= t('nav_duurzaam') ?></a>
  <a href="/faq.php" onclick="toggleMenu()"><?= t('nav_faq') ?></a>
  <a href="/contact.php" onclick="toggleMenu()"><?= t('nav_contact') ?></a>
  <a href="/bestellen.php" class="nav-cta" onclick="toggleMenu()"><?= t('nav_mob_bestel') ?></a>
  <a href="/bestellen-oud.php" onclick="toggleMenu()" style="font-size:.82rem;color:var(--ink3);">Bestellen (oud)</a>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem;">
    <?php foreach(['nl'=>$VLAG['nl'].' NL','en'=>$VLAG['en'].' EN','de'=>$VLAG['de'].' DE','no'=>$VLAG['no'].' NO'] as $code=>$label): ?>
    <a href="?lang=<?= $code ?>" class="lang-opt <?= $TAAL===$code?'actief':'' ?>" style="border:1px solid var(--zand);border-radius:6px;text-decoration:none;"><?= $label ?></a>
    <?php endforeach; ?>
  </div>
</div>
<div style="padding-top:68px;">
