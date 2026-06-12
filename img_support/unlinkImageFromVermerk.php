<?php
// unlinkImageFromVermerk.php
require_once '../utils/_utils.php';
check_login();

header('Content-Type: application/json');

$imageID   = getPostInt('imageID', 0);
$vermerkID = getPostInt('vermerkID', 0);

if ($imageID === 0 || $vermerkID === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
    exit;
}

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("
    DELETE FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke`
    WHERE `tabelle_Files_idtabelle_Files` = ?
      AND `tabelle_Vermerke_idtabelle_Vermerke` = ?
");
$stmt->bind_param("ii", $imageID, $vermerkID);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Verknüpfung entfernt.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>