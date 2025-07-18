<?php
require_once 'utils/_utils.php';

$_SESSION["variantenID"] = filter_input(INPUT_GET, 'variantenID') ;

$mysqli =utils_connect_sql();

$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=" .  filter_input(INPUT_GET, 'variantenID') . ") AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$kosten = (!isset($row["Kosten"]) || $row["Kosten"] === "") ? 0 : $row["Kosten"];
echo $kosten;



$mysqli->close();
