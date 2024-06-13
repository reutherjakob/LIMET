<?php

//----------------------------- 
// 10.5.2024
// Reuther & Fux
//----------------------------- 

include 'pdf_createBericht_MYPDFclass.php'; //require_once('TCPDF-master/TCPDF-master/tcpdf.php'); is in class file
include 'pdf_createBericht_utils.php';
include '_utils.php';

session_start();
check_login();
//if ($PDF_input_bools[8]) {
include 'pdf_createMTTabelle.php'; //}


//Input DATA from frontend-page
$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);

//$PDF_input_bool = filter_input(INPUT_GET, 'PDFinputs');
//$PDF_input_bools = explode(",", $PDF_input_bool); //foreach ($roomIDsArray as $l) { echo $l;echo " <br> ";}echo $roomIDsArray;
$Änderungsdatum = getValidatedDateFromURL();



//     -----   FORMATTING VARIABLES    -----     
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/ 
$marginBTM = 10;
$SB = 420 - 2 * PDF_MARGIN_LEFT;  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;
$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 5;
$horizontalSpacerLN3 = 8;

// B: Seite dritteln; C:Seite/4; E=1/5; F = 1/6    -> weil auf A3, zusätzl jeweils halbierzt. 
$e_B = $SB / 6;
$e_B_3rd = $e_B / 3;
$e_B_2_3rd = $e_B - $e_B_3rd;

$e_C = $SB / 8;
$e_C_3rd = $e_C / 3;
$e_C_2_3rd = $e_C - $e_C_3rd;

//$e_D = $SB / 10;
//$e_D_3rd = $e_D / 3;
//$e_D_2_3rd = $e_D - $e_D_3rd;

$e_E = $SB / 12; // 
$e_E_3rd = $e_E / 3; // = 10 
$e_E_2_3rd = $e_E - $e_E_3rd; //= 20

$font_size = 10;
$hackerl_Zellgröße = 10; //=10
$hackerl_schriftgröße = $e_E_3rd;

$block_header_height = 10;   //
$block_header_w = 25;

