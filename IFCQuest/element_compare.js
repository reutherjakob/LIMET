// ══════════════════════════════════════════════════════════════════
// element_compare.js
// Shows one card per element, with DB variant fingerprints + Excel match
// ══════════════════════════════════════════════════════════════════

let currentCompare = null; // last compare API response + room info

// ──────────────────────────────────────────────────────────────────
// Collect Excel rows for a room into the "familien" array
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
// Trigger compare for a room
// ──────────────────────────────────────────────────────────────────

function compareRoom(raum_id, raumnr, bezeichnung) {
    const familien = collectFamilienForRaum(raumnr);

    // Show loading state
    const section = document.getElementById('compare-section');
    section.style.display = 'block';
    document.getElementById('compare-room-label').textContent = `— ${raumnr} ${bezeichnung}`;
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
// Render the compare result
// ──────────────────────────────────────────────────────────────────

function renderCompare(res) {
    const container = document.getElementById('compare-blocks-container');
    container.innerHTML = '';

    // ── Unmapped families warning ─────────────────────────────────
    const unmappedDiv = document.getElementById('unmapped-section');
    if (res.unmapped_familien?.length) {
        unmappedDiv.style.display = 'block';
        document.getElementById('unmapped-list').innerHTML =
            res.unmapped_familien.map(u =>
                `<span class="badge bg-secondary me-1"
                       data-familie="${esc(u.familie)}"
                       data-laenge="${esc(u.laenge || '')}">
                    ${esc(u.familie)}${u.laenge ? ' ' + u.laenge : ''} ×${u.anzahl}
                </span>`
            ).join('');
    } else {
        unmappedDiv.style.display = 'none';
    }

    if (!res.element_blocks?.length) {
        container.innerHTML = '<div class="text-muted small p-3">Keine Elemente gefunden.</div>';
        return;
    }

    // ── One card per element ──────────────────────────────────────
    let totalChanges = 0;

    res.element_blocks.forEach(block => {
        const changes = block.comparison.filter(c => c.status !== 'match').length;
        totalChanges += changes;
        container.appendChild(buildElementBlock(block, changes));
    });

    // ── Sync card ─────────────────────────────────────────────────
    if (totalChanges > 0) {
        updateSyncSummary(res.element_blocks);
        document.getElementById('sync-card').style.display = 'block';
    } else {
        document.getElementById('sync-card').style.display = 'none';
    }
}

// ──────────────────────────────────────────────────────────────────
// Build one element block card
// ──────────────────────────────────────────────────────────────────

function buildElementBlock(block, changeCount) {
    const card = document.createElement('div');
    card.className = 'card mb-2';

    const headerClass = changeCount > 0 ? '' : '';
    const changesBadge = changeCount > 0
        ? `<span class="badge bg-warning text-dark ms-2">${changeCount} Änderung${changeCount !== 1 ? 'en' : ''}</span>`
        : `<span class="badge bg-success ms-2">✓ ok</span>`;

    card.innerHTML = `
    <div class="card-header py-2 d-flex align-items-center justify-content-between">
        <div>
            <code class="me-2" style="font-size:.8rem">${esc(block.element_id)}</code>
            <span class="fw-semibold">${esc(block.bezeichnung)}</span>
            ${changesBadge}
        </div>
        <button class="btn btn-outline-secondary btn-sm py-0 px-2"
                onclick="toggleVariantPanel(this)"
                data-target="variants-${esc(block.element_id).replace(/\./g, '_')}">
            <i class="fas fa-list fa-xs me-1"></i>Varianten-Übersicht
        </button>
    </div>`;

    const body = document.createElement('div');
    body.className = 'card-body p-0';

    // ── Variant overview panel (collapsible) ──────────────────────
    const varPanel = document.createElement('div');
    varPanel.id    = `variants-${block.element_id.replace(/\./g, '_')}`;
    varPanel.style.display = 'none';
    varPanel.className = 'px-3 pt-2 pb-1 border-bottom bg-light';
    varPanel.innerHTML = buildVariantOverview(block.db_variants, block.variante_param_ids);
    body.appendChild(varPanel);

    // ── Comparison table ──────────────────────────────────────────
    const table = document.createElement('table');
    table.className = 'table table-sm mb-0';
    table.innerHTML = `
    <thead>
        <tr class="table-light">
            <th style="width:28px"></th>
            <th>Variante</th>
            <th class="text-center">DB</th>
            <th class="text-center">Excel</th>
            <th>Revit-Familie / Mapping</th>
            <th>Aktion</th>
        </tr>
    </thead>`;

    const tbody = document.createElement('tbody');
    block.comparison.forEach(row => tbody.appendChild(buildComparisonRow(row)));
    table.appendChild(tbody);
    body.appendChild(table);
    card.appendChild(body);
    return card;
}

// ──────────────────────────────────────────────────────────────────
// Variant overview: shows all known project variants + their params
// ──────────────────────────────────────────────────────────────────

function buildVariantOverview(db_variants, variante_param_ids) {
    // variante_param_ids: the param IDs that define variants for this element type.
    // If empty → element has no variant params (always Var A).
    const hasVarianteParams = variante_param_ids && variante_param_ids.length > 0;

    if (!db_variants?.length) {
        return '<div class="text-muted small py-1">Keine Varianten im Projekt (→ Var A ohne Parameter).</div>';
    }

    const rows = db_variants.map(v => {
        const inRoom = v.in_room
            ? `<span class="badge bg-success bg-opacity-10 text-success">im Raum ×${v.db_anzahl}</span>`
            : `<span class="badge bg-light text-muted border">nicht in Raum</span>`;

        let paramBadges;
        if (!hasVarianteParams) {
            // Element type has no variant params — Var A by definition, no params to show
            paramBadges = '<span class="text-muted" style="font-size:.75rem">keine Varianten-Parameter (immer Var A)</span>';
        } else {
            // Only show the params that are relevant for variant differentiation
            const relevantParams = Object.entries(v.params)
                .filter(([pid]) => variante_param_ids.includes(parseInt(pid)));
            paramBadges = relevantParams.length
                ? relevantParams.map(([, p]) =>
                    `<span class="badge bg-info bg-opacity-10 text-info me-1" style="font-size:.68rem">
                        ${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}${p.einheit ? ' ' + esc(p.einheit) : ''}</strong>
                     </span>`
                ).join('')
                : '<span class="text-muted" style="font-size:.75rem">keine Varianten-Parameter gesetzt</span>';
        }

        return `<tr>
            <td><span class="badge bg-secondary" style="font-size:.75rem">Var ${esc(v.variante_letter)}</span></td>
            <td style="font-size:.78rem">${paramBadges}</td>
            <td>${inRoom}</td>
        </tr>`;
    }).join('');

    const headerNote = hasVarianteParams
        ? ''
        : '<div class="text-muted small mb-1" style="font-size:.75rem"><i class="fas fa-info-circle me-1"></i>Dieses Element hat keine Varianten-Parameter — alle Einträge gehen auf Var A.</div>';

    return `<div class="small fw-semibold text-muted mb-1">Projektweite Varianten${hasVarianteParams ? '' : ' (parameterlos)'}:</div>
    ${headerNote}
    <table class="table table-sm table-borderless mb-1" style="font-size:.8rem">
        <tbody>${rows}</tbody>
    </table>`;
}

// ──────────────────────────────────────────────────────────────────
// One row in the comparison table
// ──────────────────────────────────────────────────────────────────

function buildComparisonRow(row) {
    const STATUS = {
        match:       { icon: 'fa-check-circle',  cls: 'status-match',     rowCls: '',                        label: '—' },
        diff_anzahl: { icon: 'fa-not-equal',      cls: 'status-diff',      rowCls: 'table-warning bg-opacity-10', label: 'Anzahl anpassen' },
        nur_excel:   { icon: 'fa-plus-circle',    cls: 'status-nur-excel', rowCls: 'table-primary bg-opacity-10', label: 'Hinzufügen' },
        nur_db:      { icon: 'fa-minus-circle',   cls: 'status-nur-db',    rowCls: 'table-danger bg-opacity-10',  label: 'Auf 0 setzen' },
    };
    const s = STATUS[row.status] || STATUS.match;

    const tr = document.createElement('tr');
    if (s.rowCls) tr.className = s.rowCls;

    // Variant cell
    const varLetter = row.variante_letter || '—';
    const varBadge  = row.needs_new_variante
        ? `<span class="badge bg-warning text-dark">Var ${esc(varLetter)}</span>
           <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem"><i class="fas fa-plus me-1"></i>neu</span>`
        : `<span class="badge bg-secondary">Var ${esc(varLetter)}</span>`;

    const varLabel = `<div>${varBadge}</div>
        <div class="text-muted" style="font-size:.72rem">${esc(row.variante_label)}</div>`;

    // Familie / mapping info cell
    let mappingHtml = '<span class="text-muted">—</span>';
    if (row.familie) {
        const famShort = row.familie.length > 35 ? row.familie.substring(0, 33) + '…' : row.familie;
        mappingHtml  = `<span class="text-muted" style="font-size:.72rem" title="${esc(row.familie)}">${esc(famShort)}</span>`;
        if (row.laenge_raw)    mappingHtml += `<br><span class="badge bg-light text-dark border" style="font-size:.68rem">${esc(row.laenge_raw)}</span>`;
        if (row.laenge_cm)     mappingHtml += ` <i class="fas fa-arrow-right fa-xs text-muted"></i> <span class="badge bg-light text-dark border" style="font-size:.68rem">${row.laenge_cm}cm</span>`;
        if (row.is_sondermass) mappingHtml += ' <span class="badge bg-warning text-dark" style="font-size:.65rem">Sondermaß</span>';
        if (row.debug)         mappingHtml += `<br><span class="text-muted" style="font-size:.68rem"><i class="fas fa-info-circle me-1"></i>${esc(row.debug)}</span>`;

        // Excel params display
        if (row.excel_params && Object.keys(row.excel_params).length) {
            const pList = Object.values(row.excel_params).filter(p => p.wert && p.wert !== '0');
            if (pList.length) {
                mappingHtml += '<div class="mt-1">' + pList.map(p =>
                    `<span class="badge bg-info bg-opacity-10 text-info me-1" style="font-size:.65rem">
                        ${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}</strong>
                     </span>`
                ).join('') + '</div>';
            }
        }
    }

    // Action badge
    const colorMap   = { nur_db: 'danger', nur_excel: 'primary', diff_anzahl: 'warning' };
    const textMap    = { nur_db: 'danger', nur_excel: 'primary', diff_anzahl: 'warning-emphasis' };
    const actionHtml = row.status === 'match' ? '' :
        `<span class="action-badge bg-${colorMap[row.status]} bg-opacity-10 text-${textMap[row.status]}">${s.label}</span>`;

    tr.innerHTML = `
        <td class="${s.cls} text-center"><i class="fas ${s.icon} fa-xs"></i></td>
        <td style="min-width:160px">${varLabel}</td>
        <td class="text-center">${row.db_anzahl > 0 ? row.db_anzahl : '<span class="text-muted">—</span>'}</td>
        <td class="text-center">${row.excel_anzahl > 0 ? row.excel_anzahl : '<span class="text-muted">—</span>'}</td>
        <td style="min-width:200px">${mappingHtml}</td>
        <td>${actionHtml}</td>`;

    return tr;
}

function toggleVariantPanel(btn) {
    const target = document.getElementById(btn.dataset.target);
    if (!target) return;
    target.style.display = target.style.display === 'none' ? 'block' : 'none';
}

// ──────────────────────────────────────────────────────────────────
// Sync summary
// ──────────────────────────────────────────────────────────────────

function updateSyncSummary(element_blocks) {
    let cAdd = 0, cRemove = 0, cUpdate = 0, cNewVariante = 0;
    element_blocks.forEach(block => {
        block.comparison.forEach(row => {
            if (row.status === 'nur_excel')   { cAdd++;    if (row.needs_new_variante) cNewVariante++; }
            if (row.status === 'nur_db')      cRemove++;
            if (row.status === 'diff_anzahl') cUpdate++;
        });
    });

    document.getElementById('sync-summary').innerHTML = `
        ${cAdd    ? `<span class="stat-pill bg-primary bg-opacity-10 text-primary me-1">${cAdd} hinzufügen</span>` : ''}
        ${cRemove ? `<span class="stat-pill bg-danger bg-opacity-10 text-danger me-1">${cRemove} auf 0 setzen</span>` : ''}
        ${cUpdate ? `<span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis me-1">${cUpdate} anpassen</span>` : ''}
        ${cNewVariante ? `<span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis me-1"><i class="fas fa-plus me-1"></i>${cNewVariante} neue Variante(n)</span>` : ''}`;
}

// ──────────────────────────────────────────────────────────────────
// Build sync actions from comparison result
// ──────────────────────────────────────────────────────────────────

function buildSyncActions(element_blocks, raum_id, kommentar) {
    const actions = [];
    element_blocks.forEach(block => {
        block.comparison.forEach(row => {
            if (row.status === 'nur_db') {
                actions.push({
                    type:     'remove',
                    rhe_id:   row.rhe_id,
                    kommentar: kommentar || 'Entfernt via Excel-Abgleich',
                });
            } else if (row.status === 'nur_excel') {
                actions.push({
                    type:         'add',
                    raum_id:      raum_id,
                    element_id:   row.db_elem_id,
                    element_code: block.element_id,
                    anzahl:       row.excel_anzahl,
                    variante_id:  row.variante_id ?? null,   // null = needs new
                    params:       row.new_variante_params,   // only if new variante
                });
            } else if (row.status === 'diff_anzahl') {
                actions.push({
                    type:   'update',
                    rhe_id: row.rhe_id,
                    anzahl: row.excel_anzahl,
                });
            }
        });
    });
    return actions;
}

// ──────────────────────────────────────────────────────────────────
// Sync button handler
// ──────────────────────────────────────────────────────────────────

document.getElementById('btn-sync').addEventListener('click', function () {
    if (!currentCompare) return;

    const kommentar = document.getElementById('sync-kommentar').value.trim() || 'Entfernt via Excel-Abgleich';
    const actions   = buildSyncActions(
        currentCompare.element_blocks,
        currentCompare.raum_id,
        kommentar
    );

    if (!actions.length) { alert('Keine Änderungen notwendig.'); return; }

    this.disabled = true;

    $.ajax({
        url: API_SYNC,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ actions }),
        success: res => {
            document.getElementById('btn-sync').disabled = false;
            const el = document.getElementById('sync-result');
            el.style.display = 'block';
            el.className = res.ok ? 'alert alert-success small' : 'alert alert-warning small';
            el.innerHTML = res.ok
                ? `<i class="fas fa-check-circle me-1"></i><strong>${res.success}</strong> Änderungen erfolgreich übernommen.`
                : `<i class="fas fa-exclamation-triangle me-1"></i><strong>${res.success}</strong> OK, <strong>${res.errors}</strong> Fehler.`;
            // Reload compare to show updated state
            if (res.ok) {
                compareRoom(currentCompare.raum_id, currentCompare.raumnr, currentCompare.bezeichnung);
            }
        },
        error: xhr => {
            document.getElementById('btn-sync').disabled = false;
            alertApiError(xhr);
        },
    });
});