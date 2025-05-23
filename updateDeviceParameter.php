<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

$mysqli = utils_connect_sql();
$sql = "UPDATE `LIMET_RB`.`tabelle_geraete_has_tabelle_parameter`
                    SET
                    `Wert` = '" . $_GET["wert"] . "',
                    `Einheit` = '" . $_GET["einheit"] . "'
                    WHERE `TABELLE_Geraete_idTABELLE_Geraete` = " . $_SESSION["deviceID"] . " AND `TABELLE_Parameter_idTABELLE_Parameter` = " . $_GET["parameterID"] . ";";

if ($mysqli->query($sql) === TRUE) {
    echo "Parameter erfolgreich aktualisiert!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
