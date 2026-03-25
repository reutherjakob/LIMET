<?php

require_once '../utils/_utils.php';
check_login();

include 'pdf_createBericht_MYPDFclass_A3Queer.php'; //require_once('../TCPDF-main/TCPDF-main/tcpdf.php'); is in class file
include '_pdf_createBericht_utils.php';

$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);
$Änderungsdatum = getValidatedDateFromURL();


$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$SB = 420 - (PDF_MARGIN_LEFT * 2);  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;

$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 5;

$e_B = $SB / 8;
$e_B_3rd = $e_B / 3;

$font_size = 6;
$block_header_height = 10;
$block_header_w = 25;


$colour_line = array(110, 150, 80);
$style_dashed = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line); //$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 6, 'color' => array(110, 150, 80)));
$style_normal = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line);

/** @noinspection PhpUndefinedConstantInspection */
$pdf = new MYPDF('L', PDF_UNIT, "A3", true, 'UTF-8', false, true);
/** @noinspection PhpUndefinedConstantInspection */


$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A3", "Bauangaben");
$pdf->AddPage('L', 'A3');
$pdf->SetFillColor(0, 0, 0, 0); //$pdf->SetFillColor(244, 244, 244);
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);


$mysqli = utils_connect_sql();
$isnotVorentwurf = $_SESSION["projectPlanungsphase"] !== "Vorentwurf";
$elektroParams = [
    // Always show these
    ['key' => 'Anwendungsgruppe', 'label' => 'ÖVE E8101:', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B_3rd, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'AV', 'label' => 'AV: ', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B_3rd, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'SV', 'label' => 'SV: ', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B_3rd, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'ZSV', 'label' => 'ZSV: ', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B_3rd, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'USV', 'label' => 'USV: ', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B_3rd, 'ln_after' => false, 'isnotVorentwurf' => false],
];

$haustechnikParams = [
    ['key' => 'H6020', 'label' => 'H6020: ', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B_3rd, 'ln_after' => false],
    ['key' => 'HT_Waermeabgabe_W', 'label' => 'Abwärme MT: ', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B+ $e_B_3rd, 'ln_after' => false],
    ['key' => 'HT_Spuele_Stk', 'label' => 'Handwaschplätze: ', 'unit' => '', 'cell' => $e_B, 'str_cell' => $e_B_3rd, 'ln_after' => false],
];

