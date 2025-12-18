<?php
// 25 FX

require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();



$raumnummer = getPostString('raumnummer');
$raumbezeichnung = getPostString('raumbezeichnung');
$funktionsteilstelle = getPostInt('funktionsteilstelle');
$projectID = (int)$_SESSION["projectID"] ?? 0;
$raumbereich = getPostString('raumbereich');
$geschoss = getPostString('geschoss');
$bauetappe = getPostString('bauetappe');
$bauteil = getPostString('bauteil');
$nutzflaeche = getPostString('nutzflaeche');
$MTrelevant = getPostString('MTrelevant');

$stmt = $mysqli->prepare("INSERT INTO `LIMET_RB`.`tabelle_räume` 
    (`Raumnr`, `Raumbezeichnung`, `TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen`, 
    `tabelle_projekte_idTABELLE_Projekte`, `Raumbereich Nutzer`, `Geschoss`, `Bauetappe`, 
    `Bauabschnitt`, `Nutzfläche`, `MT-relevant`) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo "Prepare failed: " . $mysqli->error;
    exit;
}

$stmt->bind_param(
    'ssisssssss',
    $raumnummer,
    $raumbezeichnung,
    $funktionsteilstelle,
    $projectID,
    $raumbereich,
    $geschoss,
    $bauetappe,
    $bauteil,
    $nutzflaeche,
    $MTrelevant
);

if ($stmt->execute()) {
    echo "Raum erfolgreich hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();

  