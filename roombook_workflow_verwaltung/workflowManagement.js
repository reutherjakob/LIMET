/**
 * workflowManagement.js
 * Frontend-Logik für die Workflow-Verwaltung (roombook_workflows.php).
 *
 * Voraussetzungen: jQuery, Bootstrap 5, makeToaster() (aus _utils.js)
 *
 * Endpoints:
 *   getProjectWorkflows.php     -> Workflows des Projektes inkl. Schritte
 *   getUnassignedWorkflows.php  -> Workflows, die dem Projekt NOCH NICHT zugeordnet sind
 *   getWorkflowteile.php        -> verfügbare Aufgaben (optional je Workflow gefiltert)
 *   createWorkflow.php          -> neuen Workflow anlegen (+ Projektverknüpfung + Schritte)
 *   assignWorkflow.php          -> bestehenden Workflow mit Projekt verknüpfen
 *   renameWorkflow.php          -> Workflow umbenennen
 *   addWorkflowStep.php         -> Schritt zu Workflow hinzufügen
 *   updateWorkflowStep.php      -> Reihenfolge / Tage eines Schritts ändern
 *   removeWorkflowStep.php      -> Schritt entfernen
 *   unassignWorkflow.php        -> Workflow vom Projekt lösen
 */

// ── Helpers ───────────────────────────────────────────────────────────────────

function parseResponse(raw) {
    try {
        return typeof raw === 'string' ? JSON.parse(raw) : raw;
    } catch (e) {
        return { status: 'error', msg: String(raw) };
    }
}

function escapeHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

// Aufgaben-Auswahl befüllen (optional gefiltert auf einen Workflow)
function loadWorkflowteile(selectEl, workflowID, done) {
    $.ajax({
        url: 'getWorkflowteile.php',
        type: 'POST',
        data: workflowID ? { workflowID: workflowID } : {},
        success: function (raw) {
            const list = parseResponse(raw);
            let html = '<option value="">— wählen —</option>';
            if (Array.isArray(list)) {
                list.forEach(t => {
                    html += `<option value="${t.id}">${escapeHtml(t.aufgabe)}</option>`;
                });
            }
            selectEl.innerHTML = html;
            if (typeof done === 'function') done(Array.isArray(list) ? list : []);
        },
        error: () => makeToaster('Aufgaben konnten nicht geladen werden.', false)
    });
}

// Workflow-Typen in eine Auswahl laden
function loadWorkflowtypen(selectEl) {
    $.ajax({
        url: 'getWorkflowtypen.php',
        type: 'POST',
        success: function (raw) {
            const list = parseResponse(raw);
            let html = '<option value="">— wählen —</option>';
            if (Array.isArray(list)) {
                list.forEach(t => {
                    html += `<option value="${t.id}">${escapeHtml(t.name)}</option>`;
                });
            }
            selectEl.innerHTML = html;
        },
        error: () => makeToaster('Workflow-Typen konnten nicht geladen werden.', false)
    });
}

// ── Workflows laden & rendern ──────────────────────────────────────────────────

function loadWorkflows() {
    $.ajax({
        url: 'getProjectWorkflows.php',
        type: 'POST',
        success: function (raw) {
            const data = parseResponse(raw);
            renderWorkflows(Array.isArray(data) ? data : []);
            loadWorkflowStats();
        },
        error: function () {
            document.getElementById('wfContainer').innerHTML =
                '<div class="alert alert-danger">Workflows konnten nicht geladen werden.</div>';
        }
    });
}

// ── Übersicht / Visualisierung (ALLE Workflows) ────────────────────────────────

const wfOverview = {
    currentProjectId: 0,
    workflows: [],
    filter: 'all',     // all | in | out
    search: '',
    chartWf: 'all',    // 'all' oder Workflow-ID
    chart: null
};

