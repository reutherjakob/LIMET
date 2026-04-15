<?php
// leistungVergleich.php
require_once 'utils/_utils.php';
init_page_serversides("","x");
$dark = $_SESSION['darkmode'] ?? false;
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="<?= $dark ? 'dark' : 'light' ?>">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Leistungsvergleich</title>
    <link rel="icon" href="Logo/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        /* Nur Netzart-Farben + Grid-Layout + Auf/Zu — alles andere ist Bootstrap */
        :root {
            --c-av:  <?= $dark ? '#4a9eff' : '#1a7fe0' ?>;
            --c-sv:  <?= $dark ? '#a78bfa' : '#7c4ddb' ?>;
            --c-zsv: <?= $dark ? '#34d399' : '#1aaa78' ?>;
            --c-usv: <?= $dark ? '#fb923c' : '#d4650a' ?>;
            --c-nona:#64748b;
            --c-abw: <?= $dark ? '#f472b6' : '#c2185b' ?>;
        }
        .sticky-toolbar   { position:sticky; top:0; z-index:1030; }
        .sticky-colheader { position:sticky; top:57px; z-index:1020; }
        .rh               { display:grid; grid-template-columns:90px 1fr repeat(6,90px) 24px; align-items:center; cursor:pointer; user-select:none; }
        .col-hdr          { display:grid; grid-template-columns:90px 1fr repeat(6,90px) 24px; }
        .rh-toggle        { transition:transform .2s; }
        .rc.open .rh-toggle { transform:rotate(90deg); }
        .rb               { display:none; }
        .rc.open .rb      { display:block; }
        .el-detail        { display:none; }
        .el-detail.open   { display:block; }
        .bw               { height:4px; border-radius:2px; overflow:hidden; margin-top:3px; background:var(--bs-border-color); }
        .bf               { height:100%; border-radius:2px; transition:width .3s; }
        .rh-val           { text-align:right; font-family:'SFMono-Regular',Consolas,monospace; font-size:.72rem; padding:0 4px; }
        .rh-val .rp       { font-size:.62rem; display:block; opacity:.6; }
    </style>
</head>
<body>
<div id="limet-navbar"></div>

<!-- TOPBAR -->
<div class="sticky-toolbar bg-body border-bottom shadow-sm px-3 py-2 d-flex align-items-center gap-2 flex-wrap">
    <span class="fw-bold font-monospace small text-nowrap">
        <i class="fas fa-bolt text-warning me-1"></i>LEISTUNGSVERGLEICH
    </span>
    <div class="vr"></div>
    <div class="btn-group btn-group-sm">
        <button class="btn btn-success active"    id="btnInkl" onclick="setGLZ('inkl')">inkl. GLZ</button>
        <button class="btn btn-outline-secondary" id="btnExkl" onclick="setGLZ('exkl')">exkl. GLZ</button>
    </div>
    <div class="vr"></div>
    <div class="btn-group btn-group-sm">
        <button class="btn btn-secondary active"  id="fAll"  onclick="setFilter('all')">Alle</button>
        <button class="btn btn-outline-danger"    id="fErr"  onclick="setFilter('err')"><i class="fas fa-times-circle"></i> Fehler</button>
        <button class="btn btn-outline-warning"   id="fWarn" onclick="setFilter('warn')"><i class="fas fa-exclamation-triangle"></i> Warnung</button>
        <button class="btn btn-outline-success"   id="fOk"   onclick="setFilter('ok')"><i class="fas fa-check-circle"></i> OK</button>
        <button class="btn btn-outline-secondary" id="fNa"   onclick="setFilter('na')">Keine Daten</button>
    </div>
    <div class="vr"></div>
    <input type="text" id="searchInput" class="form-control form-control-sm" style="width:180px"
           placeholder="Raum suchen…" oninput="applyFilters()"/>
    <div class="vr"></div>
    <span class="badge rounded-pill border border-danger  text-danger  bg-transparent font-monospace" id="statErr">–</span>
    <span class="badge rounded-pill border border-warning text-warning bg-transparent font-monospace" id="statWarn">–</span>
    <span class="badge rounded-pill border border-success text-success bg-transparent font-monospace" id="statOk">–</span>
    <div class="ms-auto d-flex gap-1">

        <button class="btn btn-sm btn-outline-secondary" onclick="expandAll()">alle auf</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">alle zu</button>
    </div>

    <button type="button" class="btn btn-sm btn-outline-success d-flex align-items-center" onclick="window.location.href='/roombook_leistungAbgleich_css.php'">
        <i class="fas fa-check-double me-2"></i>
        Experimentelle CSS Version (inkl. Dark Mode)
    </button>
