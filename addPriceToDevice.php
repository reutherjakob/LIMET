<?php
// 26 FX
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

// Geräte-ID kommt jetzt per POST (wie beim Update), Session nur noch als Fallback
$deviceID = getPostInt('geraeteID', 0);
if ($deviceID <= 0) {
    $deviceID = (int)($_SESSION["deviceID"] ?? 0);
}
if ($deviceID <= 0) {
    echo "Ungültige Geräte-ID!";
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

$stmt = $mysqli->prepare(
    "INSERT INTO `LIMET_RB`.`tabelle_preise`
              (`Preis`,
               `Menge`,
               `Quelle`,
               `Datum`,
               `TABELLE_Geraete_idTABELLE_Geraete`,
               `Nebenkosten`,
               `TABELLE_Projekte_idTABELLE_Projekte`,
               `tabelle_lieferant_idTABELLE_Lieferant`,
               `idtabelle_Lose_extern`,
               `Kommentar`)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    echo "Prepare failed: " . $mysqli->error;
    $mysqli->close();
    exit;
}

$stmt->bind_param(
    'ssssisiiis',
    $preis,
    $menge,
    $quelle,
    $date,
    $deviceID,
    $nk,
    $projectParam,
    $lieferantParam,
    $losParam,
    $preiskommentar
);

if ($stmt->execute()) {
    echo "Preis zu Gerät hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();