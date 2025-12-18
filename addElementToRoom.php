<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Cast session values to int to ensure they are numbers
$projectID = (int)$_SESSION["projectID"];
$elementID = (int)$_SESSION["elementID"];
$roomID = (int)$_SESSION["roomID"];

// Prepare SELECT statement with placeholders
$stmt = $mysqli->prepare("SELECT Kosten FROM tabelle_projekt_varianten_kosten WHERE tabelle_Varianten_idtabelle_Varianten = 1 AND tabelle_elemente_idTABELLE_Elemente = ? AND tabelle_projekte_idTABELLE_Projekte = ?");
$stmt->bind_param("ii", $elementID, $projectID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (($row["Kosten"] ?? null) === null) {
    // Prepare INSERT statement
    $stmt = $mysqli->prepare("INSERT INTO tabelle_projekt_varianten_kosten (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente, tabelle_Varianten_idtabelle_Varianten, Kosten) VALUES (?, ?, 1, '0')");
    $stmt->bind_param("ii", $projectID, $elementID);

    if ($stmt->execute()) {
        echo "Variante erfolgreich angelegt! \n";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Prepare defaults for INSERT statement for adding element to room
$timestamp = date("Y-m-d H:i:s");
$neuBestand = 1;
$anzahl = 1;
$standort = 1;
$verwendung = 1;
$variante = 1;

$stmt = $mysqli->prepare("INSERT INTO tabelle_räume_has_tabelle_elemente (TABELLE_Räume_idTABELLE_Räume, TABELLE_Elemente_idTABELLE_Elemente, `Neu/Bestand`, Anzahl, Standort, Verwendung, Timestamp, tabelle_Varianten_idtabelle_Varianten) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiissi", $roomID, $elementID, $neuBestand, $anzahl, $standort, $verwendung, $timestamp, $variante);

if ($stmt->execute()) {
    echo "Element zu Raum hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();

$mysqli->close();
