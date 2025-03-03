<?php
include "_utils.php";
check_login();

$mysqli = utils_connect_sql();

$timestamp = strtotime($_GET["date"] ?? '' );

$projectID = filter_input(INPUT_GET, 'project');
if ($projectID == 0) {
    $projectID = "NULL";
}

$sql = "INSERT INTO `LIMET_RB`.`tabelle_preise`
			(`Preis`,
			`Menge`,
			`Quelle`,
			`Datum`,
			`TABELLE_Geraete_idTABELLE_Geraete`,
			`Nebenkosten`,
                        `TABELLE_Projekte_idTABELLE_Projekte`,
                        `tabelle_lieferant_idTABELLE_Lieferant`)
			VALUES
			('" . filter_input(INPUT_GET, 'ep') . "',
			'" . filter_input(INPUT_GET, 'menge') . "',
			'" . filter_input(INPUT_GET, 'quelle') . "',
			'" . date("Y-m-d", strtotime(filter_input(INPUT_GET, 'date')) ?? '' ) . "',
			" . $_SESSION["deviceID"] . ",
			'" . filter_input(INPUT_GET, 'nk') . "',
                        " . $projectID . ",
                        " . filter_input(INPUT_GET, 'lieferant') . ");";

if ($mysqli->query($sql) === TRUE) {
    echo "Preis zu Ger�t hinzugef�gt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
