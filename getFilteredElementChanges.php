<?php
require_once 'utils/_utils.php';
check_login();
header('Content-Type: application/json');

$datum = $_POST['datum'] ?? '2024-01-01';

function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$mysqli = utils_connect_sql();
$projectID = $_SESSION['projectID'] ?? 0;

if ($projectID <= 0) {
    echo json_encode(['data' => [], 'error' => 'Kein gültiges Projekt ausgewählt.']);
    exit;
}


function formatFieldValue($field, $value)
{
    if ($value === null) return '–';
    $boolFields = ['Neu/Bestand', 'Standort', 'Verwendung'];
    if (in_array($field, $boolFields)) {
        if ($field === 'Neu/Bestand') return $value ? 'Neu' : 'Bestand';
        return $value ? 'Ja' : 'Nein';
    }
    return $value;
}

$stmt = $mysqli->prepare("
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
WHERE r_neu.tabelle_projekte_idTABELLE_Projekte = ? AND rb.Timestamp >= ? 
ORDER BY rb.Timestamp DESC
");

$stmt->bind_param("is", $projectID, $datum);
$stmt->execute();
$result = $stmt->get_result();

// Badge-Farben pro Feld
$badgeColors = [
    'Neu/Bestand' => 'bg-primary',
    'Anzahl' => 'bg-success',
    'Standort' => 'bg-warning text-dark',
    'Verwendung' => 'bg-info text-dark',
    'Anschaffung' => 'bg-secondary',
    'Kurzbeschreibung' => 'bg-secondary',
];

$data = [];
while ($change = $result->fetch_assoc()) {
    $changedFields = [];
    $fieldsToCheck = [
        'Neu/Bestand', 'Anzahl', 'Standort', 'Verwendung', 'Anschaffung', 'Kurzbeschreibung'
    ];

    foreach ($fieldsToCheck as $field) {
        $copyField = $field . '_copy1';
        $original = $change[$field] ?? null;
        $copy = $change[$copyField] ?? null;
        if ($original != $copy) {
            $changedFields[] = $field;
        }
    }

    $anzahlChanges = count($changedFields);

// ✅ ÄNDERUNG 1: Zeilen ohne Änderungen überspringen
    if ($anzahlChanges === 0) {
        continue;
    }

    // ✅ Option B: Löschungen erkennen und speziell darstellen
    $isDeleted = ($change['Anzahl'] === null && $change['Standort'] === null && $change['Verwendung'] === null);

    $badgesHtml = '';
    if ($isDeleted) {
        $badgesHtml = "<span class='badge bg-danger me-1'><i class='fas fa-trash-alt me-1'></i>Eintrag gelöscht</span>";
    } else {
        foreach ($changedFields as $field) {
            $color = $badgeColors[$field] ?? 'bg-secondary';
            $newVal = h(formatFieldValue($field, $change[$field]));
            $oldVal = h(formatFieldValue($field, $change[$field . '_copy1']));
            $badgesHtml .= "<span class='badge {$color} me-1'>"
                . h($field)
                . ": <s>{$oldVal}</s> → <strong>{$newVal}</strong>"
                . "</span>";
        }
    }

    $neuBestand = $change['Neu/Bestand'] === null ? '' : ($change['Neu/Bestand'] ? 'Neu' : 'Bestand');
    $standort = $change['Standort'] === null ? '' : ($change['Standort'] ? 'Ja' : 'Nein');
    $verwendung = $change['Verwendung'] === null ? '' : ($change['Verwendung'] ? 'Ja' : 'Nein');

    $data[] = [
        $neuBestand,
        h($change['Anzahl']),
        $badgesHtml,
        $standort,
        $verwendung,
        h($change['Anschaffung']),
        h($change['Kurzbeschreibung']),
        h($change['user']),
        date('d.m.Y H:i', strtotime($change['Timestamp'])),
        h($change['raumnr_neu']) . ' ' . h($change['raumname_neu']),
        h($change['elementnr_neu']),
        h($change['elementname_neu']),
        h($change['projektBudgetID_alt']),
        h($change['projektBudgetID_neu']),
        $change['lieferdatum_alt'] ? date('d.m.Y', strtotime($change['lieferdatum_alt'])) : '',
        $change['lieferdatum_neu'] ? date('d.m.Y', strtotime($change['lieferdatum_neu'])) : ''
    ];
}

echo json_encode(['data' => $data]);
$mysqli->close();
?>