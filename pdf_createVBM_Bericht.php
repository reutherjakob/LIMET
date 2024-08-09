<?php

session_start();
require_once('TCPDF-master/TCPDF-master/tcpdf.php');

//include 'pdf_createBericht_utils.php';

class MYPDF extends TCPDF {

    public function Header() {
        if ($this->numpages > 1) {
            $image_file = 'LIMET_web.png';
            $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            $this->SetFont('helvetica', '', 8);
            
            $this->Cell(0, 0, 'Großgeräte Parameter', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->cell(0, 0, '', 'B', 0, 'L');
        } else { // Titelblatt
            $Disclaimer_txt = "Angaben beziehen sich exklusiv auf die gelisteten Geräte. Angaben beinhalten die weitere im Raum verortete MT NICHT. Technisch notwendige Mindestangaben liegen meist weit unter empfohlenen Angaben. Auflistung ist in Arbeit und erhebt aktuell keinen Anspruch auf Vollständigkeit. ";

            $Einzug = 10;
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(60);
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, "KHI", 0, false, 'L', 0, '', 0, false, 'B', 'B');

            $this->Ln();
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, "Vorentwurf ", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln(100);
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, 'Bauangaben' . "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, "von medizinischen Großgeräten", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln(30);

            $this->SetFont('helvetica', '', 9);
            $this->SetY(280 - ($this->getStringHeight(180, $Disclaimer_txt, 0, false, 'L', 0, '', 0, false, '', '')));

            $this->Multicell(180, 0, $Disclaimer_txt, 0, 'L', 0, 0);
            $this->SetFont('helvetica', '', 6);
            $image_file = 'LIMET_web.png';
            $this->Image($image_file, 150, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        }
    }

    public function Footer() {  // Page footer
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

include 'pdf_createBericht_utils.php';
include '_utils.php';

session_start();
check_login();

$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);

//     -----   FORMATTING VARIABLES    -----     
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/ 
$marginBTM = 10;
$SB = 210 - 2 * PDF_MARGIN_LEFT;  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;
$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 5;
$horizontalSpacerLN3 = 8;

$e_C = $SB / 3;
$e_C_3rd = $e_C / 3;
$e_C_2_3rd = $e_C - $e_C_3rd;

$block_header_w = 0.1;
$font_size = 6;
$block_header_height = 8;
$einzugPlus = 10; //um den text auf die Höhe der Anderen Angaben zu shiften bei ANM BO

$style_normal = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line); //$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 6, 'color' => array(110, 150, 80)));

$pdf = new MYPDF('P', PDF_UNIT, "A4", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "", "Bauangaben");
$pdf->AddPage('P', 'A4');
$pdf->SetFillColor(0, 0, 0, 0); //$pdf->SetFillColor(244, 244, 244); 
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);

$mysqli = utils_connect_sql();

