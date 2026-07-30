<?php
// linkImageToGeraet.php – Verknüpft ein Bild mit einem Gerät
require_once '../utils/_utils.php';
check_login();

header('Content-Type: application/json');

$imageID   = filter_input(INPUT_POST, 'imageID',  FILTER_VALIDATE_INT);
$geraetID  = filter_input(INPUT_POST, 'geraetID', FILTER_VALIDATE_INT);
$projectID = (int)($_SESSION['projectID'] ?? 0);

if (!$imageID || !$geraetID || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter']);
    exit;
}

$mysqli = utils_connect_sql();

// Sicherheitscheck: Bild gehört zum aktuellen Projekt (Ursprung ODER zugeordnet)
$chk = $mysqli->prepare("
    SELECT 1 FROM tabelle_Files f
    WHERE f.idtabelle_Files = ?
      AND (
            f.tabelle_projekte_idTABELLE_Projekte = ?
         OR EXISTS (
                SELECT 1 FROM tabelle_Files_has_tabelle_Projekte fp
                WHERE fp.tabelle_Files_idtabelle_Files = f.idtabelle_Files
                  AND fp.tabelle_projekte_idTABELLE_Projekte = ?
            )
      )
");
$chk->bind_param('iii', $imageID, $projectID, $projectID);
$chk->execute();
if (!$chk->get_result()->fetch_row()) {
    $chk->close(); $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Bild nicht gefunden']);
    exit;
}
$chk->close();

// Sicherheitscheck: Gerät existiert
$chk2 = $mysqli->prepare("SELECT 1 FROM tabelle_geraete WHERE idTABELLE_Geraete = ?");
$chk2->bind_param('i', $geraetID);
$chk2->execute();
if (!$chk2->get_result()->fetch_row()) {
    $chk2->close(); $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Gerät nicht gefunden']);
    exit;
}
$chk2->close();

// INSERT IGNORE → kein Fehler bei Doppelverknüpfung
$stmt = $mysqli->prepare("
    INSERT IGNORE INTO tabelle_Files_has_tabelle_Geraete
        (tabelle_idFile, tabelle_idGeraet)
    VALUES (?, ?)
");
$stmt->bind_param('ii', $imageID, $geraetID);
if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Gerät verknüpft']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>
