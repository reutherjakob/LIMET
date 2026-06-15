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
        },
        error: function () {
            document.getElementById('wfContainer').innerHTML =
                '<div class="alert alert-danger">Workflows konnten nicht geladen werden.</div>';
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