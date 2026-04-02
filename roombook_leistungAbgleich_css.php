<?php
// leistungVergleich.php
require_once 'utils/_utils.php';
init_page_serversides();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Leistungsvergleich</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>


    <?php include_theme_css(); ?>


    <style>
        /* ── TOPBAR ── */
        .topbar {
            background: var(--surf);
            border-bottom: 1px solid var(--border);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 200;
        }
        .topbar h1 {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        /* ── GLZ TOGGLE ── */
        .glz-toggle {
            display: flex;
            border: 1px solid var(--border);
            border-radius: 5px;
            overflow: hidden;
        }
        .glz-btn {
            padding: 3px 12px;
            font-size: 11px;
            cursor: pointer;
            background: none;
            border: none;
            color: var(--dim);
            font-family: 'IBM Plex Mono', monospace;
            transition: all .15s;
        }
        .glz-btn.active { background: var(--zsv); color: #000; font-weight: 600; }

        /* ── COLUMN HEADER STRIP ── */
        .col-headers {
            display: grid;
            grid-template-columns: 90px 1fr repeat(6, 90px) 24px;
            padding: 2px 12px;
            font-size: 9px;
            font-family: 'IBM Plex Mono', monospace;
            color: var(--muted);
            letter-spacing: .06em;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.015);
            position: sticky;
            top: 53px;
            z-index: 100;
        }
        .col-headers span { text-align: right; padding: 0 4px; }
        .col-headers span:nth-child(2) { text-align: left; }

        /* ── GRID ── */
        .grid { padding: 12px 16px; display: flex; flex-direction: column; gap: 8px; }

        /* ── ROOM CARD ── */
        .rc {
            background: var(--surf);
            border: 1px solid var(--border);
            border-radius: 7px;
            overflow: hidden;
        }
        .rc.s-err  { border-left: 3px solid var(--err); }
        .rc.s-warn { border-left: 3px solid var(--warn); }
        .rc.s-ok   { border-left: 3px solid var(--ok); }
        .rc.s-na   { border-left: 3px solid var(--muted); }

        /* ── ROOM HEADER ROW ── */
        .rh {
            display: grid;
            grid-template-columns: 90px 1fr repeat(6, 90px) 24px;
            align-items: center;
            padding: 6px 12px;
            cursor: pointer;
            user-select: none;
        }
        .rh:hover { background: rgba(255,255,255,.025); }
        .rh-nr   { font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--dim); }
        .rh-name { font-size: 12px; font-weight: 600; padding-right: 8px; }
        .rh-val  { text-align: right; font-family: 'IBM Plex Mono', monospace; font-size: 11px; padding: 0 4px; }
        .rh-val .el { color: var(--text); }
        .rh-val .rp { color: var(--muted); font-size: 9px; display: block; }
        .rh-toggle  { text-align: right; color: var(--muted); font-size: 10px; transition: transform .2s; }
        .rc.open .rh-toggle { transform: rotate(90deg); }

        /* ── DETAIL BODY ── */
        .rb { display: none; padding: 0 12px 12px; }
        .rc.open .rb { display: block; }

        /* ── COMPARE TABLE ── */
        .ct { width: 100%; border-collapse: collapse; font-family: 'IBM Plex Mono', monospace; font-size: 11px; margin-top: 8px; }
        .ct th { color: var(--muted); font-size: 9px; text-transform: uppercase; letter-spacing: .06em; padding: 3px 8px; text-align: right; border-bottom: 1px solid var(--border); }
        .ct th:first-child { text-align: left; }
        .ct td { padding: 5px 8px; text-align: right; border-bottom: 1px solid rgba(255,255,255,.03); vertical-align: middle; }
        .ct td:first-child { text-align: left; }
        .ct tr.sum-row td { border-top: 1px solid var(--border); font-weight: 600; }

        /* ── ELEMENT DETAIL ── */
        .el-toggle { margin-top: 8px; background: none; border: 1px solid var(--border); color: var(--muted); font-size: 10px; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-family: 'IBM Plex Mono', monospace; }
        .el-toggle:hover { color: var(--text); border-color: var(--dim); }
        .el-detail { display: none; margin-top: 8px; }
        .el-detail.open { display: block; }
        .et { width: 100%; border-collapse: collapse; font-family: 'IBM Plex Mono', monospace; font-size: 10px; }
        .et th { color: var(--muted); padding: 2px 6px; text-align: right; border-bottom: 1px solid var(--border); font-size: 9px; text-transform: uppercase; letter-spacing: .04em; }
        .et th:first-child { text-align: left; }
        .et td { padding: 3px 6px; text-align: right; color: var(--dim); border-bottom: 1px solid rgba(255,255,255,.02); }
        .et td:first-child { color: var(--text); text-align: left; max-width: 300px; }

        #searchInput { width: 180px; }
    </style>
