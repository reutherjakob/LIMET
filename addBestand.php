<?php
// 26 FX
require_once '_utils/_utils.php';
require_once '_utils/_csrf.php';
check_login();
require_csrf();
$mysqli = utils_connect_sql();

$deviceID = getPostInt('gereatID', 0);
$inventarNr = getPostString('inventarNr');
$serienNr = getPostString('serienNr');
$anschaffungsJahr = getPostString('anschaffungsJahr');
$currentPlace = getPostString('currentPlace');
$roombookID = getPostInt('roombookID', 0);

if ($roombookID === 0) {
    echo "Fehler: Kein Raum ausgewählt!";
    exit;
}


$sql = "INSERT INTO `LIMET_RB`.`tabelle_bestandsdaten` 
    (`Inventarnummer`, `Seriennummer`, `Anschaffungsjahr`, 
     `tabelle_räume_has_tabelle_elemente_id`, 
     `tabelle_geraete_idTABELLE_Geraete`, `Aktueller Ort`)
    VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);

$deviceIDParam = ($deviceID !== 0) ? $deviceID : null;

$stmt->bind_param("sssiss",
    $inventarNr,
    $serienNr,
    $anschaffungsJahr,
    $roombookID,
    $deviceIDParam,
    $currentPlace
);

if ($stmt->execute()) {
    echo "Bestand hinzugefügt!";
} else {

    echo "Fehler beim Hinzufügen.";
}

$stmt->close();
$mysqli->close();
?>
