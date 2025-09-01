<?php
session_start();
include_once '../../utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Set proper header JSON response
header('Content-Type: application/json; charset=utf-8');

// Use POST instead of GET for data creation (recommended)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed, use POST']);
    exit;
}

$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$gruppenName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$gruppenart = filter_input(INPUT_POST, 'art', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$gruppenOrt = filter_input(INPUT_POST, 'ort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$gruppenVerfasser = filter_input(INPUT_POST, 'verfasser', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$gruppenStart = filter_input(INPUT_POST, 'startzeit', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$gruppenEnde = filter_input(INPUT_POST, 'endzeit', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$gruppenDatum = filter_input(INPUT_POST, 'datum', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$projectID = (int)($_SESSION["projectID"] ?? 0);
$insertId = 0;

//echo "action: " . $action . "<br>";
// echo "meetingName: " . $gruppenName . "<br>";
// echo "meetingart: " . $gruppenart . "<br>";
// echo "meetingOrt: " . $gruppenOrt . "<br>";
// echo "meetingVerfasser: " . $gruppenVerfasser . "<br>";
// echo "meetingStart: " . $gruppenStart . "<br>";
// echo "meetingEnde: " . $gruppenEnde . "<br>";
// echo "meetingDatum: " . $gruppenDatum . "<br>";


if ($action === "new") {
// Basic validation - check required fields
    if (empty($gruppenName) || empty($gruppenart) || empty($gruppenVerfasser) || empty($gruppenStart) || empty($gruppenDatum) || $projectID <= 0) {
        http_response_code(400);

        echo json_encode(['success' => false, 'message' => 'Fehlende Pflichtfelder oder ungültiges Projekt']);
        exit;
    }
// Prepare SQL insert statement
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