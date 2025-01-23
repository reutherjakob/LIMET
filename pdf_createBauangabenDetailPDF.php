<?php
//============================================================+
// 
// Begin       : 2020-11-20
// Last Update : 2020-11-20
//
// Description : Erstellt Elementweisen Bauangaben Bericht
//               
//
//============================================================+

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
                $this->Cell(0, 0, 'Medizintechnische Vorbemessungsangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            else{
                $this->Cell(0, 0, 'Medizintechnische Bauangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
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
                $this->Cell(0, 0, 'Medizintechnische', 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();
                $this->Cell(0, 0, 'Vorbemessungsangaben', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            }
            else{
                $this->Cell(0, 0, 'Medizintechnische', 0, false, 'L', 0, '', 0, false, 'B', 'B');
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
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 145, 40, 50, 15, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);                
            }
            else{
                if($_SESSION["projectAusfuehrung"]==="LIMET"){
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 110, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
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
$pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
$pdf->SetTitle('Bauangaben Detail-MT');
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
$pdf->AddPage('L', 'A3');
// Daten laden
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}


// -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
$sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND tabelle_parameter.`Bauangaben relevant` = 1)
GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";

$result1 = $mysqli->query($sql);
$paramInfos = array();
$paramInfosCounter = 0;

while ($row = $result1->fetch_assoc()) {     
    $paramInfos[$row['idTABELLE_Parameter']]['ParamID'] = $row['idTABELLE_Parameter'];    
    $paramInfos[$row['idTABELLE_Parameter']]['KategorieID'] = $row['idTABELLE_Parameter_Kategorie'];
    $paramInfos[$row['idTABELLE_Parameter']]['Bezeichnung'] = $row['Abkuerzung'];
    $paramInfos[$row['idTABELLE_Parameter']]['Kategorie'] = $row['Kategorie'];
}

$sql ="SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter 
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND tabelle_parameter.`Bauangaben relevant` = 1)
ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
$result3 = $mysqli->query($sql);
$elementParamInfos = array();
$elementParamInfosCounter = 0;
while ($row = $result3->fetch_assoc()) {        
    $elementParamInfos[$elementParamInfosCounter]['KategorieID'] = $row['idTABELLE_Parameter_Kategorie'];
    $elementParamInfos[$elementParamInfosCounter]['ParamID'] = $row['idTABELLE_Parameter'];
    $elementParamInfos[$elementParamInfosCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $elementParamInfos[$elementParamInfosCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $elementParamInfos[$elementParamInfosCounter]['Wert'] = $row['Wert'];
    $elementParamInfos[$elementParamInfosCounter]['Einheit'] = $row['Einheit'];
    $elementParamInfosCounter = $elementParamInfosCounter + 1;   
}


// RaumIDs laden über GET
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);


foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('L', 'A3');
    // Raumdaten laden ----------------------------------
    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung,  tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss
                FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
                WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";
    
    /*
    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, 
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP, 
            tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
            tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, 
            tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, 
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`,tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";
    */
    
    
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
        $pdf->MultiCell(100, 6, "Raumfläche: ".$row['Nutzfläche']." m2",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: ".$row['Projektname'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: ".$row['Bauabschnitt'],0, 'L', 0, 0);        
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projektstatus: ".$row['Bezeichnung'],'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauetappe: ".$row['Bauetappe'],'B', 'L', 0, 0);            
        $pdf->Ln();        
    }   
    
    $lastXCoordinateHeader = $pdf->GetX();
    $lastYCoordinateHeader = $pdf->GetY();
    $lastCategory = "";
    
    $pdf->Ln();
    
    $pdf->SetFont('courier', '', 6);
    //Titelzeile für Elemente im Raum
    $pdf->MultiCell(15, 6, "ID",1, 'C', 0, 0);
    $pdf->MultiCell(40, 6, "Element",1, 'C', 0, 0);
    $pdf->MultiCell(8, 6, "Var",1, 'C', 0, 0);        
    $pdf->MultiCell(8, 6, "Stk",1, 'C', 0, 0);
    $pdf->MultiCell(11, 6, "Bestand",1, 'C', 0, 0);
         
    // Kopfzeile der Tabelle ausgeben
    foreach($paramInfos as $array) {
        if($lastCategory != $array['Kategorie']){
            $lastXCoordinate = $pdf->GetX();
            $lastYCoordinate = $pdf->GetY();
            $pdf->SetXY($lastXCoordinateHeader,$lastYCoordinateHeader);
            $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, 6, $lastCategory, 1, 'C', 0, 0);    
            $lastXCoordinateHeader = $pdf->GetX();
            $lastYCoordinateHeader = $pdf->GetY();
            $lastCategory = $array['Kategorie'];
            $pdf->SetXY($lastXCoordinate,$lastYCoordinate);
        }
        $tmp_txt = $array['Bezeichnung'];
        $text_width = 7;//$pdf->GetStringWidth($tmp_txt,'courier', '', 6);
        $rowHeight = 6;//$pdf->getStringHeight($text_width+3,$tmp_txt,false,true,'',1);
        $pdf->MultiCell($text_width, $rowHeight, $tmp_txt ,1, 'C', 0, 0);                              
    }
    $lastXCoordinate = $pdf->GetX();
    $lastYCoordinate = $pdf->GetY();
    $pdf->SetXY($lastXCoordinateHeader,$lastYCoordinateHeader);
    $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, 6, $lastCategory, 1, 'C', 0, 0);
    $pdf->SetXY($lastXCoordinate,$lastYCoordinate);
    $pdf->Ln();    
    
    
    // -------------------------Elemente im Raum laden-------------------------- 
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=".$valueOfRoomID.") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
    
    $result = $mysqli->query($sql);

    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    while ($row = $result->fetch_assoc()) {  
        $rowHeightMainLine = 10;
        $rowHeight = 6;
        // Prüfen ob Seitenende---------------------------------------------------------
        $y = $pdf->GetY();    
        if ($y >= 200) {
            $pdf->AddPage('L', 'A3');
            $pdf->Ln();
            $lastXCoordinateHeader = $pdf->GetX();
            $lastYCoordinateHeader = $pdf->GetY();
            $lastCategory = "";

            $pdf->Ln();

            $pdf->SetFont('courier', '', 6);
            //Titelzeile für Elemente im Raum
            $pdf->MultiCell(15, $rowHeight, "ID",1, 'C', 0, 0);
            $pdf->MultiCell(40, $rowHeight, "Element",1, 'C', 0, 0);
            $pdf->MultiCell(8, $rowHeight, "Var",1, 'C', 0, 0);        
            $pdf->MultiCell(8, $rowHeight, "Stk",1, 'C', 0, 0);
            $pdf->MultiCell(11, $rowHeight, "Bestand",1, 'C', 0, 0);

            // Kopfzeile der Tabelle ausgeben
            foreach($paramInfos as $array) {
                if($lastCategory != $array['Kategorie']){
                    $lastXCoordinate = $pdf->GetX();
                    $lastYCoordinate = $pdf->GetY();
                    $pdf->SetXY($lastXCoordinateHeader,$lastYCoordinateHeader);
                    $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, $lastCategory, 1, 'C', 0, 0);    
                    $lastXCoordinateHeader = $pdf->GetX();
                    $lastYCoordinateHeader = $pdf->GetY();
                    $lastCategory = $array['Kategorie'];
                    $pdf->SetXY($lastXCoordinate,$lastYCoordinate);
                }
                $tmp_txt = $array['Bezeichnung'];
                $text_width = 7;//$pdf->GetStringWidth($tmp_txt,'courier', '', 6);
                $pdf->MultiCell($text_width, $rowHeight, $tmp_txt ,1, 'C', 0, 0);                              
            }
            $lastXCoordinate = $pdf->GetX();
            $lastYCoordinate = $pdf->GetY();
            $pdf->SetXY($lastXCoordinateHeader,$lastYCoordinateHeader);
            $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, $lastCategory, 1, 'C', 0, 0);
            $pdf->SetXY($lastXCoordinate,$lastYCoordinate);
            $pdf->Ln();    
        }
        //--------------------------------------------------------------------------------
        
        
        $pdf->MultiCell(15, $rowHeightMainLine, $row['ElementID'],1, 'C', $fill, 0);
        $pdf->MultiCell(40, $rowHeightMainLine, $row['Bezeichnung'],1, 'C', $fill, 0);
        $pdf->MultiCell(8, $rowHeightMainLine, $row['Variante'],1, 'C', $fill, 0);        
        $pdf->MultiCell(8, $rowHeightMainLine, $row['SummevonAnzahl'],1, 'C', $fill, 0);
        if($row['Neu/Bestand']==1){
            $pdf->MultiCell(11, $rowHeightMainLine, "Nein",1, 'C', $fill, 0);
        }
        else{
            $pdf->MultiCell(11, $rowHeightMainLine, "Ja",1, 'C', $fill, 0);
        }                              
        
        // Parameter ausgeben
        foreach($paramInfos as $array) {
            $tmp_txt = $array['Bezeichnung'];
            $tmp_parameterID = $array['ParamID'];
            //$text_width = $pdf->GetStringWidth($tmp_txt,'courier', '', 6);                 
            $outputValue = "";
            foreach($elementParamInfos as $array1) {
                if($array1['ParamID'] == $tmp_parameterID && $array1['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] && $array1['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']){
                    $outputValue = $array1['Wert']."".$array1['Einheit'];                        
                }                  
            }                  
            $pdf->MultiCell($text_width, $rowHeightMainLine, $outputValue ,1, 'C', 0, 0); 
        }        
        $pdf->Ln();                        
    }
    $pdf->Ln();
    
    //Ausgabe Abkürzungen
    $sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
            FROM (tabelle_projekt_elementparameter INNER JOIN tabelle_parameter ON tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter = tabelle_parameter.idTABELLE_Parameter) INNER JOIN tabelle_parameter_kategorie ON tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_parameter.`Bauangaben relevant`)=1))
            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";

    $result4 = $mysqli->query($sql);
    while ($row1 = $result4->fetch_assoc()) {
        $text_width = $pdf->GetStringWidth($row1['Abkuerzung']."-",'courier', 'B', 6);
        $x = $pdf->GetX();    
        if (($x + $text_width+3) >= 400) {
            $pdf->Ln();
        }
        $pdf->SetFont('courier', 'B', 6);        
        $pdf->MultiCell($text_width+3, 6, $row1['Abkuerzung']."-" ,0, 'R', 0, 0,'','',true,0,false,false,0);
        $text_width = $pdf->GetStringWidth($row1['Bezeichnung'].";",'courier', '', 6);
        $x = $pdf->GetX();    
        if (($x + $text_width+3) >= 400) {
            $pdf->Ln();
        }
        $pdf->SetFont('courier', '', 6); 
        $pdf->MultiCell($text_width+3, 6, $row1['Bezeichnung'].";",0, 'L', 0, 0,'','',true,0,false,false,0);                
    }
    
}

// MYSQL-Verbindung schließen
$mysqli ->close();


//$pdf->MultiCell(50, 6, "Bereich",'B', 'L', 0, 0);
//$pdf->MultiCell(20, 6, "Geschoss",'B', 'C', 0, 0);

// close and output PDF document
$pdf->Output('Bauangaben-MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
