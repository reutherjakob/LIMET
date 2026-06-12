<?php
// linkImageToRoom.php – Verknüpft ein Bild mit einem Raum
require_once '../utils/_utils.php';
check_login();

header('Content-Type: application/json');

$imageID   = filter_input(INPUT_POST, 'imageID',  FILTER_VALIDATE_INT);
$raumID    = filter_input(INPUT_POST, 'raumID',   FILTER_VALIDATE_INT);
$projectID = (int)($_SESSION['projectID'] ?? 0);

if (!$imageID || !$raumID || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter']);
    exit;
}

$mysqli = utils_connect_sql();

// Sicherheitscheck: Bild gehört zum Projekt
$chk = $mysqli->prepare("
    SELECT 1 FROM tabelle_Files
    WHERE idtabelle_Files = ? AND tabelle_projekte_idTABELLE_Projekte = ?
");
$chk->bind_param('ii', $imageID, $projectID);
$chk->execute();
if (!$chk->get_result()->fetch_row()) {
    $chk->close(); $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Bild nicht gefunden']);
    exit;
}
$chk->close();

// Sicherheitscheck: Raum gehört zum Projekt
$chk2 = $mysqli->prepare("
    SELECT 1 FROM tabelle_räume
    WHERE idTABELLE_Räume = ? AND tabelle_projekte_idTABELLE_Projekte = ?
");
$chk2->bind_param('ii', $raumID, $projectID);
$chk2->execute();
if (!$chk2->get_result()->fetch_row()) {
    $chk2->close(); $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Raum nicht gefunden']);
    exit;
}
$chk2->close();

// INSERT IGNORE → kein Fehler bei Doppelverknüpfung
$stmt = $mysqli->prepare("
    INSERT IGNORE INTO tabelle_Files_has_tabelle_Raeume
        (tabelle_idfFile, tabelle_idRaeume)
    VALUES (?, ?)
");
$stmt->bind_param('ii', $imageID, $raumID);
if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Raum verknüpft']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>