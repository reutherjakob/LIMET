<?php
// unlinkImageFromRoom.php – Entfernt die Verknüpfung zwischen Bild und Raum
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

$stmt = $mysqli->prepare("
    DELETE FROM tabelle_Files_has_tabelle_Raeume
    WHERE tabelle_idfFile = ? AND tabelle_idRaeume = ?
");
$stmt->bind_param('ii', $imageID, $raumID);
if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Raumverknüpfung entfernt']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>