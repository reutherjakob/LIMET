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
        $this->Cell(0, 0, 'Medizintechnisches Raumbuch', 0, false, 'R', 0, '', 0, false, 'B', 'B');
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
$pdf->SetAuthor('LIMET Consulting und Planung für Medizintechnik');
$pdf->SetTitle('Raumbuch-MT');
$pdf->SetSubject('xxx');
$pdf->SetKeywords('xxx');

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
$pdf->AddPage('P', 'A4');

//Moneyformat
setlocale(LC_MONETARY, 'de_DE');

// Daten laden

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

// Raumdaten laden ----------------------------------
$sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung,  tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=".filter_input(INPUT_GET, 'roomID')."));";

$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) { 
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(100, 6, "Raum: ".$row['Raumbezeichnung'],0, 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Nummer: ".$row['Raumnr'],0, 'L', 0, 0);
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(100, 6, "Bereich: ".$row['Raumbereich Nutzer'],0, 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Geschoss: ".$row['Geschoss'],0, 'L', 0, 0);
    $pdf->Ln();
    $pdf->MultiCell(100, 6, "Projekt: ".$row['Projektname'],0, 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Bauteil: ".$row['Bauabschnitt'],0, 'L', 0, 0);
    $pdf->Ln();
    $pdf->MultiCell(100, 6, "Projektstatus: ".$row['Bezeichnung'],'B', 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Bauetappe: ".$row['Bauetappe'],'B', 'L', 0, 0);
}

$pdf->Ln();
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0);
$rowHeightFirstLine = $pdf->getStringHeight(50,"ElementID",false,true,'',1);
$pdf->MultiCell(15, $rowHeightFirstLine, "Gewerk",'B', 'C', 0, 0);
$pdf->MultiCell(15, $rowHeightFirstLine, "GHG",'B', 'C', 0, 0);
$pdf->MultiCell(20, $rowHeightFirstLine, "ElementID",'B', 'C', 0, 0);
$pdf->MultiCell(20, $rowHeightFirstLine, "Var",'B', 'C', 0, 0);
$pdf->MultiCell(50, $rowHeightFirstLine, "Element",'B', 'L', 0, 0);
$pdf->MultiCell(15, $rowHeightFirstLine, "Stk",'B', 'C', 0, 0);
$pdf->MultiCell(15, $rowHeightFirstLine, "Bestand",'B', 'C', 0, 0);
$pdf->MultiCell(15, $rowHeightFirstLine, "EP",'B', 'R', 0, 0);
$pdf->MultiCell(15, $rowHeightFirstLine, "PP",'B', 'R', 0, 0);
$pdf->Ln();


// VariantenInfos laden
$sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente
FROM tabelle_projekt_elementparameter
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)=14));";

$result1 = $mysqli->query($sql);
$variantenInfos = array();
while ($row = $result1->fetch_assoc()) { 
    $variantenInfos[$row['tabelle_elemente_idTABELLE_Elemente']]['tabelle_Varianten_idtabelle_Varianten'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $variantenInfos[$row['tabelle_elemente_idTABELLE_Elemente']]['tabelle_elemente_idTABELLE_Elemente'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $variantenInfos[$row['tabelle_elemente_idTABELLE_Elemente']]['Wert'] = $row['Wert'];
    $variantenInfos[$row['tabelle_elemente_idTABELLE_Elemente']]['Einheit'] = $row['Einheit'];
}


// Elemente im Raum laden
$sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_projekt_varianten_kosten.Kosten*tabelle_räume_has_tabelle_elemente.Anzahl AS PP
FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.idTABELLE_Räume)=".filter_input(INPUT_GET, 'roomID')."))
ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

$result = $mysqli->query($sql);


$fill = 0;
$pdf->SetFillColor(244, 244, 244);
$idRoombookEntry = 0;
$bestandsCounter = 1;
while ($row = $result->fetch_assoc()) {
    
    if($idRoombookEntry != $row['id']){
        $fill=!$fill;
        $bestandsCounter = 1;
        $pdf->SetFont('helvetica', '', 8);
        $rowHeightMainLine = $pdf->getStringHeight(50,$row['Bezeichnung'],false,true,'',1);
        // Wenn Seitenende? Überprüfen und neue Seite anfangen
        $y = $pdf->GetY();    
        if (($y + $rowHeightMainLine) >= 270) {
            $pdf->AddPage();
        }
        $pdf->MultiCell(15, $rowHeightMainLine, $row['Gewerke_Nr'],0, 'C', $fill, 0);
        $pdf->MultiCell(15, $rowHeightMainLine, $row['GHG'],0, 'C', $fill, 0);
        $pdf->MultiCell(20, $rowHeightMainLine, $row['ElementID'],0, 'C', $fill, 0);
        $pdf->MultiCell(20, $rowHeightMainLine, $row['Variante'],0, 'C', $fill, 0);
        $pdf->MultiCell(50, $rowHeightMainLine, $row['Bezeichnung'],0, 'L', $fill, 0);
        $pdf->MultiCell(15, $rowHeightMainLine, $row['Anzahl'],0, 'C', $fill, 0);
        if($row['Neu/Bestand']==1){
            $pdf->MultiCell(15, $rowHeightMainLine, "Nein",0, 'C', $fill, 0);
        }
        else{
            $pdf->MultiCell(15, $rowHeightMainLine, "Ja",0, 'C', $fill, 0);
        }
        $pdf->MultiCell(15, $rowHeightMainLine,  sprintf('%01.2f', $row["Kosten"]),0, 'R', $fill, 0);
        $pdf->MultiCell(15, $rowHeightMainLine,  sprintf('%01.2f', $row["PP"]),0, 'R', $fill, 0);
        
        $additionalRoombookData = "";
        foreach($variantenInfos as $array) {            
            if($array['tabelle_elemente_idTABELLE_Elemente']==$row['TABELLE_Elemente_idTABELLE_Elemente']){                
                if($array['tabelle_Varianten_idtabelle_Varianten']==$row['tabelle_Varianten_idtabelle_Varianten']){                     
                    $additionalRoombookData = $additionalRoombookData."Varianteninformation: ".$array['Wert']." ".$array['Einheit'];
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
    
        if(null != ($additionalRoombookData)){
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
        $idRoombookEntry = $row['id'];
    }
    else{
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
    }
    $pdf->Ln();    
    
}


//$pdf->MultiCell(50, 6, "Bereich",'B', 'L', 0, 0);
//$pdf->MultiCell(20, 6, "Geschoss",'B', 'C', 0, 0);

// close and output PDF document
$pdf->Output('Raumbuch-MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

