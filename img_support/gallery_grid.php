<?php
// gallery_grid.php – rendert NUR die Galerie-Items (#projectGallery Inhalt) für AJAX-Reload
require_once '../utils/_utils.php';
check_login();

$projectID = (int)($_SESSION['projectID'] ?? 0);
if (!$projectID) { exit; }

$mysqli = utils_connect_sql();

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

if (!empty($allImages)) {
    $imageIDs = array_column($allImages, 'idtabelle_Files');
    $placeholders = implode(',', array_fill(0, count($imageIDs), '?'));
    $types = str_repeat('i', count($imageIDs));

    $stmtR = $mysqli->prepare("
        SELECT fhr.tabelle_idfFile AS fileID, r.idTABELLE_Räume AS raumID, r.Raumnr
        FROM tabelle_Files_has_tabelle_Raeume fhr
        INNER JOIN tabelle_räume r ON fhr.tabelle_idRaeume = r.idTABELLE_Räume
        WHERE fhr.tabelle_idfFile IN ($placeholders)
        ORDER BY r.Raumnr
    ");
    $stmtR->bind_param($types, ...$imageIDs);
    $stmtR->execute();
    foreach ($stmtR->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $raumByImage[$row['fileID']][] = $row;
    }
    $stmtR->close();

    $stmtV = $mysqli->prepare("
        SELECT fhv.tabelle_Files_idtabelle_Files AS fileID,
               vg.idtabelle_Vermerkgruppe, vg.Gruppenname
        FROM tabelle_Files_has_tabelle_Vermerke fhv
        INNER JOIN tabelle_Vermerke v
            ON fhv.tabelle_Vermerke_idtabelle_Vermerke = v.idtabelle_Vermerke
        INNER JOIN tabelle_Vermerkuntergruppe vu
            ON v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = vu.idtabelle_Vermerkuntergruppe
        INNER JOIN tabelle_Vermerkgruppe vg
            ON vu.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = vg.idtabelle_Vermerkgruppe
        WHERE fhv.tabelle_Files_idtabelle_Files IN ($placeholders)
    ");
    $stmtV->bind_param($types, ...$imageIDs);
    $stmtV->execute();
    foreach ($stmtV->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $vermerkByImage[$row['fileID']][] = $row;
    }
    $stmtV->close();
}
$mysqli->close();

foreach ($allImages as $img):
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
            <div class="position-absolute top-0 start-0 m-1 d-none bulk-checkbox-wrap" style="z-index:20;">
                <input type="checkbox" class="form-check-input gallery-bulk-cb"
                       data-image-id="<?= $id ?>" style="width:1.2em;height:1.2em;">
            </div>

            <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100
                    d-flex flex-column justify-content-between p-1 pe-none"
                 style="z-index:10; opacity:0; transition:opacity .18s;
                    background:rgba(0,0,0,0.38); border-radius:.375rem;">
                <div class="d-flex justify-content-between pe-auto">
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-secondary btn-sm proj-meta-btn p-1"
                                data-image-id="<?= $id ?>" title="Info">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button type="button" class="btn btn-light btn-sm proj-zoom-btn p-1"
                                data-image-id="<?= $id ?>" title="Vergrößern">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
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

            <img src="https://limet-rb.com/Dokumente_RB/Images/<?= htmlspecialchars($img['Name']) ?>"
                 class="project-gallery-img img-fluid rounded w-100"
                 style="height:130px; object-fit:cover; cursor:zoom-in; display:block;"
                 alt="Projektfoto">

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