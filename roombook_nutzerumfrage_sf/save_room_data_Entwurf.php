<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
global $mysqli;
include "../roombook_nutzerlogin/db.php";
require_once "../roombook_nutzerlogin/_utils.php";
require_once "../roombook_nutzerlogin/csrf.php";

init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_admin"]);
header('Content-Type: application/json');

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
$stringFields = [
    'roomname',
    'raumnr',
    'raumbereich_nutzer',
    'raumkategorieAbfrage',
    'verdunkelung_kommentar',
    'lichtsteuerung_kommentar',
    'hoehenverstellbare_labortische_kommentar',
    'akkreditierung_kommentar',
    've_wasser_kommentar',
];

$boolFields = [
    'verdunkelung',
    'lichtsteuerung',
    'hoehenverstellbare_labortische',
    'akkreditierung',
    've_wasser',
];

$roomID = isset($_POST['roomID']) ? (int)$_POST['roomID'] : null;
if (!$roomID) {
    echo json_encode(['status' => 'error', 'message' => 'Missing roomID.']);
    exit;
}

$data = ['roomID' => $roomID];
$data['entwurf_username'] = $_SESSION['user_name'] ?? 'unknown';
$data['entwurf_saved_at'] = date('Y-m-d H:i:s');

foreach ($stringFields as $f) {
    $data[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : '';
}
foreach ($boolFields as $f) {
    if (!isset($_POST[$f]) || $_POST[$f] === '' || $_POST[$f] === 'unbekannt' || $_POST[$f] === '?') {
        $data[$f] = null;
    } else {
        $data[$f] = $_POST[$f] === '1' ? 1 : 0;
    }
}

$giftschrankOptionen = ['Nein', 'Unterbau_Digestor', 'Eigener_Schrank'];
$data['giftschrank'] = in_array($_POST['giftschrank'] ?? '', $giftschrankOptionen, true)
    ? $_POST['giftschrank']
    : null;

$data['nf'] = isset($_POST['nf']) && is_numeric($_POST['nf']) ? (float)$_POST['nf'] : null;

// Bestehende VE-Spalten stehen bewusst NICHT im UPDATE-Teil.
$sql = "INSERT INTO tabelle_room_requirements_from_user
    (roomID, roomname, raumnr, raumbereich_nutzer, nf, raumkategorieAbfrage,
     verdunkelung, verdunkelung_kommentar,
     lichtsteuerung, lichtsteuerung_kommentar,
     hoehenverstellbare_labortische, hoehenverstellbare_labortische_kommentar,
     giftschrank,
     akkreditierung, akkreditierung_kommentar,
     ve_wasser, ve_wasser_kommentar,
     entwurf_username, entwurf_saved_at)
VALUES (?,?,?,?,?,?, ?,?, ?,?, ?,?, ?, ?,?, ?,?, ?,?)
ON DUPLICATE KEY UPDATE
    verdunkelung                             = VALUES(verdunkelung),
    verdunkelung_kommentar                   = VALUES(verdunkelung_kommentar),
    lichtsteuerung                           = VALUES(lichtsteuerung),
    lichtsteuerung_kommentar                 = VALUES(lichtsteuerung_kommentar),
    hoehenverstellbare_labortische           = VALUES(hoehenverstellbare_labortische),
    hoehenverstellbare_labortische_kommentar = VALUES(hoehenverstellbare_labortische_kommentar),
    giftschrank                              = VALUES(giftschrank),
    akkreditierung                           = VALUES(akkreditierung),
    akkreditierung_kommentar                 = VALUES(akkreditierung_kommentar),
    ve_wasser                                = VALUES(ve_wasser),
    ve_wasser_kommentar                      = VALUES(ve_wasser_kommentar),
    entwurf_username                         = VALUES(entwurf_username),
    entwurf_saved_at                         = VALUES(entwurf_saved_at)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $mysqli->error]);
    exit;
}

$stmt->bind_param('isssdsisisissisisss',
    $data['roomID'],
    $data['roomname'],
    $data['raumnr'],
    $data['raumbereich_nutzer'],
    $data['nf'],
    $data['raumkategorieAbfrage'],
    $data['verdunkelung'],
    $data['verdunkelung_kommentar'],
    $data['lichtsteuerung'],
    $data['lichtsteuerung_kommentar'],
    $data['hoehenverstellbare_labortische'],
    $data['hoehenverstellbare_labortische_kommentar'],
    $data['giftschrank'],
    $data['akkreditierung'],
    $data['akkreditierung_kommentar'],
    $data['ve_wasser'],
    $data['ve_wasser_kommentar'],
    $data['entwurf_username'],
    $data['entwurf_saved_at']
);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Gespeichert.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Fehler: ' . $stmt->error]);
}
$stmt->close();
$mysqli->close();