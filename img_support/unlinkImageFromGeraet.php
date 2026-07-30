<?php
// unlinkImageFromGeraet.php – Entfernt die Verknüpfung zwischen Bild und Gerät
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

$stmt = $mysqli->prepare("
    DELETE FROM tabelle_Files_has_tabelle_Geraete
    WHERE tabelle_idFile = ? AND tabelle_idGeraet = ?
");
$stmt->bind_param('ii', $imageID, $geraetID);
if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Geräteverknüpfung entfernt']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>
