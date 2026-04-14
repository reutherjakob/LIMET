<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
global $mysqli;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
require_once "../Nutzerlogin/csrf.php";


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

// Felder die direkt als String gespeichert werden
$stringFields = [
    'roomname',
    'raumnr',
    'raumbereich_nutzer',
    'raumkategorieAbfrage',
    'explosionsschutz',
    'raumabluft_besonders', 'raumzuluft_besonders', 'sonderabluft',
    'kaltwasser_stundenverbrauch', 'kaltwasser_spitzenverbrauch',
    'verbindungsgang_kommentar',
    'vibrationsempfindlich_bodenstehend_kommentar',
    'abluftwaescher_kommentar',
    'starkstromanschluss_anzahl_kommentar',
    'spezialabwasser_kommentar',
    'nutzwasser_kommentar',
];

// Felder die als 0/1 gespeichert werden
$boolFields = [
    'doppelfluegeltuer',
    'verbindungsgang',
    'vibrationsempfindlich_bodenstehend',
    'nutzwasser',
    'spezialabwasser',
];

// Integer-Felder (select mit Zahlen)
$intFields = [
    'abluftwaescher',
    'starkstromanschluss_anzahl',
];

$roomID = isset($_POST['roomID']) ? (int)$_POST['roomID'] : null;
if (!$roomID) {
    echo json_encode(['status' => 'error', 'message' => 'Missing roomID.']);
    exit;
}

$data = ['roomID' => $roomID];
$data['username'] = $_SESSION['user_name'] ?? 'unknown';
$data['created_at'] = date('Y-m-d H:i:s');

foreach ($stringFields as $f) {
    $data[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : '';
}
foreach ($boolFields as $f) {
    $data[$f] = isset($_POST[$f]) && $_POST[$f] === '1' ? 1 : 0;
}
foreach ($intFields as $f) {
    $data[$f] = isset($_POST[$f]) ? (int)$_POST[$f] : 0;
}

if (isset($_POST['spezialgas']) && is_array($_POST['spezialgas'])) {
    $data['spezialgas'] = implode(',', array_map('trim', $_POST['spezialgas']));
} else {
    $data['spezialgas'] = 'Nein';
}

$data['nf'] = isset($_POST['nf']) && is_numeric($_POST['nf']) ? (float)$_POST['nf'] : null;


$sql = "INSERT INTO tabelle_room_requirements_from_user 
    (roomID, roomname, raumnr, raumbereich_nutzer, nf, raumkategorieAbfrage,
     doppelfluegeltuer, verbindungsgang, verbindungsgang_kommentar,
     vibrationsempfindlich_bodenstehend, vibrationsempfindlich_bodenstehend_kommentar,
     explosionsschutz, abluftwaescher, abluftwaescher_kommentar,
     spezialgas, starkstromanschluss_anzahl, starkstromanschluss_anzahl_kommentar,
     raumabluft_besonders, raumzuluft_besonders, sonderabluft,
     nutzwasser, spezialabwasser, spezialabwasser_kommentar,
     kaltwasser_stundenverbrauch, kaltwasser_spitzenverbrauch,
      username, created_at, nutzwasser_kommentar)

VALUES (?,?,?,?,?,?, ?,?,?, ?,?, ?,?,?, ?,?,?, ?,?,?, ?,?,?, ?,?,?,?, ? )
ON DUPLICATE KEY UPDATE
    roomname                                    = VALUES(roomname),
    raumnr                                      = VALUES(raumnr),
    raumbereich_nutzer                          = VALUES(raumbereich_nutzer),
    nf                                          = VALUES(nf),
    raumkategorieAbfrage                        = VALUES(raumkategorieAbfrage),
    doppelfluegeltuer                           = VALUES(doppelfluegeltuer),
    verbindungsgang                             = VALUES(verbindungsgang),
    verbindungsgang_kommentar                   = VALUES(verbindungsgang_kommentar),
    vibrationsempfindlich_bodenstehend          = VALUES(vibrationsempfindlich_bodenstehend),
    vibrationsempfindlich_bodenstehend_kommentar = VALUES(vibrationsempfindlich_bodenstehend_kommentar),
    explosionsschutz                            = VALUES(explosionsschutz),
    abluftwaescher                              = VALUES(abluftwaescher),
    abluftwaescher_kommentar                    = VALUES(abluftwaescher_kommentar),
    spezialgas                                  = VALUES(spezialgas),
    starkstromanschluss_anzahl                  = VALUES(starkstromanschluss_anzahl),
    starkstromanschluss_anzahl_kommentar        = VALUES(starkstromanschluss_anzahl_kommentar),
    raumabluft_besonders                        = VALUES(raumabluft_besonders),
    raumzuluft_besonders                        = VALUES(raumzuluft_besonders),
    sonderabluft                                = VALUES(sonderabluft),
    nutzwasser                                  = VALUES(nutzwasser),
    spezialabwasser                             = VALUES(spezialabwasser),
    spezialabwasser_kommentar                   = VALUES(spezialabwasser_kommentar),
    kaltwasser_stundenverbrauch                 = VALUES(kaltwasser_stundenverbrauch),
    kaltwasser_spitzenverbrauch                 = VALUES(kaltwasser_spitzenverbrauch),
    username                                    = VALUES(username),
    created_at                                  = VALUES(created_at),
    nutzwasser_kommentar                   = VALUES(nutzwasser_kommentar)
    ";


$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $mysqli->error]);
    exit;
}


foreach (array_merge($stringFields, ['spezialgas']) as $f) {
    if (!isset($data[$f]) || $data[$f] === null) {
        $data[$f] = '';
    }
}


$stmt->bind_param('isssdsiisississsssssiissssss',
    $data['roomID'],
    $data['roomname'],
    $data['raumnr'],
    $data['raumbereich_nutzer'],
    $data['nf'],
    $data['raumkategorieAbfrage'],
    $data['doppelfluegeltuer'],
    $data['verbindungsgang'],
    $data['verbindungsgang_kommentar'],
    $data['vibrationsempfindlich_bodenstehend'],
    $data['vibrationsempfindlich_bodenstehend_kommentar'],
    $data['explosionsschutz'],
    $data['abluftwaescher'],
    $data['abluftwaescher_kommentar'],
    $data['spezialgas'],
    $data['starkstromanschluss_anzahl'],
    $data['starkstromanschluss_anzahl_kommentar'],
    $data['raumabluft_besonders'],
    $data['raumzuluft_besonders'],
    $data['sonderabluft'],
    $data['nutzwasser'],
    $data['spezialabwasser'],
    $data['spezialabwasser_kommentar'],
    $data['kaltwasser_stundenverbrauch'],
    $data['kaltwasser_spitzenverbrauch'],
    $data['username'],
    $data['created_at'],
    $data['nutzwasser_kommentar']

);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Gespeichert.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Fehler: ' . $stmt->error]);
}
$stmt->close();
$mysqli->close();