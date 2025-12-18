<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$preis = getPostString('ep');
$menge = getPostString('menge');
$quelle = getPostString('quelle');
$dateStr = getPostString('date');
$projektID = getPostInt('project');
$nk = getPostString('nk');
$lieferant = getPostInt('lieferant');
$deviceID = $_SESSION["deviceID"];
$date =  date("Y-m-d",strtotime($dateStr));

// Prepare statement
$stmt = $mysqli->prepare("INSERT INTO `LIMET_RB`.`tabelle_preise`
          (`Preis`, `Menge`, `Quelle`, `Datum`, `TABELLE_Geraete_idTABELLE_Geraete`,
           `Nebenkosten`, `TABELLE_Projekte_idTABELLE_Projekte`, `tabelle_lieferant_idTABELLE_Lieferant`)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Check prepare
if ($stmt === false) {
    echo "Prepare failed: " . $mysqli->error;
    exit;
}

// Handle NULL for project ID
$projectParam = $projektID === 0 ? null : $projektID;

// Bind parameters
$stmt->bind_param(
    'sssssiii',
    $preis,
    $menge,
    $quelle,
    $date,
    $deviceID,
    $nk,
    $projectParam,
    $lieferant
);
if ($stmt->execute()) {
    echo "Preis zu Gerät hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
