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
    $sql = "SELECT 
                rhe.id as relationId,
                rhe.Anzahl,
                rhe.status,
                rhe.Kurzbeschreibung as element_comments,
                rhe.Standort as standort,
                rhe.`Neu/Bestand` as neuBestand,
                r.Raumnr,
                r.Raumbezeichnung,
                r.`Anmerkung allgemein` as room_comments,
                e.ElementID,
                e.Bezeichnung,
                v.Variante
            FROM tabelle_räume_has_tabelle_elemente rhe
            JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
            JOIN tabelle_elemente e ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
            LEFT JOIN tabelle_varianten v ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
            WHERE rhe.TABELLE_Räume_idTABELLE_Räume = ? 
              AND rhe.TABELLE_Elemente_idTABELLE_Elemente = ?
              AND rhe.tabelle_Varianten_idtabelle_Varianten = ?
              AND r.tabelle_projekte_idTABELLE_Projekte = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $roomId, $elementId, $variantId, $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $row['roomId'] = $roomId;
        $row['elementId'] = $elementId;
        $row['variantId'] = $variantId;

        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Element-Relation nicht gefunden']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();

