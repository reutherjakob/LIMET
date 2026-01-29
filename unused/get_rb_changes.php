<?php
header('Content-Type: application/json; charset=utf-8');
ob_start();

if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
init_page_serversides("", "x");
$mysqli = utils_connect_sql();
$projectID = $_SESSION['projectID'] ?? 0;

if ($projectID <= 0) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Kein gültiges Projekt ausgewählt.']);
    exit;
}

$minDate = $_GET['minDate'] ?? null;
$whereClauses = ['r_neu.tabelle_projekte_idTABELLE_Projekte = ?'];
$params = [$projectID];
$types = 'i';

if ($minDate) {
    $whereClauses[] = 'rb.Timestamp >= ?';
    $params[] = $minDate . ' 00:00:00';
    $types .= 's';
}

$where = implode(' AND ', $whereClauses);

$sql = "
    SELECT 
        rb.idtabelle_rb_aenderung,
        rb.`Neu/Bestand`,
        rb.`Neu/Bestand_copy1`,
        rb.Anzahl,
        rb.Anzahl_copy1,
        rb.Standort,
        rb.Standort_copy1,
        rb.Verwendung,
        rb.Verwendung_copy1,
        rb.Anschaffung,
        rb.Anschaffung_copy1,
        rb.Kurzbeschreibung,
        rb.Kurzbeschreibung_copy1,
        rb.user,
        rb.Timestamp,
        r_neu.Raumnr AS raumnr_neu,
        r_neu.tabelle_projekte_idTABELLE_Projekte,
        r_neu.Raumbezeichnung AS raumname_neu,
        e.Bezeichnung AS elementname_neu, 
        e.ElementID as elementnr_neu,
        rb.projektBudgetID_alt,
        rb.projektBudgetID_neu,
        rb.lieferdatum_alt,
        rb.lieferdatum_neu
    FROM tabelle_rb_aenderung rb
    LEFT JOIN tabelle_räume r_neu ON rb.raumID_neu = r_neu.idTABELLE_Räume
    LEFT JOIN tabelle_elemente e ON rb.elementID_neu = e.idTABELLE_Elemente 
    WHERE $where
    ORDER BY rb.Timestamp DESC
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error]);
    exit;
}

$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$data = [];
$fieldsToCheck = ['Neu/Bestand', 'Anzahl', 'Standort', 'Verwendung', 'Anschaffung', 'Kurzbeschreibung'];

while ($change = $result->fetch_assoc()) {
    $changedFields = [];
    foreach ($fieldsToCheck as $field) {
        $copyField = $field . '_copy1';
        $origVal = $change[$field] ?? null;
        $copyVal = $change[$copyField] ?? null;
        if ($origVal != $copyVal) {
            $changedFields[] = $field;
        }
    }
    $anzahlChanges = count($changedFields);

    // Store original values for render function
    $row = [
        'id' => $change['idtabelle_rb_aenderung'],
        'neuBestand' => $change['Neu/Bestand'] === null ? '' : ($change['Neu/Bestand'] ? 'Neu' : 'Bestand'),
        'anzahl' => $change['Anzahl'] ?? '',
        'anzahlChanges' => $anzahlChanges,
        'changedFields' => $changedFields,
        'standort' => $change['Standort'] === null ? '' : ($change['Standort'] ? 'Ja' : 'Nein'),
        'verwendung' => $change['Verwendung'] === null ? '' : ($change['Verwendung'] ? 'Ja' : 'Nein'),
        'anschaffung' => $change['Anschaffung'] ?? '',
        'kurzbeschreibung' => $change['Kurzbeschreibung'] ?? '',
        'user' => $change['user'] ?? '',
        'timestamp' => date('d.m.Y H:i', strtotime($change['Timestamp'])),
        'raum' => trim(($change['raumnr_neu'] ?? '') . ' ' . ($change['raumname_neu'] ?? '')),
        'elementnr' => $change['elementnr_neu'] ?? '',
        'elementname' => $change['elementname_neu'] ?? '',
        'budgetAlt' => $change['projektBudgetID_alt'] ?? '',
        'budgetNeu' => $change['projektBudgetID_neu'] ?? '',
        'lieferdatumAlt' => isset($change['lieferdatum_alt']) && $change['lieferdatum_alt'] ? date('d.m.Y', strtotime($change['lieferdatum_alt'])) : '',
        'lieferdatumNeu' => isset($change['lieferdatum_neu']) && $change['lieferdatum_neu'] ? date('d.m.Y', strtotime($change['lieferdatum_neu'])) : ''
    ];
    $data[] = $row;
}

ob_end_clean();
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$stmt->close();
$mysqli->close();
?>
