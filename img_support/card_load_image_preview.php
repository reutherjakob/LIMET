<div class="mt-1 card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <b><i class="fas fa-images me-1"></i> Projektfotos</b>
        <button type="button" id="addProjectImage" class="btn btn-outline-dark btn-sm">
            <i class="fas fa-plus"></i> Bild hinzufügen
        </button>
    </div>
    <div class="card-body">
        <?php
        global $mysqli;
        $stmt = $mysqli->prepare("
                        SELECT `idtabelle_Files`, `Name`
                        FROM `LIMET_RB`.`tabelle_Files`
                        WHERE `tabelle_projekte_idTABELLE_Projekte` = ?
                          AND `tabelle_filetype_id` = 1
                        ORDER BY `Timestamp` DESC
                    ");
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        if (empty($rows)): ?>
            <p class="text-muted fst-italic" id="galleryEmptyHint">Noch keine Fotos vorhanden.</p>
            <div id="projectGallery" class="d-flex flex-wrap gap-2"></div>
        <?php else: ?>
            <div id="projectGallery" class="d-flex flex-wrap gap-2">

                <?php foreach ($rows as $row): ?>
                    <div class="position-relative" style="display:inline-block;">
                        <div class="position-absolute top-0 end-0 m-1 d-flex gap-1" style="z-index:10;">
                            <button type="button"
                                    class="btn btn-secondary btn-sm proj-meta-btn"
                                    data-image-id="<?= (int)$row['idtabelle_Files'] ?>"
                                    title="Metadaten anzeigen"
                                    style="opacity:0.85;">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-outline-success btn-sm proj-vermerk-btn"
                                    data-image-id="<?= (int)$row['idtabelle_Files'] ?>"
                                    title="Vermerk zuordnen"
                                    style="opacity:0.85; background:rgba(255,255,255,0.85);">
                                <i class="fas fa-comment-alt"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm proj-room-btn"
                                    data-image-id="<?= (int)$row['idtabelle_Files'] ?>"
                                    title="Raum zuordnen"
                                    style="opacity:0.85; background:rgba(255,255,255,0.85);">
                                <i class="fas fa-door-open"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-danger btn-sm project-gallery-delete-btn"
                                    data-image-id="<?= (int)$row['idtabelle_Files'] ?>"
                                    title="Bild löschen"
                                    style="opacity:0.85;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <img src="https://limet-rb.com/Dokumente_RB/Images/<?= htmlspecialchars($row['Name']) ?>"
                             class="project-gallery-img rounded"
                             style="height:160px; width:160px; object-fit:cover; cursor:zoom-in;"
                             alt="Projektfoto">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