</div>

<!-- SPALTEN-HEADER -->
<div class="sticky-colheader col-hdr bg-body-tertiary border-bottom px-3 py-1 text-secondary font-monospace text-uppercase"
     style="font-size:.62rem;letter-spacing:.06em">
    <span>Nr</span><span>Raum</span>
    <span class="text-end" style="color:var(--c-av)">AV</span>
    <span class="text-end" style="color:var(--c-sv)">SV</span>
    <span class="text-end" style="color:var(--c-zsv)">ZSV</span>
    <span class="text-end" style="color:var(--c-usv)">USV</span>
    <span class="text-end">∑</span>
    <span class="text-end" style="color:var(--c-abw)">Abwärme</span>
    <span></span>
</div>

<!-- LEGENDE -->
<div class="d-flex flex-wrap gap-3 align-items-center px-3 py-1 border-bottom bg-body" style="font-size:.7rem">
    <?php foreach([['av','AV'],['sv','SV'],['zsv','ZSV'],['usv','USV'],['nona','Keine NA'],['abw','Abwärme']] as [$k,$l]): ?>
        <span class="d-flex align-items-center gap-1 text-secondary">
        <span class="rounded-circle" style="width:8px;height:8px;background:var(--c-<?=$k?>);display:inline-block"></span><?=$l?>
    </span>
    <?php endforeach; ?>
    <span class="ms-2 text-body-secondary" style="font-size:.65rem">Oben = Elemente · Unten = Raumangabe</span>
    <span class="text-success fw-semibold">✓ ≤ 0%</span>
    <span class="text-warning fw-semibold">▲ ≤ 10%</span>
    <span class="text-danger  fw-semibold">✗ > 10%</span>
</div>

