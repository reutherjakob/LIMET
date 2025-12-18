<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$id               = getPostInt('ID');
$raumnummer       = getPostString('raumnummer');
$raumbezeichnung  = getPostString('raumbezeichnung');
$raumbereich      = getPostString('raumbereich');
$mtRelevant       = getPostString('MTrelevant');

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_räume`
    SET
        `Raumnr` = ?,
        `Raumbezeichnung` = ?,
        `Raumbereich Nutzer` = ?,
        `MT-relevant` = ?
    WHERE `idTABELLE_Räume` = ?
");

$stmt->bind_param(
	"ssssi",
	$raumnummer,
	$raumbezeichnung,
	$raumbereich,
	$mtRelevant,
	$id
);

if ($stmt->execute()) {
	echo "Raum erfolgreich aktualisiert!";
} else {
	echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
