<?php
// V2.0: 2024-11-28, Reuther & Fux
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();

$mysqli =  utils_connect_sql();

$sql = "DELETE FROM `LIMET_RB`.`tabelle_geraete_has_tabelle_parameter`
                WHERE `TABELLE_Geraete_idTABELLE_Geraete`= " . $_SESSION['deviceID'] . "
		AND `TABELLE_Parameter_idTABELLE_Parameter` = " . filter_input(INPUT_GET, 'parameterID') . ";";

if ($mysqli->query($sql) === TRUE) {
    echo "Parameter von Gerät entfernt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();
?>
