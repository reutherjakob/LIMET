<?php

require_once '../../utils/_utils.php';
check_login();

$standort = 1;
$projectID = intval($_SESSION['projectID']);
$username = $_SESSION['username'] ?? 'unknown';

$besprechungsid = getPostInt('besprechungsid');
$vermerkID = getPostInt('vermerkID');
$relationId = getPostInt('relationId');
$roomId = getPostInt('roomId');
$elementId = getPostInt('elementId');
$variantId = getPostInt('variantId');
$newAmount = getPostInt('newAmount');
$changeComment = getPostString('changeComment', '');
$status = getPostInt('status', 1);
$neuBestand = getPostInt('neuBestand', 1);
$bestand_alt = getPostInt('bestand_alt', 1);
$elementKommentar = getPostString('elementKommentar', '');

$conn = utils_connect_sql();
$oldData = [];
$bezeichnung = "";
try {
    $conn->begin_transaction();
    $isNewRelation = false;
    // Bestehende Relation holen (inkl. Variante prüfen)
    if ($relationId > 0) {
        $sqlOld = "SELECT rhe.*, e.Bezeichnung AS elementName, rhe.tabelle_varianten_idtabelle_varianten AS variant
                   FROM LIMET_RB.tabelle_räume_has_tabelle_elemente rhe
                   JOIN tabelle_elemente e ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
                   WHERE rhe.id = ? 
                     AND rhe.tabelle_varianten_idtabelle_varianten = ? 
                   AND  `Neu/Bestand` = ? ";
        $stmtOld = $conn->prepare($sqlOld);
        $stmtOld->bind_param('iii', $relationId, $variantId, $neuBestand);
        $stmtOld->execute();
        $oldData = $stmtOld->get_result()->fetch_assoc();
        $stmtOld->close();
        if (!$oldData) {
            $relationId = 0;
        }
    }

    if ($relationId > 0) { // Update
        $sqlUpdate = "UPDATE tabelle_räume_has_tabelle_elemente
                      SET Anzahl = ?, status = ?, `Neu/Bestand` = ?, Timestamp = NOW(), Kurzbeschreibung = ?
                      WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('iiisi', $newAmount, $status, $neuBestand, $elementKommentar, $relationId);
        if (!$stmtUpdate->execute()) {
            throw new Exception('Update Fehler: ' . $stmtUpdate->error);
        }
        $stmtUpdate->close();

    } else { // Insert prüfen & anlegen

        // Check if room and element exist for project
        $sqlCheck = "SELECT r.idTABELLE_Räume, e.idTABELLE_Elemente, e.Bezeichnung
                 FROM tabelle_räume r, tabelle_elemente e
                 WHERE r.idTABELLE_Räume = ? AND e.idTABELLE_Elemente = ? AND r.tabelle_projekte_idTABELLE_Projekte = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param('iii', $roomId, $elementId, $projectID);
        $stmtCheck->execute();
        $checkRes = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();
        if (!$checkRes) {
            throw new Exception('Raum oder Element nicht gefunden');
        }
        $bezeichnung = $checkRes['Bezeichnung'];

        // New: Check if relation for this room, element, and variant already exists
        $sqlExistingRelation = "SELECT id, Anzahl FROM tabelle_räume_has_tabelle_elemente
                                WHERE TABELLE_Räume_idTABELLE_Räume = ? 
                                AND TABELLE_Elemente_idTABELLE_Elemente = ? 
                                AND tabelle_varianten_idtabelle_varianten = ?
                                AND `Neu/Bestand` = ? 
                                AND Standort =? ";
        $stmtExisting = $conn->prepare($sqlExistingRelation);
        $stmtExisting->bind_param('iiiii', $roomId, $elementId, $variantId, $neuBestand, $standort);
        $stmtExisting->execute();
        $existingRelation = $stmtExisting->get_result()->fetch_assoc();
        $stmtExisting->close();

        if ($existingRelation) {
            $newCount = $existingRelation['Anzahl'] + $newAmount;
            $sqlUpdateExisting = "UPDATE tabelle_räume_has_tabelle_elemente
                              SET Anzahl = ?, status = ?, `Neu/Bestand` = ?, Timestamp = NOW(), Kurzbeschreibung = ?
                              WHERE id = ?";
            $stmtUpdateExisting = $conn->prepare($sqlUpdateExisting);
            $stmtUpdateExisting->bind_param('iiisi', $newCount, $status, $neuBestand, $changeComment, $existingRelation['id']);
            if (!$stmtUpdateExisting->execute()) {
                throw new Exception('Update bestehende Relation Fehler: ' . $stmtUpdateExisting->error);
            }
            $stmtUpdateExisting->close();
            $relationId = $existingRelation['id'];
            $isNewRelation = false;
        } else {
            // Relation does not exist - insert new
            $sqlInsert = "INSERT INTO tabelle_räume_has_tabelle_elemente
                      (TABELLE_Räume_idTABELLE_Räume, TABELLE_Elemente_idTABELLE_Elemente, tabelle_varianten_idtabelle_varianten,
                       Anzahl, status, Standort, `Neu/Bestand`, Verwendung, Kurzbeschreibung, Timestamp)
                      VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, NOW())";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param('iiiisiis', $roomId, $elementId, $variantId, $newAmount, $status, $standort, $neuBestand, $changeComment);
            if (!$stmtInsert->execute()) {
                throw new Exception('Insert Fehler: ' . $stmtInsert->error);
            }
            $relationId = $stmtInsert->insert_id;
            $stmtInsert->close();
            $isNewRelation = true;
        }
    }
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => $isNewRelation ? 'Neue Relation erstellt' : 'Änderung gespeichert',
        'relationId' => $relationId,
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$neuData = [
    'Anzahl' => $newAmount,
    'status' => $status,
    'Neu/Bestand' => $neuBestand,
    'variant' => $variantId,
    'elementKommentar' => $elementKommentar,
];

// --- PROTOKOLL ---
require_once 'ProtocolHelper.php';
$protokollText = ProtocolHelper::generateProtocolText(
    $oldData ?? [],
    $neuData,
    $changeComment,
    $oldData['elementName'] ?? $bezeichnung,
);
try {
    ProtocolHelper::updateRemarkAndLink($conn, $vermerkID, $relationId, $protokollText);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}


$conn->close();