</head>
<body>
<div id="limet-navbar"></div>

<div class="topbar">
    <h1><i class="fas fa-bolt" style="color:var(--warn);margin-right:6px"></i>LEISTUNGSVERGLEICH</h1>
    <div class="sep"></div>
    <div class="glz-toggle">
        <button class="glz-btn active" id="btnInkl" onclick="setGLZ('inkl')">inkl. GLZ</button>
        <button class="glz-btn"        id="btnExkl" onclick="setGLZ('exkl')">exkl. GLZ</button>
    </div>
    <div class="sep"></div>
    <button class="tbtn active-all" id="fAll"  onclick="setFilter('all')">Alle</button>
    <button class="tbtn"            id="fErr"  onclick="setFilter('err')"><i class="fas fa-times-circle"></i> Fehler</button>
    <button class="tbtn"            id="fWarn" onclick="setFilter('warn')"><i class="fas fa-exclamation-triangle"></i> Warnung</button>
    <button class="tbtn"            id="fOk"   onclick="setFilter('ok')"><i class="fas fa-check-circle"></i> OK</button>
    <button class="tbtn"            id="fNa"   onclick="setFilter('na')">Keine Daten</button>
    <div class="sep"></div>
    <input type="text" id="searchInput" class="dark-input" placeholder="Raum suchen…" oninput="applyFilters()"/>
    <div class="sep"></div>
    <span class="spill spill-err"  id="statErr">–</span>
    <span class="spill spill-warn" id="statWarn">–</span>
    <span class="spill spill-ok"   id="statOk">–</span>
    <div style="flex:1"></div>
    <button class="tbtn" onclick="expandAll()">alle auf</button>
    <button class="tbtn" onclick="collapseAll()">alle zu</button>
    <div class="vr" style="height:22px;background:var(--border)"></div>

    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2" title="Dark Mode">
        <input class="form-check-input" type="checkbox" role="switch"
               id="localDarkToggle"
            <?= ($_SESSION['darkmode'] ?? false) ? 'checked' : '' ?>
               onchange="toggleDarkMode(this.checked)">
        <label class="form-check-label" for="localDarkToggle" style="cursor:pointer;color:var(--dim);font-size:11px">
            <i class="fas fa-<?= ($_SESSION['darkmode'] ?? false) ? 'moon' : 'sun' ?>" id="darkModeIcon"></i>
        </label>
    </div>
</div>

<div class="col-headers">
    <span>Nr</span>
    <span style="text-align:left">Raum</span>
    <span style="color:var(--av)">AV</span>
    <span style="color:var(--sv)">SV</span>
    <span style="color:var(--zsv)">ZSV</span>
    <span style="color:var(--usv)">USV</span>
    <span style="color:var(--nona)">∑</span>
    <span style="color:var(--abw)">Abwärme</span>
    <span></span>
</div>

<div class="legend">
    <span class="li"><span class="ldot" style="background:var(--av)"></span>AV</span>
    <span class="li"><span class="ldot" style="background:var(--sv)"></span>SV</span>
    <span class="li"><span class="ldot" style="background:var(--zsv)"></span>ZSV</span>
    <span class="li"><span class="ldot" style="background:var(--usv)"></span>USV</span>
    <span class="li"><span class="ldot" style="background:var(--nona)"></span>Keine NA</span>
    <span class="li"><span class="ldot" style="background:var(--abw)"></span>Abwärme</span>
    <span style="margin-left:8px">Oben = Elemente · Unten = Raumangabe · Δ = Differenz</span>
    <span class="li ok">✓ ≤ 0%</span>
    <span class="li warn">▲ ≤ 10%</span>
    <span class="li err">✗ > 10%</span>
</div>

<div class="grid" id="grid">
    <div class="loader"><div class="spinner"></div>Lade alle Räume…</div>
</div>

