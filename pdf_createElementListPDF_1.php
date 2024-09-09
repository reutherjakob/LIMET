<?php

//============================================================+
// File name   : pdf_createElementListPDF.php
// Begin       : 2017-09-07
// Last Update : 2017-09-07
//
// Description : Erstellt ein PDF mit der Auflistung aller Element in einem Projekt
//
// Author: Jakob Reuther
//
//============================================================+

include '_utils.php';
require_once('TCPDF-master/TCPDF-master/tcpdf.php');

class MYPDF extends TCPDF {

    public function Header() {
        if ($_SESSION["projectAusfuehrung"] === "MADER") {
            $image_file = 'Mader_Logo_neu.jpg';
            $this->Image($image_file, 15, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        } else {
            if ($_SESSION["projectAusfuehrung"] === "LIMET") {
                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            } else {
                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 38, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            }
        }
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 0, 'Medizintechnische Elementliste', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $this->Ln();
        $this->cell(0, 0, '', 'B', 0, 'L');
    }

    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

session_start();
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung für Medizintechnik');
$pdf->SetTitle('Elementliste-MT');
$pdf->SetSubject('Elementliste');
$pdf->SetKeywords('Elementliste');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(20);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->SetFont('helvetica', '', 10);
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
$result1 = $mysqli->query($sql);
$variantenInfos = array();
$variantenInfosCounter = 0;
while ($row = $result1->fetch_assoc()) {
    $variantenInfos[$variantenInfosCounter]['VarID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $variantenInfos[$variantenInfosCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $variantenInfos[$variantenInfosCounter]['Wert'] = $row['Wert'];
    $variantenInfos[$variantenInfosCounter]['Einheit'] = $row['Einheit'];
    $variantenInfos[$variantenInfosCounter]['Kategorie'] = $row['Kategorie'];
    $variantenInfos[$variantenInfosCounter]['Bezeichnung'] = $row['Bezeichnung'];
    $variantenInfosCounter = $variantenInfosCounter + 1;
}
$sql = "SELECT tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`
FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Anzahl)>0));";
$result2 = $mysqli->query($sql);
$raeumeMitElement = array();
$raeumeMitElementCounter = 0;
while ($row = $result2->fetch_assoc()) {
    $raeumeMitElement[$raeumeMitElementCounter]['elementID'] = $row['TABELLE_Elemente_idTABELLE_Elemente'];
    $raeumeMitElement[$raeumeMitElementCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $raeumeMitElement[$raeumeMitElementCounter]['Stk'] = $row['Anzahl'];
    $raeumeMitElement[$raeumeMitElementCounter]['raumNr'] = $row['Raumnr'];
    $raeumeMitElement[$raeumeMitElementCounter]['raum'] = $row['Raumbezeichnung'];
    $raeumeMitElement[$raeumeMitElementCounter]['Bestand'] = $row['Neu/Bestand'];
    $raeumeMitElementCounter = $raeumeMitElementCounter + 1;
}
$pdf->AddPage('L', 'A4');
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0);

$cellWidths = [10, 20, 40, 17, 15, 140, 23];
$cellHeaders = ["Stk", "ElementID", "Element", "Variante", "Bestand", "Räume", "Varianteninfo"];

// Function to check for new page
function check_for_new_page($pdf, $rowHeightFinal, $cellWidths, $cellHeaders, $rowHeightFirstLine) {
    $y = $pdf->GetY();
    if (($y + $rowHeightFinal) >= 180) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        foreach ($cellHeaders as $index => $header) {
            $pdf->MultiCell($cellWidths[$index], $rowHeightFirstLine, $header, 'B', 'C', 0, 0);
        }
        $pdf->SetFont('helvetica', 'I', 6);
        $pdf->Ln(); 
    }
}

// Function to get row height
function get_row_height($pdf, $text, $width) {
    return $pdf->getStringHeight($width, $text, false, true, '', 1);
}

// Print headers
$rowHeightFirstLine = get_row_height($pdf, "ElementID", 50);
foreach ($cellHeaders as $index => $header) {
    $pdf->MultiCell($cellWidths[$index], $rowHeightFirstLine, $header, 'B', 'C', 0, 0);
}
$pdf->Ln();

$fill = 0;
$pdf->SetFillColor(244, 244, 244);

$sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
        FROM tabelle_elemente 
        INNER JOIN (tabelle_varianten 
        INNER JOIN (tabelle_räume 
        INNER JOIN tabelle_räume_has_tabelle_elemente 
        ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
        ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) 
        ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
        WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . " 
        AND tabelle_räume_has_tabelle_elemente.Anzahl > 0 
        GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
        ORDER BY tabelle_elemente.ElementID;";
$result3 = $mysqli->query($sql);

$pdf->SetFont('helvetica', 'I', 6);

while ($row = $result3->fetch_assoc()) {
    $fill = !$fill;
    $raeume = "";
    foreach ($raeumeMitElement as $array) {
        if ($array['elementID'] === $row['TABELLE_Elemente_idTABELLE_Elemente'] && $array['variantenID'] === $row['idtabelle_Varianten'] && $array['Bestand'] === $row['Neu/Bestand']) {
            $raeume .= "" . $array['raumNr'] . "-" . $array['raum'] . ": " . $array['Stk'] . " Stk;  ";
        }
    }
    $varInfo = "";
    foreach ($variantenInfos as $array1) {
        if ($array1['elementID'] === $row['TABELLE_Elemente_idTABELLE_Elemente'] && $array1['VarID'] === $row['idtabelle_Varianten']) {
            $varInfo .= "" . $array1['Kategorie'] . "-" . $array1['Bezeichnung'] . ": " . $array1['Wert'] . " " . $array1['Einheit'] . ";  ";
        }
    }


    $rowHeightbez = get_row_height($pdf, $row['Bezeichnung'], $cellWidths[2]);
    $rowHeight = get_row_height($pdf, $raeume, $cellWidths[5]);
    $rowHeight1 = get_row_height($pdf, $varInfo, $cellWidths[6]);
    $rowHeightFinal = max($rowHeight, $rowHeight1, $rowHeightbez);

    check_for_new_page($pdf, $rowHeightFinal, $cellWidths, $cellHeaders, $rowHeightFirstLine);

    $cellData = [
        $row['SummevonAnzahl'],
        $row['ElementID'],
        $row['Bezeichnung'],
        $row['Variante'],
        $row['Neu/Bestand'] == '1' ? "Nein" : "Ja",
        $raeume,
        $varInfo
    ];

    foreach ($cellData as $index => $data) {
        $pdf->MultiCell($cellWidths[$index], $rowHeightFinal, $data, 0, $index == 5 ? 'L' : 'C', $fill, 0);
    }
    $pdf->Ln();
    $pdf->Ln(1);
}
$pdf->Output('Elementeliste-MT.pdf', 'I');
