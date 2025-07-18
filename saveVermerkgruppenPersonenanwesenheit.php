<?php

require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Get and validate input parameters safely
$anwesenheit        = filter_input(INPUT_GET, 'anwesenheit', FILTER_VALIDATE_INT);
$groupID            = filter_input(INPUT_GET, 'groupID', FILTER_VALIDATE_INT);
$ansprechpersonenID = filter_input(INPUT_GET, 'ansprechpersonenID', FILTER_VALIDATE_INT);

// Check that all inputs are valid
if ($anwesenheit === null || $groupID === null || $ansprechpersonenID === null ||
    $anwesenheit === false || $groupID === false || $ansprechpersonenID === false) {
    die("Ungültige Eingabe.");
}

$sql = "UPDATE tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen
        SET Anwesenheit = ?
        WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?
        AND tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen = ?";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

// Bind parameters: 3 integers (i = int)
$stmt->bind_param("iii", $anwesenheit, $groupID, $ansprechpersonenID);

if ($stmt->execute()) {
    echo "Anwesenheit aktualisiert!";
} else {
    echo "Fehler: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
