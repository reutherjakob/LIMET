<?php
//============================================================+
// File name   : example_011.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 011 for TCPDF class
//               Colored Table (very simple table)
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Colored Table
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('TCPDF-main/TCPDF-main/tcpdf.php');

// extend TCPF with custom functions
class MYPDF extends TCPDF {
    
    //Page header
    public function Header() {
        // Logo
        if($_SESSION["projectAusfuehrung"]==="MADER"){
            $image_file = 'Mader_Logo_neu.jpg';
            $this->Image($image_file, 15, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        }
        else{
            if($_SESSION["projectAusfuehrung"]==="LIMET"){
                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            }
            else{
                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 38, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            }
            
        }
        
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Title
        // Title        
        if($_SESSION["projectPlanungsphase"]=="Vorentwurf"){
            $this->Cell(0, 0, 'Medizintechnische Kostenschätzung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        }
        else{
            $this->Cell(0, 0, 'Medizintechnische Kostenberechnung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        }
        //$this->Cell(0, 0, 'Gesamt-Kosten', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $this->Ln();
        $this->cell(0,0,'','B',0,'L');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->cell(0,0,'','T',0,'L');
        $this->Ln();
        $tDate=date('Y-m-d');
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite '.$this->getAliasNumPage().' von '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
    
}

session_start();
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
$pdf->SetTitle('Gesamt Kosten');
$pdf->SetSubject('MT-Kosten');
$pdf->SetKeywords('MT-Kosten');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage('', 'A4');


// Daten laden

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

$sql = "SELECT tabelle_projekte.Projektname, tabelle_projekte.Preisbasis,  tabelle_planungsphasen.Bezeichnung
    FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
    WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."));";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

//TITEL einfügen
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(180, 6, "Kostenberechnung nach Gewerken und GHG", 'TLR', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(180/2, 6, "Projekt: ".$row['Projektname'],'L', 'L', 0, 0);
$pdf->MultiCell(180/2, 6, "Preisbasis: ".$row['Preisbasis'],'R', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(180, 6, "Projektphase: ".$row['Bezeichnung'],'BRL', 'L', 0, 0);
$pdf->Ln();           
$pdf->Ln();
$pdf->Ln();



// Gewerke und GHG laden ----------------------------------
$sql = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, Sum(`Kosten`*`Anzahl`) AS PP, tabelle_auftraggeber_gewerke.Bezeichnung AS GewerkBezeichnung , tabelle_auftraggeber_ghg.Bezeichnung AS GHGBezeichnung
FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG
ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG;";

$result = $mysqli->query($sql);

$raumbereichData = array(); // Array zum Zwischenspeichern der Gewerke/GHG/Summen
    
$j = 0;
while ($row = $result->fetch_assoc()) { 
    $raumbereichData[$j]['Gewerke_Nr']= $row["Gewerke_Nr"];
    $raumbereichData[$j]['Gewerke_Bezeichnung']= $row["GewerkBezeichnung"];
    $raumbereichData[$j]['GHG']= $row["GHG"];
    $raumbereichData[$j]['GHG_Bezeichnung']= $row["GHGBezeichnung"];
    $raumbereichData[$j]['PP']= $row["PP"];
    $j++;
}


setlocale(LC_MONETARY,"de_DE");
$pdf->SetFillColor(244, 244, 244);
$sumGewerk = 0;
$sumGewerkBestand = 0; 
$sumGewerkNeu = 0; 
$i = 0;
$gewerk = "";
$ghg = "";
$fill = 0;

foreach($raumbereichData as $rowData2) {  
    if($rowData2["Gewerke_Nr"] !== $gewerk){
        $pdf->SetFont('helvetica', 'B', 10);    
        if($i > 0){
            $pdf->MultiCell(120, 4, money_format('€ %!n', $sumGewerk), 'T', 'R', 0, 0);
            $pdf->MultiCell(30, 4, money_format('€ %!n', $sumGewerkNeu), 'T', 'R', 0, 0);
            $pdf->MultiCell(30, 4, money_format('€ %!n', $sumGewerkBestand), 'T', 'R', 0, 0);
            $sumGesamt = $sumGesamt + $sumGewerk;
            $sumGesamtNeu = $sumGesamtNeu + $sumGewerkNeu;
            $sumGesamtBestand = $sumGesamtBestand + $sumGewerkBestand;
            $sumGewerk = 0;
            $sumGewerkBestand = 0;  
            $sumGewerkNeu = 0;
            $pdf->Ln();
            $pdf->Ln();
        }
        $pdf->MultiCell(70, 6, "Gewerk ".$rowData2["Gewerke_Nr"]." ".$rowData2["Gewerke_Bezeichnung"],'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(50, 6, "Kosten",'B', 'R', 0, 0);
        $pdf->MultiCell(30, 6, "davon Neu",'B', 'R', 0, 0);
        $pdf->MultiCell(30, 6, "davon Bestand",'B', 'R', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->MultiCell(20, 6, "",'', 'L', 0, 0);
        if(empty($rowData2["GHG"])){
            $pdf->MultiCell(50, 6, "ohne Zuteilung: ",'', 'L', 0, 0);
        }
        else{
            $pdf->MultiCell(50, 6, "GHG: ".$rowData2["GHG"]." ".$rowData2["GHG_Bezeichnung"],'', 'L', 0, 0);
        }
        $pdf->MultiCell(50, 4, money_format('€ %!n', $rowData2["PP"]), 0, 'R', 0, 0);                
    }
    else{
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->MultiCell(20, 6, "",'', 'L', 0, 0);
        if(empty($rowData2["GHG"])){
            $pdf->MultiCell(50, 6, "ohne Zuteilung: ",'', 'L', 0, 0);
        }
        else{
            $pdf->MultiCell(50, 6, "GHG: ".$rowData2["GHG"]." ".$rowData2["GHG_Bezeichnung"],'', 'L', 0, 0);
        }
        $pdf->MultiCell(50, 4, money_format('€ %!n', $rowData2["PP"]), 0, 'R', 0, 0);
    }       
    
    // Neusumme ermitteln ----------------------------------
    if($rowData2["GHG"] == ""){
            $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG IS NULL);";
    }
    else{
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG='".$rowData2["GHG"]."');";
    }
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    if ( null != ($row['PP_neu'])){
        $pdf->MultiCell(30, 4, money_format('€ %!n', $row["PP_neu"]), 0, 'R', $fill, 0);
        //$sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
    }
    else{
        $pdf->MultiCell(30, 4,  sprintf('%01.2f', 0), 0, 'R', $fill, 0);
    }
    $sumGewerkNeu = $sumGewerkNeu + $row["PP_neu"];    
    //--------------------------------------------------------------    
    
    // Bestandssumme ermitteln ----------------------------------
    if($rowData2["GHG"] == ""){
            $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG IS NULL);";
    }
    else{
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG='".$rowData2["GHG"]."');";
    }
    
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    if ( null != ($row['PP'])  ){
        $pdf->MultiCell(30, 4, money_format('€ %!n', $row["PP"]), 0, 'R', $fill, 0);
        //$sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
    }
    else{
        $pdf->MultiCell(30, 4,  sprintf('%01.2f', 0), 0, 'R', $fill, 0);
    }
    //--------------------------------------------------------------
    
    $i++;
    $gewerk = $rowData2["Gewerke_Nr"];
    $sumGewerk = $sumGewerk + $rowData2["PP"];
    $sumGewerkBestand = $sumGewerkBestand + $row["PP"];
    
    $pdf->Ln();    
}

// Letzte Gesamtsumme bilden
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(120, 4, money_format('€ %!n', $sumGewerk), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4, money_format('€ %!n', $sumGewerkNeu), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4, money_format('€ %!n', $sumGewerkBestand), 'T', 'R', 0, 0);
$sumGesamt = $sumGesamt + $sumGewerk;
$sumGesamtNeu = $sumGesamtNeu + $sumGewerkNeu;
$sumGesamtBestand = $sumGesamtBestand + $sumGewerkBestand;
$pdf->Ln();
$pdf->Ln(); 
$pdf->MultiCell(90, 4, "GESAMT: ", 'T', 'L', 0, 0);
$pdf->MultiCell(30, 4, money_format('€ %!n', $sumGesamt), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4, money_format('€ %!n', $sumGesamtNeu), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4, money_format('€ %!n', $sumGesamtBestand), 'T', 'R', 0, 0);

// ---------------------------------------------------------


// close and output PDF document
$pdf->Output('Kosten_Gewerke-GHG.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

