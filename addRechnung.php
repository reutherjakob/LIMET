<?php
// 25 FX
require_once "utils/_utils.php";
check_login();
session_start();
$mysqli = utils_connect_sql();

$lotID = getPostInt('lotID');
$rechnungNr = getPostString('rechnungNr');
$teilRechnungNr = getPostString('teilRechnungNr');
$rechnungAusstellungsdatum = getPostString('rechnungAusstellungsdatum');
$rechnungEingangsdatum = getPostString('rechnungEingangsdatum');
$rechnungSum = getPostString('rechnungSum');
$rechnungBearbeiter = getPostString('rechnungBearbeiter');
$rechnungSchlussrechnung = getPostInt('rechnungSchlussrechnung', 0);

$stmt = $mysqli->prepare("INSERT INTO `LIMET_RB`.`tabelle_rechnungen`
            (`tabelle_lose_extern_idtabelle_Lose_Extern`, `Nummer`, `InterneNummer`, 
             `Ausstellungsdatum`, `Eingangsdatum`, `Rechnungssumme`, 
             `Bearbeiter`, `Schlussrechnung`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    $ausgabe = "Prepare failed: " . $mysqli->error;
} else {
    $stmt->bind_param(
        'isssssii',
        $lotID,
        $rechnungNr,
        $teilRechnungNr,
        $rechnungAusstellungsdatum,
        $rechnungEingangsdatum,
        $rechnungSum,
        $rechnungBearbeiter,
        $rechnungSchlussrechnung
    );

    if ($stmt->execute()) {
        $ausgabe = "Rechnung erfolgreich hinzugefügt! \n";
    } else {
        $ausgabe = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
echo $ausgabe;

?>
