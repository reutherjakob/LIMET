<?php
// unlinkImageFromProject.php – Entfernt eine ZUSÄTZLICHE Projektzuordnung.
//   Das Ursprungsprojekt kann NICHT entfernt werden (sonst würde das Bild
//   verwaisen). Standardziel = aktuelles Projekt (Session).
require_once '../utils/_utils.php';
check_login();

header('Content-Type: application/json');

$imageID       = filter_input(INPUT_POST, 'imageID',         FILTER_VALIDATE_INT);
$targetProject = filter_input(INPUT_POST, 'targetProjectID', FILTER_VALIDATE_INT);
$sessionProj   = (int)($_SESSION['projectID'] ?? 0);

if (!$targetProject) {
    $targetProject = $sessionProj;
}

if (!$imageID || !$targetProject) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter']);
    exit;
}

$mysqli = utils_connect_sql();

// Ursprungsprojekt ermitteln
$chk = $mysqli->prepare("
    SELECT tabelle_projekte_idTABELLE_Projekte
    FROM tabelle_Files WHERE idtabelle_Files = ?
");
$chk->bind_param('i', $imageID);
$chk->execute();
$row = $chk->get_result()->fetch_row();
$chk->close();

if (!$row) {
    $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Bild nicht gefunden']);
    exit;
}

if ((int)$row[0] === $targetProject) {
    $mysqli->close();
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Ursprungsprojekt kann nicht entfernt werden.'
    ]);
    exit;
}

$stmt = $mysqli->prepare("
    DELETE FROM tabelle_Files_has_tabelle_Projekte
    WHERE tabelle_Files_idtabelle_Files = ?
      AND tabelle_projekte_idTABELLE_Projekte = ?
");
$stmt->bind_param('ii', $imageID, $targetProject);
if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Projektzuordnung entfernt.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>
