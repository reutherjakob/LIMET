<?php
global $mysqli;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargefeld_ext_users"]);

header('Content-Type: application/json');

$roomId = $_POST['roomId'] ?? null;

if (!$roomId || !is_numeric($roomId)) {
    echo json_encode(['error' => '[translate:Ungültige roomId]']);
    exit;
}

// Only these fields are loaded as requested:
$fields = [
    "roomID",
    'bsl_level',
    'chemikalienliste',
    'verdunkelung',
    'sonderabluft',
    'spezialgas',
    'usv_geraete',
    'VE_Wasser',
    'kuehlwasser'
];

// Prepare explicit field list string for the SQL query
$fieldList = implode(', ', $fields);

$stmt = $mysqli->prepare("SELECT $fieldList FROM tabelle_room_requirements_from_user WHERE roomID = ?");
if (!$stmt) {
    echo json_encode(['error' => '[translate:Datenbankfehler]']);
    exit;
}

$stmt->bind_param('i', $roomId);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();

if (!$data) {
    echo json_encode(['newRoom' => '[translate:Keine Daten für diese roomId gefunden]']);
    exit;
}

// Normalize string booleans '0'/'1' to integers for JS checkbox compatibility
foreach ($data as $key => $val) {
    if ($val === '0' || $val === '1') {
        $data[$key] = (int)$val;
    }
}

echo json_encode(['data' => $data]);
$stmt->close();
