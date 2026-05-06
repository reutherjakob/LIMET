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

$stringFields = [
    'roomname',
    'raumnr',
    'raumbereich_nutzer',
    'raumkategorieAbfrage',
    'explosionsschutz',
    'raumzuluft_besonders',
    'spezialabwasser',
    'vibrationsempfindlich_bodenstehend_kommentar',
    'abluftwaescher_kommentar',
    'spezialabwasser_kommentar',
    'nutzwasser_kommentar',
    'kuehlwasser_kommentar',
    'raumtemp_kommentar',
    'luftf_kommentar',
    'spezialgas_kommentar'
];

$boolFields = [
    'doppelfluegeltuer',
    'vibrationsempfindlich_bodenstehend',
    'nutzwasser',
    'kuehlwasser',
    'DL',
    'N2',
    'Vakuum',
    'raumtemp',
    'luftf',
];

$intFields = [
    'abluftwaescher',
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
    if (!isset($_POST[$f]) || $_POST[$f] === '' || $_POST[$f] === 'unbekannt' || $_POST[$f] === '?') {
        $data[$f] = null;
    } else {
        $data[$f] = $_POST[$f] === '1' ? 1 : 0;
    }
}
foreach ($intFields as $f) {
    $data[$f] = isset($_POST[$f]) ? (int)$_POST[$f] : 0;
}

if (isset($_POST['raumabluft_besonders']) && is_array($_POST['raumabluft_besonders'])) {
    $data['raumabluft_besonders'] = implode(',', array_map('trim', $_POST['raumabluft_besonders']));
} else {
    $data['raumabluft_besonders'] = 'Nein';
}

if (isset($_POST['spezialgas']) && is_array($_POST['spezialgas'])) {
    $data['spezialgas'] = implode(',', array_map('trim', $_POST['spezialgas']));
} else {
    $data['spezialgas'] = 'Nein';
}

$data['nf'] = isset($_POST['nf']) && is_numeric($_POST['nf']) ? (float)$_POST['nf'] : null;

$sql = "INSERT INTO tabelle_room_requirements_from_user
    (roomID, roomname, raumnr, raumbereich_nutzer, nf, raumkategorieAbfrage,
     doppelfluegeltuer,
     vibrationsempfindlich_bodenstehend, vibrationsempfindlich_bodenstehend_kommentar,
     explosionsschutz,
     abluftwaescher, abluftwaescher_kommentar,
     spezialgas,
     raumabluft_besonders, raumzuluft_besonders,
     nutzwasser, nutzwasser_kommentar,
     spezialabwasser, spezialabwasser_kommentar,
     DL, N2, Vakuum,
     kuehlwasser, kuehlwasser_kommentar,
     raumtemp, raumtemp_kommentar,
     luftf, luftf_kommentar,
     username, created_at, spezialgas_kommentar)
VALUES (?,?,?,?,?,?, ?, ?,?, ?, ?,?, ?, ?,?, ?,?, ?,?, ?,?,?, ?,?, ?,?, ?,?, ?,?,?)
ON DUPLICATE KEY UPDATE
    roomname                                     = VALUES(roomname),
    raumnr                                       = VALUES(raumnr),
    raumbereich_nutzer                           = VALUES(raumbereich_nutzer),
    nf                                           = VALUES(nf),
    raumkategorieAbfrage                         = VALUES(raumkategorieAbfrage),
    doppelfluegeltuer                            = VALUES(doppelfluegeltuer),
    vibrationsempfindlich_bodenstehend           = VALUES(vibrationsempfindlich_bodenstehend),
    vibrationsempfindlich_bodenstehend_kommentar = VALUES(vibrationsempfindlich_bodenstehend_kommentar),
    explosionsschutz                             = VALUES(explosionsschutz),
    abluftwaescher                               = VALUES(abluftwaescher),
    abluftwaescher_kommentar                     = VALUES(abluftwaescher_kommentar),
    spezialgas                                   = VALUES(spezialgas),
    raumabluft_besonders                         = VALUES(raumabluft_besonders),
    raumzuluft_besonders                         = VALUES(raumzuluft_besonders),
    nutzwasser                                   = VALUES(nutzwasser),
    nutzwasser_kommentar                         = VALUES(nutzwasser_kommentar),
    spezialabwasser                              = VALUES(spezialabwasser),
    spezialabwasser_kommentar                    = VALUES(spezialabwasser_kommentar),
    DL                                           = VALUES(DL),
    N2                                           = VALUES(N2),
    Vakuum                                       = VALUES(Vakuum),
    kuehlwasser                                  = VALUES(kuehlwasser),
    kuehlwasser_kommentar                        = VALUES(kuehlwasser_kommentar),
    raumtemp                                     = VALUES(raumtemp),
    raumtemp_kommentar                           = VALUES(raumtemp_kommentar),
    luftf                                        = VALUES(luftf),
    luftf_kommentar                              = VALUES(luftf_kommentar),
    username                                     = VALUES(username),
    created_at                                   = VALUES(created_at),
        spezialgas_kommentar                                   = VALUES(spezialgas_kommentar)
    
    ";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $mysqli->error]);
    exit;
}

foreach ($stringFields as $f) {
    if (!isset($data[$f]) || $data[$f] === null) {
        $data[$f] = '';
    }
}
if (!isset($data['spezialgas'])) $data['spezialgas'] = '';

// i s s s d s  i  i  s  s  i    s   s  s s      i s  s s  i i i  i s  i s  i s  s s
$stmt->bind_param('isssdsiississssisssiiiisisissss',
    $data['roomID'],                                        // i
    $data['roomname'],                                      // s
    $data['raumnr'],                                        // s
    $data['raumbereich_nutzer'],                            // s
    $data['nf'],                                            // d
    $data['raumkategorieAbfrage'],                          // s
    $data['doppelfluegeltuer'],                             // i
    $data['vibrationsempfindlich_bodenstehend'],            // i
    $data['vibrationsempfindlich_bodenstehend_kommentar'],  // s
    $data['explosionsschutz'],                              // s
    $data['abluftwaescher'],                                // i
    $data['abluftwaescher_kommentar'],                      // s
    $data['spezialgas'],                                    // s
    $data['raumabluft_besonders'],                          // s
    $data['raumzuluft_besonders'],                          // s
    $data['nutzwasser'],                                    // i
    $data['nutzwasser_kommentar'],                          // s
    $data['spezialabwasser'],                               // s
    $data['spezialabwasser_kommentar'],                     // s
    $data['DL'],                                            // i
    $data['N2'],                                            // i
    $data['Vakuum'],                                        // i
    $data['kuehlwasser'],                                   // i
    $data['kuehlwasser_kommentar'],                         // s
    $data['raumtemp'],                                      // i
    $data['raumtemp_kommentar'],                            // s
    $data['luftf'],                                         // i
    $data['luftf_kommentar'],                               // s
    $data['username'],                                      // s
    $data['created_at'],                                     // s
    $data['spezialgas_kommentar']
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Gespeichert.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Fehler: ' . $stmt->error]);
}
$stmt->close();
$mysqli->close();