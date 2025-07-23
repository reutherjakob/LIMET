<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();


$gruppenFortsetzung = filter_input(INPUT_GET, 'gruppenFortsetzung', FILTER_VALIDATE_INT);

if ($gruppenFortsetzung !== 0) {
    echo "Gruppenfortsetzung gewählt!";
} else {

    $gruppenName = filter_input(INPUT_GET, 'gruppenName', FILTER_SANITIZE_STRING);
    $gruppenArt = filter_input(INPUT_GET, 'gruppenart', FILTER_SANITIZE_STRING);
    $gruppenOrt = filter_input(INPUT_GET, 'gruppenOrt', FILTER_SANITIZE_STRING);
    $gruppenVerfasser = filter_input(INPUT_GET, 'gruppenVerfasser', FILTER_SANITIZE_STRING);
    $gruppenStart = filter_input(INPUT_GET, 'gruppenStart', FILTER_SANITIZE_STRING); // could validate datetime format
    $gruppenEnde = filter_input(INPUT_GET, 'gruppenEnde', FILTER_SANITIZE_STRING);
    $gruppenDatum = filter_input(INPUT_GET, 'gruppenDatum', FILTER_SANITIZE_STRING);
    $gruppenID = filter_input(INPUT_GET, 'gruppenID', FILTER_VALIDATE_INT);

    if ($gruppenID === false || $gruppenID === null) {
        die("Ungültige Gruppen-ID.");
    }

    // Use prepared statement
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
}


$mysqli->close();
?>
