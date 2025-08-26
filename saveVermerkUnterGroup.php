<?php

require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// GET-Parameter sicher abholen
$untergruppenName = filter_input(INPUT_GET, 'untergruppenName', FILTER_SANITIZE_STRING);
$untergruppenNummer = filter_input(INPUT_GET, 'untergruppenNummer', FILTER_SANITIZE_STRING);
$untergruppenID = filter_input(INPUT_GET, 'untergruppenID', FILTER_VALIDATE_INT);

// Parameter validieren
if ($untergruppenID === false || $untergruppenID === null) {
    die("Ungültige ID!");
}

// Prepared Statement nutzen
$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_Vermerkuntergruppe`
    SET 
        `Untergruppenname` = ?,
        `Untergruppennummer` = ?
    WHERE `idtabelle_Vermerkuntergruppe` = ?
");

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

// Parameter binden
$stmt->bind_param("ssi", $untergruppenName, $untergruppenNummer, $untergruppenID);

// Statement ausführen
if ($stmt->execute()) {
    echo "Vermerkuntergruppe aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
