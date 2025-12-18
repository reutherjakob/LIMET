<?php
// 25 FX
require_once "utils/_utils.php";
check_login();


$mysqli = utils_connect_sql();
$projectID = getPostInt('project', 0);
if ($projectID == 0) {
    $projectID = "NULL";
}

$wartungspreis = $mysqli->real_escape_string(getPostString('wartungspreis'));
$menge = $mysqli->real_escape_string(getPostString('menge'));
$wartungsart = $mysqli->real_escape_string(getPostString('wartungsart'));
$info = $mysqli->real_escape_string(getPostString('info'));

$dateFormatted = getPostDate('date');

$lieferant = $mysqli->real_escape_string(getPostString('lieferant'));

$sql = "INSERT INTO `LIMET_RB`.`tabelle_wartungspreise`
           (
               `WartungspreisProJahr`,
               `Menge`,
               `Wartungsart`,
               `Info`,
               `Datum`,
               `tabelle_geraete_idTABELLE_Geraete`,
               `tabelle_lieferant_idTABELLE_Lieferant`,
               `tabelle_projekte_idTABELLE_Projekte`)
       VALUES
           (
               '$wartungspreis',
               '$menge',
               '$wartungsart',
               '$info',
               '$dateFormatted',
               " . intval($_SESSION["deviceID"]) . ",
               '$lieferant',
               " . ($projectID === "NULL" ? "NULL" : intval($projectID)) . "
           );";

if ($mysqli->query($sql) === TRUE) {
    echo "Wartungspreis zu Gerät hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
