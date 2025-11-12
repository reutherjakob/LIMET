<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$vermerkID = getPostInt('vermerkID');
$ansprechpersonenID = getPostInt('ansprechpersonenID');

$stmt = $mysqli->prepare("INSERT INTO `LIMET_RB`.`tabelle_Vermerke_has_tabelle_ansprechpersonen`
                (`tabelle_Vermerke_idtabelle_Vermerke`, `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`)
                VALUES (?, ?)");

if ($stmt === false) {
    echo "Prepare failed: " . $mysqli->error;
    exit;
}

$stmt->bind_param('ii', $vermerkID, $ansprechpersonenID);
if ($stmt->execute()) {
    echo "Zustaendigkeit hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
