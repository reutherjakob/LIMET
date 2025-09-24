<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();


$losID = getPostInt('los');
$vermerkText = getPostString('vermerkText');
$vermerkStatus = getPostInt('vermerkStatus');
$vermerkTyp = getPostString('vermerkTyp');
$untergruppenID = getPostInt('untergruppenID');
$vermerkID = getPostInt('vermerkID');
$faelligkeitDatum = getPostString('faelligkeitDatum', '');
if (empty($faelligkeitDatum) || $faelligkeitDatum === 'null' || $faelligkeitDatum === '0000-00-00') {
    $faelligkeitDatum = NULL;
}


if ($losID === 0) {
    $losID = NULL;
}

//$logFile = __DIR__ . '/log.log'; // Definiere logFile ganz oben!

//function writeLog($message)
//{
//    global $logFile;
//    file_put_contents($logFile, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
//}
//
//// Log input values and types
//writeLog("losID: $losID (type: " . gettype($losID) . ")");
//writeLog("vermerkText: $vermerkText (type: " . gettype($vermerkText) . ")");
//writeLog("vermerkStatus: $vermerkStatus (type: " . gettype($vermerkStatus) . ")");
//writeLog("vermerkTyp: $vermerkTyp (type: " . gettype($vermerkTyp) . ")");
//writeLog("faelligkeitDatum: " . var_export($faelligkeitDatum, true) . " (type: " . gettype($faelligkeitDatum) . ")");
//writeLog("untergruppenID: $untergruppenID (type: " . gettype($untergruppenID) . ")");
//writeLog("vermerkID: $vermerkID (type: " . gettype($vermerkID) . ")");
//
$sql = "UPDATE `LIMET_RB`.`tabelle_Vermerke`
        SET
        `tabelle_lose_extern_idtabelle_Lose_Extern` = ?,
        `Vermerktext` = ?,
        `Bearbeitungsstatus` = ?,
        `Vermerkart` = ?,
        `Faelligkeit` = ?,
        `tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe` = ?
        WHERE `idtabelle_Vermerke` = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    //writeLog("Prepare failed: " . $mysqli->error);
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("isissii",
    $losID,
    $vermerkText,
    $vermerkStatus,
    $vermerkTyp,
    $faelligkeitDatum,
    $untergruppenID,
    $vermerkID
);

if ($stmt->execute()) {
    // writeLog("Update executed successfully for vermerkID: $vermerkID");
    $mysqli->query("DELETE FROM tabelle_vermerke_has_tabelle_räume WHERE tabelle_vermerke_idTabelle_vermerke = $vermerkID");
    $roomArray = $_POST['room'] ?? [];
    foreach ($roomArray as $roomID) {
        if ($roomID != "0" && $roomID != "") {
            $sql_room = "INSERT INTO tabelle_vermerke_has_tabelle_räume
                         (tabelle_vermerke_idTabelle_vermerke, tabelle_räume_idTabelle_räume)
                         VALUES ($vermerkID, $roomID)";
            if (!$mysqli->query($sql_room)) {
                //  writeLog("Room insert failed: " . $mysqli->error);
            }
        }
    }
    echo "Vermerk aktualisiert!";
} else {
    //writeLog("Execute failed: " . $stmt->error);
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
