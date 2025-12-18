<?php
// 25 FX 
require_once 'utils/_utils.php';
check_login();
$_SESSION["variantenID"] = getPostInt('variantenID');

$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = ? 
            AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente=?
            AND tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte=?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iii", $_SESSION["variantenID"], $_SESSION["elementID"], $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$kosten = (!isset($row["Kosten"]) || $row["Kosten"] === "") ? 0 : $row["Kosten"];
echo $kosten;

$mysqli->close();
