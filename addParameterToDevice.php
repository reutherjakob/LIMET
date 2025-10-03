<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$deviceID = (int)$_SESSION['deviceID'];
$parameterID = getPostInt('ParameterID');

// Define combined parameter sets (adjust as needed for your use case)
$CombinedParametersLeistung = [6, 9, 18, 82];
$CombinedParametersGeometrie = [2, 3, 4, 7];

// Helper: Insert parameter if not already present
function insertDeviceParameter($mysqli, $deviceID, $parameterID)
{
    // Check if already exists
    $checkStmt = $mysqli->prepare("SELECT 1 FROM LIMET_RB.tabelle_geraete_has_tabelle_parameter 
        WHERE TABELLE_Geraete_idTABELLE_Geraete = ? 
        AND TABELLE_Parameter_idTABELLE_Parameter = ?");
    $checkStmt->bind_param("ii", $deviceID, $parameterID);
    $checkStmt->execute();
    $checkStmt->store_result();
    $exists = $checkStmt->num_rows > 0;
    $checkStmt->close();

    if (!$exists) {
        $insertStmt = $mysqli->prepare("INSERT INTO LIMET_RB.tabelle_geraete_has_tabelle_parameter
            (TABELLE_Geraete_idTABELLE_Geraete, 
             TABELLE_Parameter_idTABELLE_Parameter,
             TABELLE_Planungsphasen_idTABELLE_Planungsphasen)
            VALUES (?, ?, 1)");
        $insertStmt->bind_param("ii", $deviceID, $parameterID);
        $insertStmt->execute();
        $insertStmt->close();
        return true;
    }
    return false;
}

// Step 1: Insert the requested parameter
$inserted = insertDeviceParameter($mysqli, $deviceID, $parameterID);

// Step 2: Insert combined parameters if needed
if (in_array($parameterID, $CombinedParametersLeistung)) {
    foreach ($CombinedParametersLeistung as $param) {
        if ($param !== $parameterID) {
            insertDeviceParameter($mysqli, $deviceID, $param);
        }
    }
}
if (in_array($parameterID, $CombinedParametersGeometrie)) {
    foreach ($CombinedParametersGeometrie as $param) {
        if ($param !== $parameterID) {
            insertDeviceParameter($mysqli, $deviceID, $param);
        }
    }
}

// Step 3: Output result
if ($inserted) {
    echo "Parameter zu Gerät hinzugefügt!";
} else {
    echo "Parameter bereits vorhanden!";
}

$mysqli->close();
?>
