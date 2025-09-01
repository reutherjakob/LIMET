<?php

session_start();
require_once '../../utils/_utils.php';
check_login();

if (!isset($_SESSION['projectID'])) {
    echo json_encode(['success' => false, 'message' => 'Keine Projekt-ID gefunden']);
    exit;
}
$projectID = intval($_SESSION['projectID']);
$roomId = intval($_POST['roomId'] ?? 0);
$elementId = intval($_POST['elementId'] ?? 0);
$variantId = intval($_POST['variantId'] ?? 0);

if (!$roomId || !$elementId) {
    echo json_encode(['success' => false, 'message' => 'Fehlende Parameter']);
    exit;
}

$conn = utils_connect_sql();

try {
    // 1. Try to find existing relation with all details
    $sql = "
        SELECT
            rhe.id AS relationId,
            rhe.Anzahl,
            rhe.status,
            rhe.Kurzbeschreibung AS element_comments,
            rhe.Standort AS standort,
            rhe.`Neu/Bestand` AS neuBestand,
            r.Raumnr,
            r.Raumbezeichnung,
            r.`Anmerkung allgemein` AS room_comments,
            e.ElementID,
            e.Bezeichnung,
            v.Variante
        FROM tabelle_räume_has_tabelle_elemente rhe
        JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
        JOIN tabelle_elemente e ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
        LEFT JOIN tabelle_varianten v ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
        WHERE rhe.TABELLE_Räume_idTABELLE_Räume = ?
          AND rhe.TABELLE_Elemente_idTABELLE_Elemente = ?
          AND (
              (rhe.tabelle_Varianten_idtabelle_Varianten = ?) OR (rhe.tabelle_Varianten_idtabelle_Varianten IS NULL AND ? = 0)
          )
          AND r.tabelle_projekte_idTABELLE_Projekte = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $roomId, $elementId, $variantId, $variantId, $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $row['roomId'] = $roomId;
        $row['elementId'] = $elementId;
        $row['variantId'] = $variantId;
        echo json_encode(['success' => true, 'data' => $row]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // 2. If not found, fetch element and room info separately, return default amounts
    $sqlElement = "SELECT ElementID, Bezeichnung FROM tabelle_elemente WHERE idTABELLE_Elemente = ?";
    $stmtElem = $conn->prepare($sqlElement);
    $stmtElem->bind_param("i", $elementId);
    $stmtElem->execute();
    $elemData = $stmtElem->get_result()->fetch_assoc() ?: ['ElementID' => '', 'Bezeichnung' => 'Unbekannt'];
    $stmtElem->close();

    $variantName = '-';
    if ($variantId == 0) {
        $variantId = 1;
    }
    $sqlVar = "SELECT Variante FROM tabelle_varianten WHERE idtabelle_Varianten = ?";
    $stmtVar = $conn->prepare($sqlVar);
    $stmtVar->bind_param("i", $variantId);
    $stmtVar->execute();
    $varData = $stmtVar->get_result()->fetch_assoc();
    if ($varData && !empty($varData['Variante'])) $variantName = $varData['Variante'];
    $stmtVar->close();


    $sqlRoom = "SELECT Raumnr, Raumbezeichnung, `Anmerkung allgemein` FROM tabelle_räume WHERE idTABELLE_Räume = ?";
    $stmtRoom = $conn->prepare($sqlRoom);
    $stmtRoom->bind_param("i", $roomId);
    $stmtRoom->execute();
    $roomData = $stmtRoom->get_result()->fetch_assoc() ?: ['Raumnr' => '', 'Raumbezeichnung' => 'Unbekannt', 'Anmerkung allgemein' => ''];
    $stmtRoom->close();

    echo json_encode([
        'success' => true,
        'data' => [
            'relationId' => 0,
            'roomId' => $roomId,
            'elementId' => $elementId,
            'variantId' => $variantId,
            'Anzahl' => 0,
            'status' => null,
            'element_comments' => '',
            'standort' => null,
            'neuBestand' => null,
            'room_comments' => $roomData['Anmerkung allgemein'],
            'Raumnr' => $roomData['Raumnr'],
            'Raumbezeichnung' => $roomData['Raumbezeichnung'],
            'ElementID' => $elemData['ElementID'],
            'Bezeichnung' => $elemData['Bezeichnung'],
            'Variante' => $variantName
        ]
    ]);
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    $conn->close();
    exit;
}
