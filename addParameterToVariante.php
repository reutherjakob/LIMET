<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();

$mysqli = utils_connect_sql();

// Validate and cast inputs to integers
$projectID = (int)$_SESSION["projectID"];
$elementID = (int)$_SESSION["elementID"];
$parameterID = (int)$_GET["parameterID"];
$variantenID = (int)$_GET["variantenID"];

// Define special parameters set
$CombinedParametersLeistung = [6, 9, 18, 82];

$CombinedParametersGeometrie = [2, 3, 4, 7];

// Parameter insertion function
function insertParameter($mysqli, $projectID, $elementID, $paramID, $variantID) {
    $stmt = $mysqli->prepare("INSERT INTO `LIMET_RB`.`tabelle_projekt_elementparameter` 
        (`tabelle_projekte_idTABELLE_Projekte`, `tabelle_elemente_idTABELLE_Elemente`, 
        `tabelle_parameter_idTABELLE_Parameter`, `tabelle_Varianten_idtabelle_Varianten`, 
        `Wert`, `Einheit`, `tabelle_planungsphasen_idTABELLE_Planungsphasen`) 
        VALUES (?, ?, ?, ?, '', '', 1)");
    $stmt->bind_param("iiii", $projectID, $elementID, $paramID, $variantID);
    $stmt->execute();
    $stmt->close();
}

// Step 1: Fetch existing parameters for the element, project, and variant
$stmt = $mysqli->prepare("SELECT tabelle_parameter_idTABELLE_Parameter 
    FROM LIMET_RB.tabelle_projekt_elementparameter 
    WHERE tabelle_projekte_idTABELLE_Projekte = ? 
      AND tabelle_elemente_idTABELLE_Elemente = ? 
      AND tabelle_Varianten_idtabelle_Varianten = ?");
$stmt->bind_param("iii", $projectID, $elementID, $variantenID);
$stmt->execute();
$result = $stmt->get_result();

$existingParams = [];
while ($row = $result->fetch_assoc()) {
    $existingParams[] = (int)$row['tabelle_parameter_idTABELLE_Parameter'];
}
$stmt->close();

// Step 2: Insert requested parameter if not already present
if (!in_array($parameterID, $existingParams)) {
    insertParameter($mysqli, $projectID, $elementID, $parameterID, $variantenID);
    $existingParams[] = $parameterID; // Add to array to avoid re-inserting below
}

// Step 3: Handle special parameters, insert only if missing
if (in_array($parameterID, $CombinedParametersLeistung)) {
    foreach ($CombinedParametersLeistung as $param) {
        if ($param !== $parameterID && !in_array($param, $existingParams)) {
            insertParameter($mysqli, $projectID, $elementID, $param, $variantenID);
        }
    }
}

if (in_array($parameterID, $CombinedParametersGeometrie)) {
    foreach ($CombinedParametersGeometrie as $param) {
        if ($param !== $parameterID && !in_array($param, $existingParams)) {
            insertParameter($mysqli, $projectID, $elementID, $param, $variantenID);
        }
    }
}

echo "Parameter hinzugefügt!";
$mysqli->close();
?>
