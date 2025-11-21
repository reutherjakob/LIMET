<?php
// 25Fx
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$imageID = getPostInt('imageID', 0);
$roomID = getPostInt('roomID', 0);
if ($imageID === 0 || $roomID === 0) {
    die("Invalid parameters.");
}

$sqlDelete = "DELETE FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Raeume` WHERE `tabelle_idfFile` = ? AND `tabelle_idRaeume` = ?";

$stmt = $mysqli->prepare($sqlDelete);
$stmt->bind_param("ii", $imageID, $roomID);

if ($stmt->execute()) {
    echo "Bild von Raum entfernt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
