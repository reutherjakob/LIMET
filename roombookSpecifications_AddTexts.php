<?php
// 26 FX  // separate Front and Backend??
require_once '_utils/_utils.php';
init_page_serversides("", "x");
require_once '_utils/_csrf.php';

$updateMessage = '';
$updateType = 'info'; // Bootstrap-Alert-Farbe: info | success | warning | danger
$defaultAnmerkungFeld = '';
$defaultRaumbereich = '';
$defaultElementKeyword = '';
$defaultRaumbezeichnung = '';
$defaultTextToAdd = '';

$anmerkungFields = [
    'Anmerkung FunktionBO',
    'Anmerkung BauStatik',
    'Anmerkung Elektro',
    'Anmerkung Geräte',
    'Anmerkung HKLS'
];

$projectID = (int)$_SESSION['projectID'];

// ---------------------------------------------------------------------------
// Filter-Modus je Feld: 'text' (Freitext, LIKE) oder 'select' (Select2, exakte Auswahl)
// ---------------------------------------------------------------------------
$filterKeys = ['raumbereich', 'element', 'raumbezeichnung'];
$filterModes = [];
foreach ($filterKeys as $fk) {
    $filterModes[$fk] = (($_POST[$fk . 'Mode'] ?? 'text') === 'select') ? 'select' : 'text';
}

$selRaumbereiche = array_values(array_filter(
    array_map('strval', (array)($_POST['raumbereichSelect'] ?? [])),
    fn($v) => $v !== ''
));
$selElemente = array_values(array_filter(array_map('intval', (array)($_POST['elementSelect'] ?? []))));
$selRaeume = array_values(array_filter(array_map('intval', (array)($_POST['raumSelect'] ?? []))));

// Präfix (Datum + Kürzel)
$addPrefix = !empty($_POST['addPrefix']);
$prefixKuerzel = mb_substr(trim($_POST['prefixKuerzel'] ?? ($_SESSION['username'] ?? '')), 0, 20);

function sql_placeholders(int $n): string
{
    return implode(',', array_fill(0, $n, '?'));
}

/**
 * Für den Duplikat-Vergleich vereinheitlichen:
 * - HTML-Entities (&amp;, &quot;, &nbsp; …) dekodieren, falls Altdaten so gespeichert wurden
 * - alle Unicode-Leerzeichen (inkl. geschütztes Leerzeichen, Tab, \r\n) -> ein Leerzeichen
 * - unsichtbare Zeichen (Zero-Width, BOM, Soft-Hyphen) entfernen
 * - typografische Anführungszeichen / Gedankenstriche auf ASCII abbilden
 * - Groß/Klein ignorieren
 */
function normalize_for_compare(?string $s): string
{
    $s = (string)$s;
    if ($s === '') {
        return '';
    }
    // Ungültiges UTF-8 würde preg_replace mit /u scheitern lassen -> vorher reparieren
    if (!mb_check_encoding($s, 'UTF-8')) {
        $s = mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
    }
    $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $s = str_replace(
        ["\u{201C}", "\u{201D}", "\u{201E}", "\u{00AB}", "\u{00BB}", "\u{2018}", "\u{2019}", "\u{201A}", "\u{2013}", "\u{2014}", "\u{2212}"],
        ['"', '"', '"', '"', '"', "'", "'", "'", '-', '-', '-'],
        $s
    );
    $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{00AD}]/u', '', $s) ?? $s;
    // (*UCP): \s erkennt auch Unicode-Leerzeichen wie NBSP (U+00A0)
    $s = preg_replace('/(*UCP)\s+/u', ' ', $s) ?? $s;
    return mb_strtolower(trim($s), 'UTF-8');
}

/** Steht $text (normalisiert) bereits in $existing? Gleicher Text = Duplikat. */
function is_duplicate_text(?string $existing, ?string $text): bool
{
    $needle = normalize_for_compare($text);
    if ($needle === '') {
        return false;
    }
    $hay = normalize_for_compare($existing);
    return $hay === $needle || mb_strpos($hay, $needle, 0, 'UTF-8') !== false;
}

/** Hängt $text an $existing an – in neuer Zeile, falls bereits Text vorhanden ist. */
function append_anmerkung(string $existing, string $text): string
{
    $prefix = ($existing !== '' && substr($existing, -1) !== "\n") ? "\n" : '';
    return $existing . $prefix . $text;
}

// ---------------------------------------------------------------------------
// Abbrechen
// ---------------------------------------------------------------------------
if (isset($_POST['cancel_update'])) {
    $_SESSION['to_update'] = null;
}

