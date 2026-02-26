<?php
require_once 'utils/_utils.php';
check_login();

$priceID = getPostInt('priceID');
if ($priceID <= 0) {
    echo "Ungültige Preis-ID!";
    exit;
}
$mysqli = utils_connect_sql();
$preis = getPostString('ep');
$menge = getPostString('menge');
$quelle = getPostString('quelle');
$dateStr = getPostString('date');
$projektID = getPostInt('project');
$nk = getPostString('nk');
$lieferant = getPostInt('lieferant');
$deviceID = $_SESSION["deviceID"];
$date = date("Y-m-d", strtotime($dateStr));

// Update statement
$stmt = $mysqli->prepare("UPDATE `LIMET_RB`.`tabelle_preise` 
    SET `Preis`=?, `Menge`=?, `Quelle`=?, `Datum`=?, `Nebenkosten`=?, 
        `TABELLE_Projekte_idTABELLE_Projekte`=?, `tabelle_lieferant_idTABELLE_Lieferant`=?
    WHERE idTABELLE_Preise=? AND `TABELLE_Geraete_idTABELLE_Geraete`=?");

$projectParam = $projektID === 0 ? null : $projektID;

$stmt->bind_param(
    'sssssiiii',
    $preis, $menge, $quelle, $date, $nk,
    $projectParam, $lieferant, $priceID, $deviceID
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Preis erfolgreich geändert!";
    } else {
        echo "Preis nicht gefunden oder keine Änderungen!";
    }
} else {
    echo "Fehler: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
