// ══════════════════════════════════════════════════════════════════
// element_compare.js
// ══════════════════════════════════════════════════════════════════

// ── Single-room state ─────────────────────────────────────────────
let currentCompare = null;
let userChoices    = {}; // { "element_id|fingerprint" => variante_id }  (single-room)

// ── Batch state ───────────────────────────────────────────────────
// allRoomCompares[raum_id] = {
//   raum_id, raumnr, bezeichnung,
//   status: 'loading' | 'ok' | 'changes' | 'ambiguous' | 'error',
//   element_blocks: [...],
//   userChoices: { choiceKey => variante_id },
//   errorMsg: string|null
// }
let allRoomCompares   = {};
let batchLoadingTotal = 0;
let batchLoadingDone  = 0;

// ══════════════════════════════════════════════════════════════════
// Collect Excel rows for a room
// ══════════════════════════════════════════════════════════════════

function collectFamilienForRaum(raumnr) {
    const familien = [];
    if (colIdx.familie < 0) return familien;
    parsedRows.forEach(row => {
        const nr = String(row[colIdx.raumnr] ?? '').trim();
        if (nr !== raumnr) return;
        const fam = String(row[colIdx.familie] ?? '').trim();
        if (!fam) return;
        const laenge   = colIdx.laenge   >= 0 ? String(row[colIdx.laenge]   ?? '').trim() : '';
        const tiefe    = colIdx.tiefe    >= 0 ? String(row[colIdx.tiefe]    ?? '').trim() : '';
        const variante = colIdx.variante >= 0 ? String(row[colIdx.variante] ?? '').trim() : '';
        const params   = {};
        Object.entries(paramColIdx).forEach(([col, idx]) => { params[col] = String(row[idx] ?? '').trim(); });
        familien.push({ familie: fam, laenge, tiefe, variante, params });
    });
    return familien;
}

// ══════════════════════════════════════════════════════════════════
// Single-room compare (used by "Abgleichen" buttons + after choice)
// ══════════════════════════════════════════════════════════════════

function compareRoom(raum_id, raumnr, bezeichnung) {
    const familien = collectFamilienForRaum(raumnr);

    const section = document.getElementById('compare-section');
    section.style.display = 'block';
    document.getElementById('compare-room-label').textContent = `— ${raumnr}  ${bezeichnung}`;
    document.getElementById('compare-blocks-container').innerHTML = `
        <div class="text-center text-muted py-4">
            <div class="spinner-border spinner-border-sm me-2"></div>Lade Elementabgleich…
        </div>`;
    document.getElementById('sync-card').style.display = 'none';
    section.scrollIntoView({ behavior: 'smooth' });

    $.ajax({
        url: API_COMPARE,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ raum_id, familien, user_choices: userChoices }),
        success: res => {
            currentCompare = { raum_id, raumnr, bezeichnung, ...res };
            renderCompare(res);
        },
        error: xhr => alertApiError(xhr),
    });
}

// Called when user clicks a candidate button in single-room view
function chooseVariante(choiceKey, varianteId, raum_id, raumnr, bezeichnung) {
    userChoices[choiceKey] = varianteId;
    compareRoom(raum_id, raumnr, bezeichnung);
}

// ══════════════════════════════════════════════════════════════════
// Single-room render (unchanged from original)
// ══════════════════════════════════════════════════════════════════

function renderCompare(res) {
    const container = document.getElementById('compare-blocks-container');
    container.innerHTML = '';

    const unmappedDiv = document.getElementById('unmapped-section');
    if (res.unmapped_familien?.length) {
        unmappedDiv.style.display = 'block';
        document.getElementById('unmapped-list').innerHTML =
            res.unmapped_familien.map(u =>
                `<span class="badge bg-secondary me-1 mb-1"
                       data-familie="${esc(u.familie)}" data-laenge="${esc(u.laenge || '')}">
                    ${esc(u.familie)}${u.laenge ? ' · ' + u.laenge : ''} <strong>×${u.anzahl}</strong>
                </span>`
            ).join('');
    } else {
        unmappedDiv.style.display = 'none';
    }

    if (!res.element_blocks?.length) {
        container.innerHTML = '<div class="alert alert-info small mt-2">Keine Elemente gefunden.</div>';
        return;
    }

    const hasAmbiguous = res.element_blocks.some(b => b.comparison.some(c => c.status === 'ambiguous'));
    let totalActionable = 0;

    res.element_blocks.forEach(block => {
        const changes = block.comparison.filter(c => ['diff_anzahl', 'nur_excel', 'nur_db', 'ambiguous'].includes(c.status)).length;
        totalActionable += changes;
        container.appendChild(buildElementBlock(block));
    });

    if (hasAmbiguous) {
        document.getElementById('sync-card').style.display = 'none';
        const notice = document.createElement('div');
        notice.className = 'alert alert-warning small mt-2';
        notice.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i><strong>Auswahl nötig:</strong> Bitte alle orangen Zeilen durch Variantenauswahl auflösen, bevor der Sync möglich ist.';
        container.appendChild(notice);
    } else if (totalActionable > 0) {
        updateSyncSummary(res.element_blocks);
        document.getElementById('sync-card').style.display = 'block';
    } else {
        document.getElementById('sync-card').style.display = 'none';
    }
}

// ══════════════════════════════════════════════════════════════════
// BATCH COMPARE
// ══════════════════════════════════════════════════════════════════

function resetBatchState() {
    allRoomCompares   = {};
    batchLoadingTotal = 0;
    batchLoadingDone  = 0;
}