$parameter_changes_t_räume = array();
foreach ($roomIDsArray as $valueOfRoomID) {

    $sql = "SELECT tabelle_räume.idTABELLE_Räume, 
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
tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
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
WHERE tabelle_räume.idTABELLE_Räume = " . $valueOfRoomID . ";
";

    $result_rooms = $mysqli->query($sql);

    while ($row = $result_rooms->fetch_assoc()) {

        $pdf->SetFillColor(255, 255, 255);
        raum_header($pdf, $horizontalSpacerLN2, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt'], "A3XC", $parameter_changes_t_räume, $fstelle = "", $Flaeche = $row["Nutzfläche"]); //utils function

        //   ---------- ALLGEMEIN   ----------

        block_label_queer($block_header_w, $pdf, "Allgemein", $horizontalSpacerLN2 + 6, $block_header_height, $SB);
        multicell_text_hightlight($pdf, $e_B, $font_size, 'Fussboden OENORM B5220', "Ö NORM B5220: ", $parameter_changes_t_räume);
        multicell_with_str($pdf, $row['Fussboden OENORM B5220'], $e_B_3rd, "");
        $heightExceeds = false;
        multicell_text_hightlight($pdf, $e_B, $font_size, "Allgemeine Hygieneklasse", "Hygieneklasse: ", $parameter_changes_t_räume);
        if ($row['Allgemeine Hygieneklasse'] != "") {
            $heightExceeds = $pdf->getStringHeight($e_B_3rd * 4, $row['Allgemeine Hygieneklasse'], false, true, '', 1) > 6 ? true : false;
            multicell_with_str($pdf, $row['Allgemeine Hygieneklasse'], $e_B_3rd * 4, "");

        } else {
            multicell_with_str($pdf, " - ", $e_B_3rd, "");
        }

        multicell_text_hightlight($pdf, $e_B, $font_size, 'Strahlenanwendung', "Strahlenanw.: ", $parameter_changes_t_räume);
        if (($pdf->getStringHeight($e_B_3rd, $row['Strahlenanwendung'])) > 6) {
            strahlenanw($pdf, $row['Strahlenanwendung'], 4 * $e_B_3rd, $font_size);
        } else {
            strahlenanw($pdf, $row['Strahlenanwendung'], $e_B_3rd, $font_size);
        }

        multicell_text_hightlight($pdf, $e_B, $font_size, "Laseranwendung", "Laseranw.: ", $parameter_changes_t_räume);
        hackerlA3($pdf, $font_size, $e_B_3rd, $row['Laseranwendung'], "JA");


        multicell_text_hightlight($pdf, $e_B, $font_size, "Nutzfläche", "Fläche: ", $parameter_changes_t_räume);
        multicell_with_nr($pdf, $row['Nutzfläche'], "m2", 10, 4 * $e_B_3rd);
        $pdf->Ln($horizontalSpacerLN2);
        if ($heightExceeds) {
            $pdf->Ln($horizontalSpacerLN);
        }


        //       ---------- ELEKTRO -----------

        $i = 12 + $horizontalSpacerLN + $horizontalSpacerLN2;
        $blockHeight = 6 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung Elektro'], $SB) + $i;
        block_label_queer($block_header_w, $pdf, "Elektro", $blockHeight, $block_header_height, $SB);


        foreach ($elektroParams as $param) {
            if ($param['isnotVorentwurf'] && !$isnotVorentwurf) {
                if ($param['key'] === "ET_Anschlussleistung_USV_W") {
                    $pdf->Ln($horizontalSpacerLN2);
                }
            }
            if (!$isnotVorentwurf &&
                in_array($param['key'], ['ET_RJ45-Ports', 'EL_Laser 16A CEE Stk', 'EL_Roentgen 16A CEE Stk', 'RaumAnschlussLeistungInklGlz'])) {
                continue;
            } else if ($param['key'] == 'RaumAnschlussLeistungInklGlz') {
                include "pdf_getRaumleistungInklGlz.php";
                $pdf->Ln($horizontalSpacerLN2);
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);

            } else if (in_array($param['key'], ['Anwendungsgruppe', 'ET_Anschlussleistung_W', 'ET_Anschlussleistung_AV_W', 'ET_Anschlussleistung_SV_W', 'ET_Anschlussleistung_ZSV_W', 'ET_Anschlussleistung_USV_W'])) {
                $val = ($row[$param['key']] != "0") ? kify($row[$param['key']]) . $param['unit'] : "-";
                multicell_text_hightlight($pdf, $param['cell'], $font_size, $param['key'], $param['label'], $parameter_changes_t_räume);
                multicell_with_str($pdf, $val, $param['str_cell'], "");

            } else if (in_array($param['key'], ['ET_RJ45-Ports', 'EL_Laser 16A CEE Stk', 'EL_Roentgen 16A CEE Stk'])) {
                multicell_text_hightlight($pdf, $param['cell'], $font_size, $param['key'], $param['label'], $parameter_changes_t_räume);
                if ($param['key'] === 'ET_RJ45-Ports') {
                    multicell_with_nr($pdf, $row[$param['key']], $param['unit'], $pdf->getFontSizePt(), $param['str_cell']);
                } else {
                    multicell_with_str($pdf, $row[$param['key']], $param['str_cell'], $param['unit']);
                }
            } else {
                multicell_text_hightlight($pdf, $param['cell'], $font_size, $param['key'], $param['label'], $parameter_changes_t_räume);
                hackerlA3($pdf, $font_size, $param['str_cell'], $row[$param['key']], "JA");
            }
            if ($param['ln_after']) {
                $pdf->Ln($horizontalSpacerLN2);        // Print label placeholder for power section (only once)
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            }
            if ($param['key'] === "ET_Anschlussleistung_USV_W" && !$isnotVorentwurf) {
                $pdf->Ln($horizontalSpacerLN2);
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            }
        }


        anmA3($pdf, $row['Anmerkung Elektro'], $SB, $block_header_w);
        $pdf->Ln($horizontalSpacerLN2);

//
//// ---------- HAUSTEK ---------
//
        $Block_height = 6 + $horizontalSpacerLN2 + getAnmHeight($pdf, $row['Anmerkung HKLS'], $SB);
        block_label_queer($block_header_w, $pdf, "Haustechnik", $Block_height, $block_header_height, $SB);

        foreach ($haustechnikParams as $param) {
            multicell_text_hightlight($pdf, $param['cell'], $font_size, $param['key'], $param['label'], $parameter_changes_t_räume);

            if ($param['key'] === 'HT_Waermeabgabe_W') {
                $value = (
                    $row['HT_Waermeabgabe_W'] === "0" ||
                    $row['HT_Waermeabgabe_W'] == 0 ||
                    $row['HT_Waermeabgabe_W'] == "-"
                ) ? "keine Angabe" : kify($row['HT_Waermeabgabe_W']) . "W";
            } elseif ($param['key'] === 'VE_Wasser') {
                $value = translate_1_to_yes($row['VE_Wasser']);
            } else {
                $value = $row[$param['key']];
            }

            multicell_with_str($pdf, $value, $param['str_cell'], $param['unit']);

            if (!empty($param['ln_after'])) {
                $pdf->Ln($horizontalSpacerLN2);
                $pdf->Multicell($block_header_w, 1, "", 0, 0, 0, 0);
            }
        }


        $pdf->Ln($horizontalSpacerLN2);
        $ln_soll = anmA3($pdf, $row['Anmerkung HKLS'], $SB, $block_header_w);
        if ($ln_soll) {
            $pdf->Ln($horizontalSpacerLN);
        }

/// ----------- MEDGAS -----------
//
        $Block_height = 12 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung MedGas'], $SB);
        block_label_queer($block_header_w, $pdf, "Med.-Gas", $Block_height, $block_header_height, $SB);

        $medGasItems = [
            '1 Kreis O2', '1 Kreis Va', '1 Kreis DL-5', 'NGA', 'CO2',
            '2 Kreis O2', '2 Kreis Va', '2 Kreis DL-5', 'DL-10', 'N2O',
        ];

        foreach ($medGasItems as $item) {
            $label = str_replace(['1 Kreis ', '2 Kreis ', '-'], ['1 Kreis   ', '2 Kreise ', ''], $item);
            multicell_text_hightlight($pdf, $e_B, $font_size, $item, "$label: ", $parameter_changes_t_räume);
            hackerlA3($pdf, $font_size, $e_B_3rd, $row[$item], 1);

            if ($item === 'CO2') {
                $pdf->Ln($horizontalSpacerLN);
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            }
        }

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung MedGas'], $SB, $block_header_w);


////     ------- BauStatik ---------
        $anm = trim($row['Anmerkung BauStatik']?? '');
        if ($anm !== '' && $anm !== 'Keine Anmerkung' && $anm !== 'keine Angaben MT') {
            $pdf->Ln($horizontalSpacerLN);
            $Block_height = getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB);
            block_label_queer($block_header_w, $pdf, "Baustatik", $Block_height, $block_header_height, $SB);
            $pdf->Ln(1);
            anmA3($pdf, $row['Anmerkung BauStatik'], $SB, $block_header_w);
            $pdf->Ln($horizontalSpacerLN);
        }

//         ------- MT Liste  ---------

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
        $rowcounter = 0;
        while ($row2 = $resultX->fetch_assoc()) {
            $rowcounter++;
        }
        $resultX->data_seek(0);
        $upcmn_blck_size = 0;

        if ($rowcounter > 0) {
            $upcmn_blck_size = 10 + $rowcounter / 2 * 5;
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB); //el_in_room_html_table($pdf, $resultX, 1, "A3", $SB-$block_header_w);
            $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
            make_MT_list($pdf, $SB, $block_header_w, $rowcounter, $resultX, $style_normal, $style_dashed);
        } else {
            $pdf->Line(15, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_normal);
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB); //el_in_room_html_table($pdf, $resultX, 1, "A3", $SB-$block_header_w);
            $pdf->Multicell(0, 0, "Keine medizintechnische Ausstattung.", "", "L", 0, 0);
            $pdf->Ln(10);
        }
    } //sql:fetch-assoc
}// for every room

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Sanitätsrechtliche_Einreichung'), 'I');


