/**
 * Merch Master — Winkelwagen widget v2
 */

(function(global) {
'use strict';

const API_WAGEN = '/bestellen/wagen.php';
const MATEN     = ['XS','S','M','L','XL','XXL','XXXL','One Size'];
const POSITIES  = ['voorkant','achterkant','linkerborst','rechterborst'];

function valideerPosities(posities) {
    const namen = posities.map(p => p.positie);
    if (namen.includes('voorkant') && (namen.includes('linkerborst') || namen.includes('rechterborst')))
        return 'Linkerborst en rechterborst kunnen niet gecombineerd worden met voorkant.';
    if (namen.length !== new Set(namen).size)
        return 'Dezelfde positie kan niet twee keer worden toegevoegd.';
    return null;
}

// ── State ─────────────────────────────────────────────────────────────────────
let state = {
    token:   localStorage.getItem('mm_wagen_token') || null,
    regels:  [],
    totalen: null,
    btw:     localStorage.getItem('mm_btw_keuze') || 'incl',
    spoed:   false,
    laden:   false,
    fout:    null,
    view:    'wagen', // 'wagen' | 'samenvatting'
};

// ── API ───────────────────────────────────────────────────────────────────────
async function wagenApi(actie, data = {}) {
    try {
        const r = await fetch(API_WAGEN, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ actie, wagen_token: state.token || '', btw: state.btw, ...data }),
        });
        if (!r.ok) {
            if (r.status === 429) return { ok: false, fout: 'Te veel verzoeken. Even wachten.' };
            if (r.status === 503) return { ok: false, fout: 'Service tijdelijk niet beschikbaar.' };
            return { ok: false, fout: `Serverfout (${r.status})` };
        }
        return await r.json();
    } catch (e) {
        return { ok: false, fout: 'Geen verbinding met de server. Controleer uw internetverbinding.' };
    }
}

async function uploadApi(wagen_token, regel_id, bestand) {
    try {
        const fd = new FormData();
        fd.append('actie', 'upload');
        fd.append('wagen_token', wagen_token);
        fd.append('regel_id', regel_id);
        fd.append('ontwerp', bestand);
        const r = await fetch(API_WAGEN, { method: 'POST', body: fd });
        return await r.json();
    } catch (e) {
        return { ok: false, fout: 'Upload mislukt. Probeer opnieuw.' };
    }
}

