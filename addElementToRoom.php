<?php
include "_utils.php";
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=1) AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
if ($row["Kosten"] === null) {
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_varianten_kosten`
				(`tabelle_projekte_idTABELLE_Projekte`,
				`tabelle_elemente_idTABELLE_Elemente`,
				`tabelle_Varianten_idtabelle_Varianten`,
				`Kosten`)
				VALUES
				(" . $_SESSION["projectID"] . ",
				" . $_SESSION["elementID"] . ",
				1,
				'0');";

    if ($mysqli->query($sql) === TRUE) {
        echo "Variante erfolgreich angelegt! \n";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}

$sql = "INSERT INTO `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
			(`TABELLE_Räume_idTABELLE_Räume`,
			`TABELLE_Elemente_idTABELLE_Elemente`,
			`Neu/Bestand`,
			`Anzahl`,
			`Standort`,
			`Verwendung`,
			`Timestamp`,
			`tabelle_Varianten_idtabelle_Varianten`)
			VALUES
			(" . $_SESSION["roomID"] . ",
			" . $_SESSION["elementID"] . ",
			'1',
			'1',
			'1',
			'1',
			'" . date("Y-m-d H:i:s") . "',
			1);";


if ($mysqli->query($sql) === TRUE) {
    echo "Element zu Raum hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
