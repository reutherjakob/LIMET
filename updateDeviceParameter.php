<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$wert = getPostString('wert', '');
$einheit = getPostString('einheit', '');
$deviceID = $_SESSION['deviceID'] ?? 0;
$parameterID = getPostInt('parameterID', 0);

$stmt = $mysqli->prepare("UPDATE `LIMET_RB`.`tabelle_geraete_has_tabelle_parameter`
SET `Wert` = ?, `Einheit` = ?
WHERE `TABELLE_Geraete_idTABELLE_Geraete` = ? AND `TABELLE_Parameter_idTABELLE_Parameter` = ?");

$stmt->bind_param("ssii", $wert, $einheit, $deviceID, $parameterID);

if ($stmt->execute()) {
echo "Parameter erfolgreich aktualisiert!";
} else {
echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
