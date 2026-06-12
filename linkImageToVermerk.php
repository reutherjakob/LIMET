<?php
// linkImageToVermerk.php
require_once 'utils/_utils.php';
check_login();

header('Content-Type: application/json');

$imageID   = getPostInt('imageID', 0);
$vermerkID = getPostInt('vermerkID', 0);

if ($imageID === 0 || $vermerkID === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
    exit;
}

$mysqli = utils_connect_sql();

// Prüfen ob Verknüpfung bereits existiert
$stmtCheck = $mysqli->prepare("
    SELECT 1 FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke`
    WHERE `tabelle_Files_idtabelle_Files` = ?
      AND `tabelle_Vermerke_idtabelle_Vermerke` = ?
");
$stmtCheck->bind_param("ii", $imageID, $vermerkID);
$stmtCheck->execute();
$stmtCheck->store_result();
if ($stmtCheck->num_rows > 0) {
    $stmtCheck->close();
    $mysqli->close();
    echo json_encode(['status' => 'already_linked', 'msg' => 'Bild ist bereits zugeordnet.']);
    exit;
}
$stmtCheck->close();

$stmt = $mysqli->prepare("
    INSERT INTO `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke`
        (`tabelle_Files_idtabelle_Files`, `tabelle_Vermerke_idtabelle_Vermerke`)
    VALUES (?, ?)
");
$stmt->bind_param("ii", $imageID, $vermerkID);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Vermerk verknüpft.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>