<?php
global $mysqli, $formFields, $labortypen;
include "../roombook_nutzerlogin/db.php";
require_once "../roombook_nutzerlogin/_utils.php";
$role = init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_admin", "spargelfeld_view"]);

require_once "form_fields_forNutzergruppe_VE.php"; // Fragen der ERSTEN Abfrage
require_once "raumtyp_resolver.php";               // Regeln der ERSTEN Abfrage
require_once "../roombook_nutzerumfrage_sf/raumtypen.php";

header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$projektId = 95;
$roomId = filter_input(INPUT_GET, 'roomId', FILTER_VALIDATE_INT);
if (!$roomId) {
    http_response_code(400);
    exit('<p class="text-danger">Ungültige Raum-ID.</p>');
}

/* ── Erste Abfrage: [Feld, Label, Typ (B|N|T), Kommentarfeld] ─────────────── */
$abfrageFelder = [
    ['doppelfluegeltuer', 'Doppelflügeltür (1,8 m)', 'B', null],
    ['vibrationsempfindlich_bodenstehend', 'Vibrationsempf. bodenstehende Geräte', 'B', 'vibrationsempfindlich_bodenstehend_kommentar'],
    ['explosionsschutz', 'Explosionsschutz (ganzer Raum)', 'B', null],
    ['abluftwaescher', 'Abzüge mit Säurewäscher', 'N', 'abluftwaescher_kommentar'],
    ['spezialgas', 'Dezentrale Sondergase', 'T', 'spezialgas_kommentar'],
    ['raumabluft_besonders', 'Besondere Raumabluft', 'T', null],
    ['raumzuluft_besonders', 'Filterung der Zuluft', 'T', null],
    ['nutzwasser', 'Nutzwasser', 'B', 'nutzwasser_kommentar'],
    ['spezialabwasser', 'Abwasser mit Spezialbehandlung', 'T', 'spezialabwasser_kommentar'],
    ['DL', 'Druckluft', 'B', null],
    ['N2', 'Stickstoff (N2)', 'B', null],
    ['Vakuum', 'Vakuum', 'B', null],
    ['kuehlwasser', 'Kühlwasser für Geräte', 'B', 'kuehlwasser_kommentar'],
    ['raumtemp', 'Besondere Raumtemperatur', 'B', 'raumtemp_kommentar'],
    ['luftf', 'Besondere Luftfeuchtigkeit', 'B', 'luftf_kommentar'],
    ['allgemeiner_kommentar', 'Kommentar zum Raum', 'T', null],
];

/* Räume, deren Raumtyp nach der Abfrage geändert wurde (aus bericht_VE.php) */
$raumtypGeaendert = ['247', '264', '265', '266', '267', '268', '269', '274',
    '281', '282', '283', '286', '329', '330', '350', '382', '606'];

