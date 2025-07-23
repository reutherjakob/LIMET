<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$sql = "INSERT INTO `LIMET_RB`.`tabelle_verwendungselemente`
                (`id_Standortelement`,
                `id_Verwendungselement`)
                VALUES
                (".filter_input(INPUT_GET, 'standortElement').",
                ".filter_input(INPUT_GET, 'id').");";

if ($mysqli ->query($sql) === TRUE) {
	echo "Standortelement hinzugefügt!";
} else {
	echo "Error: " . $sql . "<br>" . $mysqli->error;
}


$mysqli ->close();

?>
