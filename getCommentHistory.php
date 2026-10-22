<?php
// 26 FX – History aller elementbezogenen RB-Änderungen
require_once '_utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$roombookID = getPostInt('roombookID');

/* ------------------------------------------------------------------
 * Feld-Definitionen: Label => [Spalte alt, Spalte neu, Typ, Lookup-Key]
 * Typen: text | int | bool | neuBestand | date | id
 * ------------------------------------------------------------------ */
$fields = [
    'Kommentar'       => ['Kurzbeschreibung', 'Kurzbeschreibung_copy1', 'text'],
    'Anzahl'          => ['Anzahl', 'Anzahl_copy1', 'int'],
    'Neu/Bestand'     => ['Neu/Bestand', 'Neu/Bestand_copy1', 'neuBestand'],
    'Standort'        => ['Standort', 'Standort_copy1', 'bool'],
    'Verwendung'      => ['Verwendung', 'Verwendung_copy1', 'bool'],
    'Variante'        => ['tabelle_Varianten_idtabelle_Varianten', 'tabelle_Varianten_idtabelle_Varianten_copy1', 'id', 'variante'],
    'Los intern'      => ['tabelle_Lose_Intern_idtabelle_Lose_Intern', 'tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1', 'id'],
    'Los extern'      => ['tabelle_Lose_Extern_idtabelle_Lose_Extern', 'tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1', 'id', 'losExtern'],
    'Auftraggeber HG' => ['idtabelle_auftraggeber_GHG', 'idtabelle_auftraggeber_GHG_copy1', 'id'],
    'Auftraggeber UG' => ['idtabelle_auftraggeberg_GUG', 'idtabelle_auftraggeberg_GUG_copy1', 'id'],
    'Gewerk'          => ['idTABELLE_Auftraggeber_Gewerke', 'idTABELLE_Auftraggeber_Gewerke_copy1', 'id'],
    'Raum'            => ['raumID_alt', 'raumID_neu', 'id', 'raum'],
    'Element'         => ['elementID_alt', 'elementID_neu', 'id', 'element'],
    'Budget'          => ['projektBudgetID_alt', 'projektBudgetID_neu', 'id', 'budget'],
    'Lieferdatum'     => ['lieferdatum_alt', 'lieferdatum_neu', 'date'],
    'Status'          => ['status_alt', 'status_neu', 'int'],
];

/* ------------------------------------------------------------------
 * Optionale Klartext-Auflösung von IDs.
 * Tabellen-/Spaltennamen ggf. an euer Schema anpassen – schlägt eine
 * Abfrage fehl, wird einfach die ID angezeigt.
 * ------------------------------------------------------------------ */
$lookupSql = [
    'variante'  => "SELECT idtabelle_Varianten AS id, Variante AS txt FROM tabelle_varianten",
    'losExtern' => "SELECT idtabelle_Lose_Extern AS id, CONCAT_WS(' - ', LosNr_Extern, LosBezeichnung_Extern) AS txt FROM tabelle_lose_extern",
    'raum'      => "SELECT idTABELLE_Räume AS id, CONCAT_WS(' - ', Raumnr, Raumbezeichnung) AS txt FROM tabelle_räume",
    'element'   => "SELECT idTABELLE_Elemente AS id, CONCAT_WS(' - ', ElementID, Bezeichnung) AS txt FROM tabelle_elemente",
    'budget'    => "SELECT idtabelle_projektbudgets AS id, CONCAT_WS(' - ', Budgetnummer, Budgetname) AS txt FROM tabelle_projektbudgets",
];

function isChanged($a, $b): bool
{
    if ($a === null && $b === null) return false;
    if ($a === null || $b === null) return true;
    return (string)$a !== (string)$b;
}

function loadLookup(mysqli $mysqli, string $sql, array $ids): array
{
    $ids = array_values(array_unique(array_filter($ids, fn($v) => $v !== null)));
    if (!$ids) return [];
    $map = [];
    try {
        $in = implode(',', array_map('intval', $ids));
        $res = $mysqli->query("SELECT * FROM ($sql) x WHERE x.id IN ($in)");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $map[(int)$r['id']] = $r['txt'];
            }
            $res->free();
        }
    } catch (Throwable $e) {
        // Lookup nicht verfügbar -> Fallback auf ID
    }
    return $map;
}

