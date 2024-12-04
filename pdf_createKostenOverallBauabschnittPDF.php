<?php




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


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung');
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
$pdf->AddPage('L', 'A4');


// Daten laden

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}


// TITELZEILE MIT PROJEKTINFOS--------------------------------------------------
$sql = "SELECT tabelle_projekte.Projektname,tabelle_projekte.Preisbasis, tabelle_planungsphasen.Bezeichnung
    FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
    WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."));";
$result = $mysqli->query($sql);

$row = $result->fetch_assoc();

//TITEL einfügen
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(200, 6, "Projekt: ".$row['Projektname'],'', 'L', 0, 0);
$xPosition = $pdf->getX();
$yPosition = $pdf->getY();
$pdf->Ln();
$pdf->MultiCell(200, 6, "Projektphase: ".$row['Bezeichnung'],'', 'L', 0, 0);
$pdf->Ln();           
$pdf->MultiCell(200, 6, "Preisbasis: ".$row['Preisbasis'],'', 'L', 0, 0);
$pdf->Ln();        

$sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung
FROM tabelle_auftraggeber_gewerke INNER JOIN (tabelle_projekte INNER JOIN tabelle_auftraggeber_codes ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes) ON tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes
WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."))
ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";

$pdf->SetFont('helvetica', 'B', 7);
$pdf->setXY($xPosition, $yPosition);
$pdf->MultiCell(80, '', "Gewerke: ",'', 'L', 0, 0);
$pdf->Ln();  
$pdf->SetFont('helvetica', 'I', 7);
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) { 
    $pdf->MultiCell(200, '', "",'', 'L', 0, 0);
    $pdf->MultiCell(80, '', $row['Gewerke_Nr']."-".$row['Bezeichnung'],'', 'L', 0, 0);
    $pdf->Ln();  
}
$pdf->Ln();  
$pdf->SetFont('helvetica', 'B', 10);
// data loading for header ----------------------------------
$sql = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
        FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
        GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr
        ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";

$result = $mysqli->query($sql);
$gewerkeInProject = array();
while ($row = $result->fetch_assoc()) { 
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['Gewerke_Nr'] = $row['Gewerke_Nr'];
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamt'] = 0;
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtBestand'] = 0;
}

$pdf->MultiCell(50, 6, "Bereich",'B', 'L', 0, 0);
$pdf->MultiCell(20, 6, "",'B', 'C', 0, 0);
	
foreach($gewerkeInProject as $rowData) {
    $pdf->MultiCell(25, 6, $rowData['Gewerke_Nr'], 'B', 'R', 0, 0);
}
$pdf->MultiCell(25, 6, "Gesamt",'B', 'R', 0, 0);
$pdf->Ln();
// ---------------------------------------------------------

// data loading Raumbereiche ----------------------------------
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(244, 244, 244);

$sql = "SELECT tabelle_räume.Bauabschnitt
FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
GROUP BY tabelle_räume.Bauabschnitt
ORDER BY tabelle_räume.Bauabschnitt;";

$result = $mysqli->query($sql);
$raumbereicheInProject = array();
$i = 1;
while ($row = $result->fetch_assoc()) { 
    $raumbereicheInProject[$i]['Bauabschnitt'] = $row['Bauabschnitt'];
    $i++;
}
setlocale(LC_MONETARY,"de_DE");
$sumRaumbereich = 0;
$sumRaumbereichNeu = 0;
$sumRaumbereichBestand = 0;

