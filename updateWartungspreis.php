<?php
require_once 'utils/_utils.php';
check_login();

$wartungID = getPostInt('wartungID');
if ($wartungID <= 0) {
    echo "Ungültige Wartungs-ID!";
    exit;
}

$geraeteID  = getPostInt('geraeteID');
if ($geraeteID <= 0) {
    echo "Ungültige Geräte-ID!";
    exit;
}

$dateStr    = getPostDate('date');
$wartungsart = getPostString('wartungsart');
$menge      = getPostInt('menge');
$preis      = getPostString('preis');
$info       = getPostString('info');
$lieferant  = getPostInt('lieferant');

$date = date("Y-m-d", strtotime($dateStr));

$lieferantParam = $lieferant === 0 ? null : $lieferant;

$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("
    UPDATE tabelle_wartungspreise
    SET
        Datum                                 = ?,
        Wartungsart                           = ?,
        Menge                                 = ?,
        WartungspreisProJahr                  = ?,
        Info                                  = ?,
        tabelle_lieferant_idTABELLE_Lieferant = ?
    WHERE idtabelle_wartungspreise = ?
      AND tabelle_geraete_idTABELLE_Geraete  = ?
");

$stmt->bind_param(
    'ssidsiii',
    $date,
    $wartungsart,
    $menge,
    $preis,
    $info,
    $lieferantParam,
    $wartungID,
    $geraeteID
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Wartungspreis erfolgreich geändert!";
    } else {
        echo "Wartungspreis nicht gefunden oder keine Änderungen!";
    }
} else {
    echo "Fehler: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>