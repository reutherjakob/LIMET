<?php
// getImagesForVermerk.php
// Returns image thumbnails for a given vermerkID as HTML
require_once 'utils/_utils.php';
check_login();

$vermerkID = getPostInt('vermerkID', 0);
if ($vermerkID === 0) {
    echo '';
    exit;
}

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("
    SELECT f.idtabelle_Files, f.Name
    FROM tabelle_Files f
    INNER JOIN tabelle_Files_has_tabelle_Vermerke fv
        ON f.idtabelle_Files = fv.tabelle_Files_idtabelle_Files
    WHERE fv.tabelle_Vermerke_idtabelle_Vermerke = ?
    ORDER BY f.Timestamp ASC
");
$stmt->bind_param('i', $vermerkID);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

if (empty($rows)) {
    echo '<div id="vermerkGallery' . $vermerkID . '" class="d-flex flex-wrap gap-1 mt-1"></div>';
    exit;
}
?>
<div id="vermerkGallery<?= $vermerkID ?>" class="d-flex flex-wrap gap-1 mt-1">
    <?php foreach ($rows as $row): ?>
        <div class="position-relative vermerk-img-wrapper" style="display:inline-block;">
            <button type="button"
                    class="btn btn-danger btn-sm position-absolute top-0 end-0 vermerk-unlink-img"
                    data-image-id="<?= (int)$row['idtabelle_Files'] ?>"
                    data-vermerk-id="<?= $vermerkID ?>"
                    title="Verknüpfung entfernen"
                    style="z-index:10; padding:1px 5px; font-size:0.65rem; opacity:0.85;">
                <i class="fas fa-times"></i>
            </button>
            <img src="https://limet-rb.com/Dokumente_RB/Images/<?= htmlspecialchars($row['Name']) ?>"
                 class="vermerk-gallery-img rounded border"
                 style="height:70px; width:70px; object-fit:cover; cursor:zoom-in;"
                 alt="Bild">
        </div>
    <?php endforeach; ?>
</div>