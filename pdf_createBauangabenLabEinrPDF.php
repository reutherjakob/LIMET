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
            $this->Ln();
            $this->Cell(0, 0, 'GENERALPLANER fasch&fuchs.architekten          LABORPLANUNG LIMET MADER', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            if($_SESSION["projectAusfuehrung"]==="MADER"){                                
                $image_file = 'MADER_Logo.png';
                $this->SetFont('helvetica', '', 8);                
                $this->Image($image_file, 15, 5, 13, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            }
            else{
                if($_SESSION["projectAusfuehrung"]==="LIMET"){
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                }
                else{
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 158, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $image_file = 'MADER_Logo.png';
                    $this->Image($image_file, 180, 5, 13, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                }
            }
            // Set font
            $this->SetFont('helvetica', '', 8);
            // Title
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
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Nutzfläche_Soll, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.Laserklasse, tabelle_räume.H6020, tabelle_räume.GMP, 
            tabelle_räume.ISO, tabelle_räume.`O2`, tabelle_räume.`VA`, tabelle_räume.`DL-10`, tabelle_räume.`DL-5`, tabelle_räume.CO2, tabelle_räume.N2, tabelle_räume.Ar, tabelle_räume.He, tabelle_räume.`He-RF`, tabelle_räume.H2,   
            tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, tabelle_räume.`AR_Akustik`, tabelle_räume.`ET_EMV`, tabelle_räume.`AR_AnwesendePersonen`,
            tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, 
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.AR_Schwingungsklasse, tabelle_räume.`AR_APs`, tabelle_räume.`AR_Belichtung-nat`,
            tabelle_räume.RaumNr_Bestand, tabelle_räume.Gebaeude_Bestand, tabelle_räume.`ET_EMV_ja-nein`, tabelle_räume.`EL_Leistungsbedarf_W_pro_m2`, tabelle_räume.`HT_Waermeabgabe`, tabelle_räume.`HT_Luftwechsel 1/h`, tabelle_räume.`HT_Abluft_Sicherheitsschrank_Stk`, tabelle_räume.`HT_Kühlwasserleistung_W`,
            tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.`O2_Mangel`, tabelle_räume.`CO2_Melder`, tabelle_räume.`ET_Digestorium_MSR_230V_SV_Stk`, tabelle_räume.`HT_Spuele_Stk`, tabelle_räume.`HT_Notdusche`,
            tabelle_räume.`Wasser Qual 3`, tabelle_räume.`Wasser Qual 3 l/min`, tabelle_räume.`Wasser Qual 2`, tabelle_räume.`Wasser Qual 2 l/Tag`, tabelle_räume.`Wasser Qual 1`, tabelle_räume.`Wasser Qual 1 l/Tag`,
            tabelle_räume.`HT_Abluft_Digestorium_Stk`, tabelle_räume.`HT_Abluft_Sicherheitsschrank_Unterbau_Stk`, tabelle_räume.`HT_Punktabsaugung_Stk`, tabelle_räume.`HT_Abluft_Vakuumpumpe`, tabelle_räume.`HT_Abluft_Rauchgasabzug_Stk`,
            tabelle_räume.`HT_Abluft_Esse_Stk`, tabelle_räume.`HT_Abluft_Schweissabsaugung_Stk`, tabelle_räume.`DL ISO 8573`, tabelle_räume.`DL l/min`, tabelle_räume.`O2 l/min`, tabelle_räume.`O2 Reinheit`, tabelle_räume.`CO2 l/min`, tabelle_räume.`CO2 Reinheit`, 
            tabelle_räume.`N2 Reinheit`, tabelle_räume.`N2 l/min`, tabelle_räume.`Ar Reinheit`, tabelle_räume.`Ar l/min`,
            tabelle_räume.`H2 Reinheit`, tabelle_räume.`H2 l/min`, tabelle_räume.`He Reinheit`, tabelle_räume.`He l/min`, tabelle_räume.`LN`, tabelle_räume.`LN l/Tag`,
            tabelle_räume.`ET_RJ45-Ports`, tabelle_räume.`ET_5x10mm2_USV_Stk`, tabelle_räume.`ET_32A_3Phasig_Einzelanschluss`, tabelle_räume.`ET_64A_3Phasig_Einzelanschluss`, tabelle_räume.`ET_16A_3Phasig_Einzelanschluss`, tabelle_räume.`ET_5x10mm2_AV_Stk`,
            tabelle_räume.`ET_5x10mm2_Digestorium_Stk`, tabelle_räume.`DL-tech`, tabelle_räume.`Raumnummer_Nutzer`, tabelle_räume.`Volumen`, tabelle_räume.`Fussboden`, tabelle_räume.`Decke`, tabelle_räume.`AR_APs`,
            tabelle_räume.`Anmerkung AR`, tabelle_räume.`Belichtungsfläche`, tabelle_räume.`AP_Gefaehrdung`, tabelle_räume.`EL_Beleuchtungsstaerke`, tabelle_räume.`EL_Not_Aus`,
            tabelle_räume.`HT_Belueftung`, tabelle_räume.`HT_Entlueftung`, tabelle_räume.`HT_Kuehlung`, tabelle_räume.`HT_Kaelteabgabe_Typ`, tabelle_räume.`HT_Heizung`, tabelle_räume.`HT_Waermeabgabe_Typ`,
            tabelle_räume.`PHY_Akustik_Typ`, tabelle_räume.`PHY_Akustik_Flaeche`, tabelle_räume.`PHY_Akustik_Schallgrad`,tabelle_räume.`PHY_LRV`, tabelle_räume.`Taetigkeiten`, tabelle_räume.`AP_Geistige`,
            tabelle_räume.`VEXAT_Zone`
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";       
            
    
    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) { 
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: ".$row['Projektname'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: ".$row['Raumnr'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Raum: ".$row['Raumbezeichnung'],0, 'L', 0, 0);
        $pdf->MultiCell(100, 6, "RaumNr-Nutzer: ".$row['Raumnummer_Nutzer'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);        
        $pdf->MultiCell(100, 5, "Cluster: ".$row['Raumbereich Nutzer'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 5, "Geschoss: ".$row['Geschoss'],0, 'L', 0, 0);        
        $pdf->Ln();                  
        $pdf->SetFont('helvetica', 'B', 10);
        // set color for background
        $pdf->SetFillColor(80, 136, 44);        
        $pdf->MultiCell(180, 5, 'Allgemein', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();        
        $pdf->MultiCell(100, 5, "Raumfläche: ".$row['Nutzfläche']." m2",0, 'L', 0, 0);
        $pdf->MultiCell(80, 5, "Raumhöhe: ".$row['Raumhoehe']." m",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(80, 5, "Volumen: ".$row['Volumen'],0, 'L', 0, 0);            
        $pdf->Ln();   
        $pdf->MultiCell(100, 5, "Bodenbelag: ".$row['Fussboden'],0, 'L', 0, 0); 
        $pdf->MultiCell(80, 5, "Decke: ".$row['Decke'],0, 'L', 0, 0); 
        $pdf->Ln(); 
        $pdf->MultiCell(40, 5, "Anmerkung: ",0, 'R', 0, 0); 
        $pdf->MultiCell(140, 5, $row['Anmerkung AR'], 0, 'L', 0, 0);
        $pdf->Ln(); 
        $pdf->MultiCell(40, 5, "Tätigkeiten: ", 0, 'R', 0, 0);               
        $pdf->MultiCell(140, 5, $row['Taetigkeiten'], 0, 'L', 0, 0);
        
        $pdf->Ln(); 
        $pdf->SetTextColor(0);
        $pdf->MultiCell(40, 5, "Strahlenanwendung: ",0, 'R', 0, 0);
        if($row['Strahlenanwendung']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            if($row['Strahlenanwendung']==='1'){
                $pdf->SetFont('zapfdingbats', '', 10);
                //grün
                $pdf->SetTextColor(0, 255, 0); 
                $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
            }
            else{
                $pdf->MultiCell(40, 5, "Quasi stationär",0, 'L', 0, 0);
            }
        }
        //schwarz 
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "Schwingungsklasse: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['AR_Schwingungsklasse'],0, 'L', 0, 0); 
        $pdf->Ln();        
        $pdf->MultiCell(40, 5, "Laseranwendung: ",0, 'R', 0, 0);
        if($row['Laseranwendung']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
            $pdf->SetTextColor(0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(30, 5, " Klasse: ".$row['Laserklasse'],0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "FB ÖNORM B5220:",0, 'R', 0, 0);
        $pdf->MultiCell(20, 5, " ".$row['Fussboden OENORM B5220'],0, 'L', 0, 0);
        $pdf->Ln();        
        $pdf->MultiCell(40, 5, "Abdunkelbarkeit: ",0, 'R', 0, 0);
        if($row['Abdunkelbarkeit']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            if($row['Abdunkelbarkeit']==='1'){
                $pdf->SetTextColor(0);
                $pdf->SetFont('helvetica', '', 9);
                $pdf->MultiCell(40, 5, "Vollverdunkelbar",0, 'L', 0, 0);
            }
            else{
                $pdf->SetTextColor(0);
                $pdf->SetFont('helvetica', '', 9);
                $pdf->MultiCell(40, 5, "Abdunkelbar",0, 'L', 0, 0);
            }
        }    
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();       
         
        $pdf->MultiCell(40, 5, "EMV: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['ET_EMV_ja-nein']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);             
        $pdf->MultiCell(60, 5, "EMV-Maßnahme: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['ET_EMV'],0, 'L', 0, 0); 
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(180, 5, 'ASTV', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9); 
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Arbeitsplätze: ".$row['AR_APs'],0, 'R', 0, 0);
        $pdf->MultiCell(100, 5, "Arbeitsplatz mit bes. Gefährdung: ",0, 'R', 0, 0);  
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['AP_Gefaehrdung']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Belichtungsfläche: ".$row['Belichtungsfläche']."%",0, 'R', 0, 0);
        $pdf->MultiCell(100, 5, "Raum für uberwiegend geistige Tätigkeiten: ",0, 'R', 0, 0);  
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['AP_Geistige']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(180, 5, 'Bauphysik', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9); 
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Akustikmaßnahme: ".$row['PHY_Akustik_Typ'],0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, "Fläche Akustik: ".$row['PHY_Akustik_Flaeche']."m2",0, 'R', 0, 0);   
        $pdf->MultiCell(40, 5, "Alpha w: ".$row['PHY_Akustik_Schallgrad'],0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Lichtreflexion-LRV: ".$row['PHY_LRV']."%",0, 'R', 0, 0);  
        $pdf->Ln();                
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180, 5, 'Elektro', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);        
        $pdf->Ln();        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "Not-Aus: ".$row[''],0, 'R', 0, 0); 
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['EL_Not_Aus']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(40, 5, "Not-Aus-Funktion: ",0, 'R', 0, 0); 
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Beleuchtungsstärke: ",0, 'R', 0, 0); 
        $pdf->MultiCell(40, 5, $row['EL_Beleuchtungsstaerke']." lx",0, 'L', 0, 0); 
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Netzarten: ",0, 'R', 0, 0); 
        $pdf->MultiCell(20, 5, "AV: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['AV']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "SV: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['SV']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "USV: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['USV']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "",0, 'R', 0, 0);   
        $pdf->MultiCell(40, 5, "16A 3Phasig: ".$row['ET_16A_3Phasig_Einzelanschluss']." Stk",0, 'R', 0, 0);        
        $pdf->MultiCell(40, 5, "32A 3Phasig: ".$row['ET_32A_3Phasig_Einzelanschluss']." Stk",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, "64A 3Phasig: ".$row['ET_64A_3Phasig_Einzelanschluss']." Stk",0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Signaleinrichtungen: ",0, 'R', 0, 0); 
        $pdf->SetTextColor(0, 0, 0);      
        $pdf->Ln();                
        $y = $pdf->GetY();    
        if (($y + 30) >= 270) {
            $pdf->AddPage();
        }    
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180, 5, 'Haustechnik', 'T', 'L', 1, 0, '', '', true);   
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(40,8, "Belüftung: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Belueftung'],0, 'L', 0, 0);        
        $pdf->MultiCell(40,8, "Entlüftung: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Entlueftung'],0, 'L', 0, 0);                               
        $pdf->Ln();
        $pdf->MultiCell(40,8, "Luftmenge: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Luftmenge m3/h']."m3/h",0, 'L', 0, 0);                 
        $pdf->MultiCell(40,8, "Luftwechsel auf 3m: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Luftwechsel 1/h']."/h",0, 'L', 0, 0); 
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Kühlung: ",0, 'R', 0, 0);        
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['HT_Kuehlung']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40,8, "Kälteabgabe: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Kaelteabgabe_Typ'],0, 'L', 0, 0); 
        $pdf->MultiCell(40,8, "Raumtemp Kühlung: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Raumtemp Sommer °C']."°C",0, 'L', 0, 0); 
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Heizung: ",0, 'R', 0, 0);        
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['HT_Heizung']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40,8, "Wärmeabgabe: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Waermeabgabe_Typ'],0, 'L', 0, 0); 
        $pdf->MultiCell(40,8, "Raumtemp Heizen: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Raumtemp Winter °C']."°C",0, 'L', 0, 0); 
        $pdf->Ln();
        $pdf->MultiCell(40,8, "Notdusche: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " ".$row['HT_Notdusche']." Stk",0, 'L', 0, 0);
        $pdf->MultiCell(60, 5, "Abluft Sicherheitsschränke: ",0, 'R', 0, 0);        
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['HT_Abluft_Sicherheitsschrank_Unterbau_Stk'] + $row['HT_Abluft_Sicherheitsschrank_Stk'] <= 0){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();        
        //$pdf->MultiCell(180, 5, "",'B', 'L', 0, 0);              
        // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + 8) >= 270) {
                $pdf->AddPage();
            }                
        $pdf->SetFont('helvetica', 'B', 10);
        // set color for background
        $pdf->SetFillColor(80, 136, 44);  
        $pdf->MultiCell(180, 5, 'Gase', 'T', 'L', 1, 0, '', '', true); 
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 9); 
        $pdf->MultiCell(160,5, "Zentrale Gasversorgung: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "DL: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['DL-tech']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);                                   	
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
         $pdf->MultiCell(20, 5, "He-RF: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['He-RF']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
            $pdf->MultiCell(20, 5, "", 0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
            $pdf->MultiCell(20, 5, "", 0, 'L', 0, 0);
        }        
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(160,5, "Dezentrale Gasversorgung: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "O2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['O2']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);  
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "VA: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['VA']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }        
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "CO2: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['CO2']==='0'){            
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(22, 5, "N2: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['N2']==='0'){            
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->Ln();
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "Ar: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['Ar']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "H2: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['H2']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0); 
        } 
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "He: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['He']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün+
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);       
        $pdf->MultiCell(20, 5, "LN: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['LN']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }                       
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Spezialgase: ",0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(160,5, "Sicherheitseinrichtungen: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "O2-Mangel: ",0, 'R', 0, 0);        
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['O2_Mangel']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9); 
        $pdf->MultiCell(30, 5, "CO2-Melder: ",0, 'R', 0, 0);        
        $pdf->SetFont('zapfdingbats', '', 9);
        if($row['CO2_Melder']==='0'){            
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
        }
        else{
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9); 
        $pdf->MultiCell(40, 5, "Gaswarneinrichtung-Art: ",0, 'R', 0, 0);    
         $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        // set color for background
        $pdf->SetFillColor(80, 136, 44);  
        $pdf->MultiCell(180, 5, 'VEXAT', 'T', 'L', 1, 0, '', '', true); 
        $pdf->Ln();    
        $pdf->SetFont('helvetica', '', 9); 
        $pdf->MultiCell(40,8, "Zonen: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['VEXAT_Zone'],0, 'L', 0, 0); 
        $pdf->MultiCell(100,8, "Anzahl Sicherheitsschränke: ",0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Abluft_Sicherheitsschrank_Unterbau_Stk'] + $row['HT_Abluft_Sicherheitsschrank_Stk'],0, 'L', 0, 0);  
        $pdf->Ln();         
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage();             
    }
    
    $pdf->SetFont('helvetica', 'B', 10);  
    $pdf->MultiCell(180,8, "Labortechnik Einrichtung: ", 0, 'L', 0, 0); 
    $pdf->SetFont('helvetica', '', 8);  
    $pdf->Ln(); 
    $y = $pdf->GetY();    
    if (($y + 8) >= 270) {
        $pdf->AddPage();
    }
    $rowHeightFirstLine = $pdf->getStringHeight(50,"ElementID",false,true,'',1);
    $pdf->MultiCell(20, $rowHeightFirstLine, "ElementID",'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Var",'B', 'C', 0, 0);
    $pdf->MultiCell(120, $rowHeightFirstLine, "Element",'B', 'L', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Stk",'B', 'C', 0, 0);
    $pdf->Ln();
    
    // -------------------------Elemente im Raum laden--------------------------
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID.") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
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
            $rowHeightMainLine = $pdf->getStringHeight(120,$row['Bezeichnung'],false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightMainLine) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(20, $rowHeightMainLine, $row['ElementID'],0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Variante'],0, 'C', $fill, 0);
            $pdf->MultiCell(120, $rowHeightMainLine, $row['Bezeichnung'],0, 'L', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Anzahl'],0, 'C', $fill, 0);
            $idRoombookEntry = $row['id'];
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