const WF_SEGMENT_COLORS = ['bg-primary', 'bg-info', 'bg-success', 'bg-warning', 'bg-danger', 'bg-secondary', 'bg-dark'];
// Bootstrap-Standardfarben als Hex (Chart.js braucht konkrete Farben)
const WF_CHART_COLORS = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#20c997',
                         '#0dcaf0', '#d63384', '#ffc107', '#6c757d', '#6610f2', '#212529'];

function loadWorkflowStats() {
    $.ajax({
        url: 'getWorkflowStats.php',
        type: 'POST',
        success: function (raw) {
            const s = parseResponse(raw) || {};
            wfOverview.currentProjectId = Number(s.currentProjectId) || 0;
            wfOverview.workflows = Array.isArray(s.workflows) ? s.workflows : [];
            renderOverview();
        },
        error: () => makeToaster('Workflow-Übersicht konnte nicht geladen werden.', false)
    });
}

function wfDuration(wf) {
    return (wf.steps || []).reduce((sum, s) => sum + (Number(s.tageMinDanach) || 0), 0);
}

function wfFiltered() {
    const q = wfOverview.search.trim().toLowerCase();
    return wfOverview.workflows.filter(wf => {
        if (wfOverview.filter === 'in' && !wf.inProject) return false;
        if (wfOverview.filter === 'out' && wf.inProject) return false;
        if (!q) return true;
        const hay = [wf.name, wf.typ]
            .concat((wf.assignedProjects || []).map(p => p.name))
            .concat((wf.usedInProjects || []).map(p => p.name))
            .join(' ').toLowerCase();
        return hay.includes(q);
    });
}

