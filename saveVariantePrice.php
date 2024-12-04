<?php
include "_utils.php";
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=" . $_SESSION["variantenID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

//wenn noch keine Kosten vorhanden, muss Insert erfolgen, sonst update
if (null === ($row["Kosten"])) {
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_varianten_kosten`
				(`tabelle_projekte_idTABELLE_Projekte`,
				`tabelle_elemente_idTABELLE_Elemente`,
				`tabelle_Varianten_idtabelle_Varianten`,
				`Kosten`)
				VALUES
				(" . $_SESSION["projectID"] . ",
				" . $_SESSION["elementID"] . ",
				" . $_SESSION["variantenID"] . ",
				'" . $_GET["kosten"] . "');";

    if ($mysqli->query($sql) === TRUE) {
        echo "Variante erfolgreich angelegt!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
} else {
    $sql = "UPDATE `LIMET_RB`.`tabelle_projekt_varianten_kosten`
				SET
				`Kosten` = '" . $_GET["kosten"] . "'
				WHERE `tabelle_projekte_idTABELLE_Projekte` =" . $_SESSION["projectID"] . " 
				AND `tabelle_elemente_idTABELLE_Elemente` = " . $_SESSION["elementID"] . " 
				AND `tabelle_Varianten_idtabelle_Varianten` = " . $_SESSION["variantenID"] . ";";

    if ($mysqli->query($sql) === TRUE) {
        echo "Variante erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }

}
$mysqli->close();

?>