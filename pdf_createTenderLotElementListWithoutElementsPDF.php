<?php
//============================================================+
// File name   : pdf_createTenderLotElementListPDF.php
// Begin       : 2017-11-22
// Last Update : 2017-11-22
//
// Description : Erstellt ein PDF mit der Auflistung aller Lose mit den zugeörigen Elementen
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
        $this->Cell(0, 0, 'Medizintechnische Loseinteilung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
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
$pdf->SetTitle('Loseinteilung-MT');
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


// Daten laden
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

// change character set to utf8 
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

// Elemente im Projekt laden
$sql = "SELECT tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern, tabelle_varianten.Variante, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`
FROM tabelle_elemente INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
WHERE (((tabelle_räume_has_tabelle_elemente.Anzahl)>0) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";

$result2 = $mysqli->query($sql);
$elemente = array();
$elementeCounter = 0;
while ($row = $result2->fetch_assoc()) { 
    $elemente[$elementeCounter]['elementID'] = $row['TABELLE_Elemente_idTABELLE_Elemente'];
    $elemente[$elementeCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $elemente[$elementeCounter]['Stk'] = $row['Anzahl'];
    $elemente[$elementeCounter]['ID'] = $row['ElementID'];
    $elemente[$elementeCounter]['Bezeichnung'] = $row['Bezeichnung'];
    $elemente[$elementeCounter]['Variante'] = $row['Variante'];
    $elemente[$elementeCounter]['Neu/Bestand'] = $row['Neu/Bestand'];
    $elemente[$elementeCounter]['raumNr'] = $row['Raumnr'];
    $elemente[$elementeCounter]['raum'] = $row['Raumbezeichnung'];
    $elemente[$elementeCounter]['Raumbereich'] = $row['Raumbereich Nutzer'];
    $elemente[$elementeCounter]['lotID'] = $row['tabelle_Lose_Extern_idtabelle_Lose_Extern'];
    
    $elementeCounter = $elementeCounter + 1;
}

$pdf->AddPage('L', 'A4');


//Kopfzeile
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0);
$rowHeightFirstLine = $pdf->getStringHeight(30,"Auftragnehmer",false,true,'',1);
$pdf->MultiCell(20, $rowHeightFirstLine, "LosNr",'B', 'L', 0, 0);
$pdf->MultiCell(40, $rowHeightFirstLine, "Bezeichnung",'B', 'L', 0, 0);
$pdf->MultiCell(30, $rowHeightFirstLine, "Verfahren",'B', 'L', 0, 0);
$pdf->MultiCell(30, $rowHeightFirstLine, "Bearbeiter",'B', 'L', 0, 0);
$pdf->MultiCell(35, $rowHeightFirstLine, "Schätzsumme-Neu",'B', 'R', 0, 0);
$pdf->MultiCell(40, $rowHeightFirstLine, "Schätzsumme-Bestand",'B', 'R', 0, 0);
$pdf->MultiCell(30, $rowHeightFirstLine, "Vergabesumme",'B', 'R', 0, 0);
$pdf->MultiCell(40, $rowHeightFirstLine, "Auftragnehmer",'B', 'R', 0, 0);

$pdf->Ln();


$fill = 0;
$pdf->SetFillColor(244, 244, 244);

// Abfrage der Lose                                                                
$sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, 
                tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.Verfahren, tabelle_lose_extern.mkf_von_los,
                tabelle_lose_extern.Bearbeiter, tabelle_lose_extern.Vergabesumme, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Notiz, tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant,
                losschaetzsumme.Summe As schaetzsumme,
                losbestandschaetzsumme.SummeBestand,
                losschaetzsumme.id,
                losbestandschaetzsumme.id
        FROM tabelle_lieferant 
        RIGHT JOIN tabelle_lose_extern 
        ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
        LEFT JOIN
                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS Summe
                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
            AS losschaetzsumme
            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id)
        LEFT JOIN 
                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS SummeBestand
                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
            AS losbestandschaetzsumme
            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losbestandschaetzsumme.id)
        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
        ORDER BY LosNr_Extern;";

$result3 = $mysqli->query($sql);
setlocale(LC_MONETARY,"de_DE");
$summe = 0;
$summebestand = 0;
$summeVergeben = 0;

while ($row = $result3->fetch_assoc()) {    
        $fill=!$fill;                                          
        $y = $pdf->GetY(); 
        
        $rowHeight1 = $pdf->getStringHeight(40,$row['Lieferant'],false,true,'',1);
        $rowHeight2 = $pdf->getStringHeight(40,$row['LosBezeichnung_Extern'],false,true,'',1);
        $rowHeight3 = $pdf->getStringHeight(20,$row['LosNr_Extern'],false,true,'',1);
        
        if($rowHeight1 > $rowHeight2){
            $rowHeight = $rowHeight1;
            if($rowHeight1 > $rowHeight3){
                $rowHeight = $rowHeight1;
            }
            else{
                $rowHeight = $rowHeight3;
            }
        }
        else{
            if($rowHeight2 > $rowHeight3){
                $rowHeight = $rowHeight2;
            }
            else{
                $rowHeight = $rowHeight3;
            }
            //$rowHeight = $rowHeight2;
        }
        
        if (($y + $rowHeightFirstLine) >= 180) {
            $pdf->AddPage();
        } 
        $pdf->SetFont('helvetica', '', 8);           
        $pdf->MultiCell(20, $rowHeight, $row['LosNr_Extern'],0, 'L', $fill, 0);
        $pdf->MultiCell(40, $rowHeight, $row['LosBezeichnung_Extern'],0, 'L', $fill, 0);
        $pdf->MultiCell(30, $rowHeight, $row['Verfahren'],0, 'L', $fill, 0);
        $pdf->MultiCell(30, $rowHeight, $row['Bearbeiter'],0, 'L', $fill, 0);
        $pdf->MultiCell(35, $rowHeight, sprintf('%01.2f', $row['schaetzsumme']),0, 'R', $fill, 0);
        $pdf->MultiCell(40, $rowHeight, sprintf('%01.2f', $row['SummeBestand']),0, 'R', $fill, 0);
        $pdf->MultiCell(30, $rowHeight, sprintf('%01.2f', $row['Vergabesumme']),0, 'R', $fill, 0);
        $pdf->MultiCell(40, $rowHeight, $row['Lieferant'],0, 'R', $fill, 0);
        $summe = $summe + $row['schaetzsumme'];
        $summebestand = $summebestand + $row['SummeBestand'];
        $summeVergeben = $summeVergeben + $row['Vergabesumme'];
        
        
               
    $pdf->Ln();                                    
}
$fill=!$fill; 
$pdf->MultiCell(20, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(40, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(30, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(30, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(35, $rowHeight, sprintf('%01.2f', $summe),'T', 'R', $fill, 0);
$pdf->MultiCell(40, $rowHeight, sprintf('%01.2f', $summebestand),'T', 'R', $fill, 0);
$pdf->MultiCell(30, $rowHeight, sprintf('%01.2f', $summeVergeben),'T', 'R', $fill, 0);
$pdf->MultiCell(40, $rowHeight, "",'T', 'R', $fill, 0);


// close and output PDF document
$pdf->Output('Loseinteilung-MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

