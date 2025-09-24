<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

if (filter_input(INPUT_GET, 'gereatID') != 0) {
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_bestandsdaten`
			(`Inventarnummer`,
			`Seriennummer`,
			`Anschaffungsjahr`,
			`tabelle_räume_has_tabelle_elemente_id`,
			`tabelle_geraete_idTABELLE_Geraete`,
                        `Aktueller Ort`)
			VALUES
			('" . filter_input(INPUT_GET, 'inventarNr') . "',
			'" . filter_input(INPUT_GET, 'serienNr') . "',
			'" . filter_input(INPUT_GET, 'anschaffungsJahr') . "',
			" . $_SESSION["roombookID"] . ",
			" . filter_input(INPUT_GET, 'gereatID') . ",
                        '" . filter_input(INPUT_GET, 'currentPlace') . "');";
} else {
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_bestandsdaten`
			(`Inventarnummer`,
			`Seriennummer`,
			`Anschaffungsjahr`,
			`tabelle_räume_has_tabelle_elemente_id`,
			`tabelle_geraete_idTABELLE_Geraete`,
                        `Aktueller Ort`)
			VALUES
			('" . filter_input(INPUT_GET, 'inventarNr') . "',
			'" . filter_input(INPUT_GET, 'serienNr') . "',
			'" . filter_input(INPUT_GET, 'anschaffungsJahr') . "',
			" . $_SESSION["roombookID"] . ",
			NULL,
                        '" . filter_input(INPUT_GET, 'currentPlace') . "');";
}


if ($mysqli->query($sql) === TRUE) {
    echo "Bestand hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}


$mysqli->close();

?>