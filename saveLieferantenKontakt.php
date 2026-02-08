<?php
// 25 FX

include "utils/_utils.php";
check_login();

$name = getPostString('Name');
$vorname = getPostString('Vorname');
$tel = getPostString('Tel');
$adresse = getPostString('Adresse');
$plz = getPostString('PLZ');
$ort = getPostString('Ort');
$land = getPostString('Land');
$email = getPostString('Email');
$gebiet = getPostString('gebiet');
$abteilung = getPostInt('abteilung');
$lieferant = getPostInt('lieferant');
$ansprechID = getPostInt('ansprechID');

if ($name !== '' && $vorname !== '' && $tel !== '') {

    $mysqli = utils_connect_sql();

    $sql = "UPDATE `LIMET_RB`.`tabelle_ansprechpersonen`
            SET
                `Name` = ?,
                `Vorname` = ?,
                `Tel` = ?,
                `Adresse` = ?,
                `PLZ` = ?,
                `Ort` = ?,
                `Land` = ?,
                `Mail` = ?,
                `Gebietsbereich` = ?,
                `tabelle_abteilung_idtabelle_abteilung` = ?,
                `tabelle_lieferant_idTABELLE_Lieferant` = ?
            WHERE `idTABELLE_Ansprechpersonen` = ?";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $mysqli->error;
        exit;
    }

    $stmt->bind_param(
        "sssssssssiii",
        $name,
        $vorname,
        $tel,
        $adresse,
        $plz,
        $ort,
        $land,
        $email,
        $gebiet,
        $abteilung,
        $lieferant,
        $ansprechID
    );

    if ($stmt->execute()) {
        echo "Kontaktperson gespeichert!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $mysqli->close();

} else {
    echo "Fehler bei der Übertragung der Parameter";
}
?>
