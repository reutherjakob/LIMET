<?php
require_once "utils/_utils.php";
$mysqli = utils_connect_sql();

// Validate and sanitize input
$variantenID = filter_input(INPUT_GET, 'variantenID', FILTER_VALIDATE_INT);
$kosten = filter_input(INPUT_GET, 'kosten', FILTER_VALIDATE_FLOAT);
$projectID = $_SESSION['projectID'];
$elementID = $_SESSION['elementID'];

if (!$variantenID || !$kosten || !$projectID || !$elementID) {
    die("Invalid input data");
}

// Prepare the initial SELECT query
$stmt = $mysqli->prepare("SELECT Kosten FROM tabelle_projekt_varianten_kosten 
                          WHERE tabelle_Varianten_idtabelle_Varianten = ? 
                          AND tabelle_elemente_idTABELLE_Elemente = ?
                          AND tabelle_projekte_idTABELLE_Projekte = ?");

$stmt->bind_param("iii", $variantenID, $elementID, $projectID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows === 0) {
    // Insert new record
    $stmt = $mysqli->prepare("INSERT INTO tabelle_projekt_varianten_kosten
                              (tabelle_projekte_idTABELLE_Projekte,
                              tabelle_elemente_idTABELLE_Elemente,
                              tabelle_Varianten_idtabelle_Varianten,
                              Kosten) 
                              VALUES (?, ?, ?, ?)");

    $stmt->bind_param("iiid", $projectID, $elementID, $variantenID, $kosten);

    if ($stmt->execute()) {
        echo "Variante erfolgreich angelegt!";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    // Update existing record
    $stmt = $mysqli->prepare("UPDATE tabelle_projekt_varianten_kosten
                              SET Kosten = ?
                              WHERE tabelle_projekte_idTABELLE_Projekte = ? 
                              AND tabelle_elemente_idTABELLE_Elemente = ? 
                              AND tabelle_Varianten_idtabelle_Varianten = ?");

    $stmt->bind_param("diii", $kosten, $projectID, $elementID, $variantenID);

    if ($stmt->execute()) {
        echo "Variante erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();
$mysqli->close();