<script src="utils/_utils.js"></script>
<script>
    let allData = [], glzMode = 'inkl', activeFilter = 'all';
    const NA_COLOR = { AV:'var(--av)', SV:'var(--sv)', ZSV:'var(--zsv)', USV:'var(--usv)' };
    const NA_BG    = { AV:'rgba(74,158,255,.15)', SV:'rgba(167,139,250,.15)', ZSV:'rgba(52,211,153,.15)', USV:'rgba(251,146,60,.15)' };

    function fmtW(v) {
        if (v == null || v === '') return '<span class="zero">—</span>';
        v = Math.round(v);
        if (v === 0) return '<span class="zero">0</span>';
        if (Math.abs(v) >= 1000) return (v/1000).toFixed(1)+' kW';
        return v+' W';
    }
    function status(elVal, rpVal) {
        if (rpVal === 0 && elVal === 0) return 'zero';
        if (elVal <= rpVal) return 'ok';
        const r = rpVal > 0 ? (elVal - rpVal) / rpVal : 1;
        return r <= 0.10 ? 'warn' : 'err';
    }
    function roomStatus(d) {
        const m = glzMode;
        const hasEl = d[`el_sum_${m}`] > 0 || d[`el_abw_${m}`] > 0;
        const hasRp = d.rp_sum > 0 || d.rp_abw > 0;
        if (!hasEl && !hasRp) return 'na';
        let worst = 'ok';
        for (const [el,rp] of [[d[`el_AV_${m}`],d.rp_AV],[d[`el_SV_${m}`],d.rp_SV],[d[`el_ZSV_${m}`],d.rp_ZSV],[d[`el_USV_${m}`],d.rp_USV],[d[`el_abw_${m}`],d.rp_abw]]) {
            const s = status(el, rp);
            if (s === 'err') return 'err';
            if (s === 'warn') worst = 'warn';
        }
        return worst;
    }
    function barHtml(elV, rpV, color) {
        let pct = 0, col = color;
        if (rpV > 0) pct = Math.min((elV/rpV)*100, 150);
        else if (elV > 0) pct = 100;
        if (elV > rpV*1.10) col = 'var(--err)';
        else if (elV > rpV) col = 'var(--warn)';
        return `<div class="bw"><div class="bf" style="width:${pct}%;background:${col}"></div></div>`;
    }
    function deltaHtml(elV, rpV) {
        const s = status(elV, rpV);
        if (s === 'zero') return '<span class="zero">—</span>';
        const d = elV - rpV;
        const sym = s === 'ok' ? '✓' : (s === 'warn' ? '▲' : '✗');
        return `<span class="${s}">${sym}${d !== 0 ? ` (${d>0?'+':''}${d}W)` : ''}</span>`;
    }
    function rhCell(elV, rpV, color) {
        const s = status(elV, rpV);
        return `<div class="rh-val">
            <span class="el ${s==='zero'?'zero':s}">${fmtW(elV)}</span>
            <span class="rp">${rpV > 0 ? fmtW(rpV) : ''}</span>
            ${barHtml(elV, rpV, color)}
        </div>`;
    }
    function detailHtml(d) {
        const m = glzMode;
        const rows = [
            { label:'AV',       color:'var(--av)',   el:d[`el_AV_${m}`],   rp:d.rp_AV  },
            { label:'SV',       color:'var(--sv)',   el:d[`el_SV_${m}`],   rp:d.rp_SV  },
            { label:'ZSV',      color:'var(--zsv)',  el:d[`el_ZSV_${m}`],  rp:d.rp_ZSV },
            { label:'USV',      color:'var(--usv)',  el:d[`el_USV_${m}`],  rp:d.rp_USV },
            { label:'Keine NA', color:'var(--nona)', el:d[`el_noNA_${m}`], rp:0, noRp:true },
            { label:'Abwärme',  color:'var(--abw)',  el:d[`el_abw_${m}`],  rp:d.rp_abw },
        ];
        let html = `<table class="ct"><thead><tr>
            <th>Netzart</th><th>Elemente ${m==='inkl'?'inkl.':'exkl.'} GLZ</th>
            <th>Raumangabe</th><th>Δ</th><th>Auslastung</th>
        </tr></thead><tbody>`;
        for (const r of rows) {
            if (r.el === 0 && r.rp === 0) continue;
            html += `<tr>
                <td><span class="nal"><span class="ndot" style="background:${r.color}"></span>${r.label}</span></td>
                <td>${fmtW(r.el)}</td>
                <td>${r.noRp ? '<span class="zero">–</span>' : fmtW(r.rp)}</td>
                <td>${r.noRp ? (r.el>0?'<span class="warn">▲ nicht zugeordnet</span>':'<span class="zero">—</span>') : deltaHtml(r.el,r.rp)}</td>
                <td>${r.noRp ? '' : barHtml(r.el,r.rp,r.color)}</td>
            </tr>`;
        }
        html += `<tr class="sum-row">
            <td>∑ Gesamt</td><td>${fmtW(d[`el_sum_${m}`])}</td><td>${fmtW(d.rp_sum)}</td>
            <td>${deltaHtml(d[`el_sum_${m}`],d.rp_sum)}</td><td>${barHtml(d[`el_sum_${m}`],d.rp_sum,'var(--text)')}</td>
        </tr></tbody></table>`;
        if (d.elements?.length > 0) {
            html += `<button class="el-toggle" onclick="toggleElDetail(this)"><i class="fas fa-list"></i> ${d.elements.length} Elemente mit Leistung</button>
            <div class="el-detail"><table class="et"><thead><tr>
                <th>Element</th><th>Var</th><th>Anz</th><th>P/Stk [W]</th><th>GLZ</th><th>Netzart</th>
                <th>${m==='inkl'?'P inkl.':'P exkl.'} GLZ</th><th>Abwärme/Stk</th>
            </tr></thead><tbody>`;
            for (const el of d.elements) {
                const pVal = m === 'inkl' ? el.P_inkl : el.P_exkl;
                const naBadges = el.NAs.map(na => `<span class="na-badge" style="background:${NA_BG[na]};color:${NA_COLOR[na]}">${na}</span>`).join('') || '<span class="zero" style="font-size:9px">—</span>';
                html += `<tr>
                    <td>${escH(el.name)}</td><td>${escH(el.variante)}</td><td>${el.anzahl}</td>
                    <td>${fmtW(el.P_W)}</td>
                    <td>${el.GLZ !== 1 ? `<span class="dim">${el.GLZ}</span>` : '<span class="zero">1</span>'}</td>
                    <td>${naBadges}</td><td>${fmtW(pVal)}</td>
                    <td>${el.abw > 0 ? fmtW(el.abw) : '<span class="zero">—</span>'}</td>
                </tr>`;
            }
            html += '</tbody></table></div>';
        }
        return html;
    }
    function toggleElDetail(btn) {
        const d = btn.nextElementSibling;
        d.classList.toggle('open');
        btn.innerHTML = d.classList.contains('open')
            ? '<i class="fas fa-chevron-up"></i> Elemente ausblenden'
            : `<i class="fas fa-list"></i> ${d.querySelectorAll('tbody tr').length} Elemente mit Leistung`;
    }
    function escH(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function renderAll() {
        document.querySelectorAll('.rc.open .rb').forEach(rb => {
            const d = allData.find(r => r.id === parseInt(rb.closest('.rc').dataset.id));
            if (d) rb.innerHTML = detailHtml(d);
        });
        document.querySelectorAll('.rc').forEach(card => {
            const d = allData.find(r => r.id === parseInt(card.dataset.id));
            if (!d) return;
            const m = glzMode, st = roomStatus(d);
            card.className = `rc s-${st}` + (card.classList.contains('open') ? ' open' : '');
            card.dataset.state = st;
            card.querySelector('.rh-av').innerHTML  = rhCell(d[`el_AV_${m}`],  d.rp_AV,  'var(--av)');
            card.querySelector('.rh-sv').innerHTML  = rhCell(d[`el_SV_${m}`],  d.rp_SV,  'var(--sv)');
            card.querySelector('.rh-zsv').innerHTML = rhCell(d[`el_ZSV_${m}`], d.rp_ZSV, 'var(--zsv)');
            card.querySelector('.rh-usv').innerHTML = rhCell(d[`el_USV_${m}`], d.rp_USV, 'var(--usv)');
            card.querySelector('.rh-sum').innerHTML = rhCell(d[`el_sum_${m}`], d.rp_sum,  'var(--text)');
            card.querySelector('.rh-abw').innerHTML = rhCell(d[`el_abw_${m}`], d.rp_abw, 'var(--abw)');
        });
        updateStats(); applyFilters();
    }
    function buildCards() {
        const grid = document.getElementById('grid');
        grid.innerHTML = '';
        for (const d of allData) {
            const st = roomStatus(d), m = glzMode;
            const card = document.createElement('div');
            card.className = `rc s-${st}`;
            card.dataset.id = d.id; card.dataset.name = (d.name||'').toLowerCase(); card.dataset.nr = (d.nr||'').toLowerCase(); card.dataset.state = st;
            card.innerHTML = `
                <div class="rh" onclick="toggleCard(this.parentElement)">
                    <div class="rh-nr">${escH(d.nr)}</div>
                    <div class="rh-name">${escH(d.name)}</div>
                    <div class="rh-av">${rhCell(d[`el_AV_${m}`],d.rp_AV,'var(--av)')}</div>
                    <div class="rh-sv">${rhCell(d[`el_SV_${m}`],d.rp_SV,'var(--sv)')}</div>
                    <div class="rh-zsv">${rhCell(d[`el_ZSV_${m}`],d.rp_ZSV,'var(--zsv)')}</div>
                    <div class="rh-usv">${rhCell(d[`el_USV_${m}`],d.rp_USV,'var(--usv)')}</div>
                    <div class="rh-sum">${rhCell(d[`el_sum_${m}`],d.rp_sum,'var(--text)')}</div>
                    <div class="rh-abw">${rhCell(d[`el_abw_${m}`],d.rp_abw,'var(--abw)')}</div>
                    <div class="rh-toggle">▶</div>
                </div>
                <div class="rb"></div>`;
            grid.appendChild(card);
        }
        updateStats(); applyFilters();
    }
    function toggleCard(card) {
        card.classList.toggle('open');
        if (card.classList.contains('open')) {
            const d = allData.find(r => r.id === parseInt(card.dataset.id));
            if (d) card.querySelector('.rb').innerHTML = detailHtml(d);
        }
    }
    function updateStats() {
        let err=0, warn=0, ok=0;
        document.querySelectorAll('.rc').forEach(c => {
            if (c.dataset.state==='err') err++; else if (c.dataset.state==='warn') warn++; else if (c.dataset.state==='ok') ok++;
        });
        document.getElementById('statErr').textContent  = err+' Fehler';
        document.getElementById('statWarn').textContent = warn+' Warnungen';
        document.getElementById('statOk').textContent   = ok+' OK';
    }
    function setGLZ(mode) {
        glzMode = mode;
        document.getElementById('btnInkl').classList.toggle('active', mode==='inkl');
        document.getElementById('btnExkl').classList.toggle('active', mode==='exkl');
        renderAll();
    }
    function setFilter(f) {
        activeFilter = f;
        ['All','Err','Warn','Ok','Na'].forEach(n => {
            document.getElementById('f'+n).className = 'tbtn'+(n.toLowerCase()===f?` active-${f}`:'');
        });
        applyFilters();
    }
    function applyFilters() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.rc').forEach(card => {
            const nameMatch  = !q || card.dataset.name.includes(q) || card.dataset.nr.includes(q);
            const filterMatch = activeFilter==='all' || activeFilter===card.dataset.state;
            card.style.display = (nameMatch && filterMatch) ? '' : 'none';
        });
    }
    function expandAll() {
        document.querySelectorAll('.rc').forEach(c => {
            if (!c.classList.contains('open')) { c.classList.add('open'); const d=allData.find(r=>r.id===parseInt(c.dataset.id)); if(d) c.querySelector('.rb').innerHTML=detailHtml(d); }
        });
    }
    function collapseAll() { document.querySelectorAll('.rc').forEach(c=>c.classList.remove('open')); }

    $.ajax({
        url: 'get_leistung_vergleich.php', type: 'POST', dataType: 'json',
        success(data) { allData=data; buildCards(); },
        error() { document.getElementById('grid').innerHTML='<div class="loader" style="color:var(--err)"><i class="fas fa-exclamation-circle"></i>&nbsp;Fehler beim Laden der Daten.</div>'; }
    });

    function toggleDarkMode(isDark) {
        document.getElementById('darkModeIcon').className = 'fas fa-' + (isDark ? 'moon' : 'sun');
        $.ajax({
            url: '/utils/toggle_darkmode.php',
            type: 'POST',
            success: () => location.reload()
        });
    }
</script>
</body>
</html>