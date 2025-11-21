<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

// Safely get the integer 'bestandID' from GET parameters using _utils method
$bestandID = getPostInt("bestandID", 0 ); // Assuming this method exists analogously to getPostInt

$stmt = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_bestandsdaten` WHERE `idtabelle_bestandsdaten` = ?");
$stmt->bind_param("i", $bestandID);

if ($stmt->execute()) {
    echo json_encode(["message" => "Bestand geloescht!"]);
} else {
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
