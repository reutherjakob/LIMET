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
require_once('TCPDF-master/TCPDF-master/tcpdf.php');


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
                $image_file = 'MADER_Logo.png';
                $this->Image($image_file, 15, 5, 13, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            }
            else{
                if($_SESSION["projectAusfuehrung"]==="LIMET"){
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                }
                else{
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $image_file = 'MADER_Logo.png';
                    $this->Image($image_file, 38, 5, 13, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                }

            }
            // Set font
            $this->SetFont('helvetica', '', 8);
            // Title
            if($_SESSION["projectPlanungsphase"]=="Vorentwurf"){
                $this->Cell(0, 0, 'Labortechnische Vorbemessungsangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            else{
                $this->Cell(0, 0, 'Labortechnische Bauangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
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
                $this->Cell(0, 0, 'Labortechnische', 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();
                $this->Cell(0, 0, 'Vorbemessungsangaben', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            }
            else{
                $this->Cell(0, 0, 'Labortechnische', 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();
                $this->Cell(0, 0, 'Bauangaben', 0, false, 'L', 0, '', 0, false, 'B', 'B');
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
                $image_file = 'MADER_Logo.png';
                $this->Image($image_file, 145, 40, 50, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);                
            }        
            
            else{
                if($_SESSION["projectAusfuehrung"]==="LIMET"){
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 110, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                }
                else{
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 110, 40, 30, 13, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $image_file = 'MADER_Logo.png';
                    $this->Image($image_file, 145, 41, 15, 13, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
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



// -----------------Variantenparameter Info laden----------------------------
$sql ="SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
ORDER BY tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie;";

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


// RaumIDs laden über GET
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);


foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');
    // Raumdaten laden ----------------------------------
    
    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.`Funktionelle Raum Nr`, tabelle_räume.`Raumtyp BH`, tabelle_räume.Raumhoehe, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Raumhoehe_Soll`,
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Nutzfläche_Soll, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP, 
            tabelle_räume.ISO, tabelle_räume.`O2`, tabelle_räume.`VA`, tabelle_räume.`DL-10`, tabelle_räume.`DL-5`, tabelle_räume.CO2, tabelle_räume.N2, tabelle_räume.Ar, tabelle_räume.He, tabelle_räume.`He-RF`, tabelle_räume.H2,   
            tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, tabelle_räume.`AR_Akustik`, tabelle_räume.`ET_EMV`, tabelle_räume.`AR_AnwesendePersonen`,
            tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, 
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.AR_Schwingungsklasse, tabelle_räume.`AR_APs`, tabelle_räume.`AR_Belichtung-nat`,
            tabelle_räume.RaumNr_Bestand, tabelle_räume.Gebaeude_Bestand, tabelle_räume.`ET_EMV_ja-nein`, tabelle_räume.`EL_Leistungsbedarf_W_pro_m2`, tabelle_räume.`HT_Waermeabgabe`, tabelle_räume.`HT_Luftwechsel 1/h`, tabelle_räume.`HT_Geraeteabluft m3/h`, tabelle_räume.`HT_Kühlwasserleistung_W`,
            tabelle_räume.`Allgemeine Hygieneklasse`
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";
    
    
    
    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) { 
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(100, 6, "Raum: ".$row['Raumbezeichnung'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: ".$row['Raumnr'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 6, "Raumtyp-Nutzer: ".$row['Raumtyp BH'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Funktionale-RaumNr: ".$row['Funktionelle Raum Nr'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Bereich: ".$row['Raumbereich Nutzer'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Geschoss: ".$row['Geschoss'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Raumfläche: Ist: ".$row['Nutzfläche']." m2 / Soll: ".$row['Nutzfläche_Soll']." m2",0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Raumhöhe Ist: ".$row['Raumhoehe']." m / Soll: ".$row['Raumhoehe_Soll']." m",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: ".$row['Projektname'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Raumhöhe 2: ".$row['Raumhoehe 2']." m",0, 'L', 0, 0);        
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Bestandsraumnr: ".$row['RaumNr_Bestand'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bestandsgebäude: ".$row['Gebaeude_Bestand'],0, 'L', 0, 0);
        $pdf->Ln();        
        $pdf->MultiCell(100, 6, "Projektstatus: ".$row['Bezeichnung'],'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauetappe: ".$row['Bauetappe'],'B', 'L', 0, 0);            
        $pdf->Ln();
        if(strlen($row['Anmerkung FunktionBO'])>0){       
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung FunktionBO']),false,true,'',1);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(40, $rowHeightComment, "BO:",'B', 'L', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung FunktionBO']),'B', 'L', 0, 0);
            $pdf->Ln();           
        }
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(22,8, "Allgemein: ",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0);
        $pdf->MultiCell(40, 6, "Strahlenanwendung: ",0, 'R', 0, 0);
        if($row['Strahlenanwendung']==='0'){
            $pdf->SetFont(zapfdingbats, '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            if($row['Strahlenanwendung']==='1'){
                $pdf->SetFont(zapfdingbats, '', 10);
                //grün
                $pdf->SetTextColor(0, 255, 0); 
                $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
            }
            else{
                $pdf->MultiCell(40, 6, "Quasi stationär",0, 'L', 0, 0);
            }
        }
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(60, 6, "Anzahl APs AStV: ",0, 'R', 0, 0);  
        $pdf->MultiCell(40, 6, $row['AR_APs'],0, 'L', 0, 0); 
        //schwarz 
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();        
        $pdf->MultiCell(40, 6, "Laseranwendung: ",0, 'R', 0, 0);
        if($row['Laseranwendung']==='0'){
            $pdf->SetFont(zapfdingbats, '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont(zapfdingbats, '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);            
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(60, 6, "Anwesende Personen: ",0, 'R', 0, 0); 
        $pdf->MultiCell(40, 6, $row['AR_AnwesendePersonen'],0, 'L', 0, 0);  
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Abdunkelbarkeit: ",0, 'R', 0, 0);
        if($row['Abdunkelbarkeit']==='0'){
            $pdf->SetFont(zapfdingbats, '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont(zapfdingbats, '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(60, 6, "Natürliche Belichtung: ",0, 'R', 0, 0);  
        if($row['AR_Belichtung-nat']==='1'){
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->SetFont(zapfdingbats, '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont(zapfdingbats, '', 10);            
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }                
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Rauminfo: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 6, $row['Allgemeine Hygieneklasse'],0, 'L', 0, 0); 
        if(strlen($row['Anmerkung Geräte'])>0){
            //$pdf->Ln();
            //$pdf->MultiCell(180, 6, "",'T', 'L', 0, 0);
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung Geräte']),false,true,'',1);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(40, $rowHeightComment, "Wesentliche Ausstattung:",'T', 'L', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung Geräte']),'T', 'L', 0, 0);           
        }
        $pdf->Ln();
        $pdf->MultiCell(180, 6, "",'B', 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(20,8, "Elektro: ",0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40,8, "Leistung: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " ".$row['EL_Leistungsbedarf_W_pro_m2']." W/m2",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "ÖVE E8101:",0, 'R', 0, 0);
        $pdf->MultiCell(10, 6, " ".$row['Anwendungsgruppe'],0, 'L', 0, 0);
        $pdf->MultiCell(20, 6, "AV: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['AV']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "USV: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['USV']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Netzwerk: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['IT Anbindung']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 10);
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "FB ÖNORM B5220:",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " ".$row['Fussboden OENORM B5220'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "EMV: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['ET_EMV_ja-nein']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 10);
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if(strlen($row['ET_EMV'])>0){
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['ET_EMV']),false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "EMV:",0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['ET_EMV']),0, 'L', 0, 0);           
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if(strlen($row['Anmerkung Elektro'])>0){
            $pdf->Ln();
            $pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung Elektro']),false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:",0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung Elektro']),0, 'L', 0, 0);           
        }
        $pdf->Ln();
        $y = $pdf->GetY();    
        if (($y + 30) >= 270) {
            $pdf->AddPage();
        }
        $pdf->MultiCell(180, 6, "",'B', 'L', 0, 0);
        $pdf->Ln();        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(50,8, "Haustechnik: ",0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40,8, "Wärme: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " ".$row['HT_Waermeabgabe']." W/m2",0, 'L', 0, 0);
        $pdf->MultiCell(40,8, "Luftwechsel: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " ".$row['HT_Luftwechsel 1/h']." /h",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40,8, "Geräteabluft: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " ".$row['HT_Geraeteabluft m3/h']." m3/h",0, 'L', 0, 0);
        $pdf->MultiCell(40,8, "Kühlwasserleistung: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " ".$row['HT_Kühlwasserleistung_W']." W",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "ISO-14644:",0, 'R', 0, 0);
        $pdf->MultiCell(10, 6, " ".$row['ISO'],0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if(strlen($row['Anmerkung HKLS'])>0){
            $pdf->Ln();
            $pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung HKLS']),false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:",0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung HKLS']),0, 'L', 0, 0);           
        }
        $pdf->Ln();
        //$pdf->MultiCell(180, 6, "",'B', 'L', 0, 0);              
        // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + 8) >= 270) {
                $pdf->AddPage();
            }                
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180,8, "Gase: ", 'T', 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);        
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "O2: ", 0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['O2']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "VA: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['VA']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL-5: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['DL-5']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL-10: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['DL-10']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "CO2: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['CO2']==='0'){            
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + 6) >= 270) {
                $pdf->AddPage();
            }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "N2: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['N2']==='0'){            
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(22, 6, "Ar: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['Ar']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "He: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['He']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "He-RF: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['He-RF']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "H2: ",0, 'R', 0, 0);
        $pdf->SetFont(zapfdingbats, '', 10);
        if($row['H2']==='0'){            
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }        
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if(strlen($row['Anmerkung MedGas'])>0){
            $pdf->Ln();
            $pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung MedGas']),false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:",0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung MedGas']),0, 'L', 0, 0);           
        }

        $pdf->Ln();
        $pdf->MultiCell(180, 6, "",'B', 'L', 0, 0);      
        $pdf->Ln();
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(50,8, "Bau-Statik: ",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Schwingungsklasse: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 6, $row['AR_Schwingungsklasse'],0, 'L', 0, 0);        
        
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        if(strlen($row['AR_Akustik'])>0){
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['AR_Akustik']),false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Akustik:",0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['AR_Akustik']),0, 'L', 0, 0);           
        }
        $pdf->SetFont('helvetica', '', 10);
        if(strlen($row['Anmerkung BauStatik'])>0){
            $pdf->Ln();
            $pdf->MultiCell(180, 6, "",0, 'L', 0, 0);
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung BauStatik']),false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(40, $rowHeightComment, "Anmerkung:",0, 'R', 0, 0);
            $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung BauStatik']),0, 'L', 0, 0);           
        }
        $pdf->Ln();
    }     
}

// MYSQL-Verbindung schließen
$mysqli ->close();


//$pdf->MultiCell(50, 6, "Bereich",'B', 'L', 0, 0);
//$pdf->MultiCell(20, 6, "Geschoss",'B', 'C', 0, 0);

// close and output PDF document
$pdf->Output('Raumbuch-MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
