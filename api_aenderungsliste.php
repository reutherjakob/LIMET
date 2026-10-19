<?php
/**
 * Änderungsliste – JSON-Endpunkt
 *
 * Rekonstruiert den Stand von tabelle_räume_has_tabelle_elemente und
 * tabelle_projekt_varianten_kosten zu zwei Zeitpunkten (Start / Ende),
 * indem vom aktuellen Stand aus die Änderungsprotokolle rückwärts
 * "zurückgespielt" werden.
 *
 * Actions (POST):
 *   action=filters  -> Arbeitspakete für den Filter
 *   action=load     -> Änderungsliste (start, end, ap[], standort, ohneBestand)
 */
require_once '_utils/_utils.php';
require_once '_utils/_csrf.php';
require_csrf();

header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------------------
// Konfiguration
// ---------------------------------------------------------------------------

// Arbeitspaket-Nummer steht in tabelle_räume.`Anmerkung allgemein`.
// Eine eigene Spalte für die AP-Bezeichnung gibt es (noch) nicht -> '' = nicht vorhanden.
// Sobald es eine gibt, hier den Spaltennamen eintragen.
const AP_NR_COL = 'Anmerkung allgemein';
const AP_NAME_COL = '';

// Platzhalter im Filter für Räume ohne Arbeitspaket (muss zur Seite passen)
const AP_NONE = '__ohne__';
const DUMMY_ELEMENT_IDS = [2032, 2033, 2034, 2035];


// Zuordnung alt/neu in tabelle_rb_aenderung: Basisspalte = alt, *_copy1 = neu
const RB_FIELDS = [
    'anzahl' => ['Anzahl', 'Anzahl_copy1'],
    'standort' => ['Standort', 'Standort_copy1'],
    'neu' => ['Neu/Bestand', 'Neu/Bestand_copy1'],
    'variante' => ['tabelle_Varianten_idtabelle_Varianten', 'tabelle_Varianten_idtabelle_Varianten_copy1'],
    'raum' => ['raumID_alt', 'raumID_neu'],
    'element' => ['elementID_alt', 'elementID_neu'],
    'budget' => ['projektBudgetID_alt', 'projektBudgetID_neu'],
    'kommentar' => ['Kurzbeschreibung', 'Kurzbeschreibung_copy1'],
];

// ---------------------------------------------------------------------------
// Hilfsfunktionen
// ---------------------------------------------------------------------------

function respond(array $data, int $code = 200): never
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(string $msg, int $code = 400): never
{
    respond(['ok' => false, 'error' => $msg], $code);
}

function fetchAll(mysqli $db, string $sql, string $types = '', array $params = []): array
{
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        fail('SQL-Fehler: ' . $db->error, 500);
    }
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

/** SQL-Ausdruck für AP-Nummer bzw. -Bezeichnung (getrimmt, leer statt NULL). */
function apNrExpr(string $alias = ''): string
{
    $p = $alias !== '' ? $alias . '.' : '';
    return "TRIM(COALESCE({$p}`" . AP_NR_COL . "`, ''))";
}

function apNameExpr(string $alias = ''): string
{
    if (AP_NAME_COL === '') {
        return "''";
    }
    $p = $alias !== '' ? $alias . '.' : '';
    return "TRIM(COALESCE({$p}`" . AP_NAME_COL . "`, ''))";
}

function hasApColumns(mysqli $db): bool
{
    foreach (array_filter([AP_NR_COL, AP_NAME_COL]) as $col) {
        $res = $db->query("SHOW COLUMNS FROM `tabelle_räume` LIKE '" . $db->real_escape_string($col) . "'");
        if (!$res || $res->num_rows === 0) {
            return false;
        }
    }
    return true;
}

function parseDateTime(string $value): ?string
{
    foreach (['Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i:s', 'Y-m-d'] as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $value);
        if ($dt !== false) {
            return $dt->format('Y-m-d H:i:s');
        }
    }
    return null;
}

/** Kosten liegen als varchar vor – robust in float umwandeln. */
function parseKosten($value): ?float
{
    if ($value === null) {
        return null;
    }
    $v = str_replace([' ', '€'], '', trim((string)$value));
    if ($v === '') {
        return null;
    }
    if (str_contains($v, ',') && str_contains($v, '.')) {
        $v = str_replace('.', '', $v);
    }
    $v = str_replace(',', '.', $v);
    return is_numeric($v) ? (float)$v : null;
}

