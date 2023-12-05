<?php

// Include the main TCPDF library (search for installation path).
include 'pdf_createBericht_MYPDFclass.php';
include 'pdf_createBericht_utils.php';

session_start();

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
$pdf->SetTitle('Raumbuch');
$pdf->SetSubject('Raumbuch');
$pdf->SetKeywords('Raumbuch');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$marginTop = 20; // set margins
$pdf->SetMargins($einzugLR, $marginTop, $einzugLR);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage('P', 'A4');

// Daten laden
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

// RaumIDs laden über GET
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);

foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');
    // Raumdaten laden ----------------------------------

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

    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(100, 6, "Raum: " . $row['Raumbezeichnung'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: " . $row['Raumnr'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 6, "Bereich: " . $row['Raumbereich Nutzer'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Geschoss: " . $row['Geschoss'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Bauetappe: " . $row['Bauetappe'], 'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: " . $row['Bauabschnitt'], 'B', 'L', 0, 0);

        if (strlen($row['Anmerkung FunktionBO']) > 0) {
            $pdf->Ln($ln_spacer1);
            block_label($pdf, "BO-Beschreibung");
            $outstr = format_text(clean_string(br2nl($row['Anmerkung FunktionBO'])));
            $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
            check_4_new_page($pdf, $rowHeightComment);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->MultiCell($SB, $rowHeightComment, $outstr, 0, 'L', 0, 0);
            //$pdf-> MultiCell($SB, 10, $rowHeightComment, 0,"L",0,0);
            block_spacer($pdf);
        } else {
            $pdf->Ln($ln_spacer1);
        }

        block_label($pdf, "Allgemein");
        $pdf->MultiCell($einzugC1, 6, "Raumfläche: ", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['Nutzfläche'], "m²", 10, $einzugC2);

        $pdf->MultiCell($einzugC1, 6, "Fußboden: ", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['Fussboden'], $einzugC2);
        $pdf->MultiCell($einzugC1, 6, "Abdunkelbarkeit: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['Abdunkelbarkeit'], "JA");

        $pdf->Ln($ln_spacer1);

        $pdf->MultiCell($einzugC1, 6, "Raumhöhe 1: ", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['Raumhoehe'], $einzugC2);
        
        $pdf->MultiCell($einzugC1, 6, "Belichtungsfläche: ", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['Belichtungsfläche'], $einzugC2);
        $pdf->MultiCell($einzugC1, 6, "Laseranwendung: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['Laseranwendung'], "JA");

        $pdf->Ln($ln_spacer1);
        
        $pdf->MultiCell($einzugC1, 6, "Raumhöhe 2: ", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['Raumhoehe 2'], $einzugC2);
        
        $pdf->MultiCell($einzugC1, 6, "Umfang: ", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['Umfang'], "m", $pdf-> getFontSizePt(), $einzugC2);
        //$pdf->MultiCell($einzugC2, 6, $row['Umfang'] . "m", 0, 'L', 0, 0);
        
        

        $pdf->MultiCell($einzugC1, 6, "Strahlenanwendung: ", 0, 'R', 0, 0);
        strahlenanw($pdf, $row['Strahlenanwendung'], $einzugC2, $hackerl_schriftgröße);

        $pdf->Ln($ln_spacer1);

        $pdf->MultiCell($einzugC1, 6, "Aufenthaltsr.: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['Aufenthaltsraum'], "JA");
        
        $pdf->MultiCell($einzugC1 + $einzugC2 - $hackerl_Zellgröße, 6, "H6020:", 0, 'R', 0, 0);
        
        multicell_with_nr($pdf, $row['H6020'], "m", $pdf-> getFontSizePt(), $einzugC2);
        //$pdf->MultiCell($hackerl_schriftgröße, 6, " " . $row['H6020'], 0, 'L', 0, 0);
       
        block_spacer($pdf);

        $SizeElektroSegement = 100; //TODO calc precisely
        if ((297 - $marginTop - $pdf->GetY()) < $SizeElektroSegement) {
            $pdf->AddPage();
        }

        block_label($pdf, "Elektro");
        $restspace = ((180 - $einzugE - $hackerl_Zellgröße - 10) / 5) - $hackerl_Zellgröße;
        $manual_offset = 5;
        
        $pdf->MultiCell($einzugE, 6, "ÖVE E8101:", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['Anwendungsgruppe'], $hackerl_Zellgröße);

        $pdf->MultiCell($restspace, 6, "AV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['AV'], "JA");

        $pdf->MultiCell($restspace, 6, "SV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['SV'], "JA");

        $pdf->MultiCell($restspace, 6, "ZSV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['ZSV'], "JA");

        $pdf->MultiCell($restspace, 6, "USV: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['USV'], "JA");

        $pdf->MultiCell($restspace, 6, "IT: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['IT Anbindung'], "JA");

        $pdf->Ln($ln_spacer1);
        $pdf->SetFont('helvetica', '', 6);
        
        
        $pdf->MultiCell($einzugE + $manual_offset, 6, "", 0, 'R', 0, 0);
        $pdf->MultiCell($restspace, 6, "SSD:", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['EL_AV Steckdosen Stk'], $hackerl_Zellgröße);

        $pdf->MultiCell($restspace, 6, "SSD:", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['EL_SV Steckdosen Stk'], $hackerl_Zellgröße);

        $pdf->MultiCell($restspace, 6, "SSD:", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['EL_USV Steckdosen Stk'], $hackerl_Zellgröße);

        $pdf->MultiCell($restspace, 6, "SSD:", 0, 'R', 0, 0);
        multicell_with_str($pdf, $row['EL_USV Steckdosen Stk'], $hackerl_Zellgröße);

        $pdf->MultiCell($restspace, 6, "ED:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Einzel-Datendose Stk'], $hackerl_Zellgröße);
        $pdf->Ln(3);

        $pdf->MultiCell($einzugE + $restspace + $manual_offset, 6, "BD:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Bodendose Stk'], $hackerl_Zellgröße);

        $pdf->MultiCell(($restspace + $hackerl_Zellgröße) * 3, 6, " ", 0, 'L', 0, 0);
        $pdf->MultiCell($restspace, 6, "DD:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Doppeldatendose Stk'], $hackerl_Zellgröße);
        
        $pdf->Ln($ln_spacer1/2 +2 );
        dashed_line($pdf, 0);
        $pdf->Ln($ln_spacer1/2);

        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell($einzugE, 10, "Beleuchtung:", 0, 'R', 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        
        $manual_offset = 6;
        $restspace = (($SB - $einzugE - $hackerl_Zellgröße) / 3) - $manual_offset;

        $pdf->MultiCell($restspace - (2 * $manual_offset), 6, "Dimmbar: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['EL_Beleuchtung dimmbar JA/NEIN'], "JA");
        $pdf->MultiCell($restspace, 6, "Bewegungsmelder: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['EL_Lichtschaltung BWM JA/NEIN'], "JA");
        $pdf->MultiCell($restspace, 6, "Rettungszeichenleuchte: ", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Notlicht RZL Stk'], $hackerl_Zellgröße);

        $pdf->Ln(5);

        $pdf->MultiCell($restspace + $einzugE - (2 * $manual_offset), 6, "Sicherheitsleuchte: ", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Notlicht SL Stk'], $hackerl_Zellgröße);
        $pdf->MultiCell($restspace, 6, "Lichtfarbe: ", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['EL_Lichtfarbe K'], "", 8, $hackerl_Zellgröße);

        //$pdf->Ln(6); 
        // ($restspace + $einzugE-(2*$manual_offset))   for a new line 
        $pdf->MultiCell($restspace, 6, "Leuchten: ", 0, 'R', 0, 0);

        $leuchten_printout = false;
        if ($row['EL_Beleuchtung 1 Stk'] > 0) {
            $pdf->MultiCell(100, 6, $row['EL_Beleuchtung 1 Stk'] . " Stk, " . $row['EL_Beleuchtung 1 Typ'], 0, 'L', 0, 0);
            $pdf->Ln();
            $pdf->MultiCell(75, 6, "", 0, 'R', 0, 0);
            $leuchten_printout = true;
        }
        if ($row['EL_Beleuchtung 2 Stk'] > 0) {
            $pdf->MultiCell(100, 6, $row['EL_Beleuchtung 2 Stk'] . " Stk, " . $row['EL_Beleuchtung 2 Typ'], 0, 'L', 0, 0);
            $pdf->Ln();
            $pdf->MultiCell(75, 6, "", 0, 'R', 0, 0);
            $leuchten_printout = true;
        }
        if ($row['EL_Beleuchtung 3 Stk'] > 0) {
            $pdf->MultiCell(100, 6, $row['EL_Beleuchtung 3 Stk'] . " Stk, " . $row['EL_Beleuchtung 3 Typ'], 0, 'L', 0, 0);
            $pdf->Ln();
            $pdf->MultiCell(75, 6, "", 0, 'R', 0, 0);
            $leuchten_printout = true;
        }
        if ($row['EL_Beleuchtung 4 Stk'] > 0) {
            $pdf->MultiCell(100, 6, $row['EL_Beleuchtung 4 Stk'] . " Stk, " . $row['EL_Beleuchtung 4 Typ'], 0, 'L', 0, 0);
            $pdf->Ln();
            $pdf->MultiCell(75, 6, "", 0, 'R', 0, 0);
            $leuchten_printout = true;
        }
        if ($row['EL_Beleuchtung 5 Stk'] > 0) {
            $pdf->MultiCell(100, 6, $row['EL_Beleuchtung 5 Stk'] . " Stk, " . $row['EL_Beleuchtung 5 Typ'], 0, 'L', 0, 0);
            $leuchten_printout = true;
        }
        if ($leuchten_printout) {
            $pdf->Ln(5);
        }

        //MULTIMEDIA 
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell($einzugE, 6, "Multimedia:", 0, 'R', 0, 0);
        $pdf->SetFont('helvetica', '', 8);

        $pdf->MultiCell($restspace - (2 * $manual_offset), 6, "Kamera:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Kamera Stk'], $hackerl_Zellgröße);
        $pdf->MultiCell($restspace, 6, "Lautsprecher:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Lautsprecher Stk'], $hackerl_Zellgröße);
        $pdf->MultiCell($restspace, 6, "Wanduhr:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Uhr - Wand Stk'], $hackerl_Zellgröße);
        $pdf->Ln(5);

        $pdf->MultiCell($restspace - (2 * $manual_offset) + $einzugE, 6, "Deckenuhr:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Uhr - Decke Stk'], $hackerl_Zellgröße);

        $pdf->MultiCell($restspace, 6, "Lichtruf-Terminal:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Lichtruf - Terminal Stk'], $hackerl_Zellgröße);

        $pdf->MultiCell($restspace, 6, "Lichtruf-Modul:", 0, 'R', 0, 0);
        multicell_with_stk($pdf, $row['EL_Lichtruf - Steckmodul Stk'], $hackerl_Zellgröße);

        //Brandmelder
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell($einzugE, 6, "Brandmelder:", 0, 'R', 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->MultiCell($restspace - (2 * $manual_offset), 6, "Decke: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['EL_Brandmelder Decke JA/NEIN'], "JA");

        $pdf->MultiCell($restspace, 6, "Zwischendecke: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['EL_Brandmelder ZwDecke JA/NEIN'], "JA");

        //Jalousie
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell($einzugE, 6, "Jalousie:",0, 'R', 0, 0);        
        $pdf->SetFont('helvetica', '', 8);
        $pdf->MultiCell(30, 6, "Elektrisch:" ,0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['EL_Jalousie JA/NEIN'], "JA");
        $pdf->Ln(1);
        block_spacer($pdf);

        //--------------------------- `HT_Luftmenge m3/h``HT_Luftwechsel 1/h`


        check_4_new_page($pdf, 50);
        block_label($pdf, "Haustechnik");

        //$pdf->MultiCell($einzugC1*2 + $einzugC2, 6, "H6020:", 0, 'R', 0, 0);
        //$pdf->MultiCell($einzugC2, 6, " " . $row['H6020'], 0, 'L', 0, 0);
        //$pdf->Ln(5);
        
        $pdf->MultiCell($einzugC1, 6, "Kühllast:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Kühllast W'], "W", 10, $einzugC2);
        
        $pdf->MultiCell($einzugC1, 6, "Raumtemp-Winter:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Raumtemp Winter °C'], "°C", 10 , $einzugC2); 
        
        $pdf->MultiCell($einzugC1, 6, "Heizlast:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Heizlast W'], "W", 10, $einzugC2);

        $pdf->Ln($ln_spacer1);
        
        $pdf->MultiCell($einzugC1, 6, "Kühlung-Lüftung:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Kühlung Lueftung W'], "W", 10 , $einzugC2); 
        
        $pdf->MultiCell($einzugC1, 6, "Raumtemp-Sommer:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Raumtemp Sommer °C'], "°C", 10 , $einzugC2); 
        
        $pdf->MultiCell($einzugC1, 6, "Luftwechsel:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Luftwechsel'], "1/h", 8, $einzugC2);
        
        $pdf->Ln($ln_spacer1); 
        
        $pdf->MultiCell($einzugC1, 6, "Kühlung-FB:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Fussbodenkühlung W'], "W", 10 , $einzugC2);
        
        $pdf->MultiCell($einzugC1, 6, "Raumvolumen:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['Volumen'], "m³", 8, $einzugC2);
        
        $pdf->MultiCell($einzugC1, 6, "Luftmenge:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Luftmenge m3/h'], "m³/h", 10 , $einzugC2);
        
        $pdf->Ln($ln_spacer1);
        $pdf->MultiCell($einzugC1, 6, "Kühlung-Decke:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Kühldecke W'], "W", 10 , $einzugC2); 

        
        $pdf->MultiCell($einzugC1, 6, "Summe Kühlung:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Summe Kühlung W'], "W", 8, 20);
        
        $pdf->MultiCell($einzugC1, 6, "Fancoil:", 0, 'R', 0, 0);
        multicell_with_nr($pdf, $row['HT_Fancoil W'], "W", 8, $einzugC2);
        //$pdf->Ln($ln_spacer1);
        
        

        $pdf->SetFont('helvetica', '', 10);
        if (strlen($row['Anmerkung HKLS']) > 0) {
            $pdf->Ln();
            $outstr = clean_string(br2nl($row['Anmerkung HKLS']));
            $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(0, 10, "Anmerkung:", 0, 'L', 0, 0);
            $pdf->Ln();
            $pdf->MultiCell($SB, $rowHeightComment, $outstr, 0, 'L', 0, 0);
        }
        
        newpage_or_spacer($pdf, 60);
        block_label($pdf, "Med.-Gas");
     
        $platzPer = $SB/6 - $hackerl_Zellgröße -3; 
        $manual_offset = 10;
        
        $pdf->MultiCell($platzPer +$manual_offset, 6, "1 Kreis O2: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['1 Kreis O2'], 1);

        $pdf->MultiCell($platzPer, 6, "2 Kreis O2: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['2 Kreis O2'], 1);

        $pdf->MultiCell($platzPer, 6, "1 Kreis VA: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['1 Kreis Va'], 1);
        
        $pdf->MultiCell($platzPer, 6, "2 Kreis VA: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['2 Kreis Va'], 1); 
        
        $pdf->MultiCell($platzPer, 6, "1 Kreis DL5: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['1 Kreis DL5'], 1); 
        
        $pdf->MultiCell($platzPer , 6, "2 Kreis DL5: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['2 Kreis DL5'], 1);  
        
        $pdf->Ln(5);
        
        $pdf->MultiCell($platzPer+ $manual_offset, 6, "DL10: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['DL-10'], 1); 
        
        $pdf->MultiCell($platzPer, 6, "DL-tech: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['DL-tech'], 1); 
        
        $pdf->MultiCell($platzPer, 6, "CO2: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['CO2'], 1); 
        
        $pdf->MultiCell($platzPer, 6, "N2O: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['N20'], 1); 

        $pdf->MultiCell($platzPer, 6, "NGA: ", 0, 'R', 0, 0);
        hackerl($pdf, $hackerl_schriftgröße, $row['NGA'], 1); 
         
        //TODO Check 4 next pages!!
        // $y = $pdf->GetY();
        //if (($y + 6) >= 270) {
        //    $pdf->AddPage();}
        
        if (strlen($row['Anmerkung MedGas']) > 0) {
            $pdf->Ln();
            //$pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            //$pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140, br2nl($row['Anmerkung MedGas']), false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:", 0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung MedGas']), 0, 'L', 0, 0);
        }

        if (strlen($row['AR_Ausstattung']) > 0) {
            block_spacer($pdf);
            block_label($pdf, "Architektur-Einrichtung");
            $pdf->SetFont('helvetica', '', 10);
            $rowHeightComment = $pdf->getStringHeight(140, br2nl($row['AR_Ausstattung']), false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }

            $pdf->MultiCell($SB, $rowHeightComment, br2nl($row['AR_Ausstattung']), 0, 'L', 0, 0);
            $pdf->Ln();
        }
    }

    // -------------------------Elemente im Raum laden--------------------------
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
    ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $result = $mysqli->query($sql);
    
     
    newpage_or_spacer($pdf, 12+(10*$result->num_rows));
    
    block_label($pdf, "Medizintechnik");
    el_in_room_html_table($pdf, $result, 5);
}

// MYSQL-Verbindung schließen
$mysqli->close();
//$pdf->MultiCell(50, 6, "Bereich",'B', 'L', 0, 0);
//$pdf->MultiCell(20, 6, "Geschoss",'B', 'C', 0, 0);
ob_end_clean();
// close and output PDF document
$pdf->Output('Raumbuch-MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+