<!-- GRID -->
<div class="container-fluid py-3 d-flex flex-column gap-2" id="grid">
    <div class="d-flex align-items-center gap-2 p-4 text-secondary font-monospace small">
        <div class="spinner-border spinner-border-sm text-info" role="status"></div>
        Lade alle Räume…
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    let allData=[], glzMode='inkl', activeFilter='all';
    const NA_COLOR={AV:'var(--c-av)',SV:'var(--c-sv)',ZSV:'var(--c-zsv)',USV:'var(--c-usv)'};
    const NA_BG={AV:'rgba(74,158,255,.12)',SV:'rgba(167,139,250,.12)',ZSV:'rgba(52,211,153,.12)',USV:'rgba(251,146,60,.12)'};

    function fmtW(v){
        if(v==null||v==='') return '<span class="text-secondary">—</span>';
        v=Math.round(v);
        if(v===0) return '<span class="text-secondary">0</span>';
        return Math.abs(v)>=1000?(v/1000).toFixed(1)+' kW':v+' W';
    }
    function status(el,rp){
        if(rp===0&&el===0) return 'zero';
        if(el<=rp) return 'ok';
        return ((rp>0?(el-rp)/rp:1)<=0.10)?'warn':'err';
    }
    function roomStatus(d){
        const m=glzMode;
        if(d[`el_sum_${m}`]===0&&d[`el_abw_${m}`]===0&&d.rp_sum===0&&d.rp_abw===0) return 'na';
        let w='ok';
        for(const [el,rp] of [[d[`el_AV_${m}`],d.rp_AV],[d[`el_SV_${m}`],d.rp_SV],[d[`el_ZSV_${m}`],d.rp_ZSV],[d[`el_USV_${m}`],d.rp_USV],[d[`el_abw_${m}`],d.rp_abw]]){
            const s=status(el,rp); if(s==='err') return 'err'; if(s==='warn') w='warn';
        }
        return w;
    }
    const SC={ok:'text-success',warn:'text-warning',err:'text-danger fw-bold',zero:'text-secondary'};
    function barHtml(elV,rpV,col){
        let pct=rpV>0?Math.min((elV/rpV)*100,150):(elV>0?100:0),c=col;
        if(elV>rpV*1.10) c='var(--bs-danger)'; else if(elV>rpV) c='var(--bs-warning)';
        return `<div class="bw"><div class="bf" style="width:${pct}%;background:${c}"></div></div>`;
    }
    function deltaHtml(elV,rpV){
        const s=status(elV,rpV);
        if(s==='zero') return '<span class="text-secondary">—</span>';
        const d=elV-rpV,sym=s==='ok'?'✓':(s==='warn'?'▲':'✗');
        return `<span class="${SC[s]}">${sym}${d!==0?` (${d>0?'+':''}${d}W)`:''}</span>`;
    }
    function rhCell(elV,rpV,color){
        const s=status(elV,rpV);
        return `<div class="rh-val"><span class="${SC[s]}">${fmtW(elV)}</span><span class="rp">${rpV>0?fmtW(rpV):''}</span>${barHtml(elV,rpV,color)}</div>`;
    }
    function detailHtml(d){
        const m=glzMode;
        const rows=[
            {label:'AV',      color:'var(--c-av)',  el:d[`el_AV_${m}`],   rp:d.rp_AV},
            {label:'SV',      color:'var(--c-sv)',  el:d[`el_SV_${m}`],   rp:d.rp_SV},
            {label:'ZSV',     color:'var(--c-zsv)', el:d[`el_ZSV_${m}`],  rp:d.rp_ZSV},
            {label:'USV',     color:'var(--c-usv)', el:d[`el_USV_${m}`],  rp:d.rp_USV},
            {label:'Keine NA',color:'var(--c-nona)',el:d[`el_noNA_${m}`], rp:0,noRp:true},
            {label:'Abwärme', color:'var(--c-abw)', el:d[`el_abw_${m}`],  rp:d.rp_abw},
        ];
        let html=`<table class="table table-sm table-bordered font-monospace mt-2" style="font-size:.75rem">
        <thead class="table-active"><tr>
            <th>Netzart</th><th class="text-end">Elemente ${m==='inkl'?'inkl.':'exkl.'} GLZ</th>
            <th class="text-end">Raumangabe</th><th class="text-end">Δ</th><th style="min-width:80px">Auslastung</th>
        </tr></thead><tbody>`;
        for(const r of rows){
            if(r.el===0&&r.rp===0) continue;
            html+=`<tr>
            <td><span class="d-inline-flex align-items-center gap-2">
                <span class="rounded-circle flex-shrink-0" style="width:7px;height:7px;background:${r.color}"></span>${r.label}
            </span></td>
            <td class="text-end">${fmtW(r.el)}</td>
            <td class="text-end">${r.noRp?'<span class="text-secondary">–</span>':fmtW(r.rp)}</td>
            <td class="text-end">${r.noRp?(r.el>0?'<span class="text-warning">▲ nicht zugeordnet</span>':'<span class="text-secondary">—</span>'):deltaHtml(r.el,r.rp)}</td>
            <td>${r.noRp?'':barHtml(r.el,r.rp,r.color)}</td></tr>`;
        }
        html+=`<tr class="table-active fw-bold">
        <td>∑ Gesamt</td><td class="text-end">${fmtW(d[`el_sum_${m}`])}</td>
        <td class="text-end">${fmtW(d.rp_sum)}</td><td class="text-end">${deltaHtml(d[`el_sum_${m}`],d.rp_sum)}</td>
        <td>${barHtml(d[`el_sum_${m}`],d.rp_sum,'var(--bs-body-color)')}</td></tr></tbody></table>`;
        if(d.elements?.length>0){
            html+=`<button class="btn btn-sm btn-outline-secondary font-monospace" onclick="toggleElDetail(this)">
            <i class="fas fa-list"></i> ${d.elements.length} Elemente mit Leistung</button>
        <div class="el-detail mt-2"><table class="table table-sm table-bordered table-hover font-monospace" style="font-size:.7rem">
        <thead class="table-active"><tr>
            <th>Element</th><th>Var</th><th class="text-center">Anz</th><th class="text-end">P/Stk</th>
            <th class="text-end">GLZ</th><th>Netzart</th>
            <th class="text-end">${m==='inkl'?'P inkl.':'P exkl.'} GLZ</th><th class="text-end">Abwärme/Stk</th>
        </tr></thead><tbody>`;
            for(const el of d.elements){
                const pVal=m==='inkl'?el.P_inkl:el.P_exkl;
                const naBadges=el.NAs.map(na=>`<span class="badge rounded-pill" style="background:${NA_BG[na]};color:${NA_COLOR[na]};font-size:.65rem">${na}</span>`).join(' ')||'<span class="text-secondary">—</span>';
                html+=`<tr>
                <td>${escH(el.name)}</td><td class="text-center">${escH(el.variante)}</td>
                <td class="text-center">${el.anzahl}</td><td class="text-end">${fmtW(el.P_W)}</td>
                <td class="text-end ${el.GLZ!==1?'text-warning':''}">${el.GLZ}</td>
                <td>${naBadges}</td><td class="text-end">${fmtW(pVal)}</td>
                <td class="text-end">${el.abw>0?fmtW(el.abw):'<span class="text-secondary">—</span>'}</td></tr>`;
            }
            html+='</tbody></table></div>';
        }
        return html;
    }
    function toggleElDetail(btn){
        const d=btn.nextElementSibling; d.classList.toggle('open');
        btn.innerHTML=d.classList.contains('open')
            ?'<i class="fas fa-chevron-up"></i> Elemente ausblenden'
            :`<i class="fas fa-list"></i> ${d.querySelectorAll('tbody tr').length} Elemente mit Leistung`;
    }
    function escH(s){return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
    function borderCls(st){return{ok:'border-start border-success border-3',warn:'border-start border-warning border-3',err:'border-start border-danger border-3',na:'border-start border-secondary border-3'}[st]||'';}

    function renderAll(){
        document.querySelectorAll('.rc.open .rb').forEach(rb=>{
            const d=allData.find(r=>r.id===parseInt(rb.closest('.rc').dataset.id));
            if(d) rb.innerHTML=detailHtml(d);
        });
        document.querySelectorAll('.rc').forEach(card=>{
            const d=allData.find(r=>r.id===parseInt(card.dataset.id)); if(!d) return;
            const m=glzMode,st=roomStatus(d);
            card.className=`rc card ${borderCls(st)}`+(card.classList.contains('open')?' open':'');
            card.dataset.state=st;
            card.querySelector('.rh-av').innerHTML =rhCell(d[`el_AV_${m}`], d.rp_AV, 'var(--c-av)');
            card.querySelector('.rh-sv').innerHTML =rhCell(d[`el_SV_${m}`], d.rp_SV, 'var(--c-sv)');
            card.querySelector('.rh-zsv').innerHTML=rhCell(d[`el_ZSV_${m}`],d.rp_ZSV,'var(--c-zsv)');
            card.querySelector('.rh-usv').innerHTML=rhCell(d[`el_USV_${m}`],d.rp_USV,'var(--c-usv)');
            card.querySelector('.rh-sum').innerHTML=rhCell(d[`el_sum_${m}`],d.rp_sum, 'var(--bs-body-color)');
            card.querySelector('.rh-abw').innerHTML=rhCell(d[`el_abw_${m}`],d.rp_abw,'var(--c-abw)');
        });
        updateStats(); applyFilters();
    }
    function buildCards(){
        const grid=document.getElementById('grid'); grid.innerHTML=''; const m=glzMode;
        for(const d of allData){
            const st=roomStatus(d);
            const card=document.createElement('div');
            card.className=`rc card ${borderCls(st)}`;
            card.dataset.id=d.id; card.dataset.name=(d.name||'').toLowerCase(); card.dataset.nr=(d.nr||'').toLowerCase(); card.dataset.state=st;
            card.innerHTML=`
            <div class="rh card-body py-1 px-2" onclick="toggleCard(this.parentElement)">
                <span class="text-secondary font-monospace" style="font-size:.68rem">${escH(d.nr)}</span>
                <span class="fw-semibold" style="font-size:.8rem;padding-right:8px">${escH(d.name)}</span>
                <div class="rh-av">${rhCell(d[`el_AV_${m}`],  d.rp_AV,  'var(--c-av)')}</div>
                <div class="rh-sv">${rhCell(d[`el_SV_${m}`],  d.rp_SV,  'var(--c-sv)')}</div>
                <div class="rh-zsv">${rhCell(d[`el_ZSV_${m}`],d.rp_ZSV,'var(--c-zsv)')}</div>
                <div class="rh-usv">${rhCell(d[`el_USV_${m}`],d.rp_USV,'var(--c-usv)')}</div>
                <div class="rh-sum">${rhCell(d[`el_sum_${m}`],d.rp_sum, 'var(--bs-body-color)')}</div>
                <div class="rh-abw">${rhCell(d[`el_abw_${m}`],d.rp_abw,'var(--c-abw)')}</div>
                <span class="rh-toggle text-secondary text-end" style="font-size:.7rem">▶</span>
            </div>
            <div class="rb card-body pt-0 px-3 pb-2"></div>`;
            grid.appendChild(card);
        }
        updateStats();
        applyFilters();
    }
    function toggleCard(card){
        card.classList.toggle('open');
        if(card.classList.contains('open')){
            const d=allData.find(r=>r.id===parseInt(card.dataset.id));
            if(d) card.querySelector('.rb').innerHTML=detailHtml(d);
        }
    }
    function updateStats(){
        let err=0,warn=0,ok=0;
        document.querySelectorAll('.rc').forEach(c=>{
            if(c.dataset.state==='err') err++; else if(c.dataset.state==='warn') warn++; else if(c.dataset.state==='ok') ok++;
        });
        document.getElementById('statErr').textContent  =err+' Fehler';
        document.getElementById('statWarn').textContent =warn+' Warnungen';
        document.getElementById('statOk').textContent   =ok+' OK';
    }
    function setGLZ(mode){
        glzMode=mode;
        document.getElementById('btnInkl').className='btn btn-sm '+(mode==='inkl'?'btn-success active':'btn-outline-secondary');
        document.getElementById('btnExkl').className='btn btn-sm '+(mode==='exkl'?'btn-success active':'btn-outline-secondary');
        renderAll();
    }
    function setFilter(f){
        activeFilter=f;
        const base={all:'btn-secondary',err:'btn-outline-danger',warn:'btn-outline-warning',ok:'btn-outline-success',na:'btn-outline-secondary'};
        const actv={all:'btn-secondary active',err:'btn-danger active',warn:'btn-warning active',ok:'btn-success active',na:'btn-secondary active'};
        ['all','err','warn','ok','na'].forEach(k=>{
            const btn=document.getElementById('f'+k.charAt(0).toUpperCase()+k.slice(1));
            if(btn) btn.className='btn btn-sm '+(k===f?actv[k]:base[k]);
        });
        applyFilters();
    }
    function applyFilters(){
        const q=document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.rc').forEach(card=>{
            const nm=!q||card.dataset.name.includes(q)||card.dataset.nr.includes(q);
            const fm=activeFilter==='all'||activeFilter===card.dataset.state;
            card.style.display=(nm&&fm)?'':'none';
        });
    }
    function expandAll(){
        document.querySelectorAll('.rc').forEach(c=>{
            if(!c.classList.contains('open')){
                c.classList.add('open');
                const d=allData.find(r=>r.id===parseInt(c.dataset.id));
                if(d) c.querySelector('.rb').innerHTML=detailHtml(d);
            }
        });
    }
    function collapseAll(){document.querySelectorAll('.rc').forEach(c=>c.classList.remove('open'));}

    $.ajax({
        url:'get_leistung_vergleich.php',type:'POST',dataType:'json',
        success(data){allData=data;buildCards();},
        error(){document.getElementById('grid').innerHTML='<div class="alert alert-danger m-3"><i class="fas fa-exclamation-circle me-2"></i>Fehler beim Laden der Daten.</div>';}
    });
</script>
</body>
</html>