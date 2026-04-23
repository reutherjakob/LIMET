// ══════════════════════════════════════════════════════════════════
// State — Raum-Prüfung
// ══════════════════════════════════════════════════════════════════

let validationResults = {};

// ══════════════════════════════════════════════════════════════════
// Räume prüfen
// ══════════════════════════════════════════════════════════════════

document.getElementById('btn-check-rooms').addEventListener('click', function () {
    const raumnummern = [...new Set(
        parsedRows.map(row => String(row[colIdx.raumnr] ?? '').trim()).filter(Boolean)
    )];
    if (!raumnummern.length) { alert('Keine Raumnummern in der gewählten Spalte gefunden.'); return; }

    this.disabled = true;

    $.ajax({
        url: API_CHECK_ROOMS,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({raumnummern}),
        success: res => {
            document.getElementById('btn-check-rooms').disabled = false;
            validationResults = res.results;
            renderValidation(res.results);
            showCard('validation-card');
        },
        error: xhr => {
            document.getElementById('btn-check-rooms').disabled = false;
            alertApiError(xhr);
        }
    });
});

// ══════════════════════════════════════════════════════════════════
// Validierungstabelle rendern
// ══════════════════════════════════════════════════════════════════

function renderValidation(results) {
    const tbody = document.getElementById('validation-tbody');
    tbody.innerHTML = '';
    let cFound = 0, cNotFound = 0, cDuplicate = 0;

    const linesByNr = {};
    parsedRows.forEach((row, i) => {
        const nr = String(row[colIdx.raumnr] ?? '').trim();
        if (!nr) return;
        if (!linesByNr[nr]) linesByNr[nr] = [];
        linesByNr[nr].push(i + 2);
    });

    Object.values(results).forEach(r => {
        const tr  = document.createElement('tr');
        const uid = 'elem-' + r.raumnr.replace(/[^a-z0-9]/gi, '_');

        if (r.status === 'found') {
            cFound++;
            const elemCount = r.elemente.length;
            tr.innerHTML = `
            <td class="status-found text-center"><i class="fas fa-check-circle"></i></td>
            <td>
                <strong>${esc(r.raumnr)}</strong>
                <div class="text-muted" style="font-size:.75rem">Zeile(n): ${(linesByNr[r.raumnr] || []).join(', ')}</div>
            </td>
            <td>${esc(r.bezeichnung)}</td>
            <td><span class="text-muted small">${esc(r.geschoss || '—')}</span></td>
            <td>
                ${elemCount > 0
                ? `<a class="text-decoration-none small fw-semibold" data-bs-toggle="collapse" href="#${uid}" role="button">
                           <i class="fas fa-boxes me-1"></i>${elemCount} Element${elemCount !== 1 ? 'e' : ''}
                           <i class="fas fa-chevron-down ms-1" style="font-size:.65rem"></i>
                       </a>
                       <div class="collapse" id="${uid}">${renderElementList(r.elemente)}</div>`
                : `<span class="text-muted small"><i class="fas fa-minus me-1"></i>keine</span>`}
            </td>
            <td>
                <button class="btn btn-outline-primary btn-sm py-0 px-2"
                        onclick="compareRoom(${r.raum_id},'${esc(r.raumnr)}','${esc(r.bezeichnung)}')">
                    <i class="fas fa-balance-scale fa-xs me-1"></i>Abgleichen
                </button>
            </td>`;
        } else if (r.status === 'duplicate') {
            cDuplicate++;
            tr.className = 'table-warning';
            tr.innerHTML = `
            <td class="status-duplicate text-center"><i class="fas fa-exclamation-triangle"></i></td>
            <td><strong>${esc(r.raumnr)}</strong></td>
            <td colspan="3">
                <span class="fw-semibold text-warning-emphasis">Mehrfach vergeben — Import nicht möglich</span>
                <ul class="mb-0 mt-1 text-muted small">
                    ${r.rooms.map(room => `<li>ID ${room.idTABELLE_Räume}: ${esc(room.Raumbezeichnung)} (${esc(room.Geschoss || '—')})</li>`).join('')}
                </ul>
            </td>`;
        } else {
            cNotFound++;
            tr.className = 'table-danger bg-opacity-25';
            tr.innerHTML = `
            <td class="status-notfound text-center"><i class="fas fa-times-circle"></i></td>
            <td><strong>${esc(r.raumnr)}</strong></td>
            <td colspan="3"><span class="text-danger-emphasis small">Raum nicht im Projekt gefunden</span></td>`;
        }
        tbody.appendChild(tr);
    });

    document.getElementById('validation-stats').innerHTML = `
    <span class="stat-pill bg-success bg-opacity-10 text-success">
        <i class="fas fa-check-circle fa-xs"></i>${cFound} gefunden
    </span>
    ${cNotFound  ? `<span class="stat-pill bg-danger bg-opacity-10 text-danger"><i class="fas fa-times-circle fa-xs"></i>${cNotFound} nicht gefunden</span>` : ''}
    ${cDuplicate ? `<span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis"><i class="fas fa-exclamation-triangle fa-xs"></i>${cDuplicate} doppelt</span>` : ''}`;
}

function renderElementList(elemente) {
    if (!elemente.length) return '';
    return `<ul class="elemente-list list-unstyled mb-0">
    ${elemente.map(el => `<li>
        <span class="elem-code">${esc(el.code)}</span>${esc(el.bezeichnung)}
        <span class="text-muted ms-1" style="font-size:.75rem">× ${el.anzahl}</span>
    </li>`).join('')}
</ul>`;
}
