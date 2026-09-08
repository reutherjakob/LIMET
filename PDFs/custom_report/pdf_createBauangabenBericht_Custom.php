<?php
/**
 * ============================================================================
 *  CUSTOM-BERICHT  –  HAUPTSKRIPT (reine Orchestrierung)
 * ============================================================================
 *
 *  Rendert nur die gewählten Parameter; A3 quer oder A4 hoch.
 *
 *  Aufruf:
 *    pdf_createBauangabenBericht_Custom.php
 *        ?roomID=1,2,3
 *        &date=YYYY-MM-DD
 *        &params=<urlencodierte, kommagetrennte Parameter-Keys>
 *        &set=<Set-ID>                 (optional; Vorlage aus pdf_report_sets.php)
 *        &format=A3|A4                 (optional; sonst Set-/Projekt-Default)
 *        &mt_mode=none|list|details    (optional; sonst Set-/Projekt-Default)
 *        &mark_changes=0|1             (optional; sonst Set-/Projekt-Default)
 *
 *  Prioritäten:
 *    Parameter-Auswahl : URL(params) > Set(set) > Standard-Vorauswahl (alle)
 *    Optionen          : URL          > Set       > Standard-Default
 *    Labels/Header     : Set(set)     > Standard  (kommen nur noch aus dem Set)
 *
 *  Schichten (jede Datei hat EINEN Zweck):
 *    pdf_paramDefinitions.php  – Parameter (was)
 *    pdf_report_sets.php                               – Vorlagen/Sets (Bündel; inkl. projekt95)
 *    pdf_report_layout.php                             – Maße/Farben (wie es aussieht)
 *    pdf_report_data.php                               – SQL/Daten (woher)
 *    pdf_report_render.php                             – Darstellung (zeichnen)
 *    pdf_createMTTabelle_custom.php                    – Med.-tech.-Tabellen
 *    _pdf_custom_utils.php               – generische Helfer
 *
 *  In DIESER Datei werden KEINE Funktionen deklariert – nur Setup + Ablauf.
 * ============================================================================
 */

global $mapping, $mp2, $REPORT_PARAM_GROUPS;
ob_start();
require_once '../../utils/_utils.php';
check_login();

require_once('../../TCPDF-main/TCPDF-main/tcpdf.php');
require_once __DIR__ . '/pdf_createBericht_MYPDFclass_Custom.php';
require_once '../pdf_createBauangabenBericht_constDefinitions.php';
require_once __DIR__ . '/pdf_paramDefinitions.php';
require_once __DIR__ . '/pdf_report_sets.php';
require_once __DIR__ . '/pdf_report_layout.php';
require_once __DIR__ . '/_pdf_custom_utils.php';
require_once __DIR__ . '/pdf_report_data.php';
require_once __DIR__ . '/pdf_report_render.php';
require_once __DIR__ . '/pdf_createMTTabelle_custom.php';

// ============================================================================
//  1) EINGABEN
// ============================================================================
$roomIDsArray = array_values(array_filter(array_map(
    'intval',
    explode(",", (string)filter_input(INPUT_GET, 'roomID'))
)));
$sinceDate = getValidatedDateFromURL();
$projectID = isset($_SESSION['projectID']) ? (int)$_SESSION['projectID'] : null;

// Globale Standard-Optionen (keine Projekt-Logik mehr)
$defaults = report_get_default_options();

// Optionales Set (Vorlage): liefert bereits aufgelöste Parameter, Optionen,
// sowie optional eigene Labels (Beschriftungen) und einen eigenen Raum-Header.
$setId  = filter_input(INPUT_GET, 'set');
$set    = ($setId !== null && trim($setId) !== '')
    ? report_get_set(trim($setId))
    : null;
$setParams  = $set['params']  ?? null;   // bereits aufgelöste Key-Liste (oder null)
$setOptions = $set['options'] ?? [];
$setLabels  = $set['labels']  ?? [];     // key => label (Set-spezifische Overrides)
$setHeader  = $set['header']  ?? [];     // eigener Header oder leer -> Default

// Parameter-Gruppen inkl. Set-Label-Overrides; Raum-Header aus Set oder Default.
$reportGroups = report_get_groups($setLabels);
$headerFields = !empty($setHeader) ? $setHeader : report_get_default_header();

// Parameter-Auswahl: URL(params) > Set > Projekt-Vorauswahl, dann gegen Whitelist
$rawParams = filter_input(INPUT_GET, 'params');
if ($rawParams !== null && trim($rawParams) !== '') {
    $selectedKeys = array_values(array_filter(array_map('trim', explode(',', $rawParams))));
} elseif ($setParams !== null) {
    $selectedKeys = $setParams;
} else {
    $selectedKeys = report_selectable_param_keys();
}
$selectedKeys = array_values(array_intersect(report_selectable_param_keys(), $selectedKeys));

