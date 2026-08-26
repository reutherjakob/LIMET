<?php

require_once '../utils/_utils.php';
check_login();

include 'pdf_createBericht_MYPDFclass_A3Queer.php'; //require_once('../TCPDF-main/TCPDF-main/tcpdf.php'); is in class file
include '_pdf_createBericht_utils.php';
include 'pdf_createMTTabelle_fast.php';           // <-- schnelle MT-Tabelle (Lookups statt Queries pro Raum)
include 'pdf_createBauangabenBericht_constDefinitions.php';

$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);
$Änderungsdatum = getValidatedDateFromURL();

ini_set('memory_limit', '1024M');
set_time_limit(600); // Sicherheitsnetz, falls doch mal knapp


//     -----   FORMATTING VARIABLES    -----
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
/** @noinspection PhpUndefinedConstantInspection */
$SB = 420 - 2 * PDF_MARGIN_LEFT;  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;
$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 6;
$horizontalSpacerLN3 = 8;

$e_C = $SB / 8;
$e_C_3rd = $e_C / 3;
$e_C_2_3rd = $e_C - $e_C_3rd;

$font_size = 6;
$block_header_height = 10;
$block_header_w = 25;
$einzugPlus = 10; //um den text auf die Höhe der anderen Angaben zu shiften bei ANM BO

$colour_line = array(110, 150, 80);
$style_dashed = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line);
$style_normal = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line);

/** @noinspection PhpUndefinedConstantInspection */
$pdf = new MYPDF('L', PDF_UNIT, "A3", true, 'UTF-8', false, true);
/** @noinspection PhpUndefinedConstantInspection */


$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A3", "Bauangaben");
$pdf->setCompression(true);

$pdf->AddPage('L', 'A3');
$pdf->SetFillColor(0, 0, 0, 0); //$pdf->SetFillColor(244, 244, 244);
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);


$mysqli = utils_connect_sql();
$isnotVorentwurf = $_SESSION["projectPlanungsphase"] !== "Vorentwurf";


// ===========================================================================
//  PERFORMANCE: Projektweite Daten EINMAL laden (statt pro Raum!)
//  - Parameterdefinitionen + Elementparameter-Werte als indexierte Lookups
//  - KEINE Änderungs-Query: dieser Bericht ist "ohne Änderungsmarkierungen"
// ===========================================================================
$paramDefs = [];
$valueLookup = [];
if ($isnotVorentwurf) {
    [$paramDefs, $valueLookup] = mtf_load_project_lookups($mysqli, (int)$_SESSION["projectID"]);
}
$changedLookup = []; // leer => make_MT_details_table_fast() markiert nichts


// ===========================================================================
//  ZENTRALE PARAMETER-DEFINITIONEN
//  ---------------------------------------------------------------------------
//  Alle Berichts-Parameter sind hier oben blockweise als Arrays gelistet.
//  Gerendert werden sie einheitlich über render_room_param() (siehe unten).
//
//  Feld-Bedeutung je Parameter:
//    key           = Spaltenname in tabelle_räume (bzw. berechnetes Feld)
//    label         = Beschriftung im PDF
//    unit          = Einheit (wird an den Wert gehängt, z.B. 'Stk', 'm2', '°C')
//    cell          = Breite der Label-Zelle
//    str_cell      = Breite der Werte-Zelle
//    type          = Rendering-Typ (siehe render_room_param):
//                    'str'      -> multicell_with_str
//                    'nr'       -> multicell_with_nr  (opt. 'fontsize')
//                    'hackerl'  -> Häkchen/Checkbox   ('check' = 'JA' oder 1)
//                    'power'    -> kify()-Wert + Einheit, sonst "-"
//                    'waerme'   -> HT_Waermeabgabe_W (kify + "W" bzw. "keine Angabe")
//                    've_wasser'-> translate_1_to_yes()
//                    'hygiene'  -> Hygieneklasse (Fallback " - ")
//                    'strahlen' -> strahlenanw() mit Höhen-Umbruch
//    ln_after      = Zeilenumbruch + Platzhalter nach diesem Parameter
//    check         = Vergleichswert für 'hackerl'
//    fontsize      = Schriftgröße für 'nr'
//    height_width / str_cell_wide = Sonderbreiten für 'strahlen'
// ===========================================================================

