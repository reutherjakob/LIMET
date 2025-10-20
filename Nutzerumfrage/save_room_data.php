<?php
global $mysqli;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargefeld_ext_users", "spargefeld_admin"]);
require_once("../Nutzerlogin/csrf.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_POST['csrf_token']) || !csrf_check($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Ungültiges CSRF-Token']);
    exit;
}

$mysqli->set_charset('utf8mb4');

$roomID = $_POST['roomID'] ?? null;
if ($roomID === null) {
    echo json_encode(['status' => 'error', 'message' => 'Missing roomID.']);
    exit;
}

// Explicit parameter fields to save
$params = [
    'roomID',
    'roomname',
    'bsl_level',
    'chemikalienliste',
    'verdunkelung',
    'sonderabluft',
    'spezialgas',
    'usv_geraete',
    'VE_Wasser',
    'kuehlwasser'
];

// Collect parameter values from POST, set null if missing
$values = [];
foreach ($params as $param) {
    // For yesno / boolean params, store '1' or '0' string, else null
    if (isset($_POST[$param])) {
        // Normalize boolean inputs (checkbox or buttons)
        $val = $_POST[$param];
        if ($val === '1' || $val === 1 || $val === 'on' || $val === true) {
            $values[$param] = '1';
        } else {
            $values[$param] = '0';
        }
    } else {
        $values[$param] = '0'; // Default to 0 if not set on yesno fields
    }
}

// Check if entry exists
$stmtCheck = $mysqli->prepare("SELECT COUNT(*) FROM tabelle_room_requirements_from_user WHERE roomID = ?");
if (!$stmtCheck) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error]);
    exit;
}
$stmtCheck->bind_param('i', $roomID);
$stmtCheck->execute();
$stmtCheck->bind_result($count);
$stmtCheck->fetch();
$stmtCheck->close();

if ($count > 0) {
    // Update existing record
    $sqlUpdate = "UPDATE tabelle_room_requirements_from_user SET 
        bsl_level = ?, 
        chemikalienliste = ?, 
        verdunkelung = ?, 
        sonderabluft = ?, 
        spezialgas = ?, 
        usv_geraete = ?, 
        VE_Wasser = ?, 
        kuehlwasser = ?,
        roomname = ? 
        WHERE roomID = ?";

    $stmt = $mysqli->prepare($sqlUpdate);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error]);
        exit;
    }
    $stmt->bind_param(
        'ssssssssis',
        $values['bsl_level'],
        $values['chemikalienliste'],
        $values['verdunkelung'],
        $values['sonderabluft'],
        $values['spezialgas'],
        $values['usv_geraete'],
        $values['VE_Wasser'],
        $values['kuehlwasser'],
        $roomID,
        $values['roomname']

    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data successfully updated.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed: (' . $stmt->errno . ') ' . $stmt->error]);
    }
    $stmt->close();

} else {
    // Insert new record
    $sqlInsert = "INSERT INTO tabelle_room_requirements_from_user 
        ( roomname, roomID, bsl_level, chemikalienliste, verdunkelung, sonderabluft, spezialgas, usv_geraete, VE_Wasser, kuehlwasser) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?  )";

    $stmt = $mysqli->prepare($sqlInsert);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error]);
        exit;
    }
    $stmt->bind_param(
        'isssssssss',
        $roomID,
        $values['bsl_level'],
        $values['chemikalienliste'],
        $values['verdunkelung'],
        $values['sonderabluft'],
        $values['spezialgas'],
        $values['usv_geraete'],
        $values['VE_Wasser'],
        $values['kuehlwasser'],
        $values['roomname']
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data successfully inserted.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Insert failed: (' . $stmt->errno . ') ' . $stmt->error]);
    }
    $stmt->close();
}

$mysqli->close();
