<?php
// 25 FX updateDeviceServicePrice.php
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
$lieferant     = getPostInt('lieferant', 0);
$lieferantVal  = $lieferant > 0 ? $lieferant : null;

// Basis-Update (immer)
$set    = "Datum = ?, Info = ?, Menge = ?, Wartungsart = ?, "
    . "WartungspreisProJahr = ?, tabelle_lieferant_idTABELLE_Lieferant = ?";
$types  = 'ssisdi';
$params = [$date, $info, $menge, $wartungsart, $wartungspreis, $lieferantVal];

// Projekt nur überschreiben, wenn übergeben
if (array_key_exists('project', $_POST)) {
    $project    = getPostInt('project', 0);
    $set   .= ", tabelle_projekte_idTABELLE_Projekte = ?";
    $types .= 'i';
    $params[] = $project > 0 ? $project : null;
}

// Kommentar nur überschreiben, wenn übergeben
if (array_key_exists('kommentar', $_POST)) {
    $set   .= ", Kommentar = ?";
    $types .= 's';
    $params[] = getPostString('kommentar');
}

$sql      = "UPDATE tabelle_wartungspreise SET $set WHERE idtabelle_wartungspreise = ?";
$types   .= 'i';
$params[] = $servicePriceID;

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    exit("Prepare failed: " . $mysqli->error);
}
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo "Wartungspreis aktualisiert.";
} else {
    echo "Fehler beim Aktualisieren: " . $stmt->error;
}

$stmt->close();
$mysqli->close();