async function startBatchCompare(foundRooms) {
    resetBatchState();

    const card = document.getElementById('batch-compare-card');
    card.style.display = 'block';
    card.scrollIntoView({ behavior: 'smooth' });

    // Initialise state for all rooms
    foundRooms.forEach(r => {
        allRoomCompares[r.raum_id] = {
            raum_id:       r.raum_id,
            raumnr:        r.raumnr,
            bezeichnung:   r.bezeichnung,
            status:        'loading',
            element_blocks: [],
            userChoices:   {},
            errorMsg:      null,
        };
    });

    batchLoadingTotal = foundRooms.length;
    batchLoadingDone  = 0;

    // Show progress, hide sync button
    document.getElementById('batch-progress-wrap').style.display = '';
    document.getElementById('btn-batch-sync').style.display = 'none';
    document.getElementById('batch-sync-kommentar-wrap').style.display = 'none';
    document.getElementById('batch-sync-result').style.display = 'none';
    document.getElementById('batch-stats-pills').innerHTML = '';

    renderBatchRooms(); // show skeleton rows

    // Fire all requests in parallel
    const promises = foundRooms.map(r => fetchBatchRoom(r));
    await Promise.allSettled(promises);

    // Done loading
    document.getElementById('batch-progress-wrap').style.display = 'none';
    renderBatchRooms();
    updateBatchStats();
    updateBatchSyncButton();
}

async function fetchBatchRoom(room) {
    const familien = collectFamilienForRaum(room.raumnr);
    const state    = allRoomCompares[room.raum_id];

    try {
        const response = await fetch(API_COMPARE, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                raum_id:      room.raum_id,
                familien,
                user_choices: state.userChoices,
            }),
        });
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const res = await response.json();
        if (res.error) throw new Error(res.error);

        state.element_blocks = res.element_blocks ?? [];
        state.status = computeBatchRoomStatus(state.element_blocks);
    } catch (e) {
        state.status   = 'error';
        state.errorMsg = e.message;
    }

    batchLoadingDone++;
    updateBatchProgress();
    // Re-render just this room's row
    renderBatchRoomRow(room.raum_id);
    updateBatchStats();
    updateBatchSyncButton();
}

function computeBatchRoomStatus(element_blocks) {
    let hasAmbiguous = false, hasChanges = false;
    for (const block of element_blocks) {
        for (const c of block.comparison) {
            if (c.status === 'ambiguous')   hasAmbiguous = true;
            if (['diff_anzahl', 'nur_excel', 'nur_db'].includes(c.status)) hasChanges = true;
        }
    }
    if (hasAmbiguous) return 'ambiguous';
    if (hasChanges)   return 'changes';
    return 'ok';
}

// ── Progress bar ──────────────────────────────────────────────────

function updateBatchProgress() {
    const pct = batchLoadingTotal ? Math.round(batchLoadingDone / batchLoadingTotal * 100) : 0;
    document.getElementById('batch-progress-bar').style.width = pct + '%';
    document.getElementById('batch-progress-label').textContent = 'Lade Räume…';
    document.getElementById('batch-progress-count').textContent = `${batchLoadingDone} / ${batchLoadingTotal}`;
}

// ── Stats pills ───────────────────────────────────────────────────

function updateBatchStats() {
    let cOk = 0, cChanges = 0, cAmbig = 0, cErr = 0, cLoading = 0;
    Object.values(allRoomCompares).forEach(s => {
        if (s.status === 'ok')        cOk++;
        else if (s.status === 'changes')   cChanges++;
        else if (s.status === 'ambiguous') cAmbig++;
        else if (s.status === 'error')     cErr++;
        else                               cLoading++;
    });

    const pills = [];
    if (cLoading)  pills.push(`<span class="status-pill-batch bg-secondary bg-opacity-10 text-secondary border"><i class="fas fa-spinner fa-spin fa-xs"></i> ${cLoading} laden</span>`);
    if (cOk)       pills.push(`<span class="status-pill-batch bg-success bg-opacity-10 text-success border"><i class="fas fa-check fa-xs"></i> ${cOk} ok</span>`);
    if (cChanges)  pills.push(`<span class="status-pill-batch bg-warning bg-opacity-10 text-warning-emphasis border"><i class="fas fa-pen fa-xs"></i> ${cChanges} Änderungen</span>`);
    if (cAmbig)    pills.push(`<span class="status-pill-batch bg-danger bg-opacity-10 text-danger border"><i class="fas fa-question fa-xs"></i> ${cAmbig} Auswahl nötig</span>`);
    if (cErr)      pills.push(`<span class="status-pill-batch bg-danger bg-opacity-10 text-danger border"><i class="fas fa-exclamation-circle fa-xs"></i> ${cErr} Fehler</span>`);

    document.getElementById('batch-stats-pills').innerHTML = pills.join('');
}

// ── Sync button visibility ────────────────────────────────────────

function updateBatchSyncButton() {
    const states    = Object.values(allRoomCompares);
    const allLoaded = states.every(s => s.status !== 'loading');
    if (!allLoaded) return;

    const hasAmbiguous = states.some(s => s.status === 'ambiguous');
    const hasChanges   = states.some(s => s.status === 'changes');

    const btn  = document.getElementById('btn-batch-sync');
    const wrap = document.getElementById('batch-sync-kommentar-wrap');

    if (hasChanges && !hasAmbiguous) {
        btn.style.display = '';
        btn.disabled      = false;
        wrap.style.display = '';
    } else if (hasChanges && hasAmbiguous) {
        btn.style.display = '';
        btn.disabled      = true;
        btn.title         = 'Bitte zuerst alle Auswahlen treffen';
        wrap.style.display = '';
    } else {
        btn.style.display  = 'none';
        wrap.style.display = 'none';
    }
}

// ══════════════════════════════════════════════════════════════════
// BATCH RENDER — room list
// ══════════════════════════════════════════════════════════════════

function renderBatchRooms() {
    const container = document.getElementById('batch-rooms-container');
    container.innerHTML = '';
    Object.values(allRoomCompares).forEach(state => {
        container.appendChild(buildBatchRoomRow(state));
    });
}

function renderBatchRoomRow(raum_id) {
    const container = document.getElementById('batch-rooms-container');
    const existing  = document.getElementById(`batch-row-${raum_id}`);
    const newEl     = buildBatchRoomRow(allRoomCompares[raum_id]);
    if (existing) container.replaceChild(newEl, existing);
    else          container.appendChild(newEl);
}

