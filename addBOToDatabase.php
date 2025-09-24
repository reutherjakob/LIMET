<?php
require_once 'utils/_utils.php';
check_login();


$mysqli = utils_connect_sql();


$sql = "INSERT INTO `LIMET_RB`.`tabelle_BO_Taetigkeiten`
			(`Taetigkeit_Deutsch`,
			`tabelle_BO_Untergruppe_idtabelle_BO_Untergruppe`)
			VALUES
			('" . $_GET["boTaetigkeit"] . "',
			" . $_GET["boGruppe"] . ");";


if ($mysqli->query($sql) === TRUE) {
    echo "Tätigkeit hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
