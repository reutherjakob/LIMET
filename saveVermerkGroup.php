<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

$mysqli = utils_connect_sql();

$gruppenName      = getPostString('gruppenName');
$gruppenArt       = getPostString('gruppenart');
$gruppenOrt       = getPostString('gruppenOrt');
$gruppenVerfasser = getPostString('gruppenVerfasser');
$gruppenStart     = getPostString('gruppenStart');
$gruppenEnde      = getPostString('gruppenEnde');
$gruppenDatum     = getPostString('gruppenDatum');
$gruppenID        = getPostInt('gruppenID',0);

if ($gruppenID === 0) {
    die("Ungültige Gruppen-ID.");
}

$stmt = $mysqli->prepare("
        UPDATE tabelle_Vermerkgruppe
        SET Gruppenname = ?, 
            Gruppenart = ?, 
            Ort = ?, 
            Verfasser = ?, 
            Startzeit = ?, 
            Endzeit = ?, 
            Datum = ?
        WHERE idtabelle_Vermerkgruppe = ?
    ");

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("sssssssi",
    $gruppenName,
    $gruppenArt,
    $gruppenOrt,
    $gruppenVerfasser,
    $gruppenStart,
    $gruppenEnde,
    $gruppenDatum,
    $gruppenID
);

if ($stmt->execute()) {
    echo "Vermerkgruppe aktualisiert!";
} else {
    echo "Fehler beim Aktualisieren: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
