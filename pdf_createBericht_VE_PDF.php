<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
$_SESSION["PDFTITEL"] = "Medizintechnisches Raumbuch";
$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumbuch-MT");

$mysqli = utils_connect_sql();
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
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`,  tabelle_räume.`Fussboden`, ROUND(tabelle_räume.`Umfang`,2) AS Umfang, tabelle_räume.`Raumhoehe`, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Belichtungsfläche`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung
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
        $pdf->Ln();
        if (null != ($row['Anmerkung FunktionBO'])) {
            $rowHeightComment = $pdf->getStringHeight(140, br2nl($row['Anmerkung FunktionBO']), false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(40, $rowHeightComment, "BO-Beschreibung:", 0, 'L', 0, 0);
            $pdf->Ln(6);
            $pdf->MultiCell(40, $rowHeightComment, "", 'B', 'L', 0, 0);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung FunktionBO']), 'B', 'L', 0, 0);
        }


        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(22, 8, "Allgemein: ", 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Raumfläche: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, $row['Nutzfläche'] . " m2", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 6, "Fußboden: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, $row['Fussboden'], 0, 'L', 0, 0);
        $pdf->MultiCell(40, 6, "Strahlenanwendung: ", 0, 'R', 0, 0);
        if ($row['Strahlenanwendung'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            if ($row['Strahlenanwendung'] === '1') {
                $pdf->SetFont('zapfdingbats', '', 10);
                //grün
                $pdf->SetTextColor(0, 255, 0);
                $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            } else {
                $pdf->MultiCell(40, 6, "Quasi stationär", 0, 'L', 0, 0);
            }
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Umfang: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, $row['Umfang'] . " m", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 6, "Belichtungsfläche: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, $row['Belichtungsfläche'] . " m2", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 6, "Laseranwendung: ", 0, 'R', 0, 0);
        if ($row['Laseranwendung'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Raumhöhe 1: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, $row['Raumhoehe'] . " m", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 6, "Raumhöhe 2: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, $row['Raumhoehe 2'] . " m", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 6, "Abdunkelbarkeit: ", 0, 'R', 0, 0);
        if ($row['Abdunkelbarkeit'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(180, 6, "", 'B', 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(20, 8, "Elektro: ", 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "ÖVE E8101:", 0, 'R', 0, 0);
        $pdf->MultiCell(10, 6, " " . $row['Anwendungsgruppe'], 0, 'L', 0, 0);
        $pdf->MultiCell(20, 6, "AV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['AV'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "SV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['SV'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "ZSV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['ZSV'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "USV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['USV'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(60, 6, "", 0, 'R', 0, 0);
        //$pdf->MultiCell(40, 6, "FB ÖNORM B5220:",0, 'R', 0, 0);
        //$pdf->MultiCell(20, 6, " ".$row['Fussboden OENORM B5220'],0, 'L', 0, 0);
        $pdf->MultiCell(10, 6, "IT: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['IT Anbindung'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if (null != ($row['Anmerkung Elektro'])) {
            $pdf->Ln();
            //$pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            //$pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140, br2nl($row['Anmerkung Elektro']), false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:", 0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung Elektro']), 0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->MultiCell(180, 6, "", 'B', 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(50, 8, "Haustechnik: ", 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "H6020:", 0, 'R', 0, 0);
        $pdf->MultiCell(10, 6, " " . $row['H6020'], 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if (null != ($row['Anmerkung HKLS'])) {
            $pdf->Ln();
            //$pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            //$pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140, br2nl($row['Anmerkung HKLS']), false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:", 0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung HKLS']), 0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->MultiCell(180, 6, "", 'B', 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(50, 8, "Med-Gas: ", 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $y = $pdf->GetY();
        if (($y + 6) >= 270) {
            $pdf->AddPage();
        }
        $pdf->MultiCell(40, 6, "1 Kreis O2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['1 Kreis O2'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "2 Kreis O2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['2 Kreis O2'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "1 Kreis VA: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['1 Kreis Va'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "2 Kreis VA: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['2 Kreis Va'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $y = $pdf->GetY();
        if (($y + 6) >= 270) {
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "1 Kreis DL5: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['1 Kreis DL-5'] === '0') {
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(22, 6, "2 Kreis DL5: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['2 Kreis DL-5'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL10: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['DL-10'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL-tech: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['DL-tech'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $y = $pdf->GetY();
        if (($y + 6) >= 270) {
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "CO2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['CO2'] === '0') {
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(22, 6, "N2O: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['N2O'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "NGA: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['NGA'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if (null != ($row['Anmerkung MedGas'])) {
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
        $pdf->Ln();
        $pdf->MultiCell(180, 6, "", 'B', 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(50, 8, "Bau-Statik: ", 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if (null != ($row['Anmerkung BauStatik'])) {
            $pdf->Ln();
            //$pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            //$pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140, br2nl($row['Anmerkung BauStatik']), false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:", 0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung BauStatik']), 0, 'L', 0, 0);
        }
        $pdf->Ln();
    }
    $rowHeightFirstLine = $pdf->getStringHeight(50, "ElementID", false, true, '', 1);
    // Wenn Seitenende? Überprüfen und neue Seite anfangen
    $y = $pdf->GetY();
    if (($y + 6 + 6 + 8 + 8 + $rowHeightFirstLine) >= 270) {
        $pdf->AddPage();
    }
    $pdf->MultiCell(180, 6, "", 'B', 'L', 0, 0);
    $pdf->Ln();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(50, 8, "Elemente im Raum: ", 0, 'L', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Ln();
    //$rowHeightFirstLine = $pdf->getStringHeight(50,"ElementID",false,true,'',1);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Gewerk", 'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "GHG", 'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "ElementID", 'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Var", 'B', 'C', 0, 0);
    $pdf->MultiCell(50, $rowHeightFirstLine, "Element", 'B', 'L', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Stk", 'B', 'C', 0, 0);
    $pdf->MultiCell(30, $rowHeightFirstLine, "Bestand", 'B', 'C', 0, 0);
    $pdf->Ln();

    // -------------------------Elemente im Raum laden--------------------------
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $result = $mysqli->query($sql);

    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $idRoombookEntry = 0;
    $bestandsCounter = 1;
    while ($row = $result->fetch_assoc()) {
        if ($idRoombookEntry != $row['id']) {
            $fill = !$fill;
            $bestandsCounter = 1;
            $pdf->SetFont('helvetica', '', 8);
            $rowHeightMainLine = $pdf->getStringHeight(50, $row['Bezeichnung'], false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightMainLine) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Gewerke_Nr'], 0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['GHG'], 0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['ElementID'], 0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Variante'], 0, 'C', $fill, 0);
            $pdf->MultiCell(50, $rowHeightMainLine, $row['Bezeichnung'], 0, 'L', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Anzahl'], 0, 'C', $fill, 0);
            if ($row['Neu/Bestand'] == 1) {
                $pdf->MultiCell(30, $rowHeightMainLine, "Nein", 0, 'C', $fill, 0);
            } else {
                $pdf->MultiCell(30, $rowHeightMainLine, "Ja", 0, 'C', $fill, 0);
            }

            //Zusatzinfo ausblenden
            /*
            $additionalRoombookData = "";
            foreach($variantenInfos as $array) {                 
                //---------------Ausgabe der Varianten-Parameter---------------------------
                if($array['elementID']===$row['TABELLE_Elemente_idTABELLE_Elemente']){                 
                    if($array['VarID']===$row['tabelle_Varianten_idtabelle_Varianten']){                     
                        $additionalRoombookData = $additionalRoombookData."\n".$array['Kategorie']."-".$array['Bezeichnung'].": ".$array['Wert']." ".$array['Einheit'];
                    }               
                }                                  
            }
            if($row['Standort']==1){
                $additionalRoombookData = $additionalRoombookData."\nStandort: Ja";
            }
            else{
                $additionalRoombookData = $additionalRoombookData."\nStandort: Nein";
            }
            if($row['Verwendung']==1){
                $additionalRoombookData = $additionalRoombookData."\nVerwendung: Ja";
            }
            else{
                $additionalRoombookData = $additionalRoombookData."\nVerwendung: Nein";
            }
            if(null !=($row['Kurzbeschreibung'])){
                $additionalRoombookData = $additionalRoombookData."\nKommentar: ".$row['Kurzbeschreibung'];
            }
            if(null !=($row['Inventarnummer'])){
                $additionalRoombookData = $additionalRoombookData."\nBestandsgerät ".$bestandsCounter.":\n     Inventarnummer: ".$row['Inventarnummer'];
            }
            if(null !=($row['Seriennummer'])){
                $additionalRoombookData = $additionalRoombookData."\n     Seriennummer: ".$row['Seriennummer'];
            }
            if(null !=($row['Anschaffungsjahr'])){
                $additionalRoombookData = $additionalRoombookData."\n     Anschaffungsjahr: ".$row['Anschaffungsjahr'];
            }
            if(null !=($row['Hersteller'])){
                $additionalRoombookData = $additionalRoombookData."\n     Gerät: ".$row['Hersteller']." ".$row['Typ'];
            }            

            if(null !=($additionalRoombookData)){
                $pdf->Ln();

                $pdf->SetFont('helvetica', 'I', 6);
                $rowHeight = $pdf->getStringHeight(50,$additionalRoombookData,false,true,'',1);
                // Wenn Seitenende? Überprüfen und neue Seite anfangen
                $y = $pdf->GetY();
                if (($y + $rowHeight) >= 270) {
                    $pdf->AddPage();
                }
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(50, $rowHeight, $additionalRoombookData,0, 'L', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(30, $rowHeight, "",0, 'C', $fill, 0);
                $bestandsCounter++;
            }
             * 
             */
            $idRoombookEntry = $row['id'];
            $pdf->Ln();
        } else {
            /* Keine Bestandsgeräte ausgeben
            $pdf->SetFont('helvetica', 'I', 6);

            $additionalRoombookExtraData = "";
            if(null !=($row['Inventarnummer'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."Bestandsgerät ".$bestandsCounter.":\n     Inventarnummer: ".$row['Inventarnummer'];
            }
            if(null !=($row['Seriennummer'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."\n     Seriennummer: ".$row['Seriennummer'];
            }
            if(null !=($row['Anschaffungsjahr'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."\n     Anschaffungsjahr: ".$row['Anschaffungsjahr'];
            }
            if(null !=($row['Hersteller'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."\n     Gerät: ".$row['Hersteller']." ".$row['Typ'];
            }
            $rowHeight = $pdf->getStringHeight(50,$additionalRoombookExtraData,false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeight) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(50, $rowHeight, $additionalRoombookExtraData,0, 'L', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(30, $rowHeight, "",0, 'C', $fill, 0);
            $bestandsCounter++;
             * */

        }
    }
}


//$pdf->MultiCell(50, 6, "Bereich",'B', 'L', 0, 0);
//$pdf->MultiCell(20, 6, "Geschoss",'B', 'C', 0, 0);
// MYSQL-Verbindung schließen
$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName("Raumbuch"), 'I');

//============================================================+
// END OF FILE
//============================================================+
