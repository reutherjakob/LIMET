<?php
ob_start();

if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
ob_clean();
header('Content-Type: application/json; charset=utf-8');

function json_error(string $msg, int $code = 400): never
{
    http_response_code($code);
    echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$body = json_decode(file_get_contents('php://input'), true);
if (!isset($body['raumnummern']) || !is_array($body['raumnummern'])) {
    json_error('raumnummern fehlt oder kein Array');
}

if (!isset($_SESSION['projectID'])) {
    json_error('Kein Projekt in Session (projectID fehlt)');
}
$projekt_id = (int)$_SESSION['projectID'];
if ($projekt_id <= 0) {
    json_error('projectID ungueltig: ' . $projekt_id);
}

$raumnummern = array_values(array_unique(array_filter(array_map('trim', $body['raumnummern']))));
if (empty($raumnummern)) {
    echo json_encode(['results' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$mysqli = utils_connect_sql();

// ── Query 1: NUR Räume dieses Projekts, projekt_id im SELECT zur Verifikation ─
$sql_rooms = "
    SELECT r.`idTABELLE_Räume`, r.Raumnr, r.Raumbezeichnung, r.Geschoss,
           r.tabelle_projekte_idTABELLE_Projekte AS debug_projekt
    FROM tabelle_räume r
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
    ORDER BY r.Raumnr
";

$stmt = $mysqli->prepare($sql_rooms);
if (!$stmt) {
    json_error('DB prepare error: ' . $mysqli->error, 500);
}
$stmt->bind_param('i', $projekt_id);
$stmt->execute();
$all_rooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// In PHP auf gesuchte Raumnummern filtern
$raumnummern_set = array_flip($raumnummern);
$rooms_raw = array_filter($all_rooms, fn($r) => isset($raumnummern_set[$r['Raumnr']]));

$rooms_by_nr = [];
foreach ($rooms_raw as $row) {
    $rooms_by_nr[$row['Raumnr']][] = $row;
}

// ── Query 2: Elemente ─────────────────────────────────────────────────────────
$found_ids = [];
foreach ($rooms_by_nr as $rows) {
    if (count($rows) === 1) $found_ids[] = (int)$rows[0]['idTABELLE_Räume'];
}

$elemente_by_room = [];
if (!empty($found_ids)) {
    $id_ph = implode(',', array_fill(0, count($found_ids), '?'));
    $types_elems = str_repeat('i', count($found_ids));

    $sql_elems = "
        SELECT rhe.`TABELLE_Räume_idTABELLE_Räume` AS raum_id,
               e.idTABELLE_Elemente AS element_id,
               e.Bezeichnung AS bezeichnung,
               e.ElementID AS element_code,
               rhe.Anzahl
        FROM tabelle_räume_has_tabelle_elemente rhe
        JOIN tabelle_elemente e ON e.idTABELLE_Elemente = rhe.`TABELLE_Elemente_idTABELLE_Elemente`
        WHERE rhe.`TABELLE_Räume_idTABELLE_Räume` IN ($id_ph)
        and Anzahl <>0 
        ORDER BY e.Bezeichnung
    ";

    $stmt2 = $mysqli->prepare($sql_elems);
    if (!$stmt2) {
        json_error('DB prepare error (elemente): ' . $mysqli->error, 500);
    }
    $refs2 = [&$types_elems];
    foreach ($found_ids as $k => $_) {
        $refs2[] = &$found_ids[$k];
    }
    call_user_func_array([$stmt2, 'bind_param'], $refs2);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($el = $res2->fetch_assoc()) {
        $elemente_by_room[(int)$el['raum_id']][] = [
            'id' => (int)$el['element_id'],
            'code' => $el['element_code'],
            'bezeichnung' => $el['bezeichnung'],
            'anzahl' => (int)$el['Anzahl'],
        ];
    }
    $stmt2->close();
}

// ── Antwort ───────────────────────────────────────────────────────────────────
$results = [];
foreach ($raumnummern as $nr) {
    if (!isset($rooms_by_nr[$nr])) {
        $results[$nr] = ['raumnr' => $nr, 'status' => 'not_found', 'rooms' => [], 'elemente' => []];
    } elseif (count($rooms_by_nr[$nr]) > 1) {
        // Wirklich doppelt im selben Projekt — Raumnr nicht unique in tabelle_räume
        $results[$nr] = ['raumnr' => $nr, 'status' => 'duplicate', 'rooms' => $rooms_by_nr[$nr], 'elemente' => []];
    } else {
        $room = $rooms_by_nr[$nr][0];
        $raum_id = (int)$room['idTABELLE_Räume'];
        $results[$nr] = [
            'raumnr' => $nr,
            'status' => 'found',
            'raum_id' => $raum_id,
            'bezeichnung' => $room['Raumbezeichnung'],
            'geschoss' => $room['Geschoss'] ?? '',
            'elemente' => $elemente_by_room[$raum_id] ?? [],
        ];
    }
}

echo json_encode(['projekt_id' => $projekt_id, 'results' => $results], JSON_UNESCAPED_UNICODE);