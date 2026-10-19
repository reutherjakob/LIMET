<div class="modal fade" id="projImageProjectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-folder-plus me-1"></i> Projekte zuordnen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="projProjectModalImageID">
                <p class="text-muted small mb-2">Aktuell zugeordnete Projekte:</p>
                <div id="projProjectCurrentList" class="mb-3 d-flex flex-wrap gap-1">
                    <span class="text-muted fst-italic small">Lädt…</span>
                </div>
                <hr class="my-2">
                <p class="text-muted small mb-1">Zu weiterem Projekt hinzufügen:</p>

                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="projProjectSearch" class="form-control form-control-sm"
                           placeholder="Projekt suchen…">
                </div>

                <select id="projProjectPickerSelect" class="form-select form-select-sm" size="8">
                    <?php
                    global $mysqli;
                    $stmtP = $mysqli->prepare("
                        SELECT idTABELLE_Projekte, Projektname
                        FROM tabelle_projekte
                        ORDER BY Projektname
                    ");
                    $stmtP->execute();
                    foreach ($stmtP->get_result()->fetch_all(MYSQLI_ASSOC) as $p):
                        $pLabel = $p['Projektname'] ?? ('Projekt #' . (int)$p['idTABELLE_Projekte']);
                        ?>
                        <option value="<?= (int)$p['idTABELLE_Projekte'] ?>"
                                data-search="<?= htmlspecialchars(strtolower($pLabel)) ?>">
                            <?= htmlspecialchars($pLabel) ?>
                        </option>
                    <?php endforeach;
                    $stmtP->close(); ?>
                </select>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Schließen</button>
                <button type="button" id="projProjectLinkConfirmBtn" class="btn btn-primary btn-sm" disabled>
                    <i class="fas fa-plus me-1"></i> Zuordnen
                </button>
            </div>
        </div>
    </div>
</div>
