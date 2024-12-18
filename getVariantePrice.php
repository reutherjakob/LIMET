<?php
include "_utils.php";

$_SESSION["variantenID"] = $_GET["variantenID"];

$mysqli =utils_connect_sql();

$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=" . $_GET["variantenID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

echo $row["Kosten"];

$mysqli->close();
