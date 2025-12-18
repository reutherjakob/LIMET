<?php
session_start();
include_once '../../utils/_utils.php';
check_login();


header('Content-Type: application/json; charset=utf-8');

$action = getPostString('action');
$gruppenName = getPostString('name');
$gruppenart = getPostString('art');
$gruppenOrt = getPostString('ort');
$gruppenVerfasser = getPostString('verfasser');
$gruppenStart = getPostString('startzeit');
$gruppenEnde = getPostString('endzeit');
$gruppenDatum = getPostString('datum');
$relevanteDokumente = getPostString('relevanteDokumente');
$projectID = (int)($_SESSION["projectID"] ?? 0);

$insertId = 0;


$mysqli = utils_connect_sql();


if ($action === "new") {
    if (empty($gruppenName) || empty($gruppenart) || empty($gruppenVerfasser) || empty($gruppenStart) || empty($gruppenDatum) || $projectID <= 0) {
        http_response_code(400);

        echo json_encode(['success' => false, 'message' => 'Fehlende Pflichtfelder oder ungültiges Projekt']);
        exit;
    }
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerkgruppe` 
    (`Gruppenname`, `Gruppenart`, `Ort`, `Verfasser`, `Startzeit`, `Endzeit`, `Datum`, `tabelle_projekte_idTABELLE_Projekte`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Datenbank Fehler: ' . $mysqli->error]);
        exit;
    }
    $stmt->bind_param(
        "sssssssi",
        $gruppenName,
        $gruppenart,
        $gruppenOrt,
        $gruppenVerfasser,
        $gruppenStart,
        $gruppenEnde,
        $gruppenDatum,
        $projectID
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Einfügen fehlgeschlagen: ' . $stmt->error]);
        $stmt->close();
        $mysqli->close();
        exit;
    }

    $insertId = $mysqli->insert_id;

// Immer Untergruppe 0, Allgemein anlegen
    $sqlUG = "INSERT INTO LIMET_RB.tabelle_Vermerkuntergruppe 
    (Untergruppennummer, Untergruppenname, tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) 
    VALUES (?, ?, ?)";
    $stmtUG = $mysqli->prepare($sqlUG);
    if (!$stmtUG) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Vermerkuntergruppe Prepare failed: ' . $mysqli->error]);
        exit;
    }
    $untergruppennummer = 0;
    $untergruppenname = 'Allgemein';
    $stmtUG->bind_param('isi', $untergruppennummer, $untergruppenname, $insertId);
    if (!$stmtUG->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Vermerkuntergruppe Insert failed: ' . $stmtUG->error]);
        $stmtUG->close();
        exit;
    }
    $untergruppenId = $mysqli->insert_id;
    $stmtUG->close();

// Vermerk 0, Allgemeines immer anlegen
    $sqlV0 = "INSERT INTO LIMET_RB.tabelle_Vermerke 
    (tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe, Ersteller, Erstellungszeit, Vermerktext, Bearbeitungsstatus, Vermerkart) 
    VALUES (?, ?, NOW(), ?, 0, ?)";
    $stmtV0 = $mysqli->prepare($sqlV0);
    if (!$stmtV0) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Vermerk Prepare 0 failed: ' . $mysqli->error]);
        exit;
    }
    $vermerkText0 = "Allgemeine Hinweise";
    $vermerkart0 = 'Allgemein';
    $stmtV0->bind_param('isss', $untergruppenId, $gruppenVerfasser, $vermerkText0, $vermerkart0);
    if (!$stmtV0->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Vermerk Insert 0 failed: ' . $stmtV0->error]);
        $stmtV0->close();
        exit;
    }
    $stmtV0->close();

// Wenn relevante Dokumente gesetzt sind, Vermerk 1 mit deren Text anlegen
    if ($relevanteDokumente != "") {
        $sqlV1 = "INSERT INTO LIMET_RB.tabelle_Vermerke 
        (tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe, Ersteller, Erstellungszeit, Vermerktext, Bearbeitungsstatus, Vermerkart) 
        VALUES (?, ?, NOW(), ?, 0, ?)";
        $stmtV1 = $mysqli->prepare($sqlV1);
        if (!$stmtV1) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Vermerk Prepare 1 failed: ' . $mysqli->error]);
            exit;
        }
        $relevanteDokumenteText = "Relevante Dokumente: \n " . $relevanteDokumente;
        $vermerkart1 = 'Info';
        $stmtV1->bind_param('isss', $untergruppenId, $gruppenVerfasser, $relevanteDokumenteText, $vermerkart1);
        if (!$stmtV1->execute()) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Vermerk Insert 1 failed: ' . $stmtV1->error]);
            $stmtV1->close();
            exit;
        }
        $stmtV1->close();
    }

    echo json_encode(['success' => true, 'insertId' => $insertId]);
    $stmt->close();
    $mysqli->close();
    exit;


}


if ($action === "getProtokollBesprechungen") {
    $sql = "SELECT *
            FROM LIMET_RB.tabelle_Vermerkgruppe
            WHERE tabelle_projekte_idTABELLE_Projekte = ? AND Gruppenart = 'Protokoll Besprechung'
            ORDER BY Datum DESC, Startzeit DESC";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $mysqli->error]);
        exit;
    }
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
    $stmt->close();
    $mysqli->close();
    echo json_encode(['success' => true, 'data' => $list]);
    exit;
}

if ($action === "Consolidate") {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        exit;
    }
    $raumbereiche = $_POST['raumbereiche'] ?? [];
    if ($projectID <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ungültige oder fehlende Projekt-ID']);
        exit;
    }
    if (!is_array($raumbereiche) || count($raumbereiche) === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Keine Raumbereiche übermittelt']);
        exit;
    }
    try {
        $placeholders = implode(',', array_fill(0, count($raumbereiche), '?'));
        $sql = "SELECT idTABELLE_Räume AS id FROM tabelle_räume 
            WHERE tabelle_projekte_idTABELLE_Projekte = ? 
            AND `Raumbereich Nutzer` IN ($placeholders) 
            AND Entfallen = 0";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) throw new Exception("Prepare Statement fehlgeschlagen: " . $mysqli->error);
        $types = 'i' . str_repeat('s', count($raumbereiche));
        $params = array_merge([$projectID], $raumbereiche);
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
            $mysqli->close();
            exit;
        }

        // Konsolidierungsfunktion für einen Raum
        function consolidateRoomElements(mysqli $mysqli, int $raumId): void
        {
            $sql = "SELECT * FROM tabelle_räume_has_tabelle_elemente WHERE TABELLE_Räume_idTABELLE_Räume = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) throw new Exception("Prepare (select) fehlgeschlagen: " . $mysqli->error);
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

            $updateStmt = $mysqli->prepare("UPDATE tabelle_räume_has_tabelle_elemente SET Anzahl = ?, Kurzbeschreibung = ? WHERE id = ?");
            if (!$updateStmt) throw new Exception("Prepare (update) fehlgeschlagen: " . $mysqli->error);

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

        foreach ($raumIds as $rid) {
            consolidateRoomElements($mysqli, $rid);
        }
        $mysqli->close();
        echo json_encode(['success' => true, 'message' => 'Konsolidierung der Elemente in allen Räumen der Bereiche abgeschlossen.']);
    } catch (Exception $e) {
        if ($mysqli) $mysqli->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()]);
    }
}


if ($action === 'freigabeAlle') {
    // $logFile = __DIR__ . '/freigabe_log.txt';
    $msg = "";
    function writeLog($message)
    {
        //  global $logFile;
        // file_put_contents($logFile, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
        global $msg;
        $msg = $msg . $message;
    }

    $vermerkIDs = $_POST['vermerkIDs'] ?? [];
    if (!is_array($vermerkIDs) || count($vermerkIDs) === 0) {
        //writeLog('FEHLER: Keine Vermerk-IDs erhalten.');
        echo json_encode(['success' => false, 'message' => 'Keine Vermerk-IDs erhalten.']);
        exit;
    }
    //writeLog('Starte Freigabe für Vermerk-IDs: ' . implode(',', $vermerkIDs));

    if ($mysqli->connect_error) {
        //writeLog('FEHLER: Datenbankverbindung fehlgeschlagen: ' . $mysqli->connect_error);
        echo json_encode(['success' => false, 'message' => 'Datenbankverbindung fehlgeschlagen.']);
        exit;
    }

    $idsString = implode(',', array_map('intval', $vermerkIDs));

    // Schritt 1: IDs der Einträge in tabelle_räume_has_tabelle_elemente finden
    $sqlFindIds = "
        SELECT r.id FROM tabelle_räume_has_tabelle_elemente r
        JOIN tabelle_rb_aenderung a ON r.id = a.id
        WHERE a.vermerk_ID IN ($idsString)
    ";
    $result = $mysqli->query($sqlFindIds);
    if (!$result) {
        //writeLog("FEHLER: ID-Suche fehlgeschlagen: " . $mysqli->error);
        echo json_encode(['success' => false, 'message' => 'Fehler bei der ID-Suche: ' . $mysqli->error]);
        exit;
    }

    $idsToUpdate = [];
    while ($row = $result->fetch_assoc()) {
        $idsToUpdate[] = $row['id'];
    }

    if (count($idsToUpdate) > 0) {
        $updateIdsString = implode(',', array_map('intval', $idsToUpdate));
        // Schritt 2: Status in tabelle_räume_has_tabelle_elemente aktualisieren
        $sqlUpdateStatus = "
            UPDATE tabelle_räume_has_tabelle_elemente
            SET status = 2
            WHERE id IN ($updateIdsString)
        ";
        $resultUpdate = $mysqli->query($sqlUpdateStatus);
        if ($resultUpdate) {
            //writeLog("Status erfolgreich aktualisiert für IDs: $updateIdsString");
        } else {
            //writeLog("FEHLER: Status-Update fehlgeschlagen: " . $mysqli->error);
            echo json_encode(['success' => false, 'message' => 'Status-Update fehlgeschlagen: ' . $mysqli->error]);
            exit;
        }
    } else {
        //writeLog("Keine zu aktualisierenden IDs gefunden.");
        echo json_encode(['success' => false, 'message' => 'Keine zu aktualisierenden IDs gefunden.']);
        exit;
    }

    // Vermerkart in tabelle_Vermerke aktualisieren
    $sqlUpdateVermerke = "
        UPDATE tabelle_Vermerke
        SET Vermerkart = 'Freigegeben'
        WHERE idtabelle_Vermerke IN ($idsString)
          AND Vermerkart = 'Nutzerwunsch'
    ";
    $resultVermerke = $mysqli->query($sqlUpdateVermerke);
    if ($resultVermerke) {
        //writeLog("Vermerkart erfolgreich aktualisiert für IDs: $idsString");
    } else {
        //writeLog("FEHLER: Aktualisierung Vermerkart fehlgeschlagen: " . $mysqli->error);
        echo json_encode(['success' => false, 'message' => 'Aktualisierung der Vermerkart fehlgeschlagen: ' . $mysqli->error]);
        exit;
    }

    //writeLog("Freigabe erfolgreich abgeschlossen.");
    echo json_encode(['success' => true, 'message' => 'Änderungen und Vermerke erfolgreich freigegeben.' . $msg]);

    $mysqli->close();
    exit;
}


if ($action === 'resetAlleBtn') {
    // $logFile = __DIR__ . '/freigabe_log.txt';
    /*  $msg = "";
      function writeLog($message)
      {
          //  global $logFile;
          // file_put_contents($logFile, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
          global $msg;
          $msg = $msg. $message;
      }*/
    $vermerkIDs = $_POST['vermerkIDs'] ?? [];
    if (!is_array($vermerkIDs) || count($vermerkIDs) === 0) {
        //writeLog('FEHLER: Keine Vermerk-IDs erhalten.');
        echo json_encode(['success' => false, 'message' => 'Keine Vermerk-IDs erhalten.']);
        exit;
    }
    //writeLog('Starte Freigabe für Vermerk-IDs: ' . implode(',', $vermerkIDs));
    if ($mysqli->connect_error) {
        //writeLog('FEHLER: Datenbankverbindung fehlgeschlagen: ' . $mysqli->connect_error);
        echo json_encode(['success' => false, 'message' => 'Datenbankverbindung fehlgeschlagen.']);
        exit;
    }
    $idsString = implode(',', array_map('intval', $vermerkIDs));


    $sqlFindIds = "
        SELECT r.id FROM tabelle_räume_has_tabelle_elemente r
        JOIN tabelle_rb_aenderung a ON r.id = a.id
        WHERE a.vermerk_ID IN ($idsString)";
    $result = $mysqli->query($sqlFindIds);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Fehler bei der ID-Suche: ' . $mysqli->error]);
        exit;
    }

    $idsToUpdate = [];
    while ($row = $result->fetch_assoc()) {
        $idsToUpdate[] = $row['id'];
    }

    if (count($idsToUpdate) > 0) {
        $updateIdsString = implode(',', array_map('intval', $idsToUpdate));
        // Schritt 2: Status in tabelle_räume_has_tabelle_elemente aktualisieren
        $sqlUpdateStatus = "
            UPDATE tabelle_räume_has_tabelle_elemente
            SET status = 0
            WHERE id IN ($updateIdsString)
        ";
        $resultUpdate = $mysqli->query($sqlUpdateStatus);
        if ($resultUpdate) {
            //writeLog("Status erfolgreich aktualisiert für IDs: $updateIdsString");
        } else {
            //writeLog("FEHLER: Status-Update fehlgeschlagen: " . $mysqli->error);
            echo json_encode(['success' => false, 'message' => 'Status-Update fehlgeschlagen: ' . $mysqli->error]);
            exit;
        }
    } else {
        //writeLog("Keine zu aktualisierenden IDs gefunden.");
        echo json_encode(['success' => false, 'message' => 'Keine zu aktualisierenden IDs gefunden.']);
        exit;
    }

    // Vermerkart in tabelle_Vermerke aktualisieren
    $sqlUpdateVermerke = "
        UPDATE tabelle_Vermerke
        SET Vermerkart = 'Info'
        WHERE idtabelle_Vermerke IN ($idsString)";
    $resultVermerke = $mysqli->query($sqlUpdateVermerke);

    echo json_encode(['success' => true, 'message' => 'Änderungen und Vermerke erfolgreich freigegeben.' . $msg]);

    $mysqli->close();
    exit;
}


if ($action === 'getVermerkeByVermerkgruppe') {
    $vermerkgruppeID = intval($_POST['vermerkgruppeID']);
    $sql = "
        SELECT u.idtabelle_Vermerkuntergruppe, v.idtabelle_Vermerke
        FROM tabelle_Vermerkuntergruppe u
        LEFT JOIN tabelle_Vermerke v ON v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = u.idtabelle_Vermerkuntergruppe
        WHERE u.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?
        ORDER BY u.Untergruppennummer ASC, v.Erstellungszeit ASC
    ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $vermerkgruppeID);
    $stmt->execute();
    $result = $stmt->get_result();

    $roomVermerkMap = [];
    while ($row = $result->fetch_assoc()) {
        $ugID = $row['idtabelle_Vermerkuntergruppe'];
        $vid = $row['idtabelle_Vermerke'];
        if (!isset($roomVermerkMap[$ugID])) {
            $roomVermerkMap[$ugID] = [];
        }
        if ($vid !== null) {
            $roomVermerkMap[$ugID][] = $vid;
        }
    }
    echo json_encode(['success' => true, 'data' => $roomVermerkMap]);
    exit;
}


$mysqli->close();
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Unbekannte oder fehlende Aktion']);
exit;