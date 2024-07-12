<?php

require_once('TCPDF-master/TCPDF-master/tcpdf.php');

// extend TCPF with custom functions 
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        session_start();
        // Logo        
        if ($_SESSION["projectAusfuehrung"] === "MADER") {
            $image_file = 'Mader_Logo_neu.jpg';
            $this->Image($image_file, 15, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        } else {
            if ($_SESSION["projectAusfuehrung"] === "LIMET") {
                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            } else {

                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                $image_file1 = 'Mader_Logo_neu.jpg';
                $this->Image($image_file1, 38, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            }
        }
        // Set font
        $this->SetFont('helvetica', '', 10);

        // Title                
        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

        /* change character set to utf8 */
        if (!$mysqli->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $mysqli->error);
            exit();
        }

        // Daten für Vermerkgruppenkopf laden
        $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Verfasser, tabelle_Vermerkgruppe.Startzeit, tabelle_Vermerkgruppe.Endzeit, tabelle_Vermerkgruppe.Datum, tabelle_projekte.Projektname
                    FROM tabelle_Vermerkgruppe INNER JOIN tabelle_projekte ON tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                    WHERE (((tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe)=" . filter_input(INPUT_GET, 'gruppenID') . "));";

        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()) {
            $title = $row['Gruppenname'];
        }
        $mysqli->close();

        $this->Cell(0, 0, $title, 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $this->Ln();
        $this->cell(0, 0, '', 'B', 0, 'L');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        // Page number
        $this->Cell(0, 10, 'Seite ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    // Load table data from file
    public function LoadData($file) {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach ($lines as $line) {
            $data[] = explode(';', chop($line));
        }
        return $data;
    }

    // Colored table
    public function verteilerTable($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.1);
        $this->SetFont('', '', '9');
        // Header
        $w = array(60, 50, 50, 10, 10);
        $num_headers = count($header);
        $this->Cell(0, 6, 'Teilnehmer/Verteiler:', 0, 0, 'L', 0);
        $this->Ln();
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 6, $header[$i], 1, 0, 'L', 0);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(244, 244, 244);
        $this->SetTextColor(0);
        $this->SetFont('', '', '8');

        // Data      
        $fill = 0;
        foreach ($data as $row) {
            $rowHeight = 5;
            $this->Cell($w[0], $rowHeight, $row['Name'] . " " . $row['Vorname'], 1, 0, 'L', $fill, '', 0);
            $this->Cell($w[1], $rowHeight, $row['Mail'], 1, 0, 'L', $fill, '', 3);
            $this->Cell($w[2], $rowHeight, $row['Organisation'], 1, 0, 'L', $fill, '', 3);
            $this->SetFont(zapfdingbats, '', 8);
            if ($row['Anwesenheit'] == '0') {
                $this->Cell($w[3], $rowHeight, TCPDF_FONTS::unichr(54), 1, 0, 'C', $fill, '', 0);
            } else {
                $this->Cell($w[3], $rowHeight, TCPDF_FONTS::unichr(52), 1, 0, 'C', $fill, '', 0);
            }
            if ($row['Verteiler'] == '0') {
                $this->Cell($w[4], $rowHeight, TCPDF_FONTS::unichr(54), 1, 0, 'C', $fill, '', 0);
            } else {
                $this->Cell($w[4], $rowHeight, TCPDF_FONTS::unichr(52), 1, 0, 'C', $fill, '', 0);
            }
            $this->SetFont('helvetica', '', '8');
            $this->Ln();
            $fill = !$fill;
        }

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    // Topics table 
    public function topicsTable($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.1);
        $this->SetFont('', '', '9');
        // Header
        $w = array(135, 18, 25);
        $num_headers = count($header);
        //$this->Cell(0, 6, 'Punkte:', 0, 0, 'L', 0);
//        $this->Ln();
        /* for($i = 0; $i < $num_headers; ++$i) {
          $this->MultiCell($w[$i], 6, $header[$i], 1, 'L', 0, 0, '', '');
          //$pdf->MultiCell(0, 5,"Projekt: ".$row['Projektname']."\n"."Thema: ".$row['Gruppenname']."\nDatum: ".$row['Datum']." von ".$row['Startzeit']." bis ".$row['Endzeit']."\nOrt: ".$row['Ort'], 1, 'L', 0, 1, '', '', true);
          }
          $this->Ln();
         * 
         * 
         */
        // Color and font restoration
        $this->SetFillColor(244, 244, 244);
        $this->SetTextColor(0);

        // Data        
        $fill = 0;
        $untergruppenID = 0;

        foreach ($data as $row) {
            $this->SetFont('helvetica', '', '8');
            $betreffText = "";
            if ($_SESSION["projectName"] === "GCP" && strlen($row['Raumnummer_Nutzer']) > 0) {
                $betreffText = $betreffText . 'Betrifft Raum: ' . $row['Raumnummer_Nutzer'] . " " . $row['Raumbezeichnung'] . "\n";
            } else {
                if (strlen($row['Raumnr']) > 0) { 
                    $betreffText = $betreffText . 'Betrifft Raum: ' . " " . $row['Raumnr'] . " " . $row['Raumbezeichnung'] . "\n";
                }
            }
            if (strlen($row['LosNr_Extern']) > 0) {
                $betreffText = $betreffText . 'Betrifft Los: ' . $row['LosNr_Extern'] . " " . $row['LosBezeichnung_Extern'] . "\n";
            }

            if ($row['Vermerkart'] === 'Bearbeitung') {
                $textNameFälligkeit = $row['Name'] . "\n" . $row['Faelligkeit'];
                if ($row['Bearbeitungsstatus'] === "0") {
                    $textNameFälligkeit = $textNameFälligkeit . "\n" . "Offen";
                } else {
                    $textNameFälligkeit = $textNameFälligkeit . "\n" . "Erledigt";
                }
            } else {
                $textNameFälligkeit = ""; 
            }

            $rowHeight1 = $this->getStringHeight($w[0], $row['Vermerktext'], false, true, '', 1);
            $rowHeight4 = $this->getStringHeight($w[0], $betreffText, false, true, '', 1);
            $rowHeight2 = $this->getStringHeight($w[2], $textNameFälligkeit, false, true, '', 1);
            $rowHeight3 = $this->getStringHeight($w[0], $row['Untergruppennummer'] . " " . $row['Untergruppenname'], false, true, '', 1);

            if ($rowHeight1 + $rowHeight4 > $rowHeight2) {
                $rowHeight = $rowHeight1 + $rowHeight4;
            } else {
                $rowHeight = $rowHeight2;
                $rowHeight1 = $rowHeight - $rowHeight4;
            }

            if ($untergruppenID != $row['idtabelle_Vermerkuntergruppe']) {

                // Wenn Seitenende? Überprüfen und neue Seite anfangen    
                $y = $this->GetY();
                if (($y + 2 * $rowHeight3 + $rowHeight ) >= 270) {
                    $this->AddPage();
                } else {
                    $this->Ln($rowHeight3);
                }
                
                
                
                $fill = 1;
                //$this->MultiCell($w[0], $rowHeight, $row['Untergruppennummer']." ".$row['Untergruppenname'], 1, 'L', $fill, 0, '', '');
                $this->SetFont('', 'B', '9');
                $this->MultiCell($w[0] + $w[1] + $w[2], $rowHeight3, $row['Untergruppennummer'] . ") " . $row['Untergruppenname'], 1, 'L', $fill, 0, '', '');
                $this->Ln();
//                $y = $this->GetY();
//                if (($y + $rowHeight3) >= 270) {  
//                    $this->AddPage();
//                }
                $this->SetFont('', 'B', '8');
                for ($i = 0; $i < $num_headers; ++$i) {
                    $this->MultiCell($w[$i], $rowHeight3, $header[$i], 1, 'L', 0, 0, '', '');
                }
                $this->Ln();
                $untergruppenID = $row['idtabelle_Vermerkuntergruppe'];
                $fill = 0;
            } else {
                //$this->MultiCell($w[0], $rowHeight, '', 1, 'L', $fill, 0, '', '');    
            }
            //$this->Ln($rowHeight4);
//            $y = $this->GetY();
//            if (($y + $rowHeight) >= 270) {
//                $this->AddPage();
//            }
            $y = $this->GetY();
            if (($y + $rowHeight1) >= 260) {
                $this->AddPage();
            }
            $this->SetFont('', 'I', '7');
            $this->MultiCell($w[0], $rowHeight4, $betreffText, 'LTR', 'L', $fill, 0, '', '');
            $this->SetFont('', '', '8');
            $this->MultiCell($w[1], $rowHeight, $row['Vermerkart'], 1, 'L', $fill, 0, '', '');
            if ($row['Vermerkart'] == 'Bearbeitung') {
                $this->MultiCell($w[2], $rowHeight, $textNameFälligkeit, 1, 'L', $fill, 0, '', '');
                //$this->SetFont('helvetica','','8');
            } else {
                $this->MultiCell($w[2], $rowHeight, '', 1, 'L', $fill, 0, '', '');
            }
            
            $this->Ln($rowHeight4);
            $this->MultiCell($w[0], $rowHeight1, $row['Vermerktext'], 'LRB', 'L', $fill, 1, '', ''); 
            
        }
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
$pdf->SetTitle('');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

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
$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();
session_start();
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

// Daten für Vermerkgruppenkopf laden
$sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Verfasser,  DATE_FORMAT(tabelle_Vermerkgruppe.Startzeit, '%H:%i') AS Startzeit, DATE_FORMAT(tabelle_Vermerkgruppe.Endzeit, '%H:%i') AS Endzeit, tabelle_Vermerkgruppe.Datum, tabelle_projekte.Projektname
            FROM tabelle_Vermerkgruppe INNER JOIN tabelle_projekte ON tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
            WHERE (((tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe)=" . filter_input(INPUT_GET, 'gruppenID') . "));";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {

    $title = "Projekt: " . $row['Projektname'] . "\n" . "Thema: " . $row['Gruppenname'] . "\nDatum: " . $row['Datum'] . " von " . $row['Startzeit'] . " bis " . $row['Endzeit'] . "\nOrt: " . $row['Ort'];
    $verfasser = $row['Verfasser'];
    $rowHeight1 = $pdf->getStringHeight(180, $title, false, true, '', 1);
    $pdf->MultiCell(0, $rowHeight1, $title, 1, 'L', 0, 0, '', '', true);
}
$pdf->Ln(5);
// column titles
$verteiler_table_header = array('Name', 'Mail', 'Organisation', 'Anw.', 'Vert.');

