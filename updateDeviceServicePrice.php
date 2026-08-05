<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$servicePriceID = getPostInt('servicePriceID', 0);
if ($servicePriceID < 1) {
    die("Ungültige ID.");
}

$date          = getPostDate('date');
$info          = getPostString('info');
$menge         = getPostInt('menge', 0);
$wartungsart   = getPostString('wartungsart', '0');
$wartungspreis = getPostFloat('wartungspreis', 0.0);
$project       = getPostInt('project', 0);
$lieferant     = getPostInt('lieferant', 0);
$kommentar     = getPostString('kommentar');

// NULL statt 0 in die FK-Spalten schreiben
$projectVal   = $project   > 0 ? $project   : null;
$lieferantVal = $lieferant > 0 ? $lieferant : null;

$sql = "UPDATE tabelle_wartungspreise
        SET Datum                                 = ?,
            Info                                  = ?,
            Menge                                 = ?,
            Wartungsart                           = ?,
            WartungspreisProJahr                  = ?,
            tabelle_projekte_idTABELLE_Projekte   = ?,
            tabelle_lieferant_idTABELLE_Lieferant = ?,
            Kommentar                             = ?
        WHERE idtabelle_wartungspreise = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    exit("Prepare failed: " . $mysqli->error);
}

// s=Datum, s=Info, i=Menge, s=Wartungsart, d=Preis, i=Projekt, i=Lieferant, s=Kommentar, i=ID
$stmt->bind_param(
    'ssisdiisi',
    $date,
    $info,
    $menge,
    $wartungsart,
    $wartungspreis,
    $projectVal,
    $lieferantVal,
    $kommentar,
    $servicePriceID
);

if ($stmt->execute()) {
    echo "Wartungspreis aktualisiert.";
} else {
    echo "Fehler beim Aktualisieren: " . $stmt->error;
}

$stmt->close();
$mysqli->close();