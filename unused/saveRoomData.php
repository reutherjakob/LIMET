<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$id                  = getPostInt('ID');
$raumnummer          = getPostString('raumnummer');
$raumbezeichnung     = getPostString('raumbezeichnung');
$raumbereich         = getPostString('raumbereich');
$geschoss            = getPostString('geschoss');
$bauetappe           = getPostString('bauetappe');
$bauabschnitt        = getPostString('bauteil');
$nutzflaeche         = getPostFloat('nutzflaeche');
$funktionsteilstelle = getPostInt('funktionsteilstelle');
$mtRelevant          = getPostString('MTrelevant');

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_räume`
    SET
        `Raumnr` = ?,
        `Raumbezeichnung` = ?,
        `Raumbereich Nutzer` = ?,
        `Geschoss` = ?,
        `Bauetappe` = ?,
        `Bauabschnitt` = ?,
        `Nutzfläche` = ?,
        `TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen` = ?,
        `MT-relevant` = ?
    WHERE `idTABELLE_Räume` = ?
");

$stmt->bind_param(
	"ssssssdi si",
	$raumnummer,
	$raumbezeichnung,
	$raumbereich,
	$geschoss,
	$bauetappe,
	$bauabschnitt,
	$nutzflaeche,
	$funktionsteilstelle,
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
