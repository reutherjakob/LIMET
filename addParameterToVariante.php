<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

$mysqli = utils_connect_sql();

// Validate and cast inputs to integers
$projectID = (int)$_SESSION["projectID"];
$elementID = (int)$_SESSION["elementID"];
$parameterID = (int)$_GET["parameterID"];
$variantenID = (int)$_GET["variantenID"];

// Define special parameters set
$specialParams = [6, 9, 18, 82];

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

// Insert requested parameter
insertParameter($mysqli, $projectID, $elementID, $parameterID, $variantenID);

// Handle special parameters
if (in_array($parameterID, $specialParams)) {
    foreach ($specialParams as $param) {
        if ($param !== $parameterID) {
            insertParameter($mysqli, $projectID, $elementID, $param, $variantenID);
        }
    }
}

echo "Parameter hinzugefügt!";
$mysqli->close();
?>