// ---------- ALLGEMEIN (Raumangaben) ----------
// Hinweis: 'Allgemeine Hygieneklasse' (type 'hygiene') wird bei zu hohem Text
// unten in eine eigene Zeile umgebrochen – Sonderbehandlung in der Schleife.

//// ---------- ELEKTRO ----------
//// Optionale (derzeit deaktivierte) Parameter – bei Bedarf einfach mit passendem
//// 'type' wieder in das Array aufnehmen:
////   'ET_Anschlussleistung_W' / '_AV_W' / '_SV_W' / '_ZSV_W' / '_USV_W'  -> type 'power'
////   'ET_RJ45-Ports'                                                     -> type 'nr'
////   'EL_Laser 16A CEE Stk' / 'EL_Roentgen 16A CEE Stk'                  -> type 'str'
////   'RaumAnschlussLeistungInklGlz'                                      -> type 'raumleistung_include'
//// Mit 'skip_vorentwurf' => true werden Parameter in der Phase "Vorentwurf" ausgelassen.
///
// ---------- MED.-GAS ----------
// Labels entsprechen dem bisherigen str_replace-Ergebnis
// (['1 Kreis ','2 Kreis ','-'] => ['1 Kreis   ','2 Kreise ','']).
// Alle Einträge: Häkchen ('hackerl') mit Vergleichswert 1.

$allgemeinParams = [
    ['key' => 'Allgemeine Hygieneklasse', 'label' => 'BSL: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hoher_text'],
    ['key' => 'Strahlenanwendung', 'label' => 'Strahlenanw.: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'strahlen',
        'height_width' => $e_C_3rd, 'str_cell_wide' => 4 * $e_C_3rd],
    ['key' => 'Nutzfläche', 'label' => 'Fläche: ', 'unit' => 'm2', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'nr', 'fontsize' => 10],
    ['key' => 'Decke', 'label' => 'Decke: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str', 'fontsize' => 10, 'ln_after' => true],


    ['key' => 'NGA', 'label' => 'Erste-Hilfe-Kasten: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'Aufenthaltsraum', 'label' => 'Notruf: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'AR_Belichtung-nat', 'label' => 'Tageslicht erforderlich: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'Abdunkelbarkeit', 'label' => 'Abdunkelbarkeit: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C*2, 'type' => 'abdunkel'],

];

