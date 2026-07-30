<?php
// linkImageToProject.php – Ordnet ein Bild einem (weiteren) Projekt zu.
//   Standardziel = aktuelles Projekt (Session). Optional targetProjectID.
//   Das Ursprungsprojekt (tabelle_Files.tabelle_projekte_idTABELLE_Projekte)
//   bleibt unverändert – hier werden nur ZUSÄTZLICHE Zuordnungen gespeichert.
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

// Bild + Ursprungsprojekt holen
$chk = $mysqli->prepare("
    SELECT tabelle_projekte_idTABELLE_Projekte
    FROM tabelle_Files
    WHERE idtabelle_Files = ? AND tabelle_filetype_id = 1
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
$originProject = (int)$row[0];

// Zielprojekt existiert?
$chk2 = $mysqli->prepare("SELECT 1 FROM tabelle_projekte WHERE idTABELLE_Projekte = ?");
$chk2->bind_param('i', $targetProject);
$chk2->execute();
if (!$chk2->get_result()->fetch_row()) {
    $chk2->close(); $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Zielprojekt nicht gefunden']);
    exit;
}
$chk2->close();

// Gehört bereits dem Ursprungsprojekt → nichts zu tun
if ($targetProject === $originProject) {
    $mysqli->close();
    echo json_encode(['status' => 'ok', 'msg' => 'Bild gehört bereits diesem Projekt.']);
    exit;
}

$stmt = $mysqli->prepare("
    INSERT IGNORE INTO tabelle_Files_has_tabelle_Projekte
        (tabelle_Files_idtabelle_Files, tabelle_projekte_idTABELLE_Projekte)
    VALUES (?, ?)
");
$stmt->bind_param('ii', $imageID, $targetProject);
if ($stmt->execute()) {
    $added = $stmt->affected_rows > 0;
    echo json_encode([
        'status' => 'ok',
        'msg'    => $added ? 'Bild dem Projekt zugeordnet.' : 'Bild war bereits zugeordnet.'
    ]);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>
