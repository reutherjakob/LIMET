<?php
// Kopiert nur die Bauangaben-Anmerkungsfelder auf ausgewählte Räume
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$roomIDs = json_decode($_POST["rooms"] ?? '[]', true);
if (!is_array($roomIDs) || empty($roomIDs)) {
    die("Keine gültigen Raum-IDs");
}
$roomIDs = array_filter($roomIDs, fn($id) => filter_var($id, FILTER_VALIDATE_INT) !== false);
if (empty($roomIDs)) {
    die("Keine gültigen Raum-IDs");
}

$sessionRoomID = filter_var($_SESSION["roomID"] ?? null, FILTER_VALIDATE_INT);
if (!$sessionRoomID) {
    die("Ungültige Session-Raum-ID");
}

// Quelldaten lesen
$stmt = $mysqli->prepare("SELECT `Anmerkung FunktionBO`, `Anmerkung Geräte`, `Anmerkung BauStatik`, 
                                 `Anmerkung Elektro`, `Anmerkung MedGas`, `Anmerkung HKLS`
                          FROM tabelle_räume WHERE idTABELLE_Räume = ?");
$stmt->bind_param("i", $sessionRoomID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Quellraum nicht gefunden");
}

// Update auf Zielräume
$updStmt = $mysqli->prepare("UPDATE tabelle_räume SET 
    `Anmerkung FunktionBO` = ?, `Anmerkung Geräte` = ?, `Anmerkung BauStatik` = ?,
    `Anmerkung Elektro` = ?, `Anmerkung MedGas` = ?, `Anmerkung HKLS` = ?
    WHERE idTABELLE_Räume = ?");

$ausgabe = "";
foreach ($roomIDs as $targetID) {
    $v1 = $row['Anmerkung FunktionBO'];
    $v2 = $row['Anmerkung Geräte'];
    $v3 = $row['Anmerkung BauStatik'];
    $v4 = $row['Anmerkung Elektro'];
    $v5 = $row['Anmerkung MedGas'];
    $v6 = $row['Anmerkung HKLS'];
    $updStmt->bind_param("ssssssi", $v1, $v2, $v3, $v4, $v5, $v6, $targetID);
    if ($updStmt->execute()) {
        $ausgabe .= "Raum " . htmlspecialchars($targetID) . " erfolgreich aktualisiert!<br>";
    } else {
        $ausgabe .= "Fehler bei Raum " . htmlspecialchars($targetID) . ": " . htmlspecialchars($updStmt->error) . "<br>";
    }
}

$updStmt->close();
$mysqli->close();

echo $ausgabe;
?>