// ---------------------------------------------------------------------------
// Vorschau / Suche (auch "Suche aktualisieren")
// ---------------------------------------------------------------------------
if (isset($_POST['preview_rooms'])) {
    $_SESSION['to_update'] = null;

    $anmerkungFieldInput = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;
    $raumbereichInput = trim($_POST['raumbereich'] ?? $defaultRaumbereich);
    $elementInput = trim($_POST['elementKeyword'] ?? $defaultElementKeyword);
    $raumbezeichnungInput = trim($_POST['raumbezeichnung'] ?? $defaultRaumbezeichnung);
    // Textarea liefert \r\n – einheitlich als \n speichern
    $textToAddInput = trim(str_replace("\r\n", "\n", $_POST['textToAdd'] ?? $defaultTextToAdd));

    if (!in_array($anmerkungFieldInput, $anmerkungFields, true)) {
        $updateMessage = 'Ungültiges Anmerkungsfeld ausgewählt.';
        $updateType = 'danger';
    } elseif ($textToAddInput === '') {
        $updateMessage = 'Bitte einen Text eingeben, der angehängt werden soll.';
        $updateType = 'warning';
    } else {
        $mysqli = utils_connect_sql();
        if ($mysqli->connect_errno) {
            $updateMessage = 'Fehler bei der Verbindung zur Datenbank: ' . $mysqli->connect_error;
            $updateType = 'danger';
        } else {
            $where = [
                'r.tabelle_projekte_idTABELLE_Projekte = ?',
                'r.Entfallen = 0'
            ];
            $types = 'i';
            $params = [$projectID];
            $activeFilters = []; // nur für die Rückmeldung bei 0 Treffern

            // Grundsatz: Ein leeres Feld / eine leere Auswahl filtert NICHT.

            // Raumbereich
            if ($filterModes['raumbereich'] === 'select') {
                if ($selRaumbereiche) {
                    $where[] = 'r.`Raumbereich Nutzer` IN (' . sql_placeholders(count($selRaumbereiche)) . ')';
                    $types .= str_repeat('s', count($selRaumbereiche));
                    array_push($params, ...$selRaumbereiche);
                    $activeFilters[] = 'Raumbereich ∈ [' . implode(', ', $selRaumbereiche) . ']';
                }
            } elseif ($raumbereichInput !== '') {
                $where[] = 'r.`Raumbereich Nutzer` LIKE ?';
                $types .= 's';
                $params[] = '%' . $raumbereichInput . '%';
                $activeFilters[] = 'Raumbereich enthält "' . $raumbereichInput . '"';
            }

            // Raumbezeichnung / Räume
            if ($filterModes['raumbezeichnung'] === 'select') {
                if ($selRaeume) {
                    $where[] = 'r.idTABELLE_Räume IN (' . sql_placeholders(count($selRaeume)) . ')';
                    $types .= str_repeat('i', count($selRaeume));
                    array_push($params, ...$selRaeume);
                    $activeFilters[] = count($selRaeume) . ' Raum/Räume ausgewählt';
                }
            } elseif ($raumbezeichnungInput !== '') {
                $where[] = 'r.Raumbezeichnung LIKE ?';
                $types .= 's';
                $params[] = '%' . $raumbezeichnungInput . '%';
                $activeFilters[] = 'Raumbezeichnung enthält "' . $raumbezeichnungInput . '"';
            }

            // Element
            if ($filterModes['element'] === 'select') {
                if ($selElemente) {
                    $where[] = 'e.idTABELLE_Elemente IN (' . sql_placeholders(count($selElemente)) . ')';
                    $types .= str_repeat('i', count($selElemente));
                    array_push($params, ...$selElemente);
                    $activeFilters[] = count($selElemente) . ' Element(e) ausgewählt';
                }
            } elseif ($elementInput !== '') {
                $where[] = '(e.Bezeichnung LIKE ? OR e.ElementID LIKE ?)';
                $types .= 'ss';
                $params[] = '%' . $elementInput . '%';
                $params[] = '%' . $elementInput . '%';
                $activeFilters[] = 'Element enthält "' . $elementInput . '"';
            }

            $query = "SELECT DISTINCT r.idTABELLE_Räume,
                             CONCAT(COALESCE(r.Raumnr, ''), ' - ', COALESCE(r.Raumbezeichnung, '')) AS Raumbezeichnung,
                             r.`Raumbereich Nutzer` AS Raumbereich,
                             e.Bezeichnung AS ElementName,
                             r.`$anmerkungFieldInput` AS AnmerkungText
                      FROM tabelle_räume r
                      LEFT JOIN tabelle_räume_has_tabelle_elemente re
                             ON r.idTABELLE_Räume = re.TABELLE_Räume_idTABELLE_Räume
                            AND (re.Anzahl IS NULL OR re.Anzahl > 0)
                      LEFT JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                      WHERE " . implode("\n AND ", $where) . "
                      ORDER BY r.idTABELLE_Räume";

            $stmt = $mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $rooms = [];
                while ($row = $result->fetch_assoc()) {
                    $id = (int)$row['idTABELLE_Räume'];
                    if (!isset($rooms[$id])) {
                        $existing = (string)($row['AnmerkungText'] ?? '');
                        $rooms[$id] = [
                            'Raumbezeichnung' => $row['Raumbezeichnung'],
                            'Raumbereich' => $row['Raumbereich'] ?? '',
                            'Elements' => [],
                            'AnmerkungText' => $existing,
                            // Text (ohne Datum/Kürzel) steht schon in der Anmerkung?
                            'Duplicate' => is_duplicate_text($existing, $textToAddInput),
                        ];
                    }
                    if ($row['ElementName']) {
                        $rooms[$id]['Elements'][] = $row['ElementName'];
                    }
                }
                $stmt->close();

                foreach ($rooms as &$r) {
                    $r['Elements'] = array_values(array_unique($r['Elements']));
                }
                unset($r);

                if (count($rooms) > 0) {
                    $finalText = ($addPrefix ? '[' . date('d.m.y') . ($prefixKuerzel !== '' ? ' ' . $prefixKuerzel : '') . '] ' : '')
                        . $textToAddInput;
                    $_SESSION['to_update'] = [
                        'rooms' => $rooms,
                        'anmerkungField' => $anmerkungFieldInput,
                        'textToAdd' => $finalText,
                        'rawText' => $textToAddInput,
                    ];
                } else {
                    $updateMessage = 'Keine passenden Räume gefunden. Aktive Filter: '
                        . ($activeFilters ? implode(' | ', $activeFilters) : 'keine')
                        . ' (Projekt-ID: ' . $projectID . ')';
                    $updateType = 'warning';
                }
            } else {
                $updateMessage = 'Fehler bei der Vorbereitung der Abfrage: ' . $mysqli->error;
                $updateType = 'danger';
            }
            $mysqli->close();
        }
    }
}