/* ── Raumparameter wie im Bauangaben-Bericht: [Spalte, Label, Typ, Einheit] ── */
$paramBloecke = [
    'Allgemein' => ['anm' => 'Anmerkung FunktionBO', 'params' => [
        ['Allgemeine Hygieneklasse', 'BSL', 'str', ''],
        ['Strahlenanwendung', 'Strahlenanw.', 'strahlen', ''],
        ['Nutzfläche', 'Fläche', 'str', 'm²'],
        ['Decke', 'Decke', 'str', ''],
        ['NGA', 'Erste-Hilfe-Kasten', 'bool', ''],
        ['Aufenthaltsraum', 'Notruf', 'bool', ''],
        ['AR_Belichtung-nat', 'Tageslicht erforderlich', 'bool', ''],
        ['Abdunkelbarkeit', 'Abdunkelbarkeit', 'abdunkel', ''],
    ]],
    'Elektro' => ['anm' => 'Anmerkung Elektro', 'params' => [
        ['ET_Anschlussleistung_W', 'Leistung (ohne GLZ)', 'watt', ''],
        ['ET_Anschlussleistung_AV_W', 'AV (Rauml.)', 'watt', ''],
        ['ET_Anschlussleistung_SV_W', 'SV (Rauml.)', 'watt', ''],
        ['IT Anbindung', 'IT Anschl.', 'bool', ''],
        ['Volumen', 'AV/(AV+SV)', 'str', ''],
        ['AV', 'AV', 'bool', ''],
        ['SV', 'SV', 'bool', ''],
    ]],
    'Haustechnik' => ['anm' => 'Anmerkung HKLS', 'params' => [
        ['HT_Waermeabgabe_W', 'Abwärme', 'watt', ''],
        ['HT_Raumtemp Sommer °C', 'Max. Raumtemp.', 'str', '°C'],
        ['HT_Raumtemp Winter °C', 'Min. Raumtemp.', 'str', '°C'],
        ['HT_Tempgradient_Ch', 'Toleranz +/-', 'str', '°C'],
        ['HT_Luftfeuchte', 'Luftfeuchte', 'str', '%'],
        ['HT_Luftwechsel 1/h', 'Luftwechselrate', 'str_num', 'm³/m²'],
        ['H6020', 'Druckregelung', 'str', ''],
        ['HT_Abluft_Rauchgasabzug_Stk', 'Veraschung', 'bool', ''],
        ['HT_Kaltwasser', 'Kaltwasser', 'bool', ''],
        ['HT_Warmwasser', 'Warmwasser', 'bool', ''],
        ['VE_Wasser', 'VE Wasser', 'bool', ''],
        ['HT_Notdusche', 'Notdusche', 'bool', ''],
        ['Nutzwasser', 'Nutzwasser', 'bool', ''],
        ['HT_Kühlwasser', 'Kühlwasser', 'bool', ''],
        ['HT_Spezialabwasser', 'Spez. Abwasser', 'str', ''],
        ['HT_Abluft_Digestorium_Stk', 'Abluft Digestor', 'anzahl', 'Stk'],
        ['HT_Abluft_Sicherheitsschrank_Stk', 'Abluft Sicherheitsschr.', 'anzahl', 'Stk'],
        ['HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'Sicherheitsschr. UB', 'anzahl', 'Stk'],
        ['HT_Punktabsaugung_Stk', 'Abluft Punktabs.', 'anzahl', 'Stk'],
    ]],
    'Gase' => ['anm' => 'Anmerkung MedGas', 'params' => [
        ['N2', 'N2 (zentral)', 'bool', ''],
        ['DL-5', 'DL-5 (zentral)', 'bool', ''],
        ['VA', 'VA', 'bool', ''],
    ]],
    'Baustatik' => ['anm' => 'Anmerkung BauStatik', 'params' => []],
];

/* ── Hilfsfunktionen ──────────────────────────────────────────────────────── */
function ang_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function ang_zeile(string $label, string $wert, bool $positiv, string $kommentar = ''): string
{
    $klasse = $positiv ? 'fw-semibold' : 'text-muted';
    $html = '<tr><td class="text-muted">' . ang_h($label) . '</td>'
        . '<td class="' . $klasse . '">' . nl2br(ang_h($wert));
    if ($kommentar !== '') {
        $html .= '<div class="fst-italic fw-normal text-muted">' . nl2br(ang_h($kommentar)) . '</div>';
    }
    return $html . '</td></tr>';
}

function ang_abfrage_wert($val, string $typ, array $optionen): array
{
    $s = trim((string)$val);
    if ($s === '') return ['—', false];

    if ($typ === 'B') {
        $ja = ($s === '1' || strtolower($s) === 'ja');
        return [$ja ? 'Ja' : 'Nein', $ja];
    }
    if ($typ === 'N') {
        return [$s, is_numeric($s) && (float)$s > 0];
    }
    if ($s === '0' || strtolower($s) === 'nein') return ['Nein', false];

    // Options-Keys (z.B. "Synth_Luft") in lesbare Bezeichnungen übersetzen
    $teile = array_map(fn($t) => $optionen[trim($t)] ?? trim($t), explode(',', $s));
    return [implode(', ', $teile), true];
}

/** Liefert [Text, positiv] oder null, wenn noch nichts angegeben ist. */
function ang_param_wert($v, string $typ, string $einheit): ?array
{
    $s = trim((string)$v);
    if ($s === '' || $s === '-') return null;

    switch ($typ) {
        case 'bool':
            $ja = ($s === '1' || strtolower($s) === 'ja');
            return [$ja ? 'Ja' : 'Nein', $ja];
        case 'watt':
            if (!is_numeric($s) || (float)$s == 0) return null;
            return [number_format((float)$s, 0, ',', '.') . ' W', true];
        case 'anzahl':
            if (is_numeric($s) && (float)$s == 0) return null;
            return [trim("$s $einheit"), true];
        case 'abdunkel':
            $map = [0 => 'keine', 1 => 'Blendschutz', 2 => 'Verdunkelung', 3 => 'Blendschutz + Verdunkelung'];
            return [$map[(int)$s] ?? $s, (int)$s > 0];
        case 'strahlen':
            if ($s === '0') return ['Nein', false];
            return [$s === '1' ? 'Ja' : $s, true];
        case 'str_num':
            $numerisch = is_numeric(str_replace(',', '.', $s));
            return [$numerisch ? trim("$s $einheit") : $s, true];
        default:
            return [trim("$s $einheit"), true];
    }
}

function ang_anmerkung($text): string
{
    $t = trim(preg_replace('/<br\s*\/?>/i', "\n", (string)$text));
    return in_array($t, ['', 'Keine Anmerkung', 'keine Angaben MT'], true) ? '' : $t;
}

/* ── Raum laden (inkl. Berechtigungsprüfung) ──────────────────────────────── */
$spalten = ['Raumnr', 'Raumtyp BH'];
foreach ($paramBloecke as $block) {
    $spalten[] = $block['anm'];
    foreach ($block['params'] as $p) {
        $spalten[] = $p[0];
    }
}
$select = implode(', ', array_map(fn($s) => "`$s`", array_unique($spalten)));

$sql = "SELECT $select FROM tabelle_räume
        WHERE idTABELLE_Räume = ? AND tabelle_projekte_idTABELLE_Projekte = ?";
$siehtAlle = in_array($role, ["internal_rb_user", "spargelfeld_admin", "spargelfeld_view"], true);

if (!$siehtAlle) {
    $sql .= " AND `Raumbereich Nutzer` LIKE ?";
}
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    exit('<p class="text-danger">Angaben konnten nicht geladen werden.</p>');
}
if ($siehtAlle) {
    $stmt->bind_param("ii", $roomId, $projektId);
} else {
    $likeParam = "%" . $_SESSION["user_name"] . "%";
    $stmt->bind_param("iis", $roomId, $projektId, $likeParam);
}
$stmt->execute();
$raum = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$raum) {
    http_response_code(403);
    exit('<p class="text-danger">Kein Zugriff auf diesen Raum.</p>');
}

