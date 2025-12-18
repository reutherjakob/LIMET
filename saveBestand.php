<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$geraeteID = getPostInt('geraeteID');
if ($geraeteID !== 0) {
    $sql = "UPDATE `LIMET_RB`.`tabelle_bestandsdaten`
            SET
                `Inventarnummer` = ?,
                `Seriennummer` = ?,
                `Anschaffungsjahr` = ?,
                `Aktueller Ort` = ?,
                `tabelle_geraete_idTABELLE_Geraete` = ?
            WHERE `idtabelle_bestandsdaten` = ?";
} else {
    $sql = "UPDATE `LIMET_RB`.`tabelle_bestandsdaten`
            SET
                `Inventarnummer` = ?,
                `Seriennummer` = ?,
                `Anschaffungsjahr` = ?,
                `Aktueller Ort` = ?
            WHERE `idtabelle_bestandsdaten` = ?";
}

$inventarNr = getPostString('inventarNr');
$serienNr = getPostString('serienNr');
$anschaffungsJahr = getPostString('anschaffungsJahr');
$currentPlace = getPostString('currentPlace');
$bestandID = getPostInt('bestandID');

if ($stmt = $mysqli->prepare($sql)) {
    if ($geraeteID !== 0) {
        $stmt->bind_param(
            "sssdii",
            $inventarNr,
            $serienNr,
            $anschaffungsJahr,
            $currentPlace,
            $geraeteID,
            $bestandID
        );
    } else {
        $stmt->bind_param(
            "sssdi",
            $inventarNr,
            $serienNr,
            $anschaffungsJahr,
            $currentPlace,
            $bestandID
        );
    }

    if ($stmt->execute()) {
        echo "Bestand geändert!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$mysqli->close();