function renderOverview() {
    const wrap = document.getElementById('wfOverview');
    const all = wfOverview.workflows;
    if (!all.length) { wrap.classList.add('d-none'); return; }
    wrap.classList.remove('d-none');

    const currentId = wfOverview.currentProjectId;
    document.getElementById('wfOvCntAll').textContent = all.length;
    document.getElementById('wfOvCntIn').textContent  = all.filter(w => w.inProject).length;
    document.getElementById('wfOvCntOut').textContent = all.filter(w => !w.inProject).length;

    const list = wfFiltered();
    const durations = list.map(wfDuration);
    const maxDur = list.length ? Math.max(0, ...durations) : 0;
    const minDur = list.length ? Math.min(...durations) : 0;
    const avg = arr => arr.length ? arr.reduce((a, b) => a + b, 0) / arr.length : 0;
    const fmt1 = n => n.toFixed(1).replace('.', ',');

    // KPI-Kacheln (bezogen auf den aktuellen Filter)
    const kpi = (label, value, sub) => `
        <div class="col-6 col-md-4 col-xl-2">
            <div class="border rounded p-2 h-100">
                <div class="small text-muted">${label}</div>
                <div class="fs-5 fw-semibold">${value}</div>
                ${sub ? `<div class="small text-muted text-truncate" title="${escapeHtml(sub)}">${escapeHtml(sub)}</div>` : ''}
            </div>
        </div>`;
    document.getElementById('wfOverviewKpis').innerHTML = list.length ? (
        kpi('Workflows', list.length) +
        kpi('Ø Schritte', fmt1(avg(list.map(w => (w.steps || []).length)))) +
        kpi('Ø Dauer', Math.round(avg(durations)) + ' Tage') +
        kpi('Längster', maxDur + ' Tage', list[durations.indexOf(maxDur)].name) +
        kpi('Kürzester', minDur + ' Tage', list[durations.indexOf(minDur)].name) +
        kpi('In Losen verwendet', list.filter(w => w.loseTotal > 0).length + ' / ' + list.length)
    ) : '';

    // Tabelle
    const body = document.getElementById('wfOverviewBody');
    if (!list.length) {
        body.innerHTML = '<tr><td colspan="7" class="text-muted fst-italic small">Keine Workflows für diesen Filter.</td></tr>';
    } else {
        body.innerHTML = list.map((wf, i) => {
            const steps = wf.steps || [];
            const dur = durations[i];

            let bar;
            if (!steps.length) {
                bar = '<span class="text-muted fst-italic small">keine Schritte</span>';
            } else if (maxDur === 0) {
                bar = '<span class="text-muted fst-italic small">keine Tagesangaben</span>';
            } else {
                let elapsed = 0;
                bar = '<div class="progress" style="height:1.25rem">' + steps.map((s, k) => {
                    const days = Number(s.tageMinDanach) || 0;
                    const from = elapsed;
                    elapsed += days;
                    const width = days > 0 ? (days / maxDur * 100) : 0.8; // 0-Tage-Schritte als Markierung
                    const tip = `${s.reihenfolge}. ${s.aufgabe} – ${days} Tag(e) (Tag ${from} → ${elapsed})`;
                    const label = (days / maxDur) > 0.06 ? s.reihenfolge : '';
                    return `<div class="progress-bar ${WF_SEGMENT_COLORS[k % WF_SEGMENT_COLORS.length]} border-end border-white"
                                 role="progressbar" style="width:${width}%" title="${escapeHtml(tip)}">${label}</div>`;
                }).join('') + '</div>';
            }

            const assigned = (wf.assignedProjects || []).length
                ? wf.assignedProjects.map(p => `<span class="badge ${p.id === currentId ? 'bg-primary' : 'bg-light text-dark border'} me-1 mb-1">${escapeHtml(p.name)}</span>`).join('')
                : '<span class="text-muted small">keinem Projekt zugeordnet</span>';

            const used = (wf.usedInProjects || []).length
                ? wf.usedInProjects.map(p => `<span class="badge ${p.id === currentId ? 'bg-success' : 'bg-light text-dark border'} me-1 mb-1"
                        title="${p.lose} Los(e)">${escapeHtml(p.name)} <span class="badge bg-secondary ms-1">${p.lose}</span></span>`).join('')
                : '<span class="text-muted small">nicht verwendet</span>';

            const projBadge = wf.inProject ? ' <span class="badge bg-primary ms-1">dieses Projekt</span>' : '';
            const shared = (wf.assignedProjects || []).length > 1
                ? ` <i class="fas fa-share-alt text-warning ms-1" title="Wird von ${wf.assignedProjects.length} Projekten geteilt"></i>` : '';
            const selected = String(wfOverview.chartWf) === String(wf.id) ? 'table-active' : '';

            return `
                <tr class="wf-ov-row ${selected}" role="button" data-wf-id="${wf.id}" title="Im Diagramm anzeigen">
                    <td class="fw-semibold">${escapeHtml(wf.name)}${projBadge}${shared}</td>
                    <td>${wf.typ ? `<span class="badge bg-secondary">${escapeHtml(wf.typ)}</span>` : '<span class="text-muted small">–</span>'}</td>
                    <td class="text-center"><span class="badge bg-info text-dark">${steps.length}</span></td>
                    <td class="text-center">
                        <span class="fw-semibold">${dur}</span>
                        ${dur >= 7 ? `<div class="small text-muted">≈ ${fmt1(dur / 7)} Wo.</div>` : ''}
                    </td>
                    <td>${bar}</td>
                    <td>${assigned}</td>
                    <td>${used}</td>
                </tr>`;
        }).join('');
    }

    // Diagramm-Auswahl neu befüllen (Auswahl beibehalten, falls noch im Filter)
    const sel = document.getElementById('wfChartSelect');
    if (wfOverview.chartWf !== 'all' && !list.some(w => String(w.id) === String(wfOverview.chartWf))) {
        wfOverview.chartWf = 'all';
    }
    sel.innerHTML = '<option value="all">Alle gefilterten Workflows (Vergleich)</option>' +
        list.map(w => `<option value="${w.id}">${escapeHtml(w.name)}</option>`).join('');
    sel.value = String(wfOverview.chartWf);

    renderWorkflowChart(list);
}

// ── Diagramm (Chart.js) ─────────────────────────────────────────────────────────

