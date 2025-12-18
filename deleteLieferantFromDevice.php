<?php
// 25 FX
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

$deviceID = isset($_SESSION["deviceID"]) ? intval($_SESSION["deviceID"]) : 0;
$lieferantID =getPostInt('lieferantID', 0 );

if ($deviceID <= 0 || $lieferantID <= 0 ) {
    die("Invalid device or Lieferant ID");
}

$sqlDelete = "DELETE FROM `LIMET_RB`.`tabelle_geraete_has_tabelle_lieferant`
              WHERE `tabelle_geraete_idTABELLE_Geraete` = ? AND `tabelle_lieferant_idTABELLE_Lieferant` = ?";

$stmt = $mysqli->prepare($sqlDelete);
$stmt->bind_param("ii", $deviceID, $lieferantID);

if ($stmt->execute()) {
    echo "Lieferant von Gerät entfernt!";
} else {
    echo "Error1: " . $stmt->error;
}

$stmt->close();
$mysqli->close();

?>