/** Kommentar (Kurzbeschreibung) für die Ausgabe säubern: <br> -> Leerzeichen, Tags raus, Whitespace zusammenziehen. */
function cleanKommentar(?string $value): string
{
    if ($value === null) {
        return '';
    }
    $v = preg_replace('#<br\s*/?>|</br>#i', ' ', $value);
    $v = html_entity_decode(strip_tags($v), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', $v));
}

function blankState(): array
{
    return ['exists' => false, 'anzahl' => null, 'standort' => null, 'neu' => null,
        'variante' => null, 'raum' => null, 'element' => null, 'budget' => null, 'kommentar' => null];
}

/** Macht einen Protokolleintrag auf einem Zustand rückgängig (neu -> alt). */
function undoEntry(?array $s, array $e): array
{
    $s ??= blankState();

    // Eintrag war ein INSERT: vor diesem Zeitpunkt gab es die Zeile nicht
    if ($e['anzahl_alt'] === null && $e['anzahl_neu'] !== null) {
        $s['exists'] = false;
        return $s;
    }
    $s['exists'] = true;

    foreach (['anzahl', 'standort', 'neu', 'variante', 'raum', 'element'] as $k) {
        if ($e[$k . '_alt'] !== null) {
            $s[$k] = (int)$e[$k . '_alt'];
        } elseif ($s[$k] === null && $e[$k . '_neu'] !== null) {
            // Lücke füllen (z. B. gelöschte Zeile, Feld nicht protokolliert)
            $s[$k] = (int)$e[$k . '_neu'];
        }
    }
    // Budget und Kommentar dürfen legitim NULL sein -> nur übernehmen, wenn protokolliert
    if ($e['budget_alt'] !== null || $e['budget_neu'] !== null) {
        $s['budget'] = $e['budget_alt'] === null ? null : (int)$e['budget_alt'];
    }
    if ($e['kommentar_alt'] !== null || $e['kommentar_neu'] !== null) {
        $s['kommentar'] = $e['kommentar_alt'];
    }
    return $s;
}

// ---------------------------------------------------------------------------
// Request prüfen
// ---------------------------------------------------------------------------

if (!isset($_SESSION['username'])) {
    fail('Nicht eingeloggt.', 401);
}
if (!isset($_SESSION['projectID']) || !is_numeric($_SESSION['projectID'])) {
    fail('Kein Projekt ausgewählt.', 400);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('Nur POST erlaubt.', 405);
}
if (!csrf_check($_POST['csrf'] ?? '')) {
    fail('Ungültiges CSRF-Token. Bitte Seite neu laden.', 403);
}

$projectId = (int)$_SESSION['projectID'];
$db = utils_connect_sql();
$hasAp = hasApColumns($db);
$action = getPostString('action');

// ---------------------------------------------------------------------------
// action=filters
// ---------------------------------------------------------------------------

if ($action === 'filters') {
    $aps = [];
    $ohneAp = 0;
    if ($hasAp) {
        foreach (fetchAll($db,
            "SELECT " . apNrExpr() . " AS nr, MAX(" . apNameExpr() . ") AS name, COUNT(*) AS raeume
               FROM `tabelle_räume`
              WHERE tabelle_projekte_idTABELLE_Projekte = ?
              GROUP BY nr",
            'i', [$projectId]) as $r) {
            if ($r['nr'] === '') {
                $ohneAp = (int)$r['raeume'];
            } else {
                $aps[] = ['nr' => $r['nr'], 'name' => $r['name'], 'raeume' => (int)$r['raeume']];
            }
        }
        usort($aps, fn($a, $b) => strnatcasecmp($a['nr'], $b['nr']));
    }

    // Budgets des Projekts (für den Filter "Budgets ausblenden")
    $budgetList = [];
    foreach (fetchAll($db,
        "SELECT idtabelle_projektbudgets AS id, Budgetnummer AS nr, Budgetname AS name, status
           FROM tabelle_projektbudgets
          WHERE tabelle_projekte_idTABELLE_Projekte = ?",
        'i', [$projectId]) as $r) {
        $budgetList[] = ['id' => (int)$r['id'], 'nr' => $r['nr'], 'name' => $r['name'], 'status' => (int)$r['status']];
    }
    usort($budgetList, fn($a, $b) => strnatcasecmp($a['nr'], $b['nr']));

    respond(['ok' => true, 'hasAp' => $hasAp, 'arbeitspakete' => $aps, 'ohneAp' => $ohneAp,
        'budgets' => $budgetList]);
}

if ($action !== 'load') {
    fail('Unbekannte Aktion.');
}

// ---------------------------------------------------------------------------
// action=load – Parameter
// ---------------------------------------------------------------------------

$start = parseDateTime(getPostString('start'));
$end = parseDateTime(getPostString('end'));
if ($start === null || $end === null) {
    fail('Bitte gültigen Start- und Endzeitpunkt angeben.');
}
if ($start >= $end) {
    fail('Der Startzeitpunkt muss vor dem Endzeitpunkt liegen.');
}
$onlyStandort = getPostString('standort', '1') === '1';
$hideBestand = getPostString('ohneBestand', '1') === '1';
// AP-Filter: apAll=1 -> kein Filter; sonst nur die gewählten (AP_NONE = Räume ohne AP)
$apAll = getPostString('apAll', '1') === '1';
$apFilter = array_values(array_filter(
    array_map(fn($v) => trim((string)$v), (array)($_POST['ap'] ?? [])),
    fn($v) => $v !== ''
));
if (!$apAll && !$apFilter) {
    fail('Bitte mindestens ein Arbeitspaket wählen.');
}

// ---------------------------------------------------------------------------
// Räume des Projekts
// ---------------------------------------------------------------------------

$apSelect = $hasAp
    ? ", " . apNrExpr() . " AS apNr, " . apNameExpr() . " AS apName"
    : ", '' AS apNr, '' AS apName";

$rooms = [];
foreach (fetchAll($db,
    "SELECT idTABELLE_Räume AS id, Raumnr, Raumbezeichnung $apSelect
       FROM `tabelle_räume`
      WHERE tabelle_projekte_idTABELLE_Projekte = ?",
    'i', [$projectId]) as $r) {
    $rooms[(int)$r['id']] = $r;
}

// ---------------------------------------------------------------------------
// Aktueller Stand Raum-Elemente
// ---------------------------------------------------------------------------

$state = [];
foreach (fetchAll($db,
    "SELECT rhe.id,
            rhe.`TABELLE_Räume_idTABELLE_Räume`                   AS raum,
            rhe.TABELLE_Elemente_idTABELLE_Elemente               AS element,
            rhe.`Neu/Bestand`                                     AS neu,
            rhe.Anzahl                                            AS anzahl,
            rhe.Standort                                          AS standort,
            rhe.tabelle_Varianten_idtabelle_Varianten             AS variante,
            rhe.tabelle_projektbudgets_idtabelle_projektbudgets   AS budget,
            rhe.Kurzbeschreibung                                  AS kommentar
       FROM `tabelle_räume_has_tabelle_elemente` rhe
      INNER JOIN `tabelle_räume` r ON r.idTABELLE_Räume = rhe.`TABELLE_Räume_idTABELLE_Räume`
      WHERE r.tabelle_projekte_idTABELLE_Projekte = ?",
    'i', [$projectId]) as $r) {
    $state[(int)$r['id']] = [
        'exists' => true,
        'anzahl' => (int)$r['anzahl'],
        'standort' => (int)$r['standort'],
        'neu' => (int)$r['neu'],
        'variante' => (int)$r['variante'],
        'raum' => (int)$r['raum'],
        'element' => (int)$r['element'],
        'budget' => $r['budget'] === null ? null : (int)$r['budget'],
        'kommentar' => $r['kommentar'],
    ];
}

// ---------------------------------------------------------------------------
// Protokoll ab Startzeitpunkt rückwärts abspielen
// ---------------------------------------------------------------------------

$logCols = [];
foreach (RB_FIELDS as $key => [$alt, $neu]) {
    $logCols[] = "a.`$alt` AS {$key}_alt";
    $logCols[] = "a.`$neu` AS {$key}_neu";
}
$projectRooms = "SELECT idTABELLE_Räume FROM `tabelle_räume` WHERE tabelle_projekte_idTABELLE_Projekte = ?";

$log = fetchAll($db,
    "SELECT a.idtabelle_rb_aenderung AS logId, a.id, a.Timestamp AS ts, " . implode(', ', $logCols) . "
       FROM tabelle_rb_aenderung a
      WHERE a.Timestamp > ?
        AND (   a.id IN (SELECT rhe.id
                           FROM `tabelle_räume_has_tabelle_elemente` rhe
                          INNER JOIN `tabelle_räume` r ON r.idTABELLE_Räume = rhe.`TABELLE_Räume_idTABELLE_Räume`
                          WHERE r.tabelle_projekte_idTABELLE_Projekte = ?)
             OR a.raumID_alt IN ($projectRooms)
             OR a.raumID_neu IN ($projectRooms))
      ORDER BY a.Timestamp DESC, a.idtabelle_rb_aenderung DESC",
    'siii', [$start, $projectId, $projectId, $projectId]);

$stateEnd = null;
foreach ($log as $e) {
    if ($stateEnd === null && $e['ts'] <= $end) {
        $stateEnd = $state;            // Snapshot = Stand zum Endzeitpunkt
    }
    $id = (int)$e['id'];
    $state[$id] = undoEntry($state[$id] ?? null, $e);
}
$stateEnd ??= $state;
$stateStart = $state;

// ---------------------------------------------------------------------------
// Kosten (Einzelpreise) zu Start und Ende
// ---------------------------------------------------------------------------

$cost = [];
$costGeraet = [];   // pro Element/Variante verwendete Geräte-Zeile (NULL bevorzugt)
foreach (fetchAll($db,
    "SELECT tabelle_elemente_idTABELLE_Elemente AS el,
            tabelle_Varianten_idtabelle_Varianten AS v,
            tabelle_geraete_idTABELLE_Geraete AS g,
            Kosten
       FROM tabelle_projekt_varianten_kosten
      WHERE tabelle_projekte_idTABELLE_Projekte = ?",
    'i', [$projectId]) as $r) {
    $key = $r['el'] . '|' . $r['v'];
    if (!array_key_exists($key, $cost) || $r['g'] === null) {
        $cost[$key] = parseKosten($r['Kosten']);
        $costGeraet[$key] = $r['g'];
    }
}

$costLog = fetchAll($db,
    "SELECT element, variante, geraet, kosten_alt, timestamp AS ts
       FROM tabelle_projekt_varianten_kosten_aenderung
      WHERE projekt = ? AND timestamp > ?
      ORDER BY timestamp DESC, idtabelle_projekt_varianten_kosten_aenderung DESC",
    'is', [$projectId, $start]);

$costEnd = null;
foreach ($costLog as $e) {
    if ($costEnd === null && $e['ts'] <= $end) {
        $costEnd = $cost;
    }
    $key = $e['element'] . '|' . $e['variante'];
    if (!array_key_exists($key, $costGeraet)) {
        $costGeraet[$key] = $e['geraet'];
    }
    if ((string)$costGeraet[$key] !== (string)$e['geraet']) {
        continue;   // gehört zu einer anderen Geräte-Zeile
    }
    $cost[$key] = parseKosten($e['kosten_alt']);
}
$costEnd ??= $cost;
$costStart = $cost;

// ---------------------------------------------------------------------------
// Aggregation je Raum / Element / Variante
// ---------------------------------------------------------------------------

$groups = [];
foreach (['Start' => $stateStart, 'End' => $stateEnd] as $phase => $states) {
    foreach ($states as $s) {
        if (!$s['exists'] || $s['raum'] === null || $s['element'] === null || $s['variante'] === null) {
            continue;
        }
        if (in_array($s['element'], DUMMY_ELEMENT_IDS, true)) {
            continue;   // Dummy-Element -> ignorieren
        }
        if (!isset($rooms[$s['raum']])) {
            continue;   // Raum gehört nicht (mehr) zum Projekt
        }
        if ($onlyStandort && $s['standort'] !== 1) {
            continue;
        }
        if ($hideBestand && $s['neu'] === 0) {
            continue;
        }
        $key = $s['raum'] . '|' . $s['element'] . '|' . $s['variante'];
        $groups[$key] ??= [
            'raum' => $s['raum'], 'element' => $s['element'], 'variante' => $s['variante'],
            'stkStart' => 0, 'stkEnd' => 0, 'budgetsStart' => [], 'budgetsEnd' => [],
            'kommentareStart' => [], 'kommentareEnd' => [],
            'inStart' => false, 'inEnd' => false,
        ];
        $groups[$key]['stk' . $phase] += (int)$s['anzahl'];
        $groups[$key]['in' . $phase] = true;
        if ($s['budget'] !== null) {
            $groups[$key]['budgets' . $phase][$s['budget']] = true;
        }
        $k = cleanKommentar($s['kommentar']);
        if ($k !== '') {
            $groups[$key]['kommentare' . $phase][$k] = true;
        }
    }
}

// Nachschlagetabellen
$elementIds = array_unique(array_map(fn($g) => (int)$g['element'], $groups));
$elements = [];
if ($elementIds) {
    $res = $db->query("SELECT idTABELLE_Elemente AS id, ElementID, Bezeichnung
                         FROM tabelle_elemente
                        WHERE idTABELLE_Elemente IN (" . implode(',', $elementIds) . ")");
    while ($r = $res->fetch_assoc()) {
        $elements[(int)$r['id']] = $r;
    }
}

$varianten = [];
$res = $db->query("SELECT idtabelle_Varianten AS id, Variante FROM tabelle_varianten");
while ($r = $res->fetch_assoc()) {
    $varianten[(int)$r['id']] = $r['Variante'];
}

$budgets = [];
foreach (fetchAll($db,
    "SELECT idtabelle_projektbudgets AS id, Budgetnummer, Budgetname
       FROM tabelle_projektbudgets
      WHERE tabelle_projekte_idTABELLE_Projekte = ?",
    'i', [$projectId]) as $r) {
    $budgets[(int)$r['id']] = $r;
}
$budgetLabel = function (array $ids) use ($budgets): string {
    $labels = [];
    foreach (array_keys($ids) as $id) {
        $labels[] = $budgets[$id]['Budgetnummer'] ?? ('#' . $id);
    }
    sort($labels, SORT_NATURAL);
    return implode(', ', $labels);
};

// ---------------------------------------------------------------------------
// Ausgabe
// ---------------------------------------------------------------------------

$rows = [];
foreach ($groups as $key => $g) {
    $room = $rooms[$g['raum']];
    if (!$apAll) {
        $apKey = (string)$room['apNr'] === '' ? AP_NONE : (string)$room['apNr'];
        if (!in_array($apKey, $apFilter, true)) {
            continue;
        }
    }
    $ckey = $g['element'] . '|' . $g['variante'];
    $epStart = $costStart[$ckey] ?? null;
    $epEnd = $costEnd[$ckey] ?? null;
    $ppStart = round($g['stkStart'] * ($epStart ?? 0), 2);
    $ppEnd = round($g['stkEnd'] * ($epEnd ?? 0), 2);
    $bStart = $budgetLabel($g['budgetsStart']);
    $bEnd = $budgetLabel($g['budgetsEnd']);

    if ($g['stkStart'] === 0 && $g['stkEnd'] > 0) {
        $art = 'Zusatz';
    } elseif ($g['stkStart'] > 0 && $g['stkEnd'] === 0) {
        $art = 'Entfall';
    } elseif ($g['stkStart'] !== $g['stkEnd']) {
        $art = 'Mengenänderung';
    } elseif ($bStart !== $bEnd) {
        $art = 'Budgetänderung';
    } elseif (abs($ppStart - $ppEnd) >= 0.005) {
        $art = 'Preisänderung';
    } else {
        $art = 'unverändert';
    }

    // Kommentar: Stand Ende; bei Entfall (Zeile existiert am Ende nicht mehr) Stand Start
    $kommentare = $g['inEnd'] ? $g['kommentareEnd'] : $g['kommentareStart'];

    $el = $elements[$g['element']] ?? ['ElementID' => '', 'Bezeichnung' => '#' . $g['element']];
    $rows[] = [
        'apNr' => (string)($room['apNr'] ?? ''),
        'apName' => (string)($room['apName'] ?? ''),
        'raumnr' => $room['Raumnr'],
        'raumbez' => $room['Raumbezeichnung'],
        'elementId' => $el['ElementID'],
        'element' => $el['Bezeichnung'],
        'variante' => $varianten[$g['variante']] ?? ('#' . $g['variante']),
        'art' => $art,
        'kommentar' => implode(' | ', array_keys($kommentare)),
        'budgetStart' => $bStart,
        'budgetEnd' => $bEnd,
        'budgetIdsStart' => array_map('intval', array_keys($g['budgetsStart'])),
        'budgetIdsEnd' => array_map('intval', array_keys($g['budgetsEnd'])),
        'stkStart' => $g['stkStart'],
        'stkEnd' => $g['stkEnd'],
        'epStart' => $epStart,
        'epEnd' => $epEnd,
        'ppStart' => $ppStart,
        'ppEnd' => $ppEnd,
        'ppDiff' => round($ppEnd - $ppStart, 2),
        'ohnePreis' => ($g['stkStart'] > 0 && $epStart === null) || ($g['stkEnd'] > 0 && $epEnd === null),
    ];
}

respond([
    'ok' => true,
    'hasAp' => $hasAp,
    'meta' => [
        'start' => $start,
        'end' => $end,
        'logEntries' => count($log),
        'costEntries' => count($costLog),
        'onlyStandort' => $onlyStandort,
        'hideBestand' => $hideBestand,
    ],
    'rows' => $rows,
]);