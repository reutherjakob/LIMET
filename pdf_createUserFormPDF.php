<?php
//============================================================+
// 
// Begin       : 2008-03-04
// Last Update : 2018-09-03
//
// Description : Erstellt den Bauangaben Bericht
//               
//
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


function br2nl($string){
    $return= str_replace(array("<br/>"), "\n", $string);
    return $return;
}
// extend TCPF with custom functions
class MYPDF extends TCPDF {
    
    //Page header
    public function Header() {
        //Abfrage ob Titelblatt
        if ($this->numpages > 1){ 
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
            if($_SESSION["projectPlanungsphase"]=="Vorentwurf"){
                $this->Cell(0, 0, 'Medizintechnik Bedarfserhebung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            else{
                $this->Cell(0, 0, 'Medizintechnik Bedarfserhebung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->cell(0,0,'','B',0,'L');
        }
        // Titelblatt        
        else{
            // Verbindung herstellen
            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	            
            if (!$mysqli->set_charset("utf8")) {
                printf("Error loading character set utf8: %s\n", $mysqli->error);
                exit();
            }
            
            $roomIDs = filter_input(INPUT_GET, 'roomID');
            $teile = explode(",", $roomIDs);
            
            $sql = "SELECT tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`
                    FROM tabelle_räume INNER JOIN (tabelle_planungsphasen INNER JOIN tabelle_projekte ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen) ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte ";
            $i = 0;
            foreach ($teile as $valueOfRoomID) {
                if($i == 0){
                    $sql = $sql."WHERE tabelle_räume.idTABELLE_Räume=".$valueOfRoomID." ";
                }
                else{
                    $sql = $sql."OR tabelle_räume.idTABELLE_Räume=".$valueOfRoomID." ";
                }
                $i++;                                       
            }
            $sql = $sql."GROUP BY tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer` ORDER BY tabelle_räume.`Raumbereich Nutzer`;";
            $result = $mysqli->query($sql);
            $raumInfos = array();
            $raumInfosCounter = 0;    
            while ($row = $result->fetch_assoc()) { 
                $raumInfos[$raumInfosCounter]['Projektname'] = $row['Projektname'];
                $raumInfos[$raumInfosCounter]['Planungsphase'] = $row['Bezeichnung'];
                $raumInfos[$raumInfosCounter]['Raumbereich'] = $row['Raumbereich Nutzer'];
                $raumInfosCounter = $raumInfosCounter + 1;
            }
            
            $mysqli->close();
            // Set font
            $this->SetFont('helvetica', 'B', 15);
            // Title
            $this->SetY(50);
            $this->Cell(0, 0, $raumInfos[0]['Projektname'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, $raumInfos[0]['Planungsphase'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();
            if($_SESSION["projectPlanungsphase"]=="Vorentwurf"){
                $this->Cell(0, 0, 'Medizintechnik', 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();
                $this->Cell(0, 0, 'Bedarfserhebung', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            }
            else{
                $this->Cell(0, 0, 'Medizintechnik', 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();
                $this->Cell(0, 0, 'Bedarfserhebung', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->Ln();
            $this->Cell(0, 0, 'Funktionsstellen: ', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $raumInfosCounter = 0;
            $funktionsStellen = "";
            foreach ($raumInfos as $valueOfRaumInfos) {
                if($raumInfosCounter > 0){
                    $funktionsStellen = $funktionsStellen .', ';                    
                }
                $funktionsStellen = $funktionsStellen .$raumInfos[$raumInfosCounter]['Raumbereich'];  
                
                $raumInfosCounter = $raumInfosCounter + 1;
            }
            $this->Cell(0, 0, $funktionsStellen, 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->SetFont('helvetica', '', 12);
            $this->Cell(0, 0, "Stand: ".date('Y-m-d'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
            
            $this->SetFont('helvetica', '', 6);
            //LOGOS einfügen
            if($_SESSION["projectAusfuehrung"]==="MADER"){
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 145, 40, 50, 15, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);                
            }
            else{
                if($_SESSION["projectAusfuehrung"]==="LIMET"){
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 145, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                }
                else{
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 110, 40, 30, 13, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $image_file = 'Mader_Logo_neu.jpg';
                    $this->Image($image_file, 145, 41, 50, 13, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $this->SetY(60);
                    $this->SetX(110);                    
                    $this->Cell(0, 0, "ARGE LIMET-MADER", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "Zwerggase 6/1", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "8010 Graz", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Ln();
                    $this->Cell(0, 0, "Tel: +43 1 470 48 33 Dipl.-Ing. Jens Liebmann, MBA", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "Tel: +43 650 523 27 38 Dipl.-Ing. Peter Mader", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Ln();
                    $this->Cell(0, 0, "UID ATU 69334945", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "IBAN AT90 2081 5208 0067 8128", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "BIC STSPAT2GXXX", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                }
            }
            // Deckblatt beenden            
        }
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
$pdf->SetTitle('Bauangaben-MT');
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
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
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
   // $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung,  tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss
    //            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
     //           WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";
    
    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, 
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Nutzfläche_Soll, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, 
            tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
            tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O,  
            tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";
    
    
    
    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) { 
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(100, 6, "Raum: ".$row['Raumbezeichnung'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: ".$row['Raumnr'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 6, "Bereich: ".$row['Raumbereich Nutzer'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Geschoss: ".$row['Geschoss'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Raumfläche Ist: ".$row['Nutzfläche']." m2",0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Raumfläche Soll: ".$row['Nutzfläche_Soll']." m2",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: ".$row['Projektname'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: ".$row['Bauabschnitt'],0, 'L', 0, 0);        
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projektstatus: ".$row['Bezeichnung'],'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauetappe: ".$row['Bauetappe'],'B', 'L', 0, 0);            
        $pdf->Ln();     
        $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung FunktionBO']),false,true,'',1);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(40, $rowHeightComment, "Betriebsorganisation:",0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "RFP: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung FunktionBO']),0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Tätigkeit Arzt: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, 35, "",1, 'R', 0, 0);
        $pdf->Ln();
        $pdf->Ln(6);
        $pdf->MultiCell(40, 6, "Tätigkeit Pflege: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, 35, "",1, 'R', 0, 0);
        $pdf->Ln();
        $pdf->Ln(6);        
        $pdf->MultiCell(40, 6, "Patientenwege: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, "Gehend: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "Rollstuhl: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "Liege: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(10, 6, "Bett: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Ln();           
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(40,8, "Ausstattung: ",'T', 'L', 0, 0);
        $pdf->MultiCell(140,8, "",'T', 'L', 0, 0);
        $pdf->Ln();        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Gase: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, "O2: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);  
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);  
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "VA: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);  
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, "CO2: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "N2O: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "NGA: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);   
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Geräte: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, 70, "",1, 'R', 0, 0);
        $pdf->Ln();
        $pdf->Ln(6);        
        $pdf->SetTextColor(0);
        $pdf->MultiCell(40, 6, "Strahlenanwendung: ",0, 'R', 0, 0);
        if($row['Strahlenanwendung']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        }
        else{
            if($row['Strahlenanwendung']==='1'){
                $pdf->SetFont('zapfdingbats', '', 10);
                //grün
                $pdf->SetTextColor(0, 255, 0); 
                $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
            }
            else{
                $pdf->MultiCell(15, 6, "Quasi stationär",0, 'L', 0, 0);
            }
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);    
        $pdf->MultiCell(31, 6, "Laseranwendung: ",0, 'R', 0, 0);
        if($row['Laseranwendung']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);            
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(30, 6, "Abdunkelbarkeit: ",0, 'R', 0, 0);
        if($row['Abdunkelbarkeit']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "Sterilgut: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);               
        $pdf->Ln();
    }
    
    
}

// MYSQL-Verbindung schließen
$mysqli ->close();

// close and output PDF document
$pdf->Output('Formular-Nutzer.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
