<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Use POST with sanitized integer inputs instead of GET
$parameterID = getPostInt("parameterID");
$variantenID = getPostInt("variantenID");

// Use SESSION variables as is, assuming they are already validated session state
$projectID = (int)$_SESSION["projectID"];
$elementID = $_SESSION["elementID"];

// Prepare the DELETE statement to prevent SQL injection
$stmt = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_projekt_elementparameter`
    WHERE `tabelle_projekte_idTABELLE_Projekte` = ?
    AND `tabelle_elemente_idTABELLE_Elemente` = ?
    AND `tabelle_parameter_idTABELLE_Parameter` = ?
    AND `tabelle_Varianten_idtabelle_Varianten` = ?");

if ($stmt === false) {
    // Prepare failed
    echo "Error preparing statement: " . $mysqli->error;
    $mysqli->close();
    exit;
}

// Bind parameters as integers
$stmt->bind_param("iiii", $projectID, $elementID, $parameterID, $variantenID);

// Execute and check result
if ($stmt->execute()) {
    echo "Parameter entfernt!";
} else {
    echo "Error deleting parameter: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
