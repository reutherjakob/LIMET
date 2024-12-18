<?php
//============================================================+
// File name   : pdf_createTenderWorkflowPDF.php
// Begin       : 2019-02-12
// Last Update : 2019-02-12
//
// Description : Erstellt ein PDF mit der Auflistung aller Losworkflows
//
// Author: Jakob Reuther
//
//============================================================+


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
        $this->Cell(0, 0, 'Medizintechnische Los-Workflow-Liste', 0, false, 'R', 0, '', 0, false, 'B', 'B');
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
$pdf->SetTitle('Losworkflow-MT');
//$pdf->SetSubject('xxx');
//$pdf->SetKeywords('xxx');

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


// Daten laden
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

// change character set to utf8 
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}


// Los-Workflows abfragen
$sql = "SELECT tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, DATE_FORMAT(tabelle_lot_workflow.Timestamp_Ist, '%d.%m.%Y') AS Timestamp_Ist, DATE_FORMAT(tabelle_lot_workflow.Timestamp_Soll, '%d.%m.%Y') AS Timestamp_Soll, tabelle_lot_workflow.Abgeschlossen, tabelle_workflowteil.aufgabe, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lot_workflow.Kommentar
        FROM tabelle_workflowteil INNER JOIN (tabelle_workflow_has_tabelle_wofklowteil INNER JOIN (tabelle_lose_extern INNER JOIN tabelle_lot_workflow ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern) ON (tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil) AND (tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow)) ON tabelle_workflowteil.idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil
        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
        ORDER BY tabelle_lose_extern.LosNr_Extern, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer;";

$result = $mysqli->query($sql);

$pdf->AddPage('L', 'A4');

//Kopfzeile
$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(0);


$lotID = 0;
$fill = 0;
$pdf->SetFillColor(244, 244, 244);

$data = array();
$columnCounter = array();
$i = 0;
$k = 0;

while ($row = $result->fetch_assoc()) {          
    if($i > 0 && $lotID !== $row['idtabelle_Lose_Extern']){        
        $i = 0;
    }       
    $data[$k]['idtabelle_Lose_Extern'] = $row['idtabelle_Lose_Extern'];
    $data[$k]['LosNr_Extern'] = $row['LosNr_Extern'];
    $data[$k]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];   
    $data[$k]['data'] = $row['Reihenfolgennummer']." ".$row['aufgabe'];        
    $data[$k]['Abgeschlossen'] = $row['Abgeschlossen'];
    $data[$k]['Kommentar'] = $row['Kommentar'];
    $data[$k]['Timestamp_Ist'] = $row['Timestamp_Ist'];    
    $data[$k]['Timestamp_Soll'] = $row['Timestamp_Soll']; 
    $i++; 
    $k++;            
    $lotID = $row['idtabelle_Lose_Extern'];
    $columnCounter[$row['idtabelle_Lose_Extern']]['length'] = $i;
}

/*
$lotID1 = 0;
$j = 0;
foreach($columnCounter as $row1) {
    //$width = 120 / $row1['length'];          
    $width = 120/$row1['length'];
    for ($i = $j; $i < $j + $row1['length']; $i++) {        
        //Höhe berechnen
        if ($i === $j){
            $rowHeight = $pdf->getStringHeight($width,$data[$i]['data'],false,true,'',1);
        }   
        
        if($lotID1 !== $data[$i]['idtabelle_Lose_Extern']){
            $pdf->Ln();             
            $y = $pdf->GetY(); 
            if (($y + $rowHeight) >= 200) {
                $pdf->AddPage();
            } 
                          
            $fill=!$fill;                                                                  
            $pdf->MultiCell($width, $rowHeight, $data[$i]['LosNr_Extern'],0, 'L', $fill, 0);
            $pdf->MultiCell($width, $rowHeight, $data[$i]['LosBezeichnung_Extern'],0, 'L', $fill, 0);
            $pdf->MultiCell($width, $rowHeight, $data[$i]['data'],'LTB', 'C', $fill, 0);
            $pdf->SetFont(zapfdingbats, '', 8);
            if($data[$i]['Abgeschlossen']==='0'){            
                $pdf->MultiCell($width, $rowHeight, TCPDF_FONTS::unichr(54),'TB', 'L', $fill, 0);
            }
            else{
                $pdf->MultiCell($width, $rowHeight, TCPDF_FONTS::unichr(52),'TB', 'L', $fill, 0);
            }
            $pdf->SetFont('helvetica', '', 8);
            $pdf->MultiCell($width, $rowHeight, $data[$i]['Timestamp'],'RTB', 'C', $fill, 0);
            $lotID1 = $data[$i]['idtabelle_Lose_Extern'];
            
        }
        else{
            $pdf->MultiCell($width, $rowHeight, $data[$i]['data'],'LTB', 'C', $fill, 0);
            $pdf->SetFont(zapfdingbats, '', 8);
            if($data[$i]['Abgeschlossen']==='0'){            
                $pdf->MultiCell($width, $rowHeight, TCPDF_FONTS::unichr(54),'TB', 'L', $fill, 0);
            }
            else{
                $pdf->MultiCell($width, $rowHeight, TCPDF_FONTS::unichr(52),'TB', 'L', $fill, 0);
            }
            $pdf->SetFont('helvetica', '', 8);
            $pdf->MultiCell($width, $rowHeight, $data[$i]['Timestamp'],'RTB', 'C', $fill, 0);
            
        }     
    }  
    $j = $j + $row1['length'];
}
 * */


