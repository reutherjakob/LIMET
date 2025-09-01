<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();


$gruppenName      = trim(filter_input(INPUT_GET, 'gruppenName', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenArt       = trim(filter_input(INPUT_GET, 'gruppenart', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenOrt       = trim(filter_input(INPUT_GET, 'gruppenOrt', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenVerfasser = trim(filter_input(INPUT_GET, 'gruppenVerfasser', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenStart     = trim(filter_input(INPUT_GET, 'gruppenStart', FILTER_UNSAFE_RAW));
$gruppenEnde      = trim(filter_input(INPUT_GET, 'gruppenEnde', FILTER_UNSAFE_RAW));
$gruppenDatum     = trim(filter_input(INPUT_GET, 'gruppenDatum', FILTER_UNSAFE_RAW));
$gruppenID        = filter_input(INPUT_GET, 'gruppenID', FILTER_VALIDATE_INT);

if ($gruppenID === false || $gruppenID === null) {
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
