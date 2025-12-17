<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$deviceID = getPostInt('deviceID', 0);  // Use POST deviceID, not session
$parameterID = getPostInt('parameterID', 0);

// Define combined parameter sets (adjust as needed for your use case)
$CombinedParametersLeistung = [6, 9, 18, 82];
$CombinedParametersGeometrie = [2, 3, 4, 7];

// Helper: Insert parameter if not already present
function insertDeviceParameter($mysqli, $deviceID, $parameterID) {
    // Validate device exists
    $deviceCheck = $mysqli->prepare("SELECT idTABELLE_Geraete FROM tabelle_geraete WHERE idTABELLE_Geraete = ?");
    $deviceCheck->bind_param("i", $deviceID);
    $deviceCheck->execute();
    $deviceCheck->store_result();
    if ($deviceCheck->num_rows === 0) {
        $deviceCheck->close();
        return false; // Device doesn't exist
    }
    $deviceCheck->close();

    // Validate parameter exists
    $paramCheck = $mysqli->prepare("SELECT idTABELLE_Parameter FROM tabelle_parameter WHERE idTABELLE_Parameter = ?");
    $paramCheck->bind_param("i", $parameterID);
    $paramCheck->execute();
    $paramCheck->store_result();
    if ($paramCheck->num_rows === 0) {
        $paramCheck->close();
        error_log("Invalid ParameterID: $parameterID does not exist in tabelle_Parameter");
        return false; // Parameter doesn't exist
    }
    $paramCheck->close();

    $checkStmt = $mysqli->prepare("SELECT 1 FROM LIMET_RB.tabelle_geraete_has_tabelle_parameter 
        WHERE TABELLE_Geraete_idTABELLE_Geraete = ? AND TABELLE_Parameter_idTABELLE_Parameter = ?");
    $checkStmt->bind_param("ii", $deviceID, $parameterID);
    $checkStmt->execute();
    $checkStmt->store_result();
    $exists = $checkStmt->num_rows > 0;
    $checkStmt->close();

    if (!$exists) {
        $insertStmt = $mysqli->prepare("INSERT INTO tabelle_geraete_has_tabelle_parameter
            (TABELLE_Geraete_idTABELLE_Geraete, TABELLE_Parameter_idTABELLE_Parameter, TABELLE_Planungsphasen_idTABELLE_Planungsphasen)
            VALUES (?, ?, 1)");
        $insertStmt->bind_param("ii", $deviceID, $parameterID);
        if (!$insertStmt->execute()) {
            error_log("Insert failed: " . $insertStmt->error);
            $insertStmt->close();
            return false;
        }
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
