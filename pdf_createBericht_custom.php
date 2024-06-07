<?php

//----------------------------- 
// 10.5.2024 
// Reuther & Fux
//----------------------------- 
// 
$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);

$PDF_input_bool = filter_input(INPUT_GET, 'PDFinputs');
$PDF_input_bools = explode(",", $PDF_input_bool); //foreach ($roomIDsArray as $l) { echo $l;echo " <br> ";}echo $roomIDsArray;

include 'pdf_createBericht_MYPDFclass.php'; //require_once('TCPDF-master/TCPDF-master/tcpdf.php'); is in class file
include 'pdf_createBericht_utils.php';
include '_utils.php';
if ($PDF_input_bools[8]) {
    include 'pdf_createMTTabelle.php';
}
session_start();
check_login();

//     -----   FORMATTING VARIABLES    -----  
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/ 
$SB = 210 - 2 * PDF_MARGIN_LEFT;  // A4: seitenbreite minus die lr einzüge = 180
$SH = 297 - $marginTop - PDF_MARGIN_FOOTER;
$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 5;
$horizontalSpacerLN3 = 8;
// A: Seite Halbieren; B: Seite dritteln; C:Seite/4; E=1/5; F = 1/6
$e_A = $SB / 2;
$e_A_3rd = $e_A / 3;
$e_A_2_3rd = $e_A - $e_A_3rd;
$e_B = $SB / 3;
$e_B_3rd = $e_B / 3;
$e_B_2_3rd = $e_B - $e_B_3rd;
$e_C = $SB / 4;
$e_C_3rd = $e_C / 3;
$e_C_2_3rd = $e_C - $e_C_3rd;
$e_D = $SB / 5;
$e_D_3rd = $e_D / 3;
$e_D_2_3rd = $e_D - $e_D_3rd;

$e_E = $SB / 6; //=30
$e_E_3rd = $e_E / 3; // = 10 
$e_E_2_3rd = $e_E - $e_E_3rd; //= 20

$hackerl_Zellgröße = $e_E_3rd; //=10
$hackerl_schriftgröße = $e_E_3rd;

$block_header_height = 10;   //
$blockSpaceNeededX = 100; //

$manual_offset = 5; //remove if possiblen 
// PDF: Klasse initiiert das Titelblatt; foreachRoom-> neues Blattl
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop);

//// SQL CONNECTION 
$mysqli = utils_connect_sql();

