<?php
ob_start();
require_once 'utils/_utils.php';
init_page_serversides();

$projectID = (int)($_SESSION["projectID"] ?? 0);

ob_clean();
session_write_close();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Nur POST erlaubt']);
    exit;
}

if (!$projectID) {
    echo json_encode(['ok' => false, 'msg' => 'Kein Projekt in Session']);
    exit;
}

$allowedFields = [
    'Bemerkung_Allgemein',
    'Bemerkung_ET',
    'Bemerkung_MG',
    'Bemerkung_Wasser',
    'Bemerkung_Abwasser',
    'Bemerkung_Lüftung',
    'Bemerkung_GLT',
    'Bemerkung_Kaelte',
    'Bemerkung_Arch',
];

$raw     = $_POST['entries'] ?? '';
$entries = json_decode($raw, true);

if (!is_array($entries) || empty($entries)) {
    echo json_encode(['ok' => false, 'msg' => 'Keine Einträge', 'raw' => $raw]);
    exit;
}

$mysqli = utils_connect_sql();
$mysqli->set_charset('utf8mb4');

$errors = [];
$saved  = 0;

foreach ($entries as $entry) {
    $elementID  = (int)($entry['elementID']  ?? 0);
    $varianteID = (int)($entry['varianteID'] ?? 0);
    $field      = trim($entry['field']        ?? '');
    $value      = substr(trim($entry['value'] ?? ''), 0, 512);

    if ($elementID <= 0 || !in_array($field, $allowedFields, true)) {
        $errors[] = ['field' => $field, 'reason' => 'ungültig'];
        continue;
    }

    // Erst prüfen ob Zeile existiert
    $check = $mysqli->prepare("SELECT id FROM tabelle_Element_has_Anmerkung WHERE ElementID=? AND VariantenID=? AND ProjektID=?");
    $check->bind_param('iii', $elementID, $varianteID, $projectID);
    $check->execute();
    $check->store_result();
    $exists = $check->num_rows > 0;
    $check->close();

    if ($exists) {
        // UPDATE
        $stmt = $mysqli->prepare("UPDATE tabelle_Element_has_Anmerkung SET `$field`=? WHERE ElementID=? AND VariantenID=? AND ProjektID=?");
        $stmt->bind_param('siii', $value, $elementID, $varianteID, $projectID);
    } else {
        // INSERT
        $stmt = $mysqli->prepare("INSERT INTO tabelle_Element_has_Anmerkung (ElementID, VariantenID, ProjektID, `$field`) VALUES (?,?,?,?)");
        $stmt->bind_param('iiis', $elementID, $varianteID, $projectID, $value);
    }

    if ($stmt->execute()) {
        $saved++;
    } else {
        $errors[] = ['field' => $field, 'reason' => $stmt->error];
    }
    $stmt->close();
}
$mysqli->close();

echo json_encode([
    'ok'     => empty($errors),
    'saved'  => $saved,
    'total'  => count($entries),
    'errors' => $errors,
]);
exit;