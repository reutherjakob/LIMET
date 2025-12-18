<?php

require_once '../../utils/_utils.php';  // oder dein DB-Verbindungsfile
check_login();

if (!isset($_SESSION['projectID'])) {
    echo json_encode(['success' => false, 'message' => 'Keine Projekt-ID gefunden']);
    exit;
}

function loadVermerkuntergruppenIntoSession(int $projectID, int $vermerkgruppeId = 1): void
{
    if (!isset($_SESSION['vermerkuntergruppen']) || !is_array($_SESSION['vermerkuntergruppen'])) {
        $_SESSION['vermerkuntergruppen'] = [];
    }
    if (!isset($_SESSION['vermerkuntergruppen'][$vermerkgruppeId])) {
        $conn = utils_connect_sql();
        $sql = "SELECT idtabelle_Vermerkuntergruppe, Untergruppenname 
                FROM tabelle_Vermerkuntergruppe 
                WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $vermerkgruppeId);
        $stmt->execute();
        $result = $stmt->get_result();

        $untergruppen = [];
        while ($row = $result->fetch_assoc()) {
            $untergruppen[$row['Untergruppenname']] = $row['idtabelle_Vermerkuntergruppe'];
        }
        $_SESSION['vermerkuntergruppen'][$vermerkgruppeId] = $untergruppen;
        $stmt->close();
        $conn->close();
    }
}


$projectID = intval($_SESSION['projectID']);
$username = $_SESSION['username'] ?? 'unknown';
$standort = 1; // fest auf 1 gesetzt

$besprechungsid = isset($_POST['besprechungsid']) ? ($_POST['besprechungsid']) : " - "; // = vermerkgruppenID
loadVermerkuntergruppenIntoSession($projectID, $besprechungsid);


$relationId = isset($_POST['relationId']) ? intval($_POST['relationId']) : 0;
$roomId = isset($_POST['roomId']) ? intval($_POST['roomId']) : 0;
$elementId = isset($_POST['elementId']) ? intval($_POST['elementId']) : 0;
$variantId = isset($_POST['variantId']) ? intval($_POST['variantId']) : 0;
$newAmount = isset($_POST['newAmount']) ? intval($_POST['newAmount']) : 0;
$changeComment = isset($_POST['changeComment']) ? trim($_POST['changeComment']) : '';
$status = isset($_POST['status']) ? intval($_POST['status']) : 1;
$neuBestand = isset($_POST['neuBestand']) ? intval($_POST['neuBestand']) : 1;
$elementKommentar = isset($_POST['elementKommentar']) ? ($_POST['elementKommentar']) : " defaultKommentar ";
// file_put_contents(__DIR__ . '/updateElInRoom_debug.log', print_r($status, true)  );

if (!$roomId || !$elementId) {
    echo json_encode(['success' => false, 'message' => 'Fehlende Raum- oder Element-ID']);
    exit;
}
if ($newAmount < 0) {
    echo json_encode(['success' => false, 'message' => 'Anzahl kann nicht negativ sein']);
    exit;
}
if (empty($changeComment)) {
    echo json_encode(['success' => false, 'message' => 'Kommentar ist erforderlich']);
    exit;
}

$conn = utils_connect_sql();
$oldData = null;

try {
    $conn->begin_transaction();
    $isNewRelation = false;
    if ($relationId > 0) {
        $sqlOld = "SELECT rhe.*, r.Raumnr, r.Raumbezeichnung, e.ElementID, e.Bezeichnung
                   FROM LIMET_RB.tabelle_räume_has_tabelle_elemente rhe
                   JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
                   JOIN tabelle_elemente e ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
                   WHERE rhe.id = ? AND r.tabelle_projekte_idTABELLE_Projekte = ?";
        $stmtOld = $conn->prepare($sqlOld);
        $stmtOld->bind_param('ii', $relationId, $projectID);
        $stmtOld->execute();
        $oldData = $stmtOld->get_result()->fetch_assoc();
        $stmtOld->close();
        if (!$oldData) {// Relation existiert nicht, als Neu erfassen
            $relationId = 0;
        }
    }

    if ($relationId > 0) {// Update vorhandene Relation
        $sqlUpdate = "UPDATE tabelle_räume_has_tabelle_elemente
                      SET Anzahl = ?, status = ?, `Neu/Bestand` = ?, Timestamp = NOW(), Kurzbeschreibung = ?
                      WHERE id = ?";

        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('iiisi', $newAmount, $status, $neuBestand, $elementKommentar, $relationId);
        if (!$stmtUpdate->execute()) {
            throw new Exception('Update Fehler: ' . $stmtUpdate->error);
        }
        $stmtUpdate->close();
    } else {// Neue Relation anlegen Prüfen, ob Raum und Element zum Projekt gehören
        $sqlCheck = "SELECT r.idTABELLE_Räume, e.idTABELLE_Elemente
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
        'relationId' => $relationId
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