// ── Publieke API ──────────────────────────────────────────────────────────────
const MerchWagen = {

    init({ container }) {
        const el = typeof container === 'string' ? document.querySelector(container) : container;
        if (!el) { console.error('MerchWagen: container niet gevonden'); return; }
        this._container = el;
        this._render();
        this.laden();
    },

    async laden() {
        if (!state.token) { state.laden = false; this._render(); return; }
        state.laden = true; state.fout = null;
        this._render();
        const r = await wagenApi('laden');
        if (r.ok) {
            state.regels  = r.regels  || [];
            state.totalen = r.totalen || null;
            state.fout    = null;
        } else {
            state.fout = r.fout;
        }
        state.laden = false;
        this._render();
    },

    async toevoegen(regel) {
        state.laden = true; state.fout = null;
        this._render();
        const r = await wagenApi('toevoegen', { regel });
        if (r.ok) {
            if (r.wagen_token) {
                state.token = r.wagen_token;
                localStorage.setItem('mm_wagen_token', r.wagen_token);
            }
            await this.laden();
        } else {
            state.fout  = r.fout;
            state.laden = false;
            this._render();
        }
        return r;
    },

    async verwijderen(regel_id) {
        state.fout = null;
        const r = await wagenApi('verwijderen', { regel_id });
        if (r.ok) await this.laden();
        else { state.fout = r.fout; this._render(); }
    },

    async bijwerken(regel_id, updates) {
        state.fout = null;
        const r = await wagenApi('bijwerken', { regel_id, updates });
        if (r.ok) await this.laden();
        else { state.fout = r.fout; this._render(); }
        return r;
    },

    async uploadOntwerp(regel_id, bestand) {
        if (!state.token) return { ok: false, fout: 'Geen wagen token' };
        const r = await uploadApi(state.token, regel_id, bestand);
        if (r.ok) {
            // Update lokale state
            const idx = state.regels.findIndex(r => r.id === regel_id);
            if (idx >= 0) {
                state.regels[idx].upload_token = r.upload_token;
                state.regels[idx].upload_naam  = r.bestandsnaam;
                this._render();
            }
        }
        return r;
    },

    async leegmaken() {
        if (!confirm('Wilt u de winkelwagen leegmaken?')) return;
        await wagenApi('leegmaken');
        state.regels = []; state.totalen = null; state.view = 'wagen';
        this._render();
    },

    setBtw(keuze) {
        state.btw = keuze;
        localStorage.setItem('mm_btw_keuze', keuze);
        this.laden();
    },

    setSpoed(aan) { state.spoed = aan; this._render(); },
    setView(v)    { state.view  = v;   this._render(); },

    async naarOfferte(klantdata) {
        if (!state.regels.length) { alert('Uw winkelwagen is leeg.'); return null; }
        const r = await wagenApi('naar_offerte', { klant: klantdata, spoed: state.spoed });
        if (r.ok) {
            state.regels = []; state.totalen = null;
            state.token  = null; state.view = 'wagen';
            localStorage.removeItem('mm_wagen_token');
            this._render();
        } else {
            state.fout = r.fout;
            this._render();
        }
        return r;
    },

    // ── Render ────────────────────────────────────────────────────────────────
    _render() {
        if (!this._container) return;
        this._container.innerHTML = this._html();
        this._bindEvents();
    },

    _html() {
        if (state.laden) return `<div class="mm-wagen-laden"><div class="mm-spinner"></div> Laden...</div>`;
        if (!state.regels.length) return `
            <div class="mm-wagen-leeg">
              <div style="font-size:2.5rem;margin-bottom:.75rem;">🛒</div>
              <p style="font-weight:600;">Uw winkelwagen is leeg.</p>
              <p style="font-size:.85rem;color:#7a7670;margin-top:.35rem;">Voeg producten toe via de configurator.</p>
            </div>`;

        return state.view === 'samenvatting' ? this._samenvattingHtml() : this._wagenHtml();
    },

    // ── Wagen view ────────────────────────────────────────────────────────────
    _wagenHtml() {
        const T    = state.totalen;
        const incl = state.btw === 'incl';

        return `<div class="mm-wagen">

          <div class="mm-wagen-hdr">
            <div class="mm-wagen-titel">
              Winkelwagen
              <span class="mm-wagen-badge">${state.regels.length} ${state.regels.length===1?'product':'producten'}</span>
            </div>
            <div class="mm-btw-toggle">
              <button class="mm-btw-btn ${incl?'actief':''}" data-btw="incl">Incl. BTW</button>
              <button class="mm-btw-btn ${!incl?'actief':''}" data-btw="excl">Excl. BTW</button>
            </div>
          </div>

          ${state.fout ? `<div class="mm-fout-banner">${esc(state.fout)}</div>` : ''}

          <div class="mm-wagen-regels">
            ${state.regels.map(r => this._regelHtml(r, incl)).join('')}
          </div>

          ${T ? this._totaalHtml(T) : ''}

          <div class="mm-wagen-acties">
            <button class="mm-btn-samenvatting" id="mm-btn-samenvatting">
              Controleren &amp; offerte
            </button>
            <button class="mm-btn-leeg" id="mm-btn-leeg">Wagen leegmaken</button>
          </div>
        </div>`;
    },

    _regelHtml(r, incl) {
        const p           = r.prijs;
        const display_naam = [r.product_naam || r.sku, r.kleur_naam].filter(Boolean).join(' — ');
        const matenStr    = Object.entries(r.maten||{}).filter(([,v])=>v>0).map(([k,v])=>`${k}:${v}`).join(', ');
        const positiesStr = (r.posities||[]).map(p=>`${p.positie}${r.techniek==='zeefdruk'?` (${p.kleuren}kl)`:''}`).join(' + ');
        const totaal      = p ? (incl ? `€ ${fmtN(p.totaal_incl)}` : `€ ${fmtN(p.totaal_excl)}`) : '–';
        const perst       = p ? `€ ${fmtN(p.prijs_excl)} excl. / € ${fmtN(p.prijs_incl)} incl. p/st` : '–';

        return `<div class="mm-regel" data-id="${r.id}">
          <div class="mm-regel-hdr">
            <div class="mm-regel-naam">${esc(display_naam)}</div>
            <div class="mm-regel-prijs">${totaal}</div>
          </div>
          <div class="mm-regel-details">
            <span class="mm-tag">${esc(r.techniek)}</span>
            <span class="mm-tag">${esc(positiesStr)}</span>
            ${p?.volumekorting_pct>0?`<span class="mm-tag mm-tag-korting">${p.volumekorting_pct}% korting</span>`:''}
            ${r.upload_naam?`<span class="mm-tag mm-tag-upload">📎 ${esc(r.upload_naam)}</span>`:'<span class="mm-tag mm-tag-nofile">Geen ontwerp</span>'}
          </div>
          <div class="mm-regel-maten">${esc(matenStr)} <span style="color:#7a7670;">(${r.aantal} stuks)</span></div>
          <div class="mm-regel-perst">${perst}</div>
          ${r.notitie?`<div class="mm-regel-notitie">${esc(r.notitie)}</div>`:''}
          <div class="mm-regel-acties">
            <button class="mm-btn-edit" data-id="${r.id}">Aanpassen</button>
            <button class="mm-btn-del"  data-id="${r.id}">Verwijderen</button>
          </div>
          <div class="mm-regel-edit" id="edit-${r.id}" style="display:none;">
            ${this._editHtml(r)}
          </div>
        </div>`;
    },

    _editHtml(r) {
        const maten = r.maten || {};
        return `<div class="mm-edit-blok">
          <div class="mm-edit-ttl">Maten</div>
          <div class="mm-maten-grid">
            ${MATEN.map(m=>`
              <div class="mm-maat-item">
                <label>${m}</label>
                <input type="number" min="0" max="9999" data-maat="${m}" data-id="${r.id}" value="${maten[m]||0}" class="mm-maat-input">
              </div>`).join('')}
          </div>

          <div class="mm-edit-ttl" style="margin-top:1rem;">Posities</div>
          <div style="font-size:.75rem;color:#7a7670;margin-bottom:.5rem;line-height:1.5;">
            Linker/rechterborst alleen combineerbaar met <strong>achterkant</strong>.
          </div>
          <div class="mm-pos-fout" id="pos-fout-${r.id}" style="display:none;background:#fee2e2;border:1px solid #fca5a5;border-radius:6px;padding:.5rem .75rem;font-size:.78rem;color:#991b1b;margin-bottom:.5rem;"></div>
          <div class="mm-posities-wrap" id="posities-${r.id}">
            ${(r.posities||[]).map((p,i)=>this._positieRijHtml(r.id,i,p.positie,p.kleuren,r.techniek,(r.posities||[]).length>1)).join('')}
          </div>
          ${(r.posities||[]).length < 4
            ? `<button class="mm-add-pos" data-id="${r.id}" style="margin-top:.4rem;">+ Positie</button>`
            : '<div style="font-size:.75rem;color:#7a7670;margin-top:.4rem;">Max. 4 posities.</div>'}

          <div class="mm-edit-ttl" style="margin-top:1rem;">Notitie (optioneel)</div>
          <textarea class="mm-notitie-input" data-id="${r.id}" maxlength="300"
                    placeholder="Bijv. voor artiesten backstage, of speciale instructie..."
                    style="width:100%;padding:.6rem .8rem;border:1.5px solid #e8e4dc;border-radius:8px;font-size:.82rem;font-family:inherit;resize:vertical;min-height:70px;">${esc(r.notitie||'')}</textarea>

          <div class="mm-edit-ttl" style="margin-top:1rem;">Ontwerp uploaden</div>
          <div style="font-size:.75rem;color:#7a7670;margin-bottom:.5rem;">JPG, PNG, SVG, PDF, AI, PSD — max. 20MB</div>
          ${r.upload_naam
            ? `<div style="font-size:.8rem;color:#166534;margin-bottom:.5rem;">✓ Huidig: ${esc(r.upload_naam)}</div>`
            : ''}
          <div class="mm-upload-wrap" id="upload-wrap-${r.id}">
            <input type="file" id="upload-input-${r.id}" data-id="${r.id}"
                   accept=".jpg,.jpeg,.png,.svg,.pdf,.ai,.eps,.psd"
                   class="mm-upload-input" style="display:none;">
            <label for="upload-input-${r.id}" class="mm-upload-label">
              ${r.upload_naam ? 'Ander bestand kiezen' : 'Bestand kiezen'}
            </label>
            <div class="mm-upload-status" id="upload-status-${r.id}"></div>
          </div>

          <div style="display:flex;gap:.5rem;margin-top:1rem;">
            <button class="mm-btn-save"   data-id="${r.id}">Opslaan</button>
            <button class="mm-btn-cancel" data-id="${r.id}">Annuleren</button>
          </div>
        </div>`;
    },

    _positieRijHtml(regelId, idx, huidigPos, huidigKleuren, techniek, kanVerwijderen) {
        return `<div class="mm-positie-rij" data-rij-idx="${idx}">
          <select data-pos-idx="${idx}" data-id="${regelId}" class="mm-pos-sel"
                  onchange="MerchWagen._checkPositieCombo('${regelId}')">
            ${POSITIES.map(pos=>`<option value="${pos}" ${pos===huidigPos?'selected':''}>${pos}</option>`).join('')}
          </select>
          ${techniek==='zeefdruk'?`
            <select data-kl-idx="${idx}" data-id="${regelId}" class="mm-kl-sel">
              ${[1,2,3,4].map(k=>`<option value="${k}" ${k===(huidigKleuren||1)?'selected':''}>${k} kleur${k>1?'en':''}</option>`).join('')}
            </select>`:''}
          ${kanVerwijderen?`<button class="mm-del-pos" onclick="MerchWagen._verwijderPosRij('${regelId}',this.closest('.mm-positie-rij'))">–</button>`:''}
        </div>`;
    },

    _checkPositieCombo(regelId) {
        const wrap   = document.querySelector(`#posities-${regelId}`);
        const foutEl = document.querySelector(`#pos-fout-${regelId}`);
        if (!wrap || !foutEl) return true;
        const namen   = Array.from(wrap.querySelectorAll('.mm-pos-sel')).map(s => s.value);
        const fout    = valideerPosities(namen.map(n=>({positie:n,kleuren:1})));
        foutEl.textContent = fout || '';
        foutEl.style.display = fout ? 'block' : 'none';
        return !fout;
    },

    _verwijderPosRij(regelId, rij) {
        rij.remove();
        this._checkPositieCombo(regelId);
    },

    // Helper: bereken totaal met verzending
    _getTotalWithShipping(T) {
        if (!T) return 0;
        return T.totaal_met_verzend || (T.totaal_incl + (T.verzend_incl || 0));
    },

    // ── Totaalblok ────────────────────────────────────────────────────────────
    _totaalHtml(T) {
        const spoedBedrag = T.totaal_incl * 0.40;
        // Bereken totaal met verzending (fallback als server NULL stuurt)
        const totalMetVerzending = this._getTotalWithShipping(T);

        return `<div class="mm-wagen-totaal">
          <div class="mm-totaal-rij"><span>Subtotaal (${T.totaal_stuks} stuks)</span><span>${fmt(T.subtotaal_excl)} excl. BTW</span></div>
          ${T.vol_pct>0?`<div class="mm-totaal-rij mm-korting"><span>Volumekorting (${T.vol_pct}%)</span><span>– ${fmt(T.vol_korting)}</span></div>`:''}
          <div class="mm-totaal-rij"><span>Totaal excl. BTW</span><span>${fmt(T.totaal_excl)}</span></div>
          <div class="mm-totaal-rij mm-btw-rij"><span>BTW 21%</span><span>${fmt(T.btw)}</span></div>
          <div class="mm-totaal-rij mm-eindtotaal"><span>Totaal incl. BTW</span><span>${fmt(totalMetVerzending)}</span></div>
          ${T.verzend_achteraf
            ? `<div class="mm-totaal-rij mm-verzend-info"><span style="font-size:.78rem;color:#7a7670;">📦 Verzendkosten worden achteraf berekend.</span></div>`
            : `<div class="mm-totaal-rij"><span>Verzending (${T.verzend_label})</span><span>+ ${fmt(T.verzend_excl)}</span></div>
               <div class="mm-totaal-rij mm-eindtotaal-verzend"><span>Incl. verzending</span><span>${fmt(T.totaal_met_verzend)}</span></div>`}
          <div class="mm-spoed-wrap">
            <label class="mm-spoed-label">
              <input type="checkbox" id="mm-spoed-check" ${state.spoed?'checked':''}>
              <span>Spoedorder (+40%)</span>
            </label>
            ${state.spoed?`
              <div class="mm-spoed-waarsch">
                ⚠ Spoedorders uitsluitend na bevestiging via <strong>info@merch-master.com</strong>.
                Online betaling is niet mogelijk.
              </div>
              <div class="mm-totaal-rij mm-spoed-rij"><span>Spoedtoeslag (40%)</span><span>+ ${fmt(spoedBedrag)}</span></div>
              <div class="mm-totaal-rij mm-eindtotaal"><span>Totaal incl. spoed</span><span>${fmt(T.totaal_incl*1.40)}</span></div>`:''}
          </div>
        </div>`;
    },

    // ── Samenvatting view ─────────────────────────────────────────────────────
    _samenvattingHtml() {
        const T    = state.totalen;
        const incl = state.btw === 'incl';
        const totalMetVerzending = this._getTotalWithShipping(T);

        const ontbreektUpload = state.regels.some(r => !r.upload_token);

        return `<div class="mm-wagen">
          <div class="mm-wagen-hdr">
            <button class="mm-btn-terug" id="mm-btn-terug">← Terug naar wagen</button>
            <div class="mm-wagen-titel">Controleren &amp; offerte</div>
          </div>

          ${state.fout?`<div class="mm-fout-banner">${esc(state.fout)}</div>`:''}

          ${ontbreektUpload?`<div class="mm-waarsch-banner">
            ⚠ Een of meer regels hebben nog geen ontwerp. U kunt de offerte wel aanvragen,
            maar stuur het ontwerp zo snel mogelijk na per e-mail.
          </div>`:''}

          <!-- Samenvatting tabel -->
          <div style="overflow-x:auto;margin-bottom:1.25rem;">
            <table class="mm-sam-tbl">
              <thead><tr>
                <th>Product</th><th>Techniek</th><th>Posities</th>
                <th>Maten</th><th>Stuks</th><th>Ontwerp</th><th>Notitie</th>
                <th style="text-align:right;">Subtotaal</th>
              </tr></thead>
              <tbody>
                ${state.regels.map(r => {
                  const p          = r.prijs;
                  const naam       = [r.product_naam||r.sku, r.kleur_naam].filter(Boolean).join(' — ');
                  const positiesStr = (r.posities||[]).map(p=>`${p.positie}${r.techniek==='zeefdruk'?` (${p.kleuren}kl)`:''}`).join(', ');
                  const matenStr   = Object.entries(r.maten||{}).filter(([,v])=>v>0).map(([k,v])=>`${k}:${v}`).join(', ');
                  const subtotaal  = p ? (incl ? fmt(p.totaal_incl) : fmt(p.totaal_excl)) : '–';
                  return `<tr>
                    <td><strong>${esc(naam)}</strong></td>
                    <td>${esc(r.techniek)}</td>
                    <td style="font-size:.78rem;">${esc(positiesStr)}</td>
                    <td style="font-size:.78rem;">${esc(matenStr)}</td>
                    <td style="text-align:center;">${r.aantal}</td>
                    <td style="font-size:.75rem;">${r.upload_naam
                      ? `<span style="color:#166534;">✓ ${esc(r.upload_naam)}</span>`
                      : `<span style="color:#991b1b;">Ontbreekt</span>`}</td>
                    <td style="font-size:.75rem;color:#7a7670;">${esc(r.notitie||'–')}</td>
                    <td style="text-align:right;font-weight:600;">${subtotaal}</td>
                  </tr>`;
                }).join('')}
              </tbody>
            </table>
          </div>

          <!-- Totalen samenvatting -->
          ${T?`<div class="mm-wagen-totaal" style="margin-bottom:1.25rem;">
            <div class="mm-totaal-rij"><span>Subtotaal</span><span>${fmt(T.subtotaal_excl)} excl. BTW</span></div>
            ${T.vol_pct>0?`<div class="mm-totaal-rij mm-korting"><span>Volumekorting (${T.vol_pct}%)</span><span>– ${fmt(T.vol_korting)}</span></div>`:''}
            <div class="mm-totaal-rij"><span>Totaal excl. BTW</span><span>${fmt(T.totaal_excl)}</span></div>
            <div class="mm-totaal-rij mm-btw-rij"><span>BTW 21%</span><span>${fmt(T.btw)}</span></div>
            <div class="mm-totaal-rij mm-eindtotaal"><span>Totaal incl. BTW</span><span>${fmt(totalMetVerzending)}</span></div>
            ${state.spoed?`<div class="mm-totaal-rij mm-spoed-rij"><span>Spoedtoeslag 40%</span><span>+ ${fmt(T.totaal_incl*0.40)}</span></div>
            <div class="mm-totaal-rij mm-eindtotaal"><span>Te betalen</span><span>${fmt(T.totaal_incl*1.40)}</span></div>`:''}
          </div>`:''}

          <!-- Klantgegevens -->
          <div class="mm-edit-blok" style="margin-bottom:1.25rem;">
            <div class="mm-edit-ttl">Uw gegevens</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
              <div><label class="mm-veld-lbl">Naam *</label>
                <input type="text" id="klant-naam" class="mm-veld-input" placeholder="Jan de Vries"></div>
              <div><label class="mm-veld-lbl">E-mail *</label>
                <input type="email" id="klant-email" class="mm-veld-input" placeholder="jan@bedrijf.nl"></div>
              <div><label class="mm-veld-lbl">Telefoon</label>
                <input type="tel" id="klant-tel" class="mm-veld-input" placeholder="+31 6 ..."></div>
              <div><label class="mm-veld-lbl">Bedrijf</label>
                <input type="text" id="klant-bedrijf" class="mm-veld-input"></div>
            </div>
            ${state.spoed?`<div class="mm-spoed-waarsch" style="margin-top:.75rem;">
              ⚠ Spoedorder — na aanvragen direct contact opnemen via <strong>info@merch-master.com</strong>.
            </div>`:''}
          </div>

          <div class="mm-wagen-acties">
            <button class="mm-btn-offerte" id="mm-btn-offerte">
              ${state.spoed ? 'Spoedofferte aanvragen' : 'Offerte aanvragen'}
            </button>
          </div>
        </div>`;
    },

    // ── Events ────────────────────────────────────────────────────────────────
    _bindEvents() {
        const c = this._container;
        if (!c) return;

        // BTW toggle
        c.querySelectorAll('.mm-btw-btn').forEach(btn =>
            btn.addEventListener('click', () => this.setBtw(btn.dataset.btw)));

        // Verwijder
        c.querySelectorAll('.mm-btn-del').forEach(btn =>
            btn.addEventListener('click', () => this.verwijderen(btn.dataset.id)));

        // Edit toggle
        c.querySelectorAll('.mm-btn-edit').forEach(btn =>
            btn.addEventListener('click', () => {
                const el = c.querySelector(`#edit-${btn.dataset.id}`);
                if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
            }));

        // Opslaan
        c.querySelectorAll('.mm-btn-save').forEach(btn =>
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                if (!MerchWagen._checkPositieCombo(id)) return;

                const maten = {};
                c.querySelectorAll(`.mm-maat-input[data-id="${id}"]`).forEach(inp => {
                    const v = parseInt(inp.value)||0;
                    if (v > 0) maten[inp.dataset.maat] = v;
                });
                if (!Object.keys(maten).length) { alert('Voer minimaal één maat in.'); return; }

                const posities = [];
                c.querySelectorAll(`#posities-${id} .mm-positie-rij`).forEach(rij => {
                    const sel   = rij.querySelector('.mm-pos-sel');
                    const klSel = rij.querySelector('.mm-kl-sel');
                    if (sel) posities.push({ positie: sel.value, kleuren: klSel ? parseInt(klSel.value) : 1 });
                });
                const fout = valideerPosities(posities);
                if (fout) { alert(fout); return; }

                const notitie = c.querySelector(`.mm-notitie-input[data-id="${id}"]`)?.value || '';
                await this.bijwerken(id, { maten, posities, notitie });
            }));

        // Annuleer
        c.querySelectorAll('.mm-btn-cancel').forEach(btn =>
            btn.addEventListener('click', () => {
                const el = c.querySelector(`#edit-${btn.dataset.id}`);
                if (el) el.style.display = 'none';
            }));

        // Positie toevoegen
        c.querySelectorAll('.mm-add-pos').forEach(btn =>
            btn.addEventListener('click', () => {
                const id    = btn.dataset.id;
                const wrap  = c.querySelector(`#posities-${id}`);
                const regel = state.regels.find(r => r.id === id);
                if (!wrap || !regel) return;
                const n = wrap.querySelectorAll('.mm-positie-rij').length;
                if (n >= 4) return;
                const div = document.createElement('div');
                div.className = 'mm-positie-rij';
                div.innerHTML = this._positieRijHtml(id, n, 'achterkant', 1, regel.techniek, true);
                wrap.insertBefore(div, btn);
                this._checkPositieCombo(id);
            }));

        // Upload
        c.querySelectorAll('.mm-upload-input').forEach(inp =>
            inp.addEventListener('change', async () => {
                const id      = inp.dataset.id;
                const bestand = inp.files[0];
                if (!bestand) return;
                const statusEl = c.querySelector(`#upload-status-${id}`);
                if (statusEl) statusEl.textContent = 'Uploaden...';
                const r = await this.uploadOntwerp(id, bestand);
                if (statusEl) statusEl.textContent = r.ok ? `✓ Geüpload: ${r.bestandsnaam}` : `✗ ${r.fout}`;
            }));

        // Spoed
        const spoedCheck = c.querySelector('#mm-spoed-check');
        if (spoedCheck) spoedCheck.addEventListener('change', () => this.setSpoed(spoedCheck.checked));

        // Wagen leegmaken
        c.querySelector('#mm-btn-leeg')?.addEventListener('click', () => this.leegmaken());

        // Naar samenvatting
        c.querySelector('#mm-btn-samenvatting')?.addEventListener('click', () => this.setView('samenvatting'));

        // Terug naar wagen
        c.querySelector('#mm-btn-terug')?.addEventListener('click', () => this.setView('wagen'));

        // Offerte aanvragen
        c.querySelector('#mm-btn-offerte')?.addEventListener('click', async () => {
            const naam  = c.querySelector('#klant-naam')?.value.trim();
            const email = c.querySelector('#klant-email')?.value.trim();
            if (!naam || !email) { alert('Vul uw naam en e-mailadres in.'); return; }

            const btn = c.querySelector('#mm-btn-offerte');
            btn.disabled    = true;
            btn.textContent = 'Bezig...';

            const r = await this.naarOfferte({ naam, email,
                tel:     c.querySelector('#klant-tel')?.value.trim(),
                bedrijf: c.querySelector('#klant-bedrijf')?.value.trim(),
            });

            btn.disabled = false;
            if (r?.ok) {
                document.dispatchEvent(new CustomEvent('mm:offerte-aangemaakt', { detail: r }));
                if (r.pdf_url) window.open(r.pdf_url, '_blank');
            } else {
                btn.textContent = state.spoed ? 'Spoedofferte aanvragen' : 'Offerte aanvragen';
            }
        });
    },
};