function renderWorkflowChart(list) {
    const wrapEl = document.getElementById('wfChartWrap');
    const canvas = document.getElementById('wfChart');

    if (wfOverview.chart) { wfOverview.chart.destroy(); wfOverview.chart = null; }
    if (typeof Chart === 'undefined') {
        wrapEl.innerHTML = '<div class="text-muted small">Diagramm-Bibliothek (Chart.js) konnte nicht geladen werden.</div>';
        return;
    }

    const single = wfOverview.chartWf !== 'all'
        ? list.find(w => String(w.id) === String(wfOverview.chartWf)) : null;

    if (single) renderGanttChart(canvas, wrapEl, single);
    else renderCompareChart(canvas, wrapEl, list.filter(w => (w.steps || []).length));
}

// Vergleich: je Workflow eine Treppenlinie – waagrechtes Stück = Dauer eines Schritts
function renderCompareChart(canvas, wrapEl, list) {
    wrapEl.style.height = '420px';

    const datasets = list.map((wf, i) => {
        const color = WF_CHART_COLORS[i % WF_CHART_COLORS.length];
        let t = 0;
        const pts = wf.steps.map(s => {
            const p = { x: t, y: Number(s.reihenfolge) || 0, aufgabe: s.aufgabe, days: Number(s.tageMinDanach) || 0 };
            t += p.days;
            return p;
        });
        const last = wf.steps[wf.steps.length - 1];
        pts.push({ x: t, y: Number(last.reihenfolge) || 0, end: true });
        return {
            label: wf.name,
            data: pts,
            stepped: 'after',
            borderColor: color,
            backgroundColor: color,
            borderWidth: wf.inProject ? 3 : 1.5,
            borderDash: wf.inProject ? [] : [5, 3],
            pointRadius: ctx => (ctx.raw && ctx.raw.end) ? 0 : 3,
            pointHoverRadius: 5
        };
    });

    wfOverview.chart = new Chart(canvas, {
        type: 'line',
        data: { datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'nearest', intersect: false },
            scales: {
                x: { type: 'linear', min: 0, title: { display: true, text: 'Tage ab Start' } },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    title: { display: true, text: 'Workflowteil (Reihenfolge)' }
                }
            },
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 14 } },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const r = ctx.raw;
                            if (r.end) return `${ctx.dataset.label}: Ende nach ${r.x} Tagen`;
                            return `${ctx.dataset.label}: ${r.y}. ${r.aufgabe} – ab Tag ${r.x}, ${r.days} Tag(e)`;
                        }
                    }
                }
            }
        }
    });
}

