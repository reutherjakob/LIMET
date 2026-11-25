<?php
// 26 FX
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

$priceID   = getPostInt('priceID', 0);
$deviceID  = getPostInt('geraeteID', 0);   // statt $_SESSION["deviceID"]

if ($priceID <= 0 || $deviceID <= 0) {
    echo "Ungültige Parameter!";
    exit;
}

$mysqli = utils_connect_sql();

$preis          = getPostString('ep');
$menge          = getPostString('menge');
$quelle         = getPostString('quelle');
$dateStr        = getPostString('date');
$projektID      = getPostInt('project', 0);
$nk             = getPostString('nk');
$lieferant      = getPostInt('lieferant', 0);
$losID          = getPostInt('los', 0);
$preiskommentar = getPostString('preiskommentar');

$date = date("Y-m-d", strtotime($dateStr));

// 0 -> NULL
$projectParam   = $projektID > 0 ? $projektID : null;
$lieferantParam = $lieferant > 0 ? $lieferant : null;
$losParam       = $losID > 0 ? $losID : null;

$stmt = $mysqli->prepare("UPDATE `LIMET_RB`.`tabelle_preise`
    SET `Preis`=?,
        `Menge`=?,
        `Quelle`=?,
        `Datum`=?,
        `Nebenkosten`=?,
        `TABELLE_Projekte_idTABELLE_Projekte`=?,
        `tabelle_lieferant_idTABELLE_Lieferant`=?,
        `idtabelle_Lose_extern`=?,
        `Kommentar`=?
    WHERE `idTABELLE_Preise`=? AND `TABELLE_Geraete_idTABELLE_Geraete`=?");

if ($stmt === false) {
    echo "Prepare failed: " . $mysqli->error;
    $mysqli->close();
    exit;
}

$stmt->bind_param(
    'sssssiiisii',
    $preis, $menge, $quelle, $date, $nk,
    $projectParam, $lieferantParam, $losParam,
    $preiskommentar,
    $priceID, $deviceID
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