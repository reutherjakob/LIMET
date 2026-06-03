

<div class="modal fade" id="projImageRoomModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-door-open me-1"></i> Räume zuordnen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="projRoomModalImageID">
                <p class="text-muted small mb-2">Aktuell zugeordnete Räume:</p>
                <div id="projRoomCurrentList" class="mb-3 d-flex flex-wrap gap-1">
                    <span class="text-muted fst-italic small">Lädt…</span>
                </div>
                <hr class="my-2">
                <p class="text-muted small mb-1">Raum hinzufügen:</p>
                <select id="projRoomPickerSelect" class="form-select form-select-sm">
                    <option value="">— Raum wählen —</option>
                    <?php
                    global $mysqli;
                    $projectID = (int)$_SESSION["projectID"];
                    $stmtR = $mysqli->prepare("
                            SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung, `Raumbereich Nutzer`
                            FROM tabelle_räume
                            WHERE tabelle_projekte_idTABELLE_Projekte = ?
                            ORDER BY Raumnr
                        ");
                    $stmtR->bind_param('i', $projectID);
                    $stmtR->execute();
                    $alleRaeume = $stmtR->get_result()->fetch_all(MYSQLI_ASSOC);
                    $stmtR->close();

                    ?>
                    <?php foreach ($alleRaeume as $r): ?>
                        <option value="<?= (int)$r['idTABELLE_Räume'] ?>">
                            <?= htmlspecialchars($r['Raumnr'] . ' – ' . ($r['Raumbereich Nutzer'] ?? '') . ' – ' . $r['Raumbezeichnung']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Schließen</button>
                <button type="button" id="projRoomLinkConfirmBtn" class="btn btn-primary btn-sm" disabled>
                    <i class="fas fa-plus me-1"></i> Verknüpfen
                </button>
            </div>
        </div>
    </div>
</div>