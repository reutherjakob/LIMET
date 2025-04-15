<?php

if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";

$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$_SESSION["PDFTITEL"] = "Betriebsorganisatorische Angaben";
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Betriebsorganisatoische Angaben ");
$pdf->SetFont('helvetica', '', 10);

$mysqli = utils_connect_sql();


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
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung
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
        $pdf->MultiCell(100, 6, "Raumfläche: " . $row['Nutzfläche'] . " m2", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: " . $row['Projektname'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: " . $row['Bauabschnitt'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projektstatus: " . $row['Bezeichnung'], 'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauetappe: " . $row['Bauetappe'], 'B', 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(50, 8, "Raum-Info: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
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
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "ÖVE E8101:", 0, 'R', 0, 0);
        $pdf->MultiCell(10, 6, " " . $row['Anwendungsgruppe'], 0, 'L', 0, 0);
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
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
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "H6020:", 0, 'R', 0, 0);
        $pdf->MultiCell(10, 6, " " . $row['H6020'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Abdunkelbarkeit: ", 'B', 'R', 0, 0);
        if ($row['Abdunkelbarkeit'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54), 'B', 'L', 0, 0);
        } else {
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52), 'B', 'L', 0, 0);
        }
        $pdf->MultiCell(100, 6, "", 'B', 'R', 0, 0);
        $pdf->Ln();

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(50, 8, "Gasversorgung: ", 0, 'L', 0, 0);
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
        $pdf->MultiCell(40, 6, "1 Kreis DL5: ", 'B', 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['1 Kreis DL-5'] === '0') {
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(54), 'B', 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(52), 'B', 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(22, 6, "2 Kreis DL5: ", 'B', 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['2 Kreis DL-5'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 'B', 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 'B', 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL10: ", 'B', 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['DL-10'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 'B', 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 'B', 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL-tech: ", 'B', 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['DL-tech'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 'B', 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 'B', 'L', 0, 0);
        }
        $pdf->MultiCell(40, 6, "", 'B', 'L', 0, 0);
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
            $pdf->Ln();
            $pdf->MultiCell(40, $rowHeightComment, "", 0, 'L', 0, 0);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung FunktionBO']), 0, 'L', 0, 0);
        }
        $pdf->Ln();

    }
}

// MYSQL-Verbindung schließen
$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('BO-Bericht'), 'I');

//============================================================+
// END OF FILE
//============================================================+
