<?php /** @noinspection ALL */

require_once '../utils/_utils.php';
check_login();

include 'pdf_createBericht_MYPDFclass_A3Queer_SAN.php';
include '_pdf_createBericht_utils.php';

$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);

$marginTop = 17;
$marginBTM = 10;
$SB = 420 - (PDF_MARGIN_LEFT * 2);
$SH = 297 - $marginTop - $marginBTM;

$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 5;

$e_B = $SB / 5;
$e_B_2rd = $e_B / 2;

$font_size = 6;
$block_header_height = 10;
$block_header_w = 25;
$blockHeight = $horizontalSpacerLN;

$colour_line = array(110, 150, 80);
$style_dashed = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line);
$style_normal = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line);

$layoutParams = [
    'block_header_height' => $block_header_height,
    'blockHeight'         => $blockHeight,
    'horizontalSpacerLN2' => $horizontalSpacerLN2,
    'SH'                  => $SH,
];


$pdf = new MYPDF('L', PDF_UNIT, "A3", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A3", "Raumbuch");
$pdf->AddPage('L', 'A3');
$pdf->SetFillColor(0, 0, 0, 0);
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);

$mysqli = utils_connect_sql();

$Params = [
    ['key' => 'Anwendungsgruppe',    'label' => 'ÖVE E8101:',       'unit' => '', 'cell' => $e_B_2rd - 15, 'str_cell' => $e_B_2rd / 2 + 15, 'side' => 'L'],
    ['key' => 'H6020',               'label' => 'H6020: ',           'unit' => '', 'cell' => $e_B_2rd,      'str_cell' => $e_B_2rd,           'side' => 'R'],
    ['key' => 'HT_Spuele_Stk',       'label' => 'Handwaschplätze: ', 'unit' => '', 'cell' => $e_B_2rd,      'str_cell' => $e_B_2rd - 10,      'side' => 'R'],
    ['key' => 'Fussboden OENORM B5220', 'label' => 'ÖNORM B5220: ',  'unit' => '', 'cell' => $e_B_2rd,      'str_cell' => $e_B_2rd,           'side' => 'R'],
];

$parameter_changes_t_räume = array();

$sql_mt = "SELECT tabelle_elemente.ElementID,
                tabelle_elemente.Bezeichnung,
                tabelle_varianten.Variante,
                Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
                tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
                tabelle_räume_has_tabelle_elemente.Standort,
                tabelle_räume_has_tabelle_elemente.Verwendung
            FROM tabelle_varianten
            INNER JOIN (tabelle_räume_has_tabelle_elemente
                INNER JOIN tabelle_elemente
                    ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente)
                ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante,
                     tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                     tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                     tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
                     tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = %d
                AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante";

foreach ($roomIDsArray as $valueOfRoomID) {
    $valueOfRoomID = (int)$valueOfRoomID;

    $sql_room = "SELECT tabelle_räume.idTABELLE_Räume,
            tabelle_räume.Raumnr,
            tabelle_räume.Raumbezeichnung,
            tabelle_räume.`Raumbereich Nutzer`,
            tabelle_räume.Geschoss,
            tabelle_räume.Bauetappe,
            tabelle_räume.`Fussboden OENORM B5220`,
            tabelle_räume.`Allgemeine Hygieneklasse`,
            tabelle_räume.Bauabschnitt,
            tabelle_räume.Nutzfläche,
            tabelle_räume.Strahlenanwendung,
            tabelle_räume.Laseranwendung,
            tabelle_räume.Anwendungsgruppe,
            tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV,
            tabelle_räume.H6020,
            tabelle_räume.HT_Waermeabgabe_W,
            tabelle_räume.HT_Spuele_Stk,
            tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`,
            tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`,
            tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`,
            tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O,
            tabelle_räume.`Anmerkung MedGas`,
            tabelle_räume.`Anmerkung Elektro`,
            tabelle_räume.`Anmerkung HKLS`,
            tabelle_räume.`Anmerkung Geräte`,
            tabelle_räume.`Anmerkung FunktionBO`,
            tabelle_räume.`Anmerkung BauStatik`,
            tabelle_projekte.Projektname,
            tabelle_planungsphasen.Bezeichnung,
            tabelle_räume.ET_Anschlussleistung_W
        FROM tabelle_planungsphasen
        INNER JOIN (tabelle_projekte
            INNER JOIN tabelle_räume
                ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
            ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
        WHERE tabelle_räume.idTABELLE_Räume = $valueOfRoomID";

    $result_rooms = $mysqli->query($sql_room);

    // MT vorab laden — Query-Ergebnis wird später wiederverwendet
    $resultX = $mysqli->query(sprintf($sql_mt, $valueOfRoomID));
    $rowcounter = $resultX ? $resultX->num_rows : 0;

    while ($row = $result_rooms->fetch_assoc()) {
        $pdf->SetFillColor(255, 255, 255);

        raum_header($pdf, $horizontalSpacerLN2, $SB, $row['Raumbezeichnung'], $row['Raumnr'],
            $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt'],
            "A3SAN", $parameter_changes_t_räume, "",
            $row["Nutzfläche"], $rowcounter, $layoutParams);

        //  ---------- Params -----------
        block_label_queer($block_header_w, $pdf, "ET & HKLS", $blockHeight, $block_header_height, $SB);
        foreach ($Params as $param) {
            $val = ($row[$param['key']] != "0") ? kify($row[$param['key']]) . $param['unit'] : "-";
            multicell_text_hightlight($pdf, $param['cell'], $font_size, $param['key'], $param['label'], $parameter_changes_t_räume, $param['side']);
            multicell_with_str($pdf, $val, $param['str_cell'], "", 10, "L");
        }
        $pdf->Ln($horizontalSpacerLN2);

        //  ------- MT Liste  ---------
        if ($rowcounter > 0) {
            $upcmn_blck_size = 10 + $rowcounter / 2 * 5;
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB);
            $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
            make_MT_list_San($pdf, $SB, $block_header_w, $rowcounter, $resultX, $style_normal, $style_dashed);
        } else {
            $pdf->Line(15, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_normal);
            block_label_queer($block_header_w, $pdf, "Med.-tech.", 0, $block_header_height, $SB);
            $pdf->Multicell(0, 0, "Keine medizintechnische Ausstattung.", "", "L", 0, 0);
            $pdf->Ln(5);
        }
    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Sanitätsrechtliche_Einreichung'), 'I');