/* ── Antworten der ersten Abfrage laden ───────────────────────────────────── */
$stmt = $mysqli->prepare("SELECT * FROM tabelle_room_requirements_from_user WHERE roomID = ?");
$stmt->bind_param("i", $roomId);
$stmt->execute();
$antworten = $stmt->get_result()->fetch_assoc();
$stmt->close();
$mysqli->close();

/* ── Für diesen Raumtyp gestellte Fragen ermitteln ────────────────────────── */
$raumtyp = getRaumtypById($labortypen, (string)$raum['Raumtyp BH']);
$felder = applyRaumtypOverrides($formFields, $raumtyp);
$optionen = array_column($felder, 'options', 'name');

$nichtGefragt = [];
foreach ($felder as $f) {
    if (($f['type'] ?? '') === 'texthidden' && isset($f['name'])) {
        $nichtGefragt[$f['name']] = true;
    }
}

/* ── Ausgabe: erste Abfrage ───────────────────────────────────────────────── */
echo '<h6 class="angaben-titel">Erste Abfrage</h6>';

if (in_array(trim((string)$raum['Raumnr']), $raumtypGeaendert, true)) {
    echo '<div class="small fst-italic text-muted mb-1">Raumtyp wurde nach der Abfrage geändert.</div>';
}

if (!$antworten) {
    echo '<p class="small text-muted">Für diesen Raum liegen keine Angaben vor.</p>';
} else {
    $zeilen = '';
    foreach ($abfrageFelder as [$feld, $label, $typ, $kommentarFeld]) {
        if (isset($nichtGefragt[$feld])) continue;
        $wert = $antworten[$feld] ?? null;
        if ($feld === 'allgemeiner_kommentar' && trim((string)$wert) === '') continue;

        [$text, $positiv] = ang_abfrage_wert($wert, $typ, $optionen[$feld] ?? []);
        $kommentar = $kommentarFeld ? trim((string)($antworten[$kommentarFeld] ?? '')) : '';
        $zeilen .= ang_zeile($label, $text, $positiv, $kommentar);
    }
    echo '<table class="table table-sm angaben-tabelle"><tbody>' . $zeilen . '</tbody></table>';
}

/* ── Ausgabe: Raumparameter ──────────────────────────── ───────────────────── */
$hatParameter = false;
foreach ($paramBloecke as $titel => $block) {
    $zeilen = '';
    foreach ($block['params'] as [$spalte, $label, $typ, $einheit]) {
        $wert = ang_param_wert($raum[$spalte] ?? null, $typ, $einheit);
        if ($wert !== null) {
            $zeilen .= ang_zeile($label, $wert[0], $wert[1]);
        }
    }
    $anmerkung = ang_anmerkung($raum[$block['anm']] ?? '');
    if ($zeilen === '' && $anmerkung === '') continue;

    $hatParameter = true;
    echo '<h6 class="angaben-titel">' . ang_h($titel) . '</h6>';
    if ($zeilen !== '') {
        echo '<table class="table table-sm angaben-tabelle"><tbody>' . $zeilen . '</tbody></table>';
    }
    if ($anmerkung !== '') {
        echo '<div class="small fst-italic mb-2">' . nl2br(ang_h($anmerkung)) . '</div>';
    }
}

if (!$hatParameter) {
    echo '<p class="small text-muted">Noch keine Raumparameter angegeben.</p>';
}