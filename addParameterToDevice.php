<?php
include "_utils.php";
init_page_serversides();

$mysqli = utils_connect_sql();

$sql = "INSERT INTO `LIMET_RB`.`tabelle_geraete_has_tabelle_parameter`
                (`TABELLE_Geraete_idTABELLE_Geraete`,
                `TABELLE_Parameter_idTABELLE_Parameter`,
                `TABELLE_Planungsphasen_idTABELLE_Planungsphasen`)
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
