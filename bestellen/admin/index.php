<?php
// ── Auth ──────────────────────────────────────────────────────────────────────
session_start();
define('ADMIN_PW', 'Klaas#99');

if (isset($_POST['pw'])) {
    if ($_POST['pw'] === ADMIN_PW) {
        $_SESSION['mm_admin'] = true;
    } else {
        $loginFout = true;
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /bestellen/admin/');
    exit;
}
$ingelogd = !empty($_SESSION['mm_admin']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Merch Master Admin</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --accent:#e84c1e;--ink:#1a1a1a;--ink2:#3a3832;--ink3:#7a7670;
  --border:#e8e4dc;--surface:#fff;--paper:#f5f3ef;--r:10px;
  --green:#1a7a45;--red:#991b1b;
  --shadow:0 2px 12px rgba(0,0,0,.07);
}
body{font-family:'DM Sans',system-ui,sans-serif;background:var(--paper);color:var(--ink);font-size:14px;}

/* ── Login ── */
.login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;}
.login-card{background:var(--surface);border-radius:16px;padding:2.5rem;box-shadow:0 4px 32px rgba(0,0,0,.1);width:100%;max-width:380px;border:1px solid var(--border);}
.login-logo{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;margin-bottom:2rem;color:var(--ink);}
.login-logo em{color:var(--accent);font-style:normal;}
.field label{display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink3);margin-bottom:.35rem;}
.field input{width:100%;padding:.7rem .9rem;border:1.5px solid var(--border);border-radius:8px;font-size:.9rem;background:#faf8f5;transition:border-color .2s;}
.field input:focus{outline:none;border-color:var(--accent);}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.7rem 1.4rem;border-radius:50px;font-weight:700;font-size:.85rem;cursor:pointer;border:none;transition:all .2s;font-family:inherit;}
.btn-p{background:var(--accent);color:#fff;}
.btn-p:hover{background:#c73d15;}
.btn-s{background:var(--paper);color:var(--ink2);border:1.5px solid var(--border);}
.btn-s:hover{border-color:#aaa;}
.btn-danger{background:#fee2e2;color:#991b1b;border:none;}
.btn-danger:hover{background:#fecaca;}
.btn-full{width:100%;}
.btn-sm{padding:.4rem .85rem;font-size:.78rem;}
.fout{background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:.6rem .9rem;font-size:.8rem;color:#991b1b;margin-bottom:1rem;}
.ok{background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:.6rem .9rem;font-size:.8rem;color:#166534;margin-bottom:1rem;}

/* ── Layout ── */
.shell{display:grid;grid-template-columns:220px 1fr;min-height:100vh;}
.sidebar{background:#1a1a1a;color:#e8e4dc;display:flex;flex-direction:column;}
.sidebar-logo{padding:1.5rem 1.2rem;font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;border-bottom:1px solid rgba(255,255,255,.08);}
.sidebar-logo em{color:var(--accent);font-style:normal;}
.sidebar-logo small{display:block;font-size:.65rem;font-weight:400;color:#7a7670;margin-top:.2rem;font-family:inherit;}
.nav-item{display:flex;align-items:center;gap:.65rem;padding:.8rem 1.2rem;font-size:.82rem;cursor:pointer;transition:background .15s;border-left:3px solid transparent;color:#c8c4bc;}
.nav-item:hover{background:rgba(255,255,255,.05);color:#fff;}
.nav-item.actief{background:rgba(232,76,30,.12);border-left-color:var(--accent);color:#fff;font-weight:600;}
.nav-item svg{width:15px;height:15px;flex-shrink:0;opacity:.7;}
.nav-item.actief svg{opacity:1;}
.nav-sep{padding:.6rem 1.2rem .3rem;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#4a4642;}
.main{padding:2rem;overflow-x:hidden;}
.page-ttl{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;margin-bottom:1.5rem;}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;}

/* ── Secties ── */
.sectie{display:none;}
.sectie.actief{display:block;}

/* ── Kaarten ── */
.kaart{background:var(--surface);border-radius:var(--r);box-shadow:var(--shadow);padding:1.5rem;margin-bottom:1.5rem;}
.kaart-ttl{font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;margin-bottom:1.2rem;padding-bottom:.75rem;border-bottom:2px solid var(--border);}
.kaart-ttl small{font-size:.72rem;font-weight:400;color:var(--ink3);font-family:inherit;}

/* ── Stats ── */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;}
.stat{background:var(--surface);border-radius:var(--r);padding:1.2rem;box-shadow:var(--shadow);border-left:4px solid var(--accent);}
.stat-val{font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--accent);}
.stat-lbl{font-size:.72rem;color:var(--ink3);margin-top:.2rem;}

/* ── Tabellen ── */
table{width:100%;border-collapse:collapse;font-size:.82rem;}
th{text-align:left;padding:.6rem .75rem;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--ink3);border-bottom:2px solid var(--border);}
td{padding:.65rem .75rem;border-bottom:1px solid var(--border);vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:#faf8f4;}
.badge{display:inline-block;padding:2px 8px;border-radius:12px;font-size:.68rem;font-weight:700;}
.badge-groen{background:#dcfce7;color:#166534;}
.badge-blauw{background:#dbeafe;color:#1e40af;}
.badge-grijs{background:#f0ede6;color:#6b6560;}
.badge-rood{background:#fee2e2;color:#991b1b;}
.badge-oranje{background:#ffedd5;color:#9a3412;}

/* ── Forms ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem;}
.field{margin-bottom:.75rem;}
.field label{display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink3);margin-bottom:.3rem;}
.field input,.field select,.field textarea{width:100%;padding:.65rem .85rem;border:1.5px solid var(--border);border-radius:8px;font-size:.85rem;background:#faf8f5;font-family:inherit;transition:border-color .2s;}
.field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--accent);}
.field textarea{resize:vertical;min-height:80px;}
.form-divider{border:none;border-top:1px solid var(--border);margin:1rem 0;}

/* ── Prijsmatrix tabel ── */
.matrix-wrap{overflow-x:auto;}
.matrix-tbl{border-collapse:collapse;min-width:700px;}
.matrix-tbl th,.matrix-tbl td{border:1px solid var(--border);padding:.45rem .6rem;text-align:center;font-size:.78rem;}
.matrix-tbl th{background:#f5f3ef;font-weight:700;font-size:.65rem;text-transform:uppercase;letter-spacing:.06em;}
.matrix-tbl td:first-child{font-weight:600;background:#f9f7f4;text-align:left;}
.matrix-tbl input{width:70px;padding:.3rem .4rem;border:1.5px solid var(--border);border-radius:6px;font-size:.78rem;text-align:center;background:#fff;}
.matrix-tbl input:focus{outline:none;border-color:var(--accent);}

/* ── Levertijden ── */
.lt-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;}
.lt-kaart{background:var(--paper);border:1px solid var(--border);border-radius:var(--r);padding:1.25rem;}
.lt-kaart h4{font-family:'Syne',sans-serif;font-size:.9rem;font-weight:800;margin-bottom:1rem;color:var(--accent);}
.lt-rij{display:flex;align-items:center;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);font-size:.82rem;}
.lt-rij:last-child{border-bottom:none;}
.lt-rij label{color:var(--ink2);}
.lt-rij input{width:80px;padding:.35rem .5rem;border:1.5px solid var(--border);border-radius:6px;font-size:.82rem;text-align:center;font-family:inherit;}
.lt-rij input:focus{outline:none;border-color:var(--accent);}

/* ── Loading/states ── */
.spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
.loading{text-align:center;padding:2rem;color:var(--ink3);}
.leeg{text-align:center;padding:2.5rem;color:var(--ink3);font-size:.85rem;}

/* ── Modal ── */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;}
.modal{background:var(--surface);border-radius:14px;padding:2rem;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;}
.modal-ttl{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;margin-bottom:1.25rem;}
.modal-acties{display:flex;gap:.5rem;margin-top:1.25rem;justify-content:flex-end;}

/* ── Responsief ── */
@media(max-width:900px){
  .shell{grid-template-columns:1fr;}
  .sidebar{display:none;}
  .stats{grid-template-columns:1fr 1fr;}
}
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
</head>
<body>

<?php if (!$ingelogd): ?>
<!-- ── LOGIN ── -->
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">Merch<em>Master</em> <small>Admin paneel</small></div>
    <?php if (!empty($loginFout)): ?>
      <div class="fout">Ongeldig wachtwoord</div>
    <?php endif; ?>
    <form method="POST">
      <div class="field" style="margin-bottom:1rem;">
        <label>Wachtwoord</label>
        <input type="password" name="pw" autofocus>
      </div>
      <button type="submit" class="btn btn-p btn-full">Inloggen</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ── ADMIN SHELL ── -->
<div class="shell">

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-logo">Merch<em>Master</em><small>Admin v2.0</small></div>
    <div class="nav-sep">Overzicht</div>
    <div class="nav-item actief" onclick="toonSectie('dashboard')" id="nav-dashboard">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </div>
    <div class="nav-item" onclick="toonSectie('bestellingen')" id="nav-bestellingen">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      Bestellingen
    </div>
    <div class="nav-item" onclick="toonSectie('klanten')" id="nav-klanten">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      Klanten
    </div>
    <div class="nav-sep">Instellingen</div>
    <div class="nav-item" onclick="toonSectie('prijzen')" id="nav-prijzen">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
      Prijsmarges
    </div>
    <div class="nav-item" onclick="toonSectie('drukkosten')" id="nav-drukkosten">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
      Drukkosten
    </div>
    <div class="nav-item" onclick="toonSectie('levertijden')" id="nav-levertijden">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
      Levertijden
    </div>
    <div class="nav-item" onclick="toonSectie('volumekorting')" id="nav-volumekorting">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
      Volumekorting
    </div>
    <div class="nav-item" onclick="toonSectie('offertes')" id="nav-offertes">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Offertes
    </div>
    <div style="flex:1;"></div>
    <div class="nav-item" onclick="location.href='?logout'" style="margin-bottom:.5rem;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Uitloggen
    </div>
  </div>

  <!-- Main -->
  <div class="main">

    <!-- ── DASHBOARD ── -->
    <div class="sectie actief" id="sectie-dashboard">
      <div class="topbar">
        <div class="page-ttl">Dashboard</div>
        <div style="font-size:.78rem;color:var(--ink3);" id="dash-tijd">–</div>
      </div>
      <div class="stats" id="dash-stats">
        <div class="stat"><div class="stat-val" id="ds-orders">–</div><div class="stat-lbl">Bestellingen totaal</div></div>
        <div class="stat" style="border-left-color:#1a7a45;"><div class="stat-val" style="color:#1a7a45;" id="ds-klanten">–</div><div class="stat-lbl">Klanten</div></div>
        <div class="stat" style="border-left-color:#1e40af;"><div class="stat-val" style="color:#1e40af;" id="ds-omzet">–</div><div class="stat-lbl">Omzet (incl. BTW)</div></div>
        <div class="stat" style="border-left-color:#854d0e;"><div class="stat-val" style="color:#854d0e;" id="ds-producten">–</div><div class="stat-lbl">Producten catalogus</div></div>
      </div>
      <div class="kaart">
        <div class="kaart-ttl">Recente bestellingen <small>— laatste 10</small></div>
        <div id="dash-recente"><div class="loading">Laden...</div></div>
      </div>
    </div>

    <!-- ── BESTELLINGEN ── -->
    <div class="sectie" id="sectie-bestellingen">
      <div class="topbar">
        <div class="page-ttl">Bestellingen</div>
        <div style="display:flex;gap:.5rem;">
          <select id="filter-status" onchange="laadBestellingen()" style="padding:.45rem .7rem;border:1.5px solid var(--border);border-radius:8px;font-size:.8rem;background:#fff;font-family:inherit;">
            <option value="">Alle statussen</option>
            <option value="betaald">Betaald</option>
            <option value="in_behandeling">In behandeling</option>
            <option value="geleverd">Geleverd</option>
            <option value="concept">Concept</option>
            <option value="geannuleerd">Geannuleerd</option>
          </select>
        </div>
      </div>
      <div class="kaart">
        <div id="bestellingen-tbl"><div class="loading">Laden...</div></div>
      </div>
    </div>

    <!-- ── KLANTEN ── -->
    <div class="sectie" id="sectie-klanten">
      <div class="topbar">
        <div class="page-ttl">Klanten</div>
        <input type="search" id="klanten-zoek" placeholder="Zoeken op naam / e-mail..." oninput="filterKlanten()" 
               style="padding:.5rem .85rem;border:1.5px solid var(--border);border-radius:8px;font-size:.82rem;width:250px;font-family:inherit;">
      </div>
      <div class="kaart">
        <div id="klanten-tbl"><div class="loading">Laden...</div></div>
      </div>
    </div>

    <!-- ── PRIJSMARGES ── -->
    <div class="sectie" id="sectie-prijzen">
      <div class="topbar"><div class="page-ttl">Prijsmarges</div></div>
      <div class="kaart">
        <div class="kaart-ttl">Textiel prijsmarges <small>— per kwaliteitssegment</small></div>
        <div id="marges-fout" class="fout" style="display:none;"></div>
        <div id="marges-ok" class="ok" style="display:none;">Opgeslagen!</div>
        <div id="marges-form"><div class="loading">Laden...</div></div>
      </div>
    </div>

    <!-- ── DRUKKOSTEN ── -->
    <div class="sectie" id="sectie-drukkosten">
      <div class="topbar"><div class="page-ttl">Drukkosten matrix</div></div>
      <div class="kaart">
        <div class="kaart-ttl">Kosten per techniek en oplage</div>
        <p style="font-size:.82rem;color:var(--ink3);margin-bottom:1.25rem;">
          Vul per techniek de kosten in per oplage. Prijs per stuk excl. BTW. Borduren is op aanvraag — geen matrix nodig.
        </p>
        <div id="druk-fout" class="fout" style="display:none;"></div>
        <div id="druk-ok" class="ok" style="display:none;">Drukkosten opgeslagen!</div>
        <div id="druk-tabs" style="display:flex;gap:.5rem;margin-bottom:1.25rem;border-bottom:2px solid var(--border);padding-bottom:.75rem;">
          <button class="btn btn-p btn-sm" id="druk-tab-dtf" onclick="toonDrukTab('dtf')">DTF Transfer</button>
          <button class="btn btn-s btn-sm" id="druk-tab-zeef" onclick="toonDrukTab('zeef')">Zeefdruk</button>
          <button class="btn btn-s btn-sm" id="druk-tab-bord" onclick="toonDrukTab('bord')">Borduren</button>
        </div>
        <div id="druk-inhoud"><div class="loading">Laden...</div></div>
        <div style="margin-top:1.25rem;">
          <button class="btn btn-p" onclick="slaaDrukkostenOp()">Opslaan</button>
        </div>
      </div>
    </div>

    <!-- ── LEVERTIJDEN ── -->
    <div class="sectie" id="sectie-levertijden">
      <div class="topbar"><div class="page-ttl">Levertijden</div></div>
      <div class="kaart">
        <div class="kaart-ttl">Standaard levertijden per techniek <small>— in werkdagen</small></div>
        <p style="font-size:.82rem;color:var(--ink3);margin-bottom:1.5rem;">
          Stel de minimum en maximum levertijd in per bedrukkingtechniek. Deze worden getoond op de website en in de besteltool.
        </p>
        <div id="lt-fout" class="fout" style="display:none;"></div>
        <div id="lt-ok" class="ok" style="display:none;">Levertijden opgeslagen!</div>
        <div class="lt-grid" id="lt-grid">
          <!-- DTF -->
          <div class="lt-kaart">
            <h4>DTF Transferdruk</h4>
            <div class="lt-rij">
              <label>Minimum (werkdagen)</label>
              <input type="number" id="lt-dtf-min" min="1" max="30" value="5">
            </div>
            <div class="lt-rij">
              <label>Maximum (werkdagen)</label>
              <input type="number" id="lt-dtf-max" min="1" max="30" value="8">
            </div>
            <div class="lt-rij" style="margin-top:.75rem;border-bottom:none;">
              <label style="color:var(--ink3);font-size:.75rem;">Weergave op site</label>
              <span id="lt-dtf-preview" style="font-weight:700;color:var(--accent);font-size:.9rem;">5–8 werkdagen</span>
            </div>
          </div>
          <!-- Zeefdruk -->
          <div class="lt-kaart">
            <h4>Zeefdruk</h4>
            <div class="lt-rij">
              <label>Minimum (werkdagen)</label>
              <input type="number" id="lt-zeef-min" min="1" max="30" value="6">
            </div>
            <div class="lt-rij">
              <label>Maximum (werkdagen)</label>
              <input type="number" id="lt-zeef-max" min="1" max="30" value="10">
            </div>
            <div class="lt-rij" style="margin-top:.75rem;border-bottom:none;">
              <label style="color:var(--ink3);font-size:.75rem;">Weergave op site</label>
              <span id="lt-zeef-preview" style="font-weight:700;color:var(--accent);font-size:.9rem;">6–10 werkdagen</span>
            </div>
          </div>
          <!-- Borduren -->
          <div class="lt-kaart">
            <h4>Borduren</h4>
            <div class="lt-rij">
              <label>Minimum (werkdagen)</label>
              <input type="number" id="lt-bord-min" min="1" max="30" value="7">
            </div>
            <div class="lt-rij">
              <label>Maximum (werkdagen)</label>
              <input type="number" id="lt-bord-max" min="1" max="30" value="12">
            </div>
            <div class="lt-rij" style="margin-top:.75rem;border-bottom:none;">
              <label style="color:var(--ink3);font-size:.75rem;">Weergave op site</label>
              <span id="lt-bord-preview" style="font-weight:700;color:var(--accent);font-size:.9rem;">7–12 werkdagen</span>
            </div>
          </div>
        </div>
        <div style="margin-top:1.5rem;display:flex;gap:.75rem;align-items:center;">
          <button class="btn btn-p" onclick="slaaLevertijdenOp()">Levertijden opslaan</button>
          <span id="lt-spinner" style="display:none;font-size:.8rem;color:var(--ink3);">Bezig...</span>
        </div>
      </div>
    </div>

    <!-- ── VOLUMEKORTING ── -->
    <div class="sectie" id="sectie-volumekorting">
      <div class="topbar"><div class="page-ttl">Volumekorting</div></div>
      <div class="kaart">
        <div class="kaart-ttl">Kortingsstaffels <small>— op textiel + drukkosten samen</small></div>
        <p style="font-size:.82rem;color:var(--ink3);margin-bottom:1.5rem;">
          Vul het minimale aantal stuks in en het kortingspercentage. De hoogste toepasselijke staffel wint.
          Korting geldt op het totaal van textiel + drukkosten, vóór BTW en vóór eventuele spoedtoeslag.
        </p>
        <div id="vk-fout" class="fout" style="display:none;"></div>
        <div id="vk-ok" class="ok" style="display:none;">Staffels opgeslagen!</div>
        <div id="vk-staffels" style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:1rem;max-width:420px;">
          <div class="loading">Laden...</div>
        </div>
        <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;">
          <button class="btn btn-s btn-sm" onclick="voegStaffelToe()">+ Staffel toevoegen</button>
          <button class="btn btn-p" onclick="slaaVolumekorting()">Opslaan</button>
        </div>
        <div style="background:rgba(196,98,45,.06);border-left:3px solid var(--accent);padding:.85rem 1rem;border-radius:0 8px 8px 0;font-size:.78rem;color:var(--ink2);line-height:1.7;">
          <strong>Voorbeeld:</strong> staffel 50 stuks = 5% betekent: bij 50+ stuks krijgt de klant 5% korting op het totaalbedrag (textiel + druk). Spoedtoeslag wordt daarna berekend over het bedrag na korting.
        </div>
      </div>
    </div>

    <!-- ── OFFERTES ── -->
    <div class="sectie" id="sectie-offertes">
      <div class="topbar"><div class="page-ttl">Offertes</div></div>
      <div class="kaart">
        <div class="kaart-ttl">Uitgestuurde offertes</div>
        <div id="offertes-tbl"><div class="loading">Laden...</div></div>
      </div>
    </div>

    <!-- ── CATALOGUS ── -->
    <div class="sectie" id="sectie-catalogus">
      <div class="topbar">
        <div class="page-ttl">Catalogus</div>
        <div style="display:flex;gap:.5rem;align-items:center;">
          <input type="search" id="cat-zoek" placeholder="Product zoeken..." oninput="filterCatalogus()"
                 style="padding:.5rem .85rem;border:1.5px solid var(--border);border-radius:8px;font-size:.82rem;width:220px;font-family:inherit;">
          <span id="cat-count" style="font-size:.78rem;color:var(--ink3);white-space:nowrap;"></span>
        </div>
      </div>
      <div class="kaart">
        <div id="cat-tbl"><div class="loading">Laden...</div></div>
      </div>
    </div>

  </div><!-- /main -->
</div><!-- /shell -->

<!-- ── Offerte status modal ── -->
<div id="modal-offerte" class="modal-backdrop" style="display:none;">
  <div class="modal" style="max-width:400px;">
    <div class="modal-ttl">Offerte <span id="modal-offerte-nr"></span></div>
    <div id="modal-offerte-fout" class="fout" style="display:none;"></div>
    <div class="field">
      <label>Status</label>
      <select id="modal-offerte-status" style="width:100%;padding:.65rem .85rem;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;">
        <option value="concept">Concept</option>
        <option value="verzonden">Verzonden naar klant</option>
        <option value="geaccepteerd">Geaccepteerd door klant</option>
        <option value="betaald">Betaald</option>
        <option value="vervallen">Vervallen</option>
      </select>
    </div>
    <div style="background:rgba(196,98,45,.06);border-left:3px solid var(--accent);padding:.75rem 1rem;border-radius:0 8px 8px 0;font-size:.78rem;color:var(--ink2);margin-bottom:1rem;line-height:1.7;">
      <strong>Betaald</strong> zet de offerte op betaald. Dit is handmatig — gebruik dit na ontvangst van een overboeking of na bevestiging van een spoedorder.
    </div>
    <div class="modal-acties">
      <button class="btn btn-p btn-sm" onclick="slaOfferteStatusOp()">Opslaan</button>
      <button class="btn btn-s btn-sm" onclick="sluitModal('modal-offerte')">Sluiten</button>
    </div>
  </div>
</div>

<!-- ── Bestelling detail modal ── -->
<div id="modal-bestelling" class="modal-backdrop" style="display:none;">
  <div class="modal" style="max-width:640px;">
    <div class="modal-ttl" id="modal-best-ttl">Bestelling</div>
    <div id="modal-best-inhoud"></div>
    <div class="modal-acties">
      <select id="modal-status-sel" style="padding:.5rem .75rem;border:1.5px solid var(--border);border-radius:8px;font-size:.82rem;font-family:inherit;">
        <option value="betaald">Betaald</option>
        <option value="in_behandeling">In behandeling</option>
        <option value="geleverd">Geleverd</option>
        <option value="geannuleerd">Geannuleerd</option>
      </select>
      <button class="btn btn-p btn-sm" onclick="slaStatusOp()">Status opslaan</button>
      <button class="btn btn-s btn-sm" onclick="sluitModal('modal-bestelling')">Sluiten</button>
    </div>
  </div>
</div>

<script>
// ── State ──────────────────────────────────────────────────────────────────────
const API = 'handler.php';  // bestellen/admin/handler.php
let allKlanten = [];
let allCatalogus = [];
let huidigBestId = null;
let drukkostenData = {};
let huidigDrukTab = 'dtf';

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('dash-tijd').textContent = new Date().toLocaleDateString('nl-NL', {weekday:'long',day:'numeric',month:'long',year:'numeric'});
  laadDashboard();
  // Live preview levertijden
  ['dtf','zeef','bord'].forEach(t => {
    ['min','max'].forEach(m => {
      const el = document.getElementById(`lt-${t}-${m}`);
      if (el) el.addEventListener('input', () => updateLtPreview(t));
    });
  });
});

// ── Navigatie ──────────────────────────────────────────────────────────────────
function toonSectie(naam) {
  document.querySelectorAll('.sectie').forEach(s => s.classList.remove('actief'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('actief'));
  const sectie = document.getElementById('sectie-' + naam);
  const nav    = document.getElementById('nav-' + naam);
  if (!sectie) { console.error('Sectie niet gevonden:', naam); return; }
  sectie.classList.add('actief');
  if (nav) nav.classList.add('actief');

  // Lazy load
  if (naam === 'bestellingen') laadBestellingen();
  if (naam === 'klanten')      laadKlanten();
  if (naam === 'prijzen')      laadMarges();
  if (naam === 'drukkosten')   laadDrukkosten();
  if (naam === 'levertijden')  laadLevertijden();
  if (naam === 'volumekorting') laadVolumekorting();
  if (naam === 'offertes')     laadOffertes();
  if (naam === 'catalogus')    laadCatalogus();
}

// ── Dashboard ─────────────────────────────────────────────────────────────────
async function laadDashboard() {
  const r = await api('admin-stats');
  if (r.ok) {
    document.getElementById('ds-orders').textContent    = r.stats.orders ?? '–';
    document.getElementById('ds-klanten').textContent   = r.stats.klanten ?? '–';
    document.getElementById('ds-omzet').textContent     = '€ ' + num(r.stats.omzet ?? 0);
    document.getElementById('ds-producten').textContent = r.stats.producten ?? '–';
  }
  const rb = await api('admin-bestellingen', {limit: 10});
  const el = document.getElementById('dash-recente');
  if (!rb.ok || !rb.bestellingen?.length) {
    el.innerHTML = '<div class="leeg">Nog geen bestellingen.</div>';
    return;
  }
  el.innerHTML = `<table>
    <thead><tr><th>#</th><th>Klant</th><th>Datum</th><th>Bedrag</th><th>Status</th><th></th></tr></thead>
    <tbody>${rb.bestellingen.map(b => `
      <tr>
        <td><strong>${b.order_id}</strong></td>
        <td>${esc(b.klant_naam || b.klant_email || '–')}</td>
        <td>${datumKort(b.aangemaakt)}</td>
        <td>€ ${num(b.totaal_incl)}</td>
        <td><span class="badge ${statusKleur(b.status)}">${statusLabel(b.status)}</span></td>
        <td><button class="btn btn-s btn-sm" onclick="openBestelling(${b.id})">Detail</button></td>
      </tr>`).join('')}
    </tbody></table>`;
}

// ── Bestellingen ──────────────────────────────────────────────────────────────
async function laadBestellingen() {
  const status = document.getElementById('filter-status')?.value || '';
  const r = await api('admin-bestellingen', {status, limit: 100});
  const el = document.getElementById('bestellingen-tbl');
  if (!r.ok || !r.bestellingen?.length) {
    el.innerHTML = '<div class="leeg">Geen bestellingen gevonden.</div>';
    return;
  }
  el.innerHTML = `<table>
    <thead><tr><th>#Order</th><th>Klant</th><th>E-mail</th><th>Datum</th><th>Bedrag</th><th>Winst</th><th>Status</th><th></th></tr></thead>
    <tbody>${r.bestellingen.map(b => {
      const winst = b.winst_excl != null
        ? `<span style="color:#166534;font-weight:600;">€ ${num(b.winst_excl)}</span>`
        : '<span style="color:var(--ink3);">–</span>';
      return `
      <tr>
        <td><strong>${b.order_id}</strong></td>
        <td>${esc(b.klant_naam || '–')}</td>
        <td>${esc(b.klant_email || '–')}</td>
        <td>${datumKort(b.aangemaakt)}</td>
        <td>€ ${num(b.totaal_incl)}</td>
        <td>${winst}</td>
        <td><span class="badge ${statusKleur(b.status)}">${statusLabel(b.status)}</span></td>
        <td><button class="btn btn-s btn-sm" onclick="openBestelling(${b.id})">Detail</button></td>
      </tr>`;
    }).join('')}
    </tbody></table>`;
}

async function openBestelling(id) {
  huidigBestId = id;
  const r = await api('admin-bestelling-detail', {id});
  if (!r.ok) return;
  const b = r.bestelling;
  document.getElementById('modal-best-ttl').textContent = 'Bestelling ' + b.order_id;
  document.getElementById('modal-status-sel').value = b.status;
  document.getElementById('modal-best-inhoud').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem;font-size:.82rem;">
      <div><strong>Klant</strong><br>${esc(b.klant_naam||'–')}<br>${esc(b.klant_email||'–')}<br>${esc(b.klant_tel||'')}</div>
      <div><strong>Bedrijf</strong><br>${esc(b.klant_bedrijf||'–')}<br>${esc(b.klant_kvk ? 'KVK: '+b.klant_kvk : '')}</div>
    </div>
    <div style="margin-bottom:1.25rem;font-size:.82rem;">
      <strong>Afleveradres</strong><br>${esc([b.klant_straat,b.klant_postcode,b.klant_stad,b.klant_land].filter(Boolean).join(', '))}
    </div>
    <table style="margin-bottom:1rem;">
      <thead><tr><th>Product</th><th>Kleur</th><th>Positie</th><th>Techniek</th><th>Stuks</th><th>Subtotaal</th></tr></thead>
      <tbody>${(b.regels||[]).map(r => `
        <tr>
          <td>${esc(r.textiel_merk||'')} ${esc(r.textiel_naam||'')}</td>
          <td>${esc(r.kleur_naam||'–')}</td>
          <td>${esc(r.positie||'–')}</td>
          <td>${esc(r.techniek_a||'–')}${r.is_dual?' + '+esc(r.techniek_b||''):''}</td>
          <td>${r.aantal}</td>
          <td>€ ${num(r.subtotaal)}</td>
        </tr>`).join('')}
      </tbody>
    </table>
    <div style="text-align:right;font-size:.85rem;">
      <div>Subtotaal excl. BTW: € ${num(b.totaal_excl)}</div>
      <div>BTW (21%): € ${num((b.totaal_incl||0)-(b.totaal_excl||0))}</div>
      <div style="font-weight:700;font-size:1rem;margin-top:.25rem;">Totaal incl. BTW: € ${num(b.totaal_incl)}</div>
    </div>`;
  document.getElementById('modal-bestelling').style.display = 'flex';
}

async function slaStatusOp() {
  if (!huidigBestId) return;
  const status = document.getElementById('modal-status-sel').value;
  const r = await api('admin-status-update', {id: huidigBestId, status});
  if (r.ok) { sluitModal('modal-bestelling'); laadBestellingen(); laadDashboard(); }
}

// ── Klanten ───────────────────────────────────────────────────────────────────
async function laadKlanten() {
  const r = await api('admin-klanten');
  allKlanten = r.klanten || [];
  renderKlanten(allKlanten);
}

function filterKlanten() {
  const q = document.getElementById('klanten-zoek')?.value.toLowerCase() || '';
  renderKlanten(q ? allKlanten.filter(k =>
    (k.voornaam+' '+k.achternaam+' '+k.email).toLowerCase().includes(q)
  ) : allKlanten);
}

function renderKlanten(lijst) {
  const el = document.getElementById('klanten-tbl');
  if (!lijst.length) { el.innerHTML = '<div class="leeg">Geen klanten.</div>'; return; }
  el.innerHTML = `<table>
    <thead><tr><th>Naam</th><th>E-mail</th><th>Telefoon</th><th>Bedrijf</th><th>Stad</th><th>Aangemeld</th><th>Orders</th></tr></thead>
    <tbody>${lijst.map(k => `
      <tr>
        <td>${esc(k.voornaam+' '+k.achternaam)}</td>
        <td>${esc(k.email)}</td>
        <td>${esc(k.telefoon||'–')}</td>
        <td>${esc(k.bedrijf||'–')}</td>
        <td>${esc(k.stad||'–')}</td>
        <td>${datumKort(k.aangemaakt)}</td>
        <td>${k.order_count||0}</td>
      </tr>`).join('')}
    </tbody></table>`;
}

// ── Prijsmarges ───────────────────────────────────────────────────────────────
const TEXTIEL_TIERS = {
  budget:    { label: 'Budget',    kleur: '#6b7280', omschrijving: 'Gildan, B&C basics — laagste inkoopprijs' },
  standaard: { label: 'Standaard', kleur: '#e84c1e', omschrijving: 'Build Your Brand, Asquith — middensegment' },
  premium:   { label: 'Premium',   kleur: '#854d0e', omschrijving: 'Flexfit, Anthem — hogere inkoopprijs' },
};

async function laadMarges() {
  const r = await api('admin-marges');
  const el = document.getElementById('marges-form');
  const m = r.marges || {};
  const tDef = { budget: 1.55, standaard: 1.45, premium: 1.35 };

  el.innerHTML = `
    <p style="font-size:.82rem;color:var(--ink3);margin-bottom:1.5rem;">
      Verkoopprijs textiel = inkoopprijs &times; marge. Hogere inkoopprijs = lagere marge nodig om concurrerend te blijven.
    </p>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;">
      ${Object.entries(TEXTIEL_TIERS).map(([k, t]) => {
        const val = m['textiel_'+k] ?? tDef[k];
        return `
        <div style="background:var(--paper);border:2px solid ${t.kleur}22;border-radius:10px;padding:1.25rem;">
          <div style="display:inline-block;background:${t.kleur};color:#fff;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:.25rem .6rem;border-radius:4px;margin-bottom:.75rem;">${t.label}</div>
          <div style="font-size:.75rem;color:var(--ink3);margin-bottom:1rem;line-height:1.5;">${t.omschrijving}</div>
          <div class="field" style="margin-bottom:.5rem;">
            <label>Marge (× inkoopprijs)</label>
            <input type="number" step="0.01" min="1.0" max="5.0"
                   id="marge-textiel_${k}" value="${val}"
                   oninput="updateMargePrev('${k}', this.value)"
                   style="font-size:1.1rem;font-weight:700;color:${t.kleur};">
          </div>
          <div style="font-size:.75rem;color:var(--ink3);">
            Voorbeeld: inkoop €10 &rarr;
            <strong id="prev-${k}" style="color:${t.kleur};">€ ${(10*val).toFixed(2).replace('.',',')}</strong>
          </div>
        </div>`;
      }).join('')}
    </div>
    <div style="background:rgba(196,98,45,.06);border-left:3px solid var(--accent);padding:.85rem 1rem;border-radius:0 8px 8px 0;font-size:.78rem;color:var(--ink2);margin-bottom:1.5rem;line-height:1.7;">
      <strong>Tier-indeling op inkoopprijs:</strong>
      Budget &lt; €4,00 &nbsp;|&nbsp; Standaard €4,00–€10,00 &nbsp;|&nbsp; Premium &gt; €10,00
    </div>
    <button class="btn btn-p" onclick="slaaMargesOp()">Marges opslaan</button>`;
}

function updateMargePrev(tier, val) {
  const v = parseFloat(val) || 1.0;
  const el = document.getElementById('prev-' + tier);
  if (el) el.textContent = '€ ' + (10 * v).toFixed(2).replace('.', ',');
}

function updateDrukPrev(tech, val, kleur) {
  const v = parseFloat(val) || 1.0;
  const el = document.getElementById('drukprev-' + tech);
  if (el) el.textContent = '€ ' + (7 * v).toFixed(2).replace('.', ',');
}

async function slaaMargesOp() {
  document.getElementById('marges-fout').style.display = 'none';
  document.getElementById('marges-ok').style.display = 'none';
  const marges = {};
  ['budget','standaard','premium'].forEach(k => {
    const el = document.getElementById('marge-textiel_' + k);
    if (el) marges['textiel_' + k] = parseFloat(el.value) || 1.0;
  });
  const r = await api('admin-marges-opslaan', {marges});
  if (r.ok) {
    document.getElementById('marges-ok').style.display = 'block';
    setTimeout(() => document.getElementById('marges-ok').style.display = 'none', 3000);
  } else {
    document.getElementById('marges-fout').textContent = r.fout || 'Fout bij opslaan';
    document.getElementById('marges-fout').style.display = 'block';
  }
}

// ── Drukkosten ────────────────────────────────────────────────────────────────
async function laadDrukkosten() {
  const r = await api('admin-drukkosten');
  drukkostenData = r.drukkosten || getDefaultDrukkosten();
  renderDrukTab(huidigDrukTab);
}

function getDefaultDrukkosten() {
  // Zeefdruk prijzen uit prijslijst + €0,05 opslag per stuk
  // Rijen = oplageklasse, kolommen = aantal kleuren (1-4)
  // Onder 25 stuks: niet mogelijk via zeefdruk (minimum oplage)
  const zeefMatrix = {
    // [kleuren]: { [oplage]: prijs }
    1: { 25:4.41, 50:2.83, 100:1.77, 250:1.27, 500:0.96, 1000:0.77, 2500:0.68, 5000:0.58, 10000:0.50 },
    2: { 25:7.25, 50:4.41, 100:2.62, 250:1.76, 500:1.27, 1000:1.02, 2500:0.83, 5000:0.66, 10000:0.61 },
    3: { 25:9.35, 50:5.62, 100:3.37, 250:2.11, 500:1.58, 1000:1.16, 2500:0.96, 5000:0.79, 10000:0.70 },
    4: { 25:11.57, 50:7.00, 100:4.24, 250:2.63, 500:1.89, 1000:1.43, 2500:1.17, 5000:0.91, 10000:0.82 },
  };

  return {
    dtf: {
      oplagen: ['1-9', '10-50', '50+'],
      matrix: { '1-9': 9.00, '10-50': 7.00, '50+': 6.00 }
    },
    zeef: {
      oplagen: [25, 50, 100, 250, 500, 1000, 2500, 5000, 10000],
      kleuren: [1, 2, 3, 4],
      setup:   {},
      matrix:  zeefMatrix
    }
    // borduren: op aanvraag, geen matrix
  };
}

function toonDrukTab(tab) {
  huidigDrukTab = tab;
  ['dtf','zeef','bord'].forEach(t => {
    const btn = document.getElementById('druk-tab-'+t);
    if (btn) btn.className = t === tab ? 'btn btn-p btn-sm' : 'btn btn-s btn-sm';
  });
  renderDrukTab(tab);
}

function renderDrukTab(tab) {
  const el = document.getElementById('druk-inhoud');
  const d = drukkostenData;

  // ── DTF — 3 oplage-bandjes, prijs per stuk (geen kleur-opsplitsing) ──────
  if (tab === 'dtf') {
    const cfg    = d.dtf || {};
    const oplagen = ['1-9', '10-50', '50+'];
    const matrix  = cfg.matrix || {};

    let html = `
      <p style="font-size:.8rem;color:var(--ink2);margin-bottom:1rem;line-height:1.6;">
        DTF-prijs is per stuk, ongeacht het aantal kleuren in het ontwerp.<br>
        Vul de prijs per stuk in voor elk oplageklasse (excl. BTW).
      </p>
      <div class="matrix-wrap">
        <table class="matrix-tbl">
          <thead><tr>
            <th style="min-width:120px;">Oplage</th>
            <th>Prijs per stuk (€)</th>
            <th style="color:var(--ink3);font-weight:400;">Voorbeeld bij 10 st.</th>
          </tr></thead>
          <tbody>`;

    const labels = { '1-9': '1 – 9 stuks', '10-50': '10 – 50 stuks', '50+': '50+ stuks' };
    oplagen.forEach(o => {
      const v = matrix[o] ?? '';
      html += `<tr>
        <td style="font-weight:600;">${labels[o]}</td>
        <td><input type="number" step="0.01" min="0" data-oplage="${o}"
                   value="${v}" placeholder="0.00"
                   oninput="updateDtfVoorbeeld('${o}', this.value)"
                   style="width:100px;"></td>
        <td style="color:var(--ink3);" id="dtf-vb-${o}">
          ${v ? '€ ' + (parseFloat(v) * 10).toFixed(2).replace('.',',') : '–'}
        </td>
      </tr>`;
    });

    html += `</tbody></table></div>
      <p style="font-size:.72rem;color:var(--ink3);margin-top:.75rem;">
        Prijs per stuk excl. BTW. De klantprijs wordt berekend als: (textiel × marge) + (druk × marge).
      </p>`;
    el.innerHTML = html;

  // ── Zeefdruk — per kleur × oplage ────────────────────────────────────────
  } else if (tab === 'zeef') {
    const cfg     = d.zeef || {};
    // Altijd de volledige oplagenlijst tonen — ongeacht wat in DB staat
    const oplagen = [25, 50, 100, 250, 500, 1000, 2500, 5000, 10000];
    const kleuren = [1, 2, 3, 4];
    const matrix  = cfg.matrix  || {};

    const oplLabels = {
      25:    '25 – 49',
      50:    '50 – 99',
      100:   '100 – 249',
      250:   '250 – 499',
      500:   '500 – 999',
      1000:  '1.000 – 2.499',
      2500:  '2.500 – 4.999',
      5000:  '5.000 – 9.999',
      10000: '10.000+',
    };

    let html = `
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
        <div style="background:#fff9f7;border:1.5px solid rgba(196,98,45,.2);border-radius:8px;padding:1rem;">
          <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--accent);margin-bottom:.4rem;">Onder 25 stuks</div>
          <div style="font-size:.82rem;color:var(--ink2);line-height:1.6;">
            Zeefdruk heeft een minimale oplage van <strong>25 stuks</strong>. Voor kleinere aantallen verwijzen we klanten automatisch naar <strong>DTF transferdruk</strong> — die is beschikbaar vanaf 1 stuk en heeft geen minimale oplage.
          </div>
        </div>
        <div style="background:#f0fdf4;border:1.5px solid rgba(26,122,69,.2);border-radius:8px;padding:1rem;">
          <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#166534;margin-bottom:.4rem;">Setup kosten</div>
          <div style="font-size:.82rem;color:var(--ink2);line-height:1.6;">
            Setup kosten zijn al <strong>verwerkt in de prijzen per stuk</strong>. Er zijn geen aparte setup kosten van toepassing.
          </div>
        </div>
      </div>
      <div class="matrix-wrap"><table class="matrix-tbl"><thead><tr>
        <th style="min-width:110px;">Stuks</th>`;
    kleuren.forEach(k => html += `<th>${k} kleur${k>1?'en':''}</th>`);
    html += '</tr></thead><tbody>';

    oplagen.forEach(o => {
      html += `<tr><td style="font-weight:600;">${oplLabels[o] || o+' st.'}</td>`;
      kleuren.forEach(k => {
        const v = matrix[k]?.[o] ?? '';
        html += `<td><input type="number" step="0.01" min="0" data-kleur="${k}" data-oplage="${o}" value="${v}" placeholder="0.00"></td>`;
      });
      html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += `<p style="font-size:.72rem;color:var(--ink3);margin-top:.75rem;">Prijs per stuk excl. BTW, inclusief €0,05 opslag t.o.v. inkoopprijs. Setup is verwerkt in de prijs.</p>`;
    el.innerHTML = html;

  // ── Borduren — op aanvraag ────────────────────────────────────────────────
  } else if (tab === 'bord') {
    el.innerHTML = `
      <div style="display:flex;align-items:flex-start;gap:1.25rem;padding:1.5rem;background:var(--paper);border:2px dashed var(--border);border-radius:10px;max-width:560px;">
        <div style="flex-shrink:0;width:40px;height:40px;background:rgba(196,98,45,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
          </svg>
        </div>
        <div>
          <div style="font-weight:700;font-size:.9rem;margin-bottom:.4rem;">Borduren is op aanvraag</div>
          <p style="font-size:.82rem;color:var(--ink2);line-height:1.7;margin-bottom:.75rem;">
            Borduurprijzen worden niet automatisch berekend. Klanten die borduren willen aanvragen
            worden doorgestuurd naar het contactformulier of WhatsApp.
          </p>
          <p style="font-size:.78rem;color:var(--ink3);">
            Geen matrix nodig — geen actie vereist.
          </p>
        </div>
      </div>`;
  }
}

function updateDtfVoorbeeld(oplage, val) {
  const el = document.getElementById('dtf-vb-' + oplage);
  if (!el) return;
  const v = parseFloat(val);
  el.textContent = isNaN(v) || v === 0 ? '–' : '€ ' + (v * 10).toFixed(2).replace('.', ',');
}

async function slaaDrukkostenOp() {
  document.getElementById('druk-fout').style.display = 'none';
  document.getElementById('druk-ok').style.display = 'none';

  // Lees matrix uit actieve tab
  const el = document.getElementById('druk-inhoud');
  const tab = huidigDrukTab;

  if (tab === 'dtf') {
    // DTF: simpele matrix [oplage] = prijs per stuk
    const matrix = {};
    el.querySelectorAll('input[data-oplage]').forEach(inp => {
      matrix[inp.dataset.oplage] = parseFloat(inp.value) || 0;
    });
    if (!drukkostenData.dtf) drukkostenData.dtf = {};
    drukkostenData.dtf.oplagen = ['1-9', '10-50', '50+'];
    drukkostenData.dtf.matrix  = matrix;

  } else if (tab === 'zeef') {
    const matrix = {};
    el.querySelectorAll('input[data-kleur]').forEach(inp => {
      const k = inp.dataset.kleur;
      const o = inp.dataset.oplage;
      if (!matrix[k]) matrix[k] = {};
      matrix[k][o] = parseFloat(inp.value) || 0;
    });
    if (!drukkostenData.zeef) drukkostenData.zeef = {};
    drukkostenData.zeef.matrix = matrix;
    // setup zit verwerkt in prijzen — niet opslaan

  } else if (tab === 'bord') {
    // Borduren op aanvraag — niets op te slaan
    document.getElementById('druk-ok').textContent = 'Borduren is op aanvraag — geen data om op te slaan.';
    document.getElementById('druk-ok').style.display = 'block';
    setTimeout(() => { document.getElementById('druk-ok').style.display = 'none'; document.getElementById('druk-ok').textContent = 'Drukkosten opgeslagen!'; }, 3000);
    return;
  }

  const r = await api('admin-drukkosten-opslaan', {drukkosten: drukkostenData});
  if (r.ok) {
    document.getElementById('druk-ok').style.display = 'block';
    setTimeout(() => { document.getElementById('druk-ok').style.display = 'none'; }, 3000);
  } else {
    document.getElementById('druk-fout').textContent = r.fout || 'Fout bij opslaan';
    document.getElementById('druk-fout').style.display = 'block';
  }
}

// ── Levertijden ───────────────────────────────────────────────────────────────
async function laadLevertijden() {
  document.getElementById('lt-fout').style.display = 'none';
  document.getElementById('lt-ok').style.display = 'none';
  const r = await api('admin-levertijden');
  if (r.ok && r.levertijden) {
    const lt = r.levertijden;
    if (lt.dtf)  { setLt('dtf',  lt.dtf.min,  lt.dtf.max);  }
    if (lt.zeef) { setLt('zeef', lt.zeef.min, lt.zeef.max); }
    if (lt.bord) { setLt('bord', lt.bord.min, lt.bord.max); }
  }
  // Altijd previews updaten (ook met defaults)
  ['dtf','zeef','bord'].forEach(t => updateLtPreview(t));
}

function setLt(tech, min, max) {
  const minEl = document.getElementById('lt-'+tech+'-min');
  const maxEl = document.getElementById('lt-'+tech+'-max');
  if (minEl) minEl.value = min || minEl.value;
  if (maxEl) maxEl.value = max || maxEl.value;
}

function updateLtPreview(tech) {
  const minEl = document.getElementById('lt-'+tech+'-min');
  const maxEl = document.getElementById('lt-'+tech+'-max');
  const prev  = document.getElementById('lt-'+tech+'-preview');
  if (!minEl || !maxEl || !prev) return;
  prev.textContent = `${minEl.value}–${maxEl.value} werkdagen`;
}

async function slaaLevertijdenOp() {
  document.getElementById('lt-fout').style.display = 'none';
  document.getElementById('lt-ok').style.display = 'none';
  document.getElementById('lt-spinner').style.display = 'inline';

  const levertijden = {};
  ['dtf','zeef','bord'].forEach(t => {
    const minEl = document.getElementById('lt-'+t+'-min');
    const maxEl = document.getElementById('lt-'+t+'-max');
    if (minEl && maxEl) {
      levertijden[t] = {
        min: parseInt(minEl.value) || 5,
        max: parseInt(maxEl.value) || 8
      };
    }
  });

  const r = await api('admin-levertijden-opslaan', {levertijden});
  document.getElementById('lt-spinner').style.display = 'none';

  if (r.ok) {
    document.getElementById('lt-ok').style.display = 'block';
    setTimeout(() => { document.getElementById('lt-ok').style.display = 'none'; }, 3000);
  } else {
    const el = document.getElementById('lt-fout');
    el.textContent = r.fout || 'Fout bij opslaan. Controleer de databaseverbinding.';
    el.style.display = 'block';
  }
}

// ── Volumekorting ─────────────────────────────────────────────────────────────
let vkStaffels = [];

async function laadVolumekorting() {
  const r = await api('admin-volumekorting');
  vkStaffels = r.staffels || [{min:50,pct:5},{min:100,pct:8},{min:250,pct:12}];
  renderVkStaffels();
}

function renderVkStaffels() {
  const el = document.getElementById('vk-staffels');
  if (!vkStaffels.length) {
    el.innerHTML = '<div style="font-size:.82rem;color:var(--ink3);">Geen staffels ingesteld — alle orders zonder volumekorting.</div>';
    return;
  }
  el.innerHTML = vkStaffels.map((s, i) => `
    <div style="display:flex;align-items:center;gap:.75rem;background:var(--paper);border:1px solid var(--border);border-radius:8px;padding:.75rem 1rem;">
      <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink3);width:60px;">Vanaf</div>
      <input type="number" min="1" max="100000" value="${s.min}" data-idx="${i}" data-veld="min"
             oninput="updateStaffel(${i},'min',this.value)"
             style="width:80px;padding:.4rem .6rem;border:1.5px solid var(--border);border-radius:6px;font-size:.85rem;font-family:inherit;">
      <div style="font-size:.82rem;color:var(--ink2);">stuks</div>
      <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink3);margin-left:.5rem;">Korting</div>
      <input type="number" min="0" max="50" step="0.5" value="${s.pct}" data-idx="${i}" data-veld="pct"
             oninput="updateStaffel(${i},'pct',this.value)"
             style="width:70px;padding:.4rem .6rem;border:1.5px solid var(--border);border-radius:6px;font-size:.85rem;font-family:inherit;">
      <div style="font-size:.82rem;color:var(--ink2);">%</div>
      <div style="margin-left:auto;">
        <button class="btn btn-danger btn-sm" onclick="verwijderStaffel(${i})">Verwijder</button>
      </div>
    </div>`).join('');
}

function updateStaffel(idx, veld, val) {
  if (!vkStaffels[idx]) return;
  vkStaffels[idx][veld] = parseFloat(val) || 0;
}

function voegStaffelToe() {
  const huidigMax = vkStaffels.length ? Math.max(...vkStaffels.map(s => s.min)) : 0;
  vkStaffels.push({min: huidigMax + 50, pct: 5});
  renderVkStaffels();
}

function verwijderStaffel(idx) {
  vkStaffels.splice(idx, 1);
  renderVkStaffels();
}

async function slaaVolumekorting() {
  document.getElementById('vk-fout').style.display = 'none';
  document.getElementById('vk-ok').style.display   = 'none';
  // Valideer
  for (const s of vkStaffels) {
    if (s.min < 1 || s.pct < 0 || s.pct > 50) {
      document.getElementById('vk-fout').textContent = 'Ongeldige waarde — min. 1 stuk, korting 0–50%';
      document.getElementById('vk-fout').style.display = 'block';
      return;
    }
  }
  const r = await api('admin-volumekorting-opslaan', {staffels: vkStaffels});
  if (r.ok) {
    document.getElementById('vk-ok').style.display = 'block';
    setTimeout(() => document.getElementById('vk-ok').style.display = 'none', 3000);
  } else {
    document.getElementById('vk-fout').textContent = r.fout || 'Fout bij opslaan';
    document.getElementById('vk-fout').style.display = 'block';
  }
}

// ── Offertes ──────────────────────────────────────────────────────────────────
async function laadOffertes() {
  const r = await api('admin-offertes');
  const el = document.getElementById('offertes-tbl');
  if (!r.ok || !r.offertes?.length) {
    el.innerHTML = '<div class="leeg">Nog geen offertes aangemaakt.</div>';
    return;
  }
  el.innerHTML = `<table>
    <thead><tr>
      <th>Nr</th><th>Klant</th><th>Datum</th><th>Geldig t/m</th>
      <th>Totaal</th><th>Winst</th><th>Spoed</th><th>Status</th><th>Acties</th>
    </tr></thead>
    <tbody>${r.offertes.map(o => {
      const nr     = 'MM-' + String(o.id).padStart(5,'0');
      const totaal = o.spoed
        ? (parseFloat(o.totaal_incl||0) + parseFloat(o.spoed_toeslag||0)).toFixed(2).replace('.',',')
        : parseFloat(o.totaal_incl||0).toFixed(2).replace('.',',');
      const winst  = o.winst_excl != null && o.winst_excl > 0
        ? `<span style="color:#166534;font-weight:600;">€ ${parseFloat(o.winst_excl).toFixed(2).replace('.',',')}</span>`
        : '<span style="color:var(--ink3);">–</span>';
      const verlopen = o.geldig_tot && new Date(o.geldig_tot) < new Date();
      return `<tr style="${verlopen&&o.status==='concept'?'opacity:.6':''}">
        <td><strong>${nr}</strong></td>
        <td>${esc(o.klant_naam||'–')}<br><small style="color:var(--ink3);">${esc(o.klant_email||'')}</small></td>
        <td>${datumKort(o.aangemaakt)}</td>
        <td style="${verlopen?'color:#991b1b;':''}">${o.geldig_tot ? new Date(o.geldig_tot).toLocaleDateString('nl-NL') : '–'}</td>
        <td>€ ${totaal}</td>
        <td>${winst}</td>
        <td>${o.spoed ? '<span class="badge badge-oranje">SPOED</span>' : '<span class="badge badge-grijs">Normaal</span>'}</td>
        <td><span class="badge ${offerteStatusKleur(o.status)}">${offerteStatusLabel(o.status)}</span></td>
        <td>
          <div style="display:flex;gap:.3rem;flex-wrap:wrap;">
            <a href="/bestellen/offerte_pdf.php?token=${o.token}" target="_blank" class="btn btn-s btn-sm">PDF</a>
            <button class="btn btn-s btn-sm" onclick="openOffertestatus('${o.token}','${o.status}','${nr}')">Beheer</button>
          </div>
        </td>
      </tr>`;
    }).join('')}
    </tbody></table>`;
}

function offerteStatusKleur(s) {
  return {concept:'badge-grijs',verzonden:'badge-blauw',geaccepteerd:'badge-groen',
          betaald:'badge-groen',vervallen:'badge-rood'}[s]||'badge-grijs';
}
function offerteStatusLabel(s) {
  return {concept:'Concept',verzonden:'Verzonden',geaccepteerd:'Geaccepteerd',
          betaald:'Betaald',vervallen:'Vervallen'}[s]||s;
}

let huidigOfferteToken = null;

function openOffertestatus(token, huidigStatus, nr) {
  huidigOfferteToken = token;
  document.getElementById('modal-offerte-nr').textContent    = nr;
  document.getElementById('modal-offerte-status').value       = huidigStatus;
  document.getElementById('modal-offerte-fout').style.display = 'none';
  document.getElementById('modal-offerte').style.display      = 'flex';
}

async function slaOfferteStatusOp() {
  const status = document.getElementById('modal-offerte-status').value;
  const foutEl = document.getElementById('modal-offerte-fout');
  foutEl.style.display = 'none';

  const r = await api('admin-offerte-status', { token: huidigOfferteToken, status });
  if (r.ok) {
    sluitModal('modal-offerte');
    laadOffertes();
  } else {
    foutEl.textContent   = r.fout || 'Fout bij opslaan';
    foutEl.style.display = 'block';
  }
}

// ── Catalogus ─────────────────────────────────────────────────────────────────
async function laadCatalogus() {
  const r = await api('catalogus-lijst');
  allCatalogus = r.producten || [];
  document.getElementById('cat-count').textContent = allCatalogus.length + ' producten';
  renderCatalogus(allCatalogus);
}

function filterCatalogus() {
  const q = document.getElementById('cat-zoek')?.value.toLowerCase() || '';
  const filtered = q ? allCatalogus.filter(p =>
    (p.naam+' '+p.merk+' '+p.sku).toLowerCase().includes(q)
  ) : allCatalogus;
  document.getElementById('cat-count').textContent = filtered.length + ' producten';
  renderCatalogus(filtered);
}

function renderCatalogus(lijst) {
  const el = document.getElementById('cat-tbl');
  if (!lijst.length) { el.innerHTML = '<div class="leeg">Geen producten gevonden.</div>'; return; }
  el.innerHTML = `<table>
    <thead><tr><th>SKU</th><th>Merk</th><th>Naam</th><th>Categorie</th><th>Inkoop</th><th>Kleuren</th><th>Actief</th></tr></thead>
    <tbody>${lijst.slice(0,200).map(p => `
      <tr>
        <td style="font-size:.72rem;color:var(--ink3);">${esc(p.sku||'–')}</td>
        <td>${esc(p.merk||'–')}</td>
        <td>${esc(p.naam||'–')}</td>
        <td>${esc(p.categorie||'–')}</td>
        <td>€ ${num(p.inkoopprijs||0)}</td>
        <td>${p.kleur_count||0}</td>
        <td><span class="badge ${p.actief ? 'badge-groen' : 'badge-grijs'}">${p.actief ? 'Ja' : 'Nee'}</span></td>
      </tr>`).join('')}
    </tbody></table>`;
}

// ── Modals ────────────────────────────────────────────────────────────────────
function sluitModal(id) {
  document.getElementById(id).style.display = 'none';
}
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-backdrop')) sluitModal(e.target.id);
});

// ── API helper ────────────────────────────────────────────────────────────────
async function api(action, data = {}) {
  try {
    const r = await fetch(API, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({action, admin: true, ...data})
    });
    return await r.json();
  } catch(e) {
    console.error('API fout:', e);
    return {ok: false, fout: 'Verbindingsfout: ' + e.message};
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function num(v) { return parseFloat(v||0).toFixed(2).replace('.',','); }
function datumKort(d) { if(!d) return '–'; return new Date(d).toLocaleDateString('nl-NL',{day:'numeric',month:'short',year:'numeric'}); }
function statusLabel(s) {
  return {betaald:'Betaald',concept:'Concept',in_behandeling:'In behandeling',geleverd:'Geleverd',geannuleerd:'Geannuleerd'}[s]||s;
}
function statusKleur(s) {
  return {betaald:'badge-groen',concept:'badge-grijs',in_behandeling:'badge-blauw',geleverd:'badge-groen',geannuleerd:'badge-rood'}[s]||'badge-grijs';
}
</script>

<?php endif; ?>
</body>
</html>
