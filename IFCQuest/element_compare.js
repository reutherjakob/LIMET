// ══════════════════════════════════════════════════════════════════
// element_compare.js  –  Element-Abgleich UI
// ══════════════════════════════════════════════════════════════════

let currentCompare = null;

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

        const params = {};
        Object.entries(paramColIdx).forEach(([col, idx]) => {
            params[col] = String(row[idx] ?? '').trim();
        });

        familien.push({ familie: fam, laenge, tiefe, variante, params });
    });
    return familien;
}

// ──────────────────────────────────────────────────────────────────
// Trigger compare
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
        data: JSON.stringify({ raum_id, familien }),
        success: res => {
            currentCompare = { raum_id, raumnr, bezeichnung, ...res };
            renderCompare(res);
        },
        error: xhr => alertApiError(xhr),
    });
}

// ──────────────────────────────────────────────────────────────────
// Render full compare result
// ──────────────────────────────────────────────────────────────────

function renderCompare(res) {
    const container  = document.getElementById('compare-blocks-container');
    container.innerHTML = '';

    // Unmapped families
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

    let totalChanges = 0;
    res.element_blocks.forEach(block => {
        const changes = block.comparison.filter(c => c.status !== 'match').length;
        totalChanges += changes;
        container.appendChild(buildElementBlock(block, changes));
    });

    if (totalChanges > 0) {
        updateSyncSummary(res.element_blocks);
        document.getElementById('sync-card').style.display = 'block';
    } else {
        document.getElementById('sync-card').style.display = 'none';
    }
}

// ──────────────────────────────────────────────────────────────────
// Build one element card
//
// Layout:
//   ┌─ HEADER: ElementID · Bezeichnung · change-badge ───────────┐
//   │  [Varianten-Übersicht ▾]  (collapsible)                     │
//   ├─────────────────────────────────────────────────────────────┤
//   │  VARIANT INFO BADGE ROW  (config info for this element type) │
//   ├─────────────────────────────────────────────────────────────┤
//   │  COMPARISON TABLE                                            │
//   └─────────────────────────────────────────────────────────────┘
// ──────────────────────────────────────────────────────────────────

function buildElementBlock(block, changeCount) {
    const card = document.createElement('div');
    card.className = 'card mb-2 shadow-sm';

    // ── Header ────────────────────────────────────────────────────
    const statusBadge = changeCount > 0
        ? `<span class="badge bg-warning text-dark">${changeCount} Änderung${changeCount !== 1 ? 'en' : ''}</span>`
        : `<span class="badge bg-success"><i class="fas fa-check me-1"></i>ok</span>`;

    const varScheme = block.has_variante_params
        ? `<span class="badge bg-info bg-opacity-10 text-info border border-info ms-2" style="font-size:.7rem">
               <i class="fas fa-sliders-h me-1"></i>Variante nach: ${block.variante_param_names.join(', ')}
           </span>`
        : `<span class="badge bg-secondary bg-opacity-25 text-secondary border ms-2" style="font-size:.7rem">
               <i class="fas fa-lock me-1"></i>immer Var A (keine Varianten-Parameter)
           </span>`;

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
        </div>
        <button class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="togglePanel('${panelId}', this)">
            <i class="fas fa-table fa-xs me-1"></i>DB-Varianten
        </button>
    </div>`;

    card.appendChild(header);

    // ── Collapsible: all DB variants for this element ─────────────
    const varPanel = document.createElement('div');
    varPanel.id = panelId;
    varPanel.className = 'border-bottom';
    varPanel.style.display = 'none';
    varPanel.appendChild(buildVariantPanel(block));
    card.appendChild(varPanel);

    // ── Comparison table ──────────────────────────────────────────
    const tableWrap = document.createElement('div');
    tableWrap.className = 'table-responsive';

    const table = document.createElement('table');
    table.className = 'table table-sm table-hover mb-0 align-middle';
    table.innerHTML = `
    <thead class="table-light">
        <tr>
            <th style="width:28px"></th>
            <th>Variante</th>
            <th class="text-center" style="width:60px">DB</th>
            <th class="text-center" style="width:60px">Excel</th>
            <th>Mapping / Revit-Familie</th>
            <th>Varianten-Parameter (Excel)</th>
            <th style="width:120px">Aktion</th>
        </tr>
    </thead>`;

    const tbody = document.createElement('tbody');
    if (block.comparison.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-muted small text-center py-2">Keine Einträge</td></tr>`;
    } else {
        block.comparison.forEach(row => tbody.appendChild(buildComparisonRow(row, block)));
    }
    table.appendChild(tbody);
    tableWrap.appendChild(table);
    card.appendChild(tableWrap);

    return card;
}