function formatValue($value, string $type, ?array $lookup = null): string
{
    if ($value === null || $value === '') {
        return "<span class='text-muted fst-italic'>leer</span>";
    }
    switch ($type) {
        case 'bool':
            return (int)$value === 1 ? 'Ja' : 'Nein';
        case 'neuBestand':
            return (int)$value === 1 ? 'Neu' : 'Bestand';
        case 'date':
            $d = DateTime::createFromFormat('Y-m-d', $value);
            return $d ? $d->format('d.m.Y') : htmlspecialchars($value);
        case 'id':
            if ($lookup !== null && isset($lookup[(int)$value])) {
                return htmlspecialchars($lookup[(int)$value]);
            }
            return 'ID ' . htmlspecialchars((string)$value);
        default:
            return nl2br(htmlspecialchars((string)$value));
    }
}

/* ------------------------------------------------------------------
 * Daten laden
 * ------------------------------------------------------------------ */
$stmt = $mysqli->prepare("SELECT * FROM tabelle_rb_aenderung WHERE id = ? ORDER BY Timestamp DESC, idtabelle_rb_aenderung DESC");
$stmt->bind_param("i", $roombookID);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Nur geänderte Felder sammeln
$changes = [];
$lookupIds = [];
foreach ($rows as $row) {
    foreach ($fields as $label => $def) {
        [$colOld, $colNew, $type] = $def;
        $old = $row[$colOld] ?? null;
        $new = $row[$colNew] ?? null;
        if (!isChanged($old, $new)) continue;

        $changes[] = [
            'ts'     => $row['Timestamp'],
            'user'   => $row['user'],
            'label'  => $label,
            'old'    => $old,
            'new'    => $new,
            'def'    => $def,
            'vermerk'=> $row['vermerk_ID'] ?? null,
        ];
        if (isset($def[3])) {
            $lookupIds[$def[3]][] = $old;
            $lookupIds[$def[3]][] = $new;
        }
    }
}

// IDs in Klartext auflösen (nur benötigte)
$lookups = [];
foreach ($lookupIds as $key => $ids) {
    if (isset($lookupSql[$key])) {
        $lookups[$key] = loadLookup($mysqli, $lookupSql[$key], $ids);
    }
}
$mysqli->close();

/* ------------------------------------------------------------------
 * Ausgabe
 * ------------------------------------------------------------------ */
echo "<table class='table table-striped table-bordered table-sm table-hover align-middle border border-light border-5' id='historyTable'>
    <thead class='table-light'><tr>
        <th>Datum</th>
        <th>User</th>
        <th>Feld</th>
        <th>Alt</th>
        <th>Neu</th>
        <th><div class='d-flex justify-content-center' data-bs-toggle='tooltip' title='Vermerk'><i class='far fa-sticky-note'></i></div></th>
    </tr></thead>
    <tbody>";

foreach ($changes as $c) {
    $lookup = isset($c['def'][3]) ? ($lookups[$c['def'][3]] ?? null) : null;
    $type   = $c['def'][2];
    $tsSort = htmlspecialchars($c['ts'] ?? '');
    $tsView = $c['ts'] ? date('d.m.Y H:i', strtotime($c['ts'])) : '';

    echo "<tr>";
    echo "<td class='text-nowrap' data-order='{$tsSort}'>" . htmlspecialchars($tsView) . "</td>";
    echo "<td>" . htmlspecialchars($c['user'] ?? '') . "</td>";
    echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($c['label']) . "</span></td>";
    echo "<td class='text-danger'>" . formatValue($c['old'], $type, $lookup) . "</td>";
    echo "<td class='text-success'>" . formatValue($c['new'], $type, $lookup) . "</td>";
    echo "<td class='text-center'>" . ($c['vermerk'] ? "<span class='badge bg-info text-dark'>" . (int)$c['vermerk'] . "</span>" : '') . "</td>";
    echo "</tr>";
}

if (!$changes) {
    echo "<tr><td colspan='6' class='text-center text-muted'>Keine Änderungen vorhanden</td></tr>";
}

echo "</tbody></table>";