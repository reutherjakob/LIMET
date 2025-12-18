<?php
// 25 FX
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

$bezeichnung = getPostString('bezeichnung');
$kurzbeschreibung = getPostString('kurzbeschreibung');
$elementID = $_SESSION["elementID"];

$sql = "UPDATE `LIMET_RB`.`tabelle_elemente` SET `Bezeichnung` = ?, `Kurzbeschreibung` = ? WHERE `idTABELLE_Elemente` = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssi", $bezeichnung, $kurzbeschreibung, $elementID);

if($stmt->execute()) {
	echo "Element gespeichert!";
} else {
	echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
