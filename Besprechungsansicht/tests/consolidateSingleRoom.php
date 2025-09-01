<?php
// Datei z.B. consolidate_single_room.php

include "../../utils/_utils.php";
session_start();

$roomId = 50610; // Fixer Raum

$conn = utils_connect_sql();
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'DB-Verbindung fehlgeschlagen']));
}

try {
    // Funktion zur Konsolidierung eines einzelnen Raums
    function consolidateRoomElements(mysqli $conn, int $roomId): void {
        $sql = "SELECT * FROM tabelle_räume_has_tabelle_elemente WHERE TABELLE_Räume_idTABELLE_Räume = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $roomId);
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
                    'total' => 0,
                    'comments' => [],
                    'ids' => []
                ];
            }
            $groups[$key]['total'] += $row['Anzahl'];
            if (!empty($row['Kurzbeschreibung'])) {
                $groups[$key]['comments'][] = trim($row['Kurzbeschreibung']);
            }
            $groups[$key]['ids'][] = $row['id'];
        }
        $stmt->close();

        $updateStmt = $conn->prepare("UPDATE tabelle_räume_has_tabelle_elemente SET Anzahl = ?, Kurzbeschreibung = ? WHERE id = ?");

        foreach ($groups as $group) {
            $joinedComments = implode("\n", array_unique($group['comments']));
            $firstId = array_shift($group['ids']);

            // Update first row with total Anzahl and combined comments
            $updateStmt->bind_param("isi", $group['total'], $joinedComments, $firstId);
            $updateStmt->execute();

            // Set Anzahl=0 and empty comment on other duplicates
            foreach ($group['ids'] as $id) {
                $zero = 0;
                $empty = "";
                $updateStmt->bind_param("isi", $zero, $empty, $id);
                $updateStmt->execute();
            }
        }
        $updateStmt->close();
    }

    consolidateRoomElements($conn, $roomId);

    echo json_encode(['success' => true, 'message' => "Konsolidierung für Raum $roomId durchgeführt"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
