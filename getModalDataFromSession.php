<?php
// 25 FX 
include "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

header('Content-Type: application/json');

if (!isset($_SESSION['elementID']) || !isset($_SESSION['deviceID'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Session variables not set']);
    exit();

}

$elementID = $_SESSION['elementID'];
$deviceID = $_SESSION['deviceID'];

try {
    // Element
    $stmt = $mysqli->prepare("SELECT Bezeichnung FROM tabelle_elemente WHERE idTABELLE_Elemente = ?");
    $stmt->bind_param("s", $elementID);
    $stmt->execute();
    $elementResult = $stmt->get_result()->fetch_assoc();
    $elementBezeichnung = $elementResult['Bezeichnung'] ?? 'Unbekanntes Element';

    // Device
    $stmt = $mysqli->prepare("SELECT Typ FROM tabelle_geraete WHERE idTABELLE_Geraete = ?");
    $stmt->bind_param("s", $deviceID);
    $stmt->execute();
    $deviceResult = $stmt->get_result()->fetch_assoc();
    $deviceTyp = $deviceResult['Typ'] ?? 'Unbekanntes Gerät';

    echo json_encode([
        'elementBezeichnung' => $elementBezeichnung,
        'deviceTyp' => $deviceTyp
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
exit();
