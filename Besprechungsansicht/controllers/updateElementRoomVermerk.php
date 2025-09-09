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
$elementKommentar = getPostString('elementKommentar', '');
//$raumkommentar = getPostString('raumkommentar', '');

$conn = utils_connect_sql();
$oldData = [];

// --- ÄNDERUNG SPEICHERN ---
// if (trim($raumkommentar) !== '') {
//     // Fetch existing room comment before update (or reuse if already fetched)
//     $sqlRoomComment = "SELECT `Anmerkung allgemein` FROM tabelle_räume WHERE idTABELLE_Räume = ?";
//     $stmtRoomComment = $conn->prepare($sqlRoomComment);
//     $stmtRoomComment->bind_param('i', $roomId);
//     $stmtRoomComment->execute();
//     $existingCommentRow = $stmtRoomComment->get_result()->fetch_assoc();
//     $stmtRoomComment->close();
// 
//     $existingComment = $existingCommentRow['Anmerkung allgemein'] ?? '';
// 
//     // Append new comment with line break if the existing comment is not empty
//     $newRoomComment = $existingComment;
//     if (strlen(trim($existingComment)) > 0) {
//         $newRoomComment .= "\n";
//     }
//     $newRoomComment .= $raumkommentar;
//     // Update room comment in database
//     $sqlUpdateRoom = "UPDATE tabelle_räume SET `Anmerkung allgemein` = ? WHERE idTABELLE_Räume = ?";
//     $stmtUpdateRoom = $conn->prepare($sqlUpdateRoom);
//     $stmtUpdateRoom->bind_param('si', $newRoomComment, $roomId);
//     if (!$stmtUpdateRoom->execute()) {
//         // Log or handle failure to update room comment, but do not fail the main transaction since it is committed already
//         error_log('Failed to update room comment: ' . $stmtUpdateRoom->error);
//     }
//     $stmtUpdateRoom->close();
// }

$bezeichnung = "";
try {
    $conn->begin_transaction();
    $isNewRelation = false;
    // Bestehende Relation holen (inkl. Variante prüfen)
    if ($relationId > 0) {
        $sqlOld = "SELECT rhe.*, e.Bezeichnung AS elementName, rhe.tabelle_varianten_idtabelle_varianten AS variant
                   FROM LIMET_RB.tabelle_räume_has_tabelle_elemente rhe
                   JOIN tabelle_elemente e ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
                   WHERE rhe.id = ? AND rhe.tabelle_varianten_idtabelle_varianten = ?";
        $stmtOld = $conn->prepare($sqlOld);
        $stmtOld->bind_param('ii', $relationId, $variantId);
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

