<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$roomIDs = getPostArrayInt('rooms');
if (empty($roomIDs)) {
    die("No valid room IDs found");
}

$validRoomIDs = [];
foreach ($roomIDs as $roomID) {
    $intRoomID = filter_var($roomID, FILTER_VALIDATE_INT);
    if ($intRoomID !== false) {
        $validRoomIDs[] = $intRoomID;
    }
}

if (empty($validRoomIDs)) {
    die("No valid room IDs found");
}

$sessionRoomID = filter_var($_SESSION["roomID"] ?? null, FILTER_VALIDATE_INT);
if ($sessionRoomID === false || $sessionRoomID === null) {
    die("Invalid session room ID");
}

$ausgabe = "";

$sql = "INSERT INTO tabelle_räume_has_tabelle_elemente
            (TABELLE_Räume_idTABELLE_Räume,
             TABELLE_Elemente_idTABELLE_Elemente,
             `Neu/Bestand`,
             Anzahl,
             Standort,
             Verwendung,
             Timestamp,
             tabelle_Varianten_idtabelle_Varianten)
        SELECT ?, TABELLE_Elemente_idTABELLE_Elemente, `Neu/Bestand`, Anzahl, Standort, Verwendung, ?, tabelle_Varianten_idtabelle_Varianten
        FROM LIMET_RB.tabelle_räume_has_tabelle_elemente
        WHERE TABELLE_Räume_idTABELLE_Räume = ?";

$stmt = $mysqli->prepare($sql);

$currentTimestamp = date("Y-m-d H:i:s");

foreach ($validRoomIDs as $valueOfRoomID) {
    $stmt->bind_param('isi', $valueOfRoomID, $currentTimestamp, $sessionRoomID);
    if ($stmt->execute()) {
        $ausgabe .= "Raum erfolgreich aktualisiert!\n";
    } else {
        $ausgabe .= "Error (Raum " . htmlspecialchars($valueOfRoomID) . "): " . htmlspecialchars($stmt->error) . "\n";
    }
}
$stmt->close();
$mysqli->close();

echo ($ausgabe);
?>