$width = 220/6;
$rowHeight = 10;
$info = 1; 
$columncounter = 1;
$lotID1 = 0;
$i = 0;
$x_start = $pdf->GetX();
$y_start = $pdf->GetY();

while( $i < sizeof($data)) {    
    
    if($lotID1 !== $data[$i]['idtabelle_Lose_Extern']){
        $pdf->Ln();     
        $pdf->Ln();         
        $y = $pdf->GetY(); 
        if (($y + $rowHeight) >= 160) {
            $pdf->AddPage();
        }
        $x_start = $pdf->GetX();
        $y_start = $pdf->GetY();
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->MultiCell($width, $rowHeight, $data[$i]['LosNr_Extern']." - ".$data[$i]['LosBezeichnung_Extern'],1, 'L', 0, 1, '', '', true, 0);        
        //$pdf->MultiCell($width, $rowHeight, $data[$i]['LosBezeichnung_Extern'],0, 'L', $fill, 0, '', '', true, 0);
        $pdf->SetFont('helvetica', '', 8);
        if($data[$i]['Abgeschlossen']==1){
            //grün
            $pdf->SetFillColor(217, 252, 182);
        }
        else{
            //rot
            $pdf->SetFillColor(252, 182, 182);
        }
        $pdf->MultiCell($width, $rowHeight, $data[$i]['data'],1, 'L', 1, 2, $x_start+$width, $y_start, true, 0);
        $pdf->MultiCell($width, $rowHeight, $data[$i]['Timestamp_Soll']."/".$data[$i]['Timestamp_Ist']."/".$data[$i]['Kommentar'],1, 'L', 1, 0, $x_start+$width, '', true, 0);                                
        $lotID1 = $data[$i]['idtabelle_Lose_Extern'];
        $columncounter = 2;
    }    
    else{
        if($columncounter == 6){
            $pdf->Ln();             
            $y = $pdf->GetY(); 
            if (($y + $rowHeight) >= 160) {
                $pdf->AddPage();
            }
            $columncounter = 1;
            $x_start = $pdf->GetX();
            $y_start = $pdf->GetY();
        }
        if($data[$i]['Abgeschlossen']==1){
            //grün
            $pdf->SetFillColor(217, 252, 182);
        }
        else{
            //rot
            $pdf->SetFillColor(252, 182, 182);
        }
        $pdf->MultiCell($width, $rowHeight, $data[$i]['data'],1, 'L', 1, 2, $x_start + ($width * $columncounter), $y_start, true, 0);
        $pdf->MultiCell($width, $rowHeight, $data[$i]['Timestamp_Soll']."/".$data[$i]['Timestamp_Ist']."/".$data[$i]['Kommentar'],1, 'L', 1, 0, $x_start + ($width * $columncounter), '', true, 0);  
        $columncounter++;
        
        /*
        $mod = $columncounter % 5;
        if($mod == 0){                
            $columncounter = 1;
            $info++;        
            $pdf->Ln();             
            $y = $pdf->GetY(); 
            if (($y + $rowHeight) >= 200) {
                $pdf->AddPage();
            }
            $pdf->MultiCell($width, $rowHeight, $data[$i-5]['data'],0, 'L', $fill, 0);
        }
        else{
            $pdf->MultiCell($width, $rowHeight, $data[$i]['Timestamp'],0, 'L', $fill, 0);
        }
        $columncounter++;
         * 
         * 
         */
    }
    
    
    $i++;
}


// close and output PDF document
$pdf->Output('Losworkflow-MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

