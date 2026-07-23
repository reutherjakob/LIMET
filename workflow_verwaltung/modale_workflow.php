<div class="modal fade" id="wfNewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-plus me-1"></i> Neuen Workflow anlegen</h6>
                <small class="text-muted"> Bite sicherstellen, dass kein entsprechender Workflow existiert</small>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Workflow-Typ</label>
                    <select id="wfNewTypSelect" class="form-select form-select-sm">
                        <option value="">— wählen —</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Bezeichnung</label>
                    <input type="text" id="wfNewName" class="form-control form-control-sm"
                           maxlength="45" placeholder="z. B. Standard-Ablauf">
                </div>

                <hr class="my-2">
                <p class="text-muted small mb-2">
                    Schritte hinzufügen <span class="fst-italic">(optional – kann auch später erfolgen)</span>:
                </p>

                <div class="row g-2 align-items-end mb-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-muted mb-1">Aufgabe</label>
                        <select id="wfNewTeilSelect" class="form-select form-select-sm">
                            <option value="">— wählen —</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Reihenfolge</label>
                        <input type="number" id="wfNewOrder" class="form-control form-control-sm" min="0" value="1">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Tage danach</label>
                        <input type="number" id="wfNewDays" class="form-control form-control-sm" min="0" value="0">
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="button" id="wfNewAddStageBtn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Schritt
                        </button>
                    </div>
                </div>

                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Reihenfolge</th>
                        <th>Aufgabe</th>
                        <th>Tage danach</th>
                        <th class="text-end">Aktion</th>
                    </tr>
                    </thead>
                    <tbody id="wfNewStagedBody">
                    <tr id="wfNewStagedEmpty">
                        <td colspan="4" class="text-muted fst-italic small">Noch keine Schritte hinzugefügt.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="wfNewConfirmBtn" class="btn btn-primary btn-sm">
                    <i class="fas fa-save me-1"></i> Anlegen
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="wfAddStepModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-plus me-1"></i> Schritt hinzufügen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="wfAddStepWorkflowId">
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Aufgabe</label>
                    <select id="wfAddStepTeilSelect" class="form-select form-select-sm">
                        <option value="">— wählen —</option>
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Reihenfolge</label>
                        <input type="number" id="wfAddStepOrder" class="form-control form-control-sm" min="0" value="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Tage danach (min.)</label>
                        <input type="number" id="wfAddStepDays" class="form-control form-control-sm" min="0" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="wfAddStepConfirmBtn" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Hinzufügen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bestehenden Workflow dem Projekt zuordnen -->
<div class="modal fade" id="wfAssignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-link me-1"></i> Bestehenden Workflow hinzufügen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small text-muted mb-1">Workflow</label>
                <select id="wfAssignSelect" class="form-select form-select-sm">
                    <option value="">— wählen —</option>
                </select>
                <div id="wfAssignEmpty" class="form-text fst-italic d-none">
                    Keine weiteren Workflows verfügbar – alle sind diesem Projekt bereits zugeordnet.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="wfAssignConfirmBtn" class="btn btn-primary btn-sm">
                    <i class="fas fa-link me-1"></i> Hinzufügen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Workflow umbenennen -->
<div class="modal fade" id="wfRenameModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-pen me-1"></i> Workflow umbenennen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="wfRenameWorkflowId">
                <label class="form-label small text-muted mb-1">Neue Bezeichnung</label>
                <input type="text" id="wfRenameName" class="form-control form-control-sm"
                       maxlength="45" placeholder="z. B. Standard-Ablauf">
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="wfRenameConfirmBtn" class="btn btn-primary btn-sm">
                    <i class="fas fa-save me-1"></i> Speichern
                </button>
            </div>
        </div>
    </div>
</div>