// Optionen: URL > Set > Projekt-Default
$format = filter_input(INPUT_GET, 'format');
$format = in_array($format, ['A3', 'A4'], true)
    ? $format
    : ($setOptions['format'] ?? $defaults['format']);

$mt_mode = filter_input(INPUT_GET, 'mt_mode');
$mt_mode = in_array($mt_mode, ['none', 'list', 'details'], true)
    ? $mt_mode
    : ($setOptions['mt_mode'] ?? $defaults['mt_mode']);

$markChangesRaw = filter_input(INPUT_GET, 'mark_changes');
if ($markChangesRaw !== null) {
    $markChanges = ($markChangesRaw === '1' || $markChangesRaw === 'true');
} elseif (array_key_exists('mark_changes', $setOptions)) {
    $markChanges = (bool)$setOptions['mark_changes'];
} else {
    $markChanges = $defaults['mark_changes'];
}

// Format global verfügbar machen (Titelseite in der MYPDF-Klasse liest das)
$GLOBALS['CUSTOM_REPORT_FORMAT'] = $format;

// ============================================================================
//  2) LAYOUT + PDF-SETUP
// ============================================================================
$L = report_build_layout($format);
// Header-Felder pro Zeile: Projekt-Option > Format-Default aus dem Layout
$headerPerRow = isset($defaults['header_per_row'])
    ? max(2, (int)$defaults['header_per_row'])
    : $L['header_per_row'];

/** @noinspection PhpUndefinedConstantInspection */
$pdf = new MYPDF($L['orient'], PDF_UNIT, $format, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, $L['margin'], $L['marginTop'], $L['marginBTM'], $format, "Bauangaben"); // Titelseite
$pdf->AddPage($L['orient'], $format);                                                                    // Inhaltsseite
$pdf->SetFillColor(...REPORT_STYLE['color_white']);
$pdf->SetFont(REPORT_STYLE['font_family'], '', $L['font_size']);
$pdf->SetLineStyle(report_line_style_normal());

$curOrient = $L['orient'];
$mysqli    = utils_connect_sql();

// ============================================================================
//  3) HAUPTSCHLEIFE: je Raum
// ============================================================================
$roomColumns = report_room_columns($headerFields, $selectedKeys);

foreach ($roomIDsArray as $roomIndex => $roomID) {

    // A4: jeden Raum auf frischer Hochformat-Seite beginnen
    // (auch nach einer Querformat-MT-Tabelle zurück auf Hochformat schalten)
    if ($format === 'A4' && ($roomIndex > 0 || $curOrient !== 'P')) {
        $pdf->AddPage('P', 'A4');
        $curOrient = 'P';
    }

    // Änderungen (nur wenn gewünscht)
    $changes = $markChanges
        ? report_detect_room_changes($mysqli, $roomID, $sinceDate, $mapping, $mp2)
        : [];

    // Raumdaten laden (nur benötigte Spalten)
    $row = report_fetch_room($mysqli, $roomID, $roomColumns);
    if ($row === null) {
        continue;
    }

    // Virtueller Parameter: Raumleistung der Elemente inkl. Gleichzeitigkeit (GLZ).
    // Wird nur bei Auswahl ermittelt und unter dem Param-Key in $row injiziert,
    // damit der Render-Layer rein darstellend bleibt (kein SQL).
    if (in_array('__RAUMLEISTUNG_GLZ__', $selectedKeys, true)) {
        $row['__RAUMLEISTUNG_GLZ__'] = report_fetch_room_power_glz($mysqli, (int)$projectID, $roomID);
    }

    // Raumkopf
    if ($format !== 'A4') {
        $pdf->Ln(8);
    }
    $pdf->SetFillColor(...REPORT_STYLE['color_white']);
    cb_render_room_header($pdf, $row, $headerFields, $headerPerRow, $L);

    // Parameter-Blöcke (Sondergruppen wie Med.-tech. werden separat behandelt)
    foreach ($reportGroups as $group) {
        if (!empty($group['special'])) {
            continue;
        }
        cb_render_param_block($pdf, $group, $selectedKeys, $row, $changes, $L);
    }

    // Med.-tech.
    if ($mt_mode !== 'none') {
        $curOrient = cb_render_mt_block($pdf, $mysqli, $roomID, (int)$projectID, $sinceDate,
            $L, $mt_mode, $format, $curOrient);
    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('BAUANGABEN_CUSTOM'), 'I');