$elektroParams = [
    // ['key' => 'USV',           'label' => 'USV: ',        'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_3rd  , 'type' => 'hackerl', 'check' => 'JA'],
    ['key' => 'ET_Anschlussleistung_W', 'label' => 'Leistung:', 'unit' => 'W', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'isnotVorentwurf' => false],
    ['key' => 'ET_Anschlussleistung_AV_W', 'label' => 'AV(Rauml.): ', 'unit' => 'W', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'isnotVorentwurf' => false],
    ['key' => 'ET_Anschlussleistung_SV_W', 'label' => 'SV(Rauml.): ', 'unit' => 'W', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'isnotVorentwurf' => false],
    ['key' => 'IT Anbindung', 'label' => 'IT Anschl.: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_3rd, 'type' => 'hackerl', 'check' => 'JA', 'ln_after' => true],

    ['key' => 'Volumen', 'label' => 'AV/(AV+SV): ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'nr'],
    ['key' => 'AV', 'label' => 'AV: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 'JA'],
    ['key' => 'SV', 'label' => 'SV: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 'JA'],
];

// ---------- HAUSTECHNIK ----------
$haustechnikParams = [
    ['key' => 'HT_Waermeabgabe_W', 'label' => 'Abwärme: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'waerme'],
    ['key' => 'HT_Raumtemp Sommer °C', 'label' => 'Max. Raumtemp.', 'unit' => '°C', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
    ['key' => 'HT_Raumtemp Winter °C', 'label' => 'Min. Raumtemp.', 'unit' => '°C', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
    ['key' => 'HT_Tempgradient_Ch', 'label' => 'Toleranz: +/- ', 'unit' => '°C', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str', 'ln_after' => true],

    ['key' => 'HT_Luftfeuchte', 'label' => 'Luftfeucht.:', 'unit' => '%', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
    ['key' => 'HT_Luftwechsel 1/h', 'label' => 'Luftwechselrate:', 'unit' => 'm3/m2', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str', 'unit_if_numeric' => true],
    ['key' => 'H6020', 'label' => 'Druckregel.:', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
    ['key' => 'HT_Abluft_Rauchgasabzug_Stk', 'label' => 'Veraschung:', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1, 'ln_after' => true],

    ['key' => 'HT_Kaltwasser', 'label' => 'Kaltwasser: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'HT_Warmwasser', 'label' => 'Warmwasser: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'VE_Wasser', 'label' => 'VE Wasser:', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'HT_Notdusche', 'label' => 'Notdusche:', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1, 'ln_after' => true],

    ['key' => 'Nutzwasser', 'label' => 'Nutzwasser:', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'HT_Kühlwasser', 'label' => 'Kühlwasser:', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'HT_Spezialabwasser', 'label' => 'Spez. Abwasser:', 'unit' => '', 'cell' => $e_C, 'str_cell' => 2 * $e_C, 'str' => 'hackerl', 'ln_after' => true],

    ['key' => 'HT_Abluft_Digestorium_Stk', 'label' => 'Abluft Digestor:', 'unit' => 'Stk', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
    ['key' => 'HT_Abluft_Sicherheitsschrank_Stk', 'label' => 'Abluft Sicherheitsschr.:', 'unit' => 'Stk', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
    ['key' => 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'label' => 'Sicherheitsschr. UB:', 'unit' => 'Stk', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
    ['key' => 'HT_Punktabsaugung_Stk', 'label' => 'Abluft Punktabs.:', 'unit' => 'Stk', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'str'],
];
$medGasParams = [
    ['key' => 'N2', 'label' => 'N2: (zentral)', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'DL-5', 'label' => 'DL-5 (zentral): ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
    ['key' => 'VA', 'label' => 'VA: ', 'unit' => '', 'cell' => $e_C, 'str_cell' => $e_C_2_3rd, 'type' => 'hackerl', 'check' => 1],
];


/**
 * Rendert einen einzelnen Raum-Parameter (Label + Wert) anhand seines 'type'.
 * Schreibt direkt in $pdf. Der Zeilenumbruch ('ln_after') wird bewusst NICHT
 * hier, sondern in der jeweiligen Block-Schleife behandelt, da die Platzhalter
 * je Block unterschiedlich sind.
 *
 * @param MYPDF $pdf
 * @param array $row aktuelle Raum-Zeile
 * @param array $p Parameter-Definition (siehe oben)
 * @param int $font_size
 * @param array $changes Änderungs-Lookup für Highlighting
 */
function render_room_param($pdf, $row, $p, $font_size, $changes): void
{
    $cell = $p['cell'];
    $strCell = $p['str_cell'] ?? 0;
    $unit = $p['unit'] ?? '';
    $type = $p['type'] ?? 'str';
    $value = $row[$p['key']] ?? '';

    // Beschriftung (inkl. Änderungs-Highlight)
    multicell_text_hightlight($pdf, $cell, $font_size, $p['key'], $p['label'], $changes);

    switch ($type) {
        case 'str':
            $u = $unit;
            if (!empty($p['unit_if_numeric'])
                && !is_numeric(str_replace(',', '.', trim((string)$value)))) {
                $u = ''; // z.B. "nach Erfordernis" -> ohne Einheit
            }
            multicell_with_str($pdf, $value, $strCell, $u);
            break;

        case 'nr':
            $fs = $p['fontsize'] ?? $pdf->getFontSizePt();
            multicell_with_nr($pdf, $value, $unit, $fs, $strCell);
            break;

        case 'hackerl':
            hackerlA3($pdf, $font_size, $strCell, $value, $p['check'] ?? 'JA');
            break;

        case 'power': // kify()-formatierter Leistungswert oder "-"
            $val = ($value != "0") ? kify($value) . $unit : "-";
            multicell_with_str($pdf, $val, $strCell, "");
            break;

        case 'waerme': // HT_Waermeabgabe_W
            $val = ($value === "0" || $value == 0 || $value == "-") ? "keine Angabe" : kify($value) . "W";
            multicell_with_str($pdf, $val, $strCell, "");
            break;

        case 've_wasser':
            multicell_with_str($pdf, translate_1_to_yes($value), $strCell, $unit);
            break;

        case 'hoher_text':
            multicell_with_str($pdf, $value != "" ? $value : " - ", $strCell, "");
            break;

        case 'strahlen':
            $wide = $pdf->getStringHeight($p['height_width'], $value) > 6;
            strahlenanw($pdf, $value, $wide ? $p['str_cell_wide'] : $strCell, $font_size);
            break;
        case 'abdunkel': // 0=keine, 1=Blendschutz, 2=Verdunkelung, 3=beides
            $map = [0 => 'keine', 1 => 'Blendschutz', 2 => 'Verdunkelung', 3 => 'Blendschutz+Verdunkelung'];
            multicell_with_str($pdf, $map[(int)$value] ?? ' - ', $strCell, "");
            break;
    }
}


$parameter_changes_t_räume = array();
foreach ($roomIDsArray as $valueOfRoomID) {
    $sql = "SELECT

    -- Anmerkungs-Blöcke
    tabelle_räume.`Anmerkung FunktionBO`,
    tabelle_räume.`Anmerkung Elektro`,
    tabelle_räume.`Anmerkung HKLS`,
    tabelle_räume.`Anmerkung MedGas`,
    tabelle_räume.`Anmerkung BauStatik`,

    -- Allgemein
    tabelle_räume.`Allgemeine Hygieneklasse`,
    tabelle_räume.Strahlenanwendung,
    tabelle_räume.Nutzfläche,
    tabelle_räume.Raumnr,
    tabelle_räume.Raumbezeichnung,
    tabelle_räume.`Raumbereich Nutzer`,
    tabelle_räume.Geschoss,
    tabelle_räume.Bauetappe,
    tabelle_räume.Bauabschnitt,
    tabelle_räume.Decke,
    tabelle_räume.Bauabschnitt,
    tabelle_räume.`AR_Belichtung-nat`,
    tabelle_räume.Abdunkelbarkeit,
    tabelle_räume.Aufenthaltsraum,
    tabelle_räume.NGA,
    
    -- Elektro
    tabelle_räume.Anwendungsgruppe,
    tabelle_räume.AV,
    tabelle_räume.SV,
    tabelle_räume.ET_Anschlussleistung_W,
    tabelle_räume.ET_Anschlussleistung_AV_W,
    tabelle_räume.ET_Anschlussleistung_SV_W,
    tabelle_räume.`IT Anbindung`,
    tabelle_räume.Volumen,
    
    -- Haustechnik
    tabelle_räume.HT_Waermeabgabe_W,
    tabelle_räume.HT_Kaltwasser,
    tabelle_räume.HT_Warmwasser,
    tabelle_räume.VE_Wasser,
    tabelle_räume.HT_Notdusche,
    tabelle_räume.`HT_Raumtemp Sommer °C`,
    tabelle_räume.HT_Abluft_Digestorium_Stk,
    tabelle_räume.HT_Abluft_Sicherheitsschrank_Stk,
    tabelle_räume.HT_Abluft_Sicherheitsschrank_Unterbau_Stk,
    tabelle_räume.HT_Punktabsaugung_Stk,
    tabelle_räume.`HT_Raumtemp Winter °C`,
    tabelle_räume.HT_Tempgradient_Ch,
    tabelle_räume.`HT_Luftwechsel 1/h`,
    tabelle_räume.H6020,
    
    tabelle_räume.HT_Abluft_Rauchgasabzug_Stk,
    tabelle_räume.HT_Luftfeuchte,
    tabelle_räume.HT_Spezialabwasser,
    tabelle_räume.Nutzwasser,
    tabelle_räume.HT_Kühlwasser,

    -- Gase
    tabelle_räume.`N2`,
    tabelle_räume.`DL-5`,
    tabelle_räume.VA
    
    FROM tabelle_räume
    WHERE tabelle_räume.idTABELLE_Räume = " . (int)$valueOfRoomID;

    $result_rooms = $mysqli->query($sql);
    while ($row = $result_rooms->fetch_assoc()) {
        $pdf->Ln(8);
        $pdf->SetFillColor(255, 255, 255);
        raum_header($pdf, $horizontalSpacerLN3, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt'], "A3SF", $parameter_changes_t_räume); //utils function
        $text = trim($row['Anmerkung FunktionBO'] ?? "");
        if ($text != "") {
            $outstr = format_text(clean_string(br2nl($row['Anmerkung FunktionBO'])));
            $rowHeightComment = $pdf->getStringHeight($SB - $einzugPlus, $outstr, false, true, '', 1);
            $i = ($rowHeightComment > 6) ? $horizontalSpacerLN : 0;

            block_label_queer($block_header_w, $pdf, "BO-Beschr.", $rowHeightComment + $i, $block_header_height, $SB);
            $pdf->SetFont('helvetica', 'I', 10);
//            $pdf->MultiCell($einzugPlus, $rowHeightComment, "", 0, 'L', 0, 0);
            $pdf->MultiCell($SB - $einzugPlus, $rowHeightComment, $outstr, 0, 'L', 0, 1);
            if ($rowHeightComment > 6) {
                $pdf->Ln($horizontalSpacerLN);
            } else {
                $pdf->Ln(1);
            }
        }

//   ---------- ALLGEMEIN (Raumangaben) ----------
//
        block_label_queer($block_header_w, $pdf, "Allgemein", $horizontalSpacerLN3 + 6, $block_header_height, $SB);

        // Hygieneklasse braucht ggf. eine eigene Zeile (zu hoher Text) -> Umbruch unten
        $hygiene = $row['Allgemeine Hygieneklasse'];
        $heightExceeds = $hygiene != "" && $pdf->getStringHeight($e_C_3rd, $hygiene, false, true, '', 1) > 6;

        foreach ($allgemeinParams as $param) {
            if ($param['type'] === 'hoher_text' && $heightExceeds) {
                continue; // wird unten in eigener Zeile ausgegeben
            }
            render_room_param($pdf, $row, $param, $font_size, $parameter_changes_t_räume);
            if (!empty($param['ln_after'])) {
                $pdf->Ln($horizontalSpacerLN2);        // Platzhalter-Label für Folgezeile
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            }
        }

        if ($heightExceeds) {
            $pdf->Ln($horizontalSpacerLN);
            multicell_text_hightlight($pdf, $block_header_w, $font_size, "", "", []);
            multicell_text_hightlight($pdf, $e_C, $font_size, "Allgemeine Hygieneklasse", "Hygieneklasse: ", $parameter_changes_t_räume);
            multicell_with_str($pdf, $hygiene, $e_C_3rd * 5, "");
        }

        $pdf->Ln($horizontalSpacerLN2);

//       ---------- ELEKTRO -----------
        $i = 12 + $horizontalSpacerLN + $horizontalSpacerLN2;
        $blockHeight = 6 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung Elektro'], $SB) + $i;
        block_label_queer($block_header_w, $pdf, "Elektro", $blockHeight, $block_header_height, $SB);

        foreach ($elektroParams as $param) {
            // In der Phase "Vorentwurf" markierte Parameter überspringen
            if (!$isnotVorentwurf && !empty($param['skip_vorentwurf'])) {
                continue;
            }

            if (($param['type'] ?? '') === 'raumleistung_include') {
                // Sonderfall: eingebundene Berechnung (benötigt Datei-Scope)
                include "pdf_getRaumleistungInklGlz.php";
                $pdf->Ln($horizontalSpacerLN2);
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            } else {
                render_room_param($pdf, $row, $param, $font_size, $parameter_changes_t_räume);
            }

            if (!empty($param['ln_after'])) {
                $pdf->Ln($horizontalSpacerLN2);        // Platzhalter-Label für Folgezeile
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            }
        }
        $pdf->Ln(1);
        anmA3($pdf, $row['Anmerkung Elektro'], $SB, $block_header_w);
        $pdf->Ln($horizontalSpacerLN);

//
//// ---------- HAUSTEK ---------
//
        $Block_height = 6 + $horizontalSpacerLN2 + getAnmHeight($pdf, $row['Anmerkung HKLS'], $SB);
        block_label_queer($block_header_w, $pdf, "Haustechnik", $Block_height, $block_header_height, $SB);

        foreach ($haustechnikParams as $param) {
            render_room_param($pdf, $row, $param, $font_size, $parameter_changes_t_räume);

            if (!empty($param['ln_after'])) {
                $pdf->Ln($horizontalSpacerLN2);
                $pdf->Multicell($block_header_w, 1, "", 0, 0, 0, 0);
            }
        }

        $pdf->Ln($horizontalSpacerLN2);
        if (anmA3($pdf, $row['Anmerkung HKLS'], $SB, $block_header_w)) {
            $pdf->Ln($horizontalSpacerLN);
        }


/// ----------- GAS -----------

        $Block_height = 12 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung MedGas'], $SB);
        block_label_queer($block_header_w, $pdf, "Gase", $Block_height, $block_header_height, $SB);

        foreach ($medGasParams as $param) {
            render_room_param($pdf, $row, $param, $font_size, $parameter_changes_t_räume);

            if (!empty($param['ln_after'])) {
                $pdf->Ln($horizontalSpacerLN);
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            }
        }

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung MedGas'], $SB, $block_header_w);


////     ------- BauStatik ---------
        $anm = trim($row['Anmerkung BauStatik'] ?? '');
        if ($anm !== '' && $anm !== 'Keine Anmerkung' && $anm !== 'keine Angaben MT') {
            $pdf->Ln($horizontalSpacerLN);
            $Block_height = getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB);
            block_label_queer($block_header_w, $pdf, "Baustatik", $Block_height, $block_header_height, $SB);
            $pdf->Ln(1);
            $pdf->Ln($horizontalSpacerLN);
            anmA3($pdf, $row['Anmerkung BauStatik'], $SB, $block_header_w);
            $pdf->Ln($horizontalSpacerLN);
        }
//
//
//
////     ------- MT Tabelle  ---------
//
        // -------------------------Elemente im Raum laden--------------------------
        $sql = "SELECT tabelle_elemente.ElementID,
                        tabelle_elemente.Bezeichnung,
                        tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
            tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
            tabelle_räume_has_tabelle_elemente.Standort,
            tabelle_räume_has_tabelle_elemente.Verwendung
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
            tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            -- WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $valueOfRoomID . ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
        $resultX = $mysqli->query($sql);

        // PERFORMANCE: num_rows statt Zählschleife über alle Zeilen
        $rowcounter = $resultX->num_rows;

        $upcmn_blck_size = 0;
        if ($isnotVorentwurf && $rowcounter > 0) {
            // PERFORMANCE: Parameterdefinitionen, Werte-Lookup wurden bereits
            // EINMAL vor der Raumschleife geladen (mtf_load_project_lookups).
            // Dieser Bericht markiert keine Änderungen => $changedLookup = [].
            $upcmn_blck_size = 10 + $rowcounter * 5;
            block_label_queer($block_header_w, $pdf, "Lab.-tech.", $upcmn_blck_size, $block_header_height, $SB);
            make_MT_details_table_fast($pdf, $resultX, $paramDefs, $valueLookup, $SB, $SH, $changedLookup);
        } else if ($rowcounter > 0) {
            $upcmn_blck_size = 10 + $rowcounter / 2 * 5;
            block_label_queer($block_header_w, $pdf, "Lab.-tech.", $upcmn_blck_size, $block_header_height, $SB);
            $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
            make_MT_list($pdf, $SB, $block_header_w, $rowcounter, $resultX, $style_normal, $style_dashed);
        } else {
            $pdf->Line(15, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_normal);
            block_label_queer($block_header_w, $pdf, "Lab.-tech.", $upcmn_blck_size, $block_header_height, $SB);
            $pdf->Multicell(0, 0, "Keine Ausstattung.", "", "L", 0, 0);
            $pdf->Ln();
        }
        $resultX->free();
    } //sql:fetch-assoc
    $result_rooms->free();
}// for every room
unset($valueLookup, $paramDefs);
$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('BAUANGABEN'), 'I');