foreach ($roomIDsArray as $valueOfRoomID) {


    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.`Fussboden OENORM B5220`, tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP, tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, tabelle_räume.HT_Waermeabgabe_W, tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_räume.`Fussboden`, ROUND(tabelle_räume.`Umfang`,2) AS Umfang, ROUND(tabelle_räume.`Volumen`,2) AS Volumen, tabelle_räume.`Raumhoehe`, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Belichtungsfläche`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.ET_Anschlussleistung_W, tabelle_räume.ET_Anschlussleistung_AV_W, tabelle_räume.ET_Anschlussleistung_SV_W, tabelle_räume.ET_Anschlussleistung_ZSV_W, tabelle_räume.ET_Anschlussleistung_USV_W, tabelle_räume.`EL_AV Steckdosen Stk`, tabelle_räume.`EL_SV Steckdosen Stk`, tabelle_räume.`EL_ZSV Steckdosen Stk`, tabelle_räume.`EL_USV Steckdosen Stk`, tabelle_räume.`ET_RJ45-Ports`, "
            . "tabelle_räume.`EL_Roentgen 16A CEE Stk`,tabelle_räume.GMP, tabelle_räume.HT_Abluft_Digestorium_Stk,tabelle_räume.HT_Notdusche, tabelle_räume.VE_Wasser,  "
            . "tabelle_räume.HT_Punktabsaugung_Stk, tabelle_räume.HT_Abluft_Sicherheitsschrank_Unterbau_Stk , tabelle_räume.HT_Abluft_Sicherheitsschrank_Stk, "
            . " tabelle_räume.`EL_Laser 16A CEE Stk`, tabelle_räume.`EL_Einzel-Datendose Stk`, tabelle_räume.`EL_Doppeldatendose Stk`, tabelle_räume.`EL_Bodendose Typ`, tabelle_räume.`EL_Bodendose Stk`, tabelle_räume.`EL_Beleuchtung 1 Typ`, tabelle_räume.`EL_Beleuchtung 2 Typ`, tabelle_räume.`EL_Beleuchtung 3 Typ`, tabelle_räume.`EL_Beleuchtung 4 Typ`, tabelle_räume.`EL_Beleuchtung 5 Typ`, tabelle_räume.`EL_Beleuchtung 1 Stk`, tabelle_räume.`EL_Beleuchtung 2 Stk`, tabelle_räume.`EL_Beleuchtung 3 Stk`, tabelle_räume.`EL_Beleuchtung 4 Stk`, tabelle_räume.`EL_Beleuchtung 5 Stk`, tabelle_räume.`EL_Lichtschaltung BWM JA/NEIN`, tabelle_räume.`EL_Beleuchtung dimmbar JA/NEIN`, tabelle_räume.`EL_Brandmelder Decke JA/NEIN`, tabelle_räume.`EL_Brandmelder ZwDecke JA/NEIN`, tabelle_räume.`EL_Kamera Stk`, tabelle_räume.`EL_Lautsprecher Stk`, tabelle_räume.`EL_Uhr - Wand Stk`, tabelle_räume.`EL_Uhr - Decke Stk`, tabelle_räume.`EL_Lichtruf - Terminal Stk`, tabelle_räume.`EL_Lichtruf - Steckmodul Stk`, tabelle_räume.`EL_Lichtfarbe K`, tabelle_räume.`EL_Notlicht RZL Stk`, tabelle_räume.`EL_Notlicht SL Stk`, tabelle_räume.`EL_Jalousie JA/NEIN`, tabelle_räume.`HT_Luftmenge m3/h`, CAST(REPLACE(tabelle_räume.`HT_Luftwechsel 1/h`,',','.') as decimal(10,2)) AS `HT_Luftwechsel`, tabelle_räume.`HT_Kühlung Lueftung W`, tabelle_räume.`HT_Heizlast W`, tabelle_räume.`HT_Kühllast W`, tabelle_räume.`HT_Fussbodenkühlung W`, tabelle_räume.`HT_Kühldecke W`, tabelle_räume.`HT_Fancoil W`, tabelle_räume.`HT_Summe Kühlung W`, tabelle_räume.`HT_Raumtemp Sommer °C`, tabelle_räume.`HT_Raumtemp Winter °C`, tabelle_räume.`AR_Ausstattung`, tabelle_räume.`Aufenthaltsraum` "
            . "FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "))";

    $result_rooms = $mysqli->query($sql);

    while ($row = $result_rooms->fetch_assoc()) {

        $pdf->SetFillColor(255, 255, 255);
        raum_header($pdf, $horizontalSpacerLN3, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt'], "Gr", array()); //utils function 
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
        
        $resultX = $mysqli->query($sql);
        $rowcounter = 0;
        while ($row2 = $resultX->fetch_assoc()) {
            $rowcounter++;
        }
        if ($rowcounter > 0) {
            $resultX->data_seek(0);
            block_label($pdf, "Med.-tech.", $block_header_height, $SB);
            make_MT_list2($pdf, $SB, $resultX);
            $pdf->Ln();
        }

//   ---------- ALLGEMEIN   ----------
//
        if ($row['Laseranwendung'] || $row['Strahlenanwendung']) {
            block_label($pdf, "Allgemein", $block_header_height, $SB);
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'Strahlenanwendung', "Strahlenanw.: ", array());
            if (($pdf->getStringHeight($e_C_3rd, $row['Strahlenanwendung'])) > 6) {
                strahlenanw($pdf, $row['Strahlenanwendung'], 4 * $e_C_3rd, $font_size);
            } else {
                strahlenanw($pdf, $row['Strahlenanwendung'], $e_C_3rd, $font_size);
            }
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "Laseranwendung", "Laseranw.: ", array());
            hackerlA3($pdf, $font_size, $e_C_3rd, $row['Laseranwendung'], "JA");
            $pdf->Ln($horizontalSpacerLN3);
        }

        // ELEKTRO 
        check_4_new_page($pdf, 50);
        block_label($pdf, "Elektro", $block_header_height, $SB);
        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "Anwendungsgruppe", "ÖVE E8101:", array());
        multicell_with_str($pdf, $row['Anwendungsgruppe'], $e_C_3rd, "");
        $outsr = "";
        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'ET_Anschlussleistung_W', "Summe Leistung:", array());

        if ($row['ET_Anschlussleistung_W'] != "0") {
            $outsr = kify($row['ET_Anschlussleistung_W']) . "W";
        } else {
            $outsr = "-";
        }
        multicell_with_str($pdf, $outsr, $e_C_3rd, "");

        $pdf->Ln($horizontalSpacerLN);
        anm_txt($pdf, $row['Anmerkung Elektro'], $SB, $block_header_w);
        $pdf->Ln($horizontalSpacerLN);
        check_4_new_page($pdf, 50);
// 
//// ---------- HAUSTEK ---------
//
        block_label($pdf, "Haustechnik", $block_header_height, $SB);

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "H6020", "H6020: ", array());
        multicell_with_str($pdf, $row['H6020'], $e_C_3rd, "");
        
        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "HT_Waermeabgabe_W", "Abwärme MT: ", array());
        $abwrem_out = "";
        if ($row['HT_Waermeabgabe_W'] === "0" || $row['HT_Waermeabgabe_W'] == 0 || $row['HT_Waermeabgabe_W'] == "-") {
            $abwrem_out = "keine Angabe";
        } else {
            $abwrem_out = "ca. " . kify($row['HT_Waermeabgabe_W']) . "W";
        }
        multicell_with_str($pdf, $abwrem_out, $e_C_3rd, "");

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "HT_Raumtemp Sommer °C", "max. Raumtemp.:", array());
        multicell_with_str($pdf, $row['HT_Raumtemp Sommer °C'], $e_C_3rd, "°C");

        $pdf->Ln($horizontalSpacerLN2);
        anm_txt($pdf, $row['Anmerkung HKLS'], $SB, $block_header_w);

        $pdf->Ln($horizontalSpacerLN);
        check_4_new_page($pdf, 50);

////     ------- BauStatik ---------
        if ("" != $row['Anmerkung BauStatik'] && $row['Anmerkung BauStatik'] != "keine Angaben MT") {
            $Block_height = getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB);
            block_label($pdf, "Baustatik", $block_header_height, $SB);
            anm_txt($pdf, $row['Anmerkung BauStatik'], $SB, $block_header_w);
            $pdf->Ln($horizontalSpacerLN);
        }
        $pdf->AddPage();//check_4_new_page($pdf, 50);
    } 
}

$mysqli->close();
ob_end_clean(); 
$pdf->Output('BAUANGABEN-MT.pdf', 'I');

