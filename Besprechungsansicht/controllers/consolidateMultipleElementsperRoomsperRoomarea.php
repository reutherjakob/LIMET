<?php
session_start();
require_once "../../utils/_utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$projectId = (int) ($_SESSION["projectID"] ?? 0);
$raumbereiche = $_POST['raumbereiche'] ?? [];

if ($projectId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige oder fehlende Projekt-ID']);
    exit;
}

if (!is_array($raumbereiche) || count($raumbereiche) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Keine Raumbereiche übermittelt']);
    exit;
}

$conn = utils_connect_sql();

try {
    // Räume der Raumbereiche laden
    $placeholders = implode(',', array_fill(0, count($raumbereiche), '?'));
    $sql = "SELECT idTABELLE_Räume AS id FROM tabelle_räume 
            WHERE tabelle_projekte_idTABELLE_Projekte = ? 
            AND `Raumbereich Nutzer` IN ($placeholders) 
            AND Entfallen = 0";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare Statement fehlgeschlagen: " . $conn->error);

    $types = 'i' . str_repeat('s', count($raumbereiche));
    $params = array_merge([$projectId], $raumbereiche);
    $refs = [];
    foreach ($params as $key => $val) {
        $refs[$key] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $refs));

    $stmt->execute();
    $result = $stmt->get_result();

    $raumIds = [];
    while ($row = $result->fetch_assoc()) {
        $raumIds[] = $row['id'];
    }
    $stmt->close();

    if (empty($raumIds)) {
        echo json_encode(['success' => true, 'message' => 'Keine Räume zu den angegebenen Bereichen gefunden.']);
        $conn->close();
        exit;
    }

    // Konsolidierungsfunktion für einen Raum
    function consolidateRoomElements(mysqli $conn, int $raumId): void {
        $sql = "SELECT * FROM tabelle_räume_has_tabelle_elemente WHERE TABELLE_Räume_idTABELLE_Räume = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Prepare (select) fehlgeschlagen: " . $conn->error);
        $stmt->bind_param('i', $raumId);
        $stmt->execute();
        $res = $stmt->get_result();

        $groups = [];
        while ($row = $res->fetch_assoc()) {
            $key = $row['TABELLE_Elemente_idTABELLE_Elemente'] . '|' .
                $row['tabelle_Varianten_idtabelle_Varianten'] . '|' .
                $row['Neu/Bestand'] . '|' .
                $row['Standort'] . '|' .
                $row['Verwendung'];

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'sum' => 0,
                    'comments' => [],
                    'ids' => []
                ];
            }
            $groups[$key]['sum'] += (int)$row['Anzahl'];
            if (!empty($row['Kurzbeschreibung'])) {
                $groups[$key]['comments'][] = trim($row['Kurzbeschreibung']);
            }
            $groups[$key]['ids'][] = $row['id']; // id-Tabelle_räume_has_tabelle_elemente
        }
        $stmt->close();

        $updateStmt = $conn->prepare("UPDATE tabelle_räume_has_tabelle_elemente SET Anzahl = ?, Kurzbeschreibung = ? WHERE id = ?");
        if (!$updateStmt) throw new Exception("Prepare (update) fehlgeschlagen: " . $conn->error);

        foreach ($groups as $group) {
            $combinedComments = implode("\n", array_unique($group['comments']));
            $firstId = array_shift($group['ids']);
            $updateStmt->bind_param('isi', $group['sum'], $combinedComments, $firstId);
            $updateStmt->execute();

            foreach ($group['ids'] as $id) {
                $zero = 0;
                $empty = '';
                $updateStmt->bind_param('isi', $zero, $empty, $id);
                $updateStmt->execute();
            }
        }
        $updateStmt->close();
    }

    // Alle Räume konsolidieren
    foreach ($raumIds as $rid) {
        consolidateRoomElements($conn, $rid);
    }

    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Konsolidierung der Elemente in allen Räumen der Bereiche abgeschlossen.']);

} catch (Exception $e) {
    if ($conn) $conn->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
}