function buildBatchRoomRow(state) {
    const wrap = document.createElement('div');
    wrap.id = `batch-row-${state.raum_id}`;

    // ── Status pill ───────────────────────────────────────────────
    let pillHtml = '';
    if (state.status === 'loading') {
        pillHtml = `<span class="status-pill-batch bg-secondary bg-opacity-10 text-secondary border">
            <span class="spinner-border spinner-border-sm" style="width:.6rem;height:.6rem"></span> Lädt…
        </span>`;
    } else if (state.status === 'ok') {
        pillHtml = `<span class="status-pill-batch bg-success bg-opacity-10 text-success border">
            <i class="fas fa-check fa-xs"></i> ok
        </span>`;
    } else if (state.status === 'changes') {
        // Count changes
        let cAdd = 0, cRemove = 0, cUpdate = 0;
        state.element_blocks.forEach(b => b.comparison.forEach(c => {
            if (c.status === 'nur_excel')   cAdd++;
            if (c.status === 'nur_db')      cRemove++;
            if (c.status === 'diff_anzahl') cUpdate++;
        }));
        const parts = [];
        if (cAdd)    parts.push(`<i class="fas fa-plus fa-xs text-primary"></i> ${cAdd}`);
        if (cRemove) parts.push(`<i class="fas fa-minus fa-xs text-danger"></i> ${cRemove}`);
        if (cUpdate) parts.push(`<i class="fas fa-pen fa-xs text-warning-emphasis"></i> ${cUpdate}`);
        pillHtml = `<span class="status-pill-batch bg-warning bg-opacity-10 text-warning-emphasis border">${parts.join(' &nbsp;')}</span>`;
    } else if (state.status === 'ambiguous') {
        const n = state.element_blocks.reduce((s, b) => s + b.comparison.filter(c => c.status === 'ambiguous').length, 0);
        pillHtml = `<span class="status-pill-batch bg-danger bg-opacity-10 text-danger border">
            <i class="fas fa-question fa-xs"></i> ${n} Auswahl nötig
        </span>`;
    } else if (state.status === 'error') {
        pillHtml = `<span class="status-pill-batch bg-danger bg-opacity-10 text-danger border">
            <i class="fas fa-exclamation-circle fa-xs"></i> Fehler
        </span>`;
    }

    // ── Toggle button ─────────────────────────────────────────────
    const canExpand = state.status !== 'loading' && state.status !== 'ok';
    const detailId  = `batch-detail-${state.raum_id}`;
    const toggleBtn = canExpand
        ? `<button class="btn btn-outline-secondary batch-toggle-btn py-0" onclick="toggleBatchDetail(${state.raum_id}, this)">
               <i class="fas fa-chevron-down fa-xs me-1"></i>Details
           </button>`
        : '';

    // ── Row HTML ──────────────────────────────────────────────────
    const row = document.createElement('div');
    row.className = 'batch-room-row';
    row.innerHTML = `
        <div class="text-center text-muted" style="font-size:.75rem">
            ${state.status === 'ok' ? '<i class="fas fa-check-circle text-success"></i>'
        : state.status === 'error' ? '<i class="fas fa-exclamation-circle text-danger"></i>'
            : state.status === 'loading' ? '<span class="spinner-border spinner-border-sm text-secondary" style="width:.75rem;height:.75rem"></span>'
                : state.status === 'ambiguous' ? '<i class="fas fa-question-circle text-warning"></i>'
                    : '<i class="fas fa-pen text-warning"></i>'}
        </div>
        <div class="room-label">
            <span class="room-nr">${esc(state.raumnr)}</span>
            <span class="room-bez">${esc(state.bezeichnung)}</span>
            ${state.errorMsg ? `<div class="text-danger small">${esc(state.errorMsg)}</div>` : ''}
        </div>
        <div>${pillHtml}</div>
        <div>${toggleBtn}</div>`;
    wrap.appendChild(row);

    // ── Detail panel (collapsed by default) ──────────────────────
    if (canExpand) {
        const detail = document.createElement('div');
        detail.id        = detailId;
        detail.className = 'batch-detail-panel';
        detail.style.display = 'none';
        detail.appendChild(buildBatchDetailPanel(state));
        wrap.appendChild(detail);
    }

    return wrap;
}

function toggleBatchDetail(raum_id, btn) {
    const detailId = `batch-detail-${raum_id}`;
    const el = document.getElementById(detailId);
    if (!el) return;
    const open = el.style.display !== 'none';
    el.style.display = open ? 'none' : 'block';
    btn.innerHTML = open
        ? '<i class="fas fa-chevron-down fa-xs me-1"></i>Details'
        : '<i class="fas fa-chevron-up fa-xs me-1"></i>Zuklappen';
}

// ══════════════════════════════════════════════════════════════════
// BATCH DETAIL PANEL (inside each room row)
// ══════════════════════════════════════════════════════════════════

