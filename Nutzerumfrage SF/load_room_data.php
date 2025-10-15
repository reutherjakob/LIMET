<?php
global $mysqli;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargefeld_ext_users"]);

header('Content-Type: application/json');

$roomId = $_POST['roomId'] ?? null;

if (!$roomId || !is_numeric($roomId)) {
    echo json_encode(['error' => 'Ungültige roomId']);
    exit;
}

$stmt = $mysqli->prepare("SELECT * FROM tabelle_room_requirements_from_user WHERE roomID = ?");
if (!$stmt) {
    echo json_encode(['error' => 'Datenbankfehler']);
    exit;
}

$stmt->bind_param('i', $roomId);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();

if (!$data) {
    echo json_encode(['error' => 'Keine Daten für diese roomId gefunden']);
    exit;
}

echo json_encode(['data' => $data]);
$stmt->close();
?>
