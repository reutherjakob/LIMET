<?php
include_once '../../utils/_utils.php';
include_once '../models/PivotTable.php';

check_login();
header('Content-Type: text/html; charset=utf-8');

$projectID = $_SESSION['projectID'];
$action = getPostString('action');
$conn = utils_connect_sql();

if ($action === "loadTable") {
    $raumbereiche = $_POST['raumbereich'] ?? [];
    if (!is_array($raumbereiche)) $raumbereiche = [$raumbereiche];

    $zusatzRaeume = $_POST['zusatzRaeume'] ?? [];
    if (!is_array($zusatzRaeume)) $zusatzRaeume = [$zusatzRaeume];

    $zusatzElemente = $_POST['zusatzElemente'] ?? [];
    if (!is_array($zusatzElemente)) $zusatzElemente = [$zusatzElemente];

    $mtRelevant = !empty($_POST['mtRelevant']);
    $entfallen = !empty($_POST['entfallen']);
    $nurMitElementen = !empty($_POST['nurMitElementen']);
    $ohneLeereElemente = !isset($_POST['ohneLeereElemente']) || (bool)$_POST['ohneLeereElemente'];
    $transponiert = !empty($_POST['transponiert']);

    try {
        $pivotModel = new PivotTable($conn, $projectID);
        $html = $pivotModel->getElementeJeRaeume(
            $raumbereiche,
            $zusatzRaeume,
            $zusatzElemente,
            $mtRelevant,
            $entfallen,
            $nurMitElementen,
            $ohneLeereElemente,
            $transponiert
        );
        echo $html;

    } catch (Exception $e) {
        http_response_code(500);
        echo "<div class='alert alert-danger'>Fehler: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    $conn->close();
    exit;
}

if ($_POST['action'] === 'resetElementStati') {
    $relationIds = json_decode($_POST['relationIds'], true);  // Decode back to array
    if (!is_array($relationIds)) {
        echo json_encode(['success' => false, 'message' => 'Invalid relationIds']);
        exit;
    }

    $placeholders = str_repeat('?,', count($relationIds) - 1) . '?';
    $sql = "UPDATE tabelle_räume_has_tabelle_elemente SET status = 0 WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($relationIds)), ...$relationIds);

    $stmt->execute();
    $affectedRows = $stmt->affected_rows;

    echo json_encode([
        'success' => true,
        'affectedRows' => $affectedRows,
        'processed' => count($relationIds),
        'message' => "$affectedRows von " . count($relationIds) . " Records auf Status 0 gesetzt"
    ]);
    $stmt->close();
}



if ($action == "getElementDetails") {
    $roomId = getPostInt('roomId');
    $elementId = getPostInt('elementId');
    $variantId = getPostInt('variantId');
    $bestand = getPostInt('bestand');
    $relationId = getPostInt('relationId');

    if (!$roomId || !$elementId) {
        echo json_encode(['success' => false, 'message' => 'Fehlende Parameter']);
        exit;
    }

    $conn = utils_connect_sql();

    try {

        // 0. If a specific relation is requested, fetch it directly
        if (($relationId) !== 0) {
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
            WHERE rhe.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $relationId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $row['roomId'] = $row['Raumnr']; // or keep $roomId if you want to use input
                $row['elementId'] = $elementId;
                $row['variantId'] = $variantId;
                echo json_encode(['success' => true, 'data' => $row]);
                $stmt->close();
                $conn->close();
                exit;
            }
            $stmt->close();
        }

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
          AND rhe.Standort = 1 
          AND rhe.TABELLE_Elemente_idTABELLE_Elemente = ?
          AND rhe.`Neu/Bestand` = ?
          AND (
              (rhe.tabelle_Varianten_idtabelle_Varianten = ?) OR 
              (rhe.tabelle_Varianten_idtabelle_Varianten IS NULL AND ? = 0)
          )
          AND r.tabelle_projekte_idTABELLE_Projekte = ?
        ORDER BY (rhe.Anzahl > 0) DESC, rhe.Anzahl DESC
        LIMIT 1
    ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiii", $roomId, $elementId, $bestand, $variantId, $variantId, $projectID);
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
                'neuBestand' => 1,
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
}