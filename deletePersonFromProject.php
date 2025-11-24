<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

// Use POST for personID and sanitize as integer
$personID = getPostInt("personID");

// Use SESSION variable for projectID, assuming it is validated securely
$projectID = $_SESSION["projectID"];

// Prepare statement to sanitize input and prevent SQL injection
$stmt = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_projekte_has_tabelle_ansprechpersonen`
    WHERE `TABELLE_Projekte_idTABELLE_Projekte` = ? 
    AND `TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen` = ?");

if ($stmt === false) {
	echo "Error preparing statement: " . $mysqli->error;
	$mysqli->close();
	exit;
}

// Bind parameters as integers
$stmt->bind_param("ii", $projectID, $personID);

if ($stmt->execute()) {
	echo "Person erfolgreich von Projekt entfernt!";
} else {
	echo "Error deleting person: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
