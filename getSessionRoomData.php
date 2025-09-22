<?php
require_once 'utils/_utils.php';
check_login();
header('Content-Type: application/json');

$mysqli = utils_connect_sql();
$roomID =  (int) $_SESSION['roomID'];


// Prepare the statement with a placeholder
$stmt = $mysqli->prepare("SELECT Raumbezeichnung, Raumnr, `Raumbereich Nutzer`, Geschoss FROM tabelle_räume WHERE idTABELLE_Räume = ?");

$stmt->bind_param("i", $roomID);

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'Raumbezeichnung' => htmlspecialchars($row['Raumbezeichnung']),
        'Raumnr' => htmlspecialchars($row['Raumnr']),
        'RaumbereichNutzer' => htmlspecialchars($row['Raumbereich Nutzer']),
        'Geschoss' => htmlspecialchars($row['Geschoss'])
    ]);
} else {
    echo json_encode(['error' => 'Raum nicht gefunden']);
}
$mysqli->close();