// data loading Ansprechpersonentabelle
$sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_ansprechpersonen.Mail, tabelle_organisation.Organisation, tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Anwesenheit, tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Verteiler
FROM tabelle_organisation INNER JOIN (tabelle_projekte_has_tabelle_ansprechpersonen INNER JOIN (tabelle_ansprechpersonen INNER JOIN tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen = tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen) ON tabelle_organisation.idtabelle_organisation = tabelle_projekte_has_tabelle_ansprechpersonen.tabelle_organisation_idtabelle_organisation
WHERE (((tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=" . filter_input(INPUT_GET, 'gruppenID') . ") AND ((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")) ORDER BY Organisation;";

$result = $mysqli->query($sql);
$gruppenTeilnehmer = array();
while ($row = $result->fetch_assoc()) {
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Name'] = $row['Name'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Vorname'] = $row['Vorname'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Mail'] = $row['Mail'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Organisation'] = $row['Organisation'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Anwesenheit'] = $row['Anwesenheit'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Verteiler'] = $row['Verteiler'];
}

// print colored table
$pdf->Ln();
$pdf->verteilerTable($verteiler_table_header, $gruppenTeilnehmer);

// ---------------------------------------------------------
// print topics
$pdf->Ln();
$topics_table_header = array('Text', 'Typ', 'Wer/Bis wann');

// data loading
/* $sql = "SELECT tabelle_Vermerkuntergruppe.Untergruppennummer, tabelle_Vermerkuntergruppe.Untergruppenname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Vermerktext, tabelle_ansprechpersonen.Name, tabelle_Vermerke.idtabelle_Vermerke, tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe
  FROM tabelle_ansprechpersonen RIGHT JOIN (tabelle_Vermerke_has_tabelle_ansprechpersonen RIGHT JOIN (tabelle_Vermerkuntergruppe INNER JOIN tabelle_Vermerke ON tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe = tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe) ON tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke = tabelle_Vermerke.idtabelle_Vermerke) ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
  WHERE (((tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=".filter_input(INPUT_GET, 'gruppenID')."))
  ORDER BY tabelle_Vermerkuntergruppe.Untergruppennummer;";
 */
$sql = "SELECT tabelle_Vermerkuntergruppe.Untergruppennummer, tabelle_Vermerkuntergruppe.Untergruppenname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Bearbeitungsstatus, GROUP_CONCAT(tabelle_ansprechpersonen.Name SEPARATOR ', ') AS Name, tabelle_Vermerke.idtabelle_Vermerke, tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe, tabelle_räume.Raumnr, tabelle_räume.Raumnummer_Nutzer, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern
FROM ((tabelle_ansprechpersonen RIGHT JOIN (tabelle_Vermerke_has_tabelle_ansprechpersonen RIGHT JOIN (tabelle_Vermerkuntergruppe INNER JOIN tabelle_Vermerke ON tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe = tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe) ON tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke = tabelle_Vermerke.idtabelle_Vermerke) ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
WHERE (((tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=" . filter_input(INPUT_GET, 'gruppenID') . "))
GROUP BY idtabelle_Vermerke
ORDER BY tabelle_Vermerkuntergruppe.Untergruppennummer;";

$result = $mysqli->query($sql);
$dataVermerke = array();
while ($row = $result->fetch_assoc()) {
    $dataVermerke[$row['idtabelle_Vermerke']]['idtabelle_Vermerkuntergruppe'] = $row['idtabelle_Vermerkuntergruppe'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Untergruppennummer'] = $row['Untergruppennummer'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Untergruppenname'] = $row['Untergruppenname'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Vermerktext'] = $row['Vermerktext'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Bearbeitungsstatus'] = $row['Bearbeitungsstatus'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Name'] = $row['Name'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Faelligkeit'] = $row['Faelligkeit'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Vermerkart'] = $row['Vermerkart'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Raumnummer_Nutzer'] = $row['Raumnummer_Nutzer'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Raumnr'] = $row['Raumnr'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Raumbezeichnung'] = $row['Raumbezeichnung'];
    $dataVermerke[$row['idtabelle_Vermerke']]['LosNr_Extern'] = $row['LosNr_Extern'];
    $dataVermerke[$row['idtabelle_Vermerke']]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];
}
$pdf->topicsTable($topics_table_header, $dataVermerke);

// -----------------------Abschlusstext anpassen----------------------------
$pdf->SetFont('helvetica', '', '6');
$pdf->Ln(2); 
$outstr = "Hinweis: Sollten Einwände gegen Inhalte dieses Protokolls bestehen, so werden die Empfänger ersucht, diese Einwände im Rahmen der nächsten Besprechung mündlich oder bis spätestens 10 Tage nach Erhalt des Protokolls schriftlich vorzubringen, andernfalls wird allgemeines Einverständnis angenommen. \nDie Verteilung erfolgt ausschließlich über Email. \n  " . $verfasser;
$height= $pdf -> getStringHeight(180,$outstr, false, true, '', 1);
$y = $pdf->GetY();
if (($y + $height) >= 275 ) {
    $pdf->AddPage();
}
$pdf->Multicell(180, 5, $outstr, 0, 'L', 0, 1);

$pdf->Image('/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/Image_Vermerk_2898_61e58a78cd4cf.jpeg', '', '', 40, 40, 'JPG', '', '', true, 150, '', false, false, 1, false, false, false);

// close and output PDF document
$pdf->Output('Protokoll_MT.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

 