// ── CSS ───────────────────────────────────────────────────────────────────────
(function() {
    if (document.getElementById('mm-wagen-css')) return;
    const s = document.createElement('style');
    s.id = 'mm-wagen-css';
    s.textContent = `
.mm-wagen{font-family:'DM Sans',system-ui,sans-serif;font-size:14px;color:#1a1a1a;}
.mm-wagen-laden{display:flex;align-items:center;gap:.75rem;padding:2rem;color:#7a7670;}
.mm-spinner{width:16px;height:16px;border:2.5px solid #e8e4dc;border-top-color:#c4622d;border-radius:50%;animation:mm-spin .6s linear infinite;flex-shrink:0;}
@keyframes mm-spin{to{transform:rotate(360deg);}}
.mm-wagen-leeg{text-align:center;padding:3rem 1rem;color:#7a7670;}
.mm-wagen-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:.5rem;flex-wrap:wrap;}
.mm-wagen-titel{font-weight:700;font-size:1rem;display:flex;align-items:center;gap:.5rem;}
.mm-wagen-badge{background:#c4622d;color:#fff;font-size:.65rem;font-weight:700;padding:.2rem .5rem;border-radius:12px;}
.mm-btw-toggle{display:flex;border:1.5px solid #e8e4dc;border-radius:6px;overflow:hidden;}
.mm-btw-btn{padding:.35rem .75rem;font-size:.75rem;font-weight:600;border:none;background:#fff;cursor:pointer;font-family:inherit;color:#7a7670;}
.mm-btw-btn.actief{background:#1a1a1a;color:#fff;}
.mm-fout-banner{background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:.65rem .9rem;font-size:.82rem;color:#991b1b;margin-bottom:.75rem;}
.mm-waarsch-banner{background:#fff3cd;border:1px solid #f59e0b;border-radius:8px;padding:.65rem .9rem;font-size:.82rem;color:#92400e;margin-bottom:.75rem;line-height:1.6;}
.mm-wagen-regels{display:flex;flex-direction:column;gap:.75rem;margin-bottom:1.25rem;}
.mm-regel{background:#faf8f4;border:1px solid #e8e4dc;border-radius:10px;padding:1rem;}
.mm-regel-hdr{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.5rem;}
.mm-regel-naam{font-weight:700;font-size:.9rem;}
.mm-regel-prijs{font-weight:700;color:#c4622d;white-space:nowrap;margin-left:1rem;}
.mm-regel-details{display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:.4rem;}
.mm-tag{font-size:.7rem;background:#fff;border:1px solid #e8e4dc;padding:.2rem .5rem;border-radius:4px;color:#3a3832;}
.mm-tag-korting{background:#dcfce7;border-color:#86efac;color:#166534;}
.mm-tag-upload{background:#dbeafe;border-color:#93c5fd;color:#1e40af;}
.mm-tag-nofile{background:#fee2e2;border-color:#fca5a5;color:#991b1b;}
.mm-regel-maten{font-size:.78rem;color:#3a3832;margin-bottom:.3rem;}
.mm-regel-perst{font-size:.75rem;color:#7a7670;margin-bottom:.4rem;}
.mm-regel-notitie{font-size:.78rem;color:#7a7670;background:#fff;border:1px solid #e8e4dc;border-radius:6px;padding:.4rem .6rem;margin-bottom:.4rem;line-height:1.5;}
.mm-regel-acties{display:flex;gap:.4rem;}
.mm-btn-edit,.mm-btn-del,.mm-btn-save,.mm-btn-cancel,.mm-add-pos,.mm-del-pos{
  padding:.3rem .75rem;border-radius:50px;font-size:.75rem;font-weight:600;cursor:pointer;
  border:1.5px solid #e8e4dc;background:#fff;font-family:inherit;transition:all .15s;}
.mm-btn-del{color:#991b1b;border-color:#fca5a5;}
.mm-btn-del:hover{background:#fee2e2;}
.mm-btn-save{background:#c4622d;color:#fff;border-color:#c4622d;}
.mm-btn-save:hover{background:#a3521f;}
.mm-btn-terug{padding:.4rem .85rem;border-radius:50px;font-size:.78rem;font-weight:600;cursor:pointer;border:1.5px solid #e8e4dc;background:#fff;font-family:inherit;}
.mm-edit-blok{background:#fff;border:1px solid #e8e4dc;border-radius:8px;padding:1rem;margin-top:.75rem;}
.mm-edit-ttl{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#7a7670;margin-bottom:.6rem;}
.mm-maten-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.4rem;}
.mm-maat-item{display:flex;flex-direction:column;gap:.2rem;}
.mm-maat-item label{font-size:.7rem;font-weight:600;color:#3a3832;}
.mm-maat-input{width:100%;padding:.35rem .4rem;border:1.5px solid #e8e4dc;border-radius:6px;font-size:.82rem;text-align:center;font-family:inherit;}
.mm-maat-input:focus{outline:none;border-color:#c4622d;}
.mm-posities-wrap{display:flex;flex-direction:column;gap:.5rem;}
.mm-positie-rij{display:flex;gap:.5rem;align-items:center;}
.mm-pos-sel,.mm-kl-sel{padding:.35rem .5rem;border:1.5px solid #e8e4dc;border-radius:6px;font-size:.82rem;font-family:inherit;}
.mm-upload-label{display:inline-block;padding:.4rem .85rem;border:1.5px solid #e8e4dc;border-radius:50px;font-size:.75rem;font-weight:600;cursor:pointer;font-family:inherit;background:#fff;}
.mm-upload-label:hover{background:#f5f3ef;}
.mm-upload-status{font-size:.75rem;margin-top:.35rem;color:#166534;}
.mm-wagen-totaal{background:#fff;border:1.5px solid #e8e4dc;border-radius:10px;padding:1.25rem;margin-bottom:1rem;}
.mm-totaal-rij{display:flex;justify-content:space-between;padding:.35rem 0;font-size:.85rem;border-bottom:1px solid #f5f3ef;}
.mm-totaal-rij:last-child{border-bottom:none;}
.mm-korting{color:#166534;}
.mm-btw-rij{color:#7a7670;font-size:.8rem;}
.mm-eindtotaal{font-weight:700;font-size:1rem;padding-top:.6rem;margin-top:.3rem;border-top:2px solid #1a1a1a!important;border-bottom:none!important;}
.mm-eindtotaal-verzend{font-weight:700;color:#c4622d;border-bottom:none!important;}
.mm-verzend-info{border-bottom:none!important;}
.mm-spoed-wrap{margin-top:1rem;padding-top:1rem;border-top:1px solid #e8e4dc;}
.mm-spoed-label{display:flex;align-items:center;gap:.5rem;font-size:.85rem;font-weight:600;cursor:pointer;}
.mm-spoed-waarsch{margin-top:.6rem;background:#fff3cd;border:1px solid #f59e0b;border-radius:6px;padding:.6rem .85rem;font-size:.78rem;color:#92400e;line-height:1.6;}
.mm-spoed-rij{color:#92400e;}
.mm-wagen-acties{display:flex;gap:.75rem;}
.mm-btn-samenvatting,.mm-btn-offerte{flex:1;padding:.85rem 1.5rem;background:#c4622d;color:#fff;border:none;border-radius:50px;font-weight:700;font-size:.9rem;cursor:pointer;font-family:inherit;transition:background .2s;}
.mm-btn-samenvatting:hover,.mm-btn-offerte:hover{background:#a3521f;}
.mm-btn-samenvatting:disabled,.mm-btn-offerte:disabled{background:#ccc;cursor:not-allowed;}
.mm-btn-leeg{padding:.85rem 1rem;background:#fff;color:#7a7670;border:1.5px solid #e8e4dc;border-radius:50px;font-weight:600;font-size:.82rem;cursor:pointer;font-family:inherit;}
.mm-btn-leeg:hover{background:#f5f3ef;}
.mm-sam-tbl{width:100%;border-collapse:collapse;font-size:.8rem;}
.mm-sam-tbl th{text-align:left;padding:.5rem .6rem;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7a7670;border-bottom:2px solid #e8e4dc;white-space:nowrap;}
.mm-sam-tbl td{padding:.55rem .6rem;border-bottom:1px solid #f5f3ef;vertical-align:top;}
.mm-sam-tbl tr:hover td{background:#faf8f4;}
.mm-veld-lbl{display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7a7670;margin-bottom:.3rem;}
.mm-veld-input{width:100%;padding:.6rem .8rem;border:1.5px solid #e8e4dc;border-radius:8px;font-size:.85rem;font-family:inherit;}
.mm-veld-input:focus{outline:none;border-color:#c4622d;}
    `;
    document.head.appendChild(s);
})();

// Helpers
function fmt(v)  { return '€\u00a0' + fmtN(v); }
function fmtN(v) { return parseFloat(v||0).toFixed(2).replace('.',','); }
function esc(s)  { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

global.MerchWagen = MerchWagen;
})(window);