function buildBatchDetailPanel(state) {
    const wrap = document.createElement('div');

    if (state.status === 'error') {
        wrap.innerHTML = `<div class="alert alert-danger small mb-0">${esc(state.errorMsg)}</div>`;
        return wrap;
    }

    // ── Ambiguous blocks first — need user interaction ─────────────
    const ambiguousRows = [];
    state.element_blocks.forEach(block => {
        block.comparison
            .filter(c => c.status === 'ambiguous')
            .forEach(c => ambiguousRows.push({ block, row: c }));
    });

    if (ambiguousRows.length) {
        const ambigWrap = document.createElement('div');
        ambigWrap.className = 'mb-2';
        ambigWrap.innerHTML = `<div class="small fw-semibold text-warning-emphasis mb-1">
            <i class="fas fa-question-circle me-1"></i>Auswahl erforderlich — bitte Variante wählen:
        </div>`;

        ambiguousRows.forEach(({ block, row }) => {
            const box = document.createElement('div');
            box.className = 'ambiguous-inline-wrap';
            box.id = `ambig-${state.raum_id}-${row.choice_key?.replace(/[^a-z0-9]/gi,'_')}`;

            const famShort = (row.familie || '').length > 50
                ? row.familie.substring(0, 48) + '…'
                : (row.familie || '—');

            let btns = '';
            row.candidates.forEach(c => {
                const ignoreEntries = Object.entries(c.params).filter(([, p]) => p.role === 'ignore');
                const label = ignoreEntries.length
                    ? ignoreEntries.map(([, p]) => `${p.bezeichnung}: ${p.wert}`).join(' / ')
                    : `Var ${c.variante_letter}`;
                btns += `<button class="btn btn-outline-secondary cand-btn me-1 mb-1"
                    onclick="chooseBatchVariante('${state.raum_id}', '${esc(row.choice_key)}', ${c.variante_id})">
                    <span class="badge bg-secondary me-1">Var ${esc(c.variante_letter)}</span>${esc(label)}
                </button>`;
            });

            box.innerHTML = `
                <div class="text-muted mb-1" style="font-size:.72rem">
                    <code style="font-size:.72rem">${esc(block.element_id)}</code>
                    &nbsp;·&nbsp; ${esc(famShort)}
                    ${row.laenge_raw ? `&nbsp;· B: ${esc(row.laenge_raw)}` : ''}
                    &nbsp;·&nbsp; Excel: <strong>${row.excel_anzahl}</strong>
                </div>
                <div>${btns}</div>`;
            ambigWrap.appendChild(box);
        });
        wrap.appendChild(ambigWrap);
    }

    // ── Changes summary table ─────────────────────────────────────
    const changeRows = [];
    state.element_blocks.forEach(block => {
        block.comparison
            .filter(c => ['diff_anzahl', 'nur_excel', 'nur_db'].includes(c.status))
            .forEach(c => changeRows.push({ block, row: c }));
    });

    if (changeRows.length) {
        const table = document.createElement('table');
        table.className = 'table table-sm table-hover mini-compare-table mb-0';
        table.innerHTML = `
            <thead class="table-light">
                <tr>
                    <th style="width:22px"></th>
                    <th>Element / Familie</th>
                    <th class="text-center" style="width:50px">DB</th>
                    <th class="text-center" style="width:50px">Excel</th>
                    <th>Aktion</th>
                </tr>
            </thead>`;
        const tbody = document.createElement('tbody');
        changeRows.forEach(({ block, row }) => {
            const tr = document.createElement('tr');
            let icon = '', actionBadge = '';
            if (row.status === 'nur_excel') {
                icon = '<i class="fas fa-plus-circle text-primary"></i>';
                actionBadge = `<span class="badge bg-primary" style="font-size:.65rem">Hinzufügen${row.needs_new_variante ? ' + neue Var' : ''}</span>`;
            } else if (row.status === 'nur_db') {
                icon = '<i class="fas fa-minus-circle text-danger"></i>';
                actionBadge = `<span class="badge bg-danger" style="font-size:.65rem">Auf 0 setzen</span>`;
            } else if (row.status === 'diff_anzahl') {
                icon = '<i class="fas fa-not-equal text-warning"></i>';
                actionBadge = `<span class="badge bg-warning text-dark" style="font-size:.65rem">→ ${row.excel_anzahl}</span>`;
            }
            const famShort = (row.familie || '').length > 40
                ? row.familie.substring(0, 38) + '…' : (row.familie || '—');

            tr.innerHTML = `
                <td class="text-center">${icon}</td>
                <td>
                    <div style="font-size:.72rem"><code style="font-size:.7rem">${esc(block.element_id)}</code></div>
                    <div class="text-muted" style="font-size:.7rem" title="${esc(row.familie || '')}">${esc(famShort)}</div>
                    ${row.variante_letter && row.variante_letter !== '—' ? `<span class="badge bg-secondary" style="font-size:.62rem">Var ${esc(row.variante_letter)}</span>` : ''}
                </td>
                <td class="text-center">${row.db_anzahl > 0 ? `<strong>${row.db_anzahl}</strong>` : '<span class="text-muted">—</span>'}</td>
                <td class="text-center">${row.excel_anzahl > 0 ? `<strong>${row.excel_anzahl}</strong>` : '<span class="text-muted">—</span>'}</td>
                <td>${actionBadge}</td>`;
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        wrap.appendChild(table);
    }

    if (!ambiguousRows.length && !changeRows.length) {
        wrap.innerHTML = '<div class="text-muted small py-2 px-1">Keine Änderungen.</div>';
    }

    return wrap;
}

// ══════════════════════════════════════════════════════════════════
// Ambiguous choice in batch mode
// ══════════════════════════════════════════════════════════════════

async function chooseBatchVariante(raum_id_str, choiceKey, varianteId) {
    const raum_id = parseInt(raum_id_str);
    const state   = allRoomCompares[raum_id];
    if (!state) return;

    // Save choice and re-fetch this room
    state.userChoices[choiceKey] = varianteId;
    state.status = 'loading';

    renderBatchRoomRow(raum_id);
    updateBatchStats();
    updateBatchSyncButton();

    const familien = collectFamilienForRaum(state.raumnr);
    try {
        const response = await fetch(API_COMPARE, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                raum_id,
                familien,
                user_choices: state.userChoices,
            }),
        });
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const res = await response.json();
        if (res.error) throw new Error(res.error);

        state.element_blocks = res.element_blocks ?? [];
        state.status         = computeBatchRoomStatus(state.element_blocks);
        state.errorMsg       = null;
    } catch (e) {
        state.status   = 'error';
        state.errorMsg = e.message;
    }

    renderBatchRoomRow(raum_id);
    updateBatchStats();
    updateBatchSyncButton();
}

// ══════════════════════════════════════════════════════════════════
// BATCH SYNC
// ══════════════════════════════════════════════════════════════════