$einzugPlus = 10; // um den text auf die Höhe der Anderen Angaben zu shiften bei ANM BO
//TODO newpage_or_spacerA3!!
// PDF: Klasse initiiert das Titelblatt; foreachRoom-> neues Blatt
$pdf = new MYPDF('L', PDF_UNIT, "A3", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A3");

//// SQL CONNECTION 
$mysqli = utils_connect_sql();
$pdf->AddPage('L', 'A3');
foreach ($roomIDsArray as $valueOfRoomID) {

    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, 
            tabelle_räume.`Fussboden OENORM B5220` ,  tabelle_räume.`Allgemeine Hygieneklasse` , tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP, 
            tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
            tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, 
            tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, 
            tabelle_räume.HT_Waermeabgabe_W, tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`,  tabelle_räume.`Fussboden`, ROUND(tabelle_räume.`Umfang`,2) AS Umfang, 
            ROUND(tabelle_räume.`Volumen`,2) AS Volumen,
            tabelle_räume.`Raumhoehe`, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Belichtungsfläche`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung,
            tabelle_räume.ET_Anschlussleistung_W, 
            tabelle_räume.ET_Anschlussleistung_AV_W,
            tabelle_räume.ET_Anschlussleistung_SV_W,
            tabelle_räume.ET_Anschlussleistung_ZSV_W,
            tabelle_räume.ET_Anschlussleistung_USV_W,
            tabelle_räume.`EL_AV Steckdosen Stk`,
            tabelle_räume.`EL_SV Steckdosen Stk`,
            tabelle_räume.`EL_ZSV Steckdosen Stk`, 
            tabelle_räume.`EL_USV Steckdosen Stk`,
            tabelle_räume.`ET_RJ45-Ports`,
            tabelle_räume.`EL_Roentgen 16A CEE Stk`,
            tabelle_räume.`EL_Laser 16A CEE Stk`,
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
    // $pdf->SetFont('helvetica', '', 10);

    while ($row = $result_rooms->fetch_assoc()) {
        raum_header($pdf, $horizontalSpacerLN3, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt'], "A3"); //utils function 

        if (strlen($row['Anmerkung FunktionBO']) > 0) {

            $outstr = format_text(clean_string(br2nl($row['Anmerkung FunktionBO'])));
            $rowHeightComment = $pdf->getStringHeight($SB - $einzugPlus, $outstr, false, true, '', 1);
            $i = 0;
            if ($rowHeightComment > 6) {
                $i = $horizontalSpacerLN;
            }

            block_label_queer($block_header_w, $pdf, "BO-Beschr.", $rowHeightComment + $i, $block_header_height, $SB);

            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->MultiCell($einzugPlus, $rowHeightComment, "", 0, 'L', 0, 0);
            $pdf->MultiCell($SB - $einzugPlus, $rowHeightComment, $outstr, 0, 'L', 0, 1);
            if ($rowHeightComment > 6) {
                $pdf->Ln($horizontalSpacerLN);
            }
        }
//
//   ---------- ALLGEMEIN   ----------
//


        block_label_queer($block_header_w, $pdf, "Allgemein", $horizontalSpacerLN3 + 6, $block_header_height, $SB);

        $pdf->MultiCell($e_C_2_3rd, 6, "Ö NORM B5220: ", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['Fussboden OENORM B5220'], $e_C_3rd, "");

        $pdf->MultiCell($e_C_2_3rd, 6, "Hygieneklasse: ", 0, 'R', 0, 0);
        if ($row['Allgemeine Hygieneklasse'] != "") {
            multicell_with_str($pdf, $row['Allgemeine Hygieneklasse'], $e_C_3rd * 4, "");
        } else {
            multicell_with_str($pdf, " - ", $e_C_3rd, "");
        }

        $pdf->MultiCell($e_C_2_3rd, 6, "Strahlenanw.: ", 0, 'R', 0, 0);
        if (($pdf->getStringHeight($e_C_3rd, $row['Strahlenanwendung'])) > 6) {
            strahlenanw($pdf, $row['Strahlenanwendung'], 4 * $e_C_3rd, $hackerl_schriftgröße);
        } else {
            strahlenanw($pdf, $row['Strahlenanwendung'], $e_C_3rd, $hackerl_schriftgröße);
        }
        $pdf->MultiCell($e_C_2_3rd, 6, "Laseranw.: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['Laseranwendung'], "JA");

        $pdf->MultiCell($e_C_2_3rd, 6, "Abdunkelbarkeit: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['Abdunkelbarkeit'], "JA");

        $pdf->MultiCell($e_C_2_3rd, 6, "Raumfläche: ", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['Nutzfläche'], "m2", 10, 4 * $e_C_3rd);

        $pdf->Ln($horizontalSpacerLN3);

//        $pdf->MultiCell($e_C_2_3rd, 6, "Belichtungsfläche: ", 0, 'R', 0, 0);
//        multicell_with_str($pdf, $row['Belichtungsfläche'], $e_C_3rd, "m2");
//        $pdf->Ln($horizontalSpacerLN);
//        $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
//        $pdf->MultiCell($e_C_2_3rd, 6, "Raumhöhe 1: ", 0, 'R', 0, 0);
//        multicell_with_nr($pdf, $row['Raumhoehe'], "m", 10, $e_C_3rd,);
//        $pdf->MultiCell($e_C_2_3rd, 6, "Raumhöhe 2: ", 0, 'R', 0, 0);
//        multicell_with_str($pdf, $row['Raumhoehe 2'], $e_C_3rd, "m");
//        $pdf->MultiCell($e_C_2_3rd, 6, "Umfang: ", 0, 'R', 0, 0);
//        multicell_with_str($pdf, $row['Umfang'], $e_C_3rd, "m");
//        $pdf->MultiCell($e_C_2_3rd, 6, "Raumvolumen: ", 0, 'R', 0, 0);
//        multicell_with_nr($pdf, $row['Volumen'], "m³", $pdf->getFontSizePt(), $e_C_3rd);
//        $pdf->MultiCell($e_C_2_3rd, 6, "Fußboden: ", 0, 'R', 0, 0);
//        multicell_with_str($pdf, $row['Fussboden'], $e_C_3rd, "");
//        $pdf->MultiCell($e_C_2_3rd, 6, "Aufenthaltsr.: ", 0, 'R', 0, 0);
//        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['Aufenthaltsraum'], "JA");
//        $pdf->Ln($horizontalSpacerLN);$pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
//
//    ---------- ELEKTRO -----------
//
        if (($row['AV'] == 1 || $row['SV'] == 1 || $row['ZSV'] == 1 || $row['USV'] == 1)) {
            $i = 12 + $horizontalSpacerLN + $horizontalSpacerLN2;
        } else {
            $i = 0;
        }
        $Block_height = 6 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung Elektro'], $SB) + $i;

        block_label_queer($block_header_w, $pdf, "Elektro", $Block_height, $block_header_height, $SB);

        $pdf->MultiCell($e_C_2_3rd, 6, "ÖVE E8101:", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['Anwendungsgruppe'], $e_C_3rd, "");
        $pdf->MultiCell($e_C_2_3rd, 6, "AV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['AV'], "JA");
        $pdf->MultiCell($e_C_2_3rd, 6, "SV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['SV'], "JA");
        $pdf->MultiCell($e_C_2_3rd, 6, "ZSV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['ZSV'], "JA");
        $pdf->MultiCell($e_C_2_3rd, 6, "USV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['USV'], "JA");
        $pdf->MultiCell($e_C_2_3rd, 6, "IT Anschl.: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['IT Anbindung'], "JA");

        if ($row['EL_Roentgen 16A CEE Stk'] != "0") {
            $pdf->MultiCell($e_C_2_3rd, 6, "CEE16A Röntgen: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_Roentgen 16A CEE Stk'], $e_C_3rd, " Stk");
        }

        $pdf->Ln($horizontalSpacerLN);
        $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);

        $outsr = "";
        $pdf->MultiCell($e_C_2_3rd, 6, "Summe Leistung: ", 0, 'R', 0, 0);
        if ($row['ET_Anschlussleistung_W'] != "0") {
            $outsr = kify($row['ET_Anschlussleistung_W']) . "W";
        } else {
            $outsr = "-";
        }multicell_with_str($pdf, $outsr, $e_C_3rd, "");

        if (($row['AV'] == 1 || $row['SV'] == 1 || $row['ZSV'] == 1 || $row['USV'] == 1)) {  //&& ( ($row['ET_Anschlussleistung_AV_W'] != "0") || ($row['ET_Anschlussleistung_SV_W'] != "0") || ($row['ET_Anschlussleistung_USV_W'] != "0") || ($row['ET_Anschlussleistung_ZSV_W'] != "0") )) {
            $pdf->MultiCell($e_C_2_3rd, 6, "AV Leist.: ", 0, 'R', 0, 0);
            if ($row['ET_Anschlussleistung_AV_W'] != "0") {
                $outsr = kify($row['ET_Anschlussleistung_AV_W']) . "W";
            } else {
                $outsr = "-";
            }
            multicell_with_str($pdf, $outsr, $e_C_3rd, "");
            $pdf->MultiCell($e_C_2_3rd, 6, "SV Leist.: ", 0, 'R', 0, 0);
            if ($row['ET_Anschlussleistung_SV_W'] != "0") {
                $outsr = kify($row['ET_Anschlussleistung_SV_W']) . "W";
            } else {
                $outsr = "-";
            }
            multicell_with_str($pdf, $outsr, $e_C_3rd, "");
            $pdf->MultiCell($e_C_2_3rd, 6, "ZSV Leist.: ", 0, 'R', 0, 0);
            if ($row['ET_Anschlussleistung_ZSV_W'] != "0") {
                $outsr = kify($row['ET_Anschlussleistung_ZSV_W']) . "W";
            } else {
                $outsr = "-";
            }
            multicell_with_str($pdf, $outsr, $e_C_3rd, "");
            $pdf->MultiCell($e_C_2_3rd, 6, "USV Leist.: ", 0, 'R', 0, 0);
            if ($row['ET_Anschlussleistung_USV_W'] != "0") {
                $outsr = kify($row['ET_Anschlussleistung_USV_W']) . "W";
            } else {
                $outsr = "-";
            }
            multicell_with_str($pdf, $outsr, $e_C_3rd, "");

            $pdf->MultiCell($e_C_2_3rd, 6, "RJ45-Ports: ", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['ET_RJ45-Ports'], "Stk", $pdf->getFontSizePt(), $e_C_3rd);

            $pdf->MultiCell($e_C_2_3rd, 6, "CEE16A Laser: ", 0, 'R', 0, 0);
            multicell_with_nr($pdf, $row['EL_Laser 16A CEE Stk'], "Stk", $pdf->getFontSizePt(), $e_C_3rd);

            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($block_header_w + $e_C, $block_header_height, "", 0, 'L', 0, 0);

            $pdf->MultiCell($e_C_2_3rd, 6, "AV SSD: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_AV Steckdosen Stk'], $e_C_3rd, "Stk");
            $pdf->MultiCell($e_C_2_3rd, 6, "SV SSD: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_SV Steckdosen Stk'], $e_C_3rd, "Stk");
            $pdf->MultiCell($e_C_2_3rd, 6, "ZSV SSD: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_ZSV Steckdosen Stk'], $e_C_3rd, "Stk");
            $pdf->MultiCell($e_C_2_3rd, 6, "USV SSD: ", 0, 'R', 0, 0);
            multicell_with_str($pdf, $row['EL_USV Steckdosen Stk'], $e_C_3rd, "Stk");
        }

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung Elektro'], $SB, $block_header_w);
        $pdf->Ln($horizontalSpacerLN);
// 
//// ---------- HAUSTEK ---------
//

        $Block_height = 6 + $horizontalSpacerLN2 + getAnmHeight($pdf, $row['Anmerkung HKLS'], $SB);
        block_label_queer($block_header_w, $pdf, "Haustechnik", $Block_height, $block_header_height, $SB);

        $pdf->MultiCell($e_C_2_3rd, 6, "H6020: ", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['H6020'], $e_C_3rd, "");
        $pdf->MultiCell($e_C_2_3rd, 6, "Abwärme MT: ", 0, 'R', 0, 0);

        $abwrem_out = "";
        if ($row['HT_Waermeabgabe_W'] === "0" || $row['HT_Waermeabgabe_W'] == 0 || $row['HT_Waermeabgabe_W'] == "-") {
            $abwrem_out = "keine Angabe";
        } else {
            $abwrem_out = "ca. " . kify($row['HT_Waermeabgabe_W']) . "W";
        }
        multicell_with_str($pdf, $abwrem_out, 4 * $e_C_3rd, "");

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung HKLS'], $SB, $block_header_w);

        $pdf->Ln($horizontalSpacerLN);
//
/// ----------- MEDGAS -----------
//

        $Block_height = 12 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung MedGas'], $SB);
        block_label_queer($block_header_w, $pdf, "Med.-Gas", $Block_height, $block_header_height, $SB);

        $pdf->MultiCell($e_C_2_3rd, 6, "1 Kreis O2: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['1 Kreis O2'], 1);
        $pdf->MultiCell($e_C_2_3rd, 6, "1 Kreis VA: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['1 Kreis Va'], 1);
        $pdf->MultiCell($e_C_2_3rd, 6, "1 Kreis DL5: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['1 Kreis DL-5'], 1);

        $pdf->MultiCell($e_C_2_3rd, 6, "NGA: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['NGA'], 1);
        $pdf->MultiCell($e_C_2_3rd, 6, "N2O: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['N2O'], 1);
        $pdf->MultiCell($e_C_2_3rd, 6, "CO2: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['CO2'], 1);
        $pdf->MultiCell($e_C_2_3rd, 6, "DL10: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['DL-10'], 1);

        $pdf->Ln($horizontalSpacerLN);
        $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);

        $pdf->MultiCell($e_C_2_3rd, 6, "2 Kreis O2: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['2 Kreis O2'], 1);
        $pdf->MultiCell($e_C_2_3rd, 6, "2 Kreis VA: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['2 Kreis Va'], 1);

        $pdf->MultiCell($e_C_2_3rd, 6, "2 Kreis DL5: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['2 Kreis DL-5'], 1);

        $pdf->MultiCell($e_C_2_3rd, 6, "DL-tech: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $e_C_3rd, $row['DL-tech'], 1);

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung MedGas'], $SB, $block_header_w);

////     ------- BauStatik ---------
        if ("" != $row['Anmerkung BauStatik'] && $row['Anmerkung BauStatik'] != "keine Angaben MT") {
            $pdf->Ln($horizontalSpacerLN);
            $Block_height = getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB);
            block_label_queer($block_header_w, $pdf, "Baustatik", $Block_height, $block_header_height, $SB);
            $pdf->Ln($horizontalSpacerLN2);
            anmA3($pdf, $row['Anmerkung BauStatik'], $SB, $block_header_w);
            $pdf->Ln($horizontalSpacerLN);
        }
//
////     ------- MT Tabelle  ---------
//
        // -------------------------Elemente im Raum laden-------------------------- 
        $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
            tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $valueOfRoomID . ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
        $result = $mysqli->query($sql);
        // -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
        $sql = "SELECT tabelle_parameter_kategorie.Kategorie,tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
        $result1 = $mysqli->query($sql);
        // -------------------------Elemente parameter ------------------------- 
        $sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, 
            tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter 
            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
        $result3 = $mysqli->query($sql);

//        $formattedDate = f(); 
        $sql = "SELECT tabelle_projekt_elementparameter_aenderungen.idtabelle_projekt_elementparameter_aenderungen, tabelle_projekt_elementparameter_aenderungen.projekt, tabelle_projekt_elementparameter_aenderungen.element, tabelle_projekt_elementparameter_aenderungen.parameter, tabelle_projekt_elementparameter_aenderungen.variante, tabelle_projekt_elementparameter_aenderungen.wert_alt, tabelle_projekt_elementparameter_aenderungen.wert_neu, tabelle_projekt_elementparameter_aenderungen.einheit_alt, tabelle_projekt_elementparameter_aenderungen.einheit_neu, tabelle_projekt_elementparameter_aenderungen.timestamp, tabelle_projekt_elementparameter_aenderungen.user
            FROM tabelle_projekt_elementparameter_aenderungen
            WHERE (((tabelle_projekt_elementparameter_aenderungen.projekt)=" . $_SESSION["projectID"] . "))
            AND tabelle_projekt_elementparameter_aenderungen.timestamp > '$Änderungsdatum'
            ORDER BY tabelle_projekt_elementparameter_aenderungen.timestamp DESC;";
        $changes = $mysqli->query($sql);
        $dataChanges = array();
        while ($row = $changes->fetch_assoc()) {
            $dataChanges[] = $row;
        }

        $dataChanges = filter_old_equal_new($dataChanges);
        
        
        //  ----------------------- Ausgabe Abkürzungen -----------------------
//        $sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
//            FROM (tabelle_projekt_elementparameter INNER JOIN tabelle_parameter ON tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter = tabelle_parameter.idTABELLE_Parameter) INNER JOIN tabelle_parameter_kategorie ON tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
//            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_parameter.`Bauangaben relevant`)=1))
//            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
//            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
//        $result4 = $mysqli->query($sql);

        $initialMemory = memory_get_usage();
        $row = $result->fetch_assoc();
// Get memory usage after fetching
        $finalMemory = memory_get_usage();
//        $pdf->Multicell(100, 10, $finalMemory - $initialMemory, 0, "R", 0, 1);
        $result->data_seek(0);
        if ($finalMemory - $initialMemory > -8000) {
            block_label_queer($block_header_w, $pdf, "Med.-tech.", 50, $block_header_height, $SB);
            make_MT_details_table($pdf, $result, $result1, $result3, $SB, $SH, $dataChanges);
        } else {
//            $pdf->Multicell(100, 10, $initialMemory . "       "  .$finalMemory - $initialMemory, 0, "R", 0, 1);
        }
    } //sql:fetch-assoc
}// for every room 


$mysqli->close();
ob_end_clean(); // brauchts irgendwie.... ? 
$pdf->Output('BAUANGABEN-MT.pdf', 'I');
