// ══════════════════════════════════════════════════════════════════
// Konfiguration — Spalten-Mapping
// ══════════════════════════════════════════════════════════════════

const COL_PATTERNS = {
    raumnr:  ['raumnr', 'raumnummer', 'room number', 'room nr', 'raum nr'],
    familie: ['familie', 'family', 'revit', 'modell'],
    laenge:  ['länge', 'laenge', 'length', 'lfm', 'breite'],
    tiefe:   ['tiefe', 'depth', 'mt_limet_tiefe'],
    hoehe:   ['höhe', 'hoehe', 'height', 'mt_limet_höhe'],
    variante:['variante', 'variant', 'version'],
};

const MZ_PARAMS = [
    {col: 'MT_LIMET_Anzahl Steckdosen-Auslässe', label: 'Steckdosen'},
    {col: 'MT_LIMET_Anzahl EDV-Auslässe',         label: 'EDV (RJ45)'},
    {col: 'MT_LIMET_Anzahl DL-Auslässe',          label: 'DL-5'},
    {col: 'MT_LIMET_Anzahl VA-Auslässe',          label: 'VAC'},
    {col: 'MT_LIMET_Anzahl O2-Auslässe',          label: 'O₂'},
    {col: 'MT_LIMET_Anzahl CO2-Auslässe',         label: 'CO₂'},
    {col: 'MT_LIMET_Anzahl N2-Auslässe',          label: 'N₂'},
    {col: 'MT_LIMET_Sichtbarkeit ABW',            label: 'Abfluss Wand'},
    {col: 'MT_LIMET_Sichtbarkeit KW',             label: 'Kaltwasser'},
    {col: 'MT_LIMET_Sichtbarkeit VE',             label: 'VE-Wasser'},
    {col: 'MT_LIMET_Sichtbarkeit WW',             label: 'Warmwasser'},
];

const KERN_COLS = [
    {key: 'raumnr',   col: 'Raumnummer',     label: 'Raumnummer',   placeholder: '— Spalte wählen —', required: true},
    {key: 'familie',   col: 'Familie',         label: 'Familie',      placeholder: '— optional —'},
    {key: 'laenge',   col: 'MT_LIMET_Breite', label: 'Länge/Breite', placeholder: '— optional —'},
    {key: 'tiefe',    col: 'MT_LIMET_Tiefe',  label: 'Tiefe',        placeholder: '— optional —'},
    {key: 'hoehe',    col: 'MT_LIMET_Höhe',   label: 'Höhe',         placeholder: '— optional —'},
    {key: 'variante', col: 'Variante',         label: 'Variante',     placeholder: '— optional (→ auto) —'},
];

// ══════════════════════════════════════════════════════════════════
// State — Spalten-Indizes
// ══════════════════════════════════════════════════════════════════

let colIdx     = {raumnr: -1, familie: -1, laenge: -1, tiefe: -1, hoehe: -1, variante: -1};
let paramColIdx = {};

// ══════════════════════════════════════════════════════════════════
// Selects aufbauen
// ══════════════════════════════════════════════════════════════════

function buildAllSelects() {
    buildKernSpaltenRows();
    buildMzParamRows();
    updateCheckButton();
}

function buildKernSpaltenRows() {
    const container = document.getElementById('kern-spalten-rows');
    container.innerHTML = '';

    KERN_COLS.forEach(def => {
        const wrap = document.createElement('div');
        wrap.className = 'col-12 col-sm-6 col-lg-4';
        wrap.innerHTML = `
        <div class="d-flex align-items-center">
            <label class="form-label mb-0 text-nowrap small fw-semibold" style="min-width:120px;color:#495057">${def.label}:</label>
            <select id="col-${def.key}" class="form-select form-select-sm" data-kern-key="${def.key}">
                <option value="">${def.placeholder}</option>
            </select>
        </div>`;
        container.appendChild(wrap);

        const sel = wrap.querySelector('select');
        allHeaders.forEach(h => {
            const opt = document.createElement('option');
            opt.value       = h.idx;
            opt.textContent = h.label || `(leer, Spalte ${h.idx + 1})`;
            sel.appendChild(opt);
        });

        // Auto-Detection: exakter Spaltenname zuerst, dann Pattern-Fallback
        const exactH   = allHeaders.find(h => h.label === def.col);
        const patternH = !exactH && allHeaders.find(h =>
            (COL_PATTERNS[def.key] || []).some(p => h.label.toLowerCase().includes(p))
        );
        const autoH = exactH || patternH;
        if (autoH) { sel.value = autoH.idx; colIdx[def.key] = autoH.idx; }
        else        { colIdx[def.key] = -1; }

        sel.addEventListener('change', function () {
            colIdx[def.key] = this.value !== '' ? parseInt(this.value) : -1;
            if (def.key === 'raumnr')                        { updateCheckButton(); renderPreview(); }
            if (def.key === 'tiefe' || def.key === 'hoehe')  buildParamColIndex();
        });
    });
}

function buildMzParamRows() {
    const container = document.getElementById('mz-param-rows');
    container.innerHTML = '';
    MZ_PARAMS.forEach(p => {
        const safeId = 'mzcol-' + p.col.replace(/[^a-z0-9]/gi, '_');
        const col = document.createElement('div');
        col.className = 'col-12 col-sm-6 col-lg-4';
        col.innerHTML = `
        <div class="d-flex align-items-center">
            <label class="form-label mb-0 text-nowrap small" style="min-width:120px;color:#495057">${p.label}:</label>
            <select id="${safeId}" class="form-select form-select-sm" data-mz-col="${p.col}">
                <option value="">— nicht gemappt —</option>
            </select>
        </div>`;
        container.appendChild(col);

        const sel = col.querySelector('select');
        allHeaders.forEach(h => {
            const opt = document.createElement('option');
            opt.value       = h.idx;
            opt.textContent = h.label || `(leer, Spalte ${h.idx + 1})`;
            sel.appendChild(opt);
        });

        const autoH = allHeaders.find(h => h.label === p.col);
        if (autoH) sel.value = autoH.idx;
        sel.addEventListener('change', buildParamColIndex);
    });
}

function updateCheckButton() {
    document.getElementById('btn-check-rooms').disabled = colIdx.raumnr === -1;
}

function buildParamColIndex() {
    paramColIdx = {};
    if (colIdx.hoehe >= 0) paramColIdx['MT_LIMET_Höhe']  = colIdx.hoehe;
    if (colIdx.tiefe >= 0) paramColIdx['MT_LIMET_Tiefe'] = colIdx.tiefe;
    document.querySelectorAll('#mz-param-rows select[data-mz-col]').forEach(sel => {
        if (sel.value !== '') paramColIdx[sel.dataset.mzCol] = parseInt(sel.value);
    });
}

// ══════════════════════════════════════════════════════════════════
// Tabellenvorschau
// ══════════════════════════════════════════════════════════════════

function renderPreview() {
    const theadRow = document.getElementById('preview-thead-row');
    const tbody    = document.getElementById('preview-tbody');
    theadRow.innerHTML = '';
    tbody.innerHTML    = '';

    const thNr = document.createElement('th');
    thNr.textContent = '#';
    thNr.style.width = '36px';
    theadRow.appendChild(thNr);

    allHeaders.forEach(h => {
        const th = document.createElement('th');
        th.textContent = h.label || '(leer)';
        if (h.idx === colIdx.raumnr) th.classList.add('col-highlight');
        theadRow.appendChild(th);
    });

    const limit = Math.min(parsedRows.length, PREVIEW_ROWS);
    for (let i = 0; i < limit; i++) {
        const row = parsedRows[i];
        const tr  = document.createElement('tr');
        const tdNr = document.createElement('td');
        tdNr.className   = 'text-muted';
        tdNr.textContent = i + 2;
        tr.appendChild(tdNr);

        allHeaders.forEach((h, ci) => {
            const td  = document.createElement('td');
            const val = row[ci];
            td.textContent = val instanceof Date ? val.toLocaleDateString('de-AT') : (val ?? '');
            if (!td.textContent) td.style.color = '#adb5bd';
            if (ci === colIdx.raumnr) td.classList.add('col-highlight');
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    }

    document.getElementById('preview-row-count').textContent = parsedRows.length + ' Zeilen';
    document.getElementById('preview-footer').textContent = parsedRows.length > PREVIEW_ROWS
        ? `Vorschau: erste ${PREVIEW_ROWS} von ${parsedRows.length} Zeilen`
        : `${parsedRows.length} Zeilen gesamt`;
}