// Einzelner Workflow: Gantt (waagrechte Balken von Starttag bis Endtag je Schritt)
function renderGanttChart(canvas, wrapEl, wf) {
    const steps = wf.steps || [];
    wrapEl.style.height = Math.max(220, steps.length * 34 + 90) + 'px';

    let t = 0;
    const meta = [];
    const data = steps.map(s => {
        const d = Number(s.tageMinDanach) || 0;
        meta.push({ from: t, to: t + d, days: d });
        const bar = [t, t + Math.max(d, 0.3)]; // 0-Tage-Schritte als schmale Markierung
        t += d;
        return bar;
    });

    wfOverview.chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: steps.map(s => `${s.reihenfolge}. ${s.aufgabe}`),
            datasets: [{
                label: wf.name,
                data,
                backgroundColor: steps.map((_, k) => WF_CHART_COLORS[k % WF_CHART_COLORS.length]),
                borderSkipped: false,
                barPercentage: 0.7
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { type: 'linear', min: 0, title: { display: true, text: `Tage ab Start (gesamt ${t} Tage)` } },
                y: {
                    title: { display: true, text: 'Workflowteil (Reihenfolge)' },
                    ticks: {
                        callback: function (v) {
                            const l = this.getLabelForValue(v);
                            return l.length > 40 ? l.slice(0, 39) + '…' : l;
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                title: { display: true, text: wf.name },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const m = meta[ctx.dataIndex];
                            return `Tag ${m.from} → ${m.to} (${m.days} Tag(e))`;
                        }
                    }
                }
            }
        }
    });
}
function renderWorkflows(workflows) {
    const container = document.getElementById('wfContainer');
    const hint = document.getElementById('wfEmptyHint');

    if (!workflows.length) {
        container.innerHTML = '';
        hint.classList.remove('d-none');
        return;
    }
    hint.classList.add('d-none');

    container.innerHTML = workflows.map(wf => {
        const collapseId = `workflow-collapse-${wf.id}`;

        const rows = (wf.steps || []).map(s => `
            <tr data-workflowteil-id="${s.workflowteilId}">
                <td>
                    <input type="number" min="0"
                           class="form-control form-control-sm wf-order"
                           value="${Number(s.reihenfolge) || 0}">
                </td>
                <td>${escapeHtml(s.aufgabe)}</td>
                <td>
                    <input type="number" min="0"
                           class="form-control form-control-sm wf-days"
                           value="${Number(s.tageMinDanach) || 0}">
                </td>
                <td class="text-end text-nowrap">
                    <button type="button" class="btn btn-sm btn-success wf-save-step" title="Speichern">
                        <i class="fas fa-save"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger wf-remove-step" title="Schritt entfernen">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`).join('');

        const body = rows || `
            <tr><td colspan="4" class="text-muted fst-italic small">
                Noch keine Schritte – über „Schritt“ hinzufügen.
            </td></tr>`;

        return `
        <div class="card mb-3" data-workflow-id="${wf.id}" data-workflow-name="${escapeHtml(wf.name)}">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
                 role="button"
                 data-bs-toggle="collapse"
                 data-bs-target="#${collapseId}"
                 aria-expanded="false"
                 aria-controls="${collapseId}">
                <span class="fw-semibold">
                    <i class="fas fa-stream me-2"></i>${escapeHtml(wf.name)}
                </span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary wf-rename-btn rounded-2 me-1" title="Umbenennen">
                        <i class="fas fa-pen me-1"></i> Umbenennen
                    </button>
                    <button type="button" class="btn btn-outline-success wf-add-step-btn rounded-2 me-1">
                        <i class="fas fa-plus me-1"></i> Schritt
                    </button>
                    <button type="button" class="btn btn-outline-danger wf-unassign-btn rounded-2">
                        <i class="fas fa-unlink me-1"></i> Vom Projekt lösen
                    </button>
                </div>
            </div>

            <div class="collapse" id="${collapseId}">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="col-2">Reihenfolge</th>
                                    <th>Aufgabe</th>
                                    <th class="col-2">Tage danach (min.)</th>
                                    <th class="text-end col-2">Aktion</th>
                                </tr>
                            </thead>
                            <tbody>${body}</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}
// ── Bestehende Schritte: speichern / entfernen ──────────────────────────────────

function saveStep($row) {
    const $card = $row.closest('.card');
    const payload = {
        workflowID:     Number($card.data('workflow-id')),
        workflowteilID: Number($row.data('workflowteil-id')),
        reihenfolge:    Number($row.find('.wf-order').val()) || 0,
        tageMinDanach:  Number($row.find('.wf-days').val()) || 0
    };
    $.ajax({
        url: 'updateWorkflowStep.php', type: 'POST', data: payload,
        success: function (raw) {
            const r = parseResponse(raw);
            makeToaster(r.status === 'ok' ? 'Gespeichert.' : 'Fehler: ' + (r.msg || ''), r.status === 'ok');
            // Nur die Übersicht neu laden – aufgeklappte Karten bleiben offen
            if (r.status === 'ok') loadWorkflowStats();
        },
        error: () => makeToaster('Verbindungsfehler.', false)
    });
}

function removeStep($row) {
    const $card = $row.closest('.card');
    if (!confirm('Diesen Schritt aus dem Workflow entfernen?')) return;
    $.ajax({
        url: 'removeWorkflowStep.php', type: 'POST',
        data: {
            workflowID:     Number($card.data('workflow-id')),
            workflowteilID: Number($row.data('workflowteil-id'))
        },
        success: function (raw) {
            const r = parseResponse(raw);
            if (r.status === 'ok') { makeToaster('Schritt entfernt.', true); loadWorkflows(); }
            else makeToaster('Fehler: ' + (r.msg || ''), false);
        },
        error: () => makeToaster('Verbindungsfehler.', false)
    });
}

function unassignWorkflow($card) {
    const name = $card.data('workflow-name') || 'diesen Workflow';
    if (!confirm('„' + name + '“ vom Projekt lösen?\n(Der Workflow selbst bleibt erhalten.)')) return;
    $.ajax({
        url: 'unassignWorkflow.php', type: 'POST',
        data: { workflowID: Number($card.data('workflow-id')) },
        success: function (raw) {
            const r = parseResponse(raw);
            if (r.status === 'ok') { makeToaster('Vom Projekt gelöst.', true); loadWorkflows(); }
            else makeToaster('Fehler: ' + (r.msg || ''), false);
        },
        error: () => makeToaster('Verbindungsfehler.', false)
    });
}

// ── Schritt zu bestehendem Workflow hinzufügen (Modal) ──────────────────────────

function openAddStepModal($card) {
    const workflowID = Number($card.data('workflow-id'));
    document.getElementById('wfAddStepWorkflowId').value = workflowID;
    document.getElementById('wfAddStepOrder').value = 1;
    document.getElementById('wfAddStepDays').value = 0;
    loadWorkflowteile(document.getElementById('wfAddStepTeilSelect'), workflowID);
    $('#wfAddStepModal').modal('show');
}

function submitAddStep() {
    const workflowID     = Number(document.getElementById('wfAddStepWorkflowId').value);
    const workflowteilID = Number(document.getElementById('wfAddStepTeilSelect').value);
    const reihenfolge    = Number(document.getElementById('wfAddStepOrder').value) || 0;
    const tageMinDanach  = Number(document.getElementById('wfAddStepDays').value) || 0;

    if (!workflowteilID) { makeToaster('Bitte eine Aufgabe wählen.', false); return; }

    $.ajax({
        url: 'addWorkflowStep.php', type: 'POST',
        data: { workflowID, workflowteilID, reihenfolge, tageMinDanach },
        success: function (raw) {
            const r = parseResponse(raw);
            if (r.status === 'ok') {
                makeToaster('Schritt hinzugefügt.', true);
                $('#wfAddStepModal').modal('hide');
                loadWorkflows();
            } else makeToaster('Fehler: ' + (r.msg || ''), false);
        },
        error: () => makeToaster('Verbindungsfehler.', false)
    });
}

// ── Bestehenden Workflow dem Projekt zuordnen (Modal) ───────────────────────────

function loadUnassignedWorkflows(selectEl) {
    const empty = document.getElementById('wfAssignEmpty');
    const btn   = document.getElementById('wfAssignConfirmBtn');
    $.ajax({
        url: 'getUnassignedWorkflows.php',
        type: 'POST',
        success: function (raw) {
            const list = parseResponse(raw);
            const arr  = Array.isArray(list) ? list : [];
            let html = '<option value="">— wählen —</option>';
            arr.forEach(w => {
                html += `<option value="${w.id}">${escapeHtml(w.name)}</option>`;
            });
            selectEl.innerHTML = html;

            if (!arr.length) {
                empty.classList.remove('d-none');
                selectEl.classList.add('d-none');
                btn.disabled = true;
            } else {
                empty.classList.add('d-none');
                selectEl.classList.remove('d-none');
                btn.disabled = false;
            }
        },
        error: () => makeToaster('Verfügbare Workflows konnten nicht geladen werden.', false)
    });
}

function openAssignModal() {
    const sel = document.getElementById('wfAssignSelect');
    sel.innerHTML = '<option value="">— wählen —</option>';
    loadUnassignedWorkflows(sel);
    $('#wfAssignModal').modal('show');
}

function submitAssign() {
    const workflowID = Number(document.getElementById('wfAssignSelect').value);
    if (!workflowID) { makeToaster('Bitte einen Workflow wählen.', false); return; }

    $.ajax({
        url: 'assignWorkflow.php', type: 'POST',
        data: { workflowID },
        success: function (raw) {
            const r = parseResponse(raw);
            if (r.status === 'ok') {
                makeToaster('Workflow hinzugefügt.', true);
                $('#wfAssignModal').modal('hide');
                loadWorkflows();
            } else makeToaster('Fehler: ' + (r.msg || ''), false);
        },
        error: () => makeToaster('Verbindungsfehler.', false)
    });
}

// ── Workflow umbenennen (Modal) ─────────────────────────────────────────────────

function openRenameModal($card) {
    document.getElementById('wfRenameWorkflowId').value = Number($card.data('workflow-id'));
    document.getElementById('wfRenameName').value = $card.data('workflow-name') || '';
    $('#wfRenameModal').modal('show');
    setTimeout(() => document.getElementById('wfRenameName').focus(), 300);
}

function submitRename() {
    const workflowID = Number(document.getElementById('wfRenameWorkflowId').value);
    const name = document.getElementById('wfRenameName').value.trim();
    if (!name) { makeToaster('Bitte eine Bezeichnung eingeben.', false); return; }

    $.ajax({
        url: 'renameWorkflow.php', type: 'POST',
        data: { workflowID, name },
        success: function (raw) {
            const r = parseResponse(raw);
            if (r.status === 'ok') {
                makeToaster('Umbenannt.', true);
                $('#wfRenameModal').modal('hide');
                loadWorkflows();
            } else makeToaster('Fehler: ' + (r.msg || ''), false);
        },
        error: () => makeToaster('Verbindungsfehler.', false)
    });
}

// ── Neuer Workflow (Modal) ──────────────────────────────────────────────────────

function openNewWorkflowModal() {
    document.getElementById('wfNewName').value = '';
    document.getElementById('wfNewOrder').value = 1;
    document.getElementById('wfNewDays').value = 0;
    document.getElementById('wfNewStagedBody').innerHTML =
        '<tr id="wfNewStagedEmpty"><td colspan="4" class="text-muted fst-italic small">Noch keine Schritte hinzugefügt.</td></tr>';
    loadWorkflowtypen(document.getElementById('wfNewTypSelect'));
    loadWorkflowteile(document.getElementById('wfNewTeilSelect'), null);
    $('#wfNewModal').modal('show');
}

// Schritt im Anlege-Dialog "vormerken"
function stageNewStep() {
    const sel = document.getElementById('wfNewTeilSelect');
    const teilId = Number(sel.value);
    if (!teilId) { makeToaster('Bitte eine Aufgabe wählen.', false); return; }

    if ($('#wfNewStagedBody tr[data-teil-id="' + teilId + '"]').length) {
        makeToaster('Aufgabe bereits hinzugefügt.', false);
        return;
    }

    const aufgabe = sel.options[sel.selectedIndex].text;
    const order = Number(document.getElementById('wfNewOrder').value) || 0;
    const days  = Number(document.getElementById('wfNewDays').value) || 0;

    $('#wfNewStagedEmpty').remove();
    $('#wfNewStagedBody').append(`
        <tr data-teil-id="${teilId}" data-order="${order}" data-days="${days}">
            <td>${order}</td>
            <td>${escapeHtml(aufgabe)}</td>
            <td>${days}</td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger wf-stage-remove" title="Entfernen">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`);

    // nächste Reihenfolge vorschlagen, Auswahl zurücksetzen
    document.getElementById('wfNewOrder').value = order + 1;
    sel.value = '';
}

function submitNewWorkflow() {
    const name = document.getElementById('wfNewName').value.trim();
    const workflowtypID = Number(document.getElementById('wfNewTypSelect').value);
    if (!workflowtypID) { makeToaster('Bitte einen Workflow-Typ wählen.', false); return; }
    if (!name) { makeToaster('Bitte eine Bezeichnung eingeben.', false); return; }

    const steps = $('#wfNewStagedBody tr[data-teil-id]').map(function () {
        const $r = $(this);
        return {
            workflowteilId: Number($r.data('teil-id')),
            reihenfolge:    Number($r.data('order')),
            tageMinDanach:  Number($r.data('days'))
        };
    }).get();

    $.ajax({
        url: 'createWorkflow.php', type: 'POST',
        data: { name: name, workflowtypID: workflowtypID, steps: JSON.stringify(steps) },
        success: function (raw) {
            const r = parseResponse(raw);
            if (r.status === 'ok') {
                makeToaster('Workflow angelegt.', true);
                $('#wfNewModal').modal('hide');
                loadWorkflows();
            } else makeToaster('Fehler: ' + (r.msg || ''), false);
        },
        error: () => makeToaster('Verbindungsfehler.', false)
    });
}

// ── Init ────────────────────────────────────────────────────────────────────────

$(document).ready(function () {
    loadWorkflows();

    $('#wfNewBtn').on('click', openNewWorkflowModal);
    $('#wfAssignBtn').on('click', openAssignModal);

    // Delegierte Events innerhalb der Workflow-Karten
    $('#wfContainer')
        .on('click', '.wf-save-step',   function () { saveStep($(this).closest('tr')); })
        .on('click', '.wf-remove-step', function () { removeStep($(this).closest('tr')); })
        .on('click', '.wf-add-step-btn', function () { openAddStepModal($(this).closest('.card')); })
        .on('click', '.wf-unassign-btn', function () { unassignWorkflow($(this).closest('.card')); })
        .on('click', '.wf-rename-btn', function (e) {
            // verhindert, dass gleichzeitig der Collapse umgeschaltet wird
            e.stopPropagation();
            openRenameModal($(this).closest('.card'));
        })
        .on('keydown', '.wf-order, .wf-days', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveStep($(this).closest('tr'));
            }
        });

    // Übersicht: Filter, Suche, Diagramm-Auswahl, Zeile -> Gantt
    $('input[name="wfOvFilter"]').on('change', function () {
        wfOverview.filter = this.value;
        renderOverview();
    });
    $('#wfOverviewSearch').on('input', function () {
        wfOverview.search = this.value;
        renderOverview();
    });
    $('#wfChartSelect').on('change', function () {
        wfOverview.chartWf = this.value;
        renderOverview();
    });
    $('#wfOverviewBody').on('click', '.wf-ov-row', function () {
        const id = String($(this).data('wf-id'));
        // erneuter Klick auf dieselbe Zeile -> zurück zum Vergleich
        wfOverview.chartWf = (String(wfOverview.chartWf) === id) ? 'all' : id;
        renderOverview();
        document.getElementById('wfChartWrap').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // Anlege-Dialog
    $('#wfNewAddStageBtn').on('click', stageNewStep);
    $('#wfNewStagedBody').on('click', '.wf-stage-remove', function () {
        $(this).closest('tr').remove();
        if (!$('#wfNewStagedBody tr[data-teil-id]').length) {
            $('#wfNewStagedBody').html(
                '<tr id="wfNewStagedEmpty"><td colspan="4" class="text-muted fst-italic small">Noch keine Schritte hinzugefügt.</td></tr>');
        }
    });
    $('#wfNewConfirmBtn').on('click', submitNewWorkflow);

    // Schritt-Dialog
    $('#wfAddStepConfirmBtn').on('click', submitAddStep);

    // Zuordnen-Dialog
    $('#wfAssignConfirmBtn').on('click', submitAssign);

    // Umbenennen-Dialog
    $('#wfRenameConfirmBtn').on('click', submitRename);
    $('#wfRenameName').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); submitRename(); }
    });
});