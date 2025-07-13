<?php
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
check_login();
$mysqli = utils_connect_sql();

$sql = "DELETE FROM `LIMET_RB`.`tabelle_bestandsdaten`
			WHERE `tabelle_bestandsdaten`.`idtabelle_bestandsdaten` = " . $_GET["bestandID"] . ";";

if ($mysqli->query($sql) === TRUE) {
    echo "Bestand geloescht!";
} else {
    echo $mysqli->error;
}

$mysqli->close();

?>
