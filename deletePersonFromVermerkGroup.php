<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$groupID = getPostInt('groupID');
$ansprechpersonenID = getPostInt('ansprechpersonenID');

if ($groupID === false || $ansprechpersonenID === false) {
    echo "Invalid input.";
    $mysqli->close();
    exit;
}

// Prepare the DELETE statement to prevent SQL injection
$stmt = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen`
    WHERE `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe` = ? 
    AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen` = ?");

if ($stmt === false) {
    echo "Error preparing statement: " . $mysqli->error;
    $mysqli->close();
    exit;
}

// Bind parameters as integers
$stmt->bind_param("ii", $groupID, $ansprechpersonenID);

// Execute the statement
if ($stmt->execute()) {
    echo "Person entfernt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
