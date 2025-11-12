<?php
// 10 -2025 FX
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();


$stmt = $mysqli->prepare("INSERT INTO `LIMET_RB`.`tabelle_BO_Taetigkeiten_has_tabelle_räume`
                         (`tabelle_BO_Taetigkeiten_idtabelle_BO_Taetigkeiten`,
                          `tabelle_räume_idTABELLE_Räume`)
                         VALUES (?, ?)");

if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

$boID = intval($_GET["boID"]);
$roomID = intval($_SESSION["roomID"]);

$stmt->bind_param("ii", $boID, $roomID);

// Execute and check result
if ($stmt->execute()) {
    echo "[translate:BO erfolgreich aktualisiert!]";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
