// ══════════════════════════════════════════════════════════════════
// element_compare.js
// ══════════════════════════════════════════════════════════════════

let currentCompare = null;
let userChoices    = {}; // { "element_id|fingerprint" => variante_id }

// ──────────────────────────────────────────────────────────────────
// Collect Excel rows for a room
// ──────────────────────────────────────────────────────────────────

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

// ──────────────────────────────────────────────────────────────────
// Trigger compare (also called after user makes a choice)
// ──────────────────────────────────────────────────────────────────

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

// Called when user clicks a candidate button in an ambiguous row
function chooseVariante(choiceKey, varianteId, raum_id, raumnr, bezeichnung) {
    userChoices[choiceKey] = varianteId;
    compareRoom(raum_id, raumnr, bezeichnung);
}

// ──────────────────────────────────────────────────────────────────
// Render full compare result
// ──────────────────────────────────────────────────────────────────

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
        // Show a notice above sync
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

// ──────────────────────────────────────────────────────────────────
// Element block card
// ──────────────────────────────────────────────────────────────────

function buildElementBlock(block) {
    const card = document.createElement('div');
    card.className = 'card mb-2 shadow-sm';

    const changes     = block.comparison.filter(c => ['diff_anzahl', 'nur_excel', 'nur_db'].includes(c.status)).length;
    const ambiguous   = block.comparison.filter(c => c.status === 'ambiguous').length;
    const notManaged  = block.comparison.filter(c => c.status === 'not_managed').length;

    let statusBadge = '';
    if (ambiguous)   statusBadge += `<span class="badge bg-warning text-dark ms-1"><i class="fas fa-question me-1"></i>${ambiguous} Auswahl nötig</span>`;
    if (changes)     statusBadge += `<span class="badge bg-warning text-dark ms-1">${changes} Änderung${changes !== 1 ? 'en' : ''}</span>`;
    if (!ambiguous && !changes) statusBadge = `<span class="badge bg-success ms-1"><i class="fas fa-check me-1"></i>ok</span>`;

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
            ${statusBadge}
            ${varScheme}
            ${managedBadge}
        </div>
        <button class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="togglePanel('${panelId}', this)">
            <i class="fas fa-table fa-xs me-1"></i>DB-Varianten
        </button>
    </div>`;
    card.appendChild(header);

    // DB-Varianten panel
    const varPanel = document.createElement('div');
    varPanel.id = panelId;
    varPanel.className = 'border-bottom';
    varPanel.style.display = 'none';
    varPanel.appendChild(buildVariantPanel(block));
    card.appendChild(varPanel);

    // Comparison table
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

// ──────────────────────────────────────────────────────────────────
// DB variant panel (+ Excel-Vorschau)
// ──────────────────────────────────────────────────────────────────

function buildVariantPanel(block) {
    const wrap = document.createElement('div');
    wrap.className = 'p-3 bg-light';
    const title = document.createElement('div');
    title.className = 'fw-semibold small text-muted mb-2';
    title.innerHTML = `<i class="fas fa-database me-1"></i>Projektweite Varianten für <code>${esc(block.element_id)}</code>`;
    wrap.appendChild(title);

    // Excel-only rows that have no matching DB variant (needs_new_variante)
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

    // ── vorhandene DB-Varianten ────────────────────────────────────
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

        // Passendes Comparison-Row finden → Excel-Params anzeigen
        const compRow = block.comparison.find(c => c.variante_id === v.variante_id);
        const excelParams = compRow?.excel_params ?? {};
        const excelParamHtml = Object.values(excelParams).length
            ? Object.values(excelParams).map(p =>
                `<span class="badge me-1" style="font-size:.7rem;background:rgba(25,135,84,.08);color:#198754;border:1px solid #a3cfbb">
                    <i class="fas fa-file-excel fa-xs me-1"></i>${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                </span>`).join('')
            : '<span class="text-muted small">—</span>';

        // Status-Badge
        let statusHtml = '';
        if      (compRow?.status === 'match')       statusHtml = `<span class="badge bg-success bg-opacity-10 text-success border">✓ match</span>`;
        else if (compRow?.status === 'diff_anzahl') statusHtml = `<span class="badge bg-warning text-dark border">Anz. diff</span>`;
        else if (compRow?.status === 'nur_excel')   statusHtml = `<span class="badge bg-primary bg-opacity-10 text-primary border">hinzufügen</span>`;
        else if (compRow?.status === 'nur_db')      statusHtml = `<span class="badge bg-danger bg-opacity-10 text-danger border">nur DB</span>`;
        else if (v.in_room)                         statusHtml = `<span class="badge bg-secondary bg-opacity-10 text-secondary border">im Raum</span>`;
        else                                        statusHtml = `<span class="badge bg-light text-muted border">—</span>`;

        tr.innerHTML = `
            <td><span class="badge bg-secondary">Var ${esc(v.variante_letter)}</span></td>
            <td>${statusHtml}</td>
            <td>${vpHtml}</td>
            <td>${excelParamHtml}</td>
            <td>${ipHtml}</td>
            <td class="text-center">${v.in_room ? v.db_anzahl : '—'}</td>`;
        tbody.appendChild(tr);
    });

    // ── neue Excel-Varianten (noch nicht in DB) ────────────────────
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

// ──────────────────────────────────────────────────────────────────
// One comparison row
// ──────────────────────────────────────────────────────────────────

function buildComparisonRow(row, block) {
    const STATUS = {
        match:       { icon: 'fa-check-circle text-success',   rowCls: '',                            label: '—' },
        diff_anzahl: { icon: 'fa-not-equal text-warning',      rowCls: 'table-warning',               label: 'Anzahl ändern' },
        nur_excel:   { icon: 'fa-plus-circle text-primary',    rowCls: 'table-primary bg-opacity-10', label: 'Hinzufügen' },
        nur_db:      { icon: 'fa-minus-circle text-danger',    rowCls: 'table-danger bg-opacity-10',  label: 'Auf 0 setzen' },
        ambiguous:   { icon: 'fa-question-circle text-warning', rowCls: 'table-warning',              label: 'Variante wählen' },
        not_managed: { icon: 'fa-info-circle text-secondary',  rowCls: '',                            label: 'nicht angepasst' },
    };
    const s = STATUS[row.status] || STATUS.match;

    const tr = document.createElement('tr');
    if (s.rowCls) tr.className = s.rowCls;

    // ── Icon ─────────────────────────────────────────────────────
    const tdIcon = document.createElement('td');
    tdIcon.className = 'text-center';
    tdIcon.innerHTML = `<i class="fas ${s.icon}"></i>`;
    tr.appendChild(tdIcon);

    // ── Variante ─────────────────────────────────────────────────
    const tdVar = document.createElement('td');
    const varLetter = esc(row.variante_letter || '—');
    let varHtml = '';
    if (row.status === 'ambiguous') {
        varHtml = `<span class="badge bg-warning text-dark">? wählen</span>`;
    } else if (row.needs_new_variante) {
        varHtml = `<span class="badge bg-primary">Var (neu)</span>`;
    } else if (row.status === 'not_managed') {
        varHtml = `<span class="badge bg-secondary">Var ${varLetter}</span>`;
    } else {
        varHtml = `<span class="badge bg-secondary">Var ${varLetter}</span>`;
    }
    if (row.variante_label && row.variante_label !== '—' && row.status !== 'ambiguous') {
        varHtml += `<div class="text-muted mt-1" style="font-size:.72rem">${esc(row.variante_label)}</div>`;
    }
    tdVar.innerHTML = varHtml;
    tr.appendChild(tdVar);

    // ── DB Anzahl ────────────────────────────────────────────────
    const tdDb = document.createElement('td');
    tdDb.className = 'text-center';
    tdDb.innerHTML = row.db_anzahl > 0 ? `<strong>${row.db_anzahl}</strong>` : '<span class="text-muted">—</span>';
    tr.appendChild(tdDb);

    // ── Excel Anzahl ─────────────────────────────────────────────
    const tdEx = document.createElement('td');
    tdEx.className = 'text-center';
    tdEx.innerHTML = row.excel_anzahl > 0 ? `<strong>${row.excel_anzahl}</strong>` : '<span class="text-muted">—</span>';
    tr.appendChild(tdEx);

    // ── Mapping / Familie ────────────────────────────────────────
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
        // Element params (used for element selection, not written)
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

    // ── Parameter (Excel) ────────────────────────────────────────
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

    // ── Aktion / Info ─────────────────────────────────────────────
    const tdAct = document.createElement('td');

    if (row.status === 'ambiguous') {
        // Show candidate buttons for user to choose
        const btnWrap = document.createElement('div');
        btnWrap.innerHTML = `<div class="small text-warning-emphasis fw-semibold mb-1">
            <i class="fas fa-question-circle me-1"></i>Welche Variante?
        </div>`;
        row.candidates.forEach(c => {
            const btn = document.createElement('button');
            btn.className = 'btn btn-outline-secondary btn-sm py-0 px-2 me-1 mb-1';
            btn.style.fontSize = '.72rem';

            // Show the ignore_params of this candidate so user can tell them apart
            const ignoreEntries = Object.entries(c.params)
                .filter(([, p]) => p.role === 'ignore');
            const label = ignoreEntries.length
                ? ignoreEntries.map(([, p]) => `${p.bezeichnung}: ${p.wert}`).join(' / ')
                : `Var ${c.variante_letter}`;

            btn.innerHTML = `<span class="badge bg-secondary me-1">Var ${esc(c.variante_letter)}</span>${esc(label)}`;
            btn.onclick = () => chooseVariante(
                row.choice_key,
                c.variante_id,
                currentCompare.raum_id,
                currentCompare.raumnr,
                currentCompare.bezeichnung
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

// ──────────────────────────────────────────────────────────────────
// Sync summary + actions
// ──────────────────────────────────────────────────────────────────

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
            // Skip not_managed and ambiguous (ambiguous should be resolved before sync)
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

// ──────────────────────────────────────────────────────────────────
// Sync button
// ──────────────────────────────────────────────────────────────────

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
            el.innerHTML = res.ok
                ? `<i class="fas fa-check-circle me-1"></i><strong>${res.success}</strong> Änderungen übernommen.`
                : `<i class="fas fa-exclamation-triangle me-1"></i><strong>${res.success}</strong> OK, <strong>${res.errors}</strong> Fehler.`;
            if (res.ok) {
                userChoices = {}; // reset choices after successful sync
                compareRoom(currentCompare.raum_id, currentCompare.raumnr, currentCompare.bezeichnung);
            }
        },
        error: xhr => { this.disabled = false; alertApiError(xhr); },
    });
});