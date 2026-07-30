<?php
// gallery_grid.php – rendert NUR die Galerie-Items für AJAX-Reload.
//   scope = 'own'   -> Bilder dieses Projekts (Ursprung ODER zugeordnet)
//   scope = 'other' -> Bilder anderer Projekte
require_once '../utils/_utils.php';
check_login();

require_once __DIR__ . '/_gallery_helpers.php';

$projectID = (int)($_SESSION['projectID'] ?? 0);
if (!$projectID) { exit; }

$scope = ($_POST['scope'] ?? 'own') === 'other' ? 'other' : 'own';

$mysqli = utils_connect_sql();

if ($scope === 'own') {
    $stmt = $mysqli->prepare("
        SELECT f.`idtabelle_Files`, f.`Name`, f.`Timestamp`,
               f.`tabelle_projekte_idTABELLE_Projekte` AS originID
        FROM `LIMET_RB`.`tabelle_Files` f
        WHERE f.`tabelle_filetype_id` = 1
          AND (
                f.`tabelle_projekte_idTABELLE_Projekte` = ?
             OR EXISTS (
                    SELECT 1 FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Projekte` fp
                    WHERE fp.`tabelle_Files_idtabelle_Files` = f.`idtabelle_Files`
                      AND fp.`tabelle_projekte_idTABELLE_Projekte` = ?
                )
          )
        ORDER BY f.`Timestamp` DESC
    ");
    $stmt->bind_param('ii', $projectID, $projectID);
} else {
    $stmt = $mysqli->prepare("
        SELECT f.`idtabelle_Files`, f.`Name`, f.`Timestamp`,
               f.`tabelle_projekte_idTABELLE_Projekte` AS originID,
               p.`Projektname`
        FROM `LIMET_RB`.`tabelle_Files` f
        LEFT JOIN `LIMET_RB`.`tabelle_projekte` p
            ON p.`idTABELLE_Projekte` = f.`tabelle_projekte_idTABELLE_Projekte`
        WHERE f.`tabelle_filetype_id` = 1
          AND f.`tabelle_projekte_idTABELLE_Projekte` <> ?
          AND NOT EXISTS (
                SELECT 1 FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Projekte` fp
                WHERE fp.`tabelle_Files_idtabelle_Files` = f.`idtabelle_Files`
                  AND fp.`tabelle_projekte_idTABELLE_Projekte` = ?
            )
        ORDER BY p.`Projektname`, f.`Timestamp` DESC
    ");
    $stmt->bind_param('ii', $projectID, $projectID);
}
$stmt->execute();
$images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$rel = gallery_load_relations($mysqli, array_column($images, 'idtabelle_Files'));
$mysqli->close();

foreach ($images as $img):
    $id = (int)$img['idtabelle_Files'];
    if ($scope === 'own') {
        gallery_render_item(
            $img,
            $rel['raum'][$id]    ?? [],
            $rel['vermerk'][$id] ?? [],
            $rel['geraet'][$id]  ?? [],
            'own',
            ((int)$img['originID'] === $projectID)
        );
    } else {
        gallery_render_item(
            $img,
            $rel['raum'][$id]    ?? [],
            $rel['vermerk'][$id] ?? [],
            $rel['geraet'][$id]  ?? [],
            'other',
            false,
            (string)($img['Projektname'] ?? '')
        );
    }
endforeach;
