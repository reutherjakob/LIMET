<?php

session_start();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include 'pdf_createBericht_utils.php';
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();

$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);

//     -----   FORMATTING VARIABLES    -----     
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/ 
$marginBTM = 10;
$SB = 210 - 2 * PDF_MARGIN_LEFT;  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;
$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 6;
$horizontalSpacerLN3 = 8;

$e_C = $SB / 3;
$e_C_3rd = $e_C / 3;
$e_C_2_3rd = $e_C - $e_C_3rd;

$e_D = $SB / 6;
$e_D_3rd = $e_C / 3;
$e_D_2_3rd = $e_D - $e_D_3rd;

$einzug_anm = 5;
$font_size = 6;
$block_header_height = 5;
$style_normal = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line); //$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 6, 'color' => array(110, 150, 80)));

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
            $Disclaimer_txt = "Alle Angaben beziehen sich exklusiv auf die im jeweiligen Raume angeführten Geräte und Anlagen. Die folgenden Angaben beinhalten KEINE weitere im Raum verortete Medizin Technik. ";
            $Einzug = 10;
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(60);
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, $_SESSION["projectName"], 0, false, 'L', 0, '', 0, false, 'B', 'B');
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

$pdf = new MYPDF('P', PDF_UNIT, "A4", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "", "Bauangaben");
$pdf->AddPage('P', 'A4');
$pdf->SetFillColor(0, 0, 0, 0); //$pdf->SetFillColor(244, 244, 244); 
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);

$mysqli = utils_connect_sql();

$parameterarray = array();
$execute_once = true;