// ──────────────────────────────────────────────────────────────────
// DB-Variant panel: all known variants for this element in project
// ──────────────────────────────────────────────────────────────────

function buildVariantPanel(block) {
    const wrap = document.createElement('div');
    wrap.className = 'p-3 bg-light';

    const title = document.createElement('div');
    title.className = 'fw-semibold small text-muted mb-2';
    title.innerHTML = `<i class="fas fa-database me-1"></i>Projektweite Varianten für dieses Element`;
    wrap.appendChild(title);

    if (!block.db_variants?.length) {
        wrap.innerHTML += `<div class="text-muted small">Keine Varianten im Projekt hinterlegt.</div>`;
        return wrap;
    }

    const table = document.createElement('table');
    table.className = 'table table-sm table-borderless mb-0';
    table.style.fontSize = '.8rem';

    const thead = document.createElement('thead');
    thead.innerHTML = `<tr class="text-muted">
        <th style="width:60px">Var</th>
        <th style="width:80px">In Raum</th>
        <th>${block.has_variante_params
        ? `Varianten-Parameter (${block.variante_param_names.join(', ')})`
        : 'Parameter'}</th>
        <th style="width:80px" class="text-center">Anzahl</th>
    </tr>`;
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    block.db_variants.forEach(v => {
        const tr = document.createElement('tr');

        // Which params to show: only the relevant ones if type has variante_params
        let paramHtml;
        if (!block.has_variante_params) {
            paramHtml = v.has_any_params
                ? `<span class="text-warning-emphasis small"><i class="fas fa-exclamation-triangle me-1"></i>Hat ${Object.keys(v.params).length} gespeicherte Parameter (nicht varianten-relevant)</span>`
                : `<span class="text-success small"><i class="fas fa-check me-1"></i>Keine Parameter gespeichert — korrekte Var A</span>`;
        } else {
            const relevant = Object.entries(v.params).filter(([pid]) =>
                block.variante_param_ids.includes(parseInt(pid))
            );
            if (relevant.length) {
                paramHtml = relevant.map(([, p]) =>
                    `<span class="badge bg-info bg-opacity-10 text-info border me-1" style="font-size:.72rem">
                        ${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                    </span>`
                ).join('');
            } else {
                paramHtml = `<span class="text-muted">keine Varianten-Parameter gespeichert</span>`;
            }
        }

        const inRoomBadge = v.in_room
            ? `<span class="badge bg-success bg-opacity-10 text-success border">✓ im Raum</span>`
            : `<span class="badge bg-light text-muted border">nicht hier</span>`;

        tr.innerHTML = `
            <td><span class="badge bg-secondary">Var ${esc(v.variante_letter)}</span></td>
            <td>${inRoomBadge}</td>
            <td>${paramHtml}</td>
            <td class="text-center">${v.in_room ? v.db_anzahl : '—'}</td>`;
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
        match:       { icon: 'fa-check-circle text-success',   rowCls: '',                           label: '—',              labelCls: '' },
        diff_anzahl: { icon: 'fa-not-equal text-warning',      rowCls: 'table-warning',              label: 'Anzahl ändern',  labelCls: 'badge bg-warning text-dark' },
        nur_excel:   { icon: 'fa-plus-circle text-primary',    rowCls: 'table-primary bg-opacity-10',label: 'Hinzufügen',     labelCls: 'badge bg-primary' },
        nur_db:      { icon: 'fa-minus-circle text-danger',    rowCls: 'table-danger bg-opacity-10', label: 'Auf 0 setzen',   labelCls: 'badge bg-danger' },
    };
    const s = STATUS[row.status] || STATUS.match;

    const tr = document.createElement('tr');
    if (s.rowCls) tr.className = s.rowCls;

    // ── Col 1: status icon ────────────────────────────────────────
    const tdIcon = document.createElement('td');
    tdIcon.className = 'text-center';
    tdIcon.innerHTML = `<i class="fas ${s.icon}"></i>`;
    tr.appendChild(tdIcon);

    // ── Col 2: Variante ───────────────────────────────────────────
    const tdVar = document.createElement('td');
    const varLetter = esc(row.variante_letter || '—');
    const isNew = row.needs_new_variante;
    let varHtml = isNew
        ? `<span class="badge bg-warning text-dark">Var (neu)</span>`
        : `<span class="badge bg-secondary">Var ${varLetter}</span>`;
    if (row.variante_label && row.variante_label !== '—') {
        varHtml += `<div class="text-muted mt-1" style="font-size:.72rem">${esc(row.variante_label)}</div>`;
    }
    tdVar.innerHTML = varHtml;
    tr.appendChild(tdVar);

    // ── Col 3: DB Anzahl ──────────────────────────────────────────
    const tdDb = document.createElement('td');
    tdDb.className = 'text-center';
    tdDb.innerHTML = row.db_anzahl > 0 ? `<strong>${row.db_anzahl}</strong>` : '<span class="text-muted">—</span>';
    tr.appendChild(tdDb);

    // ── Col 4: Excel Anzahl ───────────────────────────────────────
    const tdEx = document.createElement('td');
    tdEx.className = 'text-center';
    tdEx.innerHTML = row.excel_anzahl > 0 ? `<strong>${row.excel_anzahl}</strong>` : '<span class="text-muted">—</span>';
    tr.appendChild(tdEx);

    // ── Col 5: Mapping / Familie ──────────────────────────────────
    const tdMap = document.createElement('td');
    tdMap.style.minWidth = '180px';
    if (row.familie) {
        let html = '';

        // Family name (truncated with tooltip)
        const famShort = row.familie.length > 40 ? row.familie.substring(0, 38) + '…' : row.familie;
        html += `<div class="text-muted" style="font-size:.72rem" title="${esc(row.familie)}">${esc(famShort)}</div>`;

        // Dimension badges
        if (row.laenge_raw || row.tiefe_raw) {
            html += `<div class="mt-1">`;
            if (row.laenge_raw) html += `<span class="badge bg-light text-dark border me-1" style="font-size:.7rem">B: ${esc(row.laenge_raw)}</span>`;
            if (row.tiefe_raw)  html += `<span class="badge bg-light text-dark border me-1" style="font-size:.7rem">T: ${esc(row.tiefe_raw)}</span>`;
            if (row.laenge_cm)  html += `<span class="badge bg-secondary bg-opacity-25 text-dark border ms-1" style="font-size:.7rem">→ ${row.laenge_cm}cm</span>`;
            html += `</div>`;
        }

        // Debug trace
        if (row.debug) {
            html += `<div class="text-muted mt-1" style="font-size:.68rem">
                <i class="fas fa-info-circle me-1"></i>${esc(row.debug)}`;
            if (row.is_sondermass) html += ` <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem">Sondermaß</span>`;
            html += `</div>`;
        }

        tdMap.innerHTML = html;
    } else {
        tdMap.innerHTML = '<span class="text-muted small">—</span>';
    }
    tr.appendChild(tdMap);

    // ── Col 6: Varianten-Parameter aus Excel ──────────────────────
    const tdParams = document.createElement('td');
    tdParams.style.minWidth = '180px';
    if (row.excel_params && Object.keys(row.excel_params).length) {
        // Split into variante_params (fingerprint) vs info_params
        const varParamIds = block.variante_param_ids || [];
        const vpEntries   = [];
        const ipEntries   = [];

        Object.entries(row.excel_params).forEach(([pid, p]) => {
            if (p.wert === '' || p.wert === undefined) return;
            if (varParamIds.includes(parseInt(pid))) vpEntries.push(p);
            else ipEntries.push(p);
        });

        let html = '';
        if (vpEntries.length) {
            html += vpEntries.map(p =>
                `<span class="badge bg-primary bg-opacity-10 text-primary border me-1 mb-1" style="font-size:.7rem">
                    <i class="fas fa-key fa-xs me-1"></i>${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                 </span>`
            ).join('');
        }
        if (ipEntries.length) {
            html += ipEntries.filter(p => p.wert !== '0').map(p =>
                `<span class="badge bg-light text-dark border me-1 mb-1" style="font-size:.7rem">
                    ${esc(p.bezeichnung)}: ${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}
                 </span>`
            ).join('');
        }
        tdParams.innerHTML = html || '<span class="text-muted small">—</span>';
    } else {
        tdParams.innerHTML = '<span class="text-muted small">—</span>';
    }
    tr.appendChild(tdParams);

    // ── Col 7: Action ─────────────────────────────────────────────
    const tdAct = document.createElement('td');
    if (row.status !== 'match') {
        let extra = '';
        if (row.needs_new_variante) extra = `<div class="mt-1" style="font-size:.65rem"><i class="fas fa-plus me-1 text-warning"></i>neue Variante</div>`;
        tdAct.innerHTML = `<span class="${s.labelCls}" style="font-size:.72rem">${s.label}</span>${extra}`;
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
// Sync summary
// ──────────────────────────────────────────────────────────────────

function updateSyncSummary(element_blocks) {
    let cAdd = 0, cRemove = 0, cUpdate = 0, cNewVar = 0;
    element_blocks.forEach(block => {
        block.comparison.forEach(row => {
            if (row.status === 'nur_excel')   { cAdd++;    if (row.needs_new_variante) cNewVar++; }
            if (row.status === 'nur_db')      cRemove++;
            if (row.status === 'diff_anzahl') cUpdate++;
        });
    });
    document.getElementById('sync-summary').innerHTML = [
        cAdd    ? `<span class="stat-pill bg-primary bg-opacity-10 text-primary me-1">${cAdd} hinzufügen</span>` : '',
        cRemove ? `<span class="stat-pill bg-danger bg-opacity-10 text-danger me-1">${cRemove} auf 0 setzen</span>` : '',
        cUpdate ? `<span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis me-1">${cUpdate} Anzahl anpassen</span>` : '',
        cNewVar ? `<span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis me-1"><i class="fas fa-plus me-1"></i>${cNewVar} neue Variante(n)</span>` : '',
    ].join('');
}

// ──────────────────────────────────────────────────────────────────
// Build sync actions
// ──────────────────────────────────────────────────────────────────

function buildSyncActions(element_blocks, raum_id, kommentar) {
    const actions = [];
    element_blocks.forEach(block => {
        block.comparison.forEach(row => {
            if (row.status === 'nur_db') {
                actions.push({ type: 'remove', rhe_id: row.rhe_id, kommentar: kommentar || 'Entfernt via Excel-Abgleich' });
            } else if (row.status === 'nur_excel') {
                actions.push({
                    type:         'add',
                    raum_id:      raum_id,
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
                ? `<i class="fas fa-check-circle me-1"></i><strong>${res.success}</strong> Änderungen erfolgreich übernommen.`
                : `<i class="fas fa-exclamation-triangle me-1"></i><strong>${res.success}</strong> OK, <strong>${res.errors}</strong> Fehler.`;
            if (res.ok) compareRoom(currentCompare.raum_id, currentCompare.raumnr, currentCompare.bezeichnung);
        },
        error: xhr => {
            this.disabled = false;
            alertApiError(xhr);
        },
    });
});