foreach ($roomIDsArray as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');
    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, 
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP, 
            tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
            tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, 
            tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, 
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`,  tabelle_räume.`Fussboden`, ROUND(tabelle_räume.`Umfang`,2) AS Umfang, 
            ROUND(tabelle_räume.`Volumen`,2) AS Volumen,
            tabelle_räume.`Raumhoehe`, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Belichtungsfläche`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung,
            tabelle_räume.`EL_AV Steckdosen Stk`, tabelle_räume.`EL_USV Steckdosen Stk`, tabelle_räume.`EL_SV Steckdosen Stk`,
            tabelle_räume.`EL_Einzel-Datendose Stk`, tabelle_räume.`EL_Doppeldatendose Stk`,
            tabelle_räume.`EL_Bodendose Typ`, tabelle_räume.`EL_Bodendose Stk`,
            tabelle_räume.`EL_Beleuchtung 1 Typ`, tabelle_räume.`EL_Beleuchtung 2 Typ`, tabelle_räume.`EL_Beleuchtung 3 Typ`, tabelle_räume.`EL_Beleuchtung 4 Typ`, tabelle_räume.`EL_Beleuchtung 5 Typ`, 
            tabelle_räume.`EL_Beleuchtung 1 Stk`, tabelle_räume.`EL_Beleuchtung 2 Stk`, tabelle_räume.`EL_Beleuchtung 3 Stk`, tabelle_räume.`EL_Beleuchtung 4 Stk`, tabelle_räume.`EL_Beleuchtung 5 Stk`,
            tabelle_räume.`EL_Lichtschaltung BWM JA/NEIN`, tabelle_räume.`EL_Beleuchtung dimmbar JA/NEIN`,
            tabelle_räume.`EL_Brandmelder Decke JA/NEIN`, tabelle_räume.`EL_Brandmelder ZwDecke JA/NEIN`,
            tabelle_räume.`EL_Kamera Stk`, tabelle_räume.`EL_Lautsprecher Stk`, tabelle_räume.`EL_Uhr - Wand Stk`, tabelle_räume.`EL_Uhr - Decke Stk`, tabelle_räume.`EL_Lichtruf - Terminal Stk`, tabelle_räume.`EL_Lichtruf - Steckmodul Stk`,
            tabelle_räume.`EL_Lichtfarbe K`,
            tabelle_räume.`EL_Notlicht RZL Stk`, tabelle_räume.`EL_Notlicht SL Stk`, tabelle_räume.`EL_Jalousie JA/NEIN`,
            tabelle_räume.`HT_Luftmenge m3/h`, CAST(REPLACE(tabelle_räume.`HT_Luftwechsel 1/h`,',','.') as decimal(10,2)) AS `HT_Luftwechsel`, tabelle_räume.`HT_Kühlung Lueftung W`, tabelle_räume.`HT_Heizlast W`, tabelle_räume.`HT_Kühllast W`, tabelle_räume.`HT_Fussbodenkühlung W`,
            tabelle_räume.`HT_Kühldecke W`, tabelle_räume.`HT_Fancoil W`, tabelle_räume.`HT_Summe Kühlung W`, tabelle_räume.`HT_Raumtemp Sommer °C`, tabelle_räume.`HT_Raumtemp Winter °C`,
            tabelle_räume.`AR_Ausstattung`, tabelle_räume.`Aufenthaltsraum`
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "));";

    $result_rooms = $mysqli->query($sql);
    while ($row = $result_rooms->fetch_assoc()) {

        raum_header($pdf, $horizontalSpacerLN2, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt']); //utils function 


        if (strlen($row['Anmerkung FunktionBO']) > 0 && $PDF_input_bools[2]) {
            block_label($pdf, "BO-Beschreibung", $block_header_height);
            $outstr = format_text(clean_string(br2nl($row['Anmerkung FunktionBO'])));
            $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
            check_4_new_page($pdf, $rowHeightComment);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->MultiCell($SB, $rowHeightComment, $outstr, 0, 'L', 0, 1);
        }

        if ($PDF_input_bools[3]) {
            block_label($pdf, "Allgemein", $block_header_height);

            $pdf->MultiCell($e_B_2_3rd, 6, "Raumfläche: ", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['Nutzfläche'], "m²", 10, $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "H6020: ", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['H6020'], "m", $pdf->getFontSizePt(), $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Abdunkelbarkeit: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['Abdunkelbarkeit'], "JA");

            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($e_B_2_3rd, 6, "Raumhöhe 1: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['Raumhoehe'], $e_B_3rd, "m");
            $pdf->MultiCell($e_B_2_3rd, 6, "Belichtungsfläche: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['Belichtungsfläche'], $e_B_3rd, "m²");
            $pdf->MultiCell($e_B_2_3rd, 6, "Aufenthaltsr.: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['Aufenthaltsraum'], "JA");
            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($e_B_2_3rd, 6, "Raumhöhe 2: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['Raumhoehe 2'], $e_B_3rd, "m");
            $pdf->MultiCell($e_B_2_3rd, 6, "Umfang: ", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['Umfang'], "m", $pdf->getFontSizePt(), $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Laseranwendung: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['Laseranwendung'], "JA");
            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($e_B_2_3rd, 6, "Raumvolumen: ", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['Volumen'], "m³", $pdf->getFontSizePt(), $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Fußboden: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['Fussboden'], $e_B_3rd, "");
            $pdf->MultiCell($e_B_2_3rd, 6, "Strahlenanwendung: ", 0, 'R', 0, 0);
            strahlenanw($pdf, $row['Strahlenanwendung'], $e_B_3rd, $hackerl_schriftgröße);
            if (($pdf->getStringHeight($e_B_3rd, $row['Fussboden'])) > 6 || ($pdf->getStringHeight($e_B_3rd, $row['Strahlenanwendung'])) > 6) {
                $pdf->Ln($horizontalSpacerLN);
            } 
            $pdf->Ln();
        }

        if ($PDF_input_bools[4]) {

            $SizeElektroSegement = 60 + $block_header_height + 9 * $horizontalSpacerLN;

            block_label($pdf, "Elektro", $block_header_height);

            $restspace = (($SB - $e_E - $hackerl_Zellgröße) / 5) - $hackerl_Zellgröße - 2;

            $pdf->MultiCell($e_E, 6, "ÖVE E8101:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['Anwendungsgruppe'], $hackerl_Zellgröße, "");

            $pdf->MultiCell($restspace, 6, "AV: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['AV'], "JA");
            $pdf->MultiCell($restspace, 6, "SV: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['SV'], "JA");
            $pdf->MultiCell($restspace, 6, "ZSV: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['ZSV'], "JA");
            $pdf->MultiCell($restspace, 6, "USV: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['USV'], "JA");
            $pdf->MultiCell($restspace, 6, "IT: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['IT Anbindung'], "JA");
            $pdf->Ln($horizontalSpacerLN);
            $pdf->SetFont('helvetica', '', 6);

            $manual_offset = 2;
            $pdf->MultiCell($e_E + $hackerl_Zellgröße + $restspace - $manual_offset, 6, "SSD:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_AV Steckdosen Stk'], $hackerl_Zellgröße, "");
            $pdf->MultiCell($restspace, 6, "SSD:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_SV Steckdosen Stk'], $hackerl_Zellgröße, "");
            $pdf->MultiCell($restspace, 6, "SSD:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_USV Steckdosen Stk'], $hackerl_Zellgröße, "");
            $pdf->MultiCell($restspace, 6, "SSD:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_USV Steckdosen Stk'], $hackerl_Zellgröße, "");
            $pdf->MultiCell($restspace, 6, "ED:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_Einzel-Datendose Stk'], $hackerl_Zellgröße, "Stk.");
            $pdf->Ln($horizontalSpacerLN / 2);
            $pdf->MultiCell($e_E + $restspace + $hackerl_Zellgröße - $manual_offset, 6, "BD:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_Bodendose Stk'], $hackerl_Zellgröße, "");
            $pdf->MultiCell(($restspace + $hackerl_Zellgröße) * 3, 6, " ", 0, 'L', 0, 0);
            $pdf->MultiCell($restspace, 6, "DD:", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_Doppeldatendose Stk'], $hackerl_Zellgröße, "Stk.");
            $pdf->Ln($horizontalSpacerLN);
            dashed_line($pdf, 0);
            $pdf->Ln($horizontalSpacerLN / 2);

            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($e_E, 10, "Beleuchtung:", 0, 'R', 0, 0);
            $pdf->SetFont('helvetica', '', 8);

            $manual_offset = 6;
            $restspace = (($SB - $e_E - $hackerl_Zellgröße) / 3) - $manual_offset;

            $pdf->MultiCell($restspace - (2 * $manual_offset), 6, "Dimmbar: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['EL_Beleuchtung dimmbar JA/NEIN'], "JA");
            $pdf->MultiCell($restspace, 6, "Bewegungsmelder: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['EL_Lichtschaltung BWM JA/NEIN'], "JA");
            $pdf->MultiCell($restspace, 6, "Rettungszeichenleuchte: ", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Notlicht RZL Stk'], $hackerl_Zellgröße);
            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($restspace + $e_E - (2 * $manual_offset), 6, "Lichtfarbe: ", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['EL_Lichtfarbe K'], "K", 8, $hackerl_Zellgröße);
            $pdf->MultiCell($restspace, 6, "Sicherheitsleuchte: ", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Notlicht SL Stk'], $hackerl_Zellgröße);
            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($e_E + $restspace - (2 * $manual_offset), 6, "Leuchten: ", 0, 'R', 0, 0);

            $unsauberer_temp = ($SB - $e_E - $restspace - 25) / 5;
            $leuchten_printout = false;
            if ($row['EL_Beleuchtung 1 Stk'] > 0) {
                $pdf->MultiCell($unsauberer_temp, 6, $row['EL_Beleuchtung 1 Stk'] . " Stk, " . $row['EL_Beleuchtung 1 Typ'] . ".", 0, 'L', 0, 0);
                $leuchten_printout = true;
            }
            if ($row['EL_Beleuchtung 2 Stk'] > 0) {
                $pdf->MultiCell($unsauberer_temp, 6, $row['EL_Beleuchtung 2 Stk'] . " Stk, " . $row['EL_Beleuchtung 2 Typ'] . ".", 0, 'L', 0, 0);
                $leuchten_printout = true;
            }
            if ($row['EL_Beleuchtung 3 Stk'] > 0) {
                $pdf->MultiCell($unsauberer_temp, 6, $row['EL_Beleuchtung 3 Stk'] . " Stk, " . $row['EL_Beleuchtung 3 Typ'] . ".", 0, 'L', 0, 0);
                $leuchten_printout = true;
            }
            if ($row['EL_Beleuchtung 4 Stk'] > 0) {
                $pdf->MultiCell($unsauberer_temp, 6, $row['EL_Beleuchtung 4 Stk'] . " Stk, " . $row['EL_Beleuchtung 4 Typ'] . ".", 0, 'L', 0, 0);
                $leuchten_printout = true;
            }
            if ($row['EL_Beleuchtung 5 Stk'] > 0) {
                $pdf->MultiCell($unsauberer_temp, 6, $row['EL_Beleuchtung 5 Stk'] . " Stk, " . $row['EL_Beleuchtung 5 Typ'] . ".", 0, 'L', 0, 0);
                $leuchten_printout = true;
            }

            //MULTIMEDIA 
            $pdf->Ln($horizontalSpacerLN);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($e_E, 6, "Multimedia:", 0, 'R', 0, 0);
            $pdf->SetFont('helvetica', '', 8);

            $pdf->MultiCell($restspace - (2 * $manual_offset + 1), 6, "Kamera:", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Kamera Stk'], $hackerl_Zellgröße);
            $pdf->MultiCell($restspace, 6, "Lautsprecher:", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Lautsprecher Stk'], $hackerl_Zellgröße);
            $pdf->MultiCell($restspace, 6, "Wanduhr:", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Uhr - Wand Stk'], $hackerl_Zellgröße);

            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($restspace - (2 * $manual_offset + 1) + $e_E, 6, "Deckenuhr:", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Uhr - Decke Stk'], $hackerl_Zellgröße);
            $pdf->MultiCell($restspace, 6, "Lichtruf-Terminal:", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Lichtruf - Terminal Stk'], $hackerl_Zellgröße);
            $pdf->MultiCell($restspace, 6, "Lichtruf-Modul:", 0, 'R', 0, 0);
            multicell_with_stk($pdf, $row['EL_Lichtruf - Steckmodul Stk'], $hackerl_Zellgröße);
            $pdf->Ln($horizontalSpacerLN);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($e_E, 6, "Brandmelder:", 0, 'R', 0, 0);
            $pdf->SetFont('helvetica', '', 8);

            $pdf->MultiCell($restspace - (2 * $manual_offset), 6, "Decke: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['EL_Brandmelder Decke JA/NEIN'], "JA");
            $pdf->MultiCell($restspace, 6, "Zwischendecke: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['EL_Brandmelder ZwDecke JA/NEIN'], "JA");

            $pdf->Ln($horizontalSpacerLN);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($e_E, 6, "Jalousie:", 0, 'R', 0, 0);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->MultiCell($restspace - (2 * $manual_offset), 6, "Elektrisch:", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['EL_Jalousie JA/NEIN'], "JA");
            $next_block_size = $block_header_height + 40; //manually added up //TODO account for all the space taken up beforehand
            newpage_or_spacer($pdf, $next_block_size);
        }

        if ($PDF_input_bools[5]) { //HT
            block_label($pdf, "Haustechnik", $block_header_height);
            $pdf->MultiCell($e_B_2_3rd, 6, "Kühllast:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Kühllast W'], "W", 10, $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Raumtemp-Winter:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Raumtemp Winter °C'], "°C", 10, $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Heizlast:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Heizlast W'], "W", 10, $e_B_3rd);

            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($e_B_2_3rd, 6, "Kühlung-Lüftung:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Kühlung Lueftung W'], "W", 10, $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Raumtemp-Sommer:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Raumtemp Sommer °C'], "°C", 10, $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Luftwechsel:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Luftwechsel'], "1/h", 8, $e_B_3rd);

            $pdf->Ln($horizontalSpacerLN);

            $pdf->MultiCell($e_B_2_3rd, 6, "Kühlung-FB:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Fussbodenkühlung W'], "W", 10, $e_B_3rd);
            $pdf->MultiCell($e_B_2_3rd, 6, "Luftmenge:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Luftmenge m3/h'], "m³/h", 10, $e_B_3rd * 3);

            $pdf->Ln($horizontalSpacerLN);

            $pdf->MultiCell($e_B_2_3rd, 6, "Kühlung-Decke:", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['HT_Kühldecke W'], "W", 10, $e_B_3rd);
            //$pdf->MultiCell($e_B_2_3rd, 6, "Summe Kühlung:", 0, 'R', 0, 0);
            //multicell_with_nr($pdf, $row['HT_Summe Kühlung W'], "W", 8, 20);
            //$pdf->MultiCell($e_B_2_3rd, 6, "Fancoil:", 0, 'R', 0, 0);
            //multicell_with_nr($pdf, $row['HT_Fancoil W'], "W", 8, $e_B_3rd);

            $pdf->SetFont('helvetica', '', 10);
            $outstr = format_text(clean_string(br2nl($row['Anmerkung HKLS'])));
            if (strlen($outstr) > 0 && is_not_no_comment($outstr)) { //Haustechnik anmerkung
                $pdf->Ln($horizontalSpacerLN2);
                $rowHeightComment = $pdf->getStringHeight($SB - $e_E, $outstr, false, true, '', 1);

                check_4_new_page($pdf, $rowHeightComment);

                $pdf->MultiCell($e_B_2_3rd, $rowHeightComment, "Anmerkung:", 0, 'R', 0, 0);
                $pdf->MultiCell($SB - $e_B_2_3rd, $rowHeightComment, $outstr, 0, 'L', 0, 0);
                $pdf->Ln($rowHeightComment);
            }
            newpage_or_spacer($pdf, $next_block_size);
        }

        if ($PDF_input_bools[6]) { //MEDGAS
            block_label($pdf, "Med.-Gas", $block_header_height);

            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "1 Kreis O2: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['1 Kreis O2'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "2 Kreis O2: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['2 Kreis O2'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "1 Kreis VA: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['1 Kreis Va'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "2 Kreis VA: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['2 Kreis Va'], 1);

            $pdf->Ln($horizontalSpacerLN);

            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "1 Kreis DL5: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['1 Kreis DL-5'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "2 Kreis DL5: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['2 Kreis DL-5'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "DL10: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['DL-10'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "DL-tech: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['DL-tech'], 1);

            $pdf->Ln($horizontalSpacerLN);

            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "CO2: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['CO2'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "N2O: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['N2O'], 1);
            $pdf->MultiCell($e_C - $hackerl_Zellgröße, 6, "NGA: ", 0, 'R', 0, 0);
            hackerl($pdf, $hackerl_schriftgröße, $hackerl_Zellgröße, $row['NGA'], 1);

            $outstr = format_text(br2nl($row['Anmerkung MedGas']));
            if (strlen($outstr) > 0 && is_not_no_comment($outstr)) {
                $pdf->Ln($horizontalSpacerLN2);
                $rowHeightComment = $pdf->getStringHeight($SB - $e_E, $outstr, false, true, '', 1);
                check_4_new_page($pdf, $rowHeightComment);
                $pdf->MultiCell($e_E, $rowHeightComment, "Anmerkung:", 0, 'R', 0, 0);
                $pdf->MultiCell($SB - $e_E, $rowHeightComment, $outstr, 0, 'L', 0, 1);
            }

            if (strlen($row['AR_Ausstattung']) > 0) {

                block_label($pdf, "Architektur-Einrichtung", $block_header_height);
                $pdf->SetFont('helvetica', '', 10);
                $rowHeightComment = $pdf->getStringHeight(140, br2nl($row['AR_Ausstattung']), false, true, '', 1);
                check_4_new_page($pdf, $rowHeightComment);
                $pdf->MultiCell($SB, $rowHeightComment, br2nl($row['AR_Ausstattung']), 0, 'L', 0, 0);
                $pdf->Ln();
            }

//            newpage_or_spacer($pdf, $rowHeightComment);
        }

        if ($PDF_input_bools[7]) {//BAUSTATIK
            block_label($pdf, "Baustatik", $block_header_height);
        }

//        if ($PDF_input_bools[8]) { //MT Tabelle
//            make_MT_details_table($pdf, $mysqli, $valueOfRoomID, $block_header_height, $SB);
//        }

        if ($PDF_input_bools[9] || $PDF_input_bools[8]) {//MT LISTE
            $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

            $result = $mysqli->query($sql);

            $next_block_size = $block_header_height + 10 * ($result->num_rows);
            newpage_or_spacer($pdf, $next_block_size);

            block_label($pdf, "Medizintechnik");
            if ($result->num_rows > 0) {
                el_in_room_html_table($pdf, $result, 10);
            } else {
                $pdf->MultiCell($SB, 10, " Keine Elemente im Raum. ", 0, 'C', 0, 1);
            }
        }
    } //sql:fetch-assoc
}// for every room 






$mysqli->close();
ob_end_clean();
$pdf->Output('Raumbuch-MT.pdf', 'I');
