<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$deviceID = getPostInt('gereatID', 0);
$inventarNr = getPostString('inventarNr');
$serienNr = getPostString('serienNr');
$anschaffungsJahr = getPostString('anschaffungsJahr');
$currentPlace = getPostString('currentPlace');

if ($deviceID !== 0) {
    $deviceIDSql = $deviceID; // valid integer from getPostInt
} else {
    $deviceIDSql = "NULL";
}

$sql = "INSERT INTO `LIMET_RB`.`tabelle_bestandsdaten` 
    (`Inventarnummer`,
     `Seriennummer`,
     `Anschaffungsjahr`,
     `tabelle_räume_has_tabelle_elemente_id`,
     `tabelle_geraete_idTABELLE_Geraete`,
     `Aktueller Ort`)
    VALUES (?, ?, ?, ?, $deviceIDSql, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sssis", $inventarNr, $serienNr, $anschaffungsJahr, $_SESSION["roombookID"], $currentPlace);

if ($stmt->execute()) {
    echo "Bestand hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