foreach ($roomIDsArray as $valueOfRoomID) {
    $pdf->SetFont('helvetica', '', $font_size);
    $pdf->SetFillColor(255, 255, 255);
    $sql = "SELECT * FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "))";
    $resultat_parametserslt_rooms = $mysqli->query($sql);

    while ($row = $resultat_parametserslt_rooms->fetch_assoc()) {
//        echorow($row); 
        raum_header($pdf, $horizontalSpacerLN2, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt'], "Gr", array()); //utils function 
////     ------- MT ---------´
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
        $resultat_parametsersltX = $mysqli->query($sql);
        $rowcounter = 0;
        while ($rowww = $resultat_parametsersltX->fetch_assoc()) {
            $rowcounter++;
        }
        $resultat_parametsersltX->data_seek(0);
        if ($rowcounter > 0) {
            $pdf->Ln(1);
            check_4_new_page($pdf, $rowcounter / 4 * $font_size);
            block_label($pdf, "Medizintechnische Einrichtung", $block_header_height, $SB);
            make_MT_list2($pdf, $SB, $resultat_parametsersltX);
        }
//        $roomName = $row['Raumbezeichnung']; // Replace with actual room name variable
//        $elementID = $rows_el_in_room[$rowcounter]['TABELLE_Elemente_idTABELLE_Elemente'];
//        $variantID = $rows_el_in_room[$rowcounter]['Variante'];
//        $projectID = $_SESSION["projectID"];
//        echo "Room Name: " . $roomName . "<br>";
//        echo "Room ID: " . $valueOfRoomID . "<br>";
//        echo "Element ID: " . $elementID . "<br>";
//        echo "Variant ID: " . $variantID . "<br>";
//        echo "Project ID: " . $projectID . "<br>";

        if ($execute_once) {
//            echo "GET params.... " . "<br>";
            $execute_once = false;
            $sql = "SELECT tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
            tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte
                FROM tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_projekt_elementparameter ON 
                (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten)
                AND (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente)
                WHERE (((tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)=143)
                AND ((tabelle_räume_has_tabelle_elemente.Verwendung)=1) 
                AND ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $row['idTABELLE_Projekte'] . "))";
            $res = $mysqli->query($sql);
            while ($resultat_parametsers = $res->fetch_assoc()) {
                $parameterarray[$resultat_parametsers["TABELLE_Räume_idTABELLE_Räume"]] = $resultat_parametsers['Wert']; // ." ". $resultat_parametsers['Einheit'];
//                echo $resultat_parametsers['Wert'] . $resultat_parametsers['Einheit']; 
            }
//            echorow($parameterarray);
        }

//        if (isset($parameterarray[$valueOfRoomID])) {
//            echo "RID: " . $valueOfRoomID . ": " . $parameterarray[$valueOfRoomID]. "</br>";
//        }
//        foreach ($parameterarray as $p) { 
//            echo $p["TABELLE_Räume_idTABELLE_Räume"] . "<br> ";
//            if ($p["TABELLE_Räume_idTABELLE_Räume"] === $valueOfRoomID) {
//                echo "Win?" .$p['Wert']. $p['Einheit'] . "</br>";
//            }
//        }
//        
//   ---------- ALLGEMEIN   ----------
//      
        if (($row["AR_Empf_Breite_cm"] > 0 || $row["AR_Empf_Tiefe_cm"] > 0 || $row["AR_Empf_Hoehe_cm"] > 0) || ($row['Laseranwendung'] || $row['Strahlenanwendung'])) {
            $anm = "Technisch notwendige Raumbemessungsangaben sind mindestens notwendig, um die Nutzung zu gewährleisten. Es ist empfohlen, mehr Fläche zu planen.";
            check_4_new_page($pdf, getAnmHeight($pdf, $anm, $SB) + $font_size * 2);

            block_label($pdf, "Allgemein", $block_header_height, $SB);

            if ($row['Laseranwendung'] || $row['Strahlenanwendung']) {
                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'Strahlenanwendung', "Strahlenanw.: ", array());
                if (($pdf->getStringHeight($e_C_3rd, $row['Strahlenanwendung'])) > 6) {
                    strahlenanw($pdf, $row['Strahlenanwendung'], 4 * $e_C_3rd, $font_size);
                } else {
                    strahlenanw($pdf, $row['Strahlenanwendung'], $e_C_3rd, $font_size);
                }
                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "AR_Statik_relevant", "Statisch relevant: ", array());
                hackerlA3($pdf, $font_size, $e_C_3rd, $row['AR_Statik_relevant'], "JA");

                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "Laseranwendung", "Laseranw.: ", array());
                hackerlA3($pdf, $font_size, $e_C_3rd, $row['Laseranwendung'], "JA");
                $pdf->Ln($horizontalSpacerLN2);
                if ($row['Strahlenanwendung']) {
                    anm_txt($pdf,"Baulicher Strahlenschutz und Strahlenwarnleuchte außen vorgesehen."  ,$SB, $einzug_anm);
                    $pdf->Ln($horizontalSpacerLN);
                }
            }
            if ($row["AR_Empf_Breite_cm"] > 0 || $row["AR_Empf_Tiefe_cm"] > 0 || $row["AR_Empf_Hoehe_cm"] > 0) {
                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "AR_Empf_Breite_cm", "Mind. Raumbreite: ", array());
                multicell_with_nr($pdf, $row["AR_Empf_Breite_cm"], "cm", $font_size, $e_C_3rd);
                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "AR_Empf_Tiefe_cm", "Mind. Raumtiefe: ", array());
                multicell_with_nr($pdf, $row["AR_Empf_Tiefe_cm"], "cm", $font_size, $e_C_3rd);
                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "AR_Empf_Hoehe_cm", "Mind. Raumhöhe: ", array());
                multicell_with_nr($pdf, $row["AR_Empf_Hoehe_cm"], "cm", $font_size, $e_C_3rd);
                $pdf->Ln($horizontalSpacerLN2);
                anm_txt($pdf, $anm, $SB, $einzug_anm);
            }
            $pdf->Ln(1);
        }

////// ---------- ELEKTRO ---------
        check_4_new_page($pdf, getAnmHeight($pdf, $row['Anmerkung Elektro'], $SB) + $font_size * 3);
        block_label($pdf, "Elektrotechnik", $block_header_height, $SB);

        multicell_text_hightlight($pdf, $e_C_3rd, $font_size, "Anwendungsgruppe", "ÖVE8101:", array());
        multicell_with_str($pdf, $row['Anwendungsgruppe'], $e_C_3rd, "");

        $outsr = "";
        multicell_text_hightlight($pdf, $e_C_3rd, $font_size, 'ET_Anschlussleistung_W', "Leistung:", array());
        if ($row['ET_Anschlussleistung_W'] != "0") {
            $outsr = kify($row['ET_Anschlussleistung_W']) . "W";
        } else {
            $outsr = "-";
        }
        multicell_with_str($pdf, $outsr, $e_C_3rd, "");
