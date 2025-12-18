<?php
// 25 FX
require_once 'utils/_utils.php'; // Assuming your utility functions are here
check_login();

$mysqli = utils_connect_sql();

$losExtern = getPostInt('losExtern', 0);
$standort = getPostString('standort');
$verwendung = getPostString('verwendung');
$bestand = getPostInt('bestand');
$amount = getPostInt('amount', 0);
$comment = getPostString('comment');
$roombookID = getPostInt('roombookID', 0);
$timestamp = date("Y-m-d H:i:s");

if ($losExtern === 0) {
	$sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
            SET `Standort` = ?, `Verwendung` = ?, `Neu/Bestand` = ?, `Anzahl` = ?, 
                `Kurzbeschreibung` = ?, `tabelle_Lose_Extern_idtabelle_Lose_Extern` = NULL, `Timestamp` = ?
            WHERE `id` = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ssiissi", $standort, $verwendung, $bestand, $amount, $comment, $timestamp, $roombookID);
} else {
	$sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
            SET `Standort` = ?, `Verwendung` = ?, `Neu/Bestand` = ?, `Anzahl` = ?, 
                `Kurzbeschreibung` = ?, `tabelle_Lose_Extern_idtabelle_Lose_Extern` = ?, `Timestamp` = ?
            WHERE `id` = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ssiisisi", $standort, $verwendung, $bestand, $amount, $comment, $losExtern, $timestamp, $roombookID);
}

if ($stmt->execute()) {
	echo "Erfolgreich aktualisiert!";
} else {
	echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>

