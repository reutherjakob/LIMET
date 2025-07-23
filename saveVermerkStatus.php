<?php
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

$vermerkID = filter_input(INPUT_GET, 'vermerkID', FILTER_VALIDATE_INT);
$vermerkStatus = filter_input(INPUT_GET, 'vermerkStatus', FILTER_UNSAFE_RAW);
$vermerkStatus = trim($vermerkStatus);

if (!$vermerkID || $vermerkStatus === null) {
	echo "Ungültige Eingaben.";
	$mysqli->close();
	exit;
}

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_Vermerke`
    SET `Bearbeitungsstatus` = ?
    WHERE `idtabelle_Vermerke` = ?
");

$stmt->bind_param("si", $vermerkStatus, $vermerkID);

if ($stmt->execute()) {
	echo "Vermerk aktualisiert!";
} else {
	echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
