// ══════════════════════════════════════════════════════════════════
// State — Abgleich
// ══════════════════════════════════════════════════════════════════

let currentCompare = null;

// ══════════════════════════════════════════════════════════════════
// Element-Abgleich für einen einzelnen Raum
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
        const variante = colIdx.variante >= 0 ? String(row[colIdx.variante] ?? '').trim() : '';
        const params   = {};
        Object.entries(paramColIdx).forEach(([col, idx]) => { params[col] = String(row[idx] ?? '').trim(); });
        familien.push({familie: fam, laenge, variante, params});
    });
    return familien;
}

function compareRoom(raum_id, raumnr, bezeichnung) {
    const familien    = collectFamilienForRaum(raumnr);
    const excelRows   = parsedRows.filter(row => String(row[colIdx.raumnr] ?? '').trim() === raumnr);
    const excelSummary = document.getElementById('compare-excel-rows');

    document.getElementById('compare-room-label').textContent = `— ${raumnr} ${bezeichnung}`;

    if (excelRows.length && colIdx.familie >= 0) {
        const badges = excelRows.map(r => {
            const fam = esc(String(r[colIdx.familie] ?? '').trim());
            const len = colIdx.laenge >= 0 ? esc(String(r[colIdx.laenge] ?? '').trim()) : '—';
            return `<span class="badge bg-light text-dark border me-1 mb-1" style="font-size:.72rem">${fam} <strong>${len}</strong></span>`;
        }).join('');
        excelSummary.innerHTML = `<div class="text-muted small mb-0"><i class="fas fa-table me-1"></i>${excelRows.length} Excel-Zeilen: ${badges}</div>`;
        excelSummary.style.display = 'block';
    } else {
        excelSummary.style.display = 'none';
    }

    document.getElementById('compare-tbody').innerHTML =
        '<tr><td colspan="7" class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm me-2"></div>Lade…</td></tr>';
    document.getElementById('compare-section').style.display = 'block';
    document.getElementById('sync-card').style.display = 'none';
    document.getElementById('compare-section').scrollIntoView({behavior: 'smooth'});

    $.ajax({
        url: API_COMPARE,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({raum_id, familien}),
        success: res => { currentCompare = {raum_id, raumnr, ...res}; renderCompare(res); },
        error: xhr  => alertApiError(xhr)
    });
}