//
//        $pdf->Ln($horizontalSpacerLN2);
        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, " ", "Netzinnenwiederstand: ", array());
        multicell_with_nr($pdf, $parameterarray[$valueOfRoomID], " mOhm", $font_size, $e_C_3rd);

        $pdf->Ln($horizontalSpacerLN2);

        multicell_text_hightlight($pdf, $e_D_2_3rd, $font_size, "AV", "AV: ", array());
        hackerlA3($pdf, $font_size, $e_D_3rd, $row['AV'], "JA");

        multicell_text_hightlight($pdf, $e_D_2_3rd, $font_size, "SV", "SV: ", array());
        hackerlA3($pdf, $font_size, $e_D_3rd, $row['SV'], "JA");

        multicell_text_hightlight($pdf, $e_D_2_3rd, $font_size, "ZSV", "ZSV: ", array());
        hackerlA3($pdf, $font_size, $e_D_3rd, $row['ZSV'], "JA");

        multicell_text_hightlight($pdf, $e_D_2_3rd, $font_size, "USV", "USV: ", array());
        hackerlA3($pdf, $font_size, $e_D_3rd, $row['USV'], "JA");

        multicell_text_hightlight($pdf, 3 * $e_D_2_3rd, $font_size, "IT Anbindung", "IT Anschl.: ", array());
        hackerlA3($pdf, $font_size, $e_D_3rd, $row['IT Anbindung'], "JA");
        $pdf->Ln($horizontalSpacerLN2);

        anm_txt($pdf, $row['Anmerkung Elektro'], $SB, $einzug_anm);
        $pdf->Ln(1);

//// ---------- HAUSTEK ---------
        $rowHeightComment = $pdf->getStringHeight($SB, $row['Anmerkung HKLS'], false, true, '', 1);
        check_4_new_page($pdf, $rowHeightComment + 10);
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

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, " ", "Kühlwasser: ", array());
        hackerlA3($pdf, $font_size, $e_C_3rd, $row['HT_Kühlwasser'], "JA");

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, " ", "Max Temp. Gradient: ", array());
        multicell_with_nr($pdf, $row['HT_Tempgradient_Ch'], " °C/h", $font_size, $e_C_3rd);

        $pdf->Ln($horizontalSpacerLN2);
        anm_txt($pdf, $row['Anmerkung HKLS'], $SB, $einzug_anm);
        if (( "" != $row['Anmerkung BauStatik'] && $row['Anmerkung BauStatik'] != "keine Angaben MT") || ($row["AR_Flaechenlast_kgcm2"] !== 0 || $row["AR_Flaechenlast_kgcm2"] !== "-")) {
            if (!check_4_new_page($pdf, getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB))) {
                if( $row['Anmerkung HKLS']!=="" ){
                $pdf->Ln($horizontalSpacerLN);}
                else{    $pdf->Ln(2);}
                
            } else {
                $pdf->Ln(2);
                balken($pdf, $horizontalSpacerLN, $SB);
            }
        }

////  ------- BauStatik ---------


        if (( "" != $row['Anmerkung BauStatik'] && $row['Anmerkung BauStatik'] != "keine Angaben MT") || ($row["AR_Flaechenlast_kgcm2"] !== 0 || $row["AR_Flaechenlast_kgcm2"] !== "-" || $row['Fussboden OENORM B5220'] !== "kA" )) {
            check_4_new_page($pdf, 10 + $block_header_height + getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB));
            block_label($pdf, "Bau/Statik/Schwingungsklasse", $block_header_height, $SB);

            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, " ", "Boden B5220: ", array());
            multicell_with_nr($pdf, $row['Fussboden OENORM B5220'], "", $font_size, $e_C_3rd);

            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, " ", "Maximale Flächenlast: ", array());
            multicell_with_nr($pdf, $row["AR_Flaechenlast_kgcm2"], " kg/cm2", $font_size, $e_C_3rd);

            $pdf->Ln($horizontalSpacerLN2);
            anm_txt($pdf, $row['Anmerkung BauStatik'], $SB, $einzug_anm);
            $pdf->Ln($horizontalSpacerLN2);

            if (!check_4_new_page($pdf, 30 + $block_header_height + getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB))) {
                balken($pdf, 2, $SB);
            }
        } else {
            if (!check_4_new_page($pdf, 30 + $block_header_height + getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB))) {
                $pdf->Ln(2);
                balken($pdf, 2, $SB);
            }
        }

//        $pdf->AddPage(); 
    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output('BAUANGABEN-MT.pdf', 'I');
