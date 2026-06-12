<?php
/**
 * card_load_image_preview.php – Projektgalerie (Single Source of Truth)
 *
 * Features:
 *  - Filter: Raum, Vermerk-Gruppe, "ohne Zuordnung", Freitext
 *  - Sortierung: Neueste / Älteste / Name A→Z
 *  - Hover-Overlay mit Aktions-Buttons
 *  - Badges: Raum-Anzahl, Vermerk-Anzahl
 *  - Bulk-Modus: Raum zuordnen, Löschen
 *  - Upload, Delete, Room-Modal, Vermerk-Modal Handler
 *  - reloadProjectGallery() als globale Funktion
 */
global $mysqli;
$projectID = (int)($projectID ?? $_SESSION['projectID'] ?? 0);

// ── Alle Bilder mit Räumen & Vermerken laden ─────────────────────────────────
$stmt = $mysqli->prepare("
    SELECT f.`idtabelle_Files`, f.`Name`, f.`Timestamp`
    FROM `LIMET_RB`.`tabelle_Files` f
    WHERE f.`tabelle_projekte_idTABELLE_Projekte` = ?
      AND f.`tabelle_filetype_id` = 1
    ORDER BY f.`Timestamp` DESC
");
$stmt->bind_param('i', $projectID);
$stmt->execute();
$allImages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$raumByImage = [];
$vermerkByImage = [];
$allRaeume = [];
$allGruppen = [];

if (!empty($allImages)) {
    $imageIDs = array_column($allImages, 'idtabelle_Files');
    $placeholders = implode(',', array_fill(0, count($imageIDs), '?'));
    $types = str_repeat('i', count($imageIDs));

    $stmtR = $mysqli->prepare("
        SELECT fhr.tabelle_idfFile AS fileID,
               r.idTABELLE_Räume   AS raumID,
               r.Raumnr, r.Raumbezeichnung,
               r.`Raumbereich Nutzer` AS RaumbereichNutzer
        FROM tabelle_Files_has_tabelle_Raeume fhr
        INNER JOIN tabelle_räume r ON fhr.tabelle_idRaeume = r.idTABELLE_Räume
        WHERE fhr.tabelle_idfFile IN ($placeholders)
        ORDER BY r.Raumnr
    ");
    $stmtR->bind_param($types, ...$imageIDs);
    $stmtR->execute();
    foreach ($stmtR->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $raumByImage[$row['fileID']][] = $row;
        $allRaeume[$row['raumID']] = $row['Raumnr'] . ' – ' . $row['Raumbezeichnung'];
    }
    $stmtR->close();
    asort($allRaeume);

    $stmtV = $mysqli->prepare("
        SELECT fhv.tabelle_Files_idtabelle_Files AS fileID,
               v.idtabelle_Vermerke,
               LEFT(v.Vermerktext, 60) AS Kurztext,
               vg.idtabelle_Vermerkgruppe,
               vg.Gruppenname, vg.Datum
        FROM tabelle_Files_has_tabelle_Vermerke fhv
        INNER JOIN tabelle_Vermerke v
            ON fhv.tabelle_Vermerke_idtabelle_Vermerke = v.idtabelle_Vermerke
        INNER JOIN tabelle_Vermerkuntergruppe vu
            ON v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = vu.idtabelle_Vermerkuntergruppe
        INNER JOIN tabelle_Vermerkgruppe vg
            ON vu.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = vg.idtabelle_Vermerkgruppe
        WHERE fhv.tabelle_Files_idtabelle_Files IN ($placeholders)
        ORDER BY vg.Datum DESC
    ");
    $stmtV->bind_param($types, ...$imageIDs);
    $stmtV->execute();
    foreach ($stmtV->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $vermerkByImage[$row['fileID']][] = $row;
        $allGruppen[$row['idtabelle_Vermerkgruppe']] =
            $row['Gruppenname'] . ($row['Datum'] ? ' (' . $row['Datum'] . ')' : '');
    }
    $stmtV->close();
}

$imagesJson = [];
foreach ($allImages as $img) {
    $id = $img['idtabelle_Files'];
    $img['raeume'] = $raumByImage[$id] ?? [];
    $img['vermerke'] = $vermerkByImage[$id] ?? [];
    $imagesJson[] = $img;
}
?>

<div class="mt-1 card" id="projGalleryCard">

    <!-- ── Card Header ─────────────────────────────────────────────────────── -->
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <b><i class="fas fa-images me-1"></i> Projektfotos
                <span class="badge bg-secondary ms-1" id="galleryCntBadge"><?= count($allImages) ?></span>
            </b>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <!-- Bulk-Toggle -->
                <button type="button" id="bulkToggleBtn" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-check-square me-1"></i> Auswählen
                </button>
                <!-- Bulk-Aktionen -->
                <div id="bulkActions" class="d-none d-flex gap-1 align-items-center">
                    <span class="text-muted small me-1" id="bulkCountLabel">0 gewählt</span>
                    <button type="button" id="bulkRoomBtn" class="btn btn-outline-primary btn-sm" disabled>
                        <i class="fas fa-door-open me-1"></i> Raum
                    </button>
                    <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm" disabled>
                        <i class="fas fa-trash-alt me-1"></i> Löschen
                    </button>
                    <button type="button" id="bulkSelectAllBtn" class="btn btn-outline-dark btn-sm">Alle</button>
                    <button type="button" id="bulkCancelBtn" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Bild hinzufügen -->
                <button type="button" id="addProjectImage" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-plus"></i> Bild hinzufügen
                </button>
            </div>
        </div>

        <!-- ── Filter-Toolbar ─────────────────────────────────────────────── -->
        <div class="mt-2 d-flex flex-wrap gap-2 align-items-center" id="galleryFilterBar">
            <select id="galleryRaumFilter" class="form-select form-select-sm" style="max-width:200px;">
                <option value="">Alle Räume</option>
                <option value="__none__">— Kein Raum zugeordnet</option>
                <?php foreach ($allRaeume as $rId => $rLabel): ?>
                    <option value="<?= (int)$rId ?>"><?= htmlspecialchars($rLabel) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="galleryVermerkFilter" class="form-select form-select-sm" style="max-width:220px;">
                <option value="">Alle Vermerke</option>
                <option value="__none__">— Kein Vermerk zugeordnet</option>
                <?php foreach ($allGruppen as $gId => $gLabel): ?>
                    <option value="<?= (int)$gId ?>"><?= htmlspecialchars($gLabel) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="gallerySortSelect" class="form-select form-select-sm" style="max-width:160px;">
                <option value="newest">Neueste zuerst</option>
                <option value="oldest">Älteste zuerst</option>
                <option value="name">Name A→Z</option>
            </select>
            <button type="button" id="galleryResetFilter" class="btn btn-outline-secondary btn-sm d-none">
                <i class="fas fa-times me-1"></i> Filter zurücksetzen
            </button>
        </div>
    </div>

    <!-- ── Card Body ───────────────────────────────────────────────────────── -->
    <div class="card-body">
        <p class="text-muted fst-italic <?= empty($allImages) ? '' : 'd-none' ?>" id="galleryEmptyHint">
            Noch keine Fotos vorhanden.
        </p>
        <p class="text-muted fst-italic d-none small" id="galleryNoResultHint">
            <i class="fas fa-filter me-1"></i> Keine Bilder entsprechen den Filterkriterien.
        </p>

        <div id="projectGallery" class="row g-2">
            <?php foreach ($allImages as $img):
                $id = (int)$img['idtabelle_Files'];
                $raeume = $raumByImage[$id] ?? [];
                $vermerke = $vermerkByImage[$id] ?? [];
                $hasRoom = !empty($raeume);
                $hasVermerk = !empty($vermerke);
                ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 gallery-item"
                     data-image-id="<?= $id ?>"
                     data-name="<?= htmlspecialchars(strtolower($img['Name'])) ?>"
                     data-timestamp="<?= htmlspecialchars($img['Timestamp']) ?>"
                     data-raumids="<?= implode(',', array_column($raeume, 'raumID')) ?>"
                     data-vermerkgruppenids="<?= implode(',', array_unique(array_column($vermerke, 'idtabelle_Vermerkgruppe'))) ?>">

                    <div class="position-relative gallery-thumb-wrap">
                        <!-- Bulk Checkbox -->
                        <div class="position-absolute top-0 start-0 m-1 d-none bulk-checkbox-wrap" style="z-index:20;">
                            <input type="checkbox" class="form-check-input gallery-bulk-cb"
                                   data-image-id="<?= $id ?>" style="width:1.2em;height:1.2em;">
                        </div>

                        <!-- Hover-Overlay -->
                        <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100
                                d-flex flex-column justify-content-between p-1"
                             style="z-index:10; opacity:0; transition:opacity .18s;
                                background:rgba(0,0,0,0.38); border-radius:.375rem;">

                            <div class="d-flex justify-content-between pe-auto">
                                <button type="button" class="btn btn-secondary btn-sm proj-meta-btn p-1"
                                        data-image-id="<?= $id ?>" title="Info">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm project-gallery-delete-btn p-1"
                                        data-image-id="<?= $id ?>" title="Löschen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-end gap-1 pe-auto">
                                <button type="button" class="btn btn-outline-light btn-sm proj-vermerk-btn p-1"
                                        data-image-id="<?= $id ?>" title="Vermerk zuordnen">
                                    <i class="fas fa-comment-alt"></i>
                                </button>
                                <button type="button" class="btn btn-outline-light btn-sm proj-room-btn p-1"
                                        data-image-id="<?= $id ?>" title="Raum zuordnen">
                                    <i class="fas fa-door-open"></i>
                                </button>
                            </div>


                        </div>

                        <!-- Bild -->
                        <img src="https://limet-rb.com/Dokumente_RB/Images/<?= htmlspecialchars($img['Name']) ?>"
                             class="project-gallery-img img-fluid rounded w-100"
                             style="height:130px; object-fit:cover; cursor:zoom-in; display:block;"
                             alt="Projektfoto">

                        <!-- Badges -->
                        <div class="d-flex gap-1 flex-wrap mt-1" style="min-height:1.4rem;">
                            <?php if ($hasRoom): ?>
                                <span class="badge bg-primary" style="font-size:.6rem;"
                                      title="<?= htmlspecialchars(implode(', ', array_column($raeume, 'Raumnr'))) ?>">
                            <i class="fas fa-door-open"></i> <?= count($raeume) ?>
                        </span>
                            <?php endif; ?>
                            <?php if ($hasVermerk): ?>
                                <span class="badge bg-success" style="font-size:.6rem;"
                                      title="<?= htmlspecialchars(implode(', ', array_unique(array_column($vermerke, 'Gruppenname')))) ?>">
                            <i class="fas fa-comment-alt"></i> <?= count($vermerke) ?>
                        </span>
                            <?php endif; ?>
                            <?php if (!$hasRoom && !$hasVermerk): ?>
                                <span class="badge bg-light text-muted border" style="font-size:.6rem;">
                            <i class="fas fa-unlink"></i> ohne
                        </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-2 text-muted small" id="galleryCountInfo">
            <?= count($allImages) ?> Bild<?= count($allImages) !== 1 ? 'er' : '' ?>
        </div>
    </div>
</div>

<!-- ── Bulk-Raum-Modal ──────────────────────────────────────────────────────── -->
<div class="modal fade" id="bulkRoomModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-door-open me-1"></i> Raum für ausgewählte Bilder</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">
                    Raum wird allen <strong id="bulkRoomCount">0</strong> ausgewählten Bildern zugeordnet.
                </p>
                <select id="bulkRoomSelect" class="form-select form-select-sm">
                    <option value="">— Raum wählen —</option>
                    <?php
                    $stmtRAll = $mysqli->prepare(" 
                        SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung, `Raumbereich Nutzer`
                        FROM tabelle_räume
                        WHERE tabelle_projekte_idTABELLE_Projekte = ?
                        ORDER BY Raumnr
                    ");
                    $stmtRAll->bind_param('i', $projectID);
                    $stmtRAll->execute();
                    foreach ($stmtRAll->get_result()->fetch_all(MYSQLI_ASSOC) as $r): ?>
                        <option value="<?= (int)$r['idTABELLE_Räume'] ?>">
                            <?= htmlspecialchars($r['Raumnr'] . ' – ' . ($r['Raumbereich Nutzer'] ?? '') . ' – ' . $r['Raumbezeichnung']) ?>
                        </option>
                    <?php endforeach;
                    $stmtRAll->close(); ?>
                </select>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="bulkRoomConfirmBtn" class="btn btn-primary btn-sm" disabled>
                    <i class="fas fa-plus me-1"></i> Zuordnen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Bulk-Löschen-Modal ───────────────────────────────────────────────────── -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i> Bilder löschen?
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-1 text-muted small" id="bulkDeleteBody">
                Alle ausgewählten Bilder werden unwiderruflich gelöscht.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="bulkDeleteConfirmBtn" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt me-1"></i> Löschen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Styles ────────────────────────────────────────────────────────────────── -->
<style>
    .gallery-thumb-wrap:hover .gallery-overlay,
    .gallery-thumb-wrap:focus-within .gallery-overlay {
        opacity: 1 !important;
    }

    .gallery-item.bulk-selected .gallery-thumb-wrap {
        outline: 3px solid #0d6efd;
        border-radius: .375rem;
    }

    .gallery-item.bulk-mode .gallery-thumb-wrap {
        cursor: pointer;
    }
</style>