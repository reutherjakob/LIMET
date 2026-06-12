<?php
// Gibt alle Projektbilder als JSON zurück (für AJAX Galerie-Picker)
require_once '../utils/_utils.php';
check_login();

$projectID = (int)($_SESSION["projectID"] ?? 0);
if (!$projectID) {
    echo json_encode([]);
    exit;
}

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("
    SELECT `idtabelle_Files`, `Name`, `Timestamp`
    FROM `LIMET_RB`.`tabelle_Files`
    WHERE `tabelle_projekte_idTABELLE_Projekte` = ?
      AND `tabelle_filetype_id` = 1
    ORDER BY `Timestamp` DESC
");
$stmt->bind_param('i', $projectID);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

header('Content-Type: application/json');
echo json_encode($rows);
?>