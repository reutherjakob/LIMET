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


// Include the main TCPDF library (search for installation path).
require_once('TCPDF-master/TCPDF-master/tcpdf.php');

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
        $this->Cell(0, 0, 'Medizintechnische Umsiedlungsliste', 0, false, 'R', 0, '', 0, false, 'B', 'B');
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
$pdf->SetTitle('Umsiedlungsliste-MT');
$pdf->SetSubject('Umsiedlungsliste-MT');
$pdf->SetKeywords('Umsiedlungsliste-MT');

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

// Abfrage der Bestandselemente                                                      
$sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_bestandsdaten.`Aktueller Ort`, tabelle_geraete.Typ, tabelle_hersteller.Hersteller, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`
            FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
            ORDER BY tabelle_räume.`Raumbereich Nutzer` , tabelle_räume.Raumnr;";
$result = $mysqli->query($sql);

$pdf->AddPage('L', 'A4');


$fill = 0;
$pdf->SetFillColor(244, 244, 244);
$raumbereich= ""; 
$raumbereichCounter = 0;
// Ausgabe
while ($row = $result->fetch_assoc()) {    
                                        
    if($raumbereich != $row['Raumbereich Nutzer']){            
        //Kopfzeile
        if($raumbereichCounter > 0){
            $pdf->MultiCell(270, 8, "",'T', 'L', 0, 0);
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(0);
        $pdf->MultiCell(270, 8, $row['Raumbereich Nutzer'],1, 'L', 0, 0);
        $pdf->Ln();            
        $rowHeight = $pdf->getStringHeight(40,"Standort vor Siedlung",false,true,'',1);
        $pdf->SetFont('helvetica', '', 8);            
        $fill = 0;
        $pdf->MultiCell(2, $rowHeight, "",'L', 'L', 0, 0);
        $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(0, 0, 0)));
        $pdf->MultiCell(30, $rowHeight, "Raum",'B', 'L', 0, 0);
        $pdf->MultiCell(20, $rowHeight, "Element-ID",'B', 'L', 0, 0);
        $pdf->MultiCell(45, $rowHeight, "Element",'B', 'L', 0, 0);
        $pdf->MultiCell(35, $rowHeight, "Gerät",'B', 'L', 0, 0);
        $pdf->MultiCell(35, $rowHeight, "Inventarnummer",'B', 'L', 0, 0);
        $pdf->MultiCell(35, $rowHeight, "Seriennummer",'B', 'L', 0, 0);
        $pdf->MultiCell(26, $rowHeight, "Anschaffungsjahr",'B', 'L', 0, 0);
        $pdf->MultiCell(40, $rowHeight, "Standort vor Siedlung",'B', 'L', 0, 0);
        $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->MultiCell(2, $rowHeight, "",'R', 'L', 0, 0);
        $pdf->Ln();
        $raumbereich = $row['Raumbereich Nutzer'];
        $raumbereichCounter++;
    }


    $rowHeight = $pdf->getStringHeight(20,$row['Raumnr']."-".$row['Raumbezeichnung'],false,true,'',1);
    $rowHeight1 = $pdf->getStringHeight(35,$row['Hersteller']."-".$row['Typ'],false,true,'',1);
    $rowHeight2 = $pdf->getStringHeight(45,$row['Bezeichnung'],false,true,'',1);

    // Wenn Seitenende? Überprüfen und neue Seite anfangen
    $y = $pdf->GetY();
    if($rowHeight > $rowHeight1 && $rowHeight > $rowHeight2){
        $rowHeightFinal = $rowHeight;
    }
    else{
        if($rowHeight1 > $rowHeight2){
            $rowHeightFinal = $rowHeight1;
        }
        else{
            $rowHeightFinal = $rowHeight2;
        }
    }
    $rowHeightFinal = $rowHeightFinal + 1;

    if (($y + $rowHeightFinal) >= 180) {
        $pdf->AddPage();
    } 
    $pdf->MultiCell(2, $rowHeightFinal, "",'L', 'L', 0, 0);
    $pdf->MultiCell(30, $rowHeightFinal, $row['Raumnr']."-".$row['Raumbezeichnung'],'', 'L', $fill, 0);
    $pdf->MultiCell(20, $rowHeightFinal, $row['ElementID'],'', 'L', $fill, 0);
    $pdf->MultiCell(45, $rowHeightFinal, $row['Bezeichnung'],'', 'L', $fill, 0);
    $pdf->MultiCell(35, $rowHeightFinal, $row['Hersteller']."-".$row['Typ'],'', 'L', $fill, 0);
    $pdf->MultiCell(35, $rowHeightFinal, $row['Inventarnummer'],'', 'L', $fill, 0);
    $pdf->MultiCell(35, $rowHeightFinal, $row['Seriennummer'],'', 'L', $fill, 0);
    $pdf->MultiCell(26, $rowHeightFinal, $row['Anschaffungsjahr'],'', 'L', $fill, 0);
    $pdf->MultiCell(40, $rowHeightFinal, $row['Aktueller Ort'],'', 'L',$fill, 0);       
    $pdf->MultiCell(2, $rowHeightFinal, "",'R', 'L', 0, 0);

    $fill=!$fill; 
    $pdf->Ln();                                    
}

// Umrandung für Raumbereich beenden
$pdf->MultiCell(270, 8, "",'T', 'L', 0, 0);


// close and output PDF document
$pdf->Output('Umsiedlungsliste-MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

