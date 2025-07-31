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

// Retrieve and sanitize inputs from POST
$gruppenName = trim(filter_input(INPUT_POST, 'meetingName', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenart = trim(filter_input(INPUT_POST, 'meetingart', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenOrt = trim(filter_input(INPUT_POST, 'meetingOrt', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenVerfasser = trim(filter_input(INPUT_POST, 'meetingVerfasser', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenStart = trim(filter_input(INPUT_POST, 'meetingStart', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenEnde = trim(filter_input(INPUT_POST, 'meetingEnde', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$gruppenDatum = trim(filter_input(INPUT_POST, 'meetingDatum', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

$projectID = (int)($_SESSION["projectID"] ?? 0);

// Basic validation - check required fields
if (empty($gruppenName) || empty($gruppenart) || empty($gruppenVerfasser) || empty($gruppenStart) || empty($gruppenDatum) || $projectID <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fehlende Pflichtfelder oder ungültiges Projekt']);
    exit;
}

// Optional: Add date/time format validation here if desired

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

// Successfully inserted - return inserted ID
$insertId = $mysqli->insert_id;

$stmt->close();
$mysqli->close();

echo json_encode(['success' => true, 'insertId' => $insertId]);
exit;
