<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli =  utils_connect_sql();
$deviceID = isset($_SESSION['deviceID']) ? intval($_SESSION['deviceID']) : 0;
$parameterID = getPostInt('parameterID',0);

if ($deviceID <= 0 || $parameterID <=0 ) {
    die("Invalid device or parameter ID");
}

$sqlDelete = "DELETE FROM `LIMET_RB`.`tabelle_geraete_has_tabelle_parameter`
              WHERE `TABELLE_Geraete_idTABELLE_Geraete` = ? AND `TABELLE_Parameter_idTABELLE_Parameter` = ?";

$stmt = $mysqli->prepare($sqlDelete);
$stmt->bind_param("ii", $deviceID, $parameterID);

if ($stmt->execute()) {
    echo "Parameter von Gerät entfernt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
