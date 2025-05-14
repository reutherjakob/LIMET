<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

$mysqli = utils_connect_sql();

$sql = "INSERT INTO `LIMET_RB`.`tabelle_geraete_has_tabelle_parameter`
                (tabelle_geraete_has_tabelle_parameter.`TABELLE_Geraete_idTABELLE_Geraete`,
                 tabelle_geraete_has_tabelle_parameter.`TABELLE_Parameter_idTABELLE_Parameter`,
                 tabelle_geraete_has_tabelle_parameter.`TABELLE_Planungsphasen_idTABELLE_Planungsphasen`)
                VALUES
                (" . $_SESSION['deviceID'] . ",
                " . filter_input(INPUT_GET, 'parameterID') . ",
                1);";

if ($mysqli->query($sql) === TRUE) {
    echo "Parameter zu Gerät hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