document.getElementById('btn-batch-sync').addEventListener('click', async function () {
    const kommentar = document.getElementById('batch-sync-kommentar').value.trim()
        || 'Entfernt via Excel-Abgleich';

    // Collect all actions from all rooms
    const actions = [];
    Object.values(allRoomCompares).forEach(state => {
        if (state.status !== 'changes') return;
        const roomActions = buildSyncActions(state.element_blocks, state.raum_id, kommentar);
        actions.push(...roomActions);
    });

    if (!actions.length) { alert('Keine Änderungen zu übernehmen.'); return; }

    this.disabled = true;
    this.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Übernehme…`;

    try {
        const response = await fetch(API_SYNC, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ actions }),
        });
        const res = await response.json();

        const resultEl = document.getElementById('batch-sync-result');
        resultEl.style.display = 'block';

        if (res.ok) {
            resultEl.className = 'alert alert-success small mx-3 mt-2';
            resultEl.innerHTML = `<i class="fas fa-check-circle me-1"></i>
                <strong>${res.success}</strong> Änderungen erfolgreich übernommen (${Object.values(allRoomCompares).filter(s => s.status === 'changes').length} Räume).`;

            // Re-fetch all affected rooms so status updates to 'ok'
            const affectedRooms = Object.values(allRoomCompares).filter(s => s.status === 'changes');
            affectedRooms.forEach(s => { s.status = 'loading'; renderBatchRoomRow(s.raum_id); });
            updateBatchStats();

            await Promise.allSettled(affectedRooms.map(s => fetchBatchRoom(s)));

            document.getElementById('btn-batch-sync').style.display = 'none';
            document.getElementById('batch-sync-kommentar-wrap').style.display = 'none';

            if (res.warnings?.length) {
                resultEl.innerHTML += '<ul class="mb-0 mt-1">' +
                    res.warnings.map(w => `<li><i class="fas fa-exclamation-triangle text-warning me-1"></i>${esc(w)}</li>`).join('') +
                    '</ul>';
            }
        } else {
            resultEl.className = 'alert alert-warning small mx-3 mt-2';
            resultEl.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i>
                <strong>${res.success}</strong> OK, <strong>${res.errors}</strong> Fehler.`;
            this.disabled = false;
            this.innerHTML = `<i class="fas fa-database me-1"></i>Alle Änderungen übernehmen`;
        }
    } catch (e) {
        const resultEl = document.getElementById('batch-sync-result');
        resultEl.style.display  = 'block';
        resultEl.className      = 'alert alert-danger small mx-3 mt-2';
        resultEl.innerHTML      = `<i class="fas fa-exclamation-circle me-1"></i>${esc(e.message)}`;
        this.disabled           = false;
        this.innerHTML          = `<i class="fas fa-database me-1"></i>Alle Änderungen übernehmen`;
    }
});

// ══════════════════════════════════════════════════════════════════
// Single-room element block builders (unchanged from original)
// ══════════════════════════════════════════════════════════════════

function buildElementBlock(block) {
    const card = document.createElement('div');
    card.className = 'card mb-2 shadow-sm';

    const changes     = block.comparison.filter(c => ['diff_anzahl', 'nur_excel', 'nur_db'].includes(c.status)).length;
    const ambiguous   = block.comparison.filter(c => c.status === 'ambiguous').length;

    let statusBadge = '';
    if (ambiguous)   statusBadge += `<span class="badge bg-warning text-dark ms-1"><i class="fas fa-question me-1"></i>${ambiguous} Auswahl nötig</span>`;
    if (changes)     statusBadge += `<span class="badge bg-warning text-dark ms-1">${changes} Änderung${changes !== 1 ? 'en' : ''}</span>`;
    if (!ambiguous && !changes) statusBadge = `<span class="badge bg-success ms-1"><i class="fas fa-check me-1"></i>ok</span>`;

    const varAWarn = block.var_a_integrity_warn
        ? `<span class="badge bg-danger ms-1" title="Var A hat fälschlicherweise Parameter — wird beim Import automatisch korrigiert">
               <i class="fas fa-exclamation-circle me-1"></i>Var A Integritätsproblem
           </span>`
        : '';

    const varScheme = block.has_variante_params
        ? `<span class="badge bg-info bg-opacity-10 text-info border ms-2" style="font-size:.7rem">
               <i class="fas fa-sliders-h me-1"></i>Variante nach: ${esc(block.variante_param_names.join(', '))}
           </span>`
        : `<span class="badge bg-secondary bg-opacity-25 text-secondary border ms-2" style="font-size:.7rem">
               <i class="fas fa-lock me-1"></i>Var A — DB-Parameter ignoriert
           </span>`;

    const managedBadge = !block.is_managed
        ? `<span class="badge bg-light text-muted border ms-1" style="font-size:.7rem">nicht verwaltet</span>`
        : '';

    const panelId = `variants-${block.element_id.replace(/\./g, '_')}`;
    const header = document.createElement('div');
    header.className = 'card-header py-2';
    header.innerHTML = `
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
        <div class="d-flex align-items-center flex-wrap gap-1">
            <code style="font-size:.82rem;background:#f1f3f5;padding:2px 6px;border-radius:4px">${esc(block.element_id)}</code>
            <span class="fw-semibold">${esc(block.bezeichnung)}</span>
            ${statusBadge}${varAWarn}${varScheme}${managedBadge}
        </div>
        <button class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="togglePanel('${panelId}', this)">
            <i class="fas fa-table fa-xs me-1"></i>DB-Varianten
        </button>
    </div>`;
    card.appendChild(header);

    const varPanel = document.createElement('div');
    varPanel.id = panelId;
    varPanel.className = 'border-bottom';
    varPanel.style.display = 'none';
    varPanel.appendChild(buildVariantPanel(block));
    card.appendChild(varPanel);

    const tableWrap = document.createElement('div');
    tableWrap.className = 'table-responsive';
    const table = document.createElement('table');
    table.className = 'table table-sm table-hover mb-0 align-middle';
    table.innerHTML = `
    <thead class="table-light">
        <tr>
            <th style="width:28px"></th>
            <th style="min-width:130px">Variante</th>
            <th class="text-center" style="width:55px">DB</th>
            <th class="text-center" style="width:55px">Excel</th>
            <th style="min-width:180px">Mapping / Familie</th>
            <th style="min-width:180px">Parameter (Excel)</th>
            <th style="min-width:130px">Aktion / Info</th>
        </tr>
    </thead>`;
    const tbody = document.createElement('tbody');
    block.comparison.forEach(row => tbody.appendChild(buildComparisonRow(row, block)));
    table.appendChild(tbody);
    tableWrap.appendChild(table);
    card.appendChild(tableWrap);
    return card;
}

