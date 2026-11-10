<?php
global $mysqli;
include "../roombook_nutzerlogin/db.php";
require_once "../roombook_nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_view", "spargelfeld_admin"]);
header('Content-Type: application/json');

$roomId = $_GET['roomId'] ?? null;

if (!$roomId || !is_numeric($roomId)) {
    echo json_encode(['error' => '[translate:Ungültige roomId]']);
    exit;
}

$stmt = $mysqli->prepare("SELECT verdunkelung, verdunkelung_kommentar,
        lichtsteuerung, lichtsteuerung_kommentar,
        hoehenverstellbare_labortische, hoehenverstellbare_labortische_kommentar,
        giftschrank,
        akkreditierung, akkreditierung_kommentar,
        ve_wasser, ve_wasser_kommentar
    FROM tabelle_room_requirements_from_user
    WHERE roomID = ? AND entwurf_saved_at IS NOT NULL");

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

foreach ($data as $key => $val) {
   if ($val === '0' || $val === '1') {
        $data[$key] = (int)$val;
    }
}

echo json_encode(['data' => $data]);
$stmt->close();