// ---------------------------------------------------------------------------
// Ausführen
// ---------------------------------------------------------------------------
if (isset($_POST['confirm_update'])) {
    if (!empty($_SESSION['to_update'])) {
        $data = $_SESSION['to_update'];
        $postedIDs = ($_POST['selectedRooms'] ?? '') !== '' ? explode(',', $_POST['selectedRooms']) : [];
        $roomIDs = array_values(array_intersect(
            array_map('intval', $postedIDs),
            array_map('intval', array_keys($data['rooms']))
        ));

        if (empty($roomIDs)) {
            $updateMessage = 'Bitte wählen Sie mindestens einen Raum aus.';
            $updateType = 'warning';
        } else {
            $field = $data['anmerkungField'];
            $text = $data['textToAdd'];

            if (!in_array($field, $anmerkungFields, true)) {
                $updateMessage = 'Ungültiges Anmerkungsfeld.';
                $updateType = 'danger';
            } else {
                $mysqli = utils_connect_sql();
                if ($mysqli->connect_errno) {
                    $updateMessage = 'Fehler bei der Verbindung zur Datenbank: ' . $mysqli->connect_error;
                    $updateType = 'danger';
                } else {
                    // Aktuelle Werte frisch aus der DB lesen (Vorschau könnte veraltet sein) -> Basis für Undo
                    $sel = $mysqli->prepare("SELECT idTABELLE_Räume, `$field` AS val FROM tabelle_räume
                                             WHERE tabelle_projekte_idTABELLE_Projekte = ?
                                               AND idTABELLE_Räume IN (" . sql_placeholders(count($roomIDs)) . ")");
                    $types = 'i' . str_repeat('i', count($roomIDs));
                    $sel->bind_param($types, $projectID, ...$roomIDs);
                    $sel->execute();
                    $res = $sel->get_result();
                    $current = [];
                    while ($row = $res->fetch_assoc()) {
                        $current[(int)$row['idTABELLE_Räume']] = (string)($row['val'] ?? '');
                    }
                    $sel->close();

                    $upd = $mysqli->prepare("UPDATE tabelle_räume SET `$field` = ? WHERE idTABELLE_Räume = ?");
                    if ($upd) {
                        $undo = [];
                        $dupSkipped = 0;
                        $mysqli->begin_transaction();
                        try {
                            foreach ($current as $roomId => $old) {
                                // Exakt derselbe Anhang (inkl. Datum/Kürzel) steht schon drin -> nicht doppelt anhängen
                                if (is_duplicate_text($old, $text)) {
                                    $dupSkipped++;
                                    continue;
                                }
                                $new = append_anmerkung($old, $text);
                                $upd->bind_param('si', $new, $roomId);
                                $upd->execute();
                                $undo[$roomId] = ['old' => $old, 'new' => $new];
                            }
                            $mysqli->commit();

                            if ($undo) {
                                $_SESSION['last_update'] = [
                                    'projectID' => $projectID,
                                    'field' => $field,
                                    'text' => $text,
                                    'rooms' => $undo,
                                    'time' => date('d.m.Y H:i'),
                                ];
                            }
                            $updateMessage = 'Die Anmerkung wurde für ' . count($undo) . ' Räume aktualisiert.'
                                . ($dupSkipped ? " $dupSkipped Räume übersprungen, da der Text dort bereits steht." : '');
                            $updateType = $dupSkipped ? 'warning' : 'success';
                            $clearTextField = true;
                            $_SESSION['to_update'] = null;
                        } catch (Throwable $e) {
                            $mysqli->rollback();
                            $updateMessage = 'Fehler beim Update, es wurde nichts geändert: ' . $e->getMessage();
                            $updateType = 'danger';
                        }
                        $upd->close();
                    } else {
                        $updateMessage = 'Fehler bei der Vorbereitung des Update-Statements: ' . $mysqli->error;
                        $updateType = 'danger';
                    }
                    $mysqli->close();
                }
            }
        }
    } else {
        $updateMessage = 'Keine Räume für das Update zum Bestätigen gefunden.';
        $updateType = 'warning';
    }
}

// ---------------------------------------------------------------------------
// Rückgängig (letzte Ausführung)
// ---------------------------------------------------------------------------
if (isset($_POST['undo_last'])) {
    $last = $_SESSION['last_update'] ?? null;
    if (!$last || (int)$last['projectID'] !== $projectID || !in_array($last['field'], $anmerkungFields, true)) {
        $updateMessage = 'Keine Änderung zum Rückgängigmachen vorhanden.';
        $updateType = 'warning';
    } else {
        $field = $last['field'];
        $mysqli = utils_connect_sql();
        if ($mysqli->connect_errno) {
            $updateMessage = 'Fehler bei der Verbindung zur Datenbank: ' . $mysqli->connect_error;
            $updateType = 'danger';
        } else {
            $sel = $mysqli->prepare("SELECT `$field` AS val FROM tabelle_räume WHERE idTABELLE_Räume = ? FOR UPDATE");
            $upd = $mysqli->prepare("UPDATE tabelle_räume SET `$field` = ? WHERE idTABELLE_Räume = ?");
            $restored = 0;
            $skipped = 0;
            $mysqli->begin_transaction();
            try {
                foreach ($last['rooms'] as $roomId => $vals) {
                    $roomId = (int)$roomId;
                    $sel->bind_param('i', $roomId);
                    $sel->execute();
                    $row = $sel->get_result()->fetch_assoc();
                    $now = (string)($row['val'] ?? '');
                    // Nur zurücksetzen, wenn seitdem niemand das Feld geändert hat
                    if ($row !== null && $now === $vals['new']) {
                        $old = $vals['old'];
                        $upd->bind_param('si', $old, $roomId);
                        $upd->execute();
                        $restored++;
                    } else {
                        $skipped++;
                    }
                }
                $mysqli->commit();
                unset($_SESSION['last_update']);
                $updateMessage = "Rückgängig: $restored Räume zurückgesetzt."
                    . ($skipped ? " $skipped Räume übersprungen, da sie zwischenzeitlich geändert wurden." : '');
                $updateType = $skipped ? 'warning' : 'success';
            } catch (Throwable $e) {
                $mysqli->rollback();
                $updateMessage = 'Fehler beim Rückgängigmachen, es wurde nichts geändert: ' . $e->getMessage();
                $updateType = 'danger';
            }
            $sel->close();
            $upd->close();
            $mysqli->close();
        }
    }
}

// ---------------------------------------------------------------------------
// Optionen für die Dropdowns (alle Raumbereiche / Elemente / Räume des Projekts)
// ---------------------------------------------------------------------------
$optRaumbereiche = [];
$optElemente = [];
$optRaeume = [];

$mysqli = utils_connect_sql();
if (!$mysqli->connect_errno) {
    $stmt = $mysqli->prepare("SELECT DISTINCT `Raumbereich Nutzer` AS rb
                              FROM tabelle_räume
                              WHERE tabelle_projekte_idTABELLE_Projekte = ?
                                AND Entfallen = 0
                                AND `Raumbereich Nutzer` IS NOT NULL
                                AND `Raumbereich Nutzer` <> ''
                              ORDER BY rb");
    if ($stmt) {
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $optRaumbereiche[$row['rb']] = $row['rb'];
        }
        $stmt->close();
    }

    $stmt = $mysqli->prepare("SELECT DISTINCT e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung
                              FROM tabelle_elemente e
                              JOIN tabelle_räume_has_tabelle_elemente re ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                              JOIN tabelle_räume r ON r.idTABELLE_Räume = re.TABELLE_Räume_idTABELLE_Räume
                              WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                                AND r.Entfallen = 0
                                AND (re.Anzahl IS NULL OR re.Anzahl > 0)
                              ORDER BY e.ElementID");
    if ($stmt) {
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $optElemente[$row['idTABELLE_Elemente']] = $row['ElementID'] . ' - ' . $row['Bezeichnung'];
        }
        $stmt->close();
    }

    $stmt = $mysqli->prepare("SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung
                              FROM tabelle_räume
                              WHERE tabelle_projekte_idTABELLE_Projekte = ?
                                AND Entfallen = 0
                              ORDER BY Raumnr");
    if ($stmt) {
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $optRaeume[$row['idTABELLE_Räume']] = $row['Raumnr'] . ' - ' . $row['Raumbezeichnung'];
        }
        $stmt->close();
    }
    $mysqli->close();
}

// Für repopulation
$anmerkungFieldVal = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;
$raumbereichVal = $_POST['raumbereich'] ?? $defaultRaumbereich;
$elementKeywordVal = $_POST['elementKeyword'] ?? $defaultElementKeyword;
$raumbezeichnungVal = $_POST['raumbezeichnung'] ?? $defaultRaumbezeichnung;
$textToAddVal = $_POST['textToAdd'] ?? $defaultTextToAdd;
// Nach erfolgreichem Ausführen / Rückgängig: Textfeld leeren
if ($updateType === 'success' || !empty($clearTextField)) {
    $textToAddVal = '';
}

$toUpdate = $_SESSION['to_update'] ?? null;
$inConfirm = !empty($toUpdate['rooms']);
$lastUpdate = $_SESSION['last_update'] ?? null;
$hasUndo = $lastUpdate && (int)$lastUpdate['projectID'] === $projectID;

// Konfiguration der umschaltbaren Filterfelder (fürs Rendern)
$filterConfig = [
    'raumbereich' => [
        'label' => 'Raumbereich',
        'textHint' => 'Schlüsselwort/Teilwort',
        'textName' => 'raumbereich',
        'textVal' => $raumbereichVal,
        'selectName' => 'raumbereichSelect',
        'placeholder' => 'Raumbereich(e) wählen',
        'options' => $optRaumbereiche,
        'selected' => $selRaumbereiche,
    ],
    'element' => [
        'label' => 'Element',
        'textHint' => 'Schlüsselwort/Teilwort, Name oder Nummer',
        'textName' => 'elementKeyword',
        'textVal' => $elementKeywordVal,
        'selectName' => 'elementSelect',
        'placeholder' => 'Element(e) wählen',
        'options' => $optElemente,
        'selected' => $selElemente,
    ],
    'raumbezeichnung' => [
        'label' => 'Raumbezeichnung',
        'textHint' => 'Schlüsselwort/Teilwort',
        'textName' => 'raumbezeichnung',
        'textVal' => $raumbezeichnungVal,
        'selectName' => 'raumSelect',
        'placeholder' => 'Raum/Räume wählen',
        'options' => $optRaeume,
        'selected' => $selRaeume,
    ],
];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <title>RB-Bauangaben Anmerkungen Freitextsuche</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf" content="<?= h(csrf_token()) ?>">

    <link rel="stylesheet" href="_utils/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logos/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/de.js"></script>
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-3">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Anmerkung bei Raumsuche via Freitext eintragen.</strong>
                    <i class="fa fa-info-circle float-end" data-bs-toggle="tooltip" data-bs-placement="top"
                       title="Suche mit beliebigen Textteilen für Raumbereich, Element oder Raumbezeichnung – oder per Umschalter (Listen-Symbol) gezielt aus allen Einträgen des Projekts auswählen. Leere Felder schränken nicht ein. Anschließend Räume an-/abwählen und bestätigen; der Text wird angehängt."></i>
                </div>
                <div class="card-body">
                    <?php if ($updateMessage !== ''): ?>
                        <div class="alert alert-<?= $updateType ?>"><?= htmlspecialchars($updateMessage) ?></div>
                    <?php endif; ?>

                    <?php if ($hasUndo): ?>
                        <form method="post" class="alert alert-light border d-flex justify-content-between align-items-center py-2"
                              onsubmit="return confirm('Letzte Änderung wirklich rückgängig machen?');">
                            <small>
                                Letzte Änderung: <strong><?= count($lastUpdate['rooms']) ?> Räume</strong>,
                                <?= htmlspecialchars($lastUpdate['field']) ?>, <?= htmlspecialchars($lastUpdate['time']) ?>
                            </small>
                            <button type="submit" name="undo_last" class="btn btn-sm btn-outline-warning ms-2 text-nowrap">
                                <i class="fas fa-undo"></i> Rückgängig
                            </button>
                        </form>
                    <?php endif; ?>

                    <form method="post" autocomplete="off" id="confirmUpdateForm">
                        <div class="mb-3">
                            <label for="anmerkungFeld" class="form-label sr-only">Anmerkungsfeld</label>
                            <select class="form-select track-dirty" id="anmerkungFeld" name="anmerkungFeld" required>
                                <?php foreach ($anmerkungFields as $field): ?>
                                    <option value="<?= htmlspecialchars($field) ?>" <?= ($anmerkungFieldVal === $field) ? 'selected' : '' ?>><?= htmlspecialchars($field) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php foreach ($filterConfig as $key => $cfg):
                            $isSelect = $filterModes[$key] === 'select'; ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <label for="<?= $key ?>_<?= $isSelect ? 'select' : 'text' ?>" class="form-label mb-0">
                                        <?= htmlspecialchars($cfg['label']) ?>
                                        <small class="text-muted mode-hint" data-field="<?= $key ?>">
                                            (<?= $isSelect ? 'Auswahl' : htmlspecialchars($cfg['textHint']) ?>)
                                        </small>
                                    </label>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" id="btnAll_<?= $key ?>"
                                                class="btn btn-sm btn-outline-secondary btn-select-all <?= $isSelect ? '' : 'd-none' ?>"
                                                data-field="<?= $key ?>"
                                                title="Alle Einträge auswählen">
                                            Alle
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary mode-toggle"
                                                data-field="<?= $key ?>"
                                                data-texthint="<?= htmlspecialchars($cfg['textHint']) ?>"
                                                title="Zwischen Freitext und Auswahlliste wechseln">
                                            <i class="fas <?= $isSelect ? 'fa-keyboard' : 'fa-list' ?>"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="<?= $key ?>Mode" id="<?= $key ?>Mode"
                                       value="<?= $isSelect ? 'select' : 'text' ?>">

                                <div class="mode-text <?= $isSelect ? 'd-none' : '' ?>" data-field="<?= $key ?>">
                                    <input type="text" class="form-control track-dirty" id="<?= $key ?>_text"
                                           name="<?= $cfg['textName'] ?>"
                                           value="<?= htmlspecialchars($cfg['textVal']) ?>">
                                </div>

                                <div class="mode-select <?= $isSelect ? '' : 'd-none' ?>" data-field="<?= $key ?>">
                                    <select class="form-select filter-select track-dirty" id="<?= $key ?>_select"
                                            name="<?= $cfg['selectName'] ?>[]" multiple
                                            data-placeholder="<?= htmlspecialchars($cfg['placeholder']) ?>">
                                        <?php foreach ($cfg['options'] as $val => $lbl): ?>
                                            <option value="<?= htmlspecialchars((string)$val) ?>"
                                                <?= in_array($val, $cfg['selected']) ? 'selected' : '' ?>><?= htmlspecialchars($lbl) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="mb-3">
                            <label for="textToAdd" class="form-label">Text zur bestehenden Anmerkung hinzufügen</label>
                            <textarea class="form-control track-dirty" id="textToAdd" name="textToAdd"
                                      rows="3"><?= htmlspecialchars($textToAddVal) ?></textarea>
                            <div class="form-text">Wird in einer neuen Zeile an die bestehende Anmerkung angehängt.</div>

                            <div class="input-group input-group-sm mt-2">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0 track-dirty" type="checkbox" name="addPrefix" id="addPrefix"
                                           value="1" <?= $addPrefix ? 'checked' : '' ?>
                                           title="Datum + Kürzel voranstellen">
                                </div>
                                <label class="input-group-text" for="addPrefix">[<?= date('d.m.y') ?></label>
                                <input type="text" class="form-control track-dirty" name="prefixKuerzel" id="prefixKuerzel"
                                       maxlength="20" placeholder="Kürzel"
                                       value="<?= htmlspecialchars($prefixKuerzel) ?>">
                                <span class="input-group-text">]</span>
                            </div>
                            <div class="form-text">Optional: Datum + Kürzel vor den Text setzen, z.&nbsp;B. „[<?= date('d.m.y') ?> <?= htmlspecialchars($prefixKuerzel ?: 'FX') ?>] …“</div>
                        </div>

                        <?php if ($inConfirm): ?>
                            <div class="alert alert-warning py-2">
                                Räume rechts prüfen und an-/abwählen, dann ausführen.
                            </div>
                            <div class="alert alert-secondary py-2 d-none" id="dirtyHint">
                                <i class="fas fa-exclamation-triangle"></i>
                                Suchkriterien oder Text wurden geändert – bitte zuerst <strong>Suche aktualisieren</strong>.
                            </div>
                            <!-- Erster Submit-Button = Enter-Taste -> sicherer Default -->
                            <button type="submit" name="preview_rooms" class="btn btn-outline-primary mb-2 w-100">
                                <i class="fas fa-sync-alt"></i> Suche aktualisieren
                            </button>
                            <div class="d-flex">
                                <button type="submit" name="confirm_update" id="confirmBtn" class="btn btn-danger flex-grow-1">
                                    Änderung ausführen
                                </button>
                                <button type="submit" name="cancel_update" class="btn btn-secondary ms-2">Abbrechen</button>
                            </div>
                            <small class="text-muted d-block mt-1" id="hiddenSelectedHint"></small>
                            <input type="hidden" name="selectedRooms" id="selectedRoomsInput">
                        <?php else: ?>
                            <button type="submit" name="preview_rooms" class="btn btn-primary">Räume prüfen und
                                anzeigen
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="card h-100">
                <div class="card-header">
                    Übersicht der betroffenen Räume und bestehende Anmerkungen
                    <?php if ($inConfirm): ?>
                        <span class="badge bg-primary ms-2"><?= htmlspecialchars($toUpdate['anmerkungField']) ?></span>
                    <?php endif; ?>
                    <div class="float-end border-light">
                        <label for="toggle-all-rooms" title="Wirkt nur auf die aktuell sichtbaren (gefilterten) Zeilen">Alle sichtbaren Räume</label>
                        <input type="checkbox" id="toggle-all-rooms" <?= $inConfirm ? '' : 'disabled' ?>>
                        <i class="fa fa-info-circle ms-4" data-bs-toggle="tooltip" data-bs-placement="top"
                           title="Grün = Text, der angehängt wird. Räume, in denen der Text bereits steht, sind markiert und standardmäßig abgewählt. Über das Suchfeld der Tabelle kann man weiter eingrenzen und dann alle sichtbaren Räume an-/abwählen."></i>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($inConfirm): ?>
                        <table id="roomsTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th><i class="fas fa-edit"></i></th>
                                <th>Raumbezeichnung</th>
                                <th>Raumbereich</th>
                                <th>Element(e)</th>
                                <th>Bestehende Anmerkungen</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($toUpdate['rooms'] as $roomId => $roomData):
                                $dup = !empty($roomData['Duplicate']); ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="update-room-checkbox"
                                               data-roomid="<?= (int)$roomId ?>" <?= $dup ? '' : 'checked' ?>>
                                    </td>
                                    <td><?= htmlspecialchars($roomData['Raumbezeichnung'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($roomData['Raumbereich'] ?? '') ?></td>
                                    <td>
                                        <?php foreach ($roomData['Elements'] as $el): ?>
                                            <span class="badge bg-secondary me-1 mb-1"><?= htmlspecialchars($el) ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php if ($dup): ?>
                                            <span class="badge bg-warning text-dark mb-1">
                                                <i class="fas fa-clone"></i> Text bereits vorhanden
                                            </span>
                                        <?php endif; ?>
                                        <?php if (($roomData['AnmerkungText'] ?? '') !== ''): ?>
                                            <pre class="mb-1" style="white-space: pre-wrap; font-family: inherit;"><?= htmlspecialchars($roomData['AnmerkungText']) ?></pre>
                                        <?php endif; ?>
                                        <div class="preview-add text-success border-start border-success border-3 ps-2 <?= $dup ? 'd-none' : '' ?>"
                                             style="white-space: pre-wrap;"><i class="fas fa-plus small"></i> <?= htmlspecialchars($toUpdate['textToAdd']) ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Keine Räume zum Anzeigen. Bitte führen Sie eine Suche durch.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted" style="font-size: 0.8rem;">
                    Die bestehenden Anmerkungen bleiben unverändert. Der Anmerkungstext wird angehängt.
                    Bitte prüfen Sie diese vor der Ausführung der Änderung.
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    const IS_POSTBACK = <?= $_SERVER['REQUEST_METHOD'] === 'POST' ? 'true' : 'false' ?>;
    const IN_CONFIRM = <?= $inConfirm ? 'true' : 'false' ?>;
    const LS_PREFIX = 'anmFreitext.mode.';
    let isDirty = false;
    let roomsTable = null;

    function lsSet(k, v) { try { localStorage.setItem(k, v); } catch (e) {} }
    function lsGet(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }

    function setFilterMode(field, mode, interactive) {
        const isSelect = mode === 'select';
        $('#' + field + 'Mode').val(mode);
        $('.mode-text[data-field="' + field + '"]').toggleClass('d-none', isSelect);
        $('.mode-select[data-field="' + field + '"]').toggleClass('d-none', !isSelect);
        $('#btnAll_' + field).toggleClass('d-none', !isSelect);

        const $btn = $('.mode-toggle[data-field="' + field + '"]');
        $btn.find('i').toggleClass('fa-list', !isSelect).toggleClass('fa-keyboard', isSelect);
        $('.mode-hint[data-field="' + field + '"]').text('(' + (isSelect ? 'Auswahl' : $btn.data('texthint')) + ')');

        if (interactive) {
            lsSet(LS_PREFIX + field, mode);
            markDirty();
            if (isSelect) {
                $('#' + field + '_select').select2('open');
            } else {
                $('#' + field + '_text').trigger('focus');
            }
        }
    }

    // --- Raumauswahl -------------------------------------------------------
    function allCheckboxes() {
        return roomsTable ? $(roomsTable.rows().nodes()).find('.update-room-checkbox') : $();
    }

    function visibleCheckboxes() {
        return roomsTable ? $(roomsTable.rows({search: 'applied'}).nodes()).find('.update-room-checkbox') : $();
    }

    function updateSelectionUI() {
        if (!IN_CONFIRM) return;
        const $all = allCheckboxes();
        const $vis = visibleCheckboxes();
        const total = $all.filter(':checked').length;
        const visChecked = $vis.filter(':checked').length;
        const hiddenChecked = total - visChecked;

        // Vorschau (grüner Text) je Zeile
        $all.each(function () {
            $(this).closest('tr').find('.preview-add').toggleClass('d-none', !this.checked);
        });

        // Button-Text + Status
        $('#confirmBtn')
            .text('Änderung für ' + total + (total === 1 ? ' Raum' : ' Räume') + ' ausführen')
            .prop('disabled', total === 0 || isDirty);

        $('#hiddenSelectedHint').text(hiddenChecked > 0
            ? 'Davon ' + hiddenChecked + ' durch die Tabellensuche ausgeblendet.'
            : '');

        // "Alle sichtbaren"-Checkbox
        const toggle = document.getElementById('toggle-all-rooms');
        toggle.checked = $vis.length > 0 && visChecked === $vis.length;
        toggle.indeterminate = visChecked > 0 && visChecked < $vis.length;
    }

    function markDirty() {
        if (!IN_CONFIRM || isDirty) return;
        isDirty = true;
        $('#dirtyHint').removeClass('d-none');
        updateSelectionUI();
    }

    $(document).ready(function () {
        $('.filter-select').each(function () {
            $(this).select2({
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                closeOnSelect: false,
                language: 'de'
            });
        });

        // Gemerkte Modus-Wahl beim ersten Aufruf anwenden (nach POST gilt der gesendete Zustand)
        if (!IS_POSTBACK) {
            $('.mode-toggle').each(function () {
                const field = $(this).data('field');
                const stored = lsGet(LS_PREFIX + field);
                if (stored === 'select' || stored === 'text') {
                    setFilterMode(field, stored, false);
                }
            });
        }

        $('.mode-toggle').on('click', function () {
            const field = $(this).data('field');
            const current = $('#' + field + 'Mode').val();
            setFilterMode(field, current === 'select' ? 'text' : 'select', true);
        });

        // "Alle": sämtliche Optionen des jeweiligen Select2 auswählen
        $('.btn-select-all').on('click', function () {
            const $sel = $('#' + $(this).data('field') + '_select');
            $sel.find('option').prop('selected', true);
            $sel.trigger('change'); // Select2 aktualisieren + markDirty über .track-dirty
        });

        // Änderungen an Suche/Text im Bestätigungsmodus -> Ausführen sperren
        $('.track-dirty').on('input change', markDirty);


        if ($('#roomsTable').length) {
            roomsTable = $('#roomsTable').DataTable({
                language: {url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json"},
                pageLength: -1,
                paging: false,
                order: [[1, 'asc']],
                columnDefs: [{orderable: false, searchable: false, targets: 0}],
                layout: {
                    bottomStart: ['pageLength', 'search'],
                    bottomEnd: ['info', 'paging'],
                    topStart: null,
                    topEnd: null
                }
            });

            roomsTable.on('draw', updateSelectionUI);
            $('#roomsTable tbody').on('change', '.update-room-checkbox', updateSelectionUI);
        }

        // Nur sichtbare (gefilterte) Zeilen an-/abwählen
        $('#toggle-all-rooms').on('change', function () {
            visibleCheckboxes().prop('checked', this.checked);
            updateSelectionUI();
        });

        $('#confirmUpdateForm').on('submit', function (e) {
            const submitter = e.originalEvent && e.originalEvent.submitter;
            const selected = [];
            allCheckboxes().filter(':checked').each(function () {
                selected.push($(this).data('roomid'));
            });
            $('#selectedRoomsInput').val(selected.join(','));

            if (submitter && submitter.name === 'confirm_update' && (selected.length === 0 || isDirty)) {
                e.preventDefault();
            }
        });

        updateSelectionUI();

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>