$fill = 0;
foreach($raumbereicheInProject as $rowData) {
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(50, 4, $rowData['Bauabschnitt'], 0, 'L', $fill, 0);
    $pdf->MultiCell(20, 4, " ", 0, 'C', $fill, 0);
    foreach($gewerkeInProject as $key => $rowDataGewerkeInProject) {
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
            FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.Bauabschnitt )='".$rowData['Bauabschnitt']."') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='".$rowDataGewerkeInProject['Gewerke_Nr']."') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1));";
        
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        if ( null != ($row['PP'])  ){
            $pdf->MultiCell(25, 4, money_format('€ %!n', $row["PP"]), 0, 'R', $fill, 0);
            $sumRaumbereich = $sumRaumbereich + $row['PP'];
        }
        else{
            $pdf->MultiCell(25, 4,  sprintf('%01.2f', 0), 0, 'R', $fill, 0);
        }  
        $gewerkeInProject[$key]['GewerkeSummeGesamt'] = $gewerkeInProject[$key]['GewerkeSummeGesamt'] + $row['PP'];
    }
    $pdf->MultiCell(25, 4, money_format('€ %!n', $sumRaumbereich),0, 'R', $fill, 0);
    $pdf->Ln();
    $pdf->SetFont('helvetica', 'I', 6);
    
    // ------------------------------------Neu --------------------------------------------------
    $pdf->MultiCell(50, 4, 'davon Neu', 0, 'R', $fill, 0);
    $pdf->MultiCell(20, 4, '', 0, 'C', $fill, 0);
    foreach($gewerkeInProject as $key => $rowDataGewerkeInProject) {
        $sql1 = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_Neu
                FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.`Bauabschnitt`)='".$rowData['Bauabschnitt']."') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='".$rowDataGewerkeInProject['Gewerke_Nr']."') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1);";
        $result1 = $mysqli->query($sql1);
        $row1 = $result1->fetch_assoc();
        if ( null != ($row['PP']) ){
            $pdf->MultiCell(25, 4, money_format('€ %!n', $row1["PP_Neu"]), 0, 'R', $fill, 0);
            $sumRaumbereichNeu = $sumRaumbereichNeu + $row1['PP_Neu'];
        }
        else{
            $pdf->MultiCell(25, 4,  sprintf('%01.2f', 0), 0, 'R', $fill, 0);
        }
        $gewerkeInProject[$key]['GewerkeSummeGesamtNeu'] = $gewerkeInProject[$key]['GewerkeSummeGesamtNeu'] + $row1['PP_Neu'];        
    }
    $pdf->MultiCell(25, 4, money_format('€ %!n', $sumRaumbereichNeu),0, 'R', $fill, 0);
    $pdf->Ln();
    
    // ------------------------------------Bestand ---------------------------------------------- 
    $pdf->MultiCell(50, 4, 'davon Bestand', 0, 'R', $fill, 0);
    $pdf->MultiCell(20, 4, '', 0, 'C', $fill, 0);
    foreach($gewerkeInProject as $key => $rowDataGewerkeInProject) {
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
                FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.`Bauabschnitt`)='".$rowData['Bauabschnitt']."') AND ((tabelle_räume.Geschoss)='".$rowData['Geschoss']."') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='".$rowDataGewerkeInProject['Gewerke_Nr']."') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0);";
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        if ( null != ($row['PP'])   ){
            $pdf->MultiCell(25, 4, money_format('€ %!n', $row["PP"]), 0, 'R', $fill, 0);
            $sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
        }
        else{
            $pdf->MultiCell(25, 4,  sprintf('%01.2f', 0), 0, 'R', $fill, 0);
        }
        $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] = $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] + $row['PP'];        
    }
    $pdf->MultiCell(25, 4, money_format('€ %!n', $sumRaumbereichBestand),0, 'R', $fill, 0);
    $pdf->Ln();
    $fill=!$fill;
    $sumRaumbereich = 0;
    $sumRaumbereichNeu = 0;
    $sumRaumbereichBestand = 0;
    $x = $pdf->width;
    $y = $pdf->GetY();

    if (($y + 4) >= 180) {
        $pdf->AddPage();
       // $y = 0; // should be your top margin
    }
}
// ---------------------------------------------------------
// Gesamtsumme ausgeben
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(50, 4, 'Gesamt', 'T', 'L', 0, 0);
$pdf->MultiCell(20, 4, '', 'T', 'R', 0, 0);
$sumGesamt = 0;
foreach($gewerkeInProject as $rowDataGewerkeInProject) {    
    $pdf->MultiCell(25, 4,  sprintf('%01.2f', $rowDataGewerkeInProject['GewerkeSummeGesamt']), 'T', 'R', 0, 0);
    $sumGesamt = $sumGesamt + $rowDataGewerkeInProject['GewerkeSummeGesamt'];
}
$pdf->MultiCell(25, 4,  sprintf('%01.2f', $sumGesamt), 'T', 'R', 0, 0);

// Neusumme----------------------------------------------------
$pdf->Ln();
$pdf->SetFont('helvetica', 'BI', 6);
$pdf->MultiCell(50, 4, 'davon Neu', 0, 'R', 0, 0);
$pdf->MultiCell(20, 4, '', 0, 'L', 0, 0);
$sumGesamtNeu = 0;
foreach($gewerkeInProject as $rowDataGewerkeInProject) {    
    $pdf->MultiCell(25, 4,  sprintf('%01.2f', $rowDataGewerkeInProject['GewerkeSummeGesamtNeu']), 0, 'R', 0, 0);
    $sumGesamtNeu = $sumGesamtNeu + $rowDataGewerkeInProject['GewerkeSummeGesamtNeu'];
}
$pdf->MultiCell(25, 4,  sprintf('%01.2f', $sumGesamtNeu), 0, 'R', 0, 0);

// Bestand von gesamtSumme-------------------------------------
$pdf->Ln();
$pdf->SetFont('helvetica', 'BI', 6);
$pdf->MultiCell(50, 4, 'davon Bestand', 0, 'R', 0, 0);
$pdf->MultiCell(20, 4, '', 0, 'L', 0, 0);
$sumGesamtBestand = 0;
foreach($gewerkeInProject as $rowDataGewerkeInProject) {    
    $pdf->MultiCell(25, 4,  sprintf('%01.2f', $rowDataGewerkeInProject['GewerkeSummeGesamtBestand']), 0, 'R', 0, 0);
    $sumGesamtBestand = $sumGesamtBestand + $rowDataGewerkeInProject['GewerkeSummeGesamtBestand'];
}
$pdf->MultiCell(25, 4,  sprintf('%01.2f', $sumGesamtBestand), 0, 'R', 0, 0);

// close and output PDF document
$pdf->Output('xxx.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

