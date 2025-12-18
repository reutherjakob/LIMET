<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$name        = getPostString('Name');
$vorname     = getPostString('Vorname');
$tel         = getPostString('Tel');
$adresse     = getPostString('Adresse');
$plz         = getPostString('PLZ');
$ort         = getPostString('Ort');
$land        = getPostString('Land');
$email       = getPostString('Email');
$raumnr      = getPostString('Raumnr');
$personId    = getPostInt('personID');
$zustaendigkeit = getPostInt('zustaendigkeit');
$organisation   = getPostInt('organisation');

if ($name !== '' && $vorname !== '' && $tel !== '') {

	$stmt1 = $mysqli->prepare("
        UPDATE `LIMET_RB`.`tabelle_ansprechpersonen`
        SET
            `Name` = ?,
            `Vorname` = ?,
            `Tel` = ?,
            `Adresse` = ?,
            `PLZ` = ?,
            `Ort` = ?,
            `Land` = ?,
            `Mail` = ?,
            `Raumnr` = ?
        WHERE `idTABELLE_Ansprechpersonen` = ?
    ");
	$stmt1->bind_param(
		"sssssssssi",
		$name,
		$vorname,
		$tel,
		$adresse,
		$plz,
		$ort,
		$land,
		$email,
		$raumnr,
		$personId
	);

	if ($stmt1->execute()) {
		echo "Personendaten gespeichert ";
	} else {
		echo "Error1: " . $stmt1->error;
	}
	$stmt1->close();

	$stmt2 = $mysqli->prepare("
        UPDATE `LIMET_RB`.`tabelle_projekte_has_tabelle_ansprechpersonen`
        SET
            `TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten` = ?,
            `tabelle_organisation_idtabelle_organisation` = ?
        WHERE
            `TABELLE_Projekte_idTABELLE_Projekte` = ?
            AND `TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen` = ?
    ");
	$projectId = $_SESSION['projectID'];
	$stmt2->bind_param(
		"iiii",
		$zustaendigkeit,
		$organisation,
		$projectId,
		$personId
	);

	if ($stmt2->execute()) {
		echo "und Organisation bzw. Zuständigkeit gespeichert!";
	} else {
		echo "Error2: " . $stmt2->error;
	}
	$stmt2->close();

	$mysqli->close();
} else {
	echo "Fehler bei der Verbindung";
}
