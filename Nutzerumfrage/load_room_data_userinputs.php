<?php
global $mysqli;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_view"]);

header('Content-Type: application/json');

$roomId = $_GET['roomId'] ?? null;

if (!$roomId || !is_numeric($roomId)) {
    echo json_encode(['error' => '[translate:Ungültige roomId]']);
    exit;
}


$stmt = $mysqli->prepare("SELECT *  FROM tabelle_room_requirements_from_user WHERE roomID = ?");
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
