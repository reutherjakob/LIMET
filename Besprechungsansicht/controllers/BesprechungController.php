<?php
session_start();
include_once '../../utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
header('Content-Type: application/json; charset=utf-8');

$action = getPostString('action');
$gruppenName = getPostString('name');
$gruppenart = getPostString('art');
$gruppenOrt = getPostString('ort');
$gruppenVerfasser = getPostString('verfasser');
$gruppenStart = getPostString('startzeit');
$gruppenEnde = getPostString('endzeit');
$gruppenDatum = getPostString('datum');

$projectID = (int)($_SESSION["projectID"] ?? 0);
$insertId = 0;

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



$mysqli->close();
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Unbekannte oder fehlende Aktion']);
exit;