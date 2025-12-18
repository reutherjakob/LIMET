<?php
// 25 FX - unused
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$imageID = getPostInt('imageID');

$roomID = getPostInt('roomID');

if (!$imageID || !$roomID) {
    echo "Ungültige Eingaben!";
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO LIMET_RB.tabelle_Files_has_tabelle_Raeume (tabelle_idfFile, tabelle_idRaeume) VALUES (?, ?)");
$stmt->bind_param("ii", $imageID, $roomID);

if ($stmt->execute()) {
    echo "Eintrag erfolgreich hinzugefügt!";
} else {
    echo "Fehler: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
