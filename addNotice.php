<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$roomID = getPostInt('roomID');
$notiz = getPostString('Notiz');
$kategorie = getPostString('Kategorie');
$username = $_SESSION['username'] ?? '';

if ($roomID && $notiz !== '' && $kategorie !== '' && $username !== '') {
	$datum = date('Y-m-d');

	$stmt = $mysqli->prepare("
        INSERT INTO tabelle_notizen (Notiz, Datum, User, Kategorie, tabelle_räume_idTABELLE_Räume) 
        VALUES (?, ?, ?, ?, ?)
    ");
	$stmt->bind_param("ssssi", $notiz, $datum, $username, $kategorie, $roomID);

	if ($stmt->execute()) {
		echo "Erfolgreich gespeichert!";
	} else {
		echo "Fehler: " . $stmt->error;
	}

	$stmt->close();
	$mysqli->close();
} else {
	echo "Fehlende Pflichtfelder!";
}
