<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

// Use utility functions to get sanitized POST inputs
$id = getPostInt('id', 0);
$variantenID = getPostInt('variantenID', 0);
$standort = getPostString('standort', '');
$verwendung = getPostString('verwendung', '');
$bestand = getPostString('bestand', '');
$amount = getPostInt('amount', 0);
$comment = getPostString('comment', '');

// Validate essential inputs
if ($id === 0 || $variantenID === 0) {
    echo "Ungültige Eingaben!";
    exit;
}

// Get elementID securely
$stmt = $mysqli->prepare("SELECT TABELLE_Elemente_idTABELLE_Elemente FROM tabelle_räume_has_tabelle_elemente WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    echo "Eintrag nicht gefunden!";
    exit;
}

$elementID = $row["TABELLE_Elemente_idTABELLE_Elemente"];

// Check if variant cost exists
$stmt = $mysqli->prepare("SELECT Kosten FROM tabelle_projekt_varianten_kosten WHERE tabelle_Varianten_idtabelle_Varianten = ? AND tabelle_elemente_idTABELLE_Elemente = ? AND tabelle_projekte_idTABELLE_Projekte = ?");
$stmt->bind_param("iii", $variantenID, $elementID, $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Insert new variant cost if not present
if (is_null($row["Kosten"])) {
    $insert = $mysqli->prepare("INSERT INTO tabelle_projekt_varianten_kosten (tabelle_projekte_idTABELLE_Projekte,tabelle_elemente_idTABELLE_Elemente,tabelle_Varianten_idtabelle_Varianten,Kosten) VALUES (?, ?, ?, 0)");
    $insert->bind_param("iii", $_SESSION["projectID"], $elementID, $variantenID);
    if ($insert->execute()) {
        echo "Variante erfolgreich angelegt mit Kosten 0! ";
    } else {
        echo "Fehler beim Anlegen der Variante: " . $mysqli->error;
    }
    $insert->close();
}

// Update room element entry
$update = $mysqli->prepare("UPDATE tabelle_räume_has_tabelle_elemente SET Standort = ?, Verwendung = ?, `Neu/Bestand` = ?, Anzahl = ?, Kurzbeschreibung = ?, Timestamp = ?, tabelle_Varianten_idtabelle_Varianten = ? WHERE id = ?");
$timestamp = date("Y-m-d H:i:s");
$update->bind_param("ssssssii", $standort, $verwendung, $bestand, $amount, $comment, $timestamp, $variantenID, $id);

if ($update->execute()) {
    echo "Raumbucheintrag erfolgreich aktualisiert!";
} else {
    echo "Fehler beim Aktualisieren: " . $mysqli->error;
}

$update->close();
$mysqli->close();
?>
