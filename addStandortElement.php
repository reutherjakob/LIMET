<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$idStandortelement = getPostInt('standortElement', 0);
$idVerwendungselement = getPostInt('id', 0);

$sql = "INSERT INTO `LIMET_RB`.`tabelle_verwendungselemente`
        (`id_Standortelement`, `id_Verwendungselement`)
        VALUES
        ($idStandortelement, $idVerwendungselement);";

if ($mysqli->query($sql) === TRUE) {
	echo "Standortelement hinzugefügt!";
} else {
	echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli ->close();

?>
