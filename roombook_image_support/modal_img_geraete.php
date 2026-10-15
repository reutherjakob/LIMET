<div class="modal fade" id="projImageGeraetModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-plug me-1"></i> Geräte zuordnen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="projGeraetModalImageID">
                <p class="text-muted small mb-2">Aktuell zugeordnete Geräte:</p>
                <div id="projGeraetCurrentList" class="mb-3 d-flex flex-wrap gap-1">
                    <span class="text-muted fst-italic small">Lädt…</span>
                </div>
                <hr class="my-2">
                <p class="text-muted small mb-1">Gerät hinzufügen:</p>

                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="projGeraetSearch" class="form-control form-control-sm"
                           placeholder="Suche nach Hersteller / Typ…">
                </div>

                <select id="projGeraetPickerSelect" class="form-select form-select-sm" size="8">
                    <?php
                    global $mysqli;
                    $projectID = (int)$_SESSION["projectID"];
                    // Geräte dieses Projekts über Bestandsdaten -> Raum-Element -> Raum
                    $stmtG = $mysqli->prepare("
                        SELECT DISTINCT g.idTABELLE_Geraete AS geraetID,
                               g.Typ,
                               h.Hersteller,
                               bd.Inventarnummer,
                               bd.Seriennummer
                        FROM tabelle_geraete g
                        LEFT JOIN tabelle_hersteller h
                            ON h.idtabelle_hersteller = g.tabelle_hersteller_idtabelle_hersteller
                        INNER JOIN tabelle_bestandsdaten bd
                            ON bd.tabelle_geraete_idTABELLE_Geraete = g.idTABELLE_Geraete
                        INNER JOIN tabelle_räume_has_tabelle_elemente rhe
                            ON rhe.id = bd.tabelle_räume_has_tabelle_elemente_id
                        INNER JOIN tabelle_räume r
                            ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
                        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                        ORDER BY h.Hersteller, g.Typ
                    ");
                    $stmtG->bind_param('i', $projectID);
                    $stmtG->execute();
                    $alleGeraete = $stmtG->get_result()->fetch_all(MYSQLI_ASSOC);
                    $stmtG->close();

                    if (empty($alleGeraete)):
                        ?>
                        <option value="" disabled>Keine Geräte in diesem Projekt gefunden</option>
                    <?php else:
                        foreach ($alleGeraete as $g):
                            $label = trim(($g['Hersteller'] ?? '') . ' ' . ($g['Typ'] ?? ''));
                            if ($label === '') $label = 'Gerät #' . (int)$g['geraetID'];
                            $extra = [];
                            if (!empty($g['Inventarnummer'])) $extra[] = 'Inv. ' . $g['Inventarnummer'];
                            if (!empty($g['Seriennummer']))   $extra[] = 'SN ' . $g['Seriennummer'];
                            if ($extra) $label .= ' (' . implode(', ', $extra) . ')';
                            ?>
                            <option value="<?= (int)$g['geraetID'] ?>"
                                    data-search="<?= htmlspecialchars(strtolower($label)) ?>">
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach;
                    endif; ?>
                </select>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Schließen</button>
                <button type="button" id="projGeraetLinkConfirmBtn" class="btn btn-info btn-sm text-dark" disabled>
                    <i class="fas fa-plus me-1"></i> Verknüpfen
                </button>
            </div>
        </div>
    </div>
</div>