function renderCompare(res) {
    const tbody = document.getElementById('compare-tbody');
    tbody.innerHTML = '';
    let cAdd = 0, cRemove = 0, cUpdate = 0;

    const STATUS = {
        match:       {icon: 'fa-check-circle',  cls: 'status-match',     label: '—'},
        diff_anzahl: {icon: 'fa-not-equal',      cls: 'status-diff',      label: 'Anzahl anpassen'},
        nur_excel:   {icon: 'fa-plus-circle',    cls: 'status-nur-excel', label: 'Hinzufügen'},
        nur_db:      {icon: 'fa-minus-circle',   cls: 'status-nur-db',    label: 'Auf 0 setzen'},
    };

    (res.vergleich || []).forEach(el => {
        const s  = STATUS[el.status] || STATUS.match;
        const tr = document.createElement('tr');

        if      (el.status === 'nur_db')      { tr.className = 'table-danger bg-opacity-10';  cRemove++; }
        else if (el.status === 'nur_excel')   { tr.className = 'table-primary bg-opacity-10'; cAdd++;    }
        else if (el.status === 'diff_anzahl') { tr.className = 'table-warning bg-opacity-10'; cUpdate++; }

        const colorMap    = {nur_db: 'danger', nur_excel: 'primary', diff_anzahl: 'warning'};
        const textMap     = {nur_db: 'danger', nur_excel: 'primary', diff_anzahl: 'warning-emphasis'};
        const actionBadge = el.status === 'match' ? '' :
            `<span class="action-badge bg-${colorMap[el.status]} bg-opacity-10 text-${textMap[el.status]}">${s.label}</span>`;

        let mappingInfo = '';
        if (el.familie) {
            const famShort = el.familie.length > 30 ? el.familie.substring(0, 28) + '…' : el.familie;
            mappingInfo = `<span class="text-muted" style="font-size:.72rem">${esc(famShort)}</span>`;
            if (el.laenge_raw)    mappingInfo += `<br><span class="badge bg-light text-dark border" style="font-size:.68rem">${esc(el.laenge_raw)}</span>`;
            if (el.laenge_cm)     mappingInfo += ` <i class="fas fa-arrow-right fa-xs text-muted"></i> <span class="badge bg-light text-dark border" style="font-size:.68rem">${el.laenge_cm}cm</span>`;
            if (el.is_sondermass) mappingInfo += ' <span class="badge bg-warning text-dark" style="font-size:.65rem">Sondermaß</span>';
            if (el.debug)         mappingInfo += `<br><span class="text-muted" style="font-size:.68rem"><i class="fas fa-info-circle me-1"></i>${esc(el.debug)}</span>`;
            if (el.params && Object.keys(el.params).length) {
                const pEntries = Object.entries(el.params).filter(([, p]) => p?.wert !== undefined);
                if (pEntries.length) mappingInfo += '<div class="mt-1">' + pEntries.map(([, p]) =>
                    `<span class="badge bg-info bg-opacity-10 text-info me-1" style="font-size:.65rem">${esc(p.bezeichnung)}: <strong>${esc(String(p.wert))}</strong></span>`
                ).join('') + '</div>';
            }
            if (el.needs_new_variante) mappingInfo += '<span class="badge bg-warning text-dark mt-1" style="font-size:.65rem"><i class="fas fa-plus me-1"></i>neue Variante</span>';
        }

        tr.innerHTML = `
        <td class="${s.cls} text-center"><i class="fas ${s.icon} fa-xs"></i></td>
        <td><code style="font-size:.75rem">${esc(el.element_id)}</code></td>
        <td>${esc(el.bezeichnung)}</td>
        <td class="text-center">${el.db_anzahl   > 0 ? el.db_anzahl   : '<span class="text-muted">—</span>'}</td>
        <td class="text-center">${el.excel_anzahl > 0 ? el.excel_anzahl : '<span class="text-muted">—</span>'}</td>
        <td style="min-width:200px">${mappingInfo || '<span class="text-muted">—</span>'}</td>
        <td>${actionBadge}</td>`;
        tbody.appendChild(tr);
    });

    const unmapped = res.unmapped_familien || [];
    if (unmapped.length) {
        document.getElementById('unmapped-section').style.display = 'block';
        document.getElementById('unmapped-list').innerHTML =
            unmapped.map(u => `<span class="badge bg-secondary me-1" data-familie="${esc(u.familie)}" data-laenge="${esc(u.laenge || '')}">${esc(u.familie)}${u.laenge ? ' ' + u.laenge : ''} ×${u.anzahl}</span>`).join('');
    } else {
        document.getElementById('unmapped-section').style.display = 'none';
    }

    if (cAdd + cRemove + cUpdate > 0) {
        document.getElementById('sync-summary').innerHTML = `
        ${cAdd    ? `<span class="stat-pill bg-primary bg-opacity-10 text-primary me-1">${cAdd} hinzufügen</span>`          : ''}
        ${cRemove ? `<span class="stat-pill bg-danger bg-opacity-10 text-danger me-1">${cRemove} auf 0 setzen</span>`       : ''}
        ${cUpdate ? `<span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis me-1">${cUpdate} anpassen</span>` : ''}`;
        document.getElementById('sync-card').style.display = 'block';
    } else {
        document.getElementById('sync-card').style.display = 'none';
    }
}

// ══════════════════════════════════════════════════════════════════
// Sync ausführen
// ══════════════════════════════════════════════════════════════════

document.getElementById('btn-sync').addEventListener('click', function () {
    if (!currentCompare) return;
    const kommentar = document.getElementById('sync-kommentar').value.trim() || 'Entfernt via Excel-Abgleich';
    const actions   = buildSyncActions(currentCompare.vergleich, currentCompare.raum_id, kommentar);
    if (!actions.length) { alert('Keine Aktionen notwendig.'); return; }

    this.disabled = true;

    $.ajax({
        url: API_SYNC,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({actions}),
        success: res => {
            document.getElementById('btn-sync').disabled = false;
            const el = document.getElementById('sync-result');
            el.style.display = 'block';
            el.className = res.ok ? 'alert alert-success small' : 'alert alert-warning small';
            el.innerHTML = res.ok
                ? `<i class="fas fa-check-circle me-1"></i><strong>${res.success}</strong> Änderungen erfolgreich übernommen.`
                : `<i class="fas fa-exclamation-triangle me-1"></i><strong>${res.success}</strong> OK, <strong>${res.errors}</strong> Fehler.`;
            if (res.ok) compareRoom(currentCompare.raum_id, currentCompare.raumnr, '');
        },
        error: xhr => {
            document.getElementById('btn-sync').disabled = false;
            alertApiError(xhr);
        }
    });
});

function buildSyncActions(vergleich, raum_id, kommentar = '') {
    const actions = [];
    (vergleich || []).forEach(el => {
        if (el.status === 'nur_db') {
            actions.push({type: 'remove', rhe_id: el.rhe_id, kommentar: kommentar || 'Entfernt via Excel-Abgleich'});
        } else if (el.status === 'nur_excel') {
            actions.push({
                type:         'add',
                raum_id:      raum_id,
                element_id:   el.db_element_id ?? null,
                element_code: el.element_id,
                anzahl:       el.excel_anzahl,
                params:       el.params || {},
                variante_id:  el.variante_id ?? null,
            });
        } else if (el.status === 'diff_anzahl') {
            actions.push({type: 'update', rhe_id: el.rhe_id, anzahl: el.excel_anzahl});
        }
    });
    return actions;
}