function buildVariantPanel(block) {
    const wrap = document.createElement('div');
    wrap.className = 'p-3 bg-light';
    const title = document.createElement('div');
    title.className = 'fw-semibold small text-muted mb-2';
    title.innerHTML = `<i class="fas fa-database me-1"></i>Projektweite Varianten für <code>${esc(block.element_id)}</code>`;
    wrap.appendChild(title);

    const excelNewRows = block.comparison.filter(c => c.status === 'nur_excel' && !c.variante_id);

    if (!block.db_variants?.length && !excelNewRows.length) {
        wrap.innerHTML += `<div class="text-muted small">Keine Varianten im Projekt.</div>`;
        return wrap;
    }

    const table = document.createElement('table');
    table.className = 'table table-sm table-borderless mb-0';
    table.style.fontSize = '.8rem';
    table.innerHTML = `<thead><tr class="text-muted">
        <th style="width:60px">Var</th>
        <th style="width:115px">Status</th>
        <th>Varianten-Parameter (DB)</th>
        <th>Excel → wird geschrieben</th>
        <th>Nicht Importiert</th>
        <th style="width:60px" class="text-center">Anz.</th>
    </tr></thead>`;

    const tbody = document.createElement('tbody');

    block.db_variants.forEach(v => {
        const tr = document.createElement('tr');
        const vpEntries = Object.entries(v.params).filter(([, p]) => p.role === 'variante');
        const ipEntries = Object.entries(v.params).filter(([, p]) => p.role === 'ignore');

        const vpHtml = vpEntries.length
            ? vpEntries.map(([, p]) =>
                `<span class="badge bg-primary bg-opacity-10 text-primary border me-1" style="font-size:.7rem">
                    ${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                </span>`).join('')
            : block.has_variante_params
                ? '<span class="text-muted small">keine</span>'
                : '<span class="text-success small"><i class="fas fa-check me-1"></i>parameterlos</span>';

        const ipHtml = ipEntries.length
            ? ipEntries.map(([, p]) =>
                `<span class="badge bg-secondary bg-opacity-15 text-white border me-1" style="font-size:.7rem">
                    <i class="fas fa-eye-slash me-1"></i>${esc(p.bezeichnung)}: ${esc(String(p.wert))}
                </span>`).join('')
            : '<span class="text-muted small">—</span>';

        const compRow = block.comparison.find(c => c.variante_id === v.variante_id);
        const excelParams = compRow?.excel_params ?? {};
        const excelParamHtml = Object.values(excelParams).length
            ? Object.values(excelParams).map(p =>
                `<span class="badge me-1" style="font-size:.7rem;background:rgba(25,135,84,.08);color:#198754;border:1px solid #a3cfbb">
                    <i class="fas fa-file-excel fa-xs me-1"></i>${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                </span>`).join('')
            : '<span class="text-muted small">—</span>';

        let statusHtml = '';
        if      (compRow?.status === 'match')       statusHtml = `<span class="badge bg-success bg-opacity-10 text-success border">✓ match</span>`;
        else if (compRow?.status === 'diff_anzahl') statusHtml = `<span class="badge bg-warning text-dark border">Anz. diff</span>`;
        else if (compRow?.status === 'nur_excel')   statusHtml = `<span class="badge bg-primary bg-opacity-10 text-primary border">hinzufügen</span>`;
        else if (compRow?.status === 'nur_db')      statusHtml = `<span class="badge bg-danger bg-opacity-10 text-danger border">nur DB</span>`;
        else if (v.in_room)                         statusHtml = `<span class="badge bg-secondary bg-opacity-10 text-secondary border">im Raum</span>`;
        else                                        statusHtml = `<span class="badge bg-light text-muted border">—</span>`;
        if (v.var_a_integrity_warn) statusHtml += `<span class="badge bg-danger ms-1" style="font-size:.65rem"><i class="fas fa-exclamation-circle"></i></span>`;

        tr.innerHTML = `
            <td><span class="badge bg-secondary">Var ${esc(v.variante_letter)}</span></td>
            <td>${statusHtml}</td>
            <td>${vpHtml}</td>
            <td>${excelParamHtml}</td>
            <td>${ipHtml}</td>
            <td class="text-center">${v.in_room ? v.db_anzahl : '—'}</td>`;
        tbody.appendChild(tr);
    });

    excelNewRows.forEach(row => {
        const tr = document.createElement('tr');
        tr.style.background = 'rgba(13,110,253,.03)';
        const exHtml = Object.values(row.excel_params ?? {}).length
            ? Object.values(row.excel_params).map(p =>
                `<span class="badge me-1" style="font-size:.7rem;background:rgba(13,110,253,.08);color:#0d6efd;border:1px solid #9ec5fe">
                    ${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                </span>`).join('')
            : '<span class="text-muted small">—</span>';
        tr.innerHTML = `
            <td><span class="badge bg-primary">neu</span></td>
            <td><span class="badge" style="background:rgba(13,110,253,.1);color:#0d6efd;border:1px solid #9ec5fe"><i class="fas fa-plus fa-xs me-1"></i>wird angelegt</span></td>
            <td><span class="text-muted small">—</span></td>
            <td>${exHtml}</td>
            <td><span class="text-muted small">—</span></td>
            <td class="text-center"><strong>${row.excel_anzahl}</strong></td>`;
        tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    wrap.appendChild(table);
    return wrap;
}

function buildComparisonRow(row, block) {
    const STATUS = {
        match:       { icon: 'fa-check-circle text-success',    rowCls: '',                            label: '—' },
        diff_anzahl: { icon: 'fa-not-equal text-warning',       rowCls: 'table-warning',               label: 'Anzahl ändern' },
        nur_excel:   { icon: 'fa-plus-circle text-primary',     rowCls: 'table-primary bg-opacity-10', label: 'Hinzufügen' },
        nur_db:      { icon: 'fa-minus-circle text-danger',     rowCls: 'table-danger bg-opacity-10',  label: 'Auf 0 setzen' },
        ambiguous:   { icon: 'fa-question-circle text-warning', rowCls: 'table-warning',               label: 'Variante wählen' },
        not_managed: { icon: 'fa-info-circle text-secondary',   rowCls: '',                            label: 'nicht angepasst' },
    };
    const s = STATUS[row.status] || STATUS.match;

    const tr = document.createElement('tr');
    if (s.rowCls) tr.className = s.rowCls;

    const tdIcon = document.createElement('td');
    tdIcon.className = 'text-center';
    tdIcon.innerHTML = `<i class="fas ${s.icon}"></i>`;
    tr.appendChild(tdIcon);

    const tdVar = document.createElement('td');
    const varLetter = esc(row.variante_letter || '—');
    let varHtml = '';
    if (row.status === 'ambiguous') {
        varHtml = `<span class="badge bg-warning text-dark">? wählen</span>`;
    } else if (row.needs_new_variante) {
        varHtml = `<span class="badge bg-primary">Var (neu)</span>`;
    } else {
        varHtml = `<span class="badge bg-secondary">Var ${varLetter}</span>`;
    }
    if (row.variante_label && row.variante_label !== '—' && row.status !== 'ambiguous') {
        varHtml += `<div class="text-muted mt-1" style="font-size:.72rem">${esc(row.variante_label)}</div>`;
    }
    tdVar.innerHTML = varHtml;
    tr.appendChild(tdVar);

    const tdDb = document.createElement('td');
    tdDb.className = 'text-center';
    tdDb.innerHTML = row.db_anzahl > 0 ? `<strong>${row.db_anzahl}</strong>` : '<span class="text-muted">—</span>';
    tr.appendChild(tdDb);

    const tdEx = document.createElement('td');
    tdEx.className = 'text-center';
    tdEx.innerHTML = row.excel_anzahl > 0 ? `<strong>${row.excel_anzahl}</strong>` : '<span class="text-muted">—</span>';
    tr.appendChild(tdEx);

    const tdMap = document.createElement('td');
    if (row.familie) {
        let html = '';
        const famShort = row.familie.length > 42 ? row.familie.substring(0, 40) + '…' : row.familie;
        html += `<div class="text-muted" style="font-size:.72rem" title="${esc(row.familie)}">${esc(famShort)}</div>`;
        if (row.laenge_raw || row.tiefe_raw) {
            html += '<div class="mt-1">';
            if (row.laenge_raw) html += `<span class="badge bg-light text-dark border me-1" style="font-size:.7rem">B: ${esc(row.laenge_raw)}</span>`;
            if (row.tiefe_raw)  html += `<span class="badge bg-light text-dark border me-1" style="font-size:.7rem">T: ${esc(row.tiefe_raw)}</span>`;
            if (row.laenge_cm)  html += `<span class="badge bg-secondary bg-opacity-25 text-dark border ms-1" style="font-size:.7rem">→ ${row.laenge_cm}cm</span>`;
            html += '</div>';
        }
        if (row.debug) {
            html += `<div class="text-muted mt-1" style="font-size:.68rem"><i class="fas fa-info-circle me-1"></i>${esc(row.debug)}`;
            if (row.is_sondermass) html += ` <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem">Sondermaß</span>`;
            html += '</div>';
        }
        if (row.element_params && Object.keys(row.element_params).length) {
            const epList = Object.values(row.element_params);
            if (epList.length) {
                html += '<div class="mt-1">' + epList.map(p =>
                    `<span class="badge bg-light text-dark border me-1" style="font-size:.68rem" title="Element-Param (nicht gespeichert)">
                        <i class="fas fa-crosshairs fa-xs me-1 text-muted"></i>${esc(p.bezeichnung)}: ${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}
                     </span>`
                ).join('') + '</div>';
            }
        }
        tdMap.innerHTML = html;
    } else {
        tdMap.innerHTML = '<span class="text-muted small">—</span>';
    }
    tr.appendChild(tdMap);

    const tdParams = document.createElement('td');
    if (row.excel_params && Object.keys(row.excel_params).length) {
        const pList = Object.values(row.excel_params).filter(p => p.wert !== '' && p.wert !== undefined);
        tdParams.innerHTML = pList.length
            ? pList.map(p =>
                `<span class="badge bg-primary bg-opacity-10 text-primary border me-1 mb-1" style="font-size:.7rem">
                    <i class="fas fa-key fa-xs me-1"></i>${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                 </span>`).join('')
            : '<span class="text-muted small">—</span>';
    } else {
        tdParams.innerHTML = '<span class="text-muted small">—</span>';
    }
    tr.appendChild(tdParams);

    const tdAct = document.createElement('td');
    if (row.status === 'ambiguous') {
        const btnWrap = document.createElement('div');
        btnWrap.innerHTML = `<div class="small text-warning-emphasis fw-semibold mb-1">
            <i class="fas fa-question-circle me-1"></i>Welche Variante?
        </div>`;
        row.candidates.forEach(c => {
            const btn = document.createElement('button');
            btn.className = 'btn btn-outline-secondary btn-sm py-0 px-2 me-1 mb-1';
            btn.style.fontSize = '.72rem';
            const ignoreEntries = Object.entries(c.params).filter(([, p]) => p.role === 'ignore');
            const label = ignoreEntries.length
                ? ignoreEntries.map(([, p]) => `${p.bezeichnung}: ${p.wert}`).join(' / ')
                : `Var ${c.variante_letter}`;
            btn.innerHTML = `<span class="badge bg-secondary me-1">Var ${esc(c.variante_letter)}</span>${esc(label)}`;
            btn.onclick = () => chooseVariante(
                row.choice_key, c.variante_id,
                currentCompare.raum_id, currentCompare.raumnr, currentCompare.bezeichnung
            );
            btnWrap.appendChild(btn);
        });
        tdAct.appendChild(btnWrap);
    } else if (row.status === 'not_managed') {
        tdAct.innerHTML = `<span class="text-muted small"><i class="fas fa-ban me-1"></i>nicht gemappt<br><span style="font-size:.68rem">wird nicht angepasst</span></span>`;
    } else if (row.status === 'match') {
        tdAct.innerHTML = '<span class="text-success small">✓</span>';
    } else if (row.status === 'nur_db') {
        tdAct.innerHTML = `<span class="badge bg-danger" style="font-size:.72rem">Auf 0 setzen</span>`;
    } else if (row.status === 'nur_excel') {
        let extra = '';
        if (row.needs_new_variante) extra = `<div class="mt-1" style="font-size:.65rem"><i class="fas fa-plus me-1 text-primary"></i>neue Variante</div>`;
        tdAct.innerHTML = `<span class="badge bg-primary" style="font-size:.72rem">Hinzufügen</span>${extra}`;
    } else if (row.status === 'diff_anzahl') {
        tdAct.innerHTML = `<span class="badge bg-warning text-dark" style="font-size:.72rem">Anzahl → ${row.excel_anzahl}</span>`;
    }
    tr.appendChild(tdAct);
    return tr;
}

function togglePanel(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    const open = el.style.display !== 'none';
    el.style.display = open ? 'none' : 'block';
    btn.innerHTML = open
        ? '<i class="fas fa-table fa-xs me-1"></i>DB-Varianten'
        : '<i class="fas fa-table fa-xs me-1"></i>DB-Varianten ▴';
}

// ══════════════════════════════════════════════════════════════════
// Sync helpers (shared by single-room and batch)
// ══════════════════════════════════════════════════════════════════

function updateSyncSummary(element_blocks) {
    let cAdd = 0, cRemove = 0, cUpdate = 0, cNewVar = 0, cSkip = 0;
    element_blocks.forEach(block => {
        block.comparison.forEach(row => {
            if (row.status === 'nur_excel')   { cAdd++;    if (row.needs_new_variante) cNewVar++; }
            if (row.status === 'nur_db')      cRemove++;
            if (row.status === 'diff_anzahl') cUpdate++;
            if (row.status === 'not_managed') cSkip++;
        });
    });
    document.getElementById('sync-summary').innerHTML = [
        cAdd    ? `<span class="stat-pill bg-primary bg-opacity-10 text-primary me-1">${cAdd} hinzufügen</span>` : '',
        cRemove ? `<span class="stat-pill bg-danger bg-opacity-10 text-danger me-1">${cRemove} auf 0 setzen</span>` : '',
        cUpdate ? `<span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis me-1">${cUpdate} Anzahl anpassen</span>` : '',
        cNewVar ? `<span class="stat-pill bg-primary bg-opacity-10 text-primary me-1"><i class="fas fa-plus me-1"></i>${cNewVar} neue Variante(n)</span>` : '',
        cSkip   ? `<span class="stat-pill bg-light text-muted me-1 border"><i class="fas fa-ban me-1"></i>${cSkip} nicht verwaltet (unverändert)</span>` : '',
    ].join('');
}

function buildSyncActions(element_blocks, raum_id, kommentar) {
    const actions = [];
    element_blocks.forEach(block => {
        block.comparison.forEach(row => {
            if (row.status === 'not_managed' || row.status === 'ambiguous') return;
            if (row.status === 'nur_db') {
                actions.push({ type: 'remove', rhe_id: row.rhe_id, kommentar: kommentar || 'Entfernt via Excel-Abgleich' });
            } else if (row.status === 'nur_excel') {
                actions.push({
                    type:         'add',
                    raum_id,
                    element_id:   row.db_elem_id,
                    element_code: block.element_id,
                    anzahl:       row.excel_anzahl,
                    variante_id:  row.variante_id ?? null,
                    params:       row.new_variante_params,
                });
            } else if (row.status === 'diff_anzahl') {
                actions.push({ type: 'update', rhe_id: row.rhe_id, anzahl: row.excel_anzahl });
            }
        });
    });
    return actions;
}

// ══════════════════════════════════════════════════════════════════
// Single-room sync button
// ══════════════════════════════════════════════════════════════════

document.getElementById('btn-sync').addEventListener('click', function () {
    if (!currentCompare) return;
    const kommentar = document.getElementById('sync-kommentar').value.trim() || 'Entfernt via Excel-Abgleich';
    const actions   = buildSyncActions(currentCompare.element_blocks, currentCompare.raum_id, kommentar);
    if (!actions.length) { alert('Keine Änderungen notwendig.'); return; }

    this.disabled = true;
    $.ajax({
        url: API_SYNC,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ actions }),
        success: res => {
            this.disabled = false;
            const el = document.getElementById('sync-result');
            el.style.display = 'block';
            el.className = res.ok ? 'alert alert-success small mt-2' : 'alert alert-warning small mt-2';
            let msg = res.ok
                ? `<i class="fas fa-check-circle me-1"></i><strong>${res.success}</strong> Änderungen übernommen.`
                : `<i class="fas fa-exclamation-triangle me-1"></i><strong>${res.success}</strong> OK, <strong>${res.errors}</strong> Fehler.`;
            if (res.warnings?.length) {
                msg += '<ul class="mb-0 mt-1">' + res.warnings.map(w =>
                    `<li><i class="fas fa-exclamation-triangle text-warning me-1"></i>${esc(w)}</li>`).join('') + '</ul>';
            }
            el.innerHTML = msg;
            if (res.ok) {
                userChoices = {};
                compareRoom(currentCompare.raum_id, currentCompare.raumnr, currentCompare.bezeichnung);
            }
        },
        error: xhr => { this.disabled = false; alertApiError